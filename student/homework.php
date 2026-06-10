<?php
// =====================================================
// TPA — Student Homework
// =====================================================
$page_title   = 'Homework';
$page_section = 'homework';
require_once __DIR__ . '/includes/header.php';

if (!$studentId) { echo '<div class="alert alert-warning">No student account linked.</div>'; require_once __DIR__ . '/includes/footer.php'; exit; }

// Handle Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_hw'])) {
    verifyCsrf();
    $hwId  = (int)($_POST['homework_id'] ?? 0);
    $notes = trim($_POST['notes'] ?? '');
    // verify assignment
    $c = $db->prepare('SELECT h.id FROM homework h JOIN batch_students bs ON bs.batch_id=h.batch_id WHERE h.id=? AND bs.student_id=?');
    $c->execute([$hwId, $studentId]);
    if ($c->fetchColumn()) {
        $db->prepare('INSERT INTO homework_submissions (homework_id,student_id,notes,status) VALUES (?,?,?,"submitted") ON DUPLICATE KEY UPDATE notes=VALUES(notes), status="submitted"')
           ->execute([$hwId, $studentId, $notes]);
        setFlash('success','Homework marked as submitted!');
    }
    header('Location: homework.php'); exit;
}

$hwList = $db->prepare("SELECT h.*, b.name as batch_name, s.name as subject_name,
    hs.id as submission_id, hs.status as sub_status, hs.submitted_at, hs.teacher_feedback
    FROM homework h JOIN batch_students bs ON bs.batch_id=h.batch_id JOIN batches b ON b.id=h.batch_id LEFT JOIN subjects s ON s.id=h.subject_id
    LEFT JOIN homework_submissions hs ON hs.homework_id=h.id AND hs.student_id=?
    WHERE bs.student_id=? AND bs.is_active=1 ORDER BY h.due_date DESC");
$hwList->execute([$studentId,$studentId]); $hwList = $hwList->fetchAll();

$pending = array_filter($hwList, fn($h)=>empty($h['submission_id']));
$done    = array_filter($hwList, fn($h)=>!empty($h['submission_id']));
$focusId = (int)($_GET['submit'] ?? 0);
?>

<div class="page-header">
  <h1><i class="bi bi-pencil-square me-2" style="color:var(--purple);"></i>My Homework</h1>
  <span class="badge" style="background:#ede9fe;color:#4c1d95;padding:8px 14px;"><?= count($pending) ?> Pending</span>
</div>

<?php if (!empty($pending)): ?>
<h6 class="text-uppercase fw-700 mb-3" style="font-size:.7rem;letter-spacing:.1em;color:#888;">⏳ Needed</h6>
<div class="row g-3 mb-4">
  <?php foreach ($pending as $h):
    $isLate = strtotime($h['due_date']) < strtotime('today');
  ?>
  <div class="col-md-6 col-lg-4">
    <div class="stat-card h-100" style="<?= $focusId===$h['id']?'border-color:var(--purple);box-shadow:0 0 0 3px rgba(124,58,237,.12);':'' ?>">
      <div class="d-flex justify-content-between align-items-start mb-2">
        <span class="badge" style="background:#ede9fe;color:#4c1d95;font-size:.67rem;"><?= h($h['subject_name']??$h['batch_name']) ?></span>
        <?php if ($isLate): ?><span class="badge bg-danger" style="font-size:.65rem;">Late</span><?php endif; ?>
      </div>
      <div class="fw-700 mb-1"><?= h($h['title']) ?></div>
      <div class="small fw-600 <?= $isLate?'text-danger':'text-muted' ?>">Due: <?= date('D d M Y', strtotime($h['due_date'])) ?></div>
      <?php if ($h['description']): ?><div class="small text-muted mt-2 p-2 rounded" style="background:#f1f5f9;"><i class="bi bi-info-circle me-1"></i><?= h($h['description']) ?></div><?php endif; ?>

      <form method="POST" class="mt-3 border-top pt-3">
        <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
        <input type="hidden" name="submit_hw" value="1">
        <input type="hidden" name="homework_id" value="<?= $h['id'] ?>">
        <textarea name="notes" class="form-control form-control-sm mb-2" rows="1" placeholder="Add a note (optional)"></textarea>
        <button type="submit" class="btn btn-sm w-100 fw-700 text-dark" style="background:var(--gold);"><i class="bi bi-upload me-1"></i>Mark as Done</button>
      </form>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if (!empty($done)): ?>
<h6 class="text-uppercase fw-700 mb-3" style="font-size:.7rem;letter-spacing:.1em;color:#888;">✅ Completed</h6>
<div class="tpa-table">
  <table class="table table-hover mb-0">
    <thead><tr><th>Task</th><th>Subject</th><th>Due Date</th><th>Submitted On</th><th>Status</th><th>Feedback</th></tr></thead>
    <tbody>
    <?php foreach ($done as $h): ?>
      <tr>
        <td><div class="fw-600"><?= h($h['title']) ?></div><div class="small text-muted"><?= h($h['batch_name']) ?></div></td>
        <td class="small text-muted"><?= h($h['subject_name']??'—') ?></td>
        <td class="small"><?= date('d M Y', strtotime($h['due_date'])) ?></td>
        <td class="small fw-600"><?= date('d M Y', strtotime($h['submitted_at'])) ?></td>
        <td><span class="badge" style="background:#dcfce7;color:#166534;"><i class="bi bi-check-all me-1"></i>Done</span></td>
        <td class="small text-muted"><?= h($h['teacher_feedback']??'—') ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<?php if (empty($hwList)): ?>
  <div class="text-center py-5 text-muted" style="background:#fff;border-radius:16px;">
    <i class="bi bi-emoji-sunglasses fs-1 d-block mb-3" style="color:#fbbf24;"></i>
    <h5>No homework required!</h5>
    <p class="small">Relax, you don't have any pending assignments.</p>
  </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
