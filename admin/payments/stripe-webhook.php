<?php
// =====================================================
// TPA IMS — Stripe Webhook Receiver
// URL: https://yourdomain.com/tpaAG/admin/payments/stripe-webhook.php
// =====================================================

// NO session / auth — this is called by Stripe servers
define('BASEPATH', dirname(__DIR__));
require_once BASEPATH . '/includes/config.php';
require_once BASEPATH . '/includes/db.php';
require_once BASEPATH . '/includes/functions.php';

// Log to file for debugging
function webhookLog(string $msg): void {
    file_put_contents(BASEPATH . '/logs/stripe-webhook.log',
        '[' . date('Y-m-d H:i:s') . '] ' . $msg . "\n", FILE_APPEND | LOCK_EX);
}

$db             = getDB();
$webhookSecret  = getSetting('stripe_webhook_secret');
$secretKey      = getSetting('stripe_secret_key');
$payload        = @file_get_contents('php://input');
$sigHeader      = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

if (!$payload) { http_response_code(400); exit('No payload'); }

// ── Verify Stripe signature ────────────────────────────────────────────────
if ($webhookSecret) {
    $timestamp  = null;
    $signatures = [];
    foreach (explode(',', $sigHeader) as $part) {
        [$key, $val] = array_pad(explode('=', $part, 2), 2, '');
        if ($key === 't')  $timestamp    = $val;
        if ($key === 'v1') $signatures[] = $val;
    }
    if (!$timestamp || empty($signatures)) {
        webhookLog("REJECTED: Missing signature header");
        http_response_code(400); exit('Invalid signature header');
    }
    $expectedSig = hash_hmac('sha256', "{$timestamp}.{$payload}", $webhookSecret);
    if (!in_array($expectedSig, $signatures, true)) {
        webhookLog("REJECTED: Signature mismatch");
        http_response_code(403); exit('Signature mismatch');
    }
    if (abs(time() - (int)$timestamp) > 300) {
        webhookLog("REJECTED: Timestamp too old ({$timestamp})");
        http_response_code(400); exit('Stale timestamp');
    }
}

$event = json_decode($payload, true);
if (!$event) { http_response_code(400); exit('Invalid JSON'); }

$eventId   = $event['id']   ?? uniqid('stripe_', true);
$eventType = $event['type'] ?? '';
webhookLog("Received event: {$eventId} type={$eventType}");

// ── Handle events ─────────────────────────────────────────────────────────
http_response_code(200); // Always 200 first so Stripe doesn't retry on our errors

try {
    switch ($eventType) {

        case 'payment_intent.succeeded':
        case 'checkout.session.completed': {
            $obj = $event['data']['object'];

            // Extract key fields
            $amountPence  = $obj['amount_total'] ?? $obj['amount_received'] ?? $obj['amount'] ?? 0;
            $amount       = round($amountPence / 100, 2);
            $currency     = strtoupper($obj['currency'] ?? 'GBP');
            $gatewayPayId = $obj['id'] ?? $eventId;
            $payDate      = date('Y-m-d', $obj['created'] ?? time());
            // Metadata: invoice_id and student_id should be embedded by the checkout session creator
            $meta         = $obj['metadata'] ?? [];
            $invoiceId    = (int)($meta['invoice_id'] ?? 0);
            $studentId    = (int)($meta['student_id'] ?? 0);
            $parentEmail  = $obj['customer_email'] ?? ($obj['receipt_email'] ?? null);

            webhookLog("Payment {$gatewayPayId}: £{$amount} for invoice_id={$invoiceId}");

            // Lookup invoice if not in metadata — try by student email
            if (!$invoiceId && $parentEmail) {
                $lookup = $db->prepare("SELECT i.id FROM invoices i
                    JOIN students s ON s.id=i.student_id
                    JOIN student_parents p ON p.student_id=s.id
                    WHERE p.email=? AND i.status IN ('unpaid','overdue','partial')
                    ORDER BY i.due_date ASC LIMIT 1");
                $lookup->execute([$parentEmail]);
                $invoiceId = (int)$lookup->fetchColumn();
            }

            // Idempotency: skip if already processed
            $existing = $db->prepare('SELECT id FROM payments WHERE gateway_payment_id=?');
            $existing->execute([$gatewayPayId]);
            if ($existing->fetchColumn()) {
                webhookLog("SKIP: already processed {$gatewayPayId}");
                break;
            }

            if ($invoiceId) {
                // Insert payment
                $db->prepare('INSERT INTO payments (invoice_id, student_id, amount, method, payment_date, gateway, gateway_payment_id, gateway_event_id, reconciled, notes, recorded_by)
                    VALUES (?,?,?,?,?,?,?,?,1,?,0)')
                   ->execute([$invoiceId, $studentId ?: null, $amount, 'Stripe', $payDate,
                        'stripe', $gatewayPayId, $eventId, "Auto-reconciled via Stripe webhook: {$eventId}"]);

                // Recalculate invoice status
                $inv = $db->prepare('SELECT amount_due, student_id FROM invoices WHERE id=?');
                $inv->execute([$invoiceId]); $inv = $inv->fetch();
                $totalPaid = (float)$db->prepare('SELECT COALESCE(SUM(amount),0) FROM payments WHERE invoice_id=?')
                               ->execute([$invoiceId]) ? $db->query("SELECT COALESCE(SUM(amount),0) FROM payments WHERE invoice_id={$invoiceId}")->fetchColumn() : 0;

                $newStatus = $totalPaid >= $inv['amount_due'] ? 'paid' : ($totalPaid > 0 ? 'partial' : 'unpaid');
                $db->prepare('UPDATE invoices SET status=?, updated_at=NOW() WHERE id=?')->execute([$newStatus, $invoiceId]);

                // Log to communications
                $db->prepare("INSERT INTO communications (type, recipient_type, recipient_id, to_number_or_email, template_name, message, status, meta_message_id, sent_by, sent_at)
                    VALUES ('email','parent',?,?,'stripe_payment_reconciled',?,?,?,NULL,NOW())")
                   ->execute([$studentId ?: 0, $parentEmail ?? 'stripe', "Stripe payment reconciled: £{$amount}", 'sent', $eventId]);

                webhookLog("SUCCESS: Invoice {$invoiceId} → {$newStatus} (£{$amount})");
            } else {
                // Unmatched — store for manual reconciliation
                $db->prepare('INSERT INTO payments (invoice_id, student_id, amount, method, payment_date, gateway, gateway_payment_id, gateway_event_id, reconciled, notes, recorded_by)
                    VALUES (NULL, NULL, ?, ?, ?, ?, ?, ?, 0, ?, 0)')
                   ->execute([$amount, 'Stripe', $payDate, 'stripe', $gatewayPayId, $eventId,
                        "Stripe payment received — no matching invoice. Email: {$parentEmail}"]);
                webhookLog("UNMATCHED: No invoice found for payment {$gatewayPayId} (email={$parentEmail})");
            }
            break;
        }

        case 'payment_intent.payment_failed': {
            $obj          = $event['data']['object'];
            $gatewayPayId = $obj['id'] ?? $eventId;
            $failReason   = $obj['last_payment_error']['message'] ?? 'Unknown';
            webhookLog("Payment failed: {$gatewayPayId} - {$failReason}");
            // Log the failure
            $db->prepare("INSERT INTO communications (type,recipient_type,recipient_id,to_number_or_email,template_name,message,status,meta_message_id,sent_by,sent_at)
                VALUES ('email','parent',0,'stripe','payment_failed',?,?,?,NULL,NOW())")
               ->execute(["Stripe payment failed: {$gatewayPayId} — {$failReason}", 'failed', $eventId]);
            break;
        }

        // Ignore all other events
        default:
            webhookLog("IGNORED event type: {$eventType}");
    }
} catch (Exception $e) {
    webhookLog("ERROR: " . $e->getMessage());
    // Still return 200 to prevent Stripe retries on our internal errors
}

echo json_encode(['received' => true]);
