<?php
// =====================================================
// TPA — Quiz Result Page (Student)
// =====================================================
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
startSecureSession();
requireRole(['student','admin']);

$db        = getDB();
$attemptId = (int)($_GET['attempt_id'] ?? 0);
$studentId = (int)(currentUser()['student_id'] ?? 0);
if (!$attemptId) { header('Location: quizzes.php'); exit; }

// Load attempt
$att = $db->prepare('SELECT qa.*, qs.title, qs.pass_mark_pct, qs.result_mode, qs.negative_marking, sub.name as subject_name, qs.lesson, qs.year_group FROM quiz_attempts qa JOIN quiz_sets qs ON qs.id=qa.quiz_id LEFT JOIN subjects sub ON sub.id=qs.subject_id WHERE qa.id=?');
$att->execute([$attemptId]); $attempt = $att->fetch();
if (!$attempt || ($attempt['student_id'] != $studentId && !isAdmin())) { header('Location: quizzes.php'); exit; }
if ($attempt['status'] !== 'submitted') { header('Location: quiz-attempt.php?quiz_id='.$attempt['quiz_id']); exit; }

// If delayed mode and not admin, hide answers
$showAnswers = ($attempt['result_mode'] === 'instant') || isAdmin();

// Load answers with question data
$answers = $db->prepare('SELECT qa.*, qq.question, qq.option_a, qq.option_b, qq.option_c, qq.option_d, qq.correct, qq.explanation, qq.marks FROM quiz_answers qa JOIN quiz_questions qq ON qq.id=qa.question_id WHERE qa.attempt_id=? ORDER BY qq.sort_order, qq.id');
$answers->execute([$attemptId]); $answers = $answers->fetchAll();

// Leaderboard for this quiz
$leaderboard = $db->prepare('SELECT u.name as student_name, qa.score, qa.max_score, qa.percentage, qa.passed, qa.submitted_at, s.student_ref FROM quiz_attempts qa JOIN students s ON s.id=qa.student_id JOIN users u ON u.student_id=s.id WHERE qa.quiz_id=? AND qa.status="submitted" ORDER BY qa.percentage DESC, qa.time_taken_sec ASC LIMIT 15');
try { $leaderboard->execute([$attempt['quiz_id']]); $leaderboard = $leaderboard->fetchAll(); } catch(Exception $e) { $leaderboard = []; }

$pct    = (float)$attempt['percentage'];
$passed = (bool)$attempt['passed'];
$timeSec = (int)$attempt['time_taken_sec'];
$timeStr = gmdate($timeSec >= 3600 ? 'H:i:s' : 'i:s', $timeSec);

$page_title   = 'Quiz Result';
$page_section = 'quizzes';
require_once __DIR__ . '/../includes/header.php';
?>

<style>
.result-hero { border-radius:20px; padding:36px; text-align:center; color:#fff; margin-bottom:28px; }
.score-ring { width:140px; height:140px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; flex-direction:column; margin-bottom:16px; background:rgba(255,255,255,.12); border:4px solid rgba(255,255,255,.3); }
.score-pct { font-size:2.4rem; font-weight:900; line-height:1; }
.ans-card { background:#fff; border:2px solid #e2e8f0; border-radius:12px; padding:20px 24px; margin-bottom:14px; }
.ans-card.correct-ans { border-color:#16a34a; background:#f0fdf4; }
.ans-card.wrong-ans   { border-color:#dc2626; background:#fef2f2; }
.opt-row { padding:8px 12px; border-radius:8px; margin-bottom:4px; font-size:.875rem; }
.opt-row.correct { background:#dcfce7; font-weight:700; }
.opt-row.chosen   { background:#fee2e2; }
.opt-row.correct.chosen { background:#dcfce7; }
.lb-me { background:#ede9fe; font-weight:700; }
</style>

<!-- Result hero card -->
<div class="result-hero" style="background:linear-gradient(135deg,<?= $passed?'#065f46':'#7f1d1d' ?> 0%,<?= $passed?'#059669':'#dc2626' ?> 100%);">
  <div class="score-ring">
    <div class="score-pct"><?= round($pct) ?>%</div>
    <div style="font-size:.65rem;color:rgba(255,255,255,.7);text-transform:uppercase;letter-spacing:.08em;margin-top:4px;">Score</div>
  </div>
  <h2 class="fw-900 mb-1"><?= $passed ? '🎉 Well done! You Passed!' : '😔 Keep Going! You can do it!' ?></h2>
  <p style="color:rgba(255,255,255,.75);"><?= h($attempt['title']) ?><?= $attempt['subject_name'] ? ' · '.h($attempt['subject_name']) : '' ?></p>
  <div class="d-flex gap-3 justify-content-center flex-wrap mt-3">
    <div><div class="fw-900 fs-5"><?= number_format($attempt['score'],1) ?>/<?= number_format($attempt['max_score'],1) ?></div><div style="font-size:.7rem;color:rgba(255,255,255,.6);">MARKS</div></div>
    <div style="background:rgba(255,255,255,.15);width:1px;"></div>
    <div><div class="fw-900 fs-5"><?= $timeStr ?></div><div style="font-size:.7rem;color:rgba(255,255,255,.6);">TIME TAKEN</div></div>
    <div style="background:rgba(255,255,255,.15);width:1px;"></div>
    <div><div class="fw-900 fs-5"><?= count(array_filter($answers, fn($a)=>$a['is_correct'])) ?>/<?= count($answers) ?></div><div style="font-size:.7rem;color:rgba(255,255,255,.6);">CORRECT</div></div>
  </div>
  <div class="mt-3">
    <a href="quizzes.php" class="btn btn-sm" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);">← Back to Quizzes</a>
    <?php if ($attempt['attempt_limit'] > 1 || $attempt['attempt_limit'] == 0): ?><a href="quiz-attempt.php?quiz_id=<?= $attempt['quiz_id'] ?>" class="btn btn-sm ms-2" style="background:rgba(255,255,255,.9);color:var(--navy);">Retry Quiz</a><?php endif; ?>
  </div>
</div>

<div class="row g-4">
  <!-- Answer review -->
  <div class="col-lg-8">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <div class="fw-700" style="color:var(--navy);">Answer Review</div>
      <?php if (!$showAnswers): ?><span class="badge bg-warning text-dark">Results Pending (Delayed Mode)</span><?php endif; ?>
    </div>

    <?php if ($showAnswers): ?>
      <?php foreach ($answers as $i => $a): ?>
        <div class="ans-card <?= $a['is_correct'] ? 'correct-ans' : 'wrong-ans' ?>">
          <div class="d-flex gap-3">
            <div style="width:28px;height:28px;border-radius:50%;<?= $a['is_correct']?'background:#16a34a':'background:#dc2626' ?>;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:.75rem;flex-shrink:0;"><?= $a['is_correct'] ? '✓' : '✗' ?></div>
            <div class="flex-grow-1">
              <div class="fw-600 mb-3">Q<?= $i+1 ?>. <?= h($a['question']) ?></div>
              <?php foreach (['a','b','c','d'] as $k):
                $isCorrect = $k === $a['correct'];
                $isChosen  = $k === $a['chosen'];
                $cls = '';
                if ($isCorrect && $isChosen) $cls = 'correct chosen';
                elseif ($isCorrect) $cls = 'correct';
                elseif ($isChosen) $cls = 'chosen';
              ?>
                <div class="opt-row <?= $cls ?>">
                  <?= $isChosen ? '▶ ' : '' ?><?= strtoupper($k) ?>. <?= h($a['option_'.$k]) ?>
                  <?= $isCorrect ? ' <span class="text-success fw-700">✓ Correct</span>' : '' ?>
                </div>
              <?php endforeach; ?>
              <?php if (!$a['chosen']): ?><div class="small text-muted mt-2"><i class="bi bi-dash-circle me-1"></i>Not answered</div><?php endif; ?>
              <?php if ($a['explanation']): ?><div class="mt-3 p-2 rounded" style="background:rgba(0,0,0,.04);font-size:.82rem;"><i class="bi bi-lightbulb me-1 text-warning"></i><strong>Explanation:</strong> <?= h($a['explanation']) ?></div><?php endif; ?>
              <?php if ($a['marks_earned'] != 0): ?>
                <div class="mt-2 small fw-700 <?= $a['marks_earned']>0?'text-success':'text-danger' ?>"><?= $a['marks_earned'] > 0 ? '+' : '' ?><?= $a['marks_earned'] ?> marks</div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="alert alert-info">Results will be revealed by your teacher.</div>
    <?php endif; ?>
  </div>

  <!-- Leaderboard -->
  <div class="col-lg-4">
    <div class="stat-card" style="position:sticky;top:80px;">
      <div class="fw-700 mb-3" style="color:var(--navy);">🏆 Leaderboard</div>
      <?php if (empty($leaderboard)): ?>
        <p class="text-muted small">No other attempts yet.</p>
      <?php else: ?>
        <?php foreach ($leaderboard as $rank => $row):
          $isMe = isset($studentId) && false; // simplified — could check student_id
        ?>
          <div class="d-flex align-items-center gap-2 py-2 border-bottom <?= $isMe?'lb-me':'' ?>">
            <div class="fw-900" style="width:20px;color:<?= $rank===0?'#ca8a04':($rank===1?'#64748b':($rank===2?'#b45309':'#cbd5e1')) ?>;font-size:.85rem;"><?= $rank<3 ? ['🥇','🥈','🥉'][$rank] : ($rank+1) ?></div>
            <div class="flex-grow-1">
              <div class="fw-600 small"><?= h($row['student_name'] ?? $row['student_ref'] ?? 'Student') ?></div>
              <div class="text-muted" style="font-size:.7rem;"><?= round($row['percentage']) ?>% · <?= number_format($row['score'],1) ?>/<?= number_format($row['max_score'],1) ?> marks</div>
            </div>
            <span class="badge <?= $row['passed']?'bg-success':'bg-danger' ?>" style="font-size:.65rem;"><?= $row['passed']?'Pass':'Fail' ?></span>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
