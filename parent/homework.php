<?php
// =====================================================
// TPA — Parent Homework Viewer
// =====================================================
$page_title   = 'Homework';
$page_section = 'homework';
require_once __DIR__ . '/includes/header.php';

if (!$activeChild) { echo '<div class="alert alert-warning">No child linked.</div>'; require_once __DIR__ . '/includes/footer.php'; exit; }
$childId = $activeChild['id'];

$hwList = $db->prepare("SELECT h.*, b.name as batch_name, s.name as subject_name,
    hs.id as submission_id, hs.status as sub_status, hs.submitted_at, hs.teacher_feedback
    FROM homework h JOIN batch_students bs ON bs.batch_id=h.batch_id JOIN batches b ON b.id=h.batch_id LEFT JOIN subjects s ON s.id=h.subject_id
    LEFT JOIN homework_submissions hs ON hs.homework_id=h.id AND hs.student_id=?
    WHERE bs.student_id=? AND bs.is_active=1 ORDER BY h.due_date DESC");
$hwList->execute([$childId,$childId]); $hwList = $hwList->fetchAll();

$pending = array_filter($hwList, fn($h)=>empty($h['submission_id']));
$done    = array_filter($hwList, fn($h)=>!empty($h['submission_id']));
?>

<div class="page-header">
  <h1><i class="bi bi-pencil-square me-2 text-danger"></i>Homework Overview</h1>
  <span class="badge" style="background:#fce7f3;color:#9d174d;padding:8px 14px;"><?= count($pending) ?> Pending for <?= h($activeChild['name']) ?></span>
</div>

<?php if (!empty($pending)): ?>
<h6 class="text-uppercase fw-700 mb-3" style="font-size:.7rem;letter-spacing:.1em;color:#888;">⏳ Action Required</h6>
<div class="row g-3 mb-4">
  <?php foreach ($pending as $h):
    $isLate = strtotime($h['due_date']) < strtotime('today');
  ?>
  <div class="col-md-6 col-lg-4">
    <div class="stat-card h-100" style="border-left:4px solid <?= $isLate?'#dc2626':'#e2e8f0' ?>;">
      <div class="d-flex justify-content-between align-items-start mb-2">
        <span class="badge bg-light text-dark border" style="font-size:.67rem;"><?= h($h['subject_name']??$h['batch_name']) ?></span>
        <?php if ($isLate): ?><span class="badge bg-danger" style="font-size:.65rem;">Overdue</span><?php endif; ?>
      </div>
      <div class="fw-700 mb-1"><?= h($h['title']) ?></div>
      <div class="small fw-600 <?= $isLate?'text-danger':'text-muted' ?>">Due: <?= date('D d M Y', strtotime($h['due_date'])) ?></div>
      <?php if ($h['description']): ?><div class="small text-muted mt-2 p-2 rounded" style="background:#f8fafc;"><i class="bi bi-info-circle me-1"></i><?= h($h['description']) ?></div><?php endif; ?>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if (!empty($done)): ?>
<h6 class="text-uppercase fw-700 mb-3" style="font-size:.7rem;letter-spacing:.1em;color:#888;">✅ Completed</h6>
<div class="tpa-table">
  <table class="table table-hover mb-0">
    <thead><tr><th>Task</th><th>Subject</th><th>Due Date</th><th>Submitted On</th><th>Teacher Feedback</th></tr></thead>
    <tbody>
    <?php foreach ($done as $h): ?>
      <tr>
        <td><div class="fw-600"><?= h($h['title']) ?></div><div class="small text-muted"><?= h($h['batch_name']) ?></div></td>
        <td class="small text-muted"><?= h($h['subject_name']??'—') ?></td>
        <td class="small"><?= date('d M Y', strtotime($h['due_date'])) ?></td>
        <td class="small fw-600 text-success"><i class="bi bi-check me-1"></i><?= date('d M Y', strtotime($h['submitted_at'])) ?></td>
        <td class="small <?= $h['teacher_feedback']?'fw-600':'text-muted' ?>"><?= h($h['teacher_feedback']??'—') ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<?php if (empty($hwList)): ?>
  <div class="text-center py-5 text-muted" style="background:#fff;border-radius:16px;">
    <i class="bi bi-emoji-smile fs-1 d-block mb-3 text-success"></i>
    <h5><?= h($activeChild['name']) ?> has no homework.</h5>
  </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
