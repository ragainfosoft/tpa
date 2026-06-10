<?php
// =====================================================
// TPA IMS — Leads CRM Index (Kanban + Table view)
// =====================================================

$page_title   = 'Lead CRM';
$page_section = 'leads';
require_once __DIR__ . '/../includes/header.php';

$db = getDB();

// Kanban columns config
$columns = [
    'new'               => ['label' => 'New',               'color' => '#17a2b8'],
    'contacted'         => ['label' => 'Contacted',         'color' => '#0d6efd'],
    'follow_up'         => ['label' => 'Follow-Up',         'color' => '#ffc107'],
    'assessment_booked' => ['label' => 'Assessment Booked', 'color' => '#7c3aed'],
    'enrolled'          => ['label' => 'Enrolled',          'color' => '#198754'],
    'lost'              => ['label' => 'Lost',              'color' => '#6c757d'],
];

// Fetch all active staff for assign (include branch managers)
$staffList = $db->query('SELECT id, name FROM users WHERE role IN ("admin","staff","branch_manager") AND is_active = 1 ORDER BY name')->fetchAll();

// View toggle
$view = $_GET['view'] ?? '';

// Filters for table view
$search  = trim($_GET['q'] ?? '');
$fStatus = $_GET['status'] ?? '';
$fSource = $_GET['source'] ?? '';
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 25;

// Build lead query
$where  = '1=1';
$params = [];
if ($search)  { $where .= ' AND (l.name LIKE ? OR l.email LIKE ? OR l.phone LIKE ? OR l.child_name LIKE ?)'; $p = "%$search%"; $params = array_merge($params, [$p,$p,$p,$p]); }
if ($fStatus) { $where .= ' AND l.status = ?'; $params[] = $fStatus; }
if ($fSource) { $where .= ' AND l.source = ?'; $params[] = $fSource; }

$countStmt = $db->prepare("SELECT COUNT(*) FROM leads l WHERE $where");
$countStmt->execute($params);
$total = (int) $countStmt->fetchColumn();

$offset = ($page - 1) * $perPage;
$leads  = $db->prepare("SELECT l.*, u.name as assigned_name FROM leads l
    LEFT JOIN users u ON l.assigned_to = u.id
    WHERE $where ORDER BY l.created_at DESC LIMIT $perPage OFFSET $offset");
$leads->execute($params);
$leads = $leads->fetchAll();

// Kanban data
$kanbanData = [];
foreach (array_keys($columns) as $s) $kanbanData[$s] = [];

$allLeads = $db->query("SELECT l.*, u.name as assigned_name FROM leads l
    LEFT JOIN users u ON l.assigned_to = u.id
    WHERE status NOT IN ('enrolled','lost')
    ORDER BY FIELD(status,'new','contacted','follow_up','assessment_booked'), created_at DESC")->fetchAll();
foreach ($allLeads as $l) $kanbanData[$l['status']][] = $l;

// Update status via AJAX POST (CSRF-protected)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_update_status'])) {
    header('Content-Type: application/json');
    if (!verifyCsrf(false)) { echo json_encode(['ok'=>false,'error'=>'CSRF']); exit; }
    $id  = (int)$_POST['lead_id'];
    $st  = $_POST['new_status'];
    if ($id > 0 && array_key_exists($st, $columns)) {
        $db->prepare('UPDATE leads SET status = ?, updated_at = NOW() WHERE id = ?')->execute([$st, $id]);
        logActivity('lead_status_change', "Lead #$id moved to $st");
    }
    echo json_encode(['ok' => true]);
    exit;
}
?>

<div class="page-header">
  <div>
    <h1><i class="bi bi-funnel me-2" style="color:var(--gold);"></i>Lead CRM</h1>
    <p class="text-muted mb-0 small"><?= number_format($total) ?> leads total</p>
  </div>
  <div class="d-flex gap-2 align-items-center flex-wrap">
    <!-- View toggle -->
    <div class="btn-group btn-group-sm">
      <a href="?view=kanban" class="btn <?= $view==='kanban'?'btn-dark':'btn-outline-secondary' ?>"><i class="bi bi-columns-gap me-1"></i>Kanban</a>
      <a href="?view=table" class="btn <?= $view==='table'?'btn-dark':'btn-outline-secondary' ?>"><i class="bi bi-table me-1"></i>Table</a>
    </div>
    <a href="add.php" class="btn btn-sm btn-dark"><i class="bi bi-plus-lg me-1"></i>New Lead</a>
  </div>
</div>

<?php if ($view === 'kanban'): ?>

<!-- ── KANBAN VIEW ─────────────────────────────────── -->
<div class="kanban-board">
  <?php
  $avatarColors = ['new'=>'#17a2b8','contacted'=>'#0d6efd','follow_up'=>'#e67e22','assessment_booked'=>'#7c3aed','enrolled'=>'#198754','lost'=>'#6c757d'];
  foreach ($columns as $status => $col): if ($status === 'enrolled' || $status === 'lost') continue;
    $count = count($kanbanData[$status]);
  ?>
    <div class="kanban-col" id="col-<?= $status ?>" data-status="<?= $status ?>">
      <div class="kanban-col-header">
        <h6 style="color:<?= $col['color'] ?>;"><?= $col['label'] ?></h6>
        <span style="background:<?= $col['color'] ?>22;color:<?= $col['color'] ?>;font-size:.65rem;font-weight:800;padding:2px 8px;border-radius:20px;border:1px solid <?= $col['color'] ?>44;"><?= $count ?></span>
      </div>
      <div class="kanban-drop" id="drop-<?= $status ?>">
        <?php foreach ($kanbanData[$status] as $l):
          $words = array_filter(explode(' ', trim($l['name'])));
          $initials = implode('', array_map(fn($w) => strtoupper($w[0]), array_slice(array_values($words), 0, 2)));
          $avatarBg = $avatarColors[$status] ?? '#0A1628';
          $over = $l['next_followup_date'] && $l['next_followup_date'] < date('Y-m-d');
        ?>
          <div class="kanban-card" data-id="<?= $l['id'] ?>" draggable="true" style="border-left-color:<?= $col['color'] ?>;">
            <div class="d-flex align-items-center gap-2 mb-2">
              <div class="kanban-avatar" style="background:<?= $avatarBg ?>;"><?= h($initials) ?></div>
              <span class="kanban-name"><?= h($l['name']) ?></span>
            </div>
            <?php if ($l['child_name']): ?>
              <div class="kanban-meta"><i class="bi bi-person-fill me-1"></i><?= h($l['child_name']) ?><?= $l['child_year'] ? ' · ' . h($l['child_year']) : '' ?></div>
            <?php endif; ?>
            <?php if ($l['course_interest']): ?>
              <div class="kanban-course"><i class="bi bi-book me-1"></i><?= h($l['course_interest']) ?></div>
            <?php endif; ?>
            <?php if ($l['centre'] ?? ''): ?>
              <div class="kanban-meta mt-1"><i class="bi bi-geo-alt me-1"></i><?= h($l['centre']) ?></div>
            <?php endif; ?>
            <?php if ($over): ?>
              <div style="margin-top:7px;background:#fff3cd;border:1px solid #ffc107;border-radius:6px;padding:3px 8px;font-size:.68rem;font-weight:700;color:#856404;"><i class="bi bi-alarm me-1"></i>Overdue: <?= formatDate($l['next_followup_date'],'d M') ?></div>
            <?php elseif ($l['next_followup_date']): ?>
              <div style="margin-top:7px;font-size:.68rem;color:#0d6efd;font-weight:700;"><i class="bi bi-alarm me-1"></i>Follow-up: <?= formatDate($l['next_followup_date'],'d M') ?></div>
            <?php endif; ?>
            <div class="kanban-footer">
              <span class="kanban-time"><?= timeAgo($l['created_at']) ?></span>
              <div class="kanban-actions">
                <?php if ($l['whatsapp'] ?? $l['phone']): ?>
                  <a href="<?= h(waLink($l['whatsapp'] ?: $l['phone'])) ?>" target="_blank" class="btn btn-xs py-0 px-1 btn-success" style="font-size:.7rem;" title="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                <?php endif; ?>
                <a href="view.php?id=<?= $l['id'] ?>" class="btn btn-xs py-0 px-1 btn-outline-secondary" style="font-size:.7rem;" title="View"><i class="bi bi-eye"></i></a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<?php else: ?>

<!-- ── TABLE VIEW ─────────────────────────────────── -->
<div class="stat-card mb-3">
  <form method="GET" class="row g-2 align-items-end">
    <input type="hidden" name="view" value="table">
    <div class="col-sm-4"><input type="search" name="q" class="form-control form-control-sm" placeholder="Search name, email, phone…" value="<?= h($search) ?>"></div>
    <div class="col-sm-2">
      <select name="status" class="form-select form-select-sm">
        <option value="">All statuses</option>
        <?php foreach ($columns as $s => $c): ?><option value="<?= $s ?>" <?= $fStatus===$s?'selected':'' ?>><?= $c['label'] ?></option><?php endforeach; ?>
      </select>
    </div>
    <div class="col-sm-2">
      <select name="source" class="form-select form-select-sm">
        <option value="">All sources</option>
        <?php foreach (['Google Search','Word of Mouth','Social Media','Flyer','Website','Other'] as $src): ?>
          <option <?= $fSource===$src?'selected':'' ?>><?= $src ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-sm-auto"><button class="btn btn-sm btn-dark">Filter</button> <a href="index.php?view=table" class="btn btn-sm btn-outline-secondary">Clear</a></div>
  </form>
</div>

<div class="tpa-table table-responsive mb-3">
  <table class="table table-hover mb-0">
    <thead><tr><th>Name / Contact</th><th>Child</th><th>Course</th><th>Centre</th><th>Source</th><th>Status</th><th>Follow-Up</th><th>Assigned</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($leads as $l): ?>
        <tr>
          <td>
            <div class="fw-600"><?= h($l['name']) ?></div>
            <div class="small text-muted"><?= h($l['email'] ?? '') ?> <?= h($l['phone'] ?? '') ?></div>
          </td>
          <td><?= h($l['child_name'] ?? '—') ?> <span class="badge bg-light text-dark border"><?= h($l['child_year'] ?? '') ?></span></td>
          <td><?= h($l['course_interest'] ?? '—') ?></td>
          <td><?= h($l['centre'] ?? '—') ?></td>
          <td><span class="badge bg-light text-dark border"><?= h($l['source'] ?? '') ?></span></td>
          <td><?= leadStatusBadge($l['status']) ?></td>
          <td>
            <?php if ($l['next_followup_date']):
              $over = $l['next_followup_date'] < date('Y-m-d'); ?>
              <span class="<?= $over?'text-danger fw-700':'' ?> small"><?= formatDate($l['next_followup_date']) ?></span>
            <?php else: ?><span class="text-muted small">—</span><?php endif; ?>
          </td>
          <td class="small"><?= h($l['assigned_name'] ?? '—') ?></td>
          <td>
            <div class="d-flex gap-1">
              <a href="view.php?id=<?= $l['id'] ?>" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:.75rem;">View</a>
              <?php if ($l['whatsapp'] ?? $l['phone']): ?>
                <a href="<?= h(waLink($l['whatsapp'] ?: $l['phone'])) ?>" target="_blank" class="btn btn-sm btn-success py-0 px-2" style="font-size:.75rem;"><i class="bi bi-whatsapp"></i></a>
              <?php endif; ?>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?= paginate($total, $page, $perPage, '?view=table&q='.urlencode($search).'&status='.urlencode($fStatus).'&source='.urlencode($fSource)) ?>

<?php endif; ?>

<?php
$extra_js = <<<'JS'
<script>
// ── Kanban drag-and-drop ─────────────────────────────
document.querySelectorAll('.kanban-drop').forEach(zone => {
  Sortable.create(zone, {
    group: 'kanban',
    animation: 150,
    ghostClass: 'opacity-50',
    onEnd: function(evt) {
      const id     = evt.item.dataset.id;
      const newCol = evt.to.closest('.kanban-col')?.dataset.status;
      if (!newCol) return;
      const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
      fetch('index.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: `ajax_update_status=1&lead_id=${id}&new_status=${newCol}&csrf_token=${encodeURIComponent(csrf)}`
      });
    }
  });
});
</script>
JS;
require_once __DIR__ . '/../includes/footer.php';
?>
