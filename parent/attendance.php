<?php
// =====================================================
// TPA — Parent Portal: Attendance
// =====================================================
$page_title   = 'Attendance';
$page_section = 'attendance';
require_once __DIR__ . '/includes/header.php';

if (!$activeChild) { echo '<div class="alert alert-warning">No child linked.</div>'; require_once __DIR__ . '/includes/footer.php'; exit; }
$childId = $activeChild['id'];

$att = $db->prepare('SELECT SUM(status="present") as p, SUM(status="absent") as a, SUM(status="late") as l, COUNT(*) as t FROM attendance WHERE student_id=? AND date >= DATE_SUB(CURDATE(),INTERVAL 30 DAY)');
$att->execute([$childId]); $att = $att->fetch();
$attPct = $att['t'] > 0 ? round($att['p'] / $att['t'] * 100) : 0;

$records = $db->prepare("SELECT a.*, b.name as batch_name FROM attendance a JOIN batches b ON b.id=a.batch_id WHERE a.student_id=? ORDER BY a.date DESC LIMIT 50");
$records->execute([$childId]); $records = $records->fetchAll();
?>

<div class="page-header">
  <div>
    <h1><i class="bi bi-calendar-check me-2 text-success"></i>Attendance Record</h1>
    <p class="text-muted mb-0">Showing last 50 classes for <?= h($activeChild['name']) ?></p>
  </div>
  <div class="text-end">
    <div class="text-muted small fw-600 text-uppercase letter-spacing-wide">30-Day Rate</div>
    <h2 class="fw-900 mb-0" style="color:<?= $attPct>=85?'#16a34a':($attPct>=70?'#ca8a04':'#dc2626') ?>;"><?= $attPct ?>%</h2>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-4"><div class="stat-card text-center"><div class="stat-value text-success"><?= (int)$att['p'] ?></div><div class="stat-label">Present</div></div></div>
  <div class="col-4"><div class="stat-card text-center"><div class="stat-value text-warning"><?= (int)$att['l'] ?></div><div class="stat-label">Late</div></div></div>
  <div class="col-4"><div class="stat-card text-center"><div class="stat-value text-danger"><?= (int)$att['a'] ?></div><div class="stat-label">Absent</div></div></div>
</div>

<div class="tpa-table">
  <table class="table table-hover mb-0">
    <thead><tr><th>Date</th><th>Class / Batch</th><th>Status</th><th>Notes</th></tr></thead>
    <tbody>
    <?php foreach ($records as $r):
      $bg = $r['status'] === 'present' ? 'bg-success' : ($r['status'] === 'late' ? 'bg-warning text-dark' : 'bg-danger');
    ?>
      <tr>
        <td class="fw-600 small"><?= date('D d M Y', strtotime($r['date'])) ?></td>
        <td><?= h($r['batch_name']) ?></td>
        <td><span class="badge <?= $bg ?>"><?= ucfirst($r['status']) ?></span></td>
        <td class="small text-muted"><?= h($r['notes'] ?? '—') ?></td>
      </tr>
    <?php endforeach; ?>
    <?php if (empty($records)): ?><tr><td colspan="4" class="text-center py-4 text-muted">No attendance records found.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
