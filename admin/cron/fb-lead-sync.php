#!/usr/bin/env php
<?php
// =====================================================
// TPA IMS — Cron: Facebook Lead Ads safety net
//
// The webhook is the primary path. This job exists because webhooks can
// be missed (server down, token expired, Meta disabled the subscription)
// and because it backfills leads submitted before the webhook was set up.
// It re-reads recent leads from every form on the Page; anything already
// filed is skipped via the fb_lead_log unique key.
//
// Schedule hourly:
//   0 * * * * /usr/bin/php /home4/kkagucom/public_html/talentpoolacademy/admin/cron/fb-lead-sync.php
// =====================================================

require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/FacebookLeadService.php';

$now = date('Y-m-d H:i:s');
echo "[{$now}] Facebook lead sync started\n";

if (getSetting('fb_leads_enabled', '0') !== '1') {
    echo "Integration disabled in Settings — nothing to do.\n";
    exit(0);
}

$fb = new FacebookLeadService();
if (!$fb->isConfigured()) {
    echo "No Page access token configured.\n";
    exit(1);
}

$forms = $fb->fetchPageForms();
if (!$forms['ok']) {
    echo "Could not list lead forms: {$forms['error']}\n";
    exit(1);
}

$created = $dupes = $errors = 0;

foreach ($forms['data']['data'] ?? [] as $form) {
    $formId = $form['id'] ?? '';
    if ($formId === '') continue;

    $res = $fb->fetchFormLeads($formId, 50);
    if (!$res['ok']) {
        echo "  form {$formId}: {$res['error']}\n";
        $errors++;
        continue;
    }

    foreach ($res['data']['data'] ?? [] as $lead) {
        $leadgenId = (string)($lead['id'] ?? '');
        if ($leadgenId === '' || $fb->alreadyProcessed($leadgenId)) continue;

        try {
            $lead['form_id'] = $lead['form_id'] ?? $formId;
            $result = $fb->saveLead($lead);
            $fb->log($leadgenId, $lead, $result);

            if ($result['status'] === 'created')   { $created++; echo "  + lead #{$result['lead_id']} from form {$formId}\n"; }
            elseif ($result['status'] === 'duplicate') { $dupes++; }
        } catch (Throwable $e) {
            $errors++;
            $fb->log($leadgenId, $lead, ['status' => 'error', 'error' => $e->getMessage()]);
            echo "  ! {$leadgenId}: {$e->getMessage()}\n";
        }
    }
}

echo "Done — {$created} created, {$dupes} matched existing leads, {$errors} errors\n";
