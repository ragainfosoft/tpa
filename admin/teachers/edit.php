<?php
// =====================================================
// TPA IMS — Edit Teacher
// =====================================================

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
startSecureSession();
requireLogin(SITE_URL . '/login.php');

$db = getDB();
$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: index.php'); exit; }

$teacher = $db->prepare("SELECT t.*, u.name, u.email 
                         FROM teachers t 
                         JOIN users u ON t.user_id = u.id 
                         WHERE t.id = ?");
$teacher->execute([$id]);
$teacher = $teacher->fetch();
if (!$teacher) { setFlash('danger', 'Teacher not found.'); header('Location: index.php'); exit; }

// Handle update teacher
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $subjects = trim($_POST['subjects']);
    $qual     = trim($_POST['qualification']);
    $dbs      = trim($_POST['dbs_number']);
    $expiry   = $_POST['dbs_expiry'] ?: null;
    $is_active= isset($_POST['is_active']) ? 1 : 0;

    $errors = [];
    if (empty($name))   $errors[] = "Name is required.";
    if (empty($email))  $errors[] = "Email is required.";

    if (empty($errors)) {
        try {
            $db->beginTransaction();

            // Update user
            $stmt = $db->prepare("UPDATE users SET name=?, email=? WHERE id=?");
            $stmt->execute([$name, $email, $teacher['user_id']]);

            // Update teacher
            $stmt = $db->prepare("UPDATE teachers SET subjects=?, qualification=?, dbs_number=?, dbs_expiry=?, is_active=? WHERE id=?");
            $stmt->execute([$subjects, $qual, $dbs, $expiry, $is_active, $id]);

            // Handle password change if provided
            if (!empty($_POST['password'])) {
                $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $db->prepare("UPDATE users SET password_hash=? WHERE id=?")->execute([$hash, $teacher['user_id']]);
            }

            $db->commit();
            setFlash('success', "Teacher updated successfully.");
            header('Location: index.php'); exit;
        } catch (Exception $e) {
            $db->rollBack();
            $errors[] = "Error: " . $e->getMessage();
        }
    }
    if ($errors) setFlash('danger', implode('<br>', $errors));
}

$page_title   = 'Edit Teacher: ' . $teacher['name'];
$page_section = 'teachers';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
  <h1><i class="bi bi-pencil me-2" style="color:var(--gold);"></i>Edit Teacher Profile</h1>
  <a href="index.php" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<div class="row g-4">
  <div class="col-lg-8">
    <div class="stat-card">
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
        
        <div class="row g-3">
          <div class="col-12"><h6 class="fw-700 text-uppercase small text-muted mb-3">User Account</h6></div>
          <div class="col-sm-6">
            <label class="fw-600 small form-label">Full Name *</label>
            <input type="text" name="name" class="form-control" value="<?= h($teacher['name']) ?>" required>
          </div>
          <div class="col-sm-6">
            <label class="fw-600 small form-label">Email Address *</label>
            <input type="email" name="email" class="form-control" value="<?= h($teacher['email']) ?>" required>
          </div>
          <div class="col-12 mt-2">
            <label class="fw-600 small form-label">New Password (leave blank to keep current)</label>
            <input type="password" name="password" class="form-control" placeholder="Optional">
          </div>

          <div class="col-12 mt-4"><h6 class="fw-700 text-uppercase small text-muted mb-3">Professional Details</h6></div>
          <div class="col-sm-6">
            <label class="fw-600 small form-label">Subjects</label>
            <input type="text" name="subjects" class="form-control" value="<?= h($teacher['subjects']) ?>">
          </div>
          <div class="col-sm-6">
            <label class="fw-600 small form-label">Teacher Qualification</label>
            <input type="text" name="qualification" class="form-control" value="<?= h($teacher['qualification']) ?>">
          </div>
          <div class="col-sm-6">
            <label class="fw-600 small form-label">DBS Number</label>
            <input type="text" name="dbs_number" class="form-control" value="<?= h($teacher['dbs_number']) ?>">
          </div>
          <div class="col-sm-6">
            <label class="fw-600 small form-label">DBS Expiry Date</label>
            <input type="date" name="dbs_expiry" class="form-control" value="<?= h($teacher['dbs_expiry']) ?>">
          </div>
          
          <div class="col-12 mt-3">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" name="is_active" <?= $teacher['is_active'] ? 'checked' : '' ?>>
              <label class="form-check-label fw-600 small">Active Account</label>
            </div>
          </div>

          <div class="col-12 mt-4">
            <button class="btn btn-success"><i class="bi bi-check-circle me-1"></i>Save Changes</button>
            <a href="index.php" class="btn btn-outline-secondary ms-2">Cancel</a>
          </div>
        </div>
      </form>
    </div>
  </div>
  
  <div class="col-lg-4">
    <div class="stat-card bg-light border-0">
      <h6 class="fw-700 text-uppercase small text-muted mb-3">Teacher Stats</h6>
      <div class="d-flex flex-column gap-3">
        <?php
        $enrolledCount = (int)$db->prepare("SELECT COUNT(*) FROM batches WHERE teacher_id = ? AND is_active = 1")->execute([$id]);
        $enrolledCount = $db->prepare("SELECT COUNT(*) FROM batches WHERE teacher_id = ? AND is_active = 1");
        $enrolledCount->execute([$id]);
        $enrolledCount = $enrolledCount->fetchColumn();
        ?>
        <div class="d-flex justify-content-between align-items-center">
          <span class="small text-muted">Active Batches</span>
          <span class="badge bg-navy px-3" style="font-size:.85rem;"><?= $enrolledCount ?></span>
        </div>
        <div class="d-flex justify-content-between align-items-center">
          <span class="small text-muted">Centre Access</span>
          <span class="small fw-700">All Centres</span>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
