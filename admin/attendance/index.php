<?php
$page_title = 'Attendance';
$page_section = 'attendance';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
  <h1><i class="bi bi-calendar-check me-2" style="color:var(--gold);"></i>Attendance</h1>
  <div class="d-flex gap-2">
    <a href="mark.php" class="btn btn-sm btn-dark"><i class="bi bi-pencil-square me-1"></i>Mark Register</a>
    <a href="reports.php" class="btn btn-sm btn-outline-secondary"><i class="bi bi-bar-chart me-1"></i>Reports</a>
  </div>
</div>
<?php
$db = getDB();
// Recent attendance sessions
$sessions = $db->query("SELECT a.date, a.batch_id, b.name as batch_name,
    COUNT(*) as total,
    SUM(CASE WHEN a.status='present' THEN 1 ELSE 0 END) as present,
    SUM(CASE WHEN a.status='absent' THEN 1 ELSE 0 END) as absent
    FROM attendance a JOIN batches b ON a.batch_id = b.id
    GROUP BY a.date, a.batch_id ORDER BY a.date DESC, b.name LIMIT 30")->fetchAll();
?>
<div class="tpa-table table-responsive">
  <table class="table mb-0 dt-table">
    <thead><tr><th>Date</th><th>Batch</th><th>Total</th><th>Present</th><th>Absent</th><th>Attendance %</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($sessions as $s):
        $pct = $s['total'] > 0 ? round($s['present']/$s['total']*100) : 0;
        $cls = $pct >= 80 ? 'text-success' : 'text-danger';
      ?>
        <tr>
          <td class="fw-600"><?= formatDate($s['date']) ?></td>
          <td><?= h($s['batch_name']) ?></td>
          <td><?= $s['total'] ?></td>
          <td class="text-success fw-600"><?= $s['present'] ?></td>
          <td class="text-danger fw-600"><?= $s['absent'] ?></td>
          <td><span class="fw-700 <?= $cls ?>"><?= $pct ?>%</span></td>
          <td><a href="mark.php?batch_id=<?= $s['batch_id'] ?>&date=<?= $s['date'] ?>" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:.75rem;">View/Edit</a></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
