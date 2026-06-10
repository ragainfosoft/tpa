<?php
// =====================================================
// TPA — Teacher Portal Batches
// =====================================================
require_once __DIR__ . '/../admin/includes/auth.php';
require_once __DIR__ . '/../admin/includes/functions.php';
startSecureSession();
requireRole(['teacher']);

$db = getDB();
$teacherRow = $db->prepare('SELECT t.id FROM teachers t WHERE t.user_id=?');
$teacherRow->execute([currentUserId()]);
$teacherId = (int)$teacherRow->fetchColumn();

// If Viewing a Specific Batch
if (isset($_GET['view'])) {
    $batchId = (int)$_GET['view'];
    $batch = $db->prepare("SELECT b.*, s.name as sub_name FROM batches b LEFT JOIN subjects s ON s.id=b.subject_id WHERE b.id=? AND b.teacher_id=?");
    $batch->execute([$batchId, $teacherId]); $b = $batch->fetch();
    if (!$b) { header('Location: batches.php'); exit; }
    
    $students = $db->prepare("SELECT s.* FROM students s JOIN batch_students bs ON bs.student_id=s.id WHERE bs.batch_id=? AND bs.is_active=1 ORDER BY s.first_name");
    $students->execute([$batchId]); $students = $students->fetchAll();
    
    $page_title = 'Batch: ' . h($b['name']);
    $page_section = 'dashboard';
    require_once __DIR__ . '/includes/header.php';
    ?>
    <div class="page-header">
      <div>
        <h1><i class="bi bi-collection me-2 text-primary"></i><?= h($b['name']) ?></h1>
        <p class="text-muted mb-0"><?= h($b['course_type']) ?> · <?= h($b['day_of_week']) ?> <?= date('g:ia',strtotime($b['start_time'])) ?> - <?= date('g:ia',strtotime($b['end_time'])) ?></p>
      </div>
      <a href="batches.php" class="btn btn-outline-secondary">← My Batches</a>
    </div>
    
    <div class="stat-card p-0">
      <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
        <h5 class="fw-700 m-0">Enrolled Students (<?= count($students) ?>)</h5>
      </div>
      <table class="table table-hover mb-0">
        <thead class="bg-light"><tr><th>Name</th><th>Year Group</th><th>Ref</th></tr></thead>
        <tbody>
          <?php foreach ($students as $s): ?>
          <tr>
            <td class="fw-600"><?= h($s['first_name'].' '.$s['last_name']) ?></td>
            <td><?= h($s['year_group'] ?? '—') ?></td>
            <td class="small text-muted"><?= h($s['student_ref']) ?></td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($students)): ?><tr><td colspan="3" class="text-center py-4 text-muted">No students assigned to this batch.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
    <?php
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// Default View: List All Batches
$batches = $db->prepare("SELECT b.*, s.name as sub_name, (SELECT COUNT(*) FROM batch_students bs WHERE bs.batch_id=b.id AND bs.is_active=1) as enrolled FROM batches b LEFT JOIN subjects s ON s.id=b.subject_id WHERE b.teacher_id=? AND b.is_active=1 ORDER BY b.name");
$batches->execute([$teacherId]); $batches = $batches->fetchAll();

$page_title = 'My Batches';
$page_section = 'dashboard';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <h1><i class="bi bi-collection me-2 text-primary"></i>My Assigned Batches</h1>
</div>

<div class="tpa-table">
  <table class="table table-hover mb-0">
    <thead><tr><th>Batch Name</th><th>Subject</th><th>Schedule</th><th>Students</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($batches as $b): ?>
      <tr>
        <td class="fw-700"><?= h($b['name']) ?></td>
        <td class="small"><?= h($b['sub_name'] ?? $b['course_type']) ?></td>
        <td class="small"><?= h($b['day_of_week']) ?> <br><span class="text-muted"><?= date('g:ia',strtotime($b['start_time'])) ?>–<?= date('g:ia',strtotime($b['end_time'])) ?></span></td>
        <td><span class="badge border text-dark"><?= $b['enrolled'] ?> / <?= $b['max_capacity'] ?></span></td>
        <td class="text-end"><a href="batches.php?view=<?= $b['id'] ?>" class="btn btn-sm btn-outline-primary">View Roster</a></td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($batches)): ?><tr><td colspan="5" class="text-center py-5 text-muted">You have no batches assigned yet.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
