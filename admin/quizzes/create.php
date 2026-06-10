<?php
// =====================================================
// TPA — MCQ Quiz Builder (Admin / Teacher)
// Create or edit a quiz with all questions
// =====================================================

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
startSecureSession();
requireRole(['admin','branch_manager','teacher']);

$db = getDB();
$quizId = (int)($_GET['id'] ?? 0);
$errors = [];

// ── Save quiz header ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_quiz'])) {
    verifyCsrf();
    $title    = trim($_POST['title'] ?? '');
    $subjectId = (int)($_POST['subject_id'] ?? 0) ?: null;
    $yearGroup = trim($_POST['year_group'] ?? '');
    $lesson    = trim($_POST['lesson'] ?? '');
    $desc      = trim($_POST['description'] ?? '');
    $timeLimit = (int)($_POST['time_limit_min'] ?? 0);
    $attempts  = (int)($_POST['attempt_limit'] ?? 1);
    $passMark  = (float)($_POST['pass_mark_pct'] ?? 60);
    $shuffle_q = isset($_POST['shuffle_questions']) ? 1 : 0;
    $shuffle_o = isset($_POST['shuffle_options']) ? 1 : 0;
    $neg       = (float)($_POST['negative_marking'] ?? 0);
    $resultMode= in_array($_POST['result_mode']??'',['instant','delayed']) ? $_POST['result_mode'] : 'instant';
    $mPerQ     = (float)($_POST['marks_per_question'] ?? 1);

    if (!$title) $errors[] = 'Quiz title is required.';

    if (empty($errors)) {
        if ($quizId) {
            $db->prepare('UPDATE quiz_sets SET title=?,subject_id=?,year_group=?,lesson=?,description=?,time_limit_min=?,attempt_limit=?,pass_mark_pct=?,shuffle_questions=?,shuffle_options=?,negative_marking=?,result_mode=?,marks_per_question=?,updated_at=NOW() WHERE id=?')
               ->execute([$title,$subjectId,$yearGroup,$lesson,$desc,$timeLimit,$attempts,$passMark,$shuffle_q,$shuffle_o,$neg,$resultMode,$mPerQ,$quizId]);
        } else {
            $db->prepare('INSERT INTO quiz_sets (title,subject_id,year_group,lesson,description,time_limit_min,attempt_limit,pass_mark_pct,shuffle_questions,shuffle_options,negative_marking,result_mode,marks_per_question,created_by) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)')
               ->execute([$title,$subjectId,$yearGroup,$lesson,$desc,$timeLimit,$attempts,$passMark,$shuffle_q,$shuffle_o,$neg,$resultMode,$mPerQ,currentUserId()]);
            $quizId = (int)$db->lastInsertId();
        }
        setFlash('success', 'Quiz saved.');
        header('Location: create.php?id='.$quizId); exit;
    }
}

// ── Save a question ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_question'])) {
    verifyCsrf();
    $qid      = (int)($_POST['question_id'] ?? 0);
    $question = trim($_POST['question'] ?? '');
    $optA     = trim($_POST['option_a'] ?? '');
    $optB     = trim($_POST['option_b'] ?? '');
    $optC     = trim($_POST['option_c'] ?? '');
    $optD     = trim($_POST['option_d'] ?? '');
    $correct  = in_array($_POST['correct']??'',['a','b','c','d']) ? $_POST['correct'] : 'a';
    $expl     = trim($_POST['explanation'] ?? '');
    $marks    = (float)($_POST['marks'] ?? 1);
    $qzid     = (int)($_POST['quiz_id'] ?? 0);

    if ($question && $optA && $optB && $optC && $optD && $qzid) {
        if ($qid) {
            $db->prepare('UPDATE quiz_questions SET question=?,option_a=?,option_b=?,option_c=?,option_d=?,correct=?,explanation=?,marks=? WHERE id=? AND quiz_id=?')
               ->execute([$question,$optA,$optB,$optC,$optD,$correct,$expl,$marks,$qid,$qzid]);
        } else {
            $cntStmt = $db->prepare('SELECT COUNT(*)+1 FROM quiz_questions WHERE quiz_id=?'); $cntStmt->execute([$qzid]); $count = (int)$cntStmt->fetchColumn();
            $db->prepare('INSERT INTO quiz_questions (quiz_id,question,option_a,option_b,option_c,option_d,correct,explanation,marks,sort_order) VALUES (?,?,?,?,?,?,?,?,?,?)')
               ->execute([$qzid,$question,$optA,$optB,$optC,$optD,$correct,$expl,$marks,$count]);
        }
        setFlash('success','Question saved.');
    }
    header('Location: create.php?id='.$qzid); exit;
}

// ── Delete question ──
if (isset($_GET['delete_q']) && $quizId) {
    $db->prepare('DELETE FROM quiz_questions WHERE id=? AND quiz_id=?')->execute([(int)$_GET['delete_q'], $quizId]);
    header('Location: create.php?id='.$quizId); exit;
}

$quiz = null;
$questions = [];
$editQ = null;
if ($quizId) {
    $s = $db->prepare('SELECT qs.*, sub.name as subject_name FROM quiz_sets qs LEFT JOIN subjects sub ON sub.id=qs.subject_id WHERE qs.id=?');
    $s->execute([$quizId]); $quiz = $s->fetch();
    $qs = $db->prepare('SELECT * FROM quiz_questions WHERE quiz_id=? ORDER BY sort_order, id');
    $qs->execute([$quizId]); $questions = $qs->fetchAll();
    if (isset($_GET['edit_q'])) {
        foreach ($questions as $q) if ($q['id'] == (int)$_GET['edit_q']) { $editQ = $q; break; }
    }
}
$subjects = $db->query('SELECT id,name FROM subjects ORDER BY sort_order')->fetchAll();

$page_title   = $quizId ? 'Edit Quiz' : 'Create Quiz';
$page_section = 'quizzes';
require_once __DIR__ . '/../includes/header.php';
?>

<style>
.option-label { display:flex;align-items:center;gap:8px;padding:10px 14px;border-radius:8px;border:2px solid #e2e8f0;cursor:pointer;transition:.15s;margin-bottom:8px; }
.option-label:hover { border-color:var(--gold); }
.option-label input[type=radio]:checked + span { font-weight:700;color:var(--navy); }
.q-card { background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:16px;margin-bottom:12px;transition:.15s; }
.q-card:hover { border-color:var(--gold);box-shadow:0 2px 8px rgba(0,0,0,.06); }
.step-chip { display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:50%;font-weight:900;font-size:.85rem;flex-shrink:0; }
</style>

<div class="page-header">
  <div>
    <h1><i class="bi bi-question-circle me-2" style="color:var(--gold);"></i><?= $quizId ? 'Edit Quiz' : 'New Quiz' ?></h1>
    <?php if ($quiz): ?><p class="text-muted mb-0 small"><?= h($quiz['title']) ?> · <?= count($questions) ?> question(s)</p><?php endif; ?>
  </div>
  <div class="d-flex gap-2">
    <?php if ($quiz): ?>
      <a href="assign.php?quiz_id=<?= $quizId ?>" class="btn btn-sm btn-warning text-dark fw-700"><i class="bi bi-send me-1"></i>Assign</a>
      <a href="results.php?quiz_id=<?= $quizId ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-bar-chart me-1"></i>Results</a>
    <?php endif; ?>
    <a href="index.php" class="btn btn-sm btn-outline-secondary">← Back</a>
  </div>
</div>

<?php if ($errors): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= h($e) ?></li><?php endforeach; ?></ul></div><?php endif; ?>

<div class="row g-4">
  <!-- Quiz Settings -->
  <div class="col-lg-5">
    <div class="stat-card" style="position:sticky;top:80px;">
      <div class="d-flex align-items-center gap-2 mb-4">
        <div class="step-chip" style="background:var(--navy);color:var(--gold);">⚙</div>
        <h6 class="mb-0 fw-700 text-uppercase" style="font-size:.7rem;letter-spacing:.1em;color:#888;">Quiz Settings</h6>
      </div>
      <form method="POST" class="row g-3">
        <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
        <input type="hidden" name="save_quiz" value="1">
        <div class="col-12"><label class="form-label fw-600 small">Quiz Title *</label><input type="text" name="title" class="form-control" value="<?= h($quiz['title'] ?? '') ?>" required placeholder="e.g. Year 5 Maths — Fractions Quiz 1"></div>
        <div class="col-sm-6">
          <label class="form-label fw-600 small">Subject</label>
          <select name="subject_id" class="form-select">
            <option value="">Any</option>
            <?php foreach ($subjects as $s): ?><option value="<?= $s['id'] ?>" <?= ($quiz['subject_id']??'')==$s['id']?'selected':'' ?>><?= h($s['name']) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="col-sm-6">
          <label class="form-label fw-600 small">Year Group</label>
          <select name="year_group" class="form-select">
            <option value="">All</option>
            <?php foreach (['Year 1','Year 2','Year 3','Year 4','Year 5','Year 6','Year 7','Year 8','Year 9','Year 10','Year 11'] as $yg): ?>
              <option value="<?= $yg ?>" <?= ($quiz['year_group']??'')===$yg?'selected':'' ?>><?= $yg ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12"><label class="form-label fw-600 small">Lesson / Topic</label><input type="text" name="lesson" class="form-control" value="<?= h($quiz['lesson'] ?? '') ?>" placeholder="e.g. Algebra, Fractions, Comprehension"></div>
        <div class="col-12"><label class="form-label fw-600 small">Description (optional)</label><textarea name="description" class="form-control" rows="2"><?= h($quiz['description'] ?? '') ?></textarea></div>
        <div class="col-sm-4"><label class="form-label fw-600 small">Time Limit (min)</label><input type="number" name="time_limit_min" class="form-control" value="<?= $quiz['time_limit_min'] ?? 30 ?>" min="0" placeholder="0=unlimited"></div>
        <div class="col-sm-4"><label class="form-label fw-600 small">Max Attempts</label><input type="number" name="attempt_limit" class="form-control" value="<?= $quiz['attempt_limit'] ?? 1 ?>" min="0" placeholder="0=unlimited"></div>
        <div class="col-sm-4"><label class="form-label fw-600 small">Pass Mark (%)</label><input type="number" name="pass_mark_pct" class="form-control" value="<?= $quiz['pass_mark_pct'] ?? 60 ?>" min="0" max="100"></div>
        <div class="col-sm-4"><label class="form-label fw-600 small">Marks/Question</label><input type="number" name="marks_per_question" class="form-control" value="<?= $quiz['marks_per_question'] ?? 1 ?>" min="0.5" step="0.5"></div>
        <div class="col-sm-4"><label class="form-label fw-600 small">Negative Marks</label><input type="number" name="negative_marking" class="form-control" value="<?= $quiz['negative_marking'] ?? 0 ?>" min="0" step="0.25"></div>
        <div class="col-sm-4">
          <label class="form-label fw-600 small">Results</label>
          <select name="result_mode" class="form-select">
            <option value="instant" <?= ($quiz['result_mode']??'instant')==='instant'?'selected':'' ?>>Instant</option>
            <option value="delayed" <?= ($quiz['result_mode']??'')==='delayed'?'selected':'' ?>>Delayed</option>
          </select>
        </div>
        <div class="col-6"><div class="form-check"><input type="checkbox" name="shuffle_questions" class="form-check-input" id="shufQ" <?= ($quiz['shuffle_questions']??0)?'checked':'' ?>><label class="form-check-label small fw-600" for="shufQ">Shuffle Questions</label></div></div>
        <div class="col-6"><div class="form-check"><input type="checkbox" name="shuffle_options" class="form-check-input" id="shufO" <?= ($quiz['shuffle_options']??0)?'checked':'' ?>><label class="form-check-label small fw-600" for="shufO">Shuffle Options</label></div></div>
        <div class="col-12"><button type="submit" class="btn btn-dark w-100"><i class="bi bi-save me-1"></i><?= $quizId ? 'Update Settings' : 'Create Quiz' ?></button></div>
      </form>
    </div>
  </div>

  <!-- Questions Panel -->
  <?php if ($quizId): ?>
  <div class="col-lg-7">
    <!-- Add/Edit Question form -->
    <div class="stat-card mb-4">
      <div class="d-flex align-items-center gap-2 mb-4">
        <div class="step-chip" style="background:var(--gold);color:var(--navy);" id="qFormTitle">+</div>
        <h6 class="mb-0 fw-700 text-uppercase" style="font-size:.7rem;letter-spacing:.1em;color:#888;"><?= $editQ ? 'Edit Question' : 'Add Question' ?></h6>
      </div>
      <form method="POST" class="row g-3">
        <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
        <input type="hidden" name="save_question" value="1">
        <input type="hidden" name="quiz_id" value="<?= $quizId ?>">
        <input type="hidden" name="question_id" value="<?= $editQ['id'] ?? 0 ?>">
        <div class="col-12"><label class="form-label fw-600 small">Question *</label><textarea name="question" class="form-control" rows="2" required placeholder="Enter your question here…"><?= h($editQ['question'] ?? '') ?></textarea></div>
        <?php foreach (['a'=>'A','b'=>'B','c'=>'C','d'=>'D'] as $k=>$l): ?>
        <div class="col-6">
          <label class="form-label fw-600 small">Option <?= $l ?></label>
          <div class="d-flex gap-2 align-items-center">
            <input type="radio" name="correct" value="<?= $k ?>" <?= ($editQ['correct']??'a')===$k?'checked':'' ?> title="Mark as correct">
            <input type="text" name="option_<?= $k ?>" class="form-control form-control-sm" value="<?= h($editQ['option_'.$k] ?? '') ?>" required placeholder="Option <?= $l ?>">
          </div>
        </div>
        <?php endforeach; ?>
        <div class="col-12"><label class="form-label fw-600 small text-muted">Explanation (shown after quiz)</label><textarea name="explanation" class="form-control form-control-sm" rows="1" placeholder="Optional explanation for the correct answer"><?= h($editQ['explanation'] ?? '') ?></textarea></div>
        <div class="col-sm-4"><label class="form-label fw-600 small">Marks</label><input type="number" name="marks" class="form-control form-control-sm" value="<?= $editQ['marks'] ?? 1 ?>" min="0.25" step="0.25"></div>
        <div class="col-8 d-flex align-items-end gap-2">
          <button type="submit" class="btn btn-dark btn-sm"><i class="bi bi-plus-lg me-1"></i><?= $editQ ? 'Update Question' : 'Add Question' ?></button>
          <?php if ($editQ): ?><a href="create.php?id=<?= $quizId ?>" class="btn btn-outline-secondary btn-sm">Cancel Edit</a><?php endif; ?>
        </div>
      </form>
    </div>

    <!-- Question list -->
    <div class="fw-700 mb-3" style="color:var(--navy);"><?= count($questions) ?> Question<?= count($questions)!==1?'s':'' ?></div>
    <?php if (empty($questions)): ?>
      <div class="text-center text-muted py-5" style="background:#f8f9fa;border-radius:12px;">
        <i class="bi bi-question-circle fs-1 d-block mb-2"></i>No questions yet. Add your first question above.
      </div>
    <?php else: ?>
      <?php foreach ($questions as $i => $q): ?>
        <div class="q-card">
          <div class="d-flex align-items-start justify-content-between gap-3">
            <div class="d-flex gap-3 flex-grow-1">
              <div class="step-chip" style="background:#f1f5f9;color:var(--navy);font-size:.8rem;"><?= $i+1 ?></div>
              <div class="flex-grow-1">
                <div class="fw-600 mb-2"><?= h($q['question']) ?></div>
                <div class="row g-1">
                  <?php foreach (['a','b','c','d'] as $k): ?>
                    <div class="col-6">
                      <div class="small <?= $q['correct']===$k?'fw-700 text-success':'' ?>" style="<?= $q['correct']===$k?'background:#f0fdf4;':'background:#f8fafc;' ?> border-radius:6px;padding:4px 8px;">
                        <?= strtoupper($k) ?>. <?= h($q['option_'.$k]) ?> <?= $q['correct']===$k?' ✓':'' ?>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
                <?php if ($q['explanation']): ?><div class="mt-2 small text-muted"><i class="bi bi-lightbulb me-1"></i><?= h($q['explanation']) ?></div><?php endif; ?>
              </div>
            </div>
            <div class="d-flex flex-column gap-1">
              <a href="create.php?id=<?= $quizId ?>&edit_q=<?= $q['id'] ?>" class="btn btn-sm btn-outline-secondary py-0 px-2">Edit</a>
              <a href="create.php?id=<?= $quizId ?>&delete_q=<?= $q['id'] ?>" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="return confirm('Delete this question?')">Del</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
