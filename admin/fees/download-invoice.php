<?php
// =====================================================
// TPA — Download / View PDF Invoice
// =====================================================
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
startSecureSession();
requireRole(['admin','branch_manager','staff','parent']);

$invId = (int)($_GET['id'] ?? 0);
if (!$invId) die('Invoice ID required');

$db = getDB();

// ── Load invoice with all joins ────────────────────────────────────────────
$inv = $db->prepare("SELECT i.*,
    CONCAT(s.first_name,' ',s.last_name) as student_name,
    s.first_name, s.last_name, s.student_ref, s.year_group,
    b.name as batch_name, b.day_of_week, b.start_time, b.end_time, b.centre,
    br.name as branch_name, br.address as branch_address,
    p.parent_name, p.email as parent_email, p.phone as parent_phone,
    f.name as fee_plan_name
    FROM invoices i
    JOIN students s ON s.id = i.student_id
    LEFT JOIN student_payment_schedules sps ON sps.id = i.schedule_id
    LEFT JOIN fee_structures f ON f.id = sps.fee_structure_id
    LEFT JOIN batch_students bs ON bs.student_id = s.id AND bs.is_active = 1
    LEFT JOIN batches b ON b.id = bs.batch_id
    LEFT JOIN branches br ON br.id = s.branch_id
    LEFT JOIN student_parents p ON p.student_id = s.id AND p.is_primary = 1
    WHERE i.id = ?
    LIMIT 1");
$inv->execute([$invId]);
$invoice = $inv->fetch();

if (!$invoice) die('Invoice not found');

// Parent authorization
if (currentRole() === 'parent') {
    $check = $db->prepare('SELECT 1 FROM student_parents sp JOIN users u ON u.email=sp.email WHERE u.id=? AND sp.student_id=?');
    $check->execute([currentUserId(), $invoice['student_id']]);
    if (!$check->fetchColumn()) die('Unauthorized');
}

// ── Payment summary ────────────────────────────────────────────────────────
$payStmt = $db->prepare('SELECT COALESCE(SUM(amount),0) FROM payments WHERE invoice_id=?');
$payStmt->execute([$invId]);
$totalPaid = (float)$payStmt->fetchColumn();
$balanceDue = max(0, (float)$invoice['amount_due'] - $totalPaid);

// ── Settings – pull real bank / contact details ────────────────────────────
$acadName   = getSetting('site_name',  'Talent Pool Academy');
$bankName   = getSetting('bank_name',  'Talent Pool Academy');
$bankAcc    = getSetting('bank_account', '—');
$bankSort   = getSetting('bank_sort_code', '—');
$bacsRef    = getSetting('bacs_reference_prefix','TPA');
$sitePhone  = getSetting('site_phone', '');
$siteEmail  = getSetting('site_email', '');
$addrRom    = getSetting('site_address_romford','');

// ── Template fields ────────────────────────────────────────────────────────
$dueDate    = date('d F Y', strtotime($invoice['due_date']));
$issueDate  = date('d F Y', strtotime($invoice['created_at']));
$amount     = number_format((float)$invoice['amount'], 2);
$amountDue  = number_format((float)$invoice['amount_due'], 2);
$paidStr    = number_format($totalPaid, 2);
$balStr     = number_format($balanceDue, 2);
$status     = strtoupper($invoice['status']);
$_sc = $invoice['status'];
if ($_sc === 'paid')         $statusClr = '#16a34a';
elseif ($_sc === 'overdue')  $statusClr = '#dc2626';
elseif ($_sc === 'partial')  $statusClr = '#d97706';
else                         $statusClr = '#2563eb';
$description = $invoice['fee_plan_name'] ?? $invoice['period_label'] ?? 'Tuition Fee';
$batchInfo   = $invoice['batch_name']
                ? $invoice['batch_name'] . ($invoice['day_of_week'] ? ' (' . $invoice['day_of_week'] . ')' : '')
                : 'See enrolment';

// Payment rows
$paymentsHtml = '';
if ($totalPaid > 0) {
    $pRows = $db->prepare('SELECT amount, method, payment_date, notes FROM payments WHERE invoice_id=? ORDER BY payment_date');
    $pRows->execute([$invId]);
    foreach ($pRows->fetchAll() as $pr) {
        $paymentsHtml .= "<tr>
            <td>Payment received – " . htmlspecialchars($pr['method']) . "</td>
            <td>" . htmlspecialchars($invoice['batch_name'] ?? '') . "</td>
            <td style='text-align:right;color:#16a34a;'>– £" . number_format($pr['amount'], 2) . "</td>
        </tr>";
    }
}

$html = <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  * { box-sizing: border-box; }
  body { font-family: Helvetica, Arial, sans-serif; font-size: 13px; color: #1f2937; line-height: 1.5; margin: 0; padding: 0; }
  .page { padding: 40px 48px; max-width: 800px; }
  .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #F5A623; padding-bottom: 24px; margin-bottom: 32px; }
  .brand h1 { margin: 0; color: #0A1628; font-size: 26px; font-weight: 900; letter-spacing: -0.5px; }
  .brand h1 span { color: #F5A623; }
  .brand p  { margin: 4px 0 0; color: #6b7280; font-size: 11px; line-height: 1.6; }
  .inv-tag  { text-align: right; }
  .inv-tag .inv-num { font-size: 22px; font-weight: 900; color: #0A1628; }
  .inv-tag .inv-date{ font-size: 11px; color: #6b7280; margin-top: 4px; }
  .status-pill { display: inline-block; padding: 4px 14px; border-radius: 999px; color: #fff;
                 font-weight: 700; font-size: 11px; background: {$statusClr}; margin-top: 6px; letter-spacing: .05em; }
  .parties { display: flex; gap: 40px; margin-bottom: 32px; }
  .party { flex: 1; }
  .party-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: #9ca3af; margin-bottom: 6px; }
  .party-name  { font-size: 14px; font-weight: 700; color: #0A1628; margin-bottom: 2px; }
  .party-detail{ font-size: 12px; color: #4b5563; }
  table.items { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
  table.items th { background: #0A1628; color: rgba(255,255,255,.85); padding: 10px 14px;
                   text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .06em; }
  table.items td { padding: 11px 14px; border-bottom: 1px solid #f3f4f6; font-size: 13px; }
  table.items tr:last-child td { border-bottom: none; }
  .total-section { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px 20px; margin-bottom: 28px; }
  .total-row { display: flex; justify-content: space-between; padding: 4px 0; font-size: 13px; }
  .total-row.bold { font-weight: 700; font-size: 16px; color: #0A1628; border-top: 1px solid #d1d5db; padding-top: 10px; margin-top: 6px; }
  .total-row .label { color: #6b7280; }
  .bank-box { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 18px 20px; margin-bottom: 28px; }
  .bank-box h4 { color: #166534; margin: 0 0 10px; font-size: 13px; text-transform: uppercase; letter-spacing: .05em; }
  .bank-row { display: flex; gap: 0; font-size: 12px; margin-bottom: 4px; }
  .bank-row .lbl { color: #6b7280; width: 140px; flex-shrink: 0; }
  .bank-row .val { font-weight: 700; color: #111827; }
  .ref-box { background: #fff; border: 2px dashed #d1fae5; border-radius: 6px; padding: 4px 10px; display: inline-block; margin-top: 4px; font-weight: 900; color: #065f46; letter-spacing: .08em; font-size: 13px; }
  .footer { border-top: 1px solid #e5e7eb; padding-top: 16px; font-size: 11px; color: #9ca3af; text-align: center; margin-top: 12px; }
</style>
</head>
<body>
<div class="page">

  <!-- HEADER -->
  <div class="header">
    <div class="brand">
      <h1>{$acadName}</h1>
      <p>{$addrRom}<br>{$sitePhone} &nbsp;|&nbsp; {$siteEmail}</p>
    </div>
    <div class="inv-tag">
      <div style="font-size:11px;color:#9ca3af;font-weight:700;letter-spacing:.08em;text-transform:uppercase;">Invoice</div>
      <div class="inv-num">{$invoice['invoice_number']}</div>
      <div class="inv-date">Issued: {$issueDate}</div>
      <div class="inv-date" style="color:#dc2626;">Due: {$dueDate}</div>
      <div><span class="status-pill">{$status}</span></div>
    </div>
  </div>

  <!-- PARTIES -->
  <div class="parties">
    <div class="party">
      <div class="party-label">Billed To</div>
      <div class="party-name">{$invoice['parent_name']}</div>
      <div class="party-detail">
        <strong>{$invoice['student_name']}</strong><br>
        Ref: <strong>{$invoice['student_ref']}</strong><br>
        Year: {$invoice['year_group']}<br>
        {$invoice['branch_name']}
      </div>
    </div>
    <div class="party">
      <div class="party-label">Invoice Details</div>
      <div class="party-detail">
        <strong>Period:</strong> {$invoice['period_label']}<br>
        <strong>Batch:</strong> {$batchInfo}<br>
        <strong>Invoice #:</strong> {$invoice['invoice_number']}
      </div>
    </div>
  </div>

  <!-- LINE ITEMS -->
  <table class="items">
    <thead>
      <tr>
        <th>Description</th>
        <th width="30%">Class / Batch</th>
        <th width="18%" style="text-align:right;">Amount</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Tuition Fees – {$description}</td>
        <td>{$batchInfo}</td>
        <td style="text-align:right;">£{$amount}</td>
      </tr>
      {$paymentsHtml}
    </tbody>
  </table>

  <!-- TOTALS -->
  <div class="total-section">
    <div class="total-row"><span class="label">Invoice Amount</span><span>£{$amount}</span></div>
    <div class="total-row"><span class="label">Discount</span><span>– £{$invoice['discount']}</span></div>
    <div class="total-row"><span class="label">Payments Received</span><span style="color:#16a34a;">– £{$paidStr}</span></div>
    <div class="total-row bold"><span>Balance Due</span><span style="color:{$statusClr};">£{$balStr}</span></div>
  </div>

  <!-- BANK DETAILS -->
  <div class="bank-box">
    <h4>💳 How to Pay — BACS Transfer</h4>
    <div class="bank-row"><span class="lbl">Account Name:</span><span class="val">{$bankName}</span></div>
    <div class="bank-row"><span class="lbl">Account Number:</span><span class="val">{$bankAcc}</span></div>
    <div class="bank-row"><span class="lbl">Sort Code:</span><span class="val">{$bankSort}</span></div>
    <div class="bank-row"><span class="lbl">Payment Reference:</span>
      <span class="val"><span class="ref-box">{$invoice['invoice_number']}</span></span>
    </div>
  </div>

  <!-- FOOTER -->
  <div class="footer">
    Thank you for choosing {$acadName}. All fees are due by the date shown above.<br>
    Questions? Contact us on {$sitePhone} or {$siteEmail}
  </div>

</div>
</body>
</html>
HTML;

// ── Dompdf ─────────────────────────────────────────────────────────────────
$autoload = dirname(__DIR__, 2) . '/vendor/autoload.php';
if (!file_exists($autoload)) {
    http_response_code(500);
    die('PDF library not installed. Please run: <code>composer require dompdf/dompdf</code> in the project root.');
}
require_once $autoload;

$options = new \Dompdf\Options();
$options->set('isRemoteEnabled', false);
$options->set('isHtml5ParserEnabled', true);
$options->set('defaultFont', 'Helvetica');
$dompdf = new \Dompdf\Dompdf($options);
$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$filename = 'Invoice_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $invoice['invoice_number']) . '.pdf';
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');
echo $dompdf->output();
exit;
