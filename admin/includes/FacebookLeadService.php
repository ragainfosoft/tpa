<?php
// =====================================================
// TPA IMS — Facebook / Instagram Lead Ads Service
// Fetches leads from the Meta Graph API and files them
// into the leads table exactly like a website enquiry.
// =====================================================

require_once __DIR__ . '/functions.php';

class FacebookLeadService {

    const GRAPH = 'https://graph.facebook.com/v19.0/';

    private $token;
    private $appSecret;

    public function __construct() {
        $this->token     = getSetting('fb_page_token');
        $this->appSecret = getSetting('fb_app_secret');
    }

    public function isConfigured(): bool {
        return $this->token !== '';
    }

    // ── Webhook signature ────────────────────────────────────────────
    // Meta signs every POST with the app secret. Without this check the
    // endpoint would accept forged leads from anyone who knows the URL.
    public function verifySignature(string $rawBody, string $header): bool {
        if ($this->appSecret === '') return false;           // fail closed
        if (strpos($header, 'sha256=') !== 0)  return false;
        $expected = hash_hmac('sha256', $rawBody, $this->appSecret);
        return hash_equals($expected, substr($header, 7));
    }

    // ── Graph API ────────────────────────────────────────────────────
    private function get(string $path, array $params = []): array {
        if (!$this->isConfigured()) {
            return ['ok' => false, 'error' => 'Facebook Page access token not set.'];
        }
        $params['access_token'] = $this->token;
        $url = self::GRAPH . ltrim($path, '/') . '?' . http_build_query($params);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($response === false) return ['ok' => false, 'error' => 'cURL: ' . $curlErr];
        $data = json_decode($response, true);
        if (!is_array($data)) return ['ok' => false, 'error' => 'Bad JSON from Graph API'];
        if ($httpCode !== 200 || isset($data['error'])) {
            return ['ok' => false, 'error' => $data['error']['message'] ?? ('HTTP ' . $httpCode)];
        }
        return ['ok' => true, 'data' => $data];
    }

    /** Fetch one lead by its leadgen_id (what the webhook gives us). */
    public function fetchLead(string $leadgenId): array {
        return $this->get($leadgenId, [
            'fields' => 'id,created_time,form_id,field_data,campaign_name,adset_name,ad_name,platform,is_organic',
        ]);
    }

    /** Fetch recent leads for one form — used by the backfill/safety-net cron. */
    public function fetchFormLeads(string $formId, int $limit = 50): array {
        return $this->get($formId . '/leads', [
            'fields' => 'id,created_time,form_id,field_data,campaign_name,adset_name,ad_name,platform,is_organic',
            'limit'  => $limit,
        ]);
    }

    /** List the lead forms on the configured Page — used by the settings screen. */
    public function fetchPageForms(): array {
        $pageId = getSetting('fb_page_id');
        if ($pageId === '') return ['ok' => false, 'error' => 'Facebook Page ID not set.'];
        return $this->get($pageId . '/leadgen_forms', ['fields' => 'id,name,status', 'limit' => 100]);
    }

    // ── Field mapping ────────────────────────────────────────────────
    // Meta returns field_data as [{name, values[]}]. Standard fields keep
    // fixed names (full_name, email, phone_number); custom questions get
    // slugs derived from the question text, which differ per form — so we
    // match on keywords and keep every unmatched answer in the notes.
    public function mapFields(array $fieldData): array {
        $out = [
            'name' => '', 'first' => '', 'last' => '', 'email' => '', 'phone' => '',
            'child_name' => '', 'child_year' => '', 'course' => '', 'centre' => '',
            'extra' => [],
        ];

        foreach ($fieldData as $f) {
            $key = strtolower(trim($f['name'] ?? ''));
            $val = trim(implode(', ', $f['values'] ?? []));
            if ($key === '' || $val === '') continue;

            if     ($key === 'full_name')                       $out['name']  = $val;
            elseif ($key === 'first_name')                      $out['first'] = $val;
            elseif ($key === 'last_name')                       $out['last']  = $val;
            elseif ($key === 'email' || strpos($key,'email') !== false)       $out['email'] = $val;
            elseif ($key === 'phone_number' || strpos($key,'phone') !== false
                                            || strpos($key,'mobile') !== false
                                            || strpos($key,'whatsapp') !== false) $out['phone'] = $val;
            elseif (strpos($key,'child') !== false && strpos($key,'name') !== false) $out['child_name'] = $val;
            elseif (strpos($key,'year') !== false || strpos($key,'grade') !== false
                                                  || strpos($key,'class') !== false) $out['child_year'] = $val;
            elseif (strpos($key,'subject') !== false || strpos($key,'course') !== false
                                                     || strpos($key,'programme') !== false
                                                     || strpos($key,'program') !== false) $out['course'] = $val;
            elseif (strpos($key,'centre') !== false || strpos($key,'center') !== false
                                                    || strpos($key,'location') !== false
                                                    || strpos($key,'branch') !== false) $out['centre'] = $val;
            else $out['extra'][$f['name']] = $val;
        }

        if ($out['name'] === '') {
            $out['name'] = trim($out['first'] . ' ' . $out['last']);
        }
        if ($out['name'] === '') $out['name'] = $out['child_name'];
        return $out;
    }

    /** UK numbers arrive as +447… — store them the way the CRM shows them. */
    public function normalisePhone(string $number): string {
        $n = preg_replace('/[^0-9+]/', '', $number);
        $n = ltrim($n, '+');
        if (strpos($n, '44') === 0 && strlen($n) >= 12) $n = '0' . substr($n, 2);
        return substr($n, 0, 20);
    }

    /** Match the website form's centre normalisation so filters stay consistent. */
    public function normaliseCentre(string $centre): string {
        $cl = strtolower($centre);
        if ($centre === '')                       return getSetting('fb_lead_default_centre', 'No preference');
        if (strpos($cl,'romford')  !== false ||
            strpos($cl,'chadwell') !== false)     return 'Chadwell Heath';
        if (strpos($cl,'chelmsford') !== false)   return 'Chelmsford';
        if (strpos($cl,'online')     !== false)   return 'Online';
        if (strpos($cl,'parkwood')   !== false)   return 'Parkwood Academy';
        return $centre;
    }

    // ── Persistence ──────────────────────────────────────────────────
    /**
     * File one Graph API lead object into the leads table.
     * Returns ['status' => created|duplicate|skipped, 'lead_id' => int|null].
     */
    public function saveLead(array $lead): array {
        $db = getDB();
        $m  = $this->mapFields($lead['field_data'] ?? []);

        $phone = $this->normalisePhone($m['phone']);
        $email = $m['email'];
        if ($email === '' && $phone === '') {
            return ['status' => 'skipped', 'lead_id' => null, 'error' => 'Lead had neither email nor phone'];
        }

        // Context from the ad itself — worth keeping, it is why the lead exists.
        $noteLines = [];
        if (!empty($lead['campaign_name'])) $noteLines[] = 'Campaign: ' . $lead['campaign_name'];
        if (!empty($lead['adset_name']))    $noteLines[] = 'Ad set: '   . $lead['adset_name'];
        if (!empty($lead['ad_name']))       $noteLines[] = 'Ad: '       . $lead['ad_name'];
        if (!empty($lead['created_time']))  $noteLines[] = 'Submitted: ' . date('d M Y H:i', strtotime($lead['created_time']));
        foreach ($m['extra'] as $q => $a) {
            $noteLines[] = ucfirst(str_replace('_', ' ', $q)) . ': ' . $a;
        }
        $notes = implode("\n", $noteLines);

        $platform = strtolower($lead['platform'] ?? 'facebook');
        $source   = getSetting('fb_lead_source_label', 'Facebook Ad');
        if ($platform === 'instagram') $source = 'Instagram Ad';

        // Same dedupe rule as the website form: one lead per phone/email.
        $dup = $db->prepare('SELECT id FROM leads WHERE (phone = ? AND phone != "") OR (email = ? AND email != "") LIMIT 1');
        $dup->execute([$phone, $email]);
        $existing = $dup->fetch();

        if ($existing) {
            $db->prepare('INSERT INTO lead_followups (lead_id, user_id, type, notes, outcome)
                          VALUES (?, NULL, "other", ?, "Facebook ad enquiry")')
               ->execute([$existing['id'], "New Facebook lead form submission:\n" . $notes]);
            return ['status' => 'duplicate', 'lead_id' => (int)$existing['id']];
        }

        // Blank must be NULL, not 0 — assigned_to is a FK to users.id.
        $assignTo = getSetting('fb_lead_assign_to');
        $assignTo = ctype_digit($assignTo) && (int)$assignTo > 0 ? (int)$assignTo : null;

        $db->prepare('INSERT INTO leads (name,email,phone,whatsapp,child_name,child_year,course_interest,centre,source,notes,status,assigned_to)
                      VALUES (?,?,?,?,?,?,?,?,?,?,?,?)')
           ->execute([
               $m['name'] !== '' ? $m['name'] : 'Facebook lead',
               $email,
               $phone,
               $phone,
               $m['child_name'],
               $m['child_year'],
               $m['course'],
               $this->normaliseCentre($m['centre']),
               $source,
               $notes,
               'new',
               $assignTo,
           ]);
        $leadId = (int)$db->lastInsertId();

        $this->afterCreate($leadId, $m, $phone, $source);
        return ['status' => 'created', 'lead_id' => $leadId];
    }

    /** Optional follow-ups — each one guarded, never fatal to the webhook. */
    private function afterCreate(int $leadId, array $m, string $phone, string $source): void {
        if (getSetting('fb_lead_send_whatsapp', '0') === '1' && $phone !== '') {
            try {
                $tpl = getSetting('wa_template_new_lead');
                if ($tpl !== '') {
                    require_once __DIR__ . '/WhatsAppService.php';
                    $wa  = new WhatsAppService();
                    $msg = str_replace(['{parent_name}','{child_name}','{course}'],
                                       [$m['name'] ?: 'there', $m['child_name'] ?: 'your child', $m['course'] ?: 'our courses'],
                                       $tpl);
                    $wa->sendText($phone, $msg, $leadId, 'lead');
                }
            } catch (Throwable $e) {
                error_log('fb-leads: WhatsApp welcome failed — ' . $e->getMessage());
            }
        }

        $notify = getSetting('fb_lead_notify_email');
        if ($notify !== '') {
            try {
                require_once __DIR__ . '/EmailService.php';
                $body = '<p>A new lead arrived from ' . h($source) . '.</p>'
                      . '<p><strong>' . h($m['name']) . '</strong><br>'
                      . h($m['email']) . '<br>' . h($phone) . '</p>'
                      . '<p><a href="' . h($this->adminUrl('leads/view.php?id=' . $leadId)) . '">Open in the CRM</a></p>';
                EmailService::send($notify, 'TPA Admin', 'New Facebook lead: ' . ($m['name'] ?: 'Unnamed'), $body, 0, 'admin');
            } catch (Throwable $e) {
                error_log('fb-leads: notify email failed — ' . $e->getMessage());
            }
        }
    }

    /** SITE_URL points at the site root on live and at /admin locally — cope with both. */
    private function adminUrl(string $path): string {
        $base = defined('SITE_URL') ? rtrim(SITE_URL, '/') : '';
        if (substr($base, -6) !== '/admin') $base .= '/admin';
        return $base . '/' . ltrim($path, '/');
    }

    // ── Audit log / idempotency ──────────────────────────────────────
    /** TRUE if this leadgen_id was already filed (Meta retries aggressively). */
    public function alreadyProcessed(string $leadgenId): bool {
        $db = getDB();
        $st = $db->prepare('SELECT 1 FROM fb_lead_log WHERE leadgen_id = ? AND status IN ("created","duplicate") LIMIT 1');
        $st->execute([$leadgenId]);
        return (bool)$st->fetchColumn();
    }

    public function log(string $leadgenId, array $lead, array $result, string $rawPayload = ''): void {
        try {
            $db = getDB();
            $db->prepare('INSERT INTO fb_lead_log
                    (leadgen_id, form_id, page_id, campaign_name, adset_name, ad_name, platform, lead_id, status, error_message, raw_payload)
                  VALUES (?,?,?,?,?,?,?,?,?,?,?)
                  ON DUPLICATE KEY UPDATE
                    lead_id = VALUES(lead_id), status = VALUES(status),
                    error_message = VALUES(error_message), raw_payload = VALUES(raw_payload)')
               ->execute([
                   $leadgenId,
                   $lead['form_id']       ?? null,
                   $lead['page_id']       ?? null,
                   $lead['campaign_name'] ?? null,
                   $lead['adset_name']    ?? null,
                   $lead['ad_name']       ?? null,
                   $lead['platform']      ?? null,
                   $result['lead_id']     ?? null,
                   $result['status']      ?? 'received',
                   $result['error']       ?? null,
                   $rawPayload !== '' ? substr($rawPayload, 0, 60000) : null,
               ]);
        } catch (Throwable $e) {
            error_log('fb-leads: could not write fb_lead_log — ' . $e->getMessage());
        }
    }
}
