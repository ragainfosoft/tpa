#!/usr/bin/env php
<?php
// =====================================================
// TPA IMS — Cron: Auto-send Reminders
// Schedule: 0 8 * * *  (8am daily)
//   /usr/bin/php /path/to/tpaAG/admin/cron/send-reminders.php
// =====================================================

require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/WhatsAppService.php';
require_once dirname(__DIR__) . '/includes/EmailService.php';

$db    = getDB();
$today = date('Y-m-d');
$now   = date('Y-m-d H:i:s');
$log   = [];
$sent  = 0;
$errors = 0;

echo "[{$now}] Auto-reminder cron started\n";

// ──────────────────────────────────────────────────────────────────────────
// 1. FEE REMINDERS
// ──────────────────────────────────────────────────────────────────────────
if (getSetting('reminder_fee_enabled', '1') === '1') {

    $daysBefore = (int) getSetting('reminder_fee_days_before', '3');
    $resendDays = (int) getSetting('reminder_fee_overdue_resend', '7');
    $channel    = getSetting('reminder_fee_channel', 'both');
    $tplMsg     = getSetting('wa_template_fee_reminder', '');

    // a) Due-soon reminders (due in ≤ N days, not yet reminded)
    $remindDate = date('Y-m-d', strtotime("+{$daysBefore} days"));
    $dueSoon = $db->prepare("SELECT i.id, i.invoice_number, i.amount_due, i.due_date, i.period_label, i.reminder_sent_at,
        CONCAT(s.first_name,' ',s.last_name) as student_name,
        p.id as parent_id, p.parent_name, p.email, p.phone, p.whatsapp
        FROM invoices i JOIN students s ON s.id=i.student_id
        LEFT JOIN student_parents p ON p.student_id=s.id AND p.is_primary=1
        WHERE i.status IN ('unpaid','partial')
          AND i.due_date <= ?
          AND (i.reminder_sent_at IS NULL)");
    $dueSoon->execute([$remindDate]); $dueSoon = $dueSoon->fetchAll();

    // b) Overdue — re-send if last reminder > N days ago
    $resendCutoff = date('Y-m-d', strtotime("-{$resendDays} days"));
    $overdue = $db->prepare("SELECT i.id, i.invoice_number, i.amount_due, i.due_date, i.period_label, i.reminder_sent_at,
        CONCAT(s.first_name,' ',s.last_name) as student_name,
        p.id as parent_id, p.parent_name, p.email, p.phone, p.whatsapp
        FROM invoices i JOIN students s ON s.id=i.student_id
        LEFT JOIN student_parents p ON p.student_id=s.id AND p.is_primary=1
        WHERE i.status = 'overdue'
          AND (i.reminder_sent_at IS NULL OR DATE(i.reminder_sent_at) <= ?)");
    $overdue->execute([$resendCutoff]); $overdue = $overdue->fetchAll();

    $toRemind = array_merge($dueSoon, $overdue);
    $wa       = new WhatsAppService();

    foreach ($toRemind as $inv) {
        $amount  = formatMoney($inv['amount_due']);
        $dueDate = formatDate($inv['due_date']);
        try {
            if (in_array($channel, ['whatsapp','both']) && ($inv['whatsapp'] ?: $inv['phone'])) {
                $msg = str_replace(
                    ['{parent_name}','{child_name}','{amount}','{due_date}','{invoice_number}'],
                    [$inv['parent_name'],$inv['student_name'],$amount,$dueDate,$inv['invoice_number']],
                    $tplMsg
                );
                $wa->sendText($inv['whatsapp'] ?: $inv['phone'], $msg, $inv['parent_id']);
                $sent++;
            }
            if (in_array($channel, ['email','both']) && $inv['email']) {
                EmailService::sendFeeReminder($inv['email'], $inv['parent_name'], $inv['student_name'],
                    $amount, $dueDate, $inv['invoice_number'], $inv['period_label'] ?? 'Tuition Fee', $inv['parent_id']);
                $sent++;
            }
            $db->prepare('UPDATE invoices SET reminder_sent_at=NOW(), reminder_count=reminder_count+1 WHERE id=?')->execute([$inv['id']]);
            $log[] = "✓ Fee reminder → {$inv['student_name']} ({$inv['invoice_number']}, £{$inv['amount_due']})";
        } catch (Exception $e) {
            $errors++;
            $log[] = "✗ Failed for {$inv['student_name']}: " . $e->getMessage();
        }
    }
} else {
    $log[] = "» Fee reminders disabled in settings.";
}

// ──────────────────────────────────────────────────────────────────────────
// 2. ABSENCE NOTIFICATIONS
// ──────────────────────────────────────────────────────────────────────────
if (getSetting('reminder_absence_enabled', '1') === '1') {
    $absChannel = getSetting('reminder_absence_channel', 'both');
    $absTpl     = getSetting('wa_template_absence', '');

    // Absences from yesterday not yet notified
    $yesterday  = date('Y-m-d', strtotime('-1 day'));
    $absences = $db->prepare("SELECT a.id, a.date,
        CONCAT(s.first_name,' ',s.last_name) as student_name,
        b.name as batch_name,
        p.id as parent_id, p.parent_name, p.email, p.phone, p.whatsapp
        FROM attendance a JOIN students s ON s.id=a.student_id
        JOIN batches b ON b.id=a.batch_id
        LEFT JOIN student_parents p ON p.student_id=s.id AND p.is_primary=1
        WHERE a.status='absent' AND a.date=? AND a.notified_at IS NULL");
    $absences->execute([$yesterday]); $absences = $absences->fetchAll();

    $wa = isset($wa) ? $wa : new WhatsAppService();

    foreach ($absences as $a) {
        $dateStr = date('l j F Y', strtotime($a['date']));
        try {
            if (in_array($absChannel, ['whatsapp','both']) && ($a['whatsapp'] ?: $a['phone'])) {
                $msg = str_replace(
                    ['{parent_name}','{child_name}','{batch_name}','{date}'],
                    [$a['parent_name'],$a['student_name'],$a['batch_name'],$dateStr],
                    $absTpl
                );
                $wa->sendText($a['whatsapp'] ?: $a['phone'], $msg, $a['parent_id']);
                $sent++;
            }
            if (in_array($absChannel, ['email','both']) && $a['email']) {
                EmailService::sendAbsenceNotification($a['email'], $a['parent_name'],
                    $a['student_name'], $a['batch_name'], $dateStr, $a['parent_id']);
                $sent++;
            }
            $db->prepare('UPDATE attendance SET notified_at=NOW() WHERE id=?')->execute([$a['id']]);
            $log[] = "✓ Absence alert → {$a['student_name']} ({$a['batch_name']}, {$yesterday})";
        } catch (Exception $e) {
            $errors++;
            $log[] = "✗ Absence failed for {$a['student_name']}: " . $e->getMessage();
        }
    }
} else {
    $log[] = "» Absence notifications disabled in settings.";
}

// ──────────────────────────────────────────────────────────────────────────
// Mark overdue invoices
// ──────────────────────────────────────────────────────────────────────────
$db->prepare("UPDATE invoices SET status='overdue' WHERE status='unpaid' AND due_date < ?")->execute([$today]);
$markedOverdue = $db->query('SELECT ROW_COUNT()')->fetchColumn();
if ($markedOverdue > 0) $log[] = "» Marked {$markedOverdue} invoice(s) as overdue.";

// ──────────────────────────────────────────────────────────────────────────
// Summary
// ──────────────────────────────────────────────────────────────────────────
foreach ($log as $line) echo "  $line\n";
echo "\n[" . date('Y-m-d H:i:s') . "] Done. Sent: $sent | Errors: $errors\n";
