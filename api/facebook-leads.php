<?php
// =====================================================
// TPA IMS — Facebook / Instagram Lead Ads Webhook
//
// Meta calls this URL twice in its lifetime pattern:
//   GET  — once, to verify the endpoint (hub.challenge handshake)
//   POST — every time someone submits a lead form
//
// The POST body only carries a leadgen_id; the answers themselves are
// fetched from the Graph API with the Page access token.
// Configure in: Admin → Settings → Facebook Leads
// =====================================================

require_once __DIR__ . '/../admin/includes/config.php';
require_once __DIR__ . '/../admin/includes/db.php';
require_once __DIR__ . '/../admin/includes/functions.php';
require_once __DIR__ . '/../admin/includes/FacebookLeadService.php';

// ── 1. Verification handshake ────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $mode      = $_GET['hub_mode']         ?? '';
    $token     = $_GET['hub_verify_token'] ?? '';
    $challenge = $_GET['hub_challenge']    ?? '';
    $expected  = getSetting('fb_verify_token');

    if ($mode === 'subscribe' && $expected !== '' && hash_equals($expected, $token)) {
        header('Content-Type: text/plain');
        echo $challenge;                     // Meta requires the raw challenge back
        exit;
    }
    http_response_code(403);
    echo 'Verification failed';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

// ── 2. Authenticate the payload ──────────────────────────────────────
$raw = file_get_contents('php://input');
$sig = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
$fb  = new FacebookLeadService();

if (!$fb->verifySignature($raw, $sig)) {
    error_log('fb-leads: rejected webhook with bad or missing signature');
    http_response_code(403);
    exit;
}

// Always answer 200 from here on. A non-200 makes Meta retry for days and
// eventually disable the subscription — we'd rather log our own failures.
http_response_code(200);
echo 'EVENT_RECEIVED';
if (function_exists('fastcgi_finish_request')) fastcgi_finish_request();

if (getSetting('fb_leads_enabled', '0') !== '1') {
    error_log('fb-leads: webhook received but the integration is switched off in Settings');
    exit;
}

// ── 3. Process each leadgen event ────────────────────────────────────
$payload = json_decode($raw, true);
if (!is_array($payload) || ($payload['object'] ?? '') !== 'page') exit;

foreach ($payload['entry'] ?? [] as $entry) {
    foreach ($entry['changes'] ?? [] as $change) {
        if (($change['field'] ?? '') !== 'leadgen') continue;

        $value      = $change['value'] ?? [];
        $leadgenId  = (string)($value['leadgen_id'] ?? '');
        if ($leadgenId === '') continue;

        try {
            if ($fb->alreadyProcessed($leadgenId)) continue;   // retry of one we filed

            if ($fb->isSamplePayload($leadgenId)) {
                $fb->log($leadgenId, $value, [
                    'status' => 'skipped',
                    'error'  => 'Meta test payload — endpoint reachable and signature verified. '
                              . 'Use the Lead Ads Testing Tool for a real end-to-end test.',
                ], $raw);
                continue;
            }

            $fetched = $fb->fetchLead($leadgenId);
            if (!$fetched['ok']) {
                $fb->log($leadgenId, $value, ['status' => 'error', 'error' => $fetched['error']], $raw);
                error_log('fb-leads: Graph fetch failed for ' . $leadgenId . ' — ' . $fetched['error']);
                continue;
            }

            $lead = $fetched['data'];
            $lead['page_id'] = $value['page_id'] ?? ($entry['id'] ?? null);
            $lead['form_id'] = $lead['form_id'] ?? ($value['form_id'] ?? null);

            // fb_lead_log is the audit trail here; activity_log is session-bound
            // and a webhook has no logged-in user to attribute the row to.
            $result = $fb->saveLead($lead);
            $fb->log($leadgenId, $lead, $result, $raw);

        } catch (Throwable $e) {
            $fb->log($leadgenId, $value, ['status' => 'error', 'error' => $e->getMessage()], $raw);
            error_log('fb-leads: ' . $e->getMessage());
        }
    }
}
