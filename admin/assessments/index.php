<?php
// =====================================================
// TPA IMS — Assessments
// =====================================================

$page_title   = 'Assessments';
$page_section = 'assessments';
require_once __DIR__ . '/../includes/header.php';

$db = getDB();

// Create assessment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_assessment'])) {
    verifyCsrf();
    $db->prepare('INSERT INTO assessments (batch_id, name, type, subject, date, max_marks, notes, created_by) VALUES (?,?,?,?,?,?,?,?)')
       ->execute([(int)$_POST['batch_id'], trim($_POST['name']), $_POST['type'], trim($_POST['subject']), $_POST['date'], (float)$_POST['max_marks'], trim($_POST['notes']), currentUserId()]);
    setFlash('success', 'Assessment created.');
    header('Location: index.php'); exit;
}

$isTeacher = in_array(currentRole(), ['teacher']);
$teacherId = 0;
if ($isTeacher) {
    $t = $db->prepare('SELECT id FROM teachers WHERE user_id=?');
    $t->execute([currentUserId()]); $teacherId = $t->fetchColumn();
}

$bWhere = "is_active=1";
$bParams = [];
if ($isTeacher) { $bWhere .= " AND teacher_id=?"; $bParams[] = $teacherId; }
$batches = $db->prepare("SELECT id, name FROM batches WHERE $bWhere ORDER BY name");
$batches->execute($bParams); $batches = $batches->fetchAll();

$aWhere = "1=1";
if ($isTeacher) { $aWhere .= " AND b.teacher_id=?"; }
$assessments = $db->prepare("SELECT a.*, b.name as batch_name, COUNT(ar.id) as results_count
    FROM assessments a
    JOIN batches b ON b.id = a.batch_id
    LEFT JOIN assessment_results ar ON ar.assessment_id = a.id
    WHERE $aWhere
    GROUP BY a.id ORDER BY a.date DESC LIMIT 50");
$assessments->execute($isTeacher ? [$teacherId] : []); $assessments = $assessments->fetchAll();
?>

<div class="page-header">
  <h1><i class="bi bi-clipboard-check me-2" style="color:var(--gold);"></i>Assessments &amp; Results</h1>
  <button class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#addModal">
    <i class="bi bi-plus-lg me-1"></i>New Assessment
  </button>
</div>

<div class="tpa-table table-responsive">
  <table class="table table-hover mb-0">
    <thead><tr><th>Assessment</th><th>Batch</th><th>Type</th><th>Subject</th><th>Date</th><th>Max Marks</th><th>Results</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($assessments as $a): ?>
        <tr>
          <td class="fw-600"><?= h($a['name']) ?></td>
          <td class="small"><?= h($a['batch_name']) ?></td>
          <td><span class="badge bg-secondary"><?= h(str_replace('_',' ',ucfirst($a['type']))) ?></span></td>
          <td class="small"><?= h($a['subject'] ?? '—') ?></td>
          <td class="small"><?= formatDate($a['date']) ?></td>
          <td><?= h($a['max_marks']) ?></td>
          <td><span class="badge bg-info text-dark"><?= $a['results_count'] ?> entered</span></td>
          <td><a href="results.php?assessment_id=<?= $a['id'] ?>" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size:.75rem;">Enter Results</a></td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($assessments)): ?>
        <tr><td colspan="8" class="text-center text-muted py-4">No assessments yet. Create one above.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Add Assessment Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
        <input type="hidden" name="create_assessment" value="1">
        <div class="modal-header"><h5 class="modal-title">New Assessment</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body row g-3">
          <div class="col-12">
            <label class="form-label fw-600 small">Assessment Name</label>
            <input type="text" name="name" class="form-control" required placeholder="e.g. Spring Mock Exam 2026">
          </div>
          <div class="col-sm-6">
            <label class="form-label fw-600 small">Batch</label>
            <select name="batch_id" class="form-select" required>
              <option value="">Select batch…</option>
              <?php foreach ($batches as $b): ?><option value="<?= $b['id'] ?>"><?= h($b['name']) ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="col-sm-6">
            <label class="form-label fw-600 small">Type</label>
            <select name="type" class="form-select">
              <?php foreach (['mock_exam'=>'Mock Exam','classwork'=>'Classwork','homework'=>'Homework','termly_test'=>'Termly Test','entrance_test'=>'Entrance Test'] as $v=>$l): ?>
                <option value="<?= $v ?>"><?= $l ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-sm-6">
            <label class="form-label fw-600 small">Subject</label>
            <input type="text" name="subject" class="form-control" placeholder="e.g. Maths, VR, English">
          </div>
          <div class="col-sm-3">
            <label class="form-label fw-600 small">Date</label>
            <input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>" required>
          </div>
          <div class="col-sm-3">
            <label class="form-label fw-600 small">Max Marks</label>
            <input type="number" name="max_marks" class="form-control" value="100" min="1" required>
          </div>
          <div class="col-12">
            <label class="form-label fw-600 small">Notes</label>
            <textarea name="notes" class="form-control" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-dark">Create Assessment</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
