<?php
// =====================================================
// TPA IMS — Edit Student
// =====================================================

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
startSecureSession();
requireLogin(SITE_URL . '/login.php');

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: index.php'); exit; }

$db      = getDB();
$student = $db->prepare('SELECT * FROM students WHERE id = ?');
$student->execute([$id]); $student = $student->fetch();
if (!$student) { setFlash('danger','Student not found.'); header('Location: index.php'); exit; }

$parent = $db->prepare('SELECT * FROM student_parents WHERE student_id=? AND is_primary=1 LIMIT 1');
$parent->execute([$id]); $parent = $parent->fetch() ?: [];

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $errors = [];

    if (empty(trim($_POST['first_name'] ?? ''))) $errors[] = 'First name required.';
    if (empty(trim($_POST['last_name'] ?? '')))  $errors[] = 'Last name required.';

    if (empty($errors)) {
        $db->prepare('UPDATE students SET first_name=?,last_name=?,dob=?,year_group=?,school=?,gender=?,centre=?,join_date=?,notes=?,medical_notes=?,status=?,updated_at=NOW() WHERE id=?')
           ->execute([trim($_POST['first_name']),trim($_POST['last_name']),$_POST['dob']?:null,$_POST['year_group'],$_POST['school'],$_POST['gender'],$_POST['centre'],$_POST['join_date']?:null,$_POST['notes'],$_POST['medical_notes'],$_POST['status'],$id]);

        // Update/insert primary parent
        if ($parent) {
            $db->prepare('UPDATE student_parents SET parent_name=?,relationship=?,email=?,phone=?,whatsapp=? WHERE id=?')
               ->execute([trim($_POST['parent_name']),$_POST['parent_relationship'],$_POST['parent_email'],$_POST['parent_phone'],$_POST['parent_whatsapp'],$parent['id']]);
        } else {
            $db->prepare('INSERT INTO student_parents (student_id,parent_name,relationship,email,phone,whatsapp,is_primary) VALUES (?,?,?,?,?,?,1)')
               ->execute([$id,trim($_POST['parent_name']),$_POST['parent_relationship'],$_POST['parent_email'],$_POST['parent_phone'],$_POST['parent_whatsapp']]);
        }

        logActivity('student_updated', "Student #$id updated");
        setFlash('success', 'Student profile updated.');
        header('Location: view.php?id=' . $id); exit;
    }

    // Repopulate from POST on error
    $student = array_merge($student, $_POST);
    $parent  = array_merge($parent, $_POST);
}

$page_title   = 'Edit Student';
$page_section = 'students';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
  <div>
    <h1><i class="bi bi-pencil-square me-2" style="color:var(--gold);"></i>Edit Student</h1>
    <div class="text-muted small"><?= h($student['student_ref'] ?? '') ?></div>
  </div>
  <a href="view.php?id=<?= $id ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<?php if ($errors): ?>
  <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= h($e) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<form method="POST" class="row g-4">
  <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">

  <div class="col-lg-8">
    <!-- Student info -->
    <div class="stat-card mb-4">
      <h6 class="fw-700 mb-4 text-uppercase" style="font-size:.7rem;letter-spacing:.1em;color:#888;">Student Information</h6>
      <div class="row g-3">
        <div class="col-sm-6"><label class="form-label fw-600 small">First Name *</label><input type="text" name="first_name" class="form-control" value="<?= h($student['first_name']) ?>" required></div>
        <div class="col-sm-6"><label class="form-label fw-600 small">Last Name *</label><input type="text" name="last_name" class="form-control" value="<?= h($student['last_name']) ?>" required></div>
        <div class="col-sm-4"><label class="form-label fw-600 small">Date of Birth</label><input type="date" name="dob" class="form-control" value="<?= h($student['dob'] ?? '') ?>"></div>
        <div class="col-sm-4">
          <label class="form-label fw-600 small">Year Group</label>
          <select name="year_group" class="form-select">
            <option value="">Select…</option>
            <?php foreach (getYearGroups() as $yg): ?>
              <option value="<?= h($yg) ?>" <?= ($student['year_group']??'')===$yg?'selected':'' ?>><?= h($yg) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-sm-4">
          <label class="form-label fw-600 small">Gender</label>
          <select name="gender" class="form-select">
            <option value="">Select…</option>
            <?php foreach (['Male','Female','Other','Prefer not to say'] as $g): ?>
              <option value="<?= $g ?>" <?= ($student['gender']??'')===$g?'selected':'' ?>><?= $g ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-sm-6"><label class="form-label fw-600 small">School</label><input type="text" name="school" class="form-control" value="<?= h($student['school'] ?? '') ?>"></div>
        <div class="col-sm-3">
          <label class="form-label fw-600 small">Centre</label>
          <select name="centre" class="form-select">
            <option value="">Select…</option>
            <?php foreach (['Romford','Chelmsford','Online','Both'] as $c): ?>
              <option value="<?= $c ?>" <?= ($student['centre']??'')===$c?'selected':'' ?>><?= $c ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-sm-3">
          <label class="form-label fw-600 small">Status</label>
          <select name="status" class="form-select">
            <?php foreach (['active','inactive','suspended','left'] as $st): ?>
              <option value="<?= $st ?>" <?= ($student['status']??'')===$st?'selected':'' ?>><?= ucfirst($st) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-sm-4"><label class="form-label fw-600 small">Join Date</label><input type="date" name="join_date" class="form-control" value="<?= h($student['join_date'] ?? '') ?>"></div>
        <div class="col-12"><label class="form-label fw-600 small">Notes</label><textarea name="notes" class="form-control" rows="2"><?= h($student['notes'] ?? '') ?></textarea></div>
        <div class="col-12"><label class="form-label fw-600 small">Medical / SEN Notes</label><textarea name="medical_notes" class="form-control" rows="2"><?= h($student['medical_notes'] ?? '') ?></textarea></div>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <!-- Parent info -->
    <div class="stat-card">
      <h6 class="fw-700 mb-4 text-uppercase" style="font-size:.7rem;letter-spacing:.1em;color:#888;">Primary Parent / Guardian</h6>
      <div class="row g-3">
        <div class="col-12"><label class="form-label fw-600 small">Full Name</label><input type="text" name="parent_name" class="form-control" value="<?= h($parent['parent_name'] ?? '') ?>"></div>
        <div class="col-12">
          <label class="form-label fw-600 small">Relationship</label>
          <select name="parent_relationship" class="form-select">
            <?php foreach (['Mother','Father','Guardian','Other'] as $r): ?>
              <option value="<?= $r ?>" <?= ($parent['relationship']??'')===$r?'selected':'' ?>><?= $r ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12"><label class="form-label fw-600 small">Email</label><input type="email" name="parent_email" class="form-control" value="<?= h($parent['email'] ?? '') ?>"></div>
        <div class="col-12"><label class="form-label fw-600 small">Phone</label><input type="tel" name="parent_phone" class="form-control" value="<?= h($parent['phone'] ?? '') ?>"></div>
        <div class="col-12"><label class="form-label fw-600 small">WhatsApp</label><input type="tel" name="parent_whatsapp" class="form-control" value="<?= h($parent['whatsapp'] ?? '') ?>"></div>
      </div>
    </div>
  </div>

  <div class="col-12">
    <button type="submit" class="btn btn-dark"><i class="bi bi-save me-1"></i>Save Changes</button>
    <a href="view.php?id=<?= $id ?>" class="btn btn-outline-secondary ms-2">Cancel</a>
  </div>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
