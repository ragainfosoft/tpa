<?php
// =====================================================
// TPA IMS — Add New Lead
// =====================================================

$page_title   = 'Add Lead';
$page_section = 'leads';
require_once __DIR__ . '/../includes/header.php';

$db = getDB();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $name       = trim($_POST['name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $phone      = trim($_POST['phone'] ?? '');
    $whatsapp   = trim($_POST['whatsapp'] ?? '');
    $childName  = trim($_POST['child_name'] ?? '');
    $childYear  = trim($_POST['child_year'] ?? '');
    $courseInt  = trim($_POST['course_interest'] ?? '');
    $centre     = $_POST['centre'] ?? 'No preference';
    $source     = $_POST['source'] ?? 'Other';
    $notes      = trim($_POST['notes'] ?? '');
    $assignedTo = (int)($_POST['assigned_to'] ?? 0) ?: null;
    $followupDate = $_POST['next_followup_date'] ?? '';

    if (!$name)  $errors[] = 'Parent/Guardian name is required.';
    if (!$phone && !$email) $errors[] = 'At least a phone number or email is required.';

    if (empty($errors)) {
        $db->prepare('INSERT INTO leads (name,email,phone,whatsapp,child_name,child_year,course_interest,centre,source,notes,assigned_to,next_followup_date) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)')
           ->execute([$name,$email,$phone,$whatsapp,$childName,$childYear,$courseInt,$centre,$source,$notes,$assignedTo,$followupDate?:null]);
        $newId = $db->lastInsertId();
        logActivity('lead_added', "New lead #$newId: $name");
        setFlash('success', "Lead for $name added successfully.");
        header('Location: view.php?id=' . $newId);
        exit;
    }
}

$staffList = $db->query('SELECT id, name FROM users WHERE role IN ("admin","staff","branch_manager") AND is_active = 1 ORDER BY name')->fetchAll();
require_once __DIR__ . '/../includes/helpers.php';
$branches    = getBranchNames(true);
$programmes  = getProgrammes();
$leadSources = getLeadSources();
$yearGroups  = getYearGroups();
?>

<div class="page-header">
  <h1><i class="bi bi-person-plus me-2" style="color:var(--gold);"></i>Add New Lead</h1>
  <a href="index.php" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<?php if ($errors): ?>
  <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e) echo '<li>'.h($e).'</li>'; ?></ul></div>
<?php endif; ?>

<div class="row g-4">
<div class="col-lg-8">
<div class="stat-card">
<form method="POST">
  <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">

  <h6 class="fw-700 mb-3 text-uppercase" style="font-size:.7rem;letter-spacing:.1em;color:#888;">Parent / Guardian Details</h6>
  <div class="row g-3 mb-4">
    <div class="col-sm-6">
      <label class="form-label fw-600 small">Full Name *</label>
      <input type="text" name="name" class="form-control" value="<?= h($_POST['name'] ?? '') ?>" required placeholder="Jane Smith">
    </div>
    <div class="col-sm-6">
      <label class="form-label fw-600 small">Email</label>
      <input type="email" name="email" class="form-control" value="<?= h($_POST['email'] ?? '') ?>" placeholder="jane@example.com">
    </div>
    <div class="col-sm-6">
      <label class="form-label fw-600 small">Phone *</label>
      <input type="tel" name="phone" class="form-control" value="<?= h($_POST['phone'] ?? '') ?>" placeholder="07xxx xxxxxx">
    </div>
    <div class="col-sm-6">
      <label class="form-label fw-600 small">WhatsApp <span class="text-muted fw-400">(if different)</span></label>
      <input type="tel" name="whatsapp" class="form-control" value="<?= h($_POST['whatsapp'] ?? '') ?>" placeholder="07xxx xxxxxx">
    </div>
  </div>

  <h6 class="fw-700 mb-3 text-uppercase" style="font-size:.7rem;letter-spacing:.1em;color:#888;">Child Details</h6>
  <div class="row g-3 mb-4">
    <div class="col-sm-6">
      <label class="form-label fw-600 small">Child's Name</label>
      <input type="text" name="child_name" class="form-control" value="<?= h($_POST['child_name'] ?? '') ?>" placeholder="First name">
    </div>
    <div class="col-sm-6">
      <label class="form-label fw-600 small">Year Group</label>
      <select name="child_year" class="form-select">
        <option value="">Select…</option>
        <?php foreach ($yearGroups as $yg): ?>
          <option value="<?= $yg ?>" <?= ($_POST['child_year']??'')===$yg?'selected':'' ?>><?= $yg ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-sm-6">
      <label class="form-label fw-600 small">Course Interest</label>
      <select name="course_interest" class="form-select">
        <option value="">Select…</option>
        <?php foreach ($programmes as $p): ?>
          <option value="<?= h($p['name']) ?>" <?= ($_POST['course_interest']??'')===$p['name']?'selected':'' ?>>
            <?= h($p['name']) ?><?= $p['year_range'] ? ' ('.$p['year_range'].')' : '' ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-sm-6">
      <label class="form-label fw-600 small">Preferred Centre</label>
      <select name="centre" class="form-select">
        <?php foreach ($branches as $c): ?>
          <option value="<?= h($c) ?>" <?= ($_POST['centre']??'')===$c?'selected':'' ?>><?= h($c) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <h6 class="fw-700 mb-3 text-uppercase" style="font-size:.7rem;letter-spacing:.1em;color:#888;">CRM Details</h6>
  <div class="row g-3 mb-4">
    <div class="col-sm-6">
      <label class="form-label fw-600 small">Source</label>
      <select name="source" class="form-select">
        <?php foreach ($leadSources as $s): ?>
          <option value="<?= h($s) ?>" <?= ($_POST['source']??'')===$s?'selected':'' ?>><?= h($s) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-sm-6">
      <label class="form-label fw-600 small">Assign To</label>
      <select name="assigned_to" class="form-select">
        <option value="">Unassigned</option>
        <?php foreach ($staffList as $s): ?>
          <option value="<?= $s['id'] ?>" <?= (int)($_POST['assigned_to']??0)===$s['id']?'selected':'' ?>><?= h($s['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-sm-6">
      <label class="form-label fw-600 small">Next Follow-Up Date</label>
      <input type="date" name="next_followup_date" class="form-control" value="<?= h($_POST['next_followup_date'] ?? '') ?>" min="<?= date('Y-m-d') ?>">
    </div>
    <div class="col-12">
      <label class="form-label fw-600 small">Notes</label>
      <textarea name="notes" class="form-control" rows="3" placeholder="Any additional information…"><?= h($_POST['notes'] ?? '') ?></textarea>
    </div>
  </div>

  <div class="d-flex gap-2">
    <button type="submit" class="btn btn-dark"><i class="bi bi-check-lg me-1"></i>Save Lead</button>
    <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
  </div>
</form>
</div>
</div>

<div class="col-lg-4">
  <div class="stat-card" style="background:#f8f9ff;border:1px dashed #dee2e6;">
    <h6 class="fw-700 mb-3"><i class="bi bi-lightbulb text-warning me-2"></i>Tips</h6>
    <ul class="small text-muted ps-3">
      <li class="mb-2">Add the WhatsApp number to enable one-click messaging from the lead card.</li>
      <li class="mb-2">Set a follow-up date so it appears on the dashboard reminder widget.</li>
      <li class="mb-2">Assign the lead to a staff member so they receive follow-up notifications.</li>
      <li>When the lead enrols, use the "Convert to Student" button on the lead detail page.</li>
    </ul>
  </div>
</div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
