<?php
// =====================================================
// TPA IMS — Assessment Results Entry
// =====================================================

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
startSecureSession();
requireRole(['admin', 'branch_manager', 'teacher', 'staff']);

$db           = getDB();
$assessmentId = (int)($_GET['assessment_id'] ?? 0);
$errors       = [];

// Save results
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_results'])) {
    verifyCsrf();
    $aId  = (int)$_POST['assessment_id'];
    $ass  = $db->prepare('SELECT max_marks FROM assessments WHERE id=?');
    $ass->execute([$aId]); $ass = $ass->fetch();
    $maxMarks = (float)($ass['max_marks'] ?? 100);

    foreach ($_POST['marks'] as $studentId => $marks) {
        $marks    = $marks === '' ? null : (float)$marks;
        $feedback = trim($_POST['feedback'][$studentId] ?? '');
        $grade    = trim($_POST['grade'][$studentId] ?? '');
        $pct      = $marks !== null && $maxMarks > 0 ? round($marks / $maxMarks * 100, 2) : null;

        $db->prepare('INSERT INTO assessment_results (assessment_id,student_id,marks,max_marks,percentage,grade,feedback) VALUES (?,?,?,?,?,?,?)
            ON DUPLICATE KEY UPDATE marks=VALUES(marks),percentage=VALUES(percentage),grade=VALUES(grade),feedback=VALUES(feedback)')
           ->execute([$aId, (int)$studentId, $marks, $maxMarks, $pct, $grade?:null, $feedback?:null]);
    }

    logActivity('results_entered', "Results saved for assessment #$aId");
    setFlash('success', 'Results saved successfully.');
    header('Location: results.php?assessment_id=' . $aId); exit;
}

$assessment = null;
$students   = [];
$existing   = [];

if ($assessmentId) {
    $stmt = $db->prepare('SELECT a.*, b.name as batch_name FROM assessments a JOIN batches b ON b.id=a.batch_id WHERE a.id=?');
    $stmt->execute([$assessmentId]); $assessment = $stmt->fetch();

    if ($assessment) {
        $students = $db->prepare('SELECT s.id, CONCAT(s.first_name," ",s.last_name) as full_name, s.year_group FROM students s JOIN batch_students bs ON bs.student_id=s.id WHERE bs.batch_id=? AND bs.is_active=1 AND s.status="active" ORDER BY s.first_name')->fetchAll(PDO::FETCH_ASSOC);
        $db->prepare('SELECT * FROM assessments WHERE batch_id=?')->execute([$assessment['batch_id']]);

        $exRows = $db->prepare('SELECT * FROM assessment_results WHERE assessment_id=?');
        $exRows->execute([$assessmentId]);
        foreach ($exRows->fetchAll() as $r) $existing[$r['student_id']] = $r;
    }
}

$assessments = $db->query('SELECT a.id, a.name, a.type, a.date, b.name as batch_name FROM assessments a JOIN batches b ON b.id=a.batch_id ORDER BY a.date DESC LIMIT 50')->fetchAll();

$page_title   = 'Assessment Results';
$page_section = 'assessments';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
  <h1><i class="bi bi-journal-check me-2" style="color:var(--gold);"></i>Enter Results</h1>
  <a href="index.php" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Assessments</a>
</div>

<!-- Assessment picker -->
<div class="stat-card mb-4">
  <form method="GET" class="d-flex gap-3 align-items-end flex-wrap">
    <div class="flex-grow-1">
      <label class="form-label fw-600 small">Select Assessment</label>
      <select name="assessment_id" class="form-select" onchange="this.form.submit()">
        <option value="">Choose assessment…</option>
        <?php foreach ($assessments as $a): ?>
          <option value="<?= $a['id'] ?>" <?= $a['id']==$assessmentId?'selected':'' ?>>
            <?= h($a['name']) ?> — <?= h($a['batch_name']) ?> — <?= formatDate($a['date']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
  </form>
</div>

<?php if ($assessment): ?>
<!-- Assessment info -->
<div class="stat-card mb-4" style="background:linear-gradient(135deg,var(--navy) 0%,#1a2e52 100%);color:white;">
  <div class="d-flex gap-4 flex-wrap">
    <div><div style="color:rgba(255,255,255,.6);font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;">Assessment</div><div class="fw-700 mt-1"><?= h($assessment['name']) ?></div></div>
    <div><div style="color:rgba(255,255,255,.6);font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;">Batch</div><div class="fw-700 mt-1"><?= h($assessment['batch_name']) ?></div></div>
    <div><div style="color:rgba(255,255,255,.6);font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;">Date</div><div class="fw-700 mt-1"><?= formatDate($assessment['date']) ?></div></div>
    <div><div style="color:rgba(255,255,255,.6);font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;">Max Marks</div><div class="fw-700 mt-1"><?= h($assessment['max_marks']) ?></div></div>
    <div><div style="color:rgba(255,255,255,.6);font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;">Type</div><div class="fw-700 mt-1"><?= h(str_replace('_',' ',ucfirst($assessment['type']))) ?></div></div>
  </div>
</div>

<?php if (empty($students)): ?>
  <div class="alert alert-warning">No active students found in this batch. <a href="../batches/index.php">Manage Batches</a></div>
<?php else: ?>
<form method="POST">
  <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
  <input type="hidden" name="save_results" value="1">
  <input type="hidden" name="assessment_id" value="<?= $assessmentId ?>">

  <div class="tpa-table table-responsive">
    <table class="table mb-0">
      <thead>
        <tr>
          <th style="width:40%;">Student</th>
          <th style="width:15%;">Marks / <?= h($assessment['max_marks']) ?></th>
          <th style="width:10%;">%</th>
          <th style="width:10%;">Grade</th>
          <th>Feedback</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($students as $s):
          $ex = $existing[$s['id']] ?? null;
          $pct = $ex ? round($ex['percentage']) : '';
          $markColor = !$ex ? '#ddd' : ($ex['percentage']>=70?'#198754':($ex['percentage']>=50?'#ca8a04':'#dc3545'));
        ?>
          <tr>
            <td>
              <div class="fw-600 small"><?= h($s['full_name']) ?></div>
              <div class="text-muted" style="font-size:.72rem;"><?= h($s['year_group'] ?? '') ?></div>
            </td>
            <td>
              <input type="number" name="marks[<?= $s['id'] ?>]" class="form-control form-control-sm mark-input"
                     style="width:80px;" step="0.5" min="0" max="<?= h($assessment['max_marks']) ?>"
                     value="<?= $ex ? h($ex['marks']) : '' ?>"
                     data-max="<?= h($assessment['max_marks']) ?>"
                     oninput="calcPct(this)">
            </td>
            <td>
              <span class="mark-pct fw-700" style="color:<?= $markColor ?>;"><?= $ex ? $pct.'%' : '—' ?></span>
            </td>
            <td>
              <input type="text" name="grade[<?= $s['id'] ?>]" class="form-control form-control-sm" style="width:60px;" value="<?= h($ex['grade'] ?? '') ?>" placeholder="A">
            </td>
            <td>
              <input type="text" name="feedback[<?= $s['id'] ?>]" class="form-control form-control-sm" value="<?= h($ex['feedback'] ?? '') ?>" placeholder="Optional comment">
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="mt-3">
    <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i>Save All Results</button>
  </div>
</form>
<?php endif; ?>

<script>
function calcPct(input) {
  const max = parseFloat(input.dataset.max);
  const val = parseFloat(input.value);
  const pctSpan = input.closest('tr').querySelector('.mark-pct');
  if (!isNaN(val) && max > 0) {
    const pct = Math.round(val / max * 100);
    pctSpan.textContent = pct + '%';
    pctSpan.style.color = pct >= 70 ? '#198754' : pct >= 50 ? '#ca8a04' : '#dc3545';
  } else {
    pctSpan.textContent = '—';
    pctSpan.style.color = '#ddd';
  }
}
</script>

<?php else: ?>
<div class="alert alert-info">Select an assessment above to enter results.</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
