<?php
// =====================================================
// TPA IMS — Unified Reminder Centre
// =====================================================
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/WhatsAppService.php';
require_once __DIR__ . '/../includes/EmailService.php';
startSecureSession();
requireRole(['admin', 'branch_manager', 'staff']);

$db  = getDB();
$tab = $_GET['tab'] ?? 'fee';

// ── Settings cache ─────────────────────────────────────────────────────────
$smtpOk = !empty(getSetting('smtp_host')) && !empty(getSetting('smtp_user'));
$waOk   = !empty(getSetting('whatsapp_token')) && !empty(getSetting('whatsapp_phone_number_id'));

// ── Handle single-invoice send ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_single'])) {
    verifyCsrf();
    $invId  = (int)$_POST['invoice_id'];
    $via    = $_POST['via'] ?? getSetting('reminder_fee_channel', 'both');
    $inv    = $db->prepare("SELECT i.id, i.invoice_number, i.amount_due, i.due_date, i.period_label,
        i.payment_token,
        CONCAT(s.first_name,' ',s.last_name) as student_name,
        p.id as parent_id, p.parent_name, p.email, p.phone, p.whatsapp
        FROM invoices i JOIN students s ON s.id=i.student_id
        LEFT JOIN student_parents p ON p.student_id=s.id AND p.is_primary=1
        WHERE i.id=?");
    $inv->execute([$invId]); $inv = $inv->fetch();
    if ($inv) {
        $sent = 0;
        $wa = new WhatsAppService();
        $amount  = formatMoney($inv['amount_due']);
        $dueDate = formatDate($inv['due_date']);
        if (in_array($via, ['whatsapp','both']) && ($inv['whatsapp'] ?: $inv['phone'])) {
            $payMsg = "";
            if ($inv['payment_token']) {
                $payMsg = "\n💳 Pay Online: " . PaymentService::getPublicPaymentUrl($inv['payment_token']);
            }

            $msg = renderReminderTemplate(getSetting('wa_template_fee_reminder'),
                $inv['parent_name'], $inv['student_name'], $amount, $dueDate, $inv['invoice_number'], $inv['payment_token'], $payMsg);
            $wa->sendText($inv['whatsapp'] ?: $inv['phone'], $msg, $inv['parent_id']); $sent++;
        }
        if (in_array($via, ['email','both']) && $inv['email']) {
            EmailService::sendFeeReminder($inv['email'], $inv['parent_name'], $inv['student_name'],
                $amount, $dueDate, $inv['invoice_number'], $inv['period_label'] ?? 'Tuition Fee', 
                $inv['payment_token'] ?: '', $inv['parent_id']);
            $sent++;
        }
        // Track on invoice
        $db->prepare('UPDATE invoices SET reminder_sent_at=NOW(), reminder_count=reminder_count+1 WHERE id=?')->execute([$invId]);
        logActivity('fee_reminder_sent', "Reminder sent for invoice {$inv['invoice_number']}");
        setFlash('success', "Reminder sent to {$inv['parent_name']} via $via.");
    }
    header('Location: index.php?tab=fee'); exit;
}

// ── Handle bulk fee reminders ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_bulk'])) {
    verifyCsrf();
    $selected  = $_POST['invoice_ids'] ?? [];
    $via       = $_POST['via'] ?? getSetting('reminder_fee_channel', 'both');
    if (empty($selected)) { setFlash('danger', 'No invoices selected.'); header('Location: index.php?tab=fee'); exit; }

    $inList  = implode(',', array_map('intval', $selected));
    $invoices = $db->query("SELECT i.id, i.invoice_number, i.amount_due, i.due_date, i.period_label,
        i.payment_token,
        CONCAT(s.first_name,' ',s.last_name) as student_name,
        p.id as parent_id, p.parent_name, p.email, p.phone, p.whatsapp
        FROM invoices i JOIN students s ON s.id=i.student_id
        LEFT JOIN student_parents p ON p.student_id=s.id AND p.is_primary=1
        WHERE i.id IN ($inList)")->fetchAll();
    $wa   = new WhatsAppService(); $sent = 0;
    foreach ($invoices as $inv) {
        $amount  = formatMoney($inv['amount_due']);
        $dueDate = formatDate($inv['due_date']);
        // Build payment link from token (payment_token is stored on invoices, not payment_link_stripe)
        $baseUrl   = rtrim(getSetting('site_url_public') ?: str_replace('/admin','',SITE_URL), '/');
        $payLink   = $inv['payment_token'] ? $baseUrl . '/pay.php?token=' . $inv['payment_token'] : '';
        if (in_array($via, ['whatsapp','both']) && ($inv['whatsapp'] ?: $inv['phone'])) {
            $payMsg = $payLink ? "\n💳 Pay securely online: $payLink" : '';
            $msg = renderReminderTemplate(getSetting('wa_template_fee_reminder',''),
                $inv['parent_name'], $inv['student_name'], $amount, $dueDate, $inv['invoice_number'], $payLink, '', $payMsg);
            $wa->sendText($inv['whatsapp'] ?: $inv['phone'], $msg, $inv['parent_id']); $sent++;
        }
        if (in_array($via, ['email','both']) && $inv['email']) {
            EmailService::sendFeeReminder($inv['email'], $inv['parent_name'], $inv['student_name'],
                $amount, $dueDate, $inv['invoice_number'], $inv['period_label'] ?? 'Tuition Fee',
                $payLink, '', $inv['parent_id']);
            $sent++;
        }
        $db->prepare('UPDATE invoices SET reminder_sent_at=NOW(), reminder_count=reminder_count+1 WHERE id=?')->execute([$inv['id']]);
    }
    logActivity('fee_reminders_bulk', "Bulk: $sent reminders sent");
    setFlash('success', "$sent reminder(s) sent successfully.");
    header('Location: index.php?tab=fee'); exit;
}

// ── Handle absence reminder send ───────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_absence'])) {
    verifyCsrf();
    $attId = (int)$_POST['attendance_id'];
    $row   = $db->prepare("SELECT a.*, b.name as batch_name,
        CONCAT(s.first_name,' ',s.last_name) as student_name,
        p.id as parent_id, p.parent_name, p.email, p.phone, p.whatsapp
        FROM attendance a JOIN students s ON s.id=a.student_id
        JOIN batches b ON b.id=a.batch_id
        LEFT JOIN student_parents p ON p.student_id=s.id AND p.is_primary=1
        WHERE a.id=?");
    $row->execute([$attId]); $row = $row->fetch();
    if ($row) {
        $wa = new WhatsAppService();
        $date = formatDate($row['date']);
        if ($row['whatsapp'] ?: $row['phone']) {
            $msg = renderAbsenceTemplate(getSetting('wa_template_absence'),
                $row['parent_name'], $row['student_name'], $row['batch_name'], $date);
            $wa->sendText($row['whatsapp'] ?: $row['phone'], $msg, $row['parent_id']);
        }
        if ($row['email']) {
            EmailService::sendAbsenceNotification($row['email'], $row['parent_name'],
                $row['student_name'], $row['batch_name'], $date, $row['parent_id']);
        }
        $db->prepare('UPDATE attendance SET notified_at=NOW() WHERE id=?')->execute([$attId]);
        setFlash('success', "Absence notification sent for {$row['student_name']}.");
    }
    header('Location: index.php?tab=absence'); exit;
}

// ── Data queries ───────────────────────────────────────────────────────────

// Fee reminders data
$feeInvoices = $db->query("SELECT i.id, i.invoice_number, i.amount_due, i.due_date, i.status,
    i.reminder_sent_at, i.reminder_count,
    CONCAT(s.first_name,' ',s.last_name) as student_name,
    p.parent_name, p.email, p.phone, p.whatsapp
    FROM invoices i JOIN students s ON s.id=i.student_id
    LEFT JOIN student_parents p ON p.student_id=s.id AND p.is_primary=1
    WHERE i.status IN ('unpaid','overdue','partial')
    ORDER BY i.status DESC, i.due_date ASC")->fetchAll();

// Absence data (last 14 days, absent only)
$try_absence = true;
$absences = [];
try {
    $absences = $db->query("SELECT a.id, a.date, a.status, a.notified_at,
        CONCAT(s.first_name,' ',s.last_name) as student_name, s.id as student_id,
        b.name as batch_name,
        p.parent_name, p.email, p.phone, p.whatsapp
        FROM attendance a JOIN students s ON s.id=a.student_id
        JOIN batches b ON b.id=a.batch_id
        LEFT JOIN student_parents p ON p.student_id=s.id AND p.is_primary=1
        WHERE a.status='absent' AND a.date >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
        ORDER BY a.date DESC LIMIT 100")->fetchAll();
} catch (Exception $e) { $absences = []; }

// Communication stats
$commStats = $db->query("SELECT
    SUM(type='whatsapp' AND status='sent') as wa_sent,
    SUM(type='email' AND status='sent') as email_sent,
    SUM(status='failed') as failed,
    COUNT(*) as total
    FROM communications WHERE sent_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetch();

$overdueCount   = count(array_filter($feeInvoices, fn($i) => $i['status'] === 'overdue'));
$neverReminded  = count(array_filter($feeInvoices, fn($i) => !$i['reminder_sent_at']));
$absenceUnnotif = count(array_filter($absences, fn($a) => !$a['notified_at']));

// ── Helpers ────────────────────────────────────────────────────────────────
function renderReminderTemplate(string $tpl, string $parentName, string $childName,
    string $amount, string $dueDate, string $invoiceNum, string $paymentToken = '', string $payMsg = ''): string {
    if (!$tpl) {
        return "Dear {$parentName},\n\nPayment reminder from Talent Pool Academy.\n\nInvoice: {$invoiceNum}\nStudent: {$childName}\nAmount Due: {$amount}\nDue: {$dueDate}{$payMsg}\n\nBACS: " . getSetting('bank_name') . " | Acc: " . getSetting('bank_account') . " | Sort: " . getSetting('bank_sort_code') . " | Ref: {$childName} {$invoiceNum}\n\nThank you.";
    }
    $paymentUrl = $paymentToken ? PaymentService::getPublicPaymentUrl($paymentToken) : '';
    $msg = str_replace(
        ['{parent_name}','{child_name}','{amount}','{due_date}','{invoice_number}','{payment_url}'],
        [$parentName, $childName, $amount, $dueDate, $invoiceNum, $paymentUrl],
        $tpl
    );
    // Append payment link if missing from template
    if ($payMsg && strpos($msg, 'http') === false) {
        $msg .= "\n" . $payMsg;
    }
    return $msg;
}
function renderAbsenceTemplate(string $tpl, string $parentName, string $childName,
    string $batchName, string $date): string {
    return str_replace(
        ['{parent_name}','{child_name}','{batch_name}','{date}'],
        [$parentName, $childName, $batchName, $date],
        $tpl
    );
}

$page_title   = 'Reminder Centre';
$page_section = 'reminders';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
  <div>
    <h1><i class="bi bi-bell me-2" style="color:var(--gold);"></i>Reminder Centre</h1>
    <p class="text-muted mb-0 small">Manage all outgoing notifications — fee reminders, absence alerts, and more</p>
  </div>
  <a href="../settings/index.php?tab=reminders" class="btn btn-sm btn-outline-secondary"><i class="bi bi-gear me-1"></i>Reminder Settings</a>
</div>

<!-- API Status Banner -->
<?php if (!$waOk || !$smtpOk): ?>
<div class="alert alert-warning d-flex align-items-center gap-3 mb-4">
  <i class="bi bi-exclamation-triangle fs-5"></i>
  <div>
    <?php if (!$waOk): ?><strong>WhatsApp not configured</strong> — add your Meta API token in <a href="../settings/index.php?tab=whatsapp">WhatsApp Settings</a>.<br><?php endif; ?>
    <?php if (!$smtpOk): ?><strong>Email SMTP not configured</strong> — add your SMTP credentials in <a href="../settings/index.php?tab=smtp">Email Settings</a>.<?php endif; ?>
  </div>
</div>
<?php endif; ?>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
  <?php $kpis = [
    ['Overdue Invoices',     $overdueCount,             'exclamation-triangle','#fee2e2','#991b1b'],
    ['Never Received Reminder',$neverReminded,           'bell-slash',          '#fef3c7','#92400e'],
    ['Absences (14 days)',   count($absences),           'person-x',            '#f0fdf4','#166534'],
    ['Unnotified Absences',  $absenceUnnotif,           'bell',                '#fef3c7','#92400e'],
    ['WA Sent (30 days)',    $commStats['wa_sent'] ?? 0, 'whatsapp',            '#d1fae5','#065f46'],
    ['Emails Sent (30 days)',$commStats['email_sent']??0,'envelope-check',      '#e0e7ff','#3730a3'],
  ];
  foreach ($kpis as [$label,$val,$icon,$bg,$color]): ?>
  <div class="col-6 col-lg-2">
    <div class="stat-card d-flex align-items-center gap-3">
      <div class="stat-icon" style="background:<?= $bg ?>;color:<?= $color ?>;"><i class="bi bi-<?= $icon ?>"></i></div>
      <div><div class="stat-label"><?= $label ?></div><div class="stat-value" style="font-size:1.5rem;"><?= $val ?></div></div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs mb-4">
  <?php foreach (['fee'=>'💰 Fee Reminders','absence'=>'📋 Absence Alerts','log'=>'📨 Communication Log'] as $t=>$l): ?>
    <li class="nav-item"><a class="nav-link <?= $tab===$t?'active':'' ?>" href="?tab=<?= $t ?>"><?= $l ?> <?php if($t==='fee' && $overdueCount) echo '<span class="badge bg-danger ms-1">'.$overdueCount.'</span>'; ?></a></li>
  <?php endforeach; ?>
</ul>

<?php if ($tab === 'fee'): ?>
<!-- ════════════════════════════════════════════════════════════
     FEE REMINDERS TAB
════════════════════════════════════════════════════════════ -->
<?php if (empty($feeInvoices)): ?>
  <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>All invoices are paid. No reminders needed! 🎉</div>
<?php else: ?>
<form method="POST" id="bulkForm">
  <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
  <input type="hidden" name="send_bulk" value="1">

  <div class="stat-card mb-3">
    <div class="d-flex align-items-center gap-4 flex-wrap">
      <div>
        <label class="form-label fw-600 small mb-1">Send via</label>
        <div class="d-flex gap-3">
          <?php $ch = getSetting('reminder_fee_channel','both'); ?>
          <label class="form-check-label"><input type="radio" name="via" value="both" class="form-check-input me-1" <?= $ch==='both'?'checked':'' ?>> WhatsApp & Email</label>
          <label class="form-check-label"><input type="radio" name="via" value="whatsapp" class="form-check-input me-1" <?= $ch==='whatsapp'?'checked':'' ?>> WhatsApp only</label>
          <label class="form-check-label"><input type="radio" name="via" value="email" class="form-check-input me-1" <?= $ch==='email'?'checked':'' ?>> Email only</label>
        </div>
      </div>
      <div class="ms-auto d-flex gap-2">
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectAll(true)">Select All Overdue</button>
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectAll(false)">Clear</button>
        <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-bell me-1"></i>Send Selected</button>
      </div>
    </div>
  </div>

  <div class="tpa-table table-responsive">
    <table class="table mb-0">
      <thead><tr>
        <th style="width:36px;"><input type="checkbox" id="chk-all" onclick="document.querySelectorAll('.inv-chk').forEach(c=>c.checked=this.checked)"></th>
        <th>Invoice</th><th>Student</th><th>Parent / Contact</th>
        <th>Amount</th><th>Due Date</th><th>Status</th>
        <th>Last Reminded</th><th>Times Sent</th>
        <th style="width:50px;"></th>
      </tr></thead>
      <tbody>
        <?php foreach ($feeInvoices as $inv):
          $over = $inv['status'] === 'overdue';
          $hasWA = !empty($inv['whatsapp']) || !empty($inv['phone']);
          $hasEM = !empty($inv['email']);
        ?>
        <tr class="<?= $over ? 'table-danger-subtle' : '' ?>">
          <td><input type="checkbox" name="invoice_ids[]" class="inv-chk" value="<?= $inv['id'] ?>" <?= $over?'checked':'' ?>></td>
          <td class="fw-700 small"><?= h($inv['invoice_number']) ?></td>
          <td class="fw-600"><?= h($inv['student_name']) ?></td>
          <td>
            <div class="small fw-600"><?= h($inv['parent_name'] ?? '—') ?></div>
            <div class="d-flex gap-1 mt-1">
              <span class="badge <?= $hasWA ? 'bg-success' : 'bg-secondary' ?>" title="WhatsApp"><i class="bi bi-whatsapp"></i></span>
              <span class="badge <?= $hasEM ? 'bg-primary' : 'bg-secondary' ?>" title="Email"><i class="bi bi-envelope"></i></span>
            </div>
          </td>
          <td class="fw-700 <?= $over ? 'text-danger' : '' ?>"><?= formatMoney($inv['amount_due']) ?></td>
          <td class="small <?= $over ? 'text-danger fw-700' : '' ?>"><?= formatDate($inv['due_date']) ?></td>
          <td><?= invoiceStatusBadge($inv['status']) ?></td>
          <td class="small <?= !$inv['reminder_sent_at'] ? 'text-muted fst-italic' : '' ?>">
            <?= $inv['reminder_sent_at'] ? formatDateTime($inv['reminder_sent_at']) : 'Never' ?>
          </td>
          <td class="text-center">
            <?php if ($inv['reminder_count'] > 0): ?>
              <span class="badge bg-light text-dark border"><?= $inv['reminder_count'] ?>×</span>
            <?php else: ?>
              <span class="text-muted small">—</span>
            <?php endif; ?>
          </td>
          <td>
            <form method="POST" class="d-inline">
              <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
              <input type="hidden" name="send_single" value="1">
              <input type="hidden" name="invoice_id" value="<?= $inv['id'] ?>">
              <input type="hidden" name="via" value="<?= h(getSetting('reminder_fee_channel','both')) ?>">
              <button class="btn btn-sm btn-outline-primary py-0 px-2" title="Send reminder now" style="font-size:.75rem;">
                <i class="bi bi-send"></i>
              </button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</form>
<?php endif; ?>

<?php elseif ($tab === 'absence'): ?>
<!-- ════════════════════════════════════════════════════════════
     ABSENCE ALERTS TAB
════════════════════════════════════════════════════════════ -->
<div class="d-flex justify-content-between align-items-center mb-3">
  <p class="text-muted mb-0 small">Showing absences from the last 14 days.</p>
  <?php if (getSetting('reminder_absence_enabled','1') === '0'): ?>
    <span class="badge bg-secondary">Auto-notifications disabled — <a href="../settings/index.php?tab=reminders" class="text-white">Enable in Settings</a></span>
  <?php endif; ?>
</div>

<?php if (empty($absences)): ?>
  <div class="stat-card text-center py-5 text-muted">
    <i class="bi bi-person-check fs-1 d-block mb-3"></i>
    <h5>No absences in the last 14 days.</h5>
  </div>
<?php else: ?>
<div class="tpa-table table-responsive">
  <table class="table table-hover mb-0">
    <thead><tr><th>Student</th><th>Batch</th><th>Date</th><th>Parent</th><th>Contact</th><th>Notified</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($absences as $a):
        $notified = !empty($a['notified_at']);
        $hasWA    = !empty($a['whatsapp']) || !empty($a['phone']);
        $hasEM    = !empty($a['email']);
      ?>
      <tr>
        <td class="fw-600"><a href="../students/view.php?id=<?= $a['student_id'] ?>"><?= h($a['student_name']) ?></a></td>
        <td class="small"><?= h($a['batch_name']) ?></td>
        <td class="small"><?= formatDate($a['date']) ?></td>
        <td class="small fw-600"><?= h($a['parent_name'] ?? '—') ?></td>
        <td>
          <span class="badge <?= $hasWA?'bg-success':'bg-secondary' ?>" title="WhatsApp"><i class="bi bi-whatsapp"></i></span>
          <span class="badge <?= $hasEM?'bg-primary':'bg-secondary' ?>" title="Email"><i class="bi bi-envelope"></i></span>
        </td>
        <td>
          <?php if ($notified): ?>
            <span class="badge bg-success"><i class="bi bi-check me-1"></i>Sent <?= formatDateTime($a['notified_at']) ?></span>
          <?php else: ?>
            <span class="badge bg-warning text-dark">Not sent</span>
          <?php endif; ?>
        </td>
        <td>
          <?php if (!$notified && ($hasWA || $hasEM)): ?>
          <form method="POST" class="d-inline">
            <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
            <input type="hidden" name="send_absence" value="1">
            <input type="hidden" name="attendance_id" value="<?= $a['id'] ?>">
            <button class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size:.75rem;" title="Send absence notification">
              <i class="bi bi-send"></i> Notify
            </button>
          </form>
          <?php else: ?>
            <span class="text-muted small">—</span>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<?php elseif ($tab === 'log'): ?>
<!-- ════════════════════════════════════════════════════════════
     COMMUNICATION LOG TAB
════════════════════════════════════════════════════════════ -->
<?php
$fType  = $_GET['type'] ?? '';
$logPage = max(1,(int)($_GET['page']??1));
$perPage = 40;
$logWhere = '1=1'; $logParams = [];
if ($fType) { $logWhere .= ' AND type=?'; $logParams[] = $fType; }
$logTotal = $db->prepare("SELECT COUNT(*) FROM communications WHERE $logWhere");
$logTotal->execute($logParams); $logTotal = (int)$logTotal->fetchColumn();
$logOffset = ($logPage-1)*$perPage;
$logs = $db->prepare("SELECT c.*, COALESCE(c.sent_at, c.created_at) as display_time, u.name as sent_by_name FROM communications c LEFT JOIN users u ON u.id=c.sent_by WHERE $logWhere ORDER BY COALESCE(c.sent_at,c.created_at) DESC LIMIT $perPage OFFSET $logOffset");
$logs->execute($logParams); $logs = $logs->fetchAll();
?>
<div class="d-flex gap-2 mb-3 flex-wrap">
  <?php foreach ([''=> 'All','whatsapp'=>'WhatsApp','email'=>'Email'] as $v=>$l): ?>
    <a href="?tab=log&type=<?= $v ?>" class="btn btn-sm <?= $fType===$v?'btn-dark':'btn-outline-secondary' ?>"><?= $l ?></a>
  <?php endforeach; ?>
</div>
<div class="tpa-table table-responsive">
  <table class="table table-hover mb-0">
    <thead><tr><th>Type</th><th>To</th><th>Message / Subject</th><th>Status</th><th>Sent By</th><th>Date/Time</th></tr></thead>
    <tbody>
      <?php foreach ($logs as $c):
        $sBadge = ['sent'=>'info text-dark','delivered'=>'success','failed'=>'danger','pending'=>'warning text-dark'];
        $cls = $sBadge[$c['status']] ?? 'secondary';
      ?>
      <tr>
        <td>
          <?php if ($c['type']==='whatsapp'): ?>
            <span class="badge bg-success"><i class="bi bi-whatsapp me-1"></i>WA</span>
          <?php elseif ($c['type']==='email'): ?>
            <span class="badge bg-primary"><i class="bi bi-envelope me-1"></i>Email</span>
          <?php else: ?>
            <span class="badge bg-secondary"><?= h($c['type']) ?></span>
          <?php endif; ?>
        </td>
        <td class="small fw-600"><?= h($c['to_number_or_email'] ?? '—') ?></td>
        <td class="small text-muted" style="max-width:280px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;" title="<?= h($c['message'] ?? $c['template_name'] ?? '') ?>">
          <?= h(mb_substr($c['message'] ?? $c['template_name'] ?? '—', 0, 80)) ?>
        </td>
        <td><span class="badge bg-<?= $cls ?>"><?= h(ucfirst($c['status'])) ?></span>
          <?php if ($c['status']==='failed' && $c['error_message']): ?>
            <i class="bi bi-info-circle text-danger ms-1" title="<?= h($c['error_message']) ?>"></i>
          <?php endif; ?>
        </td>
        <td class="small"><?= h($c['sent_by_name'] ?? 'Auto/System') ?></td>
        <td class="small text-muted"><?= formatDateTime($c['display_time']) ?></td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($logs)): ?>
        <tr><td colspan="6" class="text-center text-muted py-4">No communications recorded yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?= paginate($logTotal, $logPage, $perPage, '?tab=log&type='.urlencode($fType)) ?>
<?php endif; ?>

<script>
function selectAll(v) {
  document.querySelectorAll('.inv-chk').forEach(c => c.checked = typeof v === 'boolean' ? v : c.closest('tr').classList.contains('table-danger-subtle'));
}
// On page load default to overdue checked
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.inv-chk').forEach(c => { /* already set via PHP checked attr */ });
});
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
