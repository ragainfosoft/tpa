<?php
// =====================================================
// TPA IMS — Add Student
// =====================================================

$page_title   = 'Add Student';
$page_section = 'students';
require_once __DIR__ . '/../includes/header.php';

$branches   = getBranchNames(false);
$yearGroups = getYearGroups();

$db     = getDB();
$errors = [];
$data   = ['first_name'=>'','last_name'=>'','dob'=>'','year_group'=>'','school'=>'','gender'=>'','centre'=>'','join_date'=>date('Y-m-d'),'notes'=>'','medical_notes'=>'',
           'parent_name'=>'','parent_relationship'=>'Mother','parent_email'=>'','parent_phone'=>'','parent_whatsapp'=>''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $data = array_merge($data, $_POST);

    if (empty(trim($data['first_name']))) $errors[] = 'First name is required.';
    if (empty(trim($data['last_name'])))  $errors[] = 'Last name is required.';
    if (empty(trim($data['parent_name']))) $errors[] = 'Parent/Guardian name is required.';

    if (empty($errors)) {
        $ref = generateStudentRef();
        $db->prepare('INSERT INTO students (student_ref,first_name,last_name,dob,year_group,school,gender,centre,join_date,notes,medical_notes) VALUES (?,?,?,?,?,?,?,?,?,?,?)')
           ->execute([$ref, trim($data['first_name']), trim($data['last_name']), $data['dob']?:null, $data['year_group'], $data['school'], $data['gender'], $data['centre'], $data['join_date']?:null, $data['notes'], $data['medical_notes']]);
        $studentId = $db->lastInsertId();

        // Add parent
        $db->prepare('INSERT INTO student_parents (student_id,parent_name,relationship,email,phone,whatsapp,is_primary) VALUES (?,?,?,?,?,?,1)')
           ->execute([$studentId, trim($data['parent_name']), $data['parent_relationship'], $data['parent_email'], $data['parent_phone'], $data['parent_whatsapp']]);
        $parentId = $db->lastInsertId();

        // Auto-create user account for parent if email provided
        if (!empty(trim($data['parent_email']))) {
            $parentEmail = trim($data['parent_email']);
            $checkUser = $db->prepare("SELECT id FROM users WHERE email = ?");
            $checkUser->execute([$parentEmail]);
            $existingUser = $checkUser->fetch();

            if (!$existingUser) {
                // Create new user
                $prefix   = getSetting('default_password_prefix', 'Acad@');
                $tempPass = $prefix . ($data['parent_phone'] ? substr(preg_replace('/\D/', '', $data['parent_phone']), -4) : date('Y'));
                $hash = password_hash($tempPass, PASSWORD_DEFAULT);
                $db->prepare("INSERT INTO users (name, email, password_hash, role, is_active) VALUES (?, ?, ?, 'parent', 1)")
                   ->execute([trim($data['parent_name']), $parentEmail, $hash]);
                $newUserId = $db->lastInsertId();
                
                // Link to parent record
                $db->prepare("UPDATE student_parents SET user_id = ? WHERE id = ?")->execute([$newUserId, $parentId]);
                
                logActivity('portal_account_created', "Auto-created parent portal for {$data['parent_name']} ($parentEmail)");
            } else {
                // Link to existing user
                $db->prepare("UPDATE student_parents SET user_id = ? WHERE id = ?")->execute([$existingUser['id'], $parentId]);
            }
        }

        logActivity('student_created', "Student $ref — {$data['first_name']} {$data['last_name']}");
        setFlash('success', "Student {$data['first_name']} {$data['last_name']} ($ref) added successfully. Parent portal account ensured.");
        header('Location: index.php'); exit;
    }
}
?>

<div class="page-header">
  <h1><i class="bi bi-person-plus me-2" style="color:var(--gold);"></i>Add New Student</h1>
  <a href="index.php" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<?php if ($errors): ?>
  <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= h($e) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<form method="POST" class="row g-4">
  <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">

  <!-- Student Details -->
  <div class="col-lg-8">
    <div class="stat-card">
      <h6 class="fw-700 mb-4 text-uppercase" style="font-size:.7rem;letter-spacing:.1em;color:#888;">Student Information</h6>
      <div class="row g-3">
        <div class="col-sm-6">
          <label class="form-label fw-600 small">First Name <span class="text-danger">*</span></label>
          <input type="text" name="first_name" class="form-control" value="<?= h($data['first_name']) ?>" required>
        </div>
        <div class="col-sm-6">
          <label class="form-label fw-600 small">Last Name <span class="text-danger">*</span></label>
          <input type="text" name="last_name" class="form-control" value="<?= h($data['last_name']) ?>" required>
        </div>
        <div class="col-sm-4">
          <label class="form-label fw-600 small">Date of Birth</label>
          <input type="date" name="dob" class="form-control" value="<?= h($data['dob']) ?>">
        </div>
        <div class="col-sm-4">
          <label class="form-label fw-600 small">Year Group</label>
          <select name="year_group" class="form-select">
            <option value="">Select…</option>
            <?php foreach ($yearGroups as $yg): ?>
              <option value="<?= h($yg) ?>" <?= $data['year_group']===$yg?'selected':'' ?>><?= h($yg) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-sm-4">
          <label class="form-label fw-600 small">Gender</label>
          <select name="gender" class="form-select">
            <option value="">Select…</option>
            <?php foreach (['Male','Female','Other','Prefer not to say'] as $g): ?>
              <option value="<?= $g ?>" <?= $data['gender']===$g?'selected':'' ?>><?= $g ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-sm-8">
          <label class="form-label fw-600 small">Current School</label>
          <input type="text" name="school" class="form-control" value="<?= h($data['school']) ?>">
        </div>
        <div class="col-sm-4">
          <label class="form-label fw-600 small">Centre</label>
          <select name="centre" class="form-select">
            <option value="">Select…</option>
            <?php foreach ($branches as $c): ?>
              <option value="<?= h($c) ?>" <?= $data['centre']===$c?'selected':'' ?>><?= h($c) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-sm-4">
          <label class="form-label fw-600 small">Join Date</label>
          <input type="date" name="join_date" class="form-control" value="<?= h($data['join_date']) ?>">
        </div>
        <div class="col-12">
          <label class="form-label fw-600 small">Notes</label>
          <textarea name="notes" class="form-control" rows="2"><?= h($data['notes']) ?></textarea>
        </div>
        <div class="col-12">
          <label class="form-label fw-600 small">Medical / SEN Notes</label>
          <textarea name="medical_notes" class="form-control" rows="2"><?= h($data['medical_notes']) ?></textarea>
        </div>
      </div>
    </div>
  </div>

  <!-- Parent Details -->
  <div class="col-lg-4">
    <div class="stat-card">
      <h6 class="fw-700 mb-4 text-uppercase" style="font-size:.7rem;letter-spacing:.1em;color:#888;">Parent / Guardian</h6>
      <div class="row g-3">
        <div class="col-12">
          <label class="form-label fw-600 small">Full Name <span class="text-danger">*</span></label>
          <input type="text" name="parent_name" class="form-control" value="<?= h($data['parent_name']) ?>" required>
        </div>
        <div class="col-12">
          <label class="form-label fw-600 small">Relationship</label>
          <select name="parent_relationship" class="form-select">
            <?php foreach (['Mother','Father','Guardian','Other'] as $r): ?>
              <option value="<?= $r ?>" <?= $data['parent_relationship']===$r?'selected':'' ?>><?= $r ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12">
          <label class="form-label fw-600 small">Email</label>
          <input type="email" name="parent_email" class="form-control" value="<?= h($data['parent_email']) ?>">
        </div>
        <div class="col-12">
          <label class="form-label fw-600 small">Phone</label>
          <input type="tel" name="parent_phone" class="form-control" value="<?= h($data['parent_phone']) ?>">
        </div>
        <div class="col-12">
          <label class="form-label fw-600 small">WhatsApp</label>
          <input type="tel" name="parent_whatsapp" class="form-control" value="<?= h($data['parent_whatsapp']) ?>" placeholder="Same as phone if identical">
        </div>
      </div>
    </div>
  </div>

  <div class="col-12">
    <button type="submit" class="btn btn-success"><i class="bi bi-person-check me-1"></i>Save Student</button>
    <a href="index.php" class="btn btn-outline-secondary ms-2">Cancel</a>
  </div>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
