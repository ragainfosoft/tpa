<?php
// =====================================================
// TPA IMS — Payment Reconciliation Dashboard
// =====================================================
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
startSecureSession();
requireRole(['admin', 'branch_manager']);

$db  = getDB();
$tab = $_GET['tab'] ?? 'all';

// ── Handle manual match (link unmatched payment to invoice) ───────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['match_payment'])) {
    verifyCsrf();
    $paymentId = (int)$_POST['payment_id'];
    $invoiceId = (int)$_POST['invoice_id'];
    if ($paymentId && $invoiceId) {
        $inv = $db->prepare('SELECT amount_due, student_id FROM invoices WHERE id=?');
        $inv->execute([$invoiceId]); $inv = $inv->fetch();
        if ($inv) {
            $db->prepare('UPDATE payments SET invoice_id=?, student_id=?, reconciled=1 WHERE id=?')
               ->execute([$invoiceId, $inv['student_id'], $paymentId]);
            // Recalculate invoice status
            $totalPaid = (float)$db->query("SELECT COALESCE(SUM(amount),0) FROM payments WHERE invoice_id={$invoiceId}")->fetchColumn();
            $newStatus = $totalPaid >= $inv['amount_due'] ? 'paid' : ($totalPaid > 0 ? 'partial' : 'unpaid');
            $db->prepare('UPDATE invoices SET status=?, updated_at=NOW() WHERE id=?')->execute([$newStatus, $invoiceId]);
            logActivity('payment_matched', "Manual match: payment #{$paymentId} → invoice #{$invoiceId}");
            setFlash('success', "Payment matched to invoice. Invoice status updated to: $newStatus.");
        }
    }
    header('Location: reconcile.php?tab=unmatched'); exit;
}

// ── Stats ──────────────────────────────────────────────────────────────────
$stats = $db->query("SELECT
    COUNT(*) as total,
    SUM(reconciled=1) as reconciled,
    SUM(reconciled=0) as unmatched,
    SUM(gateway='stripe') as stripe_total,
    SUM(gateway='gocardless') as gc_total,
    SUM(gateway='manual' OR gateway IS NULL) as manual_total,
    COALESCE(SUM(CASE WHEN gateway IN ('stripe','gocardless') THEN amount END), 0) as online_revenue,
    COALESCE(SUM(CASE WHEN gateway='manual' OR gateway IS NULL THEN amount END), 0) as manual_revenue,
    COALESCE(SUM(amount), 0) as total_revenue
    FROM payments WHERE payment_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)")->fetch();

$invoicesOut = $db->query("SELECT COALESCE(SUM(amount_due),0) as total_due FROM invoices WHERE status IN ('unpaid','overdue')")->fetchColumn();

// ── Payments data ──────────────────────────────────────────────────────────
$where  = '1=1'; $params = [];
if ($tab === 'online')    { $where .= " AND p.gateway IN ('stripe','gocardless')"; }
elseif ($tab === 'manual'){ $where .= " AND (p.gateway='manual' OR p.gateway IS NULL)"; }
elseif ($tab === 'unmatched') { $where .= " AND p.reconciled=0"; }

$payments = $db->prepare("SELECT p.*,
    CONCAT(s.first_name,' ',s.last_name) as student_name,
    i.invoice_number, i.amount_due as invoice_due, i.status as invoice_status
    FROM payments p
    LEFT JOIN students s ON s.id=p.student_id
    LEFT JOIN invoices i ON i.id=p.invoice_id
    WHERE $where ORDER BY p.payment_date DESC, p.id DESC LIMIT 200");
$payments->execute($params); $payments = $payments->fetchAll();

// For unmatched — get list of unpaid invoices for manual match dropdown
$unpaidInvoices = [];
if ($tab === 'unmatched') {
    $unpaidInvoices = $db->query("SELECT i.id, i.invoice_number, i.amount_due, i.status,
        CONCAT(s.first_name,' ',s.last_name) as student_name
        FROM invoices i JOIN students s ON s.id=i.student_id
        WHERE i.status IN ('unpaid','overdue','partial') ORDER BY i.due_date ASC LIMIT 200")->fetchAll();
}

// Stripe/GC config status
$stripeOk = !empty(getSetting('stripe_secret_key'));
$gcOk     = !empty(getSetting('gocardless_access_token'));
$siteUrl  = rtrim(SITE_URL, '/'); // e.g. http://localhost/tpaAG/admin

$page_title   = 'Payment Reconciliation';
$page_section = 'fees';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
  <div>
    <h1><i class="bi bi-arrow-left-right me-2" style="color:var(--gold);"></i>Payment Reconciliation</h1>
    <p class="text-muted mb-0 small">Auto-match online payments · View manual entries · Resolve unmatched transactions</p>
  </div>
  <div class="d-flex gap-2">
    <a href="../fees/record-payment.php" class="btn btn-sm btn-outline-secondary"><i class="bi bi-plus me-1"></i>Record Manual</a>
    <a href="reconcile.php?export=csv" class="btn btn-sm btn-outline-dark"><i class="bi bi-download me-1"></i>Export CSV</a>
  </div>
</div>

<!-- Gateway Status -->
<?php if (!$stripeOk || !$gcOk): ?>
<div class="row g-3 mb-4">
  <?php if (!$stripeOk): ?>
  <div class="col-md-6">
    <div class="alert alert-warning mb-0 d-flex align-items-center gap-3">
      <i class="bi bi-stripe fs-4"></i>
      <div>
        <strong>Stripe not configured</strong><br>
        <small class="text-muted">Add secret key in <a href="../settings/index.php?tab=payment">Payment Settings</a>.</small><br>
        <small class="font-monospace">Webhook URL: <code><?= $siteUrl ?>/payments/stripe-webhook.php</code></small>
      </div>
    </div>
  </div>
  <?php endif; ?>
  <?php if (!$gcOk): ?>
  <div class="col-md-6">
    <div class="alert alert-warning mb-0 d-flex align-items-center gap-3">
      <i class="bi bi-bank fs-4"></i>
      <div>
        <strong>GoCardless not configured</strong><br>
        <small class="text-muted">Add access token in <a href="../settings/index.php?tab=payment">Payment Settings</a>.</small><br>
        <small class="font-monospace">Webhook URL: <code><?= $siteUrl ?>/payments/gocardless-webhook.php</code></small>
      </div>
    </div>
  </div>
  <?php endif; ?>
</div>
<?php endif; ?>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
  <?php $kpis = [
    ['Total Revenue (30d)',   '£'.number_format($stats['total_revenue'],2),  'cash-stack',       '#d1fae5','#065f46'],
    ['Online Payments (30d)','£'.number_format($stats['online_revenue'],2),  'credit-card',      '#e0e7ff','#3730a3'],
    ['Manual Payments',      '£'.number_format($stats['manual_revenue'],2),  'cash',             '#f0fdf4','#166534'],
    ['Reconciled',           $stats['reconciled'].' / '.$stats['total'],     'check-circle',     '#d1fae5','#065f46'],
    ['Unmatched',            $stats['unmatched'],                             'exclamation-circle','#fee2e2','#991b1b'],
    ['Outstanding Invoices', '£'.number_format($invoicesOut,2),              'file-earmark-x',   '#fef3c7','#92400e'],
  ];
  foreach ($kpis as [$label,$val,$icon,$bg,$color]): ?>
  <div class="col-6 col-lg-2">
    <div class="stat-card d-flex align-items-center gap-3">
      <div class="stat-icon" style="background:<?= $bg ?>;color:<?= $color ?>;"><i class="bi bi-<?= $icon ?>"></i></div>
      <div><div class="stat-label"><?= $label ?></div><div class="stat-value" style="font-size:1.3rem;"><?= $val ?></div></div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs mb-4">
  <?php foreach (['all'=>'All Payments','online'=>'🌐 Online','manual'=>'✋ Manual','unmatched'=>'⚠️ Unmatched'] as $t=>$l): ?>
    <li class="nav-item">
      <a class="nav-link <?= $tab===$t?'active':'' ?>" href="?tab=<?= $t ?>">
        <?= $l ?>
        <?php if ($t==='unmatched' && $stats['unmatched'] > 0): ?>
          <span class="badge bg-danger ms-1"><?= $stats['unmatched'] ?></span>
        <?php endif; ?>
      </a>
    </li>
  <?php endforeach; ?>
</ul>

<!-- Payments Table -->
<div class="tpa-table table-responsive">
  <table class="table table-hover mb-0">
    <thead><tr>
      <th>Date</th><th>Gateway</th><th>Student</th><th>Invoice</th>
      <th>Amount</th><th>Method</th><th>Status</th><th>Gateway Ref</th>
      <?php if ($tab === 'unmatched'): ?><th>Action</th><?php endif; ?>
    </tr></thead>
    <tbody>
      <?php foreach ($payments as $p):
        $isOnline  = in_array($p['gateway'], ['stripe','gocardless']);
        $isMatched = $p['reconciled'];
        $gw = $p['gateway'] ?? '';
        if ($gw === 'stripe')     $gwLabel = '<span class="badge" style="background:#6772e5;color:#fff;">Stripe</span>';
        elseif ($gw === 'gocardless') $gwLabel = '<span class="badge" style="background:#2c97de;color:#fff;">GoCardless</span>';
        elseif ($gw === 'manual') $gwLabel = '<span class="badge bg-secondary">Manual</span>';
        else $gwLabel = '<span class="badge bg-light text-dark border">'.h($p['method'] ?? '—').'</span>';
      ?>
      <tr>
        <td class="small"><?= date('d M Y', strtotime($p['payment_date'])) ?></td>
        <td><?= $gwLabel ?></td>
        <td class="fw-600"><?= h($p['student_name'] ?? '<span class="text-muted fst-italic">Unknown</span>') ?></td>
        <td class="small">
          <?php if ($p['invoice_number']): ?>
            <a href="../fees/index.php?invoice_id=<?= $p['invoice_id'] ?>"><?= h($p['invoice_number']) ?></a><br>
            <?= invoiceStatusBadge($p['invoice_status']) ?>
          <?php else: ?>
            <span class="text-muted fst-italic">Unmatched</span>
          <?php endif; ?>
        </td>
        <td class="fw-700"><?= formatMoney($p['amount']) ?></td>
        <td class="small text-muted"><?= h($p['method'] ?? '—') ?></td>
        <td>
          <?php if ($isMatched): ?>
            <span class="badge bg-success"><i class="bi bi-check me-1"></i>Reconciled</span>
          <?php else: ?>
            <span class="badge bg-warning text-dark"><i class="bi bi-exclamation me-1"></i>Unmatched</span>
          <?php endif; ?>
        </td>
        <td class="small text-muted font-monospace" style="max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?= h($p['gateway_payment_id'] ?? '') ?>">
          <?= h(mb_substr($p['gateway_payment_id'] ?? '—', 0, 24)) ?>
        </td>
        <?php if ($tab === 'unmatched'): ?>
        <td>
          <?php if (!$isMatched): ?>
          <button class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size:.75rem;"
            onclick="openMatchModal(<?= $p['id'] ?>, <?= $p['amount'] ?>, '<?= h(date('d M Y',strtotime($p['payment_date']))) ?>')">
            <i class="bi bi-link me-1"></i>Match
          </button>
          <?php endif; ?>
        </td>
        <?php endif; ?>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($payments)): ?>
        <tr><td colspan="9" class="text-center text-muted py-5">
          <?php if ($tab === 'unmatched'): ?>
            <i class="bi bi-check-circle text-success fs-2 d-block mb-2"></i>No unmatched payments — you're all reconciled! ✅
          <?php else: ?>
            No payments found for this filter.
          <?php endif; ?>
        </td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Manual Match Modal -->
<div class="modal fade" id="matchModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header" style="background:var(--navy);"><h5 class="modal-title text-white"><i class="bi bi-link me-2"></i>Match to Invoice</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
        <input type="hidden" name="match_payment" value="1">
        <input type="hidden" name="payment_id" id="matchPaymentId">
        <div class="modal-body">
          <div class="alert alert-info small" id="matchPaymentInfo"></div>
          <div class="mb-3">
            <label class="form-label fw-600">Select Invoice to Match</label>
            <select name="invoice_id" class="form-select" required>
              <option value="">— Choose invoice —</option>
              <?php foreach ($unpaidInvoices as $inv): ?>
                <option value="<?= $inv['id'] ?>"><?= h($inv['invoice_number']) ?> — <?= h($inv['student_name']) ?> — <?= formatMoney($inv['amount_due']) ?> (<?= ucfirst($inv['status']) ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="modal-footer"><button class="btn btn-dark w-100 fw-700"><i class="bi bi-check-circle me-1"></i>Confirm Match</button></div>
      </form>
    </div>
  </div>
</div>

<script>
function openMatchModal(payId, amount, date) {
  document.getElementById('matchPaymentId').value = payId;
  document.getElementById('matchPaymentInfo').innerText = `Payment #${payId}: £${parseFloat(amount).toFixed(2)} on ${date}`;
  new bootstrap.Modal(document.getElementById('matchModal')).show();
}
</script>

<?php
// ── CSV Export ────────────────────────────────────────────────────────────
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="tpa-payments-' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Date','Student','Invoice #','Amount','Method','Gateway','Reconciled','Gateway Ref','Notes']);
    foreach ($payments as $p) {
        fputcsv($out, [
            $p['payment_date'], $p['student_name'] ?? '', $p['invoice_number'] ?? '',
            number_format($p['amount'],2), $p['method'] ?? '', $p['gateway'] ?? 'manual',
            $p['reconciled'] ? 'Yes' : 'No', $p['gateway_payment_id'] ?? '', $p['notes'] ?? ''
        ]);
    }
    fclose($out); exit;
}
require_once __DIR__ . '/../includes/footer.php';
?>
