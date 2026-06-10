<?php
// =====================================================
// TPA — Quiz Assignment Page (Admin/Teacher)
// Assign quiz to batch(es) or individual student
// =====================================================
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
startSecureSession();
requireRole(['admin','branch_manager','teacher']);

$db     = getDB();
$quizId = (int)($_GET['quiz_id'] ?? 0);
if (!$quizId) { header('Location: index.php'); exit; }

$quiz = $db->prepare('SELECT qs.*, sub.name as subject_name FROM quiz_sets qs LEFT JOIN subjects sub ON sub.id=qs.subject_id WHERE qs.id=?');
$quiz->execute([$quizId]); $quiz = $quiz->fetch();
if (!$quiz) { setFlash('danger','Quiz not found.'); header('Location: index.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign'])) {
    verifyCsrf();
    $batchIds  = $_POST['batch_ids'] ?? [];
    $dueDate   = $_POST['due_date'] ?? null;
    $assignedBy = currentUserId();

    foreach ($batchIds as $bid) {
        $bid = (int)$bid;
        if (!$bid) continue;
        // Remove previous assignment for same batch
        $db->prepare('DELETE FROM quiz_assignments WHERE quiz_id=? AND batch_id=?')->execute([$quizId,$bid]);
        $db->prepare('INSERT INTO quiz_assignments (quiz_id,batch_id,assigned_by,due_date) VALUES (?,?,?,?)')->execute([$quizId,$bid,$assignedBy,$dueDate?:null]);
    }

    // Notify students in assigned batches via WA if template set
    $waTemplate = getSetting('wa_template_quiz_assigned') ?? '';
    if ($waTemplate) {
        foreach ($batchIds as $bid) {
            $students = $db->query("SELECT s.id,s.first_name,sp.whatsapp,sp.phone FROM students s JOIN batch_students bs ON bs.student_id=s.id JOIN student_parents sp ON sp.student_id=s.id WHERE bs.batch_id=$bid AND bs.is_active=1 AND sp.is_primary=1 LIMIT 50");
            // WhatsApp would be sent here (async) — just log for now
        }
    }

    logActivity('quiz_assigned', "Quiz $quizId assigned to batches: ".implode(',', $batchIds));
    setFlash('success', '✅ Quiz assigned to '.count($batchIds).' batch(es).');
    header('Location: assign.php?quiz_id='.$quizId); exit;
}

$batches     = $db->query('SELECT b.id, b.name, b.centre, (SELECT COUNT(*) FROM batch_students bs WHERE bs.batch_id=b.id AND bs.is_active=1) as enrolled FROM batches b WHERE b.is_active=1 ORDER BY b.name')->fetchAll();
$currentAsgn = $db->prepare('SELECT batch_id FROM quiz_assignments WHERE quiz_id=?');
$currentAsgn->execute([$quizId]); $currentAsgn = array_column($currentAsgn->fetchAll(),'batch_id');

$page_title   = 'Assign Quiz';
$page_section = 'quizzes';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
  <div>
    <h1><i class="bi bi-send me-2" style="color:var(--gold);"></i>Assign Quiz</h1>
    <p class="text-muted mb-0"><?= h($quiz['title']) ?> · <?= h($quiz['subject_name'] ?? '') ?></p>
  </div>
  <a href="index.php" class="btn btn-sm btn-outline-secondary">← Library</a>
</div>

<div class="row g-4">
  <div class="col-lg-7">
    <div class="stat-card">
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
        <input type="hidden" name="assign" value="1">
        <div class="mb-4">
          <label class="form-label fw-600 small">Select Batches</label>
          <div class="row g-2">
            <?php foreach ($batches as $b): ?>
              <div class="col-sm-6">
                <label class="d-flex align-items-center gap-2 p-3" style="border:2px solid <?= in_array($b['id'],$currentAsgn)?'var(--gold)':'#e2e8f0' ?>;border-radius:10px;cursor:pointer;background:<?= in_array($b['id'],$currentAsgn)?'#fefce8':'#f8fafc' ?>;">
                  <input type="checkbox" name="batch_ids[]" value="<?= $b['id'] ?>" <?= in_array($b['id'],$currentAsgn)?'checked':'' ?> class="form-check-input mt-0">
                  <div>
                    <div class="fw-600 small"><?= h($b['name']) ?></div>
                    <div class="text-muted" style="font-size:.72rem;"><?= $b['centre']??'' ?> · <?= $b['enrolled'] ?> students</div>
                  </div>
                </label>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="mb-4">
          <label class="form-label fw-600 small">Due Date (optional)</label>
          <input type="date" name="due_date" class="form-control" style="max-width:220px;" min="<?= date('Y-m-d') ?>">
        </div>
        <button type="submit" class="btn btn-dark"><i class="bi bi-send me-1"></i>Assign Quiz</button>
      </form>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="stat-card">
      <div class="fw-700 mb-3">Quiz Summary</div>
      <table class="table table-sm mb-0">
        <tr><th class="text-muted fw-400 small border-0">Title</th><td class="fw-700 border-0"><?= h($quiz['title']) ?></td></tr>
        <tr><th class="text-muted fw-400 small">Subject</th><td><?= h($quiz['subject_name'] ?? '—') ?></td></tr>
        <tr><th class="text-muted fw-400 small">Year Group</th><td><?= h($quiz['year_group'] ?? '—') ?></td></tr>
        <tr><th class="text-muted fw-400 small">Time Limit</th><td><?= $quiz['time_limit_min']>0?$quiz['time_limit_min'].'min':'Unlimited' ?></td></tr>
        <tr><th class="text-muted fw-400 small">Attempts</th><td><?= $quiz['attempt_limit']>0?$quiz['attempt_limit']:'Unlimited' ?></td></tr>
        <tr><th class="text-muted fw-400 small">Pass Mark</th><td><?= $quiz['pass_mark_pct'] ?>%</td></tr>
        <tr><th class="text-muted fw-400 small">Shuffle</th><td><?= $quiz['shuffle_questions']?'Questions ':''.($quiz['shuffle_options']?'Options':'') ?><?= !$quiz['shuffle_questions']&&!$quiz['shuffle_options']?'Off':'' ?></td></tr>
        <tr><th class="text-muted fw-400 small">Negative Marking</th><td><?= $quiz['negative_marking']>0?$quiz['negative_marking'].' per wrong':'Off' ?></td></tr>
        <tr><th class="text-muted fw-400 small">Results</th><td><?= ucfirst($quiz['result_mode']) ?></td></tr>
      </table>
      <div class="mt-4 d-grid gap-2">
        <a href="create.php?id=<?= $quizId ?>" class="btn btn-outline-secondary btn-sm">Edit Quiz / Questions</a>
        <a href="results.php?quiz_id=<?= $quizId ?>" class="btn btn-outline-primary btn-sm">View All Results</a>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
