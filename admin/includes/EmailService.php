<?php
// =====================================================
// TPA IMS — Email Service via PHPMailer SMTP
// =====================================================

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/PaymentService.php';

class EmailService {

    /**
     * Send an HTML email using SMTP settings stored in the DB
     */
    public static function send(string $toEmail, string $toName, string $subject, string $htmlBody, int $recipientId = 0, string $recipientType = 'parent'): bool {

        // Require Composer PHPMailer
        $autoload = dirname(__DIR__, 2) . '/vendor/autoload.php';
        if (!file_exists($autoload)) {
            self::logEmail($toEmail, $subject, $htmlBody, false, 'PHPMailer not installed. Run: composer require phpmailer/phpmailer', $recipientType, $recipientId);
            return false;
        }
        require_once $autoload;

        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = getSetting('smtp_host', 'smtp.gmail.com');
            $mail->SMTPAuth   = true;
            $mail->Username   = getSetting('smtp_user');
            $mail->Password   = getSetting('smtp_pass');
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = (int) getSetting('smtp_port', '587');

            $mail->setFrom(getSetting('smtp_from_email'), getSetting('smtp_from_name', 'Talent Pool Academy'));
            $mail->addAddress($toEmail, $toName);
            $mail->addReplyTo(getSetting('site_email', getSetting('smtp_from_email')), getSetting('site_name', 'TPA'));

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = self::wrapTemplate($subject, $htmlBody);
            $mail->AltBody = strip_tags($htmlBody);

            $mail->send();
            self::logEmail($toEmail, $subject, $htmlBody, true, null, $recipientType, $recipientId);
            return true;

        } catch (\Exception $e) {
            self::logEmail($toEmail, $subject, $htmlBody, false, $mail->ErrorInfo, $recipientType, $recipientId);
            return false;
        }
    }

    // ── Pre-built email templates ────────────────────────────────────────────

    public static function sendFeeReminder(string $toEmail, string $parentName, string $studentName, string $amount, string $dueDate, string $invoiceNum, string $period, string $paymentToken = '', int $recipientId = 0): bool {
        $subject = "Fee Reminder – {$invoiceNum} | " . getSetting('site_name','Academy');
        $html = self::feeReminderHtml($parentName, $studentName, $amount, $dueDate, $invoiceNum, $period, $paymentToken);
        return self::send($toEmail, $parentName, $subject, $html, $recipientId);
    }

    public static function sendAbsenceNotification(string $toEmail, string $parentName, string $studentName, string $batchName, string $date, int $recipientId = 0): bool {
        $subject = "Absence Notification – {$studentName} | " . getSetting('site_name','Academy');
        $html = self::absenceHtml($parentName, $studentName, $batchName, $date);
        return self::send($toEmail, $parentName, $subject, $html, $recipientId);
    }

    public static function sendInvoiceCreated(string $toEmail, string $parentName, string $studentName, string $amount, string $dueDate, string $invoiceNum, string $period, string $paymentToken = '', int $recipientId = 0): bool {
        $subject = "New Invoice – {$invoiceNum} | " . getSetting('site_name','Academy');
        $html = self::invoiceCreatedHtml($parentName, $studentName, $amount, $dueDate, $invoiceNum, $period, $paymentToken);
        return self::send($toEmail, $parentName, $subject, $html, $recipientId);
    }

    public static function sendPasswordReset(string $toEmail, string $name, string $resetLink): bool {
        $subject = "Reset Your Password | " . getSetting('site_name','Academy');
        $html = "<p>Hi {$name},</p><p>Click the link below to reset your password. This link expires in 1 hour.</p>
                 <p><a href=\"{$resetLink}\" style=\"background:#0A1628;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:700;\">Reset Password</a></p>
                 <p>If you did not request this, please ignore this email.</p>";
        return self::send($toEmail, $name, $subject, $html);
    }

    // ── HTML template content ────────────────────────────────────────────────

    private static function feeReminderHtml(string $parentName, string $studentName, string $amount, string $dueDate, string $invoiceNum, string $period, string $paymentToken = ''): string {
        $bank  = getSetting('bank_account');
        $sort  = getSetting('bank_sort_code');
        $bname = getSetting('bank_name');
        $phone = getSetting('site_phone');
        
        $paymentLinks = "";
        if ($paymentToken) {
            $url = PaymentService::getPublicPaymentUrl($paymentToken);
            $paymentLinks = "<div style='margin:1.5rem 0;padding:1.5rem;background:#f8fafc;border-radius:12px;border:1px solid #e2e8f0;'>
                <h3 style='margin:0 0 1rem 0;color:#0A1628;font-size:16px;'>💳 Pay Online Now</h3>
                <div style='display:flex;gap:12px;flex-wrap:wrap;'>
                    <a href='{$url}' style='background:#0A1628;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:700;font-size:14px;display:inline-block;'>Secure Online Payment</a>
                </div>
                <p style='margin-top:1rem;font-size:12px;color:#64748b;'>Card and Bank Transfer available online.</p>
            </div>";
        }

        return "<p>Dear {$parentName},</p>
        <p>This is a reminder that the following fee is due for <strong>{$studentName}</strong>:</p>
        <table style='border-collapse:collapse;width:100%;max-width:400px;margin:1rem 0;font-size:14px;'>
          <tr><td style='padding:8px;background:#f5f5f5;font-weight:600;'>Invoice #</td><td style='padding:8px;'>{$invoiceNum}</td></tr>
          <tr><td style='padding:8px;background:#f5f5f5;font-weight:600;'>Period</td><td style='padding:8px;'>{$period}</td></tr>
          <tr><td style='padding:8px;background:#f5f5f5;font-weight:600;'>Amount Due</td><td style='padding:8px;font-weight:700;color:#0A1628;'>{$amount}</td></tr>
          <tr><td style='padding:8px;background:#f5f5f5;font-weight:600;'>Due Date</td><td style='padding:8px;color:#c0392b;font-weight:700;'>{$dueDate}</td></tr>
        </table>
        {$paymentLinks}
        <p><strong>Bank Transfer Details:</strong><br>Account Name: {$bname}<br>Account Number: {$bank}<br>Sort Code: {$sort}<br>Reference: <strong>{$studentName} {$invoiceNum}</strong></p>
        <p>If you have any questions, please call us on {$phone} or reply to this email.</p>
        <p>Thank you for your continued support.</p>";
    }

    private static function absenceHtml(string $parentName, string $studentName, string $batchName, string $date): string {
        $phone = getSetting('site_phone');
        return "<p>Dear {$parentName},</p>
        <p>We are writing to let you know that <strong>{$studentName}</strong> was marked <strong style='color:#c0392b;'>absent</strong> from the following session:</p>
        <table style='border-collapse:collapse;width:100%;max-width:400px;margin:1rem 0;font-size:14px;'>
          <tr><td style='padding:8px;background:#f5f5f5;font-weight:600;'>Session</td><td style='padding:8px;'>{$batchName}</td></tr>
          <tr><td style='padding:8px;background:#f5f5f5;font-weight:600;'>Date</td><td style='padding:8px;'>{$date}</td></tr>
        </table>
        <p>If this is an error or you have already notified us, please disregard this message.</p>
        <p>To discuss, please call {$phone} or WhatsApp us.</p>";
    }

    private static function invoiceCreatedHtml(string $parentName, string $studentName, string $amount, string $dueDate, string $invoiceNum, string $period, string $paymentToken = ''): string {
        return self::feeReminderHtml($parentName, $studentName, $amount, $dueDate, $invoiceNum, $period, $paymentToken);
    }

    // ── Email wrapper ────────────────────────────────────────────────────────

    private static function wrapTemplate(string $subject, string $body): string {
        $logo = getSetting('site_name', 'Talent Pool Academy');
        $year = date('Y');
        return <<<HTML
<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<style>body{margin:0;padding:0;background:#f0f2f5;font-family:Arial,sans-serif;color:#333;}
.wrap{max-width:600px;margin:30px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08);}
.hdr{background:#0A1628;padding:24px 32px;}.hdr h1{color:#F5A623;margin:0;font-size:20px;}
.body{padding:32px;font-size:15px;line-height:1.6;}
.ftr{background:#f7f8fa;padding:20px 32px;font-size:12px;color:#888;border-top:1px solid #eee;text-align:center;}
</style></head><body>
<div class="wrap">
  <div class="hdr"><h1>{$logo}</h1></div>
  <div class="body">{$body}</div>
  <div class="ftr">© {$year} {$logo}. All rights reserved.</div>
</div></body></html>
HTML;
    }

    private static function logEmail(string $to, string $subject, string $body, bool $success, ?string $error, string $recipientType, int $recipientId): void {
        try {
            $db = getDB();
            $db->prepare('INSERT INTO communications (type, recipient_type, recipient_id, to_number_or_email, template_name, message, status, error_message, sent_by, sent_at)
                          VALUES (?,?,?,?,?,?,?,?,?,NOW())')
               ->execute([
                   'email', $recipientType, $recipientId ?: null, $to, $subject,
                   mb_substr($body, 0, 1000),
                   $success ? 'sent' : 'failed',
                   $error,
                   function_exists('currentUserId') ? currentUserId() : null,
               ]);
        } catch (Exception $e) { /* fail silently */ }
    }
}
