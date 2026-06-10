<?php
// =====================================================
// TPA IMS — Create Invoice
// =====================================================

$page_title   = 'Create Invoice';
$page_section = 'fees';
require_once __DIR__ . '/../includes/header.php';

$db     = getDB();
$errors = [];

$students      = $db->query("SELECT id, CONCAT(first_name,' ',last_name) as name, student_ref FROM students WHERE status='active' ORDER BY first_name")->fetchAll();
$feeStructures = $db->query("SELECT id, name, amount, frequency FROM fee_structures WHERE is_active=1 ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $studentId  = (int)$_POST['student_id'];
    $amount     = (float)$_POST['amount'];
    $discount   = (float)($_POST['discount'] ?? 0);
    $amountDue  = $amount - $discount;
    $dueDate    = $_POST['due_date'];
    $periodLabel= trim($_POST['period_label'] ?? '');
    $notes      = trim($_POST['notes'] ?? '');

    if (!$studentId) $errors[] = 'Please select a student.';
    if ($amount <= 0) $errors[] = 'Amount must be greater than zero.';
    if (empty($dueDate)) $errors[] = 'Due date is required.';

    if (empty($errors)) {
        $invoiceNum = generateInvoiceNumber();
        $db->prepare('INSERT INTO invoices (invoice_number,student_id,amount,discount,amount_due,period_label,due_date,status,notes,created_by) VALUES (?,?,?,?,?,?,?,?,?,?)')
           ->execute([$invoiceNum, $studentId, $amount, $discount, $amountDue, $periodLabel, $dueDate, 'unpaid', $notes, currentUserId()]);
        logActivity('invoice_created', "Invoice $invoiceNum — £".number_format($amountDue,2));
        setFlash('success', "Invoice $invoiceNum created for £".number_format($amountDue,2).'.');
        header('Location: index.php'); exit;
    }
}
?>

<div class="page-header">
  <h1><i class="bi bi-receipt me-2" style="color:var(--gold);"></i>Create Invoice</h1>
  <a href="index.php" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<?php if ($errors): ?>
  <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= h($e) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<div class="row g-4">
  <div class="col-lg-7">
    <div class="stat-card">
      <h6 class="fw-700 mb-4 text-uppercase" style="font-size:.7rem;letter-spacing:.1em;color:#888;">Invoice Details</h6>
      <form method="POST" class="row g-3">
        <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">

        <div class="col-12">
          <label class="form-label fw-600 small">Student <span class="text-danger">*</span></label>
          <select name="student_id" class="form-select" required>
            <option value="">Select student…</option>
            <?php foreach ($students as $s): ?>
              <option value="<?= $s['id'] ?>" <?= (($_POST['student_id']??'') == $s['id'])?'selected':'' ?>>
                <?= h($s['name']) ?> (<?= h($s['student_ref']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-sm-6">
          <label class="form-label fw-600 small">Use Fee Structure</label>
          <select id="feeStructurePicker" class="form-select">
            <option value="">Manual entry…</option>
            <?php foreach ($feeStructures as $f): ?>
              <option value="<?= $f['amount'] ?>"><?= h($f['name']) ?> — £<?= number_format($f['amount'],2) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-sm-3">
          <label class="form-label fw-600 small">Amount (£) <span class="text-danger">*</span></label>
          <input type="number" name="amount" id="amountField" class="form-control" step="0.01" min="0.01" value="<?= h($_POST['amount'] ?? '') ?>" required>
        </div>
        <div class="col-sm-3">
          <label class="form-label fw-600 small">Discount (£)</label>
          <input type="number" name="discount" class="form-control" step="0.01" min="0" value="<?= h($_POST['discount'] ?? '0') ?>">
        </div>

        <div class="col-sm-6">
          <label class="form-label fw-600 small">Period Label</label>
          <input type="text" name="period_label" class="form-control" placeholder="e.g. April 2026 / Spring Term" value="<?= h($_POST['period_label'] ?? '') ?>">
        </div>
        <div class="col-sm-6">
          <label class="form-label fw-600 small">Due Date <span class="text-danger">*</span></label>
          <input type="date" name="due_date" class="form-control" value="<?= h($_POST['due_date'] ?? date('Y-m-d', strtotime('+7 days'))) ?>" required>
        </div>
        <div class="col-12">
          <label class="form-label fw-600 small">Notes</label>
          <textarea name="notes" class="form-control" rows="2"><?= h($_POST['notes'] ?? '') ?></textarea>
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-success"><i class="bi bi-receipt me-1"></i>Create Invoice</button>
          <a href="index.php" class="btn btn-outline-secondary ms-2">Cancel</a>
        </div>
      </form>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="stat-card">
      <h6 class="fw-700 mb-3 text-uppercase" style="font-size:.7rem;letter-spacing:.1em;color:#888;">Fee Structures</h6>
      <div class="table-responsive">
        <table class="table table-sm mb-0">
          <thead><tr><th>Name</th><th>Amount</th><th>Frequency</th></tr></thead>
          <tbody>
            <?php foreach ($feeStructures as $f): ?>
              <tr>
                <td class="small fw-600"><?= h($f['name']) ?></td>
                <td class="small">£<?= number_format($f['amount'],2) ?></td>
                <td class="small text-muted"><?= h(str_replace('_',' ',ucfirst($f['frequency']))) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('feeStructurePicker').addEventListener('change', function() {
  if (this.value) document.getElementById('amountField').value = this.value;
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
