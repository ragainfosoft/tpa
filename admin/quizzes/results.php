<?php
// =====================================================
// TPA IMS — Admin/Teacher Quiz Results Viewer
// =====================================================
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
startSecureSession();
requireRole(['admin', 'branch_manager', 'teacher']);

$db = getDB();
$quizId = (int)($_GET['quiz_id'] ?? 0);
if (!$quizId) { header('Location: index.php'); exit; }

// Fetch quiz details
$quizStmt = $db->prepare("SELECT qs.*, sub.name as subject_name FROM quiz_sets qs LEFT JOIN subjects sub ON sub.id=qs.subject_id WHERE qs.id=?");
$quizStmt->execute([$quizId]);
$quiz = $quizStmt->fetch();
if (!$quiz) { setFlash('danger', 'Quiz not found.'); header('Location: index.php'); exit; }

// Allow deletion of attempts
if (isset($_GET['delete_attempt'])) {
    verifyCsrf();
    $db->prepare("DELETE FROM quiz_attempts WHERE id=? AND quiz_id=?")->execute([(int)$_GET['delete_attempt'], $quizId]);
    setFlash('success', 'Attempt deleted successfully.');
    header("Location: results.php?quiz_id=$quizId"); exit;
}

// Fetch attempts
$attemptsStmt = $db->prepare("
    SELECT a.*, s.first_name, s.last_name, s.student_ref, b.name as batch_name 
    FROM quiz_attempts a
    JOIN students s ON s.id = a.student_id
    LEFT JOIN quiz_assignments qa ON qa.id = a.assignment_id
    LEFT JOIN batches b ON b.id = qa.batch_id
    WHERE a.quiz_id = ? AND a.status = 'submitted'
    ORDER BY a.percentage DESC, a.submitted_at DESC
");
$attemptsStmt->execute([$quizId]);
$attempts = $attemptsStmt->fetchAll();

$passedCount = 0;
$totalAttempts = count($attempts);
$avgScore = 0;

if ($totalAttempts > 0) {
    foreach ($attempts as $a) { if ($a['passed']) $passedCount++; }
    $avgScore = round(array_sum(array_column($attempts, 'percentage')) / $totalAttempts, 1);
}

$page_title   = 'Quiz Results: ' . h($quiz['title']);
$page_section = 'quizzes';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
  <div>
    <h1><i class="bi bi-bar-chart-line me-2" style="color:var(--navy);"></i>Quiz Results</h1>
    <p class="text-muted mb-0"><?= h($quiz['title']) ?> · <?= h($quiz['subject_name'] ?? 'General') ?></p>
  </div>
  <a href="index.php" class="btn btn-sm btn-outline-secondary">← Back to Library</a>
</div>

<div class="row g-4 mb-4">
  <div class="col-md-4">
    <div class="stat-card">
      <div class="stat-label text-muted">Total Submissions</div>
      <div class="fw-700 fs-5"><?= $totalAttempts ?></div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="stat-card">
      <div class="stat-label text-muted">Average Score</div>
      <div class="fw-700 fs-5 <?= $avgScore >= $quiz['pass_mark_pct'] ? 'text-success' : 'text-danger' ?>"><?= $avgScore ?>%</div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="stat-card">
      <div class="stat-label text-muted">Pass Rate</div>
      <div class="fw-700 fs-5"><?= $totalAttempts > 0 ? round(($passedCount / $totalAttempts) * 100) : 0 ?>%</div>
    </div>
  </div>
</div>

<div class="stat-card p-0">
  <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
    <h5 class="fw-700 mb-0">Student Submissions (Leaderboard)</h5>
  </div>
  
  <div class="tpa-table table-responsive">
    <table class="table table-hover mb-0 dt-table">
      <thead class="bg-light">
        <tr>
          <th width="50">Rank</th>
          <th>Student</th>
          <th>Batch</th>
          <th>Score</th>
          <th>Percentage</th>
          <th>Status</th>
          <th>Time Taken</th>
          <th>Date Submitted</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php $rank=1; foreach ($attempts as $a): ?>
        <tr>
          <td class="fw-700 text-muted">#<?= $rank++ ?></td>
          <td class="fw-600">
             <a href="<?= SITE_URL ?>/students/view.php?id=<?= $a['student_id'] ?>"><?= h($a['first_name'] . ' ' . $a['last_name']) ?></a>
             <div class="small text-muted"><?= h($a['student_ref']) ?></div>
          </td>
          <td class="small"><?= h($a['batch_name'] ?? 'Individual') ?></td>
          <td class="fw-600"><?= $a['score'] ?> / <?= $a['max_score'] ?></td>
          <td>
            <div class="d-flex align-items-center gap-2">
              <div class="progress flex-grow-1" style="height:6px;max-width:80px;">
                <div class="progress-bar <?= $a['passed'] ? 'bg-success' : 'bg-danger' ?>" style="width: <?= $a['percentage'] ?>%"></div>
              </div>
              <span class="small fw-700"><?= $a['percentage'] ?>%</span>
            </div>
          </td>
          <td>
            <?php if ($a['passed']): ?>
              <span class="badge bg-success bg-opacity-10 text-success border border-success">PASSED</span>
            <?php else: ?>
              <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">FAILED</span>
            <?php endif; ?>
          </td>
          <td class="small text-muted"><?= round($a['time_taken_sec'] / 60) ?> mins</td>
          <td class="small text-muted"><?= date('d M Y, g:ia', strtotime($a['submitted_at'])) ?></td>
          <td class="text-end">
            <a href="results.php?quiz_id=<?= $quizId ?>&delete_attempt=<?= $a['id'] ?>&csrf_token=<?= h(csrfToken()) ?>" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="return confirm('WARNING: This will permanently delete this student\'s attempt and allow them to take it again if limits apply. Continue?')" title="Delete Attempt"><i class="bi bi-trash"></i></a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
