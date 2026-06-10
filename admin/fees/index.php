<?php
// =====================================================
// TPA IMS — Fees Overview
// =====================================================

$page_title   = 'Fees Overview';
$page_section = 'fees';
require_once __DIR__ . '/../includes/header.php';

$db = getDB();

// ── Verify / Reject parent BACS claim ────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action    = $_POST['action'] ?? '';
    $paymentId = (int)($_POST['payment_id'] ?? 0);

    if ($action === 'verify_bacs' && $paymentId) {
        $pay = $db->prepare('SELECT p.*, i.amount_due, i.status as inv_status FROM payments p JOIN invoices i ON i.id=p.invoice_id WHERE p.id=? AND p.reconciled=0 AND p.method="bacs"');
        $pay->execute([$paymentId]); $pay = $pay->fetch();
        if ($pay) {
            $db->prepare('UPDATE payments SET reconciled=1, notes=CONCAT(COALESCE(notes,"")," [Verified by admin]") WHERE id=?')->execute([$paymentId]);
            // Work out new invoice status
            $totalPaid = (float)$db->prepare('SELECT COALESCE(SUM(amount),0) FROM payments WHERE invoice_id=? AND reconciled=1')->execute([$pay['invoice_id']]) ? $db->lastInsertId() : 0;
            $paidStmt = $db->prepare('SELECT COALESCE(SUM(amount),0) FROM payments WHERE invoice_id=? AND reconciled=1');
            $paidStmt->execute([$pay['invoice_id']]); $totalPaid = (float)$paidStmt->fetchColumn();
            $newStatus = $totalPaid >= (float)$pay['amount_due'] ? 'paid' : ($totalPaid > 0 ? 'partial' : $pay['inv_status']);
            $db->prepare('UPDATE invoices SET status=? WHERE id=?')->execute([$newStatus, $pay['invoice_id']]);
            logActivity('bacs_verified', "Admin verified BACS payment #{$paymentId} for invoice #{$pay['invoice_id']}");
            setFlash('success', 'Bank transfer verified and invoice updated.');
        }
        header('Location: index.php'); exit;
    }

    if ($action === 'reject_bacs' && $paymentId) {
        $db->prepare('DELETE FROM payments WHERE id=? AND reconciled=0 AND method="bacs"')->execute([$paymentId]);
        logActivity('bacs_rejected', "Admin rejected BACS claim #{$paymentId}");
        setFlash('warning', 'Bank transfer claim rejected and removed.');
        header('Location: index.php'); exit;
    }
}

// Pending BACS verifications
$pendingBacs = $db->query("
    SELECT p.*, i.invoice_number, i.amount_due, i.status as inv_status,
           CONCAT(s.first_name,' ',s.last_name) as student_name, s.id as student_id
    FROM payments p
    JOIN invoices i ON i.id = p.invoice_id
    JOIN students s ON s.id = p.student_id
    WHERE p.method='bacs' AND p.reconciled=0
    ORDER BY p.created_at ASC
")->fetchAll();

// Summary stats
$stats = $db->query("SELECT
    SUM(CASE WHEN status='paid' AND YEAR(i.created_at)=YEAR(NOW()) THEN amount_due ELSE 0 END) as collected_year,
    SUM(CASE WHEN status='overdue' THEN amount_due ELSE 0 END) as overdue_total,
    SUM(CASE WHEN status='unpaid' THEN amount_due ELSE 0 END) as unpaid_total,
    COUNT(CASE WHEN status='overdue' THEN 1 END) as overdue_count,
    COUNT(CASE WHEN status='paid' AND MONTH(i.created_at)=MONTH(NOW()) AND YEAR(i.created_at)=YEAR(NOW()) THEN 1 END) as paid_this_month
    FROM invoices i")->fetch();

// Invoice list with filters
$fStatus = $_GET['status'] ?? '';
$search  = trim($_GET['q'] ?? '');
$page    = max(1,(int)($_GET['page'] ?? 1));
$perPage = 25;

$where  = '1=1';
$params = [];
if ($fStatus) { $where .= ' AND i.status = ?'; $params[] = $fStatus; }
if ($search)  { $where .= ' AND (CONCAT(s.first_name," ",s.last_name) LIKE ? OR i.invoice_number LIKE ?)'; $p = "%$search%"; $params = array_merge($params,[$p,$p]); }

$total = $db->prepare("SELECT COUNT(*) FROM invoices i JOIN students s ON i.student_id = s.id WHERE $where");
$total->execute($params); $total = (int)$total->fetchColumn();

$offset   = ($page-1)*$perPage;
$invoices = $db->prepare("SELECT i.*, CONCAT(s.first_name,' ',s.last_name) as student_name
    FROM invoices i JOIN students s ON i.student_id = s.id
    WHERE $where ORDER BY i.due_date DESC LIMIT $perPage OFFSET $offset");
$invoices->execute($params);
$invoices = $invoices->fetchAll();
?>

<div class="page-header">
  <h1><i class="bi bi-receipt me-2" style="color:var(--gold);"></i>Fees &amp; Invoices</h1>
  <div class="d-flex gap-2">
    <a href="create-invoice.php" class="btn btn-sm btn-dark"><i class="bi bi-plus-lg me-1"></i>Create Invoice</a>
    <a href="structures.php" class="btn btn-sm btn-outline-primary"><i class="bi bi-layers me-1"></i>Fee Structures</a>
    <a href="../cron/process-recurring.php" class="btn btn-sm btn-outline-info" onclick="return confirm('Run automated invoice generation for all active plans?')"><i class="bi bi-gear-wide-connected me-1"></i>Run Auto-Gen</a>
  </div>
</div>

<!-- ── Pending Bank Transfer Verifications ──────────────────────────────── -->
<?php if (!empty($pendingBacs)): ?>
<div class="card border-warning mb-4" style="border-radius:14px;overflow:hidden;">
  <div class="card-header d-flex align-items-center gap-2" style="background:#fffbeb;border-bottom:1px solid #fde68a;padding:14px 20px;">
    <i class="bi bi-hourglass-split text-warning fs-5"></i>
    <strong class="text-warning-emphasis" style="font-size:.92rem;">Bank Transfer Verifications Pending</strong>
    <span class="badge bg-warning text-dark ms-1"><?= count($pendingBacs) ?></span>
    <span class="ms-auto text-muted small">Parents have claimed these bank transfers — verify once you see the funds in your account.</span>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0" style="font-size:.86rem;">
        <thead style="background:#fef9eb;">
          <tr>
            <th class="ps-3">Student</th>
            <th>Invoice</th>
            <th>Amount</th>
            <th>Claimed Payment Date</th>
            <th>Bank Ref</th>
            <th>Notes</th>
            <th>Claimed At</th>
            <th class="pe-3">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($pendingBacs as $p): ?>
          <tr>
            <td class="ps-3 fw-600"><a href="../students/view.php?id=<?= $p['student_id'] ?>" class="text-decoration-none"><?= h($p['student_name']) ?></a></td>
            <td class="fw-700"><?= h($p['invoice_number']) ?></td>
            <td class="fw-700 text-success"><?= formatMoney($p['amount']) ?></td>
            <td><?= formatDate($p['payment_date']) ?></td>
            <td><?= $p['reference'] ? '<code>'.h($p['reference']).'</code>' : '<span class="text-muted">—</span>' ?></td>
            <td class="text-muted"><?= h($p['notes'] ?? '—') ?></td>
            <td class="text-muted small"><?= formatDate($p['created_at'], true) ?></td>
            <td class="pe-3">
              <form method="POST" class="d-inline" onsubmit="return confirm('Verify this BACS payment and mark invoice as paid?')">
                <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
                <input type="hidden" name="action" value="verify_bacs">
                <input type="hidden" name="payment_id" value="<?= $p['id'] ?>">
                <button class="btn btn-sm btn-success py-0 fw-700" style="font-size:.75rem;"><i class="bi bi-check-lg me-1"></i>Verify</button>
              </form>
              <form method="POST" class="d-inline ms-1" onsubmit="return confirm('Reject this claim? The payment record will be deleted.')">
                <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
                <input type="hidden" name="action" value="reject_bacs">
                <input type="hidden" name="payment_id" value="<?= $p['id'] ?>">
                <button class="btn btn-sm btn-outline-danger py-0" style="font-size:.75rem;"><i class="bi bi-x-lg me-1"></i>Reject</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- Stat cards -->
<div class="row g-3 mb-4">
  <div class="col-6 col-lg-3">
    <div class="stat-card d-flex align-items-center gap-3">
      <div class="stat-icon" style="background:#e8f5e9;color:#2e7d32;"><i class="bi bi-currency-pound"></i></div>
      <div><div class="stat-label">Collected This Year</div><div class="stat-value" style="font-size:1.4rem;"><?= formatMoney((float)($stats['collected_year']??0)) ?></div></div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="stat-card d-flex align-items-center gap-3">
      <div class="stat-icon" style="background:#fce4ec;color:#c62828;"><i class="bi bi-exclamation-circle"></i></div>
      <div><div class="stat-label">Overdue</div><div class="stat-value" style="font-size:1.4rem;"><?= formatMoney((float)($stats['overdue_total']??0)) ?></div><div class="small text-danger"><?= $stats['overdue_count'] ?> invoices</div></div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="stat-card d-flex align-items-center gap-3">
      <div class="stat-icon" style="background:#fff3e0;color:#e65100;"><i class="bi bi-hourglass-split"></i></div>
      <div><div class="stat-label">Outstanding (Unpaid)</div><div class="stat-value" style="font-size:1.4rem;"><?= formatMoney((float)($stats['unpaid_total']??0)) ?></div></div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="stat-card d-flex align-items-center gap-3">
      <div class="stat-icon" style="background:#e3f2fd;color:#1565c0;"><i class="bi bi-check-circle"></i></div>
      <div><div class="stat-label">Paid This Month</div><div class="stat-value" style="font-size:1.4rem;"><?= (int)($stats['paid_this_month']??0) ?></div></div>
    </div>
  </div>
</div>

<!-- Filters -->
<div class="stat-card mb-3">
  <form method="GET" class="row g-2 align-items-end">
    <div class="col-sm-5"><input type="search" name="q" class="form-control form-control-sm" placeholder="Search student or invoice number…" value="<?= h($search) ?>"></div>
    <div class="col-sm-3">
      <select name="status" class="form-select form-select-sm">
        <option value="">All statuses</option>
        <?php foreach (['unpaid','paid','partial','overdue','cancelled'] as $s): ?><option value="<?= $s ?>" <?= $fStatus===$s?'selected':'' ?>><?= ucfirst($s) ?></option><?php endforeach; ?>
      </select>
    </div>
    <div class="col-sm-auto"><button class="btn btn-sm btn-dark">Filter</button> <a href="index.php" class="btn btn-sm btn-outline-secondary">Clear</a></div>
  </form>
</div>

<div class="tpa-table table-responsive mb-3">
  <table class="table table-hover mb-0">
    <thead><tr><th>Invoice #</th><th>Student</th><th>Period</th><th>Amount</th><th>Due</th><th>Status</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($invoices as $inv): ?>
        <tr>
          <td class="fw-700 small"><?= h($inv['invoice_number']) ?></td>
          <td class="fw-600"><?= h($inv['student_name']) ?></td>
          <td class="small"><?= h($inv['period_label'] ?? '—') ?></td>
          <td class="fw-700"><?= formatMoney($inv['amount_due']) ?></td>
          <td class="small <?= $inv['status']==='overdue'?'text-danger fw-700':'' ?>"><?= formatDate($inv['due_date']) ?></td>
          <td><?= invoiceStatusBadge($inv['status']) ?></td>
          <td>
            <div class="d-flex gap-1">
              <a href="record-payment.php?invoice_id=<?= $inv['id'] ?>" class="btn btn-sm btn-success py-0 px-2" style="font-size:.75rem;">Pay</a>
              <a href="reminders.php?invoice_id=<?= $inv['id'] ?>" class="btn btn-sm btn-outline-warning py-0 px-2" style="font-size:.75rem;"><i class="bi bi-bell"></i></a>
              <a href="download-invoice.php?id=<?= $inv['id'] ?>" target="_blank" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:.75rem;" title="Download PDF"><i class="bi bi-file-earmark-pdf"></i></a>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($invoices)): ?>
        <tr><td colspan="7" class="text-center text-muted py-4">No invoices found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?= paginate($total, $page, $perPage, '?q='.urlencode($search).'&status='.urlencode($fStatus)) ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
