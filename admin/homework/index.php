<?php
// =====================================================
// TPA — Teacher / Admin Homework Manager
// =====================================================
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
startSecureSession();
requireRole(['admin','branch_manager','teacher','staff']);

$db = getDB();

// Handle Creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_hw'])) {
    verifyCsrf();
    $title     = trim($_POST['title'] ?? '');
    $desc      = trim($_POST['description'] ?? '');
    $batchId   = (int)($_POST['batch_id'] ?? 0);
    $subjectId = (int)($_POST['subject_id'] ?? 0);
    $dueDate   = $_POST['due_date'] ?? null;
    $setId     = currentUserId();

    if ($title && $batchId && $dueDate) {
        try {
            $db->prepare('INSERT INTO homework (title,description,batch_id,subject_id,set_by,due_date) VALUES (?,?,?,?,?,?)')
               ->execute([$title,$desc,$batchId,$subjectId?:null,$setId,$dueDate]);
            setFlash('success','Homework assigned successfully.');
        } catch (PDOException $e) {
            setFlash('danger','Error creating homework: ' . $e->getMessage());
        }
        header('Location: index.php'); exit;
    } else {
        setFlash('danger','Please fill all required fields.');
    }
}

// Stats & Data
$batches = $db->query('SELECT b.id, b.name, s.name as sub_name FROM batches b LEFT JOIN subjects s ON s.id=b.subject_id WHERE b.is_active=1 ORDER BY b.name')->fetchAll();

$isTeacher = isTeacher();
$teacherId = 0;
if ($isTeacher) {
    $t = $db->prepare('SELECT id FROM teachers WHERE user_id=?');
    $t->execute([currentUserId()]); $teacherId = $t->fetchColumn();
}

$hwList = [];
$hwTableMissing = false;
try {
    // Queries
    $where = "1=1";
    $params = [];
    if ($isTeacher) { $where .= " AND b.teacher_id=?"; $params[] = $teacherId; }

    $hw = $db->prepare("SELECT h.*, b.name as batch_name, s.name as subject_name, u.name as teacher_name,
        (SELECT COUNT(*) FROM batch_students bs WHERE bs.batch_id=h.batch_id AND bs.is_active=1) as expected,
        (SELECT COUNT(*) FROM homework_submissions hs WHERE hs.homework_id=h.id) as submitted
        FROM homework h JOIN batches b ON b.id=h.batch_id LEFT JOIN subjects s ON s.id=h.subject_id LEFT JOIN users u ON u.id=h.set_by
        WHERE $where ORDER BY h.due_date DESC");
    $hw->execute($params); $hwList = $hw->fetchAll();
} catch (PDOException $e) {
    $hwTableMissing = true;
}

$page_title   = 'Homework';
$page_section = 'homework';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
  <h1><i class="bi bi-pencil-square me-2" style="color:var(--gold);"></i>Homework Tracker</h1>
  <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#newHwModal"><i class="bi bi-plus-lg me-1"></i>Set Homework</button>
</div>

<!-- Modal: New HW -->
<div class="modal fade" id="newHwModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-light"><h5 class="modal-title fw-700">Set Homework</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <form method="POST">
        <div class="modal-body">
          <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
          <input type="hidden" name="create_hw" value="1">
          <div class="mb-3"><label class="form-label small fw-600">Title *</label><input type="text" name="title" class="form-control" required placeholder="e.g. Chapter 6 Exercises"></div>
          <div class="mb-3">
            <label class="form-label small fw-600">Batch *</label>
            <select name="batch_id" class="form-select" required>
              <option value="">Select Batch…</option>
              <?php foreach ($batches as $b): ?><option value="<?= $b['id'] ?>"><?= h($b['name']) ?> <?= $b['sub_name']?'('.$b['sub_name'].')':'' ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3"><label class="form-label small fw-600">Due Date *</label><input type="date" name="due_date" class="form-control" required min="<?= date('Y-m-d') ?>"></div>
          <div class="mb-3"><label class="form-label small fw-600">Instructions / Description</label><textarea name="description" class="form-control" rows="3"></textarea></div>
        </div>
        <div class="modal-footer bg-light"><button type="submit" class="btn btn-dark w-100"><i class="bi bi-send me-1"></i>Assign to Batch</button></div>
      </form>
    </div>
  </div>
</div>

<?php if ($hwTableMissing): ?>
  <div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle me-2"></i>
    <strong>Database setup required.</strong> The <code>homework</code> table has not been created yet.
    Please run the SQL migration to create the homework table.
  </div>
<?php else: ?>
<div class="tpa-table">
  <table class="table table-hover mb-0">
    <thead><tr><th>Homework</th><th>Batch</th><th>Due Date</th><th>Submission</th><th>Teacher</th></tr></thead>
    <tbody>
    <?php foreach ($hwList as $h):
      $isLate = strtotime($h['due_date']) < strtotime('today');
      $isDone = $h['submitted'] >= $h['expected'] && $h['expected'] > 0;
    ?>
      <tr>
        <td>
          <div class="fw-700"><?= h($h['title']) ?></div>
          <div class="small text-muted text-truncate" style="max-width:250px;"><?= h($h['description']??'') ?></div>
        </td>
        <td>
          <div class="small fw-600"><?= h($h['batch_name']) ?></div>
          <?php if (!empty($h['subject_name'])): ?><div class="small text-muted"><?= h($h['subject_name']) ?></div><?php endif; ?>
        </td>
        <td>
          <div class="small <?= $isLate&&!$isDone?'text-danger fw-700':'' ?>"><?= date('D d M Y', strtotime($h['due_date'])) ?></div>
          <?php if ($isLate&&!$isDone): ?><span class="badge bg-danger" style="font-size:.65rem;">Overdue</span><?php endif; ?>
        </td>
        <td>
          <div class="d-flex align-items-center gap-2">
            <div class="progress flex-grow-1" style="height:6px;max-width:80px;"><div class="progress-bar <?= $isDone?'bg-success':'bg-warning' ?>" style="width:<?= $h['expected']>0?round($h['submitted']/$h['expected']*100):0 ?>%"></div></div>
            <span class="small fw-700"><?= $h['submitted'] ?>/<?= $h['expected'] ?></span>
          </div>
        </td>
        <td class="small text-muted"><?= h($h['teacher_name']) ?></td>
      </tr>
    <?php endforeach; ?>
    <?php if (empty($hwList)): ?><tr><td colspan="5" class="text-center text-muted py-4">No homework assigned yet.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
