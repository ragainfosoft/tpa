<?php
// =====================================================
// TPA IMS — Convert Lead to Student
// =====================================================

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
startSecureSession();
requireLogin(SITE_URL . '/login.php');

$leadId = (int)($_GET['lead_id'] ?? $_GET['id'] ?? $_GET['from_lead'] ?? 0);
if (!$leadId) { header('Location: index.php'); exit; }

$db   = getDB();
$lead = $db->prepare('SELECT * FROM leads WHERE id = ?');
$lead->execute([$leadId]); $lead = $lead->fetch();
if (!$lead) { setFlash('danger','Lead not found.'); header('Location: index.php'); exit; }

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    if (empty(trim($_POST['first_name'] ?? ''))) $errors[] = 'First name required.';
    if (empty(trim($_POST['last_name'] ?? '')))  $errors[] = 'Last name required.';
    if (empty(trim($_POST['parent_name'] ?? ''))) $errors[] = 'Parent name required.';

    if (empty($errors)) {
        // 1. Create student
        $ref = generateStudentRef();
        $db->prepare('INSERT INTO students (student_ref,first_name,last_name,dob,year_group,school,gender,centre,join_date,notes,status) VALUES (?,?,?,?,?,?,?,?,?,?,?)')
           ->execute([$ref, trim($_POST['first_name']), trim($_POST['last_name']), $_POST['dob']?:null, $_POST['year_group'], trim($_POST['school']), $_POST['gender'], $_POST['centre'], date('Y-m-d'), trim($_POST['notes']), 'active']);
        $studentId = $db->lastInsertId();

        // 2. Create parent contact
        $db->prepare('INSERT INTO student_parents (student_id,parent_name,relationship,email,phone,whatsapp,is_primary) VALUES (?,?,?,?,?,?,1)')
           ->execute([$studentId, trim($_POST['parent_name']), $_POST['parent_relationship'], trim($_POST['parent_email']), trim($_POST['parent_phone']), trim($_POST['parent_whatsapp'])]);
        $parentId = $db->lastInsertId();

        // Auto-create user account for parent if email provided
        if (!empty(trim($_POST['parent_email'] ?? ''))) {
            $parentEmail = trim($_POST['parent_email']);
            $parentName  = trim($_POST['parent_name']);
            $parentPhone = trim($_POST['parent_phone']);
            
            $checkUser = $db->prepare("SELECT id FROM users WHERE email = ?");
            $checkUser->execute([$parentEmail]);
            $existingUser = $checkUser->fetch();

            if (!$existingUser) {
                // Create new user
                $tempPass = 'Tpa@' . ($parentPhone ? substr(preg_replace('/\D/', '', $parentPhone), -4) : '2026');
                $hash = password_hash($tempPass, PASSWORD_DEFAULT);
                $db->prepare("INSERT INTO users (name, email, password_hash, role, is_active) VALUES (?, ?, ?, 'parent', 1)")
                   ->execute([$parentName, $parentEmail, $hash]);
                $newUserId = $db->lastInsertId();
                
                // Link to parent record
                $db->prepare("UPDATE student_parents SET user_id = ? WHERE id = ?")->execute([$newUserId, $parentId]);
                
                logActivity('portal_account_created', "Auto-created parent portal for $parentName ($parentEmail)");
            } else {
                // Link to existing user
                $db->prepare("UPDATE student_parents SET user_id = ? WHERE id = ?")->execute([$existingUser['id'], $parentId]);
            }
        }

        // 3. Mark lead as enrolled
        $db->prepare('UPDATE leads SET status="enrolled", updated_at=NOW() WHERE id=?')->execute([$leadId]);

        // 4. Send WhatsApp welcome if template exists
        $waTemplate = getSetting('wa_template_enrolled') ?? '';
        if ($waTemplate && (trim($_POST['parent_whatsapp']) ?: trim($_POST['parent_phone']))) {
            require_once __DIR__ . '/../includes/WhatsAppService.php';
            $wa  = new WhatsAppService();
            $msg = str_replace(['{parent_name}','{student_name}','{student_ref}'],
                               [trim($_POST['parent_name']), trim($_POST['first_name']).' '.trim($_POST['last_name']), $ref],
                               $waTemplate);
            $wa->sendText(trim($_POST['parent_whatsapp']) ?: trim($_POST['parent_phone']), $msg);
        }

        logActivity('lead_converted', "Lead #$leadId converted → Student $ref");
        setFlash('success', "🎉 Lead converted! Student ".trim($_POST['first_name'])." ".trim($_POST['last_name'])." ($ref) enrolled.");
        header('Location: ../students/view.php?id=' . $studentId);
        exit;
    }
}

// Pre-fill from lead
$d = [
    'first_name'          => explode(' ', $lead['child_name'] ?? '')[0] ?? '',
    'last_name'           => implode(' ', array_slice(explode(' ', $lead['child_name'] ?? ''), 1)) ?: '',
    'year_group'          => $lead['child_year'] ?? '',
    'centre'              => $lead['centre'] ?? '',
    'parent_name'         => $lead['name'] ?? '',
    'parent_relationship' => 'Mother',
    'parent_email'        => $lead['email'] ?? '',
    'parent_phone'        => $lead['phone'] ?? '',
    'parent_whatsapp'     => $lead['whatsapp'] ?? '',
    'notes'               => $lead['notes'] ?? '',
    'school'              => '',
    'gender'              => '',
    'dob'                 => '',
];
if (!empty($errors)) $d = array_merge($d, $_POST);

require_once __DIR__ . '/../includes/helpers.php';
$branches   = getBranchNames(false);
$yearGroups = getYearGroups();

$page_title   = 'Convert Lead to Student';
$page_section = 'leads';
require_once __DIR__ . '/../includes/header.php';
?>

<style>
.convert-hero { background:linear-gradient(135deg,#0A1628 0%,#1a2e52 100%); border-radius:14px; padding:24px 28px; color:white; margin-bottom:24px; }
.step-badge { display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:50%;background:var(--gold);color:var(--navy);font-weight:900;font-size:.8rem;flex-shrink:0; }
</style>

<div class="page-header">
  <div>
    <h1><i class="bi bi-arrow-right-circle me-2" style="color:var(--gold);"></i>Convert Lead to Student</h1>
    <p class="text-muted mb-0 small">Lead: <strong><?= h($lead['name']) ?></strong> — Course interest: <?= h($lead['course_interest'] ?? '—') ?></p>
  </div>
  <a href="view.php?id=<?= $leadId ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back to Lead</a>
</div>

<!-- Lead summary -->
<div class="convert-hero mb-4">
  <div class="d-flex gap-3 align-items-center flex-wrap">
    <div style="font-size:1.8rem;">🎓</div>
    <div>
      <div class="fw-700 mb-1">Enrolling <?= h($lead['child_name'] ?: $lead['name']) ?></div>
      <div style="color:rgba(255,255,255,.7);font-size:.875rem;">
        <?= h($lead['course_interest'] ?? '—') ?> &middot; <?= h($lead['centre'] ?? '—') ?> &middot; Source: <?= h($lead['source'] ?? '—') ?>
      </div>
    </div>
    <div class="ms-auto"><span class="badge" style="background:rgba(245,166,35,.3);color:var(--gold);font-size:.8rem;padding:8px 14px;">Lead #<?= $leadId ?></span></div>
  </div>
</div>

<?php if ($errors): ?>
  <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= h($e) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<form method="POST" class="row g-4">
  <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">

  <!-- Step 1: Student -->
  <div class="col-lg-7">
    <div class="stat-card mb-4">
      <div class="d-flex align-items-center gap-3 mb-4">
        <div class="step-badge">1</div>
        <h6 class="fw-700 text-uppercase mb-0" style="font-size:.75rem;letter-spacing:.1em;color:#888;">Student Details</h6>
      </div>
      <div class="row g-3">
        <div class="col-sm-6"><label class="form-label fw-600 small">First Name *</label><input type="text" name="first_name" class="form-control" value="<?= h($d['first_name']) ?>" required></div>
        <div class="col-sm-6"><label class="form-label fw-600 small">Last Name *</label><input type="text" name="last_name" class="form-control" value="<?= h($d['last_name']) ?>" required></div>
        <div class="col-sm-4"><label class="form-label fw-600 small">Date of Birth</label><input type="date" name="dob" class="form-control" value="<?= h($d['dob']) ?>"></div>
        <div class="col-sm-4">
          <label class="form-label fw-600 small">Year Group</label>
          <select name="year_group" class="form-select">
            <option value="">Select…</option>
            <?php foreach ($yearGroups as $yg): ?>
              <option value="<?= h($yg) ?>" <?= $d['year_group']===$yg?'selected':'' ?>><?= h($yg) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-sm-4">
          <label class="form-label fw-600 small">Gender</label>
          <select name="gender" class="form-select">
            <option value="">Select…</option>
            <?php foreach (['Male','Female','Other','Prefer not to say'] as $g): ?>
              <option value="<?= $g ?>" <?= $d['gender']===$g?'selected':'' ?>><?= $g ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-sm-8"><label class="form-label fw-600 small">Current School</label><input type="text" name="school" class="form-control" value="<?= h($d['school']) ?>"></div>
        <div class="col-sm-4">
          <label class="form-label fw-600 small">Centre</label>
          <select name="centre" class="form-select">
            <option value="">Select…</option>
            <?php foreach ($branches as $c): ?>
              <option value="<?= h($c) ?>" <?= $d['centre']===$c?'selected':'' ?>><?= h($c) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12"><label class="form-label fw-600 small">Notes</label><textarea name="notes" class="form-control" rows="2"><?= h($d['notes']) ?></textarea></div>
      </div>
    </div>

    <!-- Step 2: Parent -->
    <div class="stat-card">
      <div class="d-flex align-items-center gap-3 mb-4">
        <div class="step-badge">2</div>
        <h6 class="fw-700 text-uppercase mb-0" style="font-size:.75rem;letter-spacing:.1em;color:#888;">Parent / Guardian Contact</h6>
      </div>
      <div class="row g-3">
        <div class="col-sm-8"><label class="form-label fw-600 small">Full Name *</label><input type="text" name="parent_name" class="form-control" value="<?= h($d['parent_name']) ?>" required></div>
        <div class="col-sm-4">
          <label class="form-label fw-600 small">Relationship</label>
          <select name="parent_relationship" class="form-select">
            <?php foreach (['Mother','Father','Guardian','Other'] as $r): ?>
              <option value="<?= $r ?>" <?= $d['parent_relationship']===$r?'selected':'' ?>><?= $r ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-sm-6"><label class="form-label fw-600 small">Email</label><input type="email" name="parent_email" class="form-control" value="<?= h($d['parent_email']) ?>"></div>
        <div class="col-sm-6"><label class="form-label fw-600 small">Phone</label><input type="tel" name="parent_phone" class="form-control" value="<?= h($d['parent_phone']) ?>"></div>
        <div class="col-12"><label class="form-label fw-600 small">WhatsApp</label><input type="tel" name="parent_whatsapp" class="form-control" value="<?= h($d['parent_whatsapp']) ?>" placeholder="Same as phone if identical"></div>
      </div>
    </div>
  </div>

  <!-- Step 3: Confirm -->
  <div class="col-lg-5">
    <div class="stat-card" style="position:sticky;top:80px;">
      <div class="d-flex align-items-center gap-3 mb-4">
        <div class="step-badge">3</div>
        <h6 class="fw-700 text-uppercase mb-0" style="font-size:.75rem;letter-spacing:.1em;color:#888;">Confirm & Enrol</h6>
      </div>
      <div class="alert alert-success border-0 mb-4" style="background:#f0fdf4;">
        <i class="bi bi-check-circle-fill text-success me-2"></i>
        <strong>What happens on save:</strong>
        <ul class="mb-0 mt-2 small">
          <li>A new student record is created</li>
          <li>Parent contact is saved</li>
          <li>Lead is marked as <strong>Enrolled</strong></li>
          <li>WhatsApp welcome message is sent (if set up)</li>
        </ul>
      </div>
      <button type="submit" class="btn btn-dark btn-lg w-100">
        <i class="bi bi-person-check me-2"></i>Enrol Student Now
      </button>
      <a href="view.php?id=<?= $leadId ?>" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>
    </div>
  </div>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
