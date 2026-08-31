<?php
// =====================================================
// TPA IMS — Settings
// =====================================================

$page_title   = 'Settings';
$page_section = 'settings';
require_once __DIR__ . '/../includes/header.php';
requireRole(['admin']);

$db = getDB();

$tab = $_GET['tab'] ?? 'general';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $keys = $_POST['settings'] ?? [];
    // INSERT..ON DUPLICATE so a newly-introduced setting key saves on the
    // first attempt instead of silently doing nothing.
    $stmt = $db->prepare('INSERT INTO settings (`key`, value) VALUES (?, ?)
                          ON DUPLICATE KEY UPDATE value = VALUES(value)');
    foreach ($keys as $key => $value) {
        $stmt->execute([$key, trim($value)]);
    }
    // Clear getSetting cache by doing nothing (page reload)
    logActivity('settings_saved', "Settings [{$tab}] updated");
    setFlash('success', 'Settings saved successfully.');
    header('Location: index.php?tab=' . $tab);
    exit;
}

// Load all settings into assoc array
$rows = $db->query('SELECT `key`, value FROM settings')->fetchAll();
$cfg  = array_column($rows, 'value', 'key');

function sval(array $cfg, string $key): string { return htmlspecialchars($cfg[$key] ?? '', ENT_QUOTES); }
?>

<div class="page-header">
  <h1><i class="bi bi-gear me-2" style="color:var(--gold);"></i>Settings</h1>
</div>

<!-- Tab nav -->
<ul class="nav nav-tabs mb-4">
  <?php foreach (['general'=>'General','smtp'=>'Email (SMTP)','whatsapp'=>'WhatsApp','facebook'=>'Facebook Leads','payment'=>'Payments','reminders'=>'🔔 Reminders','users'=>'Users'] as $t=>$label): ?>
    <li class="nav-item"><a class="nav-link <?= $tab===$t?'active':'' ?>" href="?tab=<?= $t ?>"><?= $label ?></a></li>
  <?php endforeach; ?>
</ul>

<form method="POST">
  <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
  <div class="stat-card">

  <?php if ($tab === 'general'): ?>
    <h6 class="fw-700 mb-4 text-uppercase" style="font-size:.7rem;letter-spacing:.1em;color:#888;">General Settings</h6>
    <div class="row g-3">
      <?php foreach (['site_name'=>'Academy Name','site_phone'=>'Main Phone','site_email'=>'Main Email','site_address_romford'=>'Romford Address','site_address_chelmsford'=>'Chelmsford Address','bank_name'=>'Bank Account Name','bank_account'=>'Bank Account Number','bank_sort_code'=>'Sort Code','bacs_reference_prefix'=>'BACS Reference Prefix','invoice_prefix'=>'Invoice Prefix','student_ref_prefix'=>'Student Ref Prefix'] as $k=>$l): ?>
      <div class="col-sm-6">
        <label class="form-label fw-600 small"><?= $l ?></label>
        <input type="text" name="settings[<?= $k ?>]" class="form-control" value="<?= sval($cfg,$k) ?>">
      </div>
      <?php endforeach; ?>
    </div>

  <?php elseif ($tab === 'smtp'): ?>
    <h6 class="fw-700 mb-4 text-uppercase" style="font-size:.7rem;letter-spacing:.1em;color:#888;">Email (SMTP) Settings</h6>
    <div class="alert alert-info small"><i class="bi bi-info-circle me-2"></i>For Gmail: enable "App Passwords" and use your app password below. Host: smtp.gmail.com, Port: 587.</div>
    <div class="row g-3">
      <?php foreach (['smtp_host'=>'SMTP Host','smtp_port'=>'SMTP Port','smtp_user'=>'SMTP Username','smtp_pass'=>'SMTP Password','smtp_from_name'=>'From Name','smtp_from_email'=>'From Email Address'] as $k=>$l): ?>
      <div class="col-sm-6">
        <label class="form-label fw-600 small"><?= $l ?></label>
        <input type="<?= $k==='smtp_pass'?'password':'text' ?>" name="settings[<?= $k ?>]" class="form-control" value="<?= sval($cfg,$k) ?>" autocomplete="off">
      </div>
      <?php endforeach; ?>
    </div>

  <?php elseif ($tab === 'whatsapp'): ?>
    <h6 class="fw-700 mb-4 text-uppercase" style="font-size:.7rem;letter-spacing:.1em;color:#888;">WhatsApp Cloud API (Meta — Pay Per Conversation, 1,000 Free/Month)</h6>
    <div class="alert alert-info small">
      <strong>Setup:</strong> 1) Create a <a href="https://business.facebook.com" target="_blank">Meta Business Account</a> &rarr;
      2) Create a WhatsApp Business App in <a href="https://developers.facebook.com" target="_blank">Meta Developers</a> &rarr;
      3) Get your Phone Number ID and generate a permanent System User Access Token &rarr;
      4) Submit message templates for approval (fee_reminder, absence_notification, etc.)
    </div>
    <div class="row g-3 mb-4">
      <?php foreach (['whatsapp_phone_number_id'=>'Phone Number ID','whatsapp_token'=>'Permanent Access Token','whatsapp_api_url'=>'API Base URL (leave default)'] as $k=>$l): ?>
      <div class="col-sm-6">
        <label class="form-label fw-600 small"><?= $l ?></label>
        <input type="<?= $k==='whatsapp_token'?'password':'text' ?>" name="settings[<?= $k ?>]" class="form-control" value="<?= sval($cfg,$k) ?>" autocomplete="off">
      </div>
      <?php endforeach; ?>
    </div>

    <hr>
    <h6 class="fw-700 mb-3 text-uppercase" style="font-size:.7rem;letter-spacing:.1em;color:#888;">Message Templates <span class="text-muted fw-400">(plain text, sent automatically at each pipeline stage)</span></h6>
    <div class="alert alert-secondary small mb-4">
      <strong>Placeholders:</strong> <code>{parent_name}</code> <code>{child_name}</code> <code>{student_ref}</code> <code>{date}</code> <code>{course}</code> <code>{amount}</code> <code>{invoice_number}</code>
    </div>
    <div class="row g-3">
      <?php foreach ([
        'wa_template_new_lead'         => ['New Lead Welcome', 'Hi {parent_name}, thank you for your enquiry! We\'d love to help {child_name} reach their full potential. A member of our team will be in touch very soon. — Talent Pool Academy'],
        'wa_template_assessment_booked'=> ['Assessment Booked', 'Hi {parent_name}, great news! We\'ve booked a FREE assessment for {child_name} on {date}. Please arrive 5 minutes early. Any questions? Call us on the number above. — TPA'],
        'wa_template_enrolled'         => ['Enrolled / Welcome', 'Hi {parent_name}, welcome to Talent Pool Academy! {child_name} has been successfully enrolled (Ref: {student_ref}). We look forward to helping them thrive. — TPA'],
        'wa_template_fee_reminder'     => ['Fee Reminder', 'Hi {parent_name}, this is a friendly reminder that a payment of {amount} is due for {child_name}. Invoice: {invoice_number}. BACS details: see attached. Thank you — TPA'],
      ] as $k => [$l, $default]): ?>
      <div class="col-12">
        <label class="form-label fw-600 small"><?= $l ?></label>
        <textarea name="settings[<?= $k ?>]" class="form-control font-monospace" rows="3" style="font-size:.8rem;"><?= sval($cfg, $k) ?: htmlspecialchars($default, ENT_QUOTES) ?></textarea>
      </div>
      <?php endforeach; ?>
    </div>

  <?php elseif ($tab === 'facebook'): ?>
    <?php
      // SITE_URL points at the admin folder on live; the webhook lives at the site root.
      $siteRoot   = preg_replace('#/admin/?$#', '', rtrim(SITE_URL, '/'));
      $webhookUrl = $siteRoot . '/api/facebook-leads.php';
      $staff      = $db->query('SELECT id, name FROM users WHERE role IN ("admin","staff","branch_manager") AND is_active = 1 ORDER BY name')->fetchAll();

      // Optional live check: does the saved token actually reach the Page?
      $fbTest = null;
      if (isset($_GET['test'])) {
          require_once __DIR__ . '/../includes/FacebookLeadService.php';
          $fbTest = (new FacebookLeadService())->fetchPageForms();
      }
    ?>
    <h6 class="fw-700 mb-4 text-uppercase" style="font-size:.7rem;letter-spacing:.1em;color:#888;">Facebook &amp; Instagram Lead Ads</h6>
    <div class="alert alert-info small">
      <strong>Setup:</strong>
      1) Open your app in <a href="https://developers.facebook.com" target="_blank">Meta for Developers</a> &rarr;
      2) Add the <em>Webhooks</em> product, subscribe the <code>page</code> object to the <code>leadgen</code> field &rarr;
      3) Paste the callback URL and verify token below into Meta &rarr;
      4) Under <em>Messenger/Pages</em>, generate a long-lived <strong>Page access token</strong> with <code>leads_retrieval</code> and <code>pages_show_list</code> &rarr;
      5) Switch the integration on and submit a test lead from Meta's Lead Ads Testing Tool.
    </div>

    <div class="alert alert-secondary small">
      <strong>Callback URL:</strong> <code><?= h($webhookUrl) ?></code><br>
      <strong>Subscribe to field:</strong> <code>leadgen</code> on the <code>page</code> object
    </div>

    <div class="row g-3 mb-3">
      <div class="col-sm-6">
        <label class="form-label fw-600 small">Integration enabled</label>
        <select name="settings[fb_leads_enabled]" class="form-select">
          <option value="1" <?= ($cfg['fb_leads_enabled'] ?? '0')==='1'?'selected':'' ?>>✅ Enabled</option>
          <option value="0" <?= ($cfg['fb_leads_enabled'] ?? '0')==='0'?'selected':'' ?>>❌ Disabled</option>
        </select>
      </div>
      <?php foreach ([
        'fb_verify_token' => ['Webhook Verify Token', 'Any string you invent — paste the same one into Meta'],
        'fb_app_secret'   => ['App Secret', 'Meta app dashboard → Settings → Basic'],
        'fb_page_id'      => ['Facebook Page ID', 'Your Page → About → Page ID'],
        'fb_page_token'   => ['Page Access Token (long-lived)', 'Needs the leads_retrieval permission'],
      ] as $k => [$l, $hint]): ?>
      <div class="col-sm-6">
        <label class="form-label fw-600 small"><?= $l ?></label>
        <input type="<?= in_array($k, ['fb_app_secret','fb_page_token'], true) ? 'password' : 'text' ?>"
               name="settings[<?= $k ?>]" class="form-control" value="<?= sval($cfg,$k) ?>" autocomplete="off">
        <div class="form-text small"><?= $hint ?></div>
      </div>
      <?php endforeach; ?>
    </div>

    <hr>
    <h6 class="fw-700 mb-3 text-uppercase" style="font-size:.7rem;letter-spacing:.1em;color:#888;">How incoming leads are filed</h6>
    <div class="row g-3">
      <div class="col-sm-6">
        <label class="form-label fw-600 small">Source label</label>
        <input type="text" name="settings[fb_lead_source_label]" class="form-control" value="<?= sval($cfg,'fb_lead_source_label') ?: 'Facebook Ad' ?>">
        <div class="form-text small">Shown in the Source column. Instagram leads are labelled “Instagram Ad” automatically.</div>
      </div>
      <div class="col-sm-6">
        <label class="form-label fw-600 small">Default centre</label>
        <input type="text" name="settings[fb_lead_default_centre]" class="form-control" value="<?= sval($cfg,'fb_lead_default_centre') ?: 'No preference' ?>">
        <div class="form-text small">Used when the lead form does not ask which centre.</div>
      </div>
      <div class="col-sm-6">
        <label class="form-label fw-600 small">Auto-assign new leads to</label>
        <select name="settings[fb_lead_assign_to]" class="form-select">
          <option value="">— Leave unassigned —</option>
          <?php foreach ($staff as $u): ?>
            <option value="<?= (int)$u['id'] ?>" <?= ($cfg['fb_lead_assign_to'] ?? '')===(string)$u['id']?'selected':'' ?>><?= h($u['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-sm-6">
        <label class="form-label fw-600 small">Send the WhatsApp welcome automatically</label>
        <select name="settings[fb_lead_send_whatsapp]" class="form-select">
          <option value="0" <?= ($cfg['fb_lead_send_whatsapp'] ?? '0')==='0'?'selected':'' ?>>❌ No</option>
          <option value="1" <?= ($cfg['fb_lead_send_whatsapp'] ?? '0')==='1'?'selected':'' ?>>✅ Yes — use the “New Lead Welcome” template</option>
        </select>
      </div>
      <div class="col-sm-6">
        <label class="form-label fw-600 small">Notify this email on every new lead</label>
        <input type="text" name="settings[fb_lead_notify_email]" class="form-control" value="<?= sval($cfg,'fb_lead_notify_email') ?>" placeholder="leave blank for no notification">
      </div>
    </div>

    <hr>
    <div class="d-flex align-items-center gap-2 mb-3">
      <a href="?tab=facebook&test=1" class="btn btn-sm btn-outline-dark"><i class="bi bi-plug me-1"></i>Test connection</a>
      <span class="text-muted small">Calls the Graph API with the saved token and lists your lead forms. Save your changes first.</span>
    </div>
    <?php if ($fbTest !== null): ?>
      <?php if (!$fbTest['ok']): ?>
        <div class="alert alert-danger small mb-4"><strong>Connection failed:</strong> <?= h($fbTest['error']) ?></div>
      <?php else: ?>
        <div class="alert alert-success small mb-4">
          <strong>Connected.</strong> Lead forms found on this Page:
          <?php $forms = $fbTest['data']['data'] ?? []; ?>
          <?php if (!$forms): ?>
            <em>none yet — create a lead form on the Page first.</em>
          <?php else: ?>
            <ul class="mb-0 mt-2">
              <?php foreach ($forms as $f): ?>
                <li><?= h($f['name'] ?? '—') ?> <code><?= h($f['id'] ?? '') ?></code> <span class="text-muted"><?= h($f['status'] ?? '') ?></span></li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    <?php endif; ?>

    <h6 class="fw-700 mb-3 text-uppercase" style="font-size:.7rem;letter-spacing:.1em;color:#888;">Recent webhook activity</h6>
    <?php
      $fbLog      = [];
      $fbLogReady = true;
      try {
          $fbLog = $db->query('SELECT * FROM fb_lead_log ORDER BY created_at DESC LIMIT 15')->fetchAll();
      } catch (Throwable $ex) {
          $fbLogReady = false;
          echo '<div class="alert alert-warning small">Run <code>migrations/003_facebook_lead_ads.sql</code> to create the <code>fb_lead_log</code> table.</div>';
      }
    ?>
    <?php if ($fbLog): ?>
      <div class="table-responsive">
        <table class="table table-sm align-middle small">
          <thead><tr><th>When</th><th>Leadgen ID</th><th>Campaign</th><th>Status</th><th>Lead</th></tr></thead>
          <tbody>
          <?php foreach ($fbLog as $r): ?>
            <tr>
              <td class="text-nowrap"><?= formatDate($r['created_at'], 'd M H:i') ?></td>
              <td><code><?= h($r['leadgen_id']) ?></code></td>
              <td><?= h($r['campaign_name'] ?? '') ?></td>
              <td>
                <?php $cls = ['created'=>'success','duplicate'=>'info','error'=>'danger','skipped'=>'warning'][$r['status']] ?? 'secondary'; ?>
                <span class="badge bg-<?= $cls ?>"><?= h($r['status']) ?></span>
                <?php if (!empty($r['error_message'])): ?><div class="text-danger small"><?= h($r['error_message']) ?></div><?php endif; ?>
              </td>
              <td><?php if ($r['lead_id']): ?><a href="../leads/view.php?id=<?= (int)$r['lead_id'] ?>">#<?= (int)$r['lead_id'] ?></a><?php else: ?>—<?php endif; ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php elseif ($fbLogReady): ?>
      <p class="text-muted small">No leads received yet.</p>
    <?php endif; ?>

  <?php elseif ($tab === 'payment'): ?>
    <h6 class="fw-700 mb-4 text-uppercase" style="font-size:.7rem;letter-spacing:.1em;color:#888;">Payment Gateways</h6>
    <div class="mb-4">
      <h6 class="fw-700">GoCardless (Direct Debit — Recommended for recurring tuition)</h6>
      <p class="text-muted small mb-3">1% + 20p per payment, capped at £4. Parents authorise once, fees collected automatically. <a href="https://gocardless.com" target="_blank">gocardless.com</a></p>
      <div class="row g-3">
        <?php foreach (['gocardless_access_token'=>'Access Token','gocardless_environment'=>'Environment (sandbox / live)','gocardless_webhook_secret'=>'Webhook Signing Secret'] as $k=>$l): ?>
        <div class="col-sm-6">
          <label class="form-label fw-600 small"><?= $l ?></label>
          <input type="<?= (strpos($k,'secret')!==false||strpos($k,'token')!==false)?'password':'text' ?>" name="settings[<?= $k ?>]" class="form-control" value="<?= sval($cfg,$k) ?>" autocomplete="off">
        </div>
        <?php endforeach; ?>
        <div class="col-12">
          <div class="alert alert-secondary small mb-0">
            <strong>Webhook URL:</strong> <code><?= h(rtrim(SITE_URL,'/')) ?>/payments/gocardless-webhook.php</code><br>
            Set this in your GoCardless dashboard under <em>Developers → Webhooks</em>.
          </div>
        </div>
      </div>
    </div>
    <hr>
    <div>
      <h6 class="fw-700">Stripe (Card Payments)</h6>
      <p class="text-muted small mb-3">1.5% + 20p for European cards. Used for one-off card payments via hosted Payment Link. <a href="https://stripe.com" target="_blank">stripe.com</a></p>
      <div class="row g-3">
        <?php foreach (['stripe_public_key'=>'Publishable Key','stripe_secret_key'=>'Secret Key','stripe_webhook_secret'=>'Webhook Signing Secret'] as $k=>$l): ?>
        <div class="col-sm-6">
          <label class="form-label fw-600 small"><?= $l ?></label>
          <input type="<?= $k!=='stripe_public_key'?'password':'text' ?>" name="settings[<?= $k ?>]" class="form-control" value="<?= sval($cfg,$k) ?>" autocomplete="off">
        </div>
        <?php endforeach; ?>
        <div class="col-12">
          <div class="alert alert-secondary small mb-0">
            <strong>Stripe Webhook URL:</strong> <code><?= h(rtrim(SITE_URL,'/')) ?>/payments/stripe-webhook.php</code><br>
            Register this in your <a href="https://dashboard.stripe.com/webhooks" target="_blank">Stripe Dashboard → Webhooks</a>. Events: <code>payment_intent.succeeded</code>, <code>checkout.session.completed</code>.
          </div>
        </div>
      </div>
    </div>

  <?php elseif ($tab === 'reminders'): ?>
    <h6 class="fw-700 mb-2 text-uppercase" style="font-size:.7rem;letter-spacing:.1em;color:#888;">Auto-Reminder Rules</h6>
    <p class="text-muted small mb-4">These settings control what gets sent automatically by the daily cron job (<code>cron/send-reminders.php</code>). Individual reminders can always be sent manually from the <a href="../reminders/index.php">Reminder Centre</a>.</p>

    <div class="row g-4">
      <!-- Fee Reminders -->
      <div class="col-lg-6">
        <div class="stat-card h-100">
          <h6 class="fw-700 mb-3">💰 Fee Reminders</h6>
          <div class="mb-3">
            <label class="form-label fw-600 small">Auto-send enabled</label>
            <select name="settings[reminder_fee_enabled]" class="form-select form-select-sm">
              <option value="1" <?= ($cfg['reminder_fee_enabled']??'1')==='1'?'selected':'' ?>>✅ Enabled</option>
              <option value="0" <?= ($cfg['reminder_fee_enabled']??'1')==='0'?'selected':'' ?>>❌ Disabled</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-600 small">Send reminder N days before due date</label>
            <input type="number" name="settings[reminder_fee_days_before]" class="form-control form-control-sm" value="<?= sval($cfg,'reminder_fee_days_before') ?: '3' ?>" min="0" max="30">
          </div>
          <div class="mb-3">
            <label class="form-label fw-600 small">Re-send overdue reminder every N days</label>
            <input type="number" name="settings[reminder_fee_overdue_resend]" class="form-control form-control-sm" value="<?= sval($cfg,'reminder_fee_overdue_resend') ?: '7' ?>" min="1" max="30">
          </div>
          <div class="mb-3">
            <label class="form-label fw-600 small">Send channel</label>
            <select name="settings[reminder_fee_channel]" class="form-select form-select-sm">
              <?php foreach (['both'=>'WhatsApp & Email','whatsapp'=>'WhatsApp only','email'=>'Email only'] as $v=>$l): ?>
                <option value="<?= $v ?>" <?= ($cfg['reminder_fee_channel']??'both')===$v?'selected':'' ?>><?= $l ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="form-label fw-600 small">WhatsApp message template</label>
            <textarea name="settings[wa_template_fee_reminder]" class="form-control form-control-sm font-monospace" rows="4" style="font-size:.78rem;"><?= sval($cfg,'wa_template_fee_reminder') ?></textarea>
            <div class="text-muted" style="font-size:.7rem;margin-top:.25rem;">Placeholders: <code>{parent_name} {child_name} {amount} {due_date} {invoice_number}</code></div>
          </div>
        </div>
      </div>

      <!-- Absence Alerts -->
      <div class="col-lg-6">
        <div class="stat-card h-100">
          <h6 class="fw-700 mb-3">📋 Absence Alerts</h6>
          <div class="mb-3">
            <label class="form-label fw-600 small">Auto-send enabled</label>
            <select name="settings[reminder_absence_enabled]" class="form-select form-select-sm">
              <option value="1" <?= ($cfg['reminder_absence_enabled']??'1')==='1'?'selected':'' ?>>✅ Enabled</option>
              <option value="0" <?= ($cfg['reminder_absence_enabled']??'1')==='0'?'selected':'' ?>>❌ Disabled</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-600 small">Send channel</label>
            <select name="settings[reminder_absence_channel]" class="form-select form-select-sm">
              <?php foreach (['both'=>'WhatsApp & Email','whatsapp'=>'WhatsApp only','email'=>'Email only'] as $v=>$l): ?>
                <option value="<?= $v ?>" <?= ($cfg['reminder_absence_channel']??'both')===$v?'selected':'' ?>><?= $l ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="form-label fw-600 small">WhatsApp message template</label>
            <textarea name="settings[wa_template_absence]" class="form-control form-control-sm font-monospace" rows="4" style="font-size:.78rem;"><?= sval($cfg,'wa_template_absence') ?></textarea>
            <div class="text-muted" style="font-size:.7rem;margin-top:.25rem;">Placeholders: <code>{parent_name} {child_name} {batch_name} {date}</code></div>
          </div>
        </div>
      </div>

      <!-- Cron Info -->
      <div class="col-12">
        <div class="alert alert-secondary d-flex align-items-start gap-3">
          <i class="bi bi-terminal fs-5 mt-1"></i>
          <div>
            <strong>Cron Job Setup</strong><br>
            <span class="small">Add this line to your server crontab to auto-run reminders at 8am daily:</span><br>
            <code class="small">0 8 * * * /usr/bin/php <?= realpath(__DIR__ . '/../../cron/send-reminders.php') ?></code><br>
            <small class="text-muted">Or trigger manually: visit <a href="../cron/send-reminders.php" target="_blank">cron/send-reminders.php</a></small>
          </div>
        </div>
      </div>
    </div>

  <?php elseif ($tab === 'users'): ?>
    <h6 class="fw-700 mb-4 text-uppercase" style="font-size:.7rem;letter-spacing:.1em;color:#888;">Staff User Accounts</h6>
    <a href="users.php" class="btn btn-sm btn-dark mb-3"><i class="bi bi-plus-lg me-1"></i>Add / Manage Users</a>
    <?php $users = $db->query('SELECT id, name, email, role, is_active, last_login FROM users ORDER BY role, name')->fetchAll(); ?>
    <div class="table-responsive">
      <table class="table table-sm mb-0">
        <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Last Login</th></tr></thead>
        <tbody>
          <?php foreach ($users as $u): ?>
            <tr>
              <td class="fw-600"><?= h($u['name']) ?></td>
              <td class="small"><?= h($u['email']) ?></td>
              <td><span class="badge bg-<?= $u['role']==='admin'?'danger':($u['role']==='staff'?'primary':'info') ?>"><?= ucfirst($u['role']) ?></span></td>
              <td><?= $u['is_active']?'<span class="badge bg-success">Active</span>':'<span class="badge bg-secondary">Inactive</span>' ?></td>
              <td class="small text-muted"><?= $u['last_login'] ? formatDateTime($u['last_login']) : '—' ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

  <?php if ($tab !== 'users'): ?>
    <div class="mt-4">
      <button type="submit" class="btn btn-dark"><i class="bi bi-save me-1"></i>Save Settings</button>
    </div>
  <?php endif; ?>

  </div>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
