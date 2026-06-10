<?php
// =====================================================
// TPA — Student Quiz List
// =====================================================
$page_title   = 'My Quizzes';
$page_section = 'quizzes';
require_once __DIR__ . '/includes/header.php';

if (!$studentId) { echo '<div class="alert alert-warning">No student account linked.</div>'; require_once __DIR__ . '/includes/footer.php'; exit; }

// Assigned quizzes for this student
$assigned = $db->prepare("
    SELECT DISTINCT qs.id, qs.title, qs.subject_id, qs.year_group, qs.lesson, qs.time_limit_min, qs.attempt_limit, qs.pass_mark_pct, sub.name as subject_name, qa.due_date,
        (SELECT COUNT(*) FROM quiz_attempts att WHERE att.quiz_id=qs.id AND att.student_id=? AND att.status='submitted') as attempts_done,
        (SELECT MAX(att2.percentage) FROM quiz_attempts att2 WHERE att2.quiz_id=qs.id AND att2.student_id=? AND att2.status='submitted') as best_pct,
        (SELECT COUNT(*) FROM quiz_questions WHERE quiz_id=qs.id) as question_count
    FROM quiz_assignments qa JOIN quiz_sets qs ON qs.id=qa.quiz_id LEFT JOIN subjects sub ON sub.id=qs.subject_id
    JOIN batch_students bs ON bs.batch_id=qa.batch_id
    WHERE bs.student_id=? AND bs.is_active=1 AND qs.is_active=1
    ORDER BY qa.due_date IS NULL, qa.due_date ASC, qs.id DESC
");
$assigned->execute([$studentId,$studentId,$studentId]); $assigned = $assigned->fetchAll();

// Split into pending / completed
$pending   = array_filter($assigned, fn($q)=>$q['attempts_done']==0 || ($q['attempt_limit']==0));
$completed = array_filter($assigned, fn($q)=>$q['attempts_done']>0);
?>

<div class="page-header">
  <h1><i class="bi bi-question-circle me-2" style="color:var(--purple);"></i>My Quizzes</h1>
  <span class="badge" style="background:#ede9fe;color:#4c1d95;padding:8px 14px;"><?= count(array_filter($assigned, fn($q)=>$q['attempts_done']==0)) ?> Pending</span>
</div>

<!-- Pending quizzes -->
<?php if (!empty($pending)): ?>
<h6 class="text-uppercase fw-700 mb-3" style="font-size:.7rem;letter-spacing:.1em;color:#888;">📝 To Attempt</h6>
<div class="row g-3 mb-4">
  <?php foreach ($pending as $q):
    $isLocked = ($q['attempt_limit'] > 0 && $q['attempts_done'] >= $q['attempt_limit']);
    $dueInfo   = $q['due_date'] ? 'Due '.date('D d M',strtotime($q['due_date'])) : 'No deadline';
    $isOverdue = $q['due_date'] && strtotime($q['due_date']) < strtotime('today');
  ?>
    <div class="col-md-6 col-lg-4">
      <div class="stat-card h-100 d-flex flex-column" style="border-left:4px solid <?= $isOverdue?'#dc2626':'var(--purple)' ?>;">
        <div class="flex-grow-1">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <span class="badge" style="background:#ede9fe;color:#4c1d95;font-size:.67rem;"><?= h($q['subject_name']??'General') ?></span>
            <?php if ($isOverdue): ?><span class="badge bg-danger" style="font-size:.65rem;">Overdue</span><?php endif; ?>
          </div>
          <div class="fw-700 mb-1"><?= h($q['title']) ?></div>
          <div class="small text-muted mb-3"><?= $q['year_group'] ? h($q['year_group']).' · ' : '' ?><?= h($q['lesson']??'') ?></div>
          <div class="d-flex gap-2 flex-wrap mb-3">
            <span class="badge bg-light text-dark border"><i class="bi bi-list-check me-1"></i><?= $q['question_count'] ?> Qs</span>
            <span class="badge bg-light text-dark border"><i class="bi bi-clock me-1"></i><?= $q['time_limit_min']>0?$q['time_limit_min'].'min':'Unlimited' ?></span>
            <span class="badge bg-light text-dark border"><i class="bi bi-check-circle me-1"></i>Pass: <?= $q['pass_mark_pct'] ?>%</span>
          </div>
          <div class="small fw-600 <?= $isOverdue?'text-danger':'text-muted' ?>"><?= $dueInfo ?></div>
        </div>
        <?php if (!$isLocked): ?>
          <a href="quiz-attempt.php?quiz_id=<?= $q['id'] ?>" class="btn mt-3 fw-700" style="background:var(--purple);color:#fff;"><i class="bi bi-play-fill me-1"></i>Start Quiz</a>
        <?php else: ?>
          <button class="btn mt-3 btn-outline-secondary" disabled>No Attempts Left</button>
        <?php endif; ?>
      </div>
    </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Completed quizzes -->
<?php if (!empty($completed)): ?>
<h6 class="text-uppercase fw-700 mb-3" style="font-size:.7rem;letter-spacing:.1em;color:#888;">✅ Completed</h6>
<div class="tpa-table">
  <table class="table table-hover mb-0">
    <thead><tr><th>Quiz</th><th>Subject</th><th>Attempts</th><th>Best Score</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($completed as $q):
      $pct     = round((float)$q['best_pct']);
      $passed  = $pct >= $q['pass_mark_pct'];
    ?>
      <tr>
        <td><div class="fw-600"><?= h($q['title']) ?></div><div class="small text-muted"><?= h($q['lesson']??'') ?></div></td>
        <td class="small text-muted"><?= h($q['subject_name']??'—') ?></td>
        <td><span class="badge bg-light text-dark border"><?= $q['attempts_done'] ?>/<?= $q['attempt_limit']>0?$q['attempt_limit']:'∞' ?></span></td>
        <td><span class="fw-700" style="color:<?= $pct>=70?'#16a34a':($pct>=50?'#ca8a04':'#dc2626') ?>;"><?= $pct ?>%</span></td>
        <td><?= $passed ? '<span class="badge bg-success">Passed</span>' : '<span class="badge bg-danger">Failed</span>' ?></td>
        <td>
          <?php if ($q['attempt_limit']==0 || $q['attempts_done']<$q['attempt_limit']): ?>
            <a href="quiz-attempt.php?quiz_id=<?= $q['id'] ?>" class="btn btn-sm btn-outline-secondary py-0">Retry</a>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<?php if (empty($assigned)): ?>
  <div class="text-center py-5 text-muted" style="background:#fff;border-radius:16px;">
    <i class="bi bi-question-circle fs-1 d-block mb-3"></i>
    <h5>No quizzes assigned yet.</h5>
    <p class="small">Your teacher will assign quizzes for your batch.</p>
  </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
