<?php
// =====================================================
// TPA IMS — Mark Attendance Register
// =====================================================

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
startSecureSession();
requireLogin(SITE_URL . '/login.php');

$db      = getDB();
$batchId = (int)($_GET['batch_id'] ?? 0);
$date    = $_GET['date'] ?? date('Y-m-d');
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) $date = date('Y-m-d');

$batches = $db->query('SELECT b.id, b.name, b.day_of_week FROM batches b WHERE b.is_active = 1 ORDER BY b.name')->fetchAll();

// Save attendance — redirect BEFORE header outputs HTML
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_attendance'])) {
    verifyCsrf();
    $bId     = (int)$_POST['batch_id'];
    $regDate = $_POST['date'];
    $rows    = $_POST['attendance'] ?? [];

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $regDate)) { setFlash('danger','Invalid date.'); header('Location: mark.php?batch_id='.$bId.'&date='.$date); exit; }

    $stmt       = $db->prepare('INSERT INTO attendance (batch_id, student_id, date, status, notes, marked_by) VALUES (?,?,?,?,?,?)
                          ON DUPLICATE KEY UPDATE status = VALUES(status), notes = VALUES(notes), marked_by = VALUES(marked_by)');
    $notifyStmt = $db->prepare('UPDATE attendance SET parent_notified=1, notified_at=NOW() WHERE batch_id=? AND student_id=? AND date=?');

    require_once __DIR__ . '/../includes/WhatsAppService.php';
    require_once __DIR__ . '/../includes/EmailService.php';

    $batchName = $db->prepare('SELECT name FROM batches WHERE id = ?');
    $batchName->execute([$bId]);
    $batchName = $batchName->fetchColumn();

    foreach ($rows as $studentId => $data) {
        $status = $data['status'] ?? 'present';
        $notes  = trim($data['notes'] ?? '');
        $stmt->execute([$bId, $studentId, $regDate, $status, $notes, currentUserId()]);

        if (in_array($status, ['absent','late'])) {
            // Only notify once per student per session — check if already notified
            $alreadyNotified = $db->prepare('SELECT parent_notified FROM attendance WHERE batch_id=? AND student_id=? AND date=? LIMIT 1');
            $alreadyNotified->execute([$bId, $studentId, $regDate]);
            $prevRow = $alreadyNotified->fetch();
            if (!($prevRow['parent_notified'] ?? false)) {
                $parent = $db->prepare('SELECT sp.*, CONCAT(s.first_name," ",s.last_name) as student_name FROM student_parents sp JOIN students s ON s.id = sp.student_id WHERE sp.student_id = ? AND sp.is_primary = 1 LIMIT 1');
                $parent->execute([$studentId]);
                $parent = $parent->fetch();
                if ($parent) {
                    $siteName = getSetting('site_name', 'Academy');
                    $tpl = getSetting('wa_template_absence', '');
                    $msg = $tpl
                        ? str_replace(['{parent_name}','{child_name}','{batch_name}','{date}','{status}'],
                            [$parent['parent_name'],$parent['student_name'],$batchName,date('D j M Y',strtotime($regDate)),$status], $tpl)
                        : "Hi {$parent['parent_name']}, {$parent['student_name']} was marked *{$status}* for {$batchName} on " . date('D j M Y',strtotime($regDate)) . ". Contact us on " . getSetting('site_phone','') . " — {$siteName}";
                    $wa = new WhatsAppService();
                    $wa->sendText($parent['whatsapp'] ?: $parent['phone'], $msg, $parent['id']);
                    EmailService::sendAbsenceNotification($parent['email'], $parent['parent_name'], $parent['student_name'], $batchName, date('D j M Y',strtotime($regDate)), $parent['id']);
                    $notifyStmt->execute([$bId, $studentId, $regDate]);
                }
            }
        }
    }

    logActivity('attendance_saved', "Attendance saved for batch #$bId on $regDate");
    setFlash('success', 'Attendance saved successfully.');
    header('Location: mark.php?batch_id=' . $bId . '&date=' . $regDate);
    exit;
}

// Now safe to output HTML
$page_title   = 'Mark Attendance';
$page_section = 'attendance';
require_once __DIR__ . '/../includes/header.php';

$students = [];
$batch    = null;
$existing = [];

if ($batchId) {
    $batchRow = $db->prepare('SELECT * FROM batches WHERE id = ?');
    $batchRow->execute([$batchId]);
    $batch = $batchRow->fetch();

    if ($batch) {
        $students = $db->prepare('SELECT s.id, s.first_name, s.last_name, s.year_group FROM students s
            JOIN batch_students bs ON bs.student_id = s.id
            WHERE bs.batch_id = ? AND bs.is_active = 1 AND s.status = "active"
            ORDER BY s.first_name');
        $students->execute([$batchId]);
        $students = $students->fetchAll();

        $existingRows = $db->prepare('SELECT student_id, status, notes FROM attendance WHERE batch_id = ? AND date = ?');
        $existingRows->execute([$batchId, $date]);
        foreach ($existingRows->fetchAll() as $r) $existing[$r['student_id']] = $r;
    }
}
?>

<div class="page-header">
  <h1><i class="bi bi-calendar-check me-2" style="color:var(--gold);"></i>Mark Attendance</h1>
  <a href="index.php" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<!-- Batch + Date selector -->
<div class="stat-card mb-4">
  <form method="GET" class="row g-3 align-items-end">
    <div class="col-sm-5">
      <label class="form-label fw-600 small">Select Batch</label>
      <select name="batch_id" class="form-select" required onchange="this.form.submit()">
        <option value="">Choose a batch…</option>
        <?php foreach ($batches as $b): ?>
          <option value="<?= $b['id'] ?>" <?= $batchId==$b['id']?'selected':'' ?>>
            <?= h($b['name']) ?> (<?= h($b['day_of_week']) ?>)
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-sm-3">
      <label class="form-label fw-600 small">Date</label>
      <input type="date" name="date" class="form-control" value="<?= h($date) ?>" max="<?= date('Y-m-d') ?>" onchange="this.form.submit()">
    </div>
    <?php if ($batchId): ?>
      <div class="col-sm-auto">
        <button type="submit" class="btn btn-outline-secondary btn-sm">Refresh</button>
      </div>
    <?php endif; ?>
  </form>
</div>

<?php if ($batchId && $batch): ?>

<!-- Quick marks toolbar -->
<div class="d-flex gap-2 mb-3 flex-wrap">
  <button class="btn btn-sm btn-success" onclick="markAll('present')"><i class="bi bi-check-all me-1"></i>All Present</button>
  <button class="btn btn-sm btn-outline-danger" onclick="markAll('absent')"><i class="bi bi-x-lg me-1"></i>All Absent</button>
  <span class="text-muted small align-self-center ms-auto"><?= count($students) ?> students · <?= date('D j F Y', strtotime($date)) ?></span>
</div>

<form method="POST">
  <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
  <input type="hidden" name="save_attendance" value="1">
  <input type="hidden" name="batch_id" value="<?= $batchId ?>">
  <input type="hidden" name="date" value="<?= h($date) ?>">

  <div class="tpa-table table-responsive mb-4">
    <table class="table mb-0">
      <thead><tr><th>#</th><th>Student</th><th>Year</th><th>Status</th><th>Notes</th></tr></thead>
      <tbody>
        <?php foreach ($students as $i => $s):
          $cur = $existing[$s['id']]['status'] ?? 'present';
          $curNotes = $existing[$s['id']]['notes'] ?? '';
        ?>
          <tr id="row-<?= $s['id'] ?>">
            <td class="text-muted small"><?= $i+1 ?></td>
            <td class="fw-600"><?= h($s['first_name'] . ' ' . $s['last_name']) ?></td>
            <td><span class="badge bg-light text-dark border"><?= h($s['year_group'] ?? '') ?></span></td>
            <td>
              <div class="btn-group btn-group-sm att-group" data-id="<?= $s['id'] ?>">
                <?php foreach (['present'=>['success','P'],'absent'=>['danger','A'],'late'=>['warning','L'],'excused'=>['info','E']] as $st=>[$cls,$lbl]): ?>
                  <input type="radio" class="btn-check att-radio" name="attendance[<?= $s['id'] ?>][status]" id="att-<?= $s['id'] ?>-<?= $st ?>" value="<?= $st ?>" autocomplete="off" <?= $cur===$st?'checked':'' ?>>
                  <label class="btn btn-outline-<?= $cls ?>" for="att-<?= $s['id'] ?>-<?= $st ?>" title="<?= ucfirst($st) ?>"><?= $lbl ?></label>
                <?php endforeach; ?>
              </div>
            </td>
            <td><input type="text" name="attendance[<?= $s['id'] ?>][notes]" class="form-control form-control-sm" placeholder="Optional note" value="<?= h($curNotes) ?>" style="min-width:160px;"></td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($students)): ?>
          <tr><td colspan="5" class="text-center text-muted py-4">No active students in this batch.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if (!empty($students)): ?>
    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i>Save Register</button>
      <span class="text-muted small align-self-center">Absent/Late students will be automatically notified by WhatsApp &amp; email.</span>
    </div>
  <?php endif; ?>
</form>

<?php elseif (!$batchId): ?>
  <div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>Please select a batch and date above to mark the register.</div>
<?php endif; ?>

<?php
$extra_js = <<<'JS'
<script>
function markAll(status) {
  document.querySelectorAll('.att-radio[value="' + status + '"]').forEach(r => r.checked = true);
}
</script>
JS;
require_once __DIR__ . '/../includes/footer.php';
?>
