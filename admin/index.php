<?php
// =====================================================
// TPA IMS — Admin Dashboard
// =====================================================

$page_title   = 'Dashboard';
$page_section = 'dashboard';

require_once __DIR__ . '/includes/header.php';

$db = getDB();

// ── Stats ────────────────────────────────────────────

$totalStudents  = $db->query('SELECT COUNT(*) FROM students WHERE status = "active"')->fetchColumn();
$totalLeads     = $db->query('SELECT COUNT(*) FROM leads WHERE status NOT IN ("enrolled","lost")')->fetchColumn();
$overdueInvoices= $db->query('SELECT COUNT(*) FROM invoices WHERE status = "overdue"')->fetchColumn();
$overdueAmount  = $db->query('SELECT COALESCE(SUM(amount_due),0) FROM invoices WHERE status = "overdue"')->fetchColumn();

// Fees collected this month
$feeThisMonth   = $db->query('SELECT COALESCE(SUM(amount),0) FROM payments WHERE YEAR(payment_date)=YEAR(NOW()) AND MONTH(payment_date)=MONTH(NOW())')->fetchColumn();

// Today's attendance
$todayTotal     = $db->query('SELECT COUNT(*) FROM attendance WHERE date = CURDATE()')->fetchColumn();
$todayPresent   = $db->query('SELECT COUNT(*) FROM attendance WHERE date = CURDATE() AND status = "present"')->fetchColumn();
$attendancePct  = $todayTotal > 0 ? round($todayPresent / $todayTotal * 100) : 0;

// ── Monthly fee chart (last 6 months) ────────────────

$chartData = $db->query("SELECT DATE_FORMAT(payment_date,'%b %Y') as month, SUM(amount) as total
    FROM payments WHERE payment_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY YEAR(payment_date), MONTH(payment_date) ORDER BY payment_date ASC")->fetchAll();

$chartLabels = array_column($chartData, 'month');
$chartValues = array_column($chartData, 'total');

// ── Lead pipeline counts ──────────────────────────────

$pipelineData = $db->query("SELECT status, COUNT(*) as cnt FROM leads GROUP BY status")->fetchAll();
$pipeline = array_column($pipelineData, 'cnt', 'status');

// ── Upcoming follow-ups ───────────────────────────────

$followups = $db->query("SELECT l.id, l.name, l.child_year, l.course_interest, l.next_followup_date, l.status,
    u.name as assigned_name
    FROM leads l LEFT JOIN users u ON l.assigned_to = u.id
    WHERE l.next_followup_date >= CURDATE() AND l.status NOT IN ('enrolled','lost')
    ORDER BY l.next_followup_date ASC LIMIT 8")->fetchAll();

// ── Overdue invoices ──────────────────────────────────

$overdueList = $db->query("SELECT i.invoice_number, i.amount_due, i.due_date,
    CONCAT(s.first_name,' ',s.last_name) as student_name
    FROM invoices i JOIN students s ON i.student_id = s.id
    WHERE i.status = 'overdue' ORDER BY i.due_date ASC LIMIT 8")->fetchAll();

// ── Recent lead activity ──────────────────────────────

$recentLeads = $db->query("SELECT * FROM leads ORDER BY created_at DESC LIMIT 6")->fetchAll();

// ── Today's batches ───────────────────────────────────

$dayName = date('l'); // e.g. Monday
$todayBatches = $db->prepare("SELECT b.*, u.name as teacher_name,
    (SELECT COUNT(*) FROM batch_students bs WHERE bs.batch_id = b.id AND bs.is_active = 1) as enrolled
    FROM batches b LEFT JOIN teachers t ON b.teacher_id = t.id LEFT JOIN users u ON t.user_id = u.id
    WHERE b.is_active = 1 AND FIND_IN_SET(?, REPLACE(b.day_of_week,' ',''))
    ORDER BY b.start_time");
$todayBatches->execute([$dayName]);
$todayBatches = $todayBatches->fetchAll();
?>

<!-- ── Page header ── -->
<div class="page-header">
  <div>
    <h1><i class="bi bi-speedometer2 me-2" style="color:var(--gold);"></i>Dashboard</h1>
    <p class="text-muted mb-0 small"><?= date('l, d F Y') ?> &middot; <?= getSetting('site_name') ?></p>
  </div>
  <div class="d-flex gap-2">
    <a href="leads/add.php" class="btn btn-sm btn-dark"><i class="bi bi-plus-lg me-1"></i>New Lead</a>
    <a href="students/add.php" class="btn btn-sm" style="background:var(--gold);color:var(--navy);font-weight:700;"><i class="bi bi-person-plus me-1"></i>Enrol Student</a>
  </div>
</div>

<!-- ── Stat Cards ── -->
<div class="row g-3 mb-4">

  <div class="col-6 col-xl-3">
    <div class="stat-card d-flex align-items-center gap-3">
      <div class="stat-icon" style="background:#e8f5e9;color:#2e7d32;"><i class="bi bi-people-fill"></i></div>
      <div>
        <div class="stat-label">Active Students</div>
        <div class="stat-value"><?= number_format($totalStudents) ?></div>
      </div>
    </div>
  </div>

  <div class="col-6 col-xl-3">
    <div class="stat-card d-flex align-items-center gap-3">
      <div class="stat-icon" style="background:#fff3e0;color:#e65100;"><i class="bi bi-funnel-fill"></i></div>
      <div>
        <div class="stat-label">Active Leads</div>
        <div class="stat-value"><?= number_format($totalLeads) ?></div>
      </div>
    </div>
  </div>

  <div class="col-6 col-xl-3">
    <div class="stat-card d-flex align-items-center gap-3">
      <div class="stat-icon" style="background:#e3f2fd;color:#1565c0;"><i class="bi bi-currency-pound"></i></div>
      <div>
        <div class="stat-label">Fees This Month</div>
        <div class="stat-value"><?= formatMoney($feeThisMonth) ?></div>
      </div>
    </div>
  </div>

  <div class="col-6 col-xl-3">
    <div class="stat-card d-flex align-items-center gap-3">
      <div class="stat-icon" style="background:#fce4ec;color:#c62828;"><i class="bi bi-exclamation-circle-fill"></i></div>
      <div>
        <div class="stat-label">Overdue Invoices</div>
        <div class="stat-value"><?= number_format($overdueInvoices) ?></div>
        <div class="small text-danger fw-600"><?= formatMoney($overdueAmount) ?></div>
      </div>
    </div>
  </div>

</div>

<!-- ── Charts Row ── -->
<div class="row g-3 mb-4">

  <!-- Monthly Fee Trend -->
  <div class="col-lg-8">
    <div class="stat-card h-100">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="fw-700" style="color:var(--navy);">Monthly Fee Collection</div>
        <a href="reports/index.php" class="btn btn-sm btn-outline-secondary">View Report</a>
      </div>
      <canvas id="feeChart" height="90"></canvas>
    </div>
  </div>

  <!-- Lead Pipeline Pie -->
  <div class="col-lg-4">
    <div class="stat-card h-100">
      <div class="fw-700 mb-3" style="color:var(--navy);">Lead Pipeline</div>
      <canvas id="leadPie" height="160"></canvas>
      <div class="mt-3 small">
        <?php
        $statusColors = ['new'=>'#17a2b8','contacted'=>'#0d6efd','follow_up'=>'#ffc107','assessment_booked'=>'#7c3aed','enrolled'=>'#198754','lost'=>'#6c757d'];
        foreach ($pipeline as $s => $c): $col = $statusColors[$s] ?? '#999'; ?>
          <span class="me-2"><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:<?= $col ?>;"></span>
          <?= h(str_replace('_',' ',ucfirst($s))) ?> (<?= $c ?>)</span>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

</div>

<!-- ── Attendance + Today's Classes ── -->
<div class="row g-3 mb-4">

  <!-- Today's attendance meter -->
  <div class="col-lg-4">
    <div class="stat-card h-100">
      <div class="fw-700 mb-3" style="color:var(--navy);">Today's Attendance</div>
      <div class="text-center my-3">
        <div style="position:relative;display:inline-block;">
          <canvas id="attendanceDonut" width="140" height="140"></canvas>
          <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);font-size:1.6rem;font-weight:900;color:var(--navy);"><?= $attendancePct ?>%</div>
        </div>
      </div>
      <div class="d-flex justify-content-around text-center small mt-2">
        <div><div class="fw-800 text-success fs-5"><?= $todayPresent ?></div><div class="text-muted">Present</div></div>
        <div><div class="fw-800 text-danger fs-5"><?= $todayTotal - $todayPresent ?></div><div class="text-muted">Absent/Late</div></div>
        <div><div class="fw-800 fs-5" style="color:var(--navy);"><?= $todayTotal ?></div><div class="text-muted">Total</div></div>
      </div>
    </div>
  </div>

  <!-- Today's classes -->
  <div class="col-lg-8">
    <div class="stat-card h-100">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="fw-700" style="color:var(--navy);">Today's Classes (<?= $dayName ?>)</div>
        <a href="attendance/index.php" class="btn btn-sm btn-outline-secondary">Mark Attendance</a>
      </div>
      <?php if (empty($todayBatches)): ?>
        <p class="text-muted small">No classes scheduled for today.</p>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-sm mb-0">
            <thead style="background:#f8f9fa;"><tr><th>Batch</th><th>Time</th><th>Teacher</th><th>Students</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($todayBatches as $b): ?>
              <tr>
                <td class="fw-600"><?= h($b['name']) ?></td>
                <td><?= date('g:ia', strtotime($b['start_time'])) ?> – <?= date('g:ia', strtotime($b['end_time'])) ?></td>
                <td><?= h($b['teacher_name'] ?? '—') ?></td>
                <td><?= $b['enrolled'] ?>/<?= $b['max_capacity'] ?></td>
                <td><a href="attendance/mark.php?batch_id=<?= $b['id'] ?>&date=<?= date('Y-m-d') ?>" class="btn btn-xs btn-sm btn-success py-0 px-2" style="font-size:.75rem;">Mark</a></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>

</div>

<!-- ── Follow-ups & Overdue ── -->
<div class="row g-3 mb-4">

  <!-- Upcoming follow-ups -->
  <div class="col-lg-6">
    <div class="stat-card h-100">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="fw-700" style="color:var(--navy);">Upcoming Follow-Ups</div>
        <a href="leads/index.php" class="btn btn-sm btn-outline-secondary">View All Leads</a>
      </div>
      <?php if (empty($followups)): ?>
        <p class="text-muted small">No follow-ups scheduled. 🎉</p>
      <?php else: ?>
        <?php foreach ($followups as $fu):
          $isToday    = $fu['next_followup_date'] === date('Y-m-d');
          $isOverdue  = $fu['next_followup_date'] < date('Y-m-d');
          $cls        = $isOverdue ? 'border-danger' : ($isToday ? 'border-warning' : 'border-light');
        ?>
          <div class="d-flex align-items-center justify-content-between py-2 border-bottom <?= $cls ?>" style="border-left: 3px solid; padding-left: 8px;">
            <div>
              <div class="fw-600 small"><?= h($fu['name']) ?> <span class="text-muted fw-400">· <?= h($fu['child_year'] ?? '') ?></span></div>
              <div class="text-muted" style="font-size:.75rem;"><?= h($fu['course_interest'] ?? '') ?></div>
            </div>
            <div class="text-end">
              <div class="small <?= $isOverdue ? 'text-danger fw-700' : ($isToday ? 'text-warning fw-700' : 'text-muted') ?>">
                <?= $isToday ? 'Today' : ($isOverdue ? 'Overdue' : formatDate($fu['next_followup_date'])) ?>
              </div>
              <a href="leads/view.php?id=<?= $fu['id'] ?>" class="btn btn-xs btn-sm btn-outline-primary py-0 px-2" style="font-size:.7rem;">View</a>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- Overdue invoices -->
  <div class="col-lg-6">
    <div class="stat-card h-100">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="fw-700" style="color:var(--navy);">Overdue Fees</div>
        <a href="fees/reminders.php" class="btn btn-sm btn-outline-danger"><i class="bi bi-bell me-1"></i>Send Reminders</a>
      </div>
      <?php if (empty($overdueList)): ?>
        <p class="text-muted small">No overdue invoices. 🎉</p>
      <?php else: ?>
        <?php foreach ($overdueList as $inv): $days = (int)((time() - strtotime($inv['due_date'])) / 86400); ?>
          <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
            <div>
              <div class="fw-600 small"><?= h($inv['student_name']) ?></div>
              <div class="text-muted" style="font-size:.75rem;"><?= h($inv['invoice_number']) ?> · <?= $days ?> days overdue</div>
            </div>
            <div class="text-end">
              <div class="fw-700 text-danger small"><?= formatMoney($inv['amount_due']) ?></div>
              <a href="fees/invoices.php?student=<?= urlencode($inv['student_name']) ?>" class="btn btn-xs btn-sm btn-outline-secondary py-0 px-2" style="font-size:.7rem;">View</a>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

</div>

<!-- ── Recent Leads ── -->
<div class="stat-card mb-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="fw-700" style="color:var(--navy);">Recent Leads</div>
    <a href="leads/add.php" class="btn btn-sm btn-dark"><i class="bi bi-plus-lg me-1"></i>Add Lead</a>
  </div>
  <div class="table-responsive">
    <table class="table table-hover mb-0 tpa-table">
      <thead><tr><th>Name</th><th>Child / Year</th><th>Course</th><th>Source</th><th>Status</th><th>Added</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($recentLeads as $l): ?>
          <tr>
            <td class="fw-600"><?= h($l['name']) ?><br><small class="text-muted"><?= h($l['phone'] ?? '') ?></small></td>
            <td><?= h($l['child_name'] ?? '—') ?> <span class="badge bg-light text-dark border"><?= h($l['child_year'] ?? '') ?></span></td>
            <td><?= h($l['course_interest'] ?? '—') ?></td>
            <td><?= h($l['source'] ?? '—') ?></td>
            <td><?= leadStatusBadge($l['status']) ?></td>
            <td class="text-muted small"><?= timeAgo($l['created_at']) ?></td>
            <td>
              <a href="leads/view.php?id=<?= $l['id'] ?>" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:.75rem;">View</a>
              <?php if ($l['whatsapp'] ?? $l['phone']): ?>
                <a href="<?= h(waLink($l['whatsapp'] ?: $l['phone'])) ?>" target="_blank" class="btn btn-sm btn-success py-0 px-2" style="font-size:.75rem;"><i class="bi bi-whatsapp"></i></a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$chartLabelsJson = json_encode($chartLabels);
$chartValuesJson = json_encode(array_map('floatval', $chartValues));
$pipelineLabels  = json_encode(array_map(fn($s) => str_replace('_',' ',ucfirst($s)), array_keys($pipeline)));
$pipelineValues  = json_encode(array_values($pipeline));
$pipelineColours = json_encode(array_values(array_intersect_key($statusColors, $pipeline)));

$extra_js = <<<JS
<script>
// Fee collection bar chart
new Chart(document.getElementById('feeChart'), {
  type: 'bar',
  data: { labels: {$chartLabelsJson}, datasets:[{ label:'Fees (£)', data: {$chartValuesJson},
    backgroundColor:'rgba(10,22,40,.8)', borderRadius:6 }]},
  options:{ responsive:true, plugins:{legend:{display:false}}, scales:{ y:{ ticks:{ callback:v=>'£'+v }}}}
});
// Lead pipeline pie
new Chart(document.getElementById('leadPie'), {
  type: 'doughnut',
  data: { labels: {$pipelineLabels}, datasets:[{ data: {$pipelineValues}, backgroundColor: {$pipelineColours}, borderWidth:2 }]},
  options:{ responsive:true, plugins:{legend:{display:false}}, cutout:'65%' }
});
// Attendance donut
new Chart(document.getElementById('attendanceDonut'), {
  type:'doughnut',
  data:{ datasets:[{ data:[{$todayPresent},{$todayTotal}-{$todayPresent}],
    backgroundColor:['#198754','#dee2e6'], borderWidth:0 }]},
  options:{ responsive:false, cutout:'72%', plugins:{tooltip:{enabled:false}} }
});
</script>
JS;

require_once __DIR__ . '/includes/footer.php';
?>
