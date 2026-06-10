<?php
// =====================================================
// TPA IMS — Edit Lead
// =====================================================

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
startSecureSession();
requireLogin(SITE_URL . '/login.php');

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: index.php'); exit; }

$db   = getDB();
$lead = $db->prepare('SELECT * FROM leads WHERE id = ?');
$lead->execute([$id]); $lead = $lead->fetch();
if (!$lead) { setFlash('danger','Lead not found.'); header('Location: index.php'); exit; }

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    if (empty(trim($_POST['name'] ?? '')))                   $errors[] = 'Parent name required.';
    if (empty(trim($_POST['phone'] ?? '')) && empty(trim($_POST['email'] ?? ''))) $errors[] = 'Phone or email required.';

    if (empty($errors)) {
        $db->prepare('UPDATE leads SET name=?,email=?,phone=?,whatsapp=?,child_name=?,child_year=?,course_interest=?,centre=?,source=?,notes=?,assigned_to=?,next_followup_date=?,updated_at=NOW() WHERE id=?')
           ->execute([
               trim($_POST['name']), trim($_POST['email']), trim($_POST['phone']), trim($_POST['whatsapp']),
               trim($_POST['child_name']), $_POST['child_year'], $_POST['course_interest'], $_POST['centre'],
               $_POST['source'], trim($_POST['notes']),
               (int)($_POST['assigned_to'] ?? 0) ?: null,
               $_POST['next_followup_date'] ?: null,
               $id
           ]);
        logActivity('lead_updated', "Lead #$id updated");
        setFlash('success', 'Lead updated successfully.');
        header('Location: view.php?id=' . $id); exit;
    }
    $lead = array_merge($lead, $_POST);
}

$staffList = $db->query('SELECT id,name FROM users WHERE role IN ("admin","staff","branch_manager") AND is_active=1 ORDER BY name')->fetchAll();
require_once __DIR__ . '/../includes/helpers.php';
$branches    = getBranchNames(true);
$programmes  = getProgrammes();
$leadSources = getLeadSources();
$yearGroups  = getYearGroups();

$page_title   = 'Edit Lead';
$page_section = 'leads';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
  <h1><i class="bi bi-pencil me-2" style="color:var(--gold);"></i>Edit Lead</h1>
  <a href="view.php?id=<?= $id ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<?php if ($errors): ?>
  <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= h($e) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<form method="POST" class="row g-4">
  <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">

  <div class="col-lg-8">
    <div class="stat-card mb-4">
      <h6 class="fw-700 mb-4 text-uppercase" style="font-size:.7rem;letter-spacing:.1em;color:#888;">Parent / Guardian</h6>
      <div class="row g-3">
        <div class="col-sm-6"><label class="form-label fw-600 small">Full Name *</label><input type="text" name="name" class="form-control" value="<?= h($lead['name']) ?>" required></div>
        <div class="col-sm-6"><label class="form-label fw-600 small">Email</label><input type="email" name="email" class="form-control" value="<?= h($lead['email'] ?? '') ?>"></div>
        <div class="col-sm-6"><label class="form-label fw-600 small">Phone *</label><input type="tel" name="phone" class="form-control" value="<?= h($lead['phone'] ?? '') ?>"></div>
        <div class="col-sm-6"><label class="form-label fw-600 small">WhatsApp</label><input type="tel" name="whatsapp" class="form-control" value="<?= h($lead['whatsapp'] ?? '') ?>"></div>
      </div>
    </div>

    <div class="stat-card mb-4">
      <h6 class="fw-700 mb-4 text-uppercase" style="font-size:.7rem;letter-spacing:.1em;color:#888;">Child Details</h6>
      <div class="row g-3">
        <div class="col-sm-6"><label class="form-label fw-600 small">Child's Name</label><input type="text" name="child_name" class="form-control" value="<?= h($lead['child_name'] ?? '') ?>"></div>
        <div class="col-sm-6">
          <label class="form-label fw-600 small">Year Group</label>
          <select name="child_year" class="form-select">
            <option value="">Select…</option>
            <?php foreach ($yearGroups as $yg): ?>
              <option value="<?= h($yg) ?>" <?= ($lead['child_year']??'')===$yg?'selected':'' ?>><?= h($yg) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-sm-6">
          <label class="form-label fw-600 small">Course Interest</label>
          <select name="course_interest" class="form-select">
            <option value="">Select…</option>
            <?php foreach ($programmes as $p): ?>
              <option value="<?= h($p['name']) ?>" <?= ($lead['course_interest']??'')===$p['name']?'selected':'' ?>>
                <?= h($p['name']) ?><?= $p['year_range'] ? ' ('.$p['year_range'].')' : '' ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-sm-6">
          <label class="form-label fw-600 small">Preferred Centre</label>
          <select name="centre" class="form-select">
            <?php foreach ($branches as $c): ?>
              <option value="<?= h($c) ?>" <?= ($lead['centre']??'')===$c?'selected':'' ?>><?= h($c) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
    </div>

    <div class="stat-card">
      <h6 class="fw-700 mb-4 text-uppercase" style="font-size:.7rem;letter-spacing:.1em;color:#888;">CRM Details</h6>
      <div class="row g-3">
        <div class="col-sm-4">
          <label class="form-label fw-600 small">Source</label>
          <select name="source" class="form-select">
            <?php foreach ($leadSources as $s): ?>
              <option value="<?= h($s) ?>" <?= ($lead['source']??'')===$s?'selected':'' ?>><?= h($s) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-sm-4">
          <label class="form-label fw-600 small">Assign To</label>
          <select name="assigned_to" class="form-select">
            <option value="">Unassigned</option>
            <?php foreach ($staffList as $s): ?>
              <option value="<?= $s['id'] ?>" <?= ($lead['assigned_to']??0)==$s['id']?'selected':'' ?>><?= h($s['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-sm-4"><label class="form-label fw-600 small">Next Follow-Up</label><input type="date" name="next_followup_date" class="form-control" value="<?= h($lead['next_followup_date'] ?? '') ?>"></div>
        <div class="col-12"><label class="form-label fw-600 small">Notes</label><textarea name="notes" class="form-control" rows="3"><?= h($lead['notes'] ?? '') ?></textarea></div>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="stat-card" style="background:#f8f9ff;border:1px dashed #dee2e6;">
      <h6 class="fw-700 mb-3"><i class="bi bi-info-circle text-primary me-2"></i>Lead Status</h6>
      <div class="mb-2 small text-muted">Current status: <?= leadStatusBadge($lead['status']) ?></div>
      <p class="small text-muted">Change status from the Lead Detail view using the status dropdown on the sidebar.</p>
      <a href="view.php?id=<?= $id ?>" class="btn btn-sm btn-outline-secondary">Go to Lead View</a>
    </div>
  </div>

  <div class="col-12">
    <button type="submit" class="btn btn-dark"><i class="bi bi-save me-1"></i>Save Changes</button>
    <a href="view.php?id=<?= $id ?>" class="btn btn-outline-secondary ms-2">Cancel</a>
  </div>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
