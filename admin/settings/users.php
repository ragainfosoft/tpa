<?php
// =====================================================
// TPA IMS — User Management (Admin / Staff)
// =====================================================
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
startSecureSession();
requireRole(['admin']); // Only admins can manage users — prevents staff escalation

$db = getDB();

// Handle Add / Edit User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_user'])) {
    verifyCsrf();
    $id       = (int)($_POST['user_id'] ?? 0);
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $role     = trim($_POST['role'] ?? 'staff');
    $branchId = (int)($_POST['branch_id'] ?? 0);
    $phone    = trim($_POST['phone'] ?? '');
    $pass     = $_POST['password'] ?? '';
    
    $branchVal = $branchId > 0 ? $branchId : null;

    if ($id) {
        // Update user
        if ($pass) {
            $db->prepare("UPDATE users SET name=?, email=?, role=?, branch_id=?, phone=?, password_hash=? WHERE id=?")
               ->execute([$name, $email, $role, $branchVal, $phone, password_hash($pass, PASSWORD_DEFAULT), $id]);
        } else {
            $db->prepare("UPDATE users SET name=?, email=?, role=?, branch_id=?, phone=? WHERE id=?")
               ->execute([$name, $email, $role, $branchVal, $phone, $id]);
        }
        if ($role === 'teacher') {
            $db->prepare("INSERT IGNORE INTO teachers (user_id, is_active) VALUES (?, 1)")->execute([$id]);
        }
        setFlash('success', 'User updated successfully.');
    } else {
        // Insert user
        if (!$pass) $pass = getSetting('staff_default_password', 'Staff@' . date('Y'));
        try {
            $db->prepare("INSERT INTO users (name, email, password_hash, role, branch_id, phone, is_active) VALUES (?, ?, ?, ?, ?, ?, 1)")
               ->execute([$name, $email, password_hash($pass, PASSWORD_DEFAULT), $role, $branchVal, $phone]);
            $newId = $db->lastInsertId();
            if ($role === 'teacher') {
                $db->prepare("INSERT INTO teachers (user_id, is_active) VALUES (?, 1)")->execute([$newId]);
            }
            setFlash('success', "User created successfully. Share the default password securely with them.");
        } catch (PDOException $e) {
            setFlash('danger', 'Error creating user. Email might already exist.');
        }
    }
    header('Location: users.php'); exit;
}

// Handle Status Toggle
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $db->prepare("UPDATE users SET is_active = NOT is_active WHERE id=?")->execute([$id]);
    setFlash('success', 'User status updated.');
    header('Location: users.php'); exit;
}

// Fetch all users
$stmt = $db->query("SELECT u.*, b.name as branch_name 
                    FROM users u 
                    LEFT JOIN branches b ON u.branch_id = b.id 
                    ORDER BY FIELD(u.role, 'admin', 'branch_manager', 'teacher', 'staff', 'parent', 'student'), u.name");
$users = $stmt->fetchAll();

// Fetch branches for dropdown
$branches = $db->query("SELECT id, name FROM branches WHERE is_active=1 ORDER BY name")->fetchAll();

$page_title   = 'User Management';
$page_section = 'settings';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
  <div>
    <h1><i class="bi bi-people me-2" style="color:var(--gold);"></i>User Management</h1>
    <p class="text-muted mb-0">Manage platform access, roles, and branch assignments.</p>
  </div>
  <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#userModal" onclick="resetForm()"><i class="bi bi-plus-lg me-1"></i>New User</button>
</div>

<div class="tpa-table table-responsive">
  <table class="table table-hover mb-0 dt-table">
    <thead class="bg-light">
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Branch</th>
        <th>Phone</th>
        <th>Status</th>
        <th class="text-end">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $u): ?>
      <tr>
        <td class="fw-600"><?= h($u['name']) ?></td>
        <td><?= h($u['email']) ?></td>
        <td>
          <span class="badge border bg-light text-dark text-uppercase"><?= str_replace('_', ' ', h($u['role'])) ?></span>
        </td>
        <td><?= h($u['branch_name'] ?? '—') ?></td>
        <td class="small"><?= h($u['phone'] ?? '—') ?></td>
        <td>
          <?php if($u['is_active']): ?>
            <span class="badge bg-success">Active</span>
          <?php else: ?>
            <span class="badge bg-danger">Inactive</span>
          <?php endif; ?>
        </td>
        <td class="text-end">
          <button class="btn btn-sm btn-outline-primary py-0 px-2" 
                  onclick="editUser(<?= $u['id'] ?>, '<?= h(addslashes($u['name'])) ?>', '<?= h(addslashes($u['email'])) ?>', '<?= h($u['role']) ?>', <?= $u['branch_id'] ?: 0 ?>, '<?= h(addslashes($u['phone']??'')) ?>')">
            <i class="bi bi-pencil-square"></i>
          </button>
          <?php if ($u['id'] !== currentUserId()): ?>
            <a href="users.php?toggle=<?= $u['id'] ?>" class="btn btn-sm <?= $u['is_active'] ? 'btn-outline-danger' : 'btn-outline-success' ?> py-0 px-2" onclick="return confirm('Toggle active status for this user?');">
              <i class="bi bi-power"></i>
            </a>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- Add/Edit User Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title fw-700" id="modalTitle">New User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
        <input type="hidden" name="save_user" value="1">
        <input type="hidden" name="user_id" id="edit_user_id" value="0">
        
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label small fw-600">Full Name *</label>
            <input type="text" name="name" id="edit_name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-600">Email Address *</label>
            <input type="email" name="email" id="edit_email" class="form-control" required>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-sm-6">
              <label class="form-label small fw-600">Role *</label>
              <select name="role" id="edit_role" class="form-select" required>
                <option value="admin">Admin</option>
                <option value="branch_manager">Branch Manager</option>
                <option value="teacher">Teacher</option>
                <option value="staff">Staff</option>
                <option value="student">Student</option>
                <option value="parent">Parent</option>
              </select>
            </div>
            <div class="col-sm-6">
              <label class="form-label small fw-600">Branch (Optional)</label>
              <select name="branch_id" id="edit_branch" class="form-select">
                <option value="0">Global (No specific branch)</option>
                <?php foreach ($branches as $b): ?>
                  <option value="<?= $b['id'] ?>"><?= h($b['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-600">Phone</label>
            <input type="text" name="phone" id="edit_phone" class="form-control">
          </div>
          <div class="mb-3 border-top pt-3">
            <label class="form-label small fw-600">Password</label>
            <input type="password" name="password" id="edit_password" class="form-control" placeholder="Leave blank to keep current password">
            <div class="form-text" id="passHelp">If creating a new user, a strong default password will be generated if left blank.</div>
          </div>
        </div>
        
        <div class="modal-footer bg-light">
          <button type="submit" class="btn btn-dark w-100 fw-700">Save User</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function resetForm() {
    document.getElementById('modalTitle').innerText = 'New User';
    document.getElementById('edit_user_id').value = '0';
    document.getElementById('edit_name').value = '';
    document.getElementById('edit_email').value = '';
    document.getElementById('edit_role').value = 'staff';
    document.getElementById('edit_branch').value = '0';
    document.getElementById('edit_phone').value = '';
    document.getElementById('edit_password').placeholder = 'Enter new password...';
    document.getElementById('passHelp').innerText = 'Leave blank to use the default password configured in Settings.';
}

function editUser(id, name, email, role, branchId, phone) {
    document.getElementById('modalTitle').innerText = 'Edit User';
    document.getElementById('edit_user_id').value = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_role').value = role;
    document.getElementById('edit_branch').value = branchId;
    document.getElementById('edit_phone').value = phone;
    document.getElementById('edit_password').placeholder = 'Leave blank to keep unchanged';
    document.getElementById('passHelp').innerText = 'Only enter a password if you wish to reset it for this user.';
    
    var myModal = new bootstrap.Modal(document.getElementById('userModal'));
    myModal.show();
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
