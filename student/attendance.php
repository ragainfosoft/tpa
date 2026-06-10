<?php
// TPA — Student Attendance
require_once __DIR__ . '/includes/header.php';

$records = $db->prepare("SELECT a.date, a.status, b.name as batch_name FROM attendance a JOIN batches b ON b.id=a.batch_id WHERE a.student_id=? ORDER BY a.date DESC LIMIT 30");
$records->execute([$studentId]); $records = $records->fetchAll();
?>

<div class="page-header"><h1><i class="bi bi-calendar-check me-2 text-primary"></i>My Attendance</h1></div>
<div class="tpa-table">
  <table class="table">
    <thead><tr><th>Date</th><th>Batch</th><th>Status</th></tr></thead>
    <tbody>
      <?php foreach ($records as $r): ?>
      <tr>
        <td class="fw-600"><?= date('D, d M Y', strtotime($r['date'])) ?></td>
        <td class="small"><?= h($r['batch_name']) ?></td>
        <td>
          <span class="badge w-75 <?= $r['status']==='present'?'bg-success':($r['status']==='absent'?'bg-danger':'bg-warning') ?>"><?= ucfirst($r['status']) ?></span>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($records)): ?><tr><td colspan="3" class="text-center py-4 text-muted">No attendance records found.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
