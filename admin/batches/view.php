<?php
// =====================================================
// TPA IMS — Manage Batch
// =====================================================
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
startSecureSession();
requireRole(['admin', 'branch_manager', 'staff']);

$db = getDB();
$batchId = (int)($_GET['id'] ?? 0);
if (!$batchId) { header('Location: index.php'); exit; }

// --- Handle Add Student ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_student'])) {
    verifyCsrf();
    $studentId = (int)$_POST['student_id'];
    if ($studentId) {
        $db->prepare("INSERT INTO batch_students (batch_id, student_id, joined_date, is_active) VALUES (?, ?, CURDATE(), 1) ON DUPLICATE KEY UPDATE is_active = 1")
           ->execute([$batchId, $studentId]);
        setFlash('success', 'Student added to batch.');
    }
    header("Location: view.php?id=$batchId"); exit;
}

// --- Handle Edit Batch ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_batch'])) {
    verifyCsrf();
    $db->prepare('UPDATE batches SET name=?, course_type=?, year_group=?, teacher_id=?, centre=?, day_of_week=?, start_time=?, end_time=?, max_capacity=?, term=? WHERE id=?')
       ->execute([trim($_POST['name']), $_POST['course_type'], $_POST['year_group'], $_POST['teacher_id'] ?: null, $_POST['centre'], $_POST['day_of_week'], $_POST['start_time'], $_POST['end_time'], (int)$_POST['max_capacity'] ?: 10, trim($_POST['term']), $batchId]);
    setFlash('success', 'Batch details updated successfully.');
    header("Location: view.php?id=$batchId"); exit;
}

// --- Handle Remove Student (POST only — prevents CSRF via link) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_student'])) {
    verifyCsrf();
    $studentId = (int)$_POST['remove_student'];
    $db->prepare("UPDATE batch_students SET is_active = 0 WHERE batch_id = ? AND student_id = ?")->execute([$batchId, $studentId]);
    setFlash('success', 'Student removed from batch.');
    header("Location: view.php?id=$batchId"); exit;
}

// Fetch Batch Details
$bStmt = $db->prepare("SELECT b.*, u.name as teacher_name, br.name as branch_name
                       FROM batches b 
                       LEFT JOIN teachers t ON b.teacher_id=t.id LEFT JOIN users u ON t.user_id=u.id 
                       LEFT JOIN branches br ON br.id=b.branch_id
                       WHERE b.id=?");
$bStmt->execute([$batchId]);
$batch = $bStmt->fetch();
if (!$batch) { setFlash('danger', 'Batch not found.'); header('Location: index.php'); exit; }

// Fetch Enrolled Students
$enrolled = $db->prepare("SELECT s.id, s.first_name, s.last_name, s.student_ref, bs.joined_date, s.year_group
                          FROM batch_students bs
                          JOIN students s ON bs.student_id = s.id
                          WHERE bs.batch_id = ? AND bs.is_active = 1
                          ORDER BY s.first_name, s.last_name");
$enrolled->execute([$batchId]);
$students = $enrolled->fetchAll();

// Fetch Unenrolled (available) Students for Dropdown
$available = $db->prepare("SELECT id, first_name, last_name, student_ref 
                           FROM students 
                           WHERE status='active' AND id NOT IN (SELECT student_id FROM batch_students WHERE batch_id=? AND is_active=1) 
                           ORDER BY first_name, last_name");
$available->execute([$batchId]);
$availableStudents = $available->fetchAll();

// Fetch Teachers for Edit Dropdown
$teachers = $db->query("SELECT t.id, u.name FROM teachers t JOIN users u ON u.id = t.user_id WHERE u.role='teacher' AND t.is_active=1 ORDER BY u.name")->fetchAll();

$page_title   = 'Manage Batch: ' . h($batch['name']);
$page_section = 'batches';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
  <div>
    <h1><i class="bi bi-collection me-2" style="color:var(--gold);"></i><?= h($batch['name']) ?></h1>
    <p class="text-muted mb-0">Manage enrolled students and view batch details</p>
  </div>
  <div>
    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editBatchModal"><i class="bi bi-pencil-square me-1"></i>Edit Batch</button>
    <a href="index.php" class="btn btn-sm btn-outline-secondary ms-2">← Back to Batches</a>
  </div>
</div>

<div class="row g-4 mb-4">
  <!-- Batch Meta Cards -->
  <div class="col-md-3">
    <div class="stat-card">
      <div class="stat-label text-muted">Teacher</div>
      <div class="fw-700 fs-5"><?= h($batch['teacher_name'] ?? 'Unassigned') ?></div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card">
      <div class="stat-label text-muted">Schedule</div>
      <div class="fw-700 fs-5"><?= h($batch['day_of_week']) ?></div>
      <div class="small text-muted"><?= date('g:ia', strtotime($batch['start_time'])) ?> - <?= date('g:ia', strtotime($batch['end_time'])) ?></div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card">
      <div class="stat-label text-muted">Course Type</div>
      <div class="fw-700 fs-5"><?= h(strtoupper($batch['course_type'])) ?></div>
      <div class="small text-muted"><?= h($batch['term'] ?? '—') ?></div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card">
      <div class="stat-label text-muted">Enrolled / Capacity</div>
      <div class="fw-700 fs-5 <?= count($students) >= $batch['max_capacity'] ? 'text-danger' : 'text-success' ?>"><?= count($students) ?> / <?= $batch['max_capacity'] ?></div>
    </div>
  </div>
</div>

<div class="row g-4">
  <div class="col-lg-8">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="fw-700 mb-0">Enrolled Students</h5>
    </div>
    
    <div class="tpa-table table-responsive">
      <table class="table table-hover mb-0">
        <thead class="bg-light">
          <tr>
            <th>Student</th>
            <th>Ref #</th>
            <th>Year Group</th>
            <th>Enrolled Date</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($students as $s): ?>
            <tr>
              <td class="fw-600"><a href="<?= SITE_URL ?>/students/view.php?id=<?= $s['id'] ?>"><?= h($s['first_name'] . ' ' . $s['last_name']) ?></a></td>
              <td class="small text-muted"><?= h($s['student_ref']) ?></td>
              <td><?= h($s['year_group'] ?? '—') ?></td>
              <td class="small"><?= date('d M Y', strtotime($s['joined_date'] ?? 'now')) ?></td>
              <td class="text-end">
                <form method="POST" class="d-inline" onsubmit="return confirm('Remove this student from the batch?')">
                  <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
                  <input type="hidden" name="remove_student" value="<?= $s['id'] ?>">
                  <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2"><i class="bi bi-person-x"></i></button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($students)): ?>
            <tr><td colspan="5" class="py-4 text-center text-muted">No students currently enrolled in this batch.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  
  <div class="col-lg-4">
    <div class="stat-card" style="background:var(--navy); color:#fff;">
      <h6 class="fw-700 mb-3" style="color:var(--gold);">Quick Add Student</h6>
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
        <input type="hidden" name="add_student" value="1">
        <div class="mb-3">
          <label class="form-label small text-white-50">Select Student</label>
          <select name="student_id" class="form-select" required>
            <option value="">-- Choose student --</option>
            <?php foreach ($availableStudents as $as): ?>
              <option value="<?= $as['id'] ?>"><?= h($as['first_name'] . ' ' . $as['last_name']) ?> (<?= h($as['student_ref']) ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
        <button type="submit" class="btn fw-700 w-100" style="background:var(--gold); color:var(--navy);" <?= count($students) >= $batch['max_capacity'] ? 'disabled title="Batch is full"' : '' ?>>
          <i class="bi bi-person-plus me-1"></i> Enroll Student
        </button>
      </form>
      <?php if (count($students) >= $batch['max_capacity']): ?>
        <p class="small text-danger mt-2 mb-0"><i class="bi bi-exclamation-triangle me-1"></i> This batch has reached maximum capacity.</p>
      <?php endif; ?>
    </div>
    
    <div class="stat-card mt-3">
        <h6 class="fw-700 mb-2">Actions</h6>
        <div class="d-grid gap-2">
            <a href="<?= SITE_URL ?>/attendance/index.php?batch_id=<?= $batchId ?>" class="btn btn-outline-success btn-sm"><i class="bi bi-calendar-check me-1"></i> Mark Attendance</a>
            <a href="<?= SITE_URL ?>/homework/index.php?batch_id=<?= $batchId ?>" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil-square me-1"></i> Assignment / Homework</a>
            <button class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#editBatchModal"><i class="bi bi-pencil-square me-1"></i>Edit Batch & Teacher</button>
        </div>
    </div>
  </div>
</div>

<!-- Edit Batch Modal -->
<div class="modal fade" id="editBatchModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-light"><h5 class="modal-title fw-700">Edit Batch</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
        <input type="hidden" name="edit_batch" value="1">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-sm-6"><label class="form-label small fw-600">Batch Name *</label><input type="text" name="name" class="form-control" value="<?= h($batch['name']) ?>" required></div>
            <div class="col-sm-6"><label class="form-label small fw-600">Course Type</label>
              <select name="course_type" class="form-select">
                <?php foreach (['11plus','sats','ks1','ks2','ks3','gcse','easter_camp','summer_camp','other'] as $c): ?><option value="<?= $c ?>" <?= $batch['course_type']===$c?'selected':'' ?>><?= strtoupper($c) ?></option><?php endforeach; ?>
              </select></div>
            <div class="col-sm-4"><label class="form-label small fw-600">Year Group</label><input type="text" name="year_group" class="form-control" value="<?= h($batch['year_group']) ?>"></div>
            <div class="col-sm-4"><label class="form-label small fw-600">Centre</label>
              <select name="centre" class="form-select"><?php foreach(['Romford','Chelmsford','Online','Both'] as $c): ?><option <?= $batch['centre']===$c?'selected':'' ?>><?= $c ?></option><?php endforeach; ?></select></div>
            <div class="col-sm-4"><label class="form-label small fw-600">Max Capacity</label><input type="number" name="max_capacity" class="form-control" value="<?= h($batch['max_capacity']) ?>" min="1" max="50"></div>
            <div class="col-sm-4"><label class="form-label small fw-600">Day(s) of Week</label><input type="text" name="day_of_week" class="form-control" value="<?= h($batch['day_of_week']) ?>"></div>
            <div class="col-sm-4"><label class="form-label small fw-600">Start Time</label><input type="time" name="start_time" class="form-control" value="<?= h($batch['start_time']) ?>"></div>
            <div class="col-sm-4"><label class="form-label small fw-600">End Time</label><input type="time" name="end_time" class="form-control" value="<?= h($batch['end_time']) ?>"></div>
            <div class="col-sm-6"><label class="form-label small fw-600">Teacher</label>
              <select name="teacher_id" class="form-select"><option value="">— Unassigned —</option><?php foreach($teachers as $t): ?><option value="<?= $t['id'] ?>" <?= $batch['teacher_id']==$t['id']?'selected':'' ?>><?= h($t['name']) ?></option><?php endforeach; ?></select></div>
            <div class="col-sm-6"><label class="form-label small fw-600">Term</label><input type="text" name="term" class="form-control" value="<?= h($batch['term']) ?>"></div>
          </div>
        </div>
        <div class="modal-footer bg-light"><button class="btn btn-dark w-100 fw-700">Save Changes</button></div>
      </form>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
