<?php
// TPA — Student Fees
require_once __DIR__ . '/includes/header.php';

$invoices = $db->prepare("SELECT * FROM invoices WHERE student_id=? ORDER BY created_at DESC");
$invoices->execute([$studentId]); $invoices = $invoices->fetchAll();
?>

<div class="page-header"><h1><i class="bi bi-receipt me-2 text-success"></i>My Invoices</h1></div>
<div class="tpa-table">
  <table class="table">
    <thead><tr><th>Invoice #</th><th>Description</th><th>Due Date</th><th>Amount</th><th>Status</th></tr></thead>
    <tbody>
      <?php foreach ($invoices as $inv): ?>
      <tr>
        <td class="fw-600"><?= h($inv['invoice_number']) ?></td>
        <td class="small"><?= h($inv['title']) ?></td>
        <td><?= date('d M Y', strtotime($inv['due_date'])) ?></td>
        <td class="fw-700">£<?= number_format($inv['amount_due'],2) ?></td>
        <td>
          <span class="badge <?= $inv['status']==='paid'?'bg-success':($inv['status']==='overdue'?'bg-danger':'bg-warning text-dark') ?>">
            <?= strtoupper($inv['status']) ?>
          </span>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($invoices)): ?><tr><td colspan="5" class="text-center py-4 text-muted">No invoices found.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
