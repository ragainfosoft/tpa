<?php
// =====================================================
// TPA IMS — Student Directory
// =====================================================

$page_title   = 'Students';
$page_section = 'students';
require_once __DIR__ . '/../includes/header.php';

$db = getDB();
$search   = trim($_GET['q'] ?? '');
$fYear    = $_GET['year'] ?? '';
$fStatus  = $_GET['status'] ?? 'active';
$fCentre  = $_GET['centre'] ?? '';
$page     = max(1,(int)($_GET['page'] ?? 1));
$perPage  = 30;

$where  = '1=1';
$params = [];
if ($search)  { $where .= ' AND (s.first_name LIKE ? OR s.last_name LIKE ? OR s.student_ref LIKE ? OR p.email LIKE ? OR p.phone LIKE ?)'; $p = "%$search%"; $params = [$p,$p,$p,$p,$p]; }
if ($fYear)   { $where .= ' AND s.year_group = ?'; $params[] = $fYear; }
if ($fStatus) { $where .= ' AND s.status = ?'; $params[] = $fStatus; }
if ($fCentre) { $where .= ' AND s.centre = ?'; $params[] = $fCentre; }

$countStmt = $db->prepare("SELECT COUNT(DISTINCT s.id) FROM students s LEFT JOIN student_parents p ON p.student_id = s.id AND p.is_primary = 1 WHERE $where");
$countStmt->execute($params);
$total = (int) $countStmt->fetchColumn();

$offset   = ($page-1) * $perPage;
$students = $db->prepare("SELECT s.*, CONCAT(s.first_name,' ',s.last_name) as full_name,
    p.parent_name, p.phone as parent_phone, p.whatsapp as parent_wa
    FROM students s
    LEFT JOIN student_parents p ON p.student_id = s.id AND p.is_primary = 1
    WHERE $where GROUP BY s.id ORDER BY s.first_name ASC LIMIT $perPage OFFSET $offset");
$students->execute($params);
$students = $students->fetchAll();
?>

<div class="page-header">
  <div>
    <h1><i class="bi bi-people me-2" style="color:var(--gold);"></i>Students</h1>
    <p class="text-muted mb-0 small"><?= number_format($total) ?> students</p>
  </div>
  <div class="d-flex gap-2 flex-wrap">
    <a href="add.php" class="btn btn-sm btn-dark"><i class="bi bi-person-plus me-1"></i>Enrol Student</a>
    <a href="export.php?q=<?= urlencode($search) ?>&year=<?= urlencode($fYear) ?>&status=<?= urlencode($fStatus) ?>&centre=<?= urlencode($fCentre) ?>" class="btn btn-sm btn-outline-success"><i class="bi bi-download me-1"></i>Export CSV</a>
  </div>
</div>

<!-- Filters -->
<div class="stat-card mb-3">
  <form method="GET" class="row g-2 align-items-end">
    <div class="col-sm-4"><input type="search" name="q" class="form-control form-control-sm" placeholder="Search name, ref, parent phone…" value="<?= h($search) ?>"></div>
    <div class="col-sm-2">
      <select name="year" class="form-select form-select-sm">
        <option value="">All years</option>
        <option value="Reception" <?= $fYear==='Reception'?'selected':'' ?>>Reception</option>
        <?php for ($y=1;$y<=13;$y++): ?><option value="Year <?= $y ?>" <?= $fYear==="Year $y"?'selected':'' ?>>Year <?= $y ?></option><?php endfor; ?>
        <option value="Adult" <?= $fYear==='Adult'?'selected':'' ?>>Adult</option>
      </select>
    </div>
    <div class="col-sm-2">
      <select name="status" class="form-select form-select-sm">
        <option value="">All statuses</option>
        <?php foreach (['active','inactive','suspended','left'] as $s): ?><option value="<?= $s ?>" <?= $fStatus===$s?'selected':'' ?>><?= ucfirst($s) ?></option><?php endforeach; ?>
      </select>
    </div>
    <div class="col-sm-2">
      <select name="centre" class="form-select form-select-sm">
        <option value="">All centres</option>
        <?php foreach (getBranchNames(false) as $c): ?>
          <option value="<?= h($c) ?>" <?= $fCentre===$c?'selected':'' ?>><?= h($c) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-sm-auto"><button class="btn btn-sm btn-dark">Filter</button> <a href="index.php" class="btn btn-sm btn-outline-secondary">Clear</a></div>
  </form>
</div>

<div class="tpa-table table-responsive mb-3">
  <table class="table table-hover mb-0">
    <thead><tr><th>Ref</th><th>Student</th><th>Year</th><th>Centre</th><th>Parent</th><th>Status</th><th>Joined</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($students as $s): ?>
        <tr>
          <td class="small text-muted fw-600"><?= h($s['student_ref']) ?></td>
          <td>
            <div class="fw-600"><?= h($s['full_name']) ?></div>
            <div class="small text-muted"><?= h($s['school'] ?? '') ?></div>
          </td>
          <td><span class="badge bg-light text-dark border"><?= h($s['year_group'] ?? '—') ?></span></td>
          <td><?= h($s['centre'] ?? '—') ?></td>
          <td>
            <div class="small fw-600"><?= h($s['parent_name'] ?? '—') ?></div>
            <div class="small text-muted"><?= h($s['parent_phone'] ?? '') ?></div>
          </td>
          <td><?= studentStatusBadge($s['status']) ?></td>
          <td class="small text-muted"><?= formatDate($s['join_date']) ?></td>
          <td>
            <div class="d-flex gap-1">
              <a href="view.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:.75rem;">View</a>
              <?php if ($s['parent_wa'] ?? $s['parent_phone']): ?>
                <a href="<?= h(waLink($s['parent_wa'] ?: $s['parent_phone'])) ?>" target="_blank" class="btn btn-sm btn-success py-0 px-2" style="font-size:.75rem;"><i class="bi bi-whatsapp"></i></a>
              <?php endif; ?>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($students)): ?>
        <tr><td colspan="8" class="text-center text-muted py-4">No students found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?= paginate($total, $page, $perPage, '?q='.urlencode($search).'&year='.urlencode($fYear).'&status='.urlencode($fStatus).'&centre='.urlencode($fCentre)) ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
