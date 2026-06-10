<?php
// =====================================================
// TPA — Parent Dashboard
// =====================================================
$page_title   = 'Parent Dashboard';
$page_section = 'dashboard';
require_once __DIR__ . '/includes/header.php';

if (!$activeChild) {
    echo '<div class="alert alert-warning">No children linked to this account. Please contact administrator.</div>';
    require_once __DIR__ . '/includes/footer.php'; exit;
}
$childId = $activeChild['id'];

// Attendance (last 30 days)
$att = $db->prepare('SELECT SUM(status="present") as p, SUM(status="absent") as a, COUNT(*) as t FROM attendance WHERE student_id=? AND date >= DATE_SUB(CURDATE(),INTERVAL 30 DAY)');
$att->execute([$childId]); $att = $att->fetch();
$attPct = $att['t'] > 0 ? round($att['p'] / $att['t'] * 100) : 0;

// Fees Overview
$feesSummary = $db->prepare('SELECT SUM(CASE WHEN status IN ("unpaid","overdue") THEN amount_due ELSE 0 END) as outstanding, SUM(CASE WHEN status="paid" THEN amount_due ELSE 0 END) as paid FROM invoices WHERE student_id=?');
$feesSummary->execute([$childId]); $feesSummary = $feesSummary->fetch();

// Recent invoices
$recentInvoices = $db->prepare('SELECT * FROM invoices WHERE student_id=? ORDER BY due_date DESC LIMIT 5');
$recentInvoices->execute([$childId]); $recentInvoices = $recentInvoices->fetchAll();

// Recent results
$results = $db->prepare("SELECT ar.*, a.name as assessment_name, a.subject, a.date FROM assessment_results ar JOIN assessments a ON a.id=ar.assessment_id WHERE ar.student_id=? ORDER BY a.date DESC LIMIT 5");
$results->execute([$childId]); $results = $results->fetchAll();

// Recent attendance absences
$absences = $db->prepare("SELECT a.*, b.name as batch_name FROM attendance a JOIN batches b ON b.id=a.batch_id WHERE a.student_id=? AND a.status IN ('absent','late') ORDER BY a.date DESC LIMIT 10");
$absences->execute([$childId]); $absences = $absences->fetchAll();

// Batches enrolled
$batches = $db->prepare("SELECT b.name, b.day_of_week, b.start_time, b.end_time, b.centre FROM batches b JOIN batch_students bs ON bs.batch_id=b.id WHERE bs.student_id=? AND bs.is_active=1");
$batches->execute([$childId]); $batches = $batches->fetchAll();
?>

<!-- Child hero card -->
<div class="mb-4 p-4 rounded-3 text-white" style="background:linear-gradient(135deg,#9f1239 0%,#6f0e28 100%);position:relative;overflow:hidden;">
  <div style="position:absolute;top:-30px;right:-30px;width:160px;height:160px;background:rgba(255,255,255,.05);border-radius:50%;"></div>
  <div class="d-flex align-items-center gap-3">
    <div style="width:64px;height:64px;background:var(--gold);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.8rem;font-weight:900;color:#7f1d1d;flex-shrink:0;"><?= strtoupper(substr($activeChild['name'],0,1)) ?></div>
    <div>
      <h4 class="fw-900 mb-1"><?= h($activeChild['name']) ?></h4>
      <div class="d-flex gap-2 flex-wrap">
        <span class="badge" style="background:rgba(245,166,35,.3);color:var(--gold);"><?= h($activeChild['student_ref']) ?></span>
        <span class="badge bg-light text-dark"><?= h($activeChild['year_group'] ?? '—') ?></span>
        <?php foreach ($batches as $b): ?>
          <span class="badge" style="background:rgba(255,255,255,.12);"><?= h($b['name']) ?></span>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
  <div class="col-sm-3">
    <div class="stat-card text-center">
      <div class="stat-value" style="color:<?= $attPct>=85?'#16a34a':($attPct>=70?'#ca8a04':'#dc2626') ?>;"><?= $attPct ?>%</div>
      <div class="stat-label">Attendance</div>
    </div>
  </div>
  <div class="col-sm-3">
    <div class="stat-card text-center">
      <div class="stat-value text-danger">£<?= number_format((float)$feesSummary['outstanding'],0) ?></div>
      <div class="stat-label">Outstanding</div>
    </div>
  </div>
  <div class="col-sm-3">
    <div class="stat-card text-center">
      <div class="stat-value text-success">£<?= number_format((float)$feesSummary['paid'],0) ?></div>
      <div class="stat-label">Paid</div>
    </div>
  </div>
  <div class="col-sm-3">
    <div class="stat-card text-center">
      <div class="stat-value"><?= count($absences) ?></div>
      <div class="stat-label">Absences (recent)</div>
    </div>
  </div>
</div>

<div class="row g-4">
  <!-- Recent Results -->
  <div class="col-lg-6">
    <div class="stat-card h-100">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="fw-700" style="color:var(--navy);">🏆 Recent Results</div>
        <a href="results.php" class="btn btn-sm btn-outline-secondary" style="font-size:.75rem;">All Results</a>
      </div>
      <?php foreach ($results as $r): ?>
        <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
          <div>
            <div class="fw-600 small"><?= h($r['assessment_name']) ?></div>
            <div class="text-muted" style="font-size:.72rem;"><?= h($r['subject'] ?? '—') ?> · <?= date('d M Y', strtotime($r['date'])) ?></div>
          </div>
          <div class="text-end">
            <div class="fw-700" style="color:<?= ($r['percentage']??0)>=70?'#16a34a':(($r['percentage']??0)>=50?'#ca8a04':'#dc2626') ?>;"><?= $r['percentage'] !== null ? round($r['percentage']).'%' : '—' ?></div>
            <?php if ($r['grade']): ?><div class="small text-muted"><?= h($r['grade']) ?></div><?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
      <?php if (empty($results)): ?><p class="text-muted small py-3 text-center">No results yet.</p><?php endif; ?>
    </div>
  </div>

  <!-- Fees -->
  <div class="col-lg-6">
    <div class="stat-card h-100">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="fw-700" style="color:var(--navy);">💳 Fee Invoices</div>
        <a href="fees.php" class="btn btn-sm btn-outline-secondary" style="font-size:.75rem;">All Fees</a>
      </div>
      <?php foreach ($recentInvoices as $inv): ?>
        <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
          <div>
            <div class="fw-600 small"><?= h($inv['invoice_number']) ?></div>
            <div class="text-muted" style="font-size:.72rem;">Due <?= date('d M Y', strtotime($inv['due_date'])) ?></div>
          </div>
          <div class="text-end">
            <div class="fw-700 <?= $inv['status']==='overdue'?'text-danger':($inv['status']==='paid'?'text-success':'text-warning') ?>">
              £<?= number_format($inv['amount_due'],2) ?>
            </div>
            <div class="small"><?= ucfirst($inv['status']) ?></div>
          </div>
        </div>
      <?php endforeach; ?>
      <?php if (empty($recentInvoices)): ?><p class="text-muted small py-3 text-center">No invoices.</p><?php endif; ?>
    </div>
  </div>

  <!-- Absences -->
  <?php if (!empty($absences)): ?>
  <div class="col-12">
    <div class="tpa-table">
      <div class="p-3 border-bottom"><div class="fw-700" style="color:var(--navy);">📋 Recent Absences / Late</div></div>
      <table class="table table-hover mb-0">
        <thead><tr><th>Date</th><th>Class</th><th>Status</th><th>Notes</th></tr></thead>
        <tbody>
        <?php foreach ($absences as $a): ?>
          <tr>
            <td class="fw-600 small"><?= date('D d M Y', strtotime($a['date'])) ?></td>
            <td class="small"><?= h($a['batch_name']) ?></td>
            <td><?= $a['status']==='absent'?'<span class="badge bg-danger">Absent</span>':'<span class="badge bg-warning text-dark">Late</span>' ?></td>
            <td class="small text-muted"><?= h($a['notes'] ?? '—') ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
