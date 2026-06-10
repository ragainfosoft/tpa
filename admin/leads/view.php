<?php
// =====================================================
// TPA IMS — Lead Detail / View Page
// =====================================================

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

startSecureSession();
requireLogin(SITE_URL . '/login.php');

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: index.php'); exit; }

$db   = getDB();
$lead = $db->prepare('SELECT l.*, u.name as assigned_name FROM leads l LEFT JOIN users u ON l.assigned_to = u.id WHERE l.id = ?');
$lead->execute([$id]);
$lead = $lead->fetch();
if (!$lead) { setFlash('danger','Lead not found.'); header('Location: index.php'); exit; }

// Handle POST actions — all redirects happen BEFORE header.php emits HTML
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    verifyCsrf();

    if ($_POST['action'] === 'update_status') {
        $newStatus = $_POST['status'];
        $db->prepare('UPDATE leads SET status = ?, updated_at = NOW() WHERE id = ?')->execute([$newStatus, $id]);
        logActivity('lead_status_change', "Lead #$id → $newStatus");
        setFlash('success', 'Status updated.');
        header('Location: view.php?id=' . $id); exit;
    }

    if ($_POST['action'] === 'add_followup') {
        $type     = $_POST['type'] ?? 'call';
        $notes    = trim($_POST['notes'] ?? '');
        $outcome  = trim($_POST['outcome'] ?? '');
        $nextDate = $_POST['next_followup_date'] ?? '';
        $db->prepare('INSERT INTO lead_followups (lead_id, user_id, type, notes, outcome, next_followup_date) VALUES (?,?,?,?,?,?)')
           ->execute([$id, currentUserId(), $type, $notes, $outcome, $nextDate ?: null]);
        if ($nextDate) {
            $db->prepare('UPDATE leads SET next_followup_date = ?, updated_at = NOW() WHERE id = ?')->execute([$nextDate, $id]);
        }
        logActivity('lead_followup', "Follow-up logged for lead #$id");
        setFlash('success', 'Follow-up logged.');
        header('Location: view.php?id=' . $id); exit;
    }

    if ($_POST['action'] === 'assign') {
        $db->prepare('UPDATE leads SET assigned_to = ?, updated_at = NOW() WHERE id = ?')->execute([$_POST['assigned_to'] ?: null, $id]);
        setFlash('success', 'Lead assigned.');
        header('Location: view.php?id=' . $id); exit;
    }

    if ($_POST['action'] === 'book_assessment') {
        $assessDate = $_POST['assessment_date'] ?? '';
        $assessTime = trim($_POST['assessment_notes'] ?? '');
        $db->prepare('UPDATE leads SET status="assessment_booked", updated_at=NOW() WHERE id=?')->execute([$id]);
        $db->prepare('INSERT INTO lead_followups (lead_id,user_id,type,notes,outcome) VALUES (?,?,"assessment",?,"Assessment booked")')
           ->execute([$id, currentUserId(), "Assessment booked" . ($assessDate ? " for $assessDate" : '') . ($assessTime ? " — $assessTime" : '')]);
        // WhatsApp notification
        $waTemplate = getSetting('wa_template_assessment_booked') ?? '';
        if ($waTemplate && ($lead['whatsapp'] ?: $lead['phone'])) {
            require_once __DIR__ . '/../includes/WhatsAppService.php';
            $wa = new WhatsAppService();
            $msg = str_replace(['{parent_name}','{child_name}','{date}'],
                               [$lead['name'], $lead['child_name']?:$lead['name'], $assessDate ?: 'TBC'],
                               $waTemplate);
            $wa->sendText($lead['whatsapp'] ?: $lead['phone'], $msg);
        }
        logActivity('assessment_booked', "Assessment booked for lead #$id");
        setFlash('success', '📋 Assessment booked! Status updated and parent notified via WhatsApp.');
        header('Location: view.php?id=' . $id); exit;
    }

    if ($_POST['action'] === 'convert') {
        header('Location: convert.php?lead_id=' . $id);
        exit;
    }
}

// Now safe to output HTML
$page_title   = 'Lead Detail';
$page_section = 'leads';
require_once __DIR__ . '/../includes/header.php';

$followups = $db->prepare('SELECT f.*, u.name as user_name FROM lead_followups f LEFT JOIN users u ON f.user_id = u.id WHERE f.lead_id = ? ORDER BY f.created_at DESC');
$followups->execute([$id]);
$followups = $followups->fetchAll();

$staffList = $db->query('SELECT id, name FROM users WHERE role IN ("admin","staff") AND is_active = 1 ORDER BY name')->fetchAll();

$typeIcons = ['call'=>'telephone','whatsapp'=>'whatsapp','email'=>'envelope','visit'=>'building','assessment'=>'clipboard-check','other'=>'three-dots'];
?>

<div class="page-header">
  <div>
    <a href="index.php" class="btn btn-sm btn-outline-secondary mb-2"><i class="bi bi-arrow-left me-1"></i>All Leads</a>
    <h1><i class="bi bi-person-lines-fill me-2" style="color:var(--gold);"></i><?= h($lead['name']) ?></h1>
    <div class="d-flex align-items-center gap-2 flex-wrap mt-1">
      <?= leadStatusBadge($lead['status']) ?>
      <span class="text-muted small">Added <?= formatDate($lead['created_at']) ?></span>
      <?php if ($lead['assigned_name']): ?>
        <span class="badge bg-light text-dark border"><i class="bi bi-person me-1"></i><?= h($lead['assigned_name']) ?></span>
      <?php endif; ?>
    </div>
  </div>
  <div class="d-flex gap-2 flex-wrap">
    <?php if ($lead['whatsapp'] ?? $lead['phone']): ?>
      <a href="<?= h(waLink($lead['whatsapp'] ?: $lead['phone'], 'Hi ' . $lead['name'] . ', this is Talent Pool Academy. ')) ?>" target="_blank" class="btn btn-sm btn-success"><i class="bi bi-whatsapp me-1"></i>WhatsApp</a>
    <?php endif; ?>
    <?php if ($lead['email']): ?>
      <a href="mailto:<?= h($lead['email']) ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-envelope me-1"></i>Email</a>
    <?php endif; ?>
    <a href="edit.php?id=<?= $id ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil me-1"></i>Edit</a>
    <?php if ($lead['status'] !== 'enrolled' && $lead['status'] !== 'assessment_booked'): ?>
      <button class="btn btn-sm btn-warning text-dark fw-700" data-bs-toggle="modal" data-bs-target="#assessModal">
        <i class="bi bi-clipboard-check me-1"></i>Book Assessment
      </button>
    <?php endif; ?>
    <?php if ($lead['status'] !== 'enrolled'): ?>
      <a href="convert.php?lead_id=<?= $id ?>" class="btn btn-sm btn-dark">
        <i class="bi bi-person-check me-1"></i>Enrol as Student
      </a>
    <?php endif; ?>
  </div>
</div>

<!-- Book Assessment Modal -->
<div class="modal fade" id="assessModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
        <input type="hidden" name="action" value="book_assessment">
        <div class="modal-header" style="background:var(--navy);">
          <h5 class="modal-title text-white"><i class="bi bi-clipboard-check me-2"></i>Book Free Assessment</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body row g-3">
          <div class="col-12">
            <label class="form-label fw-600 small">Proposed Assessment Date</label>
            <input type="date" name="assessment_date" class="form-control" min="<?= date('Y-m-d') ?>">
          </div>
          <div class="col-12">
            <label class="form-label fw-600 small">Notes / Time Slot</label>
            <input type="text" name="assessment_notes" class="form-control" placeholder="e.g. Saturday 10am Romford centre">
          </div>
          <div class="col-12">
            <div class="alert alert-info border-0 mb-0" style="background:#eff6ff;font-size:.83rem;">
              <i class="bi bi-info-circle me-2"></i>Lead status will be updated to <strong>Assessment Booked</strong> and a WhatsApp message will be sent to the parent (if template is configured in Settings).
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-warning text-dark fw-700"><i class="bi bi-clipboard-check me-1"></i>Confirm Booking</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="row g-4">

  <!-- Left: Lead info -->
  <div class="col-lg-4">

    <!-- Contact -->
    <div class="stat-card mb-3">
      <h6 class="fw-700 mb-3 text-uppercase" style="font-size:.7rem;letter-spacing:.1em;color:#888;">Contact</h6>
      <table class="table table-sm table-borderless mb-0 small">
        <tr><td class="text-muted fw-600 pe-3">Phone</td><td><?= $lead['phone'] ? '<a href="tel:'.h($lead['phone']).'">'.h($lead['phone']).'</a>' : '—' ?></td></tr>
        <tr><td class="text-muted fw-600">WhatsApp</td><td><?= $lead['whatsapp'] ? '<a href="'.h(waLink($lead['whatsapp'])).'" target="_blank">'.h($lead['whatsapp']).'</a>' : '—' ?></td></tr>
        <tr><td class="text-muted fw-600">Email</td><td style="word-break:break-all;"><?= $lead['email'] ? '<a href="mailto:'.h($lead['email']).'">'.h($lead['email']).'</a>' : '—' ?></td></tr>
      </table>
    </div>

    <!-- Child -->
    <div class="stat-card mb-3">
      <h6 class="fw-700 mb-3 text-uppercase" style="font-size:.7rem;letter-spacing:.1em;color:#888;">Child</h6>
      <table class="table table-sm table-borderless mb-0 small">
        <tr><td class="text-muted fw-600 pe-3">Name</td><td><?= h($lead['child_name'] ?? '—') ?></td></tr>
        <tr><td class="text-muted fw-600">Year</td><td><?= h($lead['child_year'] ?? '—') ?></td></tr>
        <tr><td class="text-muted fw-600">Course</td><td><?= h($lead['course_interest'] ?? '—') ?></td></tr>
        <tr><td class="text-muted fw-600">Centre</td><td><?= h($lead['centre'] ?? '—') ?></td></tr>
        <tr><td class="text-muted fw-600">Source</td><td><?= h($lead['source'] ?? '—') ?></td></tr>
      </table>
    </div>

    <!-- Status change -->
    <div class="stat-card mb-3">
      <h6 class="fw-700 mb-3 text-uppercase" style="font-size:.7rem;letter-spacing:.1em;color:#888;">Change Status</h6>
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
        <input type="hidden" name="action" value="update_status">
        <div class="d-flex gap-2">
          <select name="status" class="form-select form-select-sm">
            <?php foreach (['new','contacted','follow_up','assessment_booked','enrolled','lost'] as $s): ?>
              <option value="<?= $s ?>" <?= $lead['status']===$s?'selected':'' ?>><?= str_replace('_',' ',ucfirst($s)) ?></option>
            <?php endforeach; ?>
          </select>
          <button class="btn btn-sm btn-dark">Update</button>
        </div>
      </form>
    </div>

    <!-- Assign -->
    <div class="stat-card mb-3">
      <h6 class="fw-700 mb-3 text-uppercase" style="font-size:.7rem;letter-spacing:.1em;color:#888;">Assigned To</h6>
      <form method="POST" class="d-flex gap-2">
        <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
        <input type="hidden" name="action" value="assign">
        <select name="assigned_to" class="form-select form-select-sm">
          <option value="">Unassigned</option>
          <?php foreach ($staffList as $s): ?>
            <option value="<?= $s['id'] ?>" <?= $lead['assigned_to']==$s['id']?'selected':'' ?>><?= h($s['name']) ?></option>
          <?php endforeach; ?>
        </select>
        <button class="btn btn-sm btn-dark">Save</button>
      </form>
    </div>

    <?php if ($lead['notes']): ?>
    <div class="stat-card">
      <h6 class="fw-700 mb-2 text-uppercase" style="font-size:.7rem;letter-spacing:.1em;color:#888;">Notes</h6>
      <p class="small text-muted mb-0"><?= nl2br(h($lead['notes'])) ?></p>
    </div>
    <?php endif; ?>

  </div>

  <!-- Right: Follow-up timeline & add form -->
  <div class="col-lg-8">

    <!-- Add follow-up -->
    <div class="stat-card mb-4">
      <h6 class="fw-700 mb-3"><i class="bi bi-plus-circle text-success me-2"></i>Log Follow-Up</h6>
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
        <input type="hidden" name="action" value="add_followup">
        <div class="row g-3">
          <div class="col-sm-4">
            <label class="form-label fw-600 small">Contact Type</label>
            <select name="type" class="form-select form-select-sm">
              <option value="call">📞 Phone Call</option>
              <option value="whatsapp">💬 WhatsApp</option>
              <option value="email">📧 Email</option>
              <option value="visit">🏫 Walk-in Visit</option>
              <option value="assessment">📋 Assessment</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div class="col-sm-4">
            <label class="form-label fw-600 small">Outcome</label>
            <input type="text" name="outcome" class="form-control form-control-sm" placeholder="e.g. Left voicemail, Keen to join">
          </div>
          <div class="col-sm-4">
            <label class="form-label fw-600 small">Next Follow-Up Date</label>
            <input type="date" name="next_followup_date" class="form-control form-control-sm" min="<?= date('Y-m-d') ?>">
          </div>
          <div class="col-12">
            <label class="form-label fw-600 small">Notes</label>
            <textarea name="notes" class="form-control form-control-sm" rows="2" placeholder="Details of the conversation…"></textarea>
          </div>
          <div class="col-12">
            <button class="btn btn-sm btn-success"><i class="bi bi-check-lg me-1"></i>Log Follow-Up</button>
          </div>
        </div>
      </form>
    </div>

    <!-- Timeline -->
    <div class="stat-card">
      <h6 class="fw-700 mb-3"><i class="bi bi-clock-history me-2" style="color:var(--gold);"></i>Activity Timeline</h6>
      <?php if (empty($followups)): ?>
        <p class="text-muted small">No follow-ups logged yet.</p>
      <?php else: ?>
        <div class="timeline-list">
          <?php foreach ($followups as $f):
            $icon = $typeIcons[$f['type']] ?? 'three-dots';
          ?>
            <div class="d-flex gap-3 mb-4">
              <div style="width:36px;height:36px;border-radius:50%;background:var(--gold-pale);color:var(--gold);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:1rem;">
                <i class="bi bi-<?= $icon ?>"></i>
              </div>
              <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start">
                  <div class="fw-700 small"><?= ucfirst(h($f['type'])) ?> <span class="text-muted fw-400">&middot; <?= h($f['user_name'] ?? 'Staff') ?></span></div>
                  <div class="text-muted" style="font-size:.72rem;"><?= formatDateTime($f['created_at']) ?></div>
                </div>
                <?php if ($f['outcome']): ?>
                  <div class="small text-success fw-600 mb-1"><i class="bi bi-arrow-return-right me-1"></i><?= h($f['outcome']) ?></div>
                <?php endif; ?>
                <?php if ($f['notes']): ?>
                  <p class="small text-muted mb-1"><?= nl2br(h($f['notes'])) ?></p>
                <?php endif; ?>
                <?php if ($f['next_followup_date']): ?>
                  <div class="small text-primary"><i class="bi bi-calendar me-1"></i>Next follow-up: <?= formatDate($f['next_followup_date']) ?></div>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
