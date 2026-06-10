<?php
// =====================================================
// TPA IMS — Reports
// =====================================================

$page_title   = 'Reports';
$page_section = 'reports';
require_once __DIR__ . '/../includes/header.php';

$db = getDB();

// Key metrics
$s = $db->query("SELECT
    (SELECT COUNT(*) FROM students WHERE status='active') as active_students,
    (SELECT COUNT(*) FROM leads WHERE status NOT IN ('enrolled','lost')) as open_leads,
    (SELECT COUNT(*) FROM batches WHERE is_active=1) as active_batches,
    (SELECT COALESCE(SUM(amount_due),0) FROM invoices WHERE status='overdue') as overdue_amount,
    (SELECT COALESCE(SUM(amount_due),0) FROM invoices WHERE status='paid' AND MONTH(created_at)=MONTH(NOW()) AND YEAR(created_at)=YEAR(NOW())) as collected_month,
    (SELECT COALESCE(SUM(amount_due),0) FROM invoices WHERE status='unpaid') as outstanding
")->fetch();

// Attendance summary last 30 days
$att = $db->query("SELECT
    COUNT(*) as total,
    SUM(status='present') as present,
    SUM(status='absent') as absent,
    SUM(status='late') as late,
    SUM(status='excused') as excused
    FROM attendance WHERE date >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetch();

// Fee collection by month (last 6 months)
$monthly = $db->query("SELECT DATE_FORMAT(created_at,'%b %Y') as month, SUM(amount_due) as total
    FROM invoices WHERE status='paid' AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(created_at,'%Y-%m') ORDER BY MIN(created_at)")->fetchAll();

// Top overdue students
$overdue = $db->query("SELECT CONCAT(s.first_name,' ',s.last_name) as student, COUNT(i.id) as count, SUM(i.amount_due) as total
    FROM invoices i JOIN students s ON s.id=i.student_id WHERE i.status='overdue'
    GROUP BY i.student_id ORDER BY total DESC LIMIT 10")->fetchAll();
?>

<div class="page-header">
  <h1><i class="bi bi-bar-chart me-2" style="color:var(--gold);"></i>Reports &amp; Analytics</h1>
</div>

<!-- KPI Row -->
<div class="row g-3 mb-4">
  <?php
  $kpis = [
    ['Active Students', $s['active_students'], 'people', '#e3f2fd', '#1565c0'],
    ['Open Leads', $s['open_leads'], 'person-plus', '#f3e8ff', '#6a1b9a'],
    ['Active Batches', $s['active_batches'], 'collection', '#e8f5e9', '#2e7d32'],
    ['Collected (This Month)', '£'.number_format($s['collected_month'],2), 'currency-pound', '#e8f5e9', '#2e7d32'],
    ['Outstanding', '£'.number_format($s['outstanding'],2), 'hourglass-split', '#fff3e0', '#e65100'],
    ['Overdue', '£'.number_format($s['overdue_amount'],2), 'exclamation-circle', '#fce4ec', '#c62828'],
  ];
  foreach ($kpis as [$label, $val, $icon, $bg, $color]):
  ?>
    <div class="col-6 col-lg-2">
      <div class="stat-card d-flex align-items-center gap-3 h-100">
        <div class="stat-icon" style="background:<?= $bg ?>;color:<?= $color ?>;"><i class="bi bi-<?= $icon ?>"></i></div>
        <div><div class="stat-label"><?= $label ?></div><div class="stat-value" style="font-size:1.3rem;"><?= $val ?></div></div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<div class="row g-4">

  <!-- Attendance Summary -->
  <div class="col-lg-4">
    <div class="stat-card h-100">
      <h6 class="fw-700 mb-4 text-uppercase" style="font-size:.7rem;letter-spacing:.1em;color:#888;">Attendance — Last 30 Days</h6>
      <?php if ($att['total'] > 0):
        $pct = fn($n) => round($n / $att['total'] * 100);
      ?>
        <?php foreach ([['Present','present','success'],['Absent','absent','danger'],['Late','late','warning'],['Excused','excused','info']] as [$l,$k,$c]): ?>
          <div class="mb-3">
            <div class="d-flex justify-content-between mb-1 small fw-600">
              <span><?= $l ?></span>
              <span><?= $att[$k] ?> (<?= $pct($att[$k]) ?>%)</span>
            </div>
            <div class="progress" style="height:8px;">
              <div class="progress-bar bg-<?= $c ?>" style="width:<?= $pct($att[$k]) ?>%"></div>
            </div>
          </div>
        <?php endforeach; ?>
        <p class="text-muted small mb-0 mt-3"><?= $att['total'] ?> sessions recorded in the last 30 days.</p>
      <?php else: ?>
        <p class="text-muted small">No attendance data yet.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Monthly Fee Collection -->
  <div class="col-lg-4">
    <div class="stat-card h-100">
      <h6 class="fw-700 mb-4 text-uppercase" style="font-size:.7rem;letter-spacing:.1em;color:#888;">Fee Collection — Last 6 Months</h6>
      <?php if (!empty($monthly)):
        $max = max(array_column($monthly, 'total')) ?: 1;
      ?>
        <?php foreach ($monthly as $m): ?>
          <div class="mb-3">
            <div class="d-flex justify-content-between mb-1 small fw-600">
              <span><?= h($m['month']) ?></span>
              <span><?= formatMoney($m['total']) ?></span>
            </div>
            <div class="progress" style="height:8px;">
              <div class="progress-bar" style="background:var(--gold);width:<?= round($m['total']/$max*100) ?>%"></div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-muted small">No payment data yet.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Overdue Students -->
  <div class="col-lg-4">
    <div class="stat-card h-100">
      <h6 class="fw-700 mb-4 text-uppercase" style="font-size:.7rem;letter-spacing:.1em;color:#888;">Top Overdue Students</h6>
      <?php if (!empty($overdue)): ?>
        <div class="table-responsive">
          <table class="table table-sm mb-0">
            <thead><tr><th>Student</th><th class="text-end">Overdue</th></tr></thead>
            <tbody>
              <?php foreach ($overdue as $o): ?>
                <tr>
                  <td class="small fw-600"><?= h($o['student']) ?></td>
                  <td class="small text-end text-danger fw-700"><?= formatMoney($o['total']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p class="text-muted small">No overdue invoices. 🎉</p>
      <?php endif; ?>
    </div>
  </div>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
