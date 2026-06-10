<?php
// =====================================================
// TPA IMS — GoCardless Webhook Receiver
// URL: https://yourdomain.com/tpaAG/admin/payments/gocardless-webhook.php
// Docs: https://developer.gocardless.com/api-reference#making-requests-webhooks
// =====================================================

define('BASEPATH', dirname(__DIR__));
require_once BASEPATH . '/includes/config.php';
require_once BASEPATH . '/includes/db.php';
require_once BASEPATH . '/includes/functions.php';

function gcLog(string $msg): void {
    @mkdir(BASEPATH . '/logs', 0755, true);
    file_put_contents(BASEPATH . '/logs/gocardless-webhook.log',
        '[' . date('Y-m-d H:i:s') . '] ' . $msg . "\n", FILE_APPEND | LOCK_EX);
}

$db            = getDB();
$webhookSecret = getSetting('gocardless_webhook_secret');
$payload       = @file_get_contents('php://input');
$signature     = $_SERVER['HTTP_WEBHOOK_SIGNATURE'] ?? '';

if (!$payload) { http_response_code(400); exit('No payload'); }

// ── Verify GoCardless webhook signature ──────────────────────────────────
if ($webhookSecret) {
    $expected = hash_hmac('sha256', $payload, $webhookSecret);
    if (!hash_equals($expected, $signature)) {
        gcLog("REJECTED: Signature mismatch. Got={$signature}");
        http_response_code(498); exit('Invalid signature');
    }
}

$body = json_decode($payload, true);
if (!$body) { http_response_code(400); exit('Invalid JSON'); }

http_response_code(200); // Acknowledge immediately

$events = $body['events'] ?? [];
gcLog("Received " . count($events) . " event(s)");

foreach ($events as $event) {
    $eventId      = $event['id'] ?? '';
    $resourceType = $event['resource_type'] ?? '';
    $action       = $event['action'] ?? '';
    $links        = $event['links'] ?? [];

    gcLog("Event {$eventId}: {$resourceType}.{$action}");

    // Idempotency
    try {
        $exists = $db->prepare('SELECT id FROM payments WHERE gateway_event_id=?');
        $exists->execute([$eventId]);
        if ($exists->fetchColumn()) { gcLog("SKIP duplicate: {$eventId}"); continue; }
    } catch (Exception $e) {}

    try {
        // ── Payment confirmed ──────────────────────────────────────────
        if ($resourceType === 'payments' && in_array($action, ['paid_out', 'confirmed'])) {
            $gcPaymentId = $links['payment'] ?? '';
            $amount      = (float)(($event['details']['amount'] ?? 0) / 100); // pence → £
            $payDate     = $event['created_at'] ? date('Y-m-d', strtotime($event['created_at'])) : date('Y-m-d');

            // Look up invoice by gc_payment_id stored in invoice notes or metadata
            // Convention: when creating GC mandate, store invoice_id in description
            $invoiceId = 0;
            $metaRef   = $event['details']['description'] ?? '';
            if (preg_match('/TPA-\d+/', $metaRef, $m)) {
                $stmt = $db->prepare('SELECT id FROM invoices WHERE invoice_number=? LIMIT 1');
                $stmt->execute([$m[0]]); $invoiceId = (int)$stmt->fetchColumn();
            }
            // Also check links.mandate → student lookup
            if (!$invoiceId && isset($links['mandate'])) {
                $gcMandateId = $links['mandate'];
                $stmt = $db->prepare("SELECT i.id FROM invoices i
                    JOIN student_payment_schedules sps ON sps.student_id=i.student_id
                    WHERE sps.gocardless_mandate_id=? AND i.status IN ('unpaid','overdue','partial')
                    ORDER BY i.due_date ASC LIMIT 1");
                $stmt->execute([$gcMandateId]);
                $invoiceId = (int)$stmt->fetchColumn();
            }

            if ($invoiceId) {
                $inv = $db->prepare('SELECT amount_due, student_id FROM invoices WHERE id=?');
                $inv->execute([$invoiceId]); $inv = $inv->fetch();

                $db->prepare('INSERT INTO payments (invoice_id, student_id, amount, method, payment_date, gateway, gateway_payment_id, gateway_event_id, reconciled, notes, recorded_by)
                    VALUES (?,?,?,?,?,?,?,?,1,?,0)')
                   ->execute([$invoiceId, $inv['student_id'] ?? 0, $amount, 'GoCardless', $payDate,
                        'gocardless', $gcPaymentId, $eventId,
                        "Auto-reconciled via GoCardless webhook: {$eventId}"]);

                // Recalculate status
                $totalPaid = (float)$db->query("SELECT COALESCE(SUM(amount),0) FROM payments WHERE invoice_id={$invoiceId}")->fetchColumn();
                $newStatus = $totalPaid >= $inv['amount_due'] ? 'paid' : ($totalPaid > 0 ? 'partial' : 'unpaid');
                $db->prepare('UPDATE invoices SET status=?, updated_at=NOW() WHERE id=?')->execute([$newStatus, $invoiceId]);

                gcLog("SUCCESS: Invoice {$invoiceId} → {$newStatus} (£{$amount} via GC {$gcPaymentId})");
            } else {
                // Unmatched — store anyway
                $db->prepare('INSERT INTO payments (invoice_id, student_id, amount, method, payment_date, gateway, gateway_payment_id, gateway_event_id, reconciled, notes, recorded_by)
                    VALUES (NULL, NULL, ?, ?, ?, ?, ?, ?, 0, ?, 0)')
                   ->execute([$amount, 'GoCardless', $payDate, 'gocardless', $gcPaymentId, $eventId,
                        "GoCardless payment received — no matching invoice. GC ID: {$gcPaymentId}"]);
                gcLog("UNMATCHED: GC payment {$gcPaymentId} £{$amount}");
            }
        }

        // ── Payment failed / charged back ─────────────────────────────
        elseif ($resourceType === 'payments' && in_array($action, ['failed','charged_back','customer_approval_denied'])) {
            gcLog("Payment problem: {$action} for GC event {$eventId}");
            // Could send alert to admin — for now just log
        }

    } catch (Exception $e) {
        gcLog("ERROR processing {$eventId}: " . $e->getMessage());
    }
}

echo json_encode(['processed' => count($events)]);
