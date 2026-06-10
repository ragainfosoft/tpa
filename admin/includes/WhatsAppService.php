<?php
// =====================================================
// TPA IMS — Meta WhatsApp Cloud API Service
// =====================================================

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/PaymentService.php';

class WhatsAppService {

    private string $token;
    private string $phoneNumberId;
    private string $apiUrl;

    public function __construct() {
        $this->token         = getSetting('whatsapp_token');
        $this->phoneNumberId = getSetting('whatsapp_phone_number_id');
        $this->apiUrl        = 'https://graph.facebook.com/v18.0/' . $this->phoneNumberId . '/messages';
    }

    /**
     * Send a free-form text message (only works within 24-hour customer service window)
     */
    public function sendText(string $toNumber, string $message, int $recipientId = 0, string $recipientType = 'parent'): bool {
        $to = $this->normaliseNumber($toNumber);
        if (!$to) return false;

        $payload = [
            'messaging_product' => 'whatsapp',
            'to'                => $to,
            'type'              => 'text',
            'text'              => ['body' => $message],
        ];

        $result = $this->call($payload);
        $this->logMessage('whatsapp', $recipientType, $recipientId, $to, '', $message, $result);
        return $result['success'];
    }

    /**
     * Send a pre-approved template message (works any time, required for business-initiated)
     */
    public function sendTemplate(string $toNumber, string $templateName, array $components = [], string $languageCode = 'en_GB', int $recipientId = 0, string $recipientType = 'parent'): bool {
        $to = $this->normaliseNumber($toNumber);
        if (!$to) return false;

        $payload = [
            'messaging_product' => 'whatsapp',
            'to'                => $to,
            'type'              => 'template',
            'template'          => [
                'name'       => $templateName,
                'language'   => ['code' => $languageCode],
                'components' => $components,
            ],
        ];

        $result = $this->call($payload);
        $this->logMessage('whatsapp', $recipientType, $recipientId, $to, $templateName, '', $result);
        return $result['success'];
    }

    /**
     * Build a fee reminder template component array
     */
    public static function feeReminderComponents(string $studentName, string $amount, string $dueDate, string $invoiceNum, string $paymentToken = ''): array {
        $params = [
            ['type' => 'text', 'text' => $studentName],
            ['type' => 'text', 'text' => $amount],
            ['type' => 'text', 'text' => $dueDate],
            ['type' => 'text', 'text' => $invoiceNum],
        ];
        if ($paymentToken) {
            $url = PaymentService::getPublicPaymentUrl($paymentToken);
            $params[] = ['type' => 'text', 'text' => "Pay Online: $url"];
        }

        return [[
            'type'       => 'body',
            'parameters' => $params,
        ]];
    }

    /**
     * Build an absence notification template component array
     */
    public static function absenceComponents(string $studentName, string $batchName, string $date): array {
        return [[
            'type'       => 'body',
            'parameters' => [
                ['type' => 'text', 'text' => $studentName],
                ['type' => 'text', 'text' => $batchName],
                ['type' => 'text', 'text' => $date],
            ],
        ]];
    }

    // ── Send a wa.me deep-link (fallback if API not configured) ──────────────

    public static function buildWALink(string $number, string $message = ''): string {
        return waLink($number, $message);
    }

    // ── Private helpers ─────────────────────────────────────────────────────

    private function call(array $payload): array {
        if (!$this->token || !$this->phoneNumberId) {
            return ['success' => false, 'error' => 'WhatsApp API not configured.'];
        }

        $ch = curl_init($this->apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $this->token,
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT        => 10,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode($response, true) ?? [];

        if ($httpCode === 200 && isset($data['messages'][0]['id'])) {
            return ['success' => true, 'message_id' => $data['messages'][0]['id']];
        }
        return ['success' => false, 'error' => $data['error']['message'] ?? 'Unknown error'];
    }

    private function normaliseNumber(string $number): ?string {
        $n = preg_replace('/\D/', '', $number);
        if (strlen($n) === 11 && $n[0] === '0') $n = '44' . substr($n, 1);
        if (strlen($n) < 10) return null;
        return $n;
    }

    private function logMessage(string $type, string $recipientType, int $recipientId, string $to, string $template, string $message, array $result): void {
        try {
            $db = getDB();
            $db->prepare('INSERT INTO communications (type, recipient_type, recipient_id, to_number_or_email, template_name, message, status, meta_message_id, error_message, sent_by, sent_at)
                          VALUES (?,?,?,?,?,?,?,?,?,?,NOW())')
               ->execute([
                   $type,
                   $recipientType,
                   $recipientId ?: null,
                   $to,
                   $template ?: null,
                   $message ?: null,
                   $result['success'] ? 'sent' : 'failed',
                   $result['message_id'] ?? null,
                   $result['error'] ?? null,
                   function_exists('currentUserId') ? currentUserId() : null,
               ]);
        } catch (Exception $e) { /* fail silently */ }
    }
}
