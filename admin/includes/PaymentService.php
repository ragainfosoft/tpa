<?php
// =====================================================
// TPA IMS — Payment Service (Stripe & GoCardless)
// =====================================================

require_once __DIR__ . '/functions.php';

class PaymentService {

    /**
     * Get the public payment URL for an invoice
     */
    public static function getPublicPaymentUrl(string $token): string {
        $baseUrl = getSetting('site_url_public', str_replace('/admin', '', SITE_URL));
        return rtrim($baseUrl, '/') . '/pay-invoice.php?token=' . $token;
    }

    /**
     * Generate a Stripe Checkout Session for an invoice
     */
    public static function createStripeSession(array $invoice, array $student, string $successUrl, string $cancelUrl): ?string {
        $secretKey = getSetting('stripe_secret_key');
        if (empty($secretKey)) return null;

        require_once dirname(__DIR__, 2) . '/vendor/autoload.php';
        \Stripe\Stripe::setApiKey($secretKey);

        try {
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'gbp',
                        'product_data' => [
                            'name' => "Invoice #{$invoice['invoice_number']} - {$invoice['title']}",
                            'description' => "Student: {$student['first_name']} {$student['last_name']}",
                        ],
                        'unit_amount' => (int)($invoice['amount_due'] * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'customer_email' => $student['parent_email'] ?? null,
                'client_reference_id' => $invoice['id'],
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'metadata' => [
                    'invoice_id' => $invoice['id'],
                    'student_id' => $invoice['student_id']
                ]
            ]);
            return $session->url;
        } catch (Exception $e) {
            error_log("Stripe Session Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Handle GoCardless Payment (Basic Redirect for now)
     */
    public static function getGoCardlessUrl(array $invoice): string {
        // Typically requires GC Pro SDK or a pre-defined payment link.
        // For now, return the manual link if it exists, or a placeholder.
        return $invoice['payment_link_gocardless'] ?: '';
    }
}
