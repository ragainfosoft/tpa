<?php
// =====================================================
// TPA IMS — Record Payment Against Invoice
// =====================================================

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
startSecureSession();
requireLogin(SITE_URL . '/login.php');

$db        = getDB();
$invoiceId = (int)($_GET['invoice_id'] ?? $_GET['id'] ?? 0);
$invoice   = null;

if ($invoiceId) {
    $stmt = $db->prepare('SELECT i.*, CONCAT(s.first_name," ",s.last_name) as student_name, s.id as student_id FROM invoices i JOIN students s ON s.id=i.student_id WHERE i.id=?');
    $stmt->execute([$invoiceId]); $invoice = $stmt->fetch();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $invId     = (int)$_POST['invoice_id'];
    $amount    = (float)$_POST['amount'];
    $method    = $_POST['method'] ?? 'BACS';
    $payDate   = $_POST['payment_date'] ?: date('Y-m-d');
    $notes     = trim($_POST['notes'] ?? '');

    if ($amount <= 0) $errors[] = 'Amount must be greater than 0.';
    if (!$invId) $errors[] = 'Invoice required.';

    if (empty($errors)) {
        // Get current invoice data
        $inv = $db->prepare('SELECT * FROM invoices WHERE id=?');
        $inv->execute([$invId]); $inv = $inv->fetch();

        // Insert payment record
        $db->prepare('INSERT INTO payments (invoice_id,student_id,amount,method,payment_date,notes,recorded_by) VALUES (?,?,?,?,?,?,?)')
           ->execute([$invId, $inv['student_id'], $amount, $method, $payDate, $notes, currentUserId()]);

        // Update invoice status
        $totalPaid = $db->prepare('SELECT COALESCE(SUM(amount),0) FROM payments WHERE invoice_id=?');
        $totalPaid->execute([$invId]); $totalPaid = (float)$totalPaid->fetchColumn();

        if ($totalPaid >= $inv['amount_due']) {
            $newStatus = 'paid';
        } elseif ($totalPaid > 0) {
            $newStatus = 'partial';
        } else {
            $newStatus = $inv['status'];
        }

        $db->prepare('UPDATE invoices SET status=?, updated_at=NOW() WHERE id=?')->execute([$newStatus, $invId]);

        logActivity('payment_recorded', "Payment £".number_format($amount,2)." for invoice {$inv['invoice_number']}");
        setFlash('success', '✅ Payment of '.formatMoney($amount).' recorded. Invoice is now '.ucfirst($newStatus).'.');
        header('Location: index.php'); exit;
    }
}

$unpaidInvoices = $db->query("SELECT i.id, i.invoice_number, i.amount_due, i.status, CONCAT(s.first_name,' ',s.last_name) as student_name FROM invoices i JOIN students s ON s.id=i.student_id WHERE i.status IN ('unpaid','overdue','partial') ORDER BY i.due_date ASC LIMIT 100")->fetchAll();

$page_title   = 'Record Payment';
$page_section = 'fees';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
  <h1><i class="bi bi-cash-stack me-2" style="color:var(--gold);"></i>Record Payment</h1>
  <a href="index.php" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back to Fees</a>
</div>

<?php if ($errors): ?>
  <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= h($e) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<div class="row g-4">
  <div class="col-lg-6">
    <div class="stat-card">
      <h6 class="fw-700 mb-4 text-uppercase" style="font-size:.7rem;letter-spacing:.1em;color:#888;">Payment Details</h6>
      <form method="POST" class="row g-3">
        <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
        <input type="hidden" name="invoice_id" id="invoiceIdField" value="<?= $invoiceId ?>">

        <div class="col-12">
          <label class="form-label fw-600 small">Invoice *</label>
          <select id="invoicePicker" class="form-select" onchange="pickInvoice(this)">
            <option value="">Select invoice…</option>
            <?php foreach ($unpaidInvoices as $inv): ?>
              <option value="<?= $inv['id'] ?>" data-amount="<?= $inv['amount_due'] ?>" <?= $inv['id']==$invoiceId?'selected':'' ?>>
                <?= h($inv['invoice_number']) ?> — <?= h($inv['student_name']) ?> — £<?= number_format($inv['amount_due'],2) ?> (<?= ucfirst($inv['status']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-sm-6">
          <label class="form-label fw-600 small">Amount Received (£) *</label>
          <input type="number" name="amount" id="amountField" class="form-control" step="0.01" min="0.01"
                 value="<?= $invoice ? number_format($invoice['amount_due'],2,'.','') : '' ?>" required>
        </div>
        <div class="col-sm-6">
          <label class="form-label fw-600 small">Payment Method</label>
          <select name="method" class="form-select">
            <?php foreach (['BACS','Cash','Card','Cheque','Other'] as $m): ?>
              <option><?= $m ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-sm-6">
          <label class="form-label fw-600 small">Payment Date</label>
          <input type="date" name="payment_date" class="form-control" value="<?= date('Y-m-d') ?>">
        </div>
        <div class="col-12">
          <label class="form-label fw-600 small">Reference / Notes</label>
          <input type="text" name="notes" class="form-control" placeholder="e.g. bank transfer ref, receipt number">
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-success btn-lg"><i class="bi bi-check-circle me-2"></i>Record Payment</button>
        </div>
      </form>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="stat-card" style="background:#f8ffe8;border:1px solid #bbf7d0;">
      <h6 class="fw-700 mb-3"><i class="bi bi-info-circle text-success me-2"></i>How it works</h6>
      <ul class="small text-muted ps-3">
        <li class="mb-2">Select the unpaid or overdue invoice from the list.</li>
        <li class="mb-2">Enter the exact amount received — if it's a partial payment, the invoice will be marked <strong>Partial</strong>.</li>
        <li class="mb-2">Once the full amount is paid, the invoice is automatically marked <strong>Paid</strong>.</li>
        <li>All payments are logged for audit/reporting.</li>
      </ul>
    </div>

    <?php if ($invoice): ?>
    <div class="stat-card mt-3">
      <h6 class="fw-700 mb-3 text-uppercase" style="font-size:.7rem;letter-spacing:.1em;color:#888;">Selected Invoice</h6>
      <table class="table table-sm table-borderless mb-0 small">
        <tr><td class="text-muted fw-600">Student</td><td><?= h($invoice['student_name']) ?></td></tr>
        <tr><td class="text-muted fw-600">Invoice</td><td><?= h($invoice['invoice_number']) ?></td></tr>
        <tr><td class="text-muted fw-600">Amount Due</td><td class="fw-700 text-danger"><?= formatMoney($invoice['amount_due']) ?></td></tr>
        <tr><td class="text-muted fw-600">Status</td><td><?= invoiceStatusBadge($invoice['status']) ?></td></tr>
        <tr><td class="text-muted fw-600">Period</td><td><?= h($invoice['period_label'] ?? '—') ?></td></tr>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>

<script>
function pickInvoice(el) {
  const opt = el.options[el.selectedIndex];
  document.getElementById('invoiceIdField').value = el.value;
  const amt = opt.dataset.amount;
  if (amt) document.getElementById('amountField').value = parseFloat(amt).toFixed(2);
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
