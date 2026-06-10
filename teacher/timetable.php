<?php
// TPA — Teacher Timetable
require_once __DIR__ . '/includes/header.php';

$batches = $db->prepare("SELECT b.*, s.name as sub_name FROM batches b LEFT JOIN subjects s ON s.id=b.subject_id WHERE b.teacher_id=? AND b.is_active=1 ORDER BY b.start_time");
$batches->execute([$teacherId]); $batches = $batches->fetchAll();

$schedule = [];
foreach (['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $d) {
    $schedule[$d] = array_filter($batches, fn($b) => stripos($b['day_of_week'], $d) !== false);
}
?>
<div class="page-header"><h1><i class="bi bi-calendar3 me-2 text-success"></i>My Timetable</h1></div>
<div class="row g-4">
  <?php foreach ($schedule as $day => $dayBatches): if(empty($dayBatches)) continue; ?>
    <div class="col-md-6 col-lg-4">
      <div class="stat-card h-100 border-top border-4 border-success">
        <h5 class="fw-900 mb-3 text-uppercase" style="letter-spacing:.05em;"><?= $day ?></h5>
        <?php foreach ($dayBatches as $b): ?>
          <div class="mb-3 pb-3 border-bottom">
            <div class="fw-700 text-dark"><?= h($b['name']) ?></div>
            <div class="small text-muted mb-2"><?= h($b['sub_name']??$b['course_type']) ?> · <?= h($b['centre']) ?></div>
            <div class="d-flex align-items-center gap-2">
              <span class="badge bg-light text-dark"><i class="bi bi-clock me-1"></i><?= date('g:ia',strtotime($b['start_time'])) ?>–<?= date('g:ia',strtotime($b['end_time'])) ?></span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endforeach; ?>
  <?php if(empty($batches)): ?><div class="col-12"><div class="stat-card text-center py-5 text-muted">No scheduled batches found.</div></div><?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
