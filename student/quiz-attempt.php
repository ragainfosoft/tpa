<?php
// =====================================================
// TPA — Student Quiz Attempt Engine
// =====================================================

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
startSecureSession();
requireRole(['student','admin']);

$db        = getDB();
$quizId    = (int)($_GET['quiz_id'] ?? 0);
$studentId = (int)(currentUser()['student_id'] ?? 0);

if (!$quizId || !$studentId) { header('Location: quizzes.php'); exit; }

// Load quiz
$quiz = $db->prepare('SELECT qs.*, sub.name as subject_name FROM quiz_sets qs LEFT JOIN subjects sub ON sub.id=qs.subject_id WHERE qs.id=? AND qs.is_active=1');
$quiz->execute([$quizId]); $quiz = $quiz->fetch();
if (!$quiz) { setFlash('danger','Quiz not found.'); header('Location: quizzes.php'); exit; }

// Check attempt limit
if ($quiz['attempt_limit'] > 0) {
    $attCount = $db->prepare('SELECT COUNT(*) FROM quiz_attempts WHERE quiz_id=? AND student_id=? AND status="submitted"');
    $attCount->execute([$quizId, $studentId]);
    if ((int)$attCount->fetchColumn() >= $quiz['attempt_limit']) {
        setFlash('warning','You have used all available attempts for this quiz.');
        header('Location: quizzes.php'); exit;
    }
}

// Find or start attempt
$attemptId = null;
$attempt   = null;
$inProgress = $db->prepare('SELECT * FROM quiz_attempts WHERE quiz_id=? AND student_id=? AND status="in_progress" ORDER BY id DESC LIMIT 1');
$inProgress->execute([$quizId, $studentId]);
$attempt = $inProgress->fetch();

if (!$attempt) {
    // Start new attempt
    $db->prepare('INSERT INTO quiz_attempts (quiz_id,student_id,started_at,status) VALUES (?,?,NOW(),"in_progress")')->execute([$quizId,$studentId]);
    $attemptId = (int)$db->lastInsertId();
    $attempt   = $db->prepare('SELECT * FROM quiz_attempts WHERE id=?');
    $attempt->execute([$attemptId]); $attempt = $attempt->fetch();
} else {
    $attemptId = $attempt['id'];
}

// Load questions (apply shuffle from quiz settings)
$questions = $db->prepare('SELECT * FROM quiz_questions WHERE quiz_id=? ORDER BY sort_order, id');
$questions->execute([$quizId]); $questions = $questions->fetchAll();
if ($quiz['shuffle_questions']) shuffle($questions);

// Load already-saved answers
$savedAnswers = [];
$sa = $db->prepare('SELECT question_id, chosen FROM quiz_answers WHERE attempt_id=?');
$sa->execute([$attemptId]); foreach ($sa->fetchAll() as $r) $savedAnswers[$r['question_id']] = $r['chosen'];

// ── Submit quiz ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_quiz'])) {
    verifyCsrf();
    $answers  = $_POST['answer'] ?? [];
    $timeTaken = (int)($_POST['time_taken'] ?? 0);
    $totalScore = 0; $maxScore = 0;

    // Load all questions fresh
    $allQ = $db->prepare('SELECT * FROM quiz_questions WHERE quiz_id=?');
    $allQ->execute([$quizId]); $allQ = $allQ->fetchAll();

    foreach ($allQ as $q) {
        $chosen    = $answers[$q['id']] ?? null;
        $isCorrect = ($chosen === $q['correct']) ? 1 : 0;
        $marks     = (float)$q['marks'];
        $maxScore  += $marks;
        $earned    = 0;
        if ($isCorrect) { $earned = $marks; $totalScore += $marks; }
        elseif ($chosen && $quiz['negative_marking'] > 0) { $earned = -(float)$quiz['negative_marking']; $totalScore -= (float)$quiz['negative_marking']; }

        $db->prepare('INSERT INTO quiz_answers (attempt_id,question_id,chosen,is_correct,marks_earned) VALUES (?,?,?,?,?) ON DUPLICATE KEY UPDATE chosen=VALUES(chosen),is_correct=VALUES(is_correct),marks_earned=VALUES(marks_earned)')
           ->execute([$attemptId,$q['id'],$chosen??null,$isCorrect,$earned]);
    }

    $pct    = $maxScore > 0 ? round($totalScore / $maxScore * 100, 2) : 0;
    $passed = $pct >= $quiz['pass_mark_pct'] ? 1 : 0;

    $db->prepare('UPDATE quiz_attempts SET submitted_at=NOW(),score=?,max_score=?,percentage=?,passed=?,time_taken_sec=?,status="submitted" WHERE id=?')
       ->execute([$totalScore,$maxScore,$pct,$passed,$timeTaken,$attemptId]);

    logActivity('quiz_submitted', "Student $studentId submitted quiz $quizId, score $totalScore/$maxScore ($pct%)");

    header('Location: quiz-result.php?attempt_id='.$attemptId);
    exit;
}

// Calculate time remaining
$elapsedSec = time() - strtotime($attempt['started_at']);
$totalTimeSec = (int)$quiz['time_limit_min'] * 60;
$remainingSec = $quiz['time_limit_min'] > 0 ? max(0, $totalTimeSec - $elapsedSec) : -1;
// If timed out, auto-submit would be done client-side by JS

$page_title   = h($quiz['title']);
$page_section = 'quizzes';
$extra_css    = '';
require_once __DIR__ . '/../includes/header.php';
?>

<style>
.quiz-container { max-width:760px; margin:0 auto; }
.quiz-header { background:linear-gradient(135deg,var(--purple) 0%,#4c1d95 100%); border-radius:16px; padding:24px 28px; color:#fff; margin-bottom:28px; }
.q-card { background:#fff; border:2px solid #e2e8f0; border-radius:14px; padding:24px; margin-bottom:20px; transition:.2s; }
.q-card:focus-within { border-color:var(--purple); box-shadow:0 0 0 3px rgba(124,58,237,.12); }
.opt-label { display:flex; align-items:center; gap:12px; padding:12px 16px; border:2px solid #e2e8f0; border-radius:10px; margin-bottom:8px; cursor:pointer; transition:.15s; }
.opt-label:hover { border-color:var(--purple); background:#f5f3ff; }
.opt-label input[type=radio]:checked ~ span { font-weight:700; }
.opt-label:has(input:checked) { border-color:var(--purple); background:#ede9fe; }
.opt-circle { width:34px; height:34px; border-radius:50%; border:2px solid #e2e8f0; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:.8rem; flex-shrink:0; background:#f8fafc; transition:.15s; }
.opt-label:has(input:checked) .opt-circle { background:var(--purple); border-color:var(--purple); color:#fff; }
.timer-bar { height:6px; border-radius:3px; background:#e2e8f0; overflow:hidden; margin-top:8px; }
.timer-fill { height:100%; background:var(--gold); border-radius:3px; transition:width 1s linear; }
.q-nav { display:flex; flex-wrap:wrap; gap:6px; }
.q-nav-btn { width:36px; height:36px; border-radius:8px; border:2px solid #e2e8f0; background:#fff; font-size:.8rem; font-weight:700; cursor:pointer; transition:.15s; }
.q-nav-btn.answered { background:#ede9fe; border-color:var(--purple); color:var(--purple); }
.q-nav-btn.current { background:var(--purple); border-color:var(--purple); color:#fff; }
</style>

<div class="quiz-container">
  <!-- Quiz header -->
  <div class="quiz-header">
    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
      <div>
        <h4 class="fw-900 mb-1"><?= h($quiz['title']) ?></h4>
        <div style="color:rgba(255,255,255,.75);font-size:.85rem;"><?= h($quiz['subject_name'] ?? '') ?><?= $quiz['year_group'] ? ' · '.$quiz['year_group'] : '' ?><?= $quiz['lesson'] ? ' · '.$quiz['lesson'] : '' ?></div>
      </div>
      <?php if ($quiz['time_limit_min'] > 0): ?>
      <div class="text-end" style="min-width:100px;">
        <div id="timerDisplay" class="fw-900" style="font-size:1.8rem;letter-spacing:.05em;color:var(--gold);">--:--</div>
        <div class="timer-bar"><div id="timerFill" class="timer-fill" style="width:100%"></div></div>
        <div style="font-size:.65rem;color:rgba(255,255,255,.5);margin-top:4px;">TIME REMAINING</div>
      </div>
      <?php endif; ?>
    </div>
    <div class="d-flex gap-3 mt-3 flex-wrap">
      <span class="badge" style="background:rgba(255,255,255,.15);"><?= count($questions) ?> Questions</span>
      <span class="badge" style="background:rgba(255,255,255,.15);"><?= $quiz['marks_per_question'] ?> mark each</span>
      <?php if ($quiz['pass_mark_pct']): ?><span class="badge" style="background:rgba(255,255,255,.15);">Pass: <?= $quiz['pass_mark_pct'] ?>%</span><?php endif; ?>
      <?php if ($quiz['negative_marking'] > 0): ?><span class="badge bg-danger">-<?= $quiz['negative_marking'] ?> per wrong</span><?php endif; ?>
    </div>
  </div>

  <form method="POST" id="quizForm">
    <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
    <input type="hidden" name="submit_quiz" value="1">
    <input type="hidden" name="time_taken" id="timeTakenField" value="0">

    <div class="row g-4">
      <!-- Questions -->
      <div class="col-lg-8">
        <?php foreach ($questions as $i => $q):
          $opts = ['a','b','c','d'];
          if ($quiz['shuffle_options']) shuffle($opts);
          $saved = $savedAnswers[$q['id']] ?? null;
        ?>
        <div class="q-card" id="qcard_<?= $i ?>">
          <div class="d-flex gap-3">
            <div style="width:32px;height:32px;border-radius:50%;background:#ede9fe;color:var(--purple);font-weight:900;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><?= $i+1 ?></div>
            <div class="flex-grow-1">
              <div class="fw-600 mb-3"><?= nl2br(h($q['question'])) ?></div>
              <?php foreach ($opts as $k): ?>
                <label class="opt-label">
                  <input type="radio" name="answer[<?= $q['id'] ?>]" value="<?= $k ?>" <?= $saved===$k?'checked':'' ?> class="d-none" onchange="markAnswered(<?= $i ?>)">
                  <div class="opt-circle"><?= strtoupper($k) ?></div>
                  <span><?= h($q['option_'.$k]) ?></span>
                </label>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>

        <!-- Submit -->
        <div class="stat-card text-center">
          <p class="text-muted small mb-3">Review your answers before submitting. Once submitted you cannot change them.</p>
          <button type="submit" class="btn btn-lg px-5 fw-700" style="background:var(--purple);color:#fff;" onclick="return confirm('Submit quiz? You cannot make changes after submitting.')">
            <i class="bi bi-check-circle me-2"></i>Submit Quiz
          </button>
        </div>
      </div>

      <!-- Sidebar: navigation -->
      <div class="col-lg-4">
        <div class="stat-card" style="position:sticky;top:80px;">
          <div class="fw-700 mb-3 small text-uppercase" style="letter-spacing:.06em;color:#888;">Question Navigator</div>
          <div class="q-nav" id="qNav">
            <?php foreach ($questions as $i => $q): ?>
              <button type="button" class="q-nav-btn <?= isset($savedAnswers[$q['id']]) ? 'answered' : '' ?>" id="qnav_<?= $i ?>" onclick="scrollToQ(<?= $i ?>)"><?= $i+1 ?></button>
            <?php endforeach; ?>
          </div>
          <div class="mt-3 d-flex gap-2 flex-wrap small">
            <span><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#ede9fe;"></span> Answered</span>
            <span><span style="display:inline-block;width:10px;height:10px;border-radius:2px;border:2px solid #e2e8f0;"></span> Unanswered</span>
          </div>
          <div class="mt-3 small text-muted" id="progressText">0/<?= count($questions) ?> answered</div>
        </div>
      </div>
    </div>
  </form>
</div>

<script>
const TOTAL_SEC = <?= $remainingSec ?>;
const TOTAL_LIMIT = <?= $totalTimeSec ?>;
let elapsed = <?= $elapsedSec ?>;
let answered = {};

// Pre-mark already-answered (from DB)
<?php foreach ($savedAnswers as $qid => $chosen): ?>
  answered[<?= $qid ?>] = true;
<?php endforeach; ?>

function markAnswered(idx) {
  const card = document.getElementById('qcard_' + idx);
  const radios = card.querySelectorAll('input[type=radio]');
  radios.forEach(r => {
    if (r.checked) { answered[r.name] = true; }
  });
  const navBtn = document.getElementById('qnav_' + idx);
  if (navBtn) navBtn.classList.add('answered');
  const cnt = Object.keys(answered).length;
  document.getElementById('progressText').textContent = cnt + '/<?= count($questions) ?> answered';
}

function scrollToQ(idx) {
  document.querySelectorAll('.q-nav-btn').forEach(b => b.classList.remove('current'));
  document.getElementById('qnav_' + idx)?.classList.add('current');
  document.getElementById('qcard_' + idx)?.scrollIntoView({behavior:'smooth',block:'start'});
}

// Timer
<?php if ($quiz['time_limit_min'] > 0 && $remainingSec > 0): ?>
let remaining = <?= $remainingSec ?>;
const timerEl    = document.getElementById('timerDisplay');
const timerFill  = document.getElementById('timerFill');
const timeTaken  = document.getElementById('timeTakenField');
function tick() {
  if (remaining <= 0) { timeTaken.value = TOTAL_LIMIT; document.getElementById('quizForm').submit(); return; }
  remaining--;
  elapsed++;
  timeTaken.value = elapsed;
  const m = Math.floor(remaining/60), s = remaining%60;
  timerEl.textContent = String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
  timerEl.style.color = remaining < 60 ? '#ef4444' : (remaining < 180 ? '#f59e0b' : '#F5A623');
  const pct = (remaining / TOTAL_LIMIT) * 100;
  timerFill.style.width = pct + '%';
  timerFill.style.background = remaining < 60 ? '#ef4444' : '#F5A623';
}
setInterval(tick, 1000);
tick();
<?php else: ?>
// No timer — track elapsed
let elapsedTmr = 0;
setInterval(() => { elapsedTmr++; document.getElementById('timeTakenField').value = elapsedTmr; }, 1000);
<?php endif; ?>

// Pre-populate answered nav buttons
window.addEventListener('load', () => {
  document.querySelectorAll('input[type=radio]:checked').forEach(r => {
    const card = r.closest('.q-card');
    if (card) {
      const idx = Array.from(document.querySelectorAll('.q-card')).indexOf(card);
      const navBtn = document.getElementById('qnav_' + idx);
      if (navBtn) navBtn.classList.add('answered');
    }
  });
  const cnt = document.querySelectorAll('input[type=radio]:checked').length;
  document.getElementById('progressText').textContent = cnt + '/<?= count($questions) ?> answered';
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
