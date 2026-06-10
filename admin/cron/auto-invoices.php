#!/usr/bin/env php
<?php
// =====================================================
// TPA IMS — Cron Job: Automated Invoice Generation
// Schedule: Run nightly at midnight
//   crontab: 0 0 * * * /usr/bin/php /path/to/tpaAG/admin/cron/auto-invoices.php
// =====================================================

require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/WhatsAppService.php';
require_once dirname(__DIR__) . '/includes/EmailService.php';

$db      = getDB();
$today   = date('Y-m-d');
$created = 0;

echo "[" . date('Y-m-d H:i:s') . "] Auto-invoice cron started\n";

// Fetch active payment schedules where next_invoice_date = today or earlier
$schedules = $db->prepare("SELECT sps.*, fs.name as fee_name, fs.amount, fs.frequency,
    CONCAT(s.first_name,' ',s.last_name) as student_name, s.id as student_id,
    p.parent_name, p.email, p.phone, p.whatsapp, p.id as parent_id
    FROM student_payment_schedules sps
    JOIN fee_structures fs ON sps.fee_structure_id = fs.id
    JOIN students s ON sps.student_id = s.id
    LEFT JOIN student_parents p ON p.student_id = s.id AND p.is_primary = 1
    WHERE sps.is_active = 1 AND sps.auto_generate = 1
      AND sps.next_invoice_date <= ? AND s.status = 'active'
      AND (sps.end_date IS NULL OR sps.end_date >= ?)");
$schedules->execute([$today, $today]);
$schedules = $schedules->fetchAll();

$wa = new WhatsAppService();

foreach ($schedules as $sch) {
    $invoiceNum = generateInvoiceNumber();
    $dueDate    = $sch['next_invoice_date'];
    $amount     = (float) $sch['amount'];

    // Period label
    $periodLabel = match($sch['frequency']) {
        'monthly'     => date('F Y', strtotime($dueDate)),
        'termly'      => getTermLabel($dueDate),
        'half_termly' => 'Half Term ' . date('M Y', strtotime($dueDate)),
        'annual'      => 'Annual 20' . date('Y', strtotime($dueDate)),
        default       => date('F Y', strtotime($dueDate)),
    };

    // Create invoice
    $db->prepare("INSERT INTO invoices (invoice_number, student_id, schedule_id, amount, discount, amount_due, period_label, due_date, status, created_by)
                  VALUES (?,?,?,?,0,?,?,?,'unpaid',0)")
       ->execute([$invoiceNum, $sch['student_id'], $sch['id'], $amount, $amount, $periodLabel, $dueDate]);

    echo "  Created invoice $invoiceNum for {$sch['student_name']} (£$amount due $dueDate)\n";
    $created++;

    // Notify parent via WhatsApp + email
    if ($sch['whatsapp'] ?: $sch['phone']) {
        $wa->sendText($sch['whatsapp'] ?: $sch['phone'],
            "Dear {$sch['parent_name']},\n\nYour invoice for {$sch['student_name']} is now available.\n\n📋 Invoice: $invoiceNum\n💰 Amount: £$amount\n📅 Due: $dueDate\n\nPlease pay via BACS:\n" . getSetting('bank_name') . "\nAcc: " . getSetting('bank_account') . "\nSort: " . getSetting('bank_sort_code') . "\nRef: {$sch['student_name']}\n\nThank you - Talent Pool Academy",
            $sch['parent_id']);
    }
    if ($sch['email']) {
        EmailService::sendInvoiceCreated($sch['email'], $sch['parent_name'], $sch['student_name'], formatMoney($amount), $dueDate, $invoiceNum, $periodLabel, $sch['parent_id']);
    }

    // Calculate next invoice date
    $nextDate = calculateNextDate($sch['next_invoice_date'], $sch['frequency']);
    $db->prepare('UPDATE student_payment_schedules SET next_invoice_date = ? WHERE id = ?')
       ->execute([$nextDate, $sch['id']]);
}

// Mark unpaid invoices past due date as overdue
$marked = $db->prepare("UPDATE invoices SET status = 'overdue' WHERE status = 'unpaid' AND due_date < ?")->execute([$today]);
echo "  Marked " . $db->query('SELECT ROW_COUNT()')->fetchColumn() . " invoices as overdue\n";

echo "[" . date('Y-m-d H:i:s') . "] Done. Created $created invoices.\n";

// ── Helpers ──────────────────────────────────────────

function calculateNextDate(string $fromDate, string $frequency): string {
    return match($frequency) {
        'per_session' => date('Y-m-d', strtotime($fromDate . ' +7 days')),
        'weekly'      => date('Y-m-d', strtotime($fromDate . ' +7 days')),
        'fortnightly' => date('Y-m-d', strtotime($fromDate . ' +14 days')),
        'monthly'     => date('Y-m-d', strtotime($fromDate . ' +1 month')),
        'half_termly' => date('Y-m-d', strtotime($fromDate . ' +7 weeks')),
        'termly'      => date('Y-m-d', strtotime($fromDate . ' +4 months')),
        'annual'      => date('Y-m-d', strtotime($fromDate . ' +1 year')),
        default       => date('Y-m-d', strtotime($fromDate . ' +1 month')),
    };
}

function getTermLabel(string $date): string {
    $m = (int) date('n', strtotime($date));
    $y = date('Y', strtotime($date));
    if ($m >= 9)  return "Autumn Term $y";
    if ($m >= 4)  return "Summer Term $y";
    return "Spring Term $y";
}
