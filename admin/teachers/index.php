<?php
// =====================================================
// TPA IMS — Teacher Management
// =====================================================

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
startSecureSession();
requireLogin(SITE_URL . '/login.php');

$db = getDB();

// Handle add teacher
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_teacher'])) {
    verifyCsrf();
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $subjects = trim($_POST['subjects']);
    $qual     = trim($_POST['qualification']);
    $dbs      = trim($_POST['dbs_number']);
    $expiry   = $_POST['dbs_expiry'] ?: null;

    $errors = [];
    if (empty($name))   $errors[] = "Name is required.";
    if (empty($email))  $errors[] = "Email is required.";
    if (empty($password)) $errors[] = "Password is required.";

    // Check if email exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) $errors[] = "Email already in use.";

    if (empty($errors)) {
        try {
            $db->beginTransaction();

            // Create user
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (name, email, password_hash, role, is_active) VALUES (?, ?, ?, 'teacher', 1)");
            $stmt->execute([$name, $email, $hash]);
            $userId = $db->lastInsertId();

            // Create teacher profile
            $stmt = $db->prepare("INSERT INTO teachers (user_id, subjects, qualification, dbs_number, dbs_expiry, is_active) VALUES (?, ?, ?, ?, ?, 1)");
            $stmt->execute([$userId, $subjects, $qual, $dbs, $expiry]);

            $db->commit();
            setFlash('success', "Teacher $name added successfully.");
            header('Location: index.php'); exit;
        } catch (Exception $e) {
            $db->rollBack();
            $errors[] = "Error: " . $e->getMessage();
        }
    }
    if ($errors) setFlash('danger', implode('<br>', $errors));
}

$page_title   = 'Teachers & Staff';
$page_section = 'teachers';
require_once __DIR__ . '/../includes/header.php';

$teachers = $db->query("SELECT t.*, u.name, u.email, u.last_login 
                        FROM teachers t 
                        JOIN users u ON t.user_id = u.id 
                        ORDER BY u.name ASC")->fetchAll();
?>

<div class="page-header">
  <h1><i class="bi bi-person-badge me-2" style="color:var(--gold);"></i>Teachers &amp; Staff</h1>
  <button class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#addTeacherModal"><i class="bi bi-plus-lg me-1"></i>Add Teacher</button>
</div>

<div class="tpa-table table-responsive">
  <table class="table table-hover mb-0 dt-table">
    <thead>
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Subjects</th>
        <th>DBS Status</th>
        <th>Last Login</th>
        <th>Status</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($teachers as $t): ?>
        <tr>
          <td class="fw-700"><?= h($t['name']) ?></td>
          <td><?= h($t['email']) ?></td>
          <td class="small"><?= h($t['subjects'] ?: '—') ?></td>
          <td class="small">
            <?php if ($t['dbs_number']): ?>
              <span class="text-success"><i class="bi bi-check-circle me-1"></i><?= h($t['dbs_number']) ?></span>
              <?php if ($t['dbs_expiry']): ?>
                <div class="text-muted" style="font-size:.7rem;">Exp: <?= formatDate($t['dbs_expiry']) ?></div>
              <?php endif; ?>
            <?php else: ?>
              <span class="text-muted">No DBS on record</span>
            <?php endif; ?>
          </td>
          <td class="small text-muted"><?= $t['last_login'] ? formatDate($t['last_login'], true) : 'Never' ?></td>
          <td>
            <?= $t['is_active'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' ?>
          </td>
          <td>
            <a href="edit.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:.75rem;">Edit</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- Add Teacher Modal -->
<div class="modal fade" id="addTeacherModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background:var(--navy);">
        <h5 class="modal-title text-white">Add New Teacher</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
        <input type="hidden" name="add_teacher" value="1">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-sm-6">
              <label class="fw-600 small form-label">Full Name *</label>
              <input type="text" name="name" class="form-control" required placeholder="John Doe">
            </div>
            <div class="col-sm-6">
              <label class="fw-600 small form-label">Email Address *</label>
              <input type="email" name="email" class="form-control" required placeholder="john@example.com">
            </div>
            <div class="col-sm-6">
              <label class="fw-600 small form-label">Password *</label>
              <input type="password" name="password" class="form-control" required>
              <div class="form-text small">Teacher will use this to login.</div>
            </div>
            <div class="col-sm-6">
              <label class="fw-600 small form-label">Subjects</label>
              <input type="text" name="subjects" class="form-control" placeholder="e.g. Maths, English, 11+">
            </div>
            <div class="col-12">
              <label class="fw-600 small form-label">Qualification</label>
              <input type="text" name="qualification" class="form-control" placeholder="e.g. PGCE, BA (Hons) English">
            </div>
            <div class="col-sm-6">
              <label class="fw-600 small form-label">DBS Number</label>
              <input type="text" name="dbs_number" class="form-control">
            </div>
            <div class="col-sm-6">
              <label class="fw-600 small form-label">DBS Expiry Date</label>
              <input type="date" name="dbs_expiry" class="form-control">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-dark">Create Teacher Account</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
