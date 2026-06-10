<?php
// =====================================================
// TPA IMS — Send Fee Reminders
// =====================================================

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/WhatsAppService.php';
require_once __DIR__ . '/../includes/EmailService.php';
startSecureSession();
requireLogin(SITE_URL . '/login.php');

$db = getDB();

// Build invoice list with reminder tracking
$overdueInvoices = $db->query("SELECT i.id, i.invoice_number, i.amount_due, i.due_date,
    i.period_label, i.status, i.reminder_sent_at, i.reminder_count,
    i.payment_token,
    CONCAT(s.first_name,' ',s.last_name) as student_name, s.id as student_id,
    p.id as parent_id, p.parent_name, p.email, p.phone, p.whatsapp
    FROM invoices i
    JOIN students s ON i.student_id = s.id
    LEFT JOIN student_parents p ON p.student_id = s.id AND p.is_primary = 1
    WHERE i.status IN ('unpaid','overdue','partial')
    ORDER BY i.status DESC, i.due_date ASC")->fetchAll();

// Pre-selected invoice from URL
$preSelected = (int)($_GET['invoice_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_reminders'])) {
    verifyCsrf();
    $selected = $_POST['invoice_ids'] ?? [];
    $via      = $_POST['via'] ?? 'both';

    if (empty($selected)) {
        setFlash('danger', 'Please select at least one invoice.');
        header('Location: reminders.php'); exit;
    }

    $wa   = new WhatsAppService();
    $sent = 0;
    $failed = 0;

    foreach ($selected as $invId) {
        $inv = null;
        foreach ($overdueInvoices as $row) { if ($row['id'] == $invId) { $inv = $row; break; } }
        if (!$inv) continue;

        $amount  = formatMoney($inv['amount_due']);
        $dueDate = formatDate($inv['due_date']);
        $tpl     = getSetting('wa_template_fee_reminder', '');

        $waNum = $inv['whatsapp'] ?: $inv['phone'];

        if (in_array($via, ['whatsapp','both']) && $waNum) {
            $payMsg = "";
            if ($inv['payment_token']) {
                $payMsg = "\n💳 Pay Online: " . PaymentService::getPublicPaymentUrl($inv['payment_token']);
            }

            $msg = $tpl
                ? str_replace(['{parent_name}','{child_name}','{amount}','{due_date}','{invoice_number}','{payment_url}'],
                    [$inv['parent_name'],$inv['student_name'],$amount,$dueDate,$inv['invoice_number'], PaymentService::getPublicPaymentUrl($inv['payment_token'])], $tpl)
                : "Dear {$inv['parent_name']},\n\nPayment reminder from Talent Pool Academy.\n\nInvoice: {$inv['invoice_number']}\nStudent: {$inv['student_name']}\nAmount Due: {$amount}\nDue: {$dueDate}{$payMsg}\n\nBACS: " . getSetting('bank_name') . " | Acc: " . getSetting('bank_account') . " | Sort: " . getSetting('bank_sort_code') . " | Ref: {$inv['student_name']}\n\nThank you.";
            
            // Append payment link if missing from template
            if ($payMsg && $tpl && strpos($msg, 'http') === false) {
                $msg .= "\n" . $payMsg;
            }
            $ok = $wa->sendText($waNum, $msg, $inv['parent_id']);
            $ok ? $sent++ : $failed++;
        }

        if (in_array($via, ['email','both']) && $inv['email']) {
            $ok = EmailService::sendFeeReminder($inv['email'], $inv['parent_name'], $inv['student_name'],
                $amount, $dueDate, $inv['invoice_number'], $inv['period_label'] ?? 'Tuition Fee', 
                $inv['payment_token'] ?: '', $inv['parent_id']);
            $ok ? $sent++ : $failed++;
        }

        // Track on invoice
        $db->prepare('UPDATE invoices SET reminder_sent_at=NOW(), reminder_count=reminder_count+1 WHERE id=?')
           ->execute([$invId]);
    }

    logActivity('fee_reminders_sent', "Sent $sent fee reminders" . ($failed ? ", $failed failed" : ''));
    $msg = "$sent reminder(s) sent successfully.";
    if ($failed) $msg .= " ⚠️ $failed failed (check Communications log for details).";
    setFlash($failed ? 'warning' : 'success', $msg);
    header('Location: reminders.php'); exit;
}

$waOk   = !empty(getSetting('whatsapp_token')) && !empty(getSetting('whatsapp_phone_number_id'));
$smtpOk = !empty(getSetting('smtp_host')) && !empty(getSetting('smtp_user'));

$page_title   = 'Fee Reminders';
$page_section = 'fees';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
  <div>
    <h1><i class="bi bi-bell me-2" style="color:var(--gold);"></i>Fee Reminders</h1>
    <p class="text-muted mb-0 small">Send payment reminders to parents via WhatsApp and/or Email</p>
  </div>
  <div class="d-flex gap-2">
    <a href="index.php" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back to Fees</a>
    <a href="../../reminders/index.php" class="btn btn-sm btn-outline-dark"><i class="bi bi-bell me-1"></i>Reminder Centre</a>
  </div>
</div>

<!-- API status banners -->
<?php if (!$waOk || !$smtpOk): ?>
<div class="alert alert-warning d-flex align-items-start gap-3 mb-4">
  <i class="bi bi-exclamation-triangle fs-5 mt-1"></i>
  <div>
    <?php if (!$waOk): ?><strong>WhatsApp not configured</strong> — add your Meta API token in <a href="../settings/index.php?tab=whatsapp">WhatsApp Settings</a>. WhatsApp reminders will be skipped.<br><?php endif; ?>
    <?php if (!$smtpOk): ?><strong>Email SMTP not configured</strong> — add credentials in <a href="../settings/index.php?tab=smtp">Email Settings</a>. Email reminders will be skipped.<?php endif; ?>
  </div>
</div>
<?php endif; ?>

<?php if (empty($overdueInvoices)): ?>
  <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>No unpaid or overdue invoices. All fees are up to date! 🎉</div>
<?php else: ?>

<form method="POST" id="reminderForm">
  <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
  <input type="hidden" name="send_reminders" value="1">

  <!-- Send via selector — styled pill buttons -->
  <div class="stat-card mb-4">
    <div class="d-flex align-items-center gap-4 flex-wrap">
      <div>
        <div class="fw-700 small text-uppercase mb-2" style="letter-spacing:.06em;color:#666;">Send via</div>
        <div class="d-flex gap-2" id="channelPicker">
          <?php
          $defCh = getSetting('reminder_fee_channel', 'both');
          $channels = [
            'both'     => ['<i class="bi bi-whatsapp me-1"></i><i class="bi bi-envelope me-1"></i> WhatsApp & Email', 'success'],
            'whatsapp' => ['<i class="bi bi-whatsapp me-1"></i> WhatsApp only', 'success'],
            'email'    => ['<i class="bi bi-envelope me-1"></i> Email only', 'primary'],
          ];
          foreach ($channels as $val => [$lbl, $color]):
          ?>
          <label class="channel-pill btn btn-outline-<?= $color ?> btn-sm <?= $defCh===$val?'active':'' ?>"
                 for="via_<?= $val ?>" style="border-radius:999px;">
            <input type="radio" name="via" id="via_<?= $val ?>" value="<?= $val ?>"
                   class="d-none via-radio" <?= $defCh===$val?'checked':'' ?>>
            <?= $lbl ?>
          </label>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="ms-auto d-flex gap-2 flex-wrap">
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectOverdue()">
          <i class="bi bi-exclamation-triangle me-1"></i>Select Overdue
        </button>
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectAll(true)">Select All</button>
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectAll(false)">Clear</button>
        <button type="submit" class="btn btn-sm btn-danger fw-700" id="sendBtn">
          <i class="bi bi-send me-1"></i>Send <span id="sendCount">0</span> Reminder(s)
        </button>
      </div>
    </div>
  </div>

  <!-- Table -->
  <div class="tpa-table table-responsive mb-3">
    <table class="table mb-0" id="remTable">
      <thead><tr>
        <th style="width:36px;"><input type="checkbox" id="chk-all" onclick="selectAll(this.checked)"></th>
        <th>Invoice</th>
        <th>Student</th>
        <th>Parent</th>
        <th>Amount</th>
        <th>Due Date</th>
        <th>Status</th>
        <th>Channels</th>
        <th>Last Reminded</th>
        <th>Times</th>
      </tr></thead>
      <tbody>
        <?php foreach ($overdueInvoices as $inv):
          $over   = $inv['status'] === 'overdue';
          $hasWA  = !empty($inv['whatsapp']) || !empty($inv['phone']);
          $hasEM  = !empty($inv['email']);
          $isPreSel = ($preSelected && $preSelected == $inv['id']);
        ?>
        <tr class="<?= $over ? 'table-danger-subtle' : '' ?>" data-overdue="<?= $over?1:0 ?>">
          <td><input type="checkbox" name="invoice_ids[]" class="inv-chk" value="<?= $inv['id'] ?>"
                     <?= ($over || $isPreSel) ? 'checked' : '' ?> onchange="updateCount()"></td>
          <td class="fw-700 small"><?= h($inv['invoice_number']) ?></td>
          <td class="fw-600"><?= h($inv['student_name']) ?></td>
          <td>
            <div class="small fw-600"><?= h($inv['parent_name'] ?? '—') ?></div>
            <div class="small text-muted"><?= h($inv['phone'] ?? '') ?></div>
          </td>
          <td class="fw-700 <?= $over ? 'text-danger' : '' ?>"><?= formatMoney($inv['amount_due']) ?></td>
          <td class="small <?= $over ? 'text-danger fw-700' : '' ?>"><?= formatDate($inv['due_date']) ?></td>
          <td><?= invoiceStatusBadge($inv['status']) ?></td>
          <td>
            <!-- Live channel badges update with the radio selection -->
            <span class="ch-badge-wa badge <?= $hasWA ? 'bg-success' : 'bg-secondary' ?>"
                  title="<?= $hasWA ? 'WhatsApp available: '.h($inv['whatsapp'] ?: $inv['phone']) : 'No WhatsApp/phone' ?>">
              <i class="bi bi-whatsapp me-1"></i><?= $hasWA ? '✓' : '✗' ?>
            </span>
            <span class="ch-badge-em badge <?= $hasEM ? 'bg-primary' : 'bg-secondary' ?>"
                  title="<?= $hasEM ? 'Email: '.h($inv['email']) : 'No email' ?>">
              <i class="bi bi-envelope me-1"></i><?= $hasEM ? '✓' : '✗' ?>
            </span>
          </td>
          <td class="small <?= !$inv['reminder_sent_at'] ? 'text-muted fst-italic' : '' ?>">
            <?= $inv['reminder_sent_at'] ? formatDateTime($inv['reminder_sent_at']) : 'Never sent' ?>
          </td>
          <td class="text-center">
            <?php if ($inv['reminder_count'] > 0): ?>
              <span class="badge bg-light text-dark border"><?= $inv['reminder_count'] ?>×</span>
            <?php else: ?>
              <span class="text-muted small">—</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</form>
<?php endif; ?>

<style>
.channel-pill { transition: all .15s; font-size: .8rem; }
.channel-pill.active { color: #fff !important; }
.channel-pill.btn-outline-success.active { background: #16a34a; border-color: #16a34a; }
.channel-pill.btn-outline-primary.active { background: #2563eb; border-color: #2563eb; }
/* Dim unavailable channel badges */
.ch-hidden { opacity: .2; }
</style>

<script>
// ── Channel pill selection ──────────────────────────────────────────────────
document.querySelectorAll('.channel-pill').forEach(pill => {
  pill.addEventListener('click', function() {
    document.querySelectorAll('.channel-pill').forEach(p => p.classList.remove('active'));
    this.classList.add('active');
    this.querySelector('.via-radio').checked = true;
    updateChannelBadges(this.querySelector('.via-radio').value);
  });
});

function updateChannelBadges(via) {
  document.querySelectorAll('.ch-badge-wa').forEach(b => {
    b.classList.toggle('ch-hidden', via === 'email');
  });
  document.querySelectorAll('.ch-badge-em').forEach(b => {
    b.classList.toggle('ch-hidden', via === 'whatsapp');
  });
}

// ── Checkbox helpers ────────────────────────────────────────────────────────
function selectAll(v) {
  document.querySelectorAll('.inv-chk').forEach(c => c.checked = v);
  const hdr = document.getElementById('chk-all');
  if (hdr) hdr.checked = v;
  updateCount();
}

function selectOverdue() {
  document.querySelectorAll('.inv-chk').forEach(c => {
    c.checked = c.closest('tr').dataset.overdue === '1';
  });
  updateCount();
}

function updateCount() {
  const n = document.querySelectorAll('.inv-chk:checked').length;
  document.getElementById('sendCount').textContent = n;
  document.getElementById('sendBtn').disabled = n === 0;
}

// ── Init ─────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  updateCount();
  // Set initial channel badge state from checked radio
  const checkedVia = document.querySelector('.via-radio:checked');
  if (checkedVia) updateChannelBadges(checkedVia.value);
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
