<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
startSecureSession();
requireLogin(SITE_URL . '/login.php');

$db = getDB();

// Handle add batch — redirect BEFORE any HTML
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_batch'])) {
    verifyCsrf();
    $name = trim($_POST['name'] ?? '');
    if (!$name) { setFlash('danger','Batch name is required.'); header('Location: index.php'); exit; }
    $days = isset($_POST['days']) ? implode(', ', (array)$_POST['days']) : (trim($_POST['day_of_week'] ?? '') ?: 'Monday');
    $db->prepare('INSERT INTO batches (name,course_type,year_group,teacher_id,centre,day_of_week,start_time,end_time,max_capacity,term)
                  VALUES (?,?,?,?,?,?,?,?,?,?)')
       ->execute([$name,$_POST['course_type'],$_POST['year_group'],$_POST['teacher_id']?:null,$_POST['centre'],$days,$_POST['start_time'],$_POST['end_time'],(int)($_POST['max_capacity']??0) ?: 8, trim($_POST['term'])]);
    setFlash('success','Batch created.');
    header('Location: index.php'); exit;
}

// Toggle batch active/inactive
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['toggle_batch'])) {
    verifyCsrf();
    $bId = (int)$_POST['batch_id'];
    $db->prepare('UPDATE batches SET is_active = 1 - is_active WHERE id = ?')->execute([$bId]);
    setFlash('success','Batch status updated.');
    header('Location: index.php'); exit;
}

require_once __DIR__ . '/../includes/helpers.php';
$branches    = getBranchNames(true);
$courseTypes = getCourseTypes();

$page_title   = 'Batches & Groups';
$page_section = 'batches';
require_once __DIR__ . '/../includes/header.php';

$batches  = $db->query("SELECT b.*, u.name as teacher_name,
    (SELECT COUNT(*) FROM batch_students bs WHERE bs.batch_id=b.id AND bs.is_active=1) as enrolled
    FROM batches b LEFT JOIN teachers t ON b.teacher_id=t.id LEFT JOIN users u ON t.user_id=u.id
    ORDER BY b.is_active DESC, b.name")->fetchAll();

$teachers = $db->query("SELECT t.id, u.name FROM teachers t JOIN users u ON t.user_id=u.id WHERE t.is_active=1 ORDER BY u.name")->fetchAll();
?>
<div class="page-header">
  <h1><i class="bi bi-collection me-2" style="color:var(--gold);"></i>Batches &amp; Groups</h1>
  <button class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#addBatchModal"><i class="bi bi-plus-lg me-1"></i>New Batch</button>
</div>

<div class="tpa-table table-responsive">
  <table class="table table-hover mb-0 dt-table">
    <thead><tr><th>Name</th><th>Course</th><th>Year</th><th>Day / Time</th><th>Centre</th><th>Teacher</th><th>Students</th><th>Status</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($batches as $b): ?>
        <tr>
          <td class="fw-700"><?= h($b['name']) ?></td>
          <td><span class="badge bg-dark"><?= strtoupper(h($b['course_type'])) ?></span></td>
          <td><?= h($b['year_group'] ?? '—') ?></td>
          <td class="small"><?= h($b['day_of_week']) ?> <?= date('g:ia',strtotime($b['start_time']??'00:00')) ?> – <?= date('g:ia',strtotime($b['end_time']??'00:00')) ?></td>
          <td><?= h($b['centre'] ?? '—') ?></td>
          <td><?= h($b['teacher_name'] ?? '—') ?></td>
          <td><?= $b['enrolled'] ?>/<?= $b['max_capacity'] ?></td>
          <td><?= $b['is_active'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' ?></td>
          <td>
            <div class="d-flex gap-1">
              <a href="view.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:.75rem;">Manage</a>
              <form method="POST" class="d-inline">
                <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
                <input type="hidden" name="toggle_batch" value="1">
                <input type="hidden" name="batch_id" value="<?= $b['id'] ?>">
                <button type="submit" class="btn btn-sm py-0 px-2 <?= $b['is_active'] ? 'btn-outline-warning' : 'btn-outline-success' ?>" style="font-size:.75rem;" title="<?= $b['is_active'] ? 'Deactivate' : 'Activate' ?>">
                  <?= $b['is_active'] ? 'Deactivate' : 'Activate' ?>
                </button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- Add Batch Modal -->
<div class="modal fade" id="addBatchModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background:var(--navy);"><h5 class="modal-title text-white">New Batch</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
        <input type="hidden" name="add_batch" value="1">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-sm-6"><label class="fw-600 small form-label">Batch Name *</label><input type="text" name="name" class="form-control" required placeholder="e.g. Saturday 11+ Year 5"></div>
            <div class="col-sm-6"><label class="fw-600 small form-label">Course Type</label>
              <select name="course_type" class="form-select">
                <?php foreach ($courseTypes as $ct): ?><option value="<?= h($ct['code']) ?>"><?= h($ct['name']) ?></option><?php endforeach; ?>
              </select></div>
            <div class="col-sm-4"><label class="fw-600 small form-label">Year Group</label><input type="text" name="year_group" class="form-control" placeholder="Year 5 &amp; 6"></div>
            <div class="col-sm-4"><label class="fw-600 small form-label">Centre</label>
              <select name="centre" class="form-select"><?php foreach($branches as $c): ?><option value="<?= h($c) ?>"><?= h($c) ?></option><?php endforeach; ?></select></div>
            <div class="col-sm-4"><label class="fw-600 small form-label">Max Capacity</label><input type="number" name="max_capacity" class="form-control" value="10" min="1" max="30"></div>
            <div class="col-sm-12">
              <label class="fw-600 small form-label mb-2">Day(s) of Week *</label>
              <div class="d-flex flex-wrap gap-3">
                <?php foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day): ?>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="days[]" value="<?= $day ?>" id="day_<?= $day ?>" <?= $day==='Saturday'?'checked':'' ?>>
                    <label class="form-check-label small" for="day_<?= $day ?>"><?= $day ?></label>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
            <div class="col-sm-4"><label class="fw-600 small form-label">Start Time</label><input type="time" name="start_time" class="form-control" value="10:00"></div>
            <div class="col-sm-4"><label class="fw-600 small form-label">End Time</label><input type="time" name="end_time" class="form-control" value="12:00"></div>
            <div class="col-sm-4"><label class="fw-600 small form-label">Term</label><input type="text" name="term" class="form-control" placeholder="Spring 2026"></div>
            <div class="col-sm-12"><label class="fw-600 small form-label">Teacher</label>
              <select name="teacher_id" class="form-select"><option value="">— Select —</option><?php foreach($teachers as $t): ?><option value="<?= $t['id'] ?>"><?= h($t['name']) ?></option><?php endforeach; ?></select></div>
          </div>
        </div>
        <div class="modal-footer"><button class="btn btn-dark">Create Batch</button></div>
      </form>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
