<?php
// =====================================================
// TPA — Parent Portal: Fees / Invoices
// =====================================================

// ── AJAX POST handler — must run BEFORE any HTML output ─────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'claim_bacs') {
    require_once __DIR__ . '/../admin/includes/auth.php';
    require_once __DIR__ . '/../admin/includes/functions.php';
    startSecureSession();
    requireRole(['parent','admin']);
    $db     = getDB();
    $userId = currentUserId();

    header('Content-Type: application/json');

    if (!verifyCsrf(false)) {
        echo json_encode(['ok'=>false,'msg'=>'Security token expired. Please refresh the page and try again.']); exit;
    }

    $invId   = (int)($_POST['invoice_id'] ?? 0);
    $payRef  = trim($_POST['reference'] ?? '');
    $payDate = trim($_POST['payment_date'] ?? date('Y-m-d'));
    $notes   = trim($_POST['notes'] ?? '');

    // Verify invoice belongs to one of this user's children (secure lookup)
    $myStudents = $db->prepare("SELECT s.id FROM students s JOIN student_parents sp ON sp.student_id=s.id JOIN users u ON u.email=sp.email WHERE u.id=?");
    $myStudents->execute([$userId]);
    $myStudentIds = array_column($myStudents->fetchAll(), 'id');

    if (empty($myStudentIds)) {
        echo json_encode(['ok'=>false,'msg'=>'No children linked to your account.']); exit;
    }

    $placeholders = implode(',', array_fill(0, count($myStudentIds), '?'));
    $inv = $db->prepare("SELECT id, amount_due, student_id, status FROM invoices WHERE id=? AND student_id IN ($placeholders)");
    $inv->execute(array_merge([$invId], $myStudentIds));
    $inv = $inv->fetch();

    if (!$inv) {
        echo json_encode(['ok'=>false,'msg'=>'Invoice not found.']); exit;
    }
    if ($inv['status'] === 'paid') {
        echo json_encode(['ok'=>false,'msg'=>'This invoice is already marked as paid.']); exit;
    }

    $existing = $db->prepare('SELECT id FROM payments WHERE invoice_id=? AND method="bacs" AND reconciled=0');
    $existing->execute([$invId]);
    if ($existing->fetch()) {
        echo json_encode(['ok'=>false,'msg'=>'A bank transfer claim is already awaiting verification for this invoice.']); exit;
    }

    $db->prepare('INSERT INTO payments (invoice_id, student_id, amount, payment_date, method, reference, notes, recorded_by, reconciled)
                  VALUES (?,?,?,?,?,?,?,?,0)')
       ->execute([$invId, $inv['student_id'], $inv['amount_due'], $payDate ?: date('Y-m-d'), 'bacs', $payRef ?: null, $notes ?: null, $userId]);

    logActivity('parent_bacs_claim', "Parent claimed BACS payment for invoice #{$invId}, student #{$inv['student_id']}, ref: $payRef");
    echo json_encode(['ok'=>true,'msg'=>'Thank you! Your payment has been recorded and is awaiting verification by our team.']); exit;
}

// ── Normal page load ─────────────────────────────────────────────────────────
$page_title   = 'Fee Invoices';
$page_section = 'fees';
require_once __DIR__ . '/includes/header.php';

if (!$activeChild) {
    echo '<div class="alert alert-warning">No child linked to your account.</div>';
    require_once __DIR__ . '/includes/footer.php'; exit;
}
$childId = $activeChild['id'];

// ── Fetch invoices with pending BACS claim flag ──────────────────────────────
$invoices = $db->prepare("
    SELECT i.*,
           (SELECT id FROM payments WHERE invoice_id=i.id AND method='bacs' AND reconciled=0 LIMIT 1) AS pending_bacs_id,
           (SELECT reference FROM payments WHERE invoice_id=i.id AND method='bacs' AND reconciled=0 LIMIT 1) AS pending_bacs_ref
    FROM invoices i
    WHERE i.student_id=?
    ORDER BY
        FIELD(i.status,'overdue','unpaid','partial','paid','draft','cancelled','refunded'),
        i.due_date ASC
");
$invoices->execute([$childId]); $invoices = $invoices->fetchAll();

$totalDue     = array_sum(array_map(fn($i) => in_array($i['status'],['unpaid','overdue']) ? $i['amount_due'] : 0, $invoices));
$overdueCount = count(array_filter($invoices, fn($i)=>$i['status']==='overdue'));
$pendingCount = count(array_filter($invoices, fn($i)=>!empty($i['pending_bacs_id'])));

// Bank details from settings
$bankName    = getSetting('bank_name','');
$bankAccount = getSetting('bank_account','');
$bankSort    = getSetting('bank_sort_code','');
$bacsPrefix  = getSetting('bacs_reference_prefix','');
$csrfToken   = csrfToken();

// Pay-online base URL (root of project)
$payBase = rtrim(str_replace('/admin','',SITE_URL),'/');
?>

<div class="page-header">
  <div>
    <h1><i class="bi bi-receipt me-2 text-primary"></i>Fee Invoices</h1>
    <p class="text-muted mb-0">Billing history for <?= h($activeChild['name']) ?></p>
  </div>
  <?php if ($totalDue > 0): ?>
  <div class="text-end">
    <div class="text-muted small fw-600 text-uppercase" style="letter-spacing:.06em;">Total Balance Due</div>
    <h2 class="fw-900 text-danger mb-0">£<?= number_format($totalDue, 2) ?></h2>
    <?php if ($overdueCount > 0): ?>
      <span class="badge bg-danger mt-1"><?= $overdueCount ?> overdue</span>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</div>

<!-- ── How to Pay Banner (only when there are outstanding invoices) ─────────── -->
<?php if ($totalDue > 0): ?>
<div class="how-to-pay-banner">
  <div class="row g-0 align-items-center">
    <div class="col-auto me-3"><i class="bi bi-info-circle-fill" style="font-size:1.6rem;color:var(--gold);"></i></div>
    <div class="col">
      <div class="fw-700" style="font-size:.92rem;">How to pay your fees</div>
      <div style="font-size:.82rem;color:rgba(255,255,255,.8);margin-top:2px;">
        Use <strong>Pay Online</strong> for instant card payment, or <strong>Bank Transfer</strong> to pay by BACS — then click <em>"I've Paid by Bank Transfer"</em> so our team can verify it.
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<?php if ($pendingCount > 0): ?>
<div class="alert alert-warning d-flex align-items-start gap-2 mb-4">
  <i class="bi bi-hourglass-split mt-1"></i>
  <div><strong><?= $pendingCount ?> invoice<?= $pendingCount>1?'s':'' ?></strong> ha<?= $pendingCount>1?'ve':'s' ?> a bank transfer awaiting admin verification. We'll confirm shortly.</div>
</div>
<?php endif; ?>

<div class="row g-4">

  <!-- ── Invoice Cards ──────────────────────────────────────────────────── -->
  <div class="col-lg-8">

    <?php if (empty($invoices)): ?>
      <div class="stat-card text-center py-5">
        <i class="bi bi-check-circle-fill" style="font-size:2.5rem;color:#22c55e;"></i>
        <div class="fw-700 mt-3">No invoices yet.</div>
        <div class="text-muted small mt-1">Invoices will appear here once they are raised by the academy.</div>
      </div>
    <?php endif; ?>

    <?php foreach ($invoices as $inv):
      $isOverdue = $inv['status'] === 'overdue';
      $isPaid    = $inv['status'] === 'paid';
      $isPartial = $inv['status'] === 'partial';
      $isUnpaid  = $inv['status'] === 'unpaid';
      $hasPending= !empty($inv['pending_bacs_id']);
      $canPay    = !$isPaid && !$hasPending;
      $hasToken  = !empty($inv['payment_token']);

      $statusColor = $isOverdue ? '#dc2626' : ($isPaid ? '#16a34a' : ($isPartial ? '#d97706' : '#2563eb'));
      $statusBg    = $isOverdue ? '#fef2f2' : ($isPaid ? '#f0fdf4' : ($isPartial ? '#fffbeb' : '#eff6ff'));
      $cardBorder  = $hasPending ? '#f59e0b' : ($isOverdue ? '#fca5a5' : ($isPaid ? '#bbf7d0' : '#e2e8f0'));
    ?>
    <div class="invoice-card" style="border-color:<?= $cardBorder ?>;" data-inv-id="<?= $inv['id'] ?>">

      <!-- Top row: Invoice # + Status badge -->
      <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
        <div>
          <div class="fw-800" style="font-size:1rem;color:var(--navy);"><?= h($inv['invoice_number']) ?></div>
          <div class="text-muted small mt-1"><?= h($inv['period_label'] ?? 'Invoice') ?></div>
        </div>
        <div class="text-end flex-shrink-0">
          <?php if ($hasPending): ?>
            <span class="status-badge" style="background:#fef3c7;color:#92400e;border:1px solid #fde68a;">
              <i class="bi bi-hourglass-split me-1"></i>Awaiting Verification
            </span>
          <?php else: ?>
            <span class="status-badge" style="background:<?= $statusBg ?>;color:<?= $statusColor ?>;">
              <?= $isOverdue ? '<i class="bi bi-exclamation-circle me-1"></i>' : '' ?><?= ucfirst($inv['status']) ?>
            </span>
          <?php endif; ?>
        </div>
      </div>

      <!-- Amount + Due Date row -->
      <div class="row g-3 mb-3">
        <div class="col-6">
          <div class="inv-meta-label">Amount Due</div>
          <div class="fw-900" style="font-size:1.35rem;color:<?= $isOverdue?'#dc2626':($isPaid?'#16a34a':'var(--navy)') ?>;">
            £<?= number_format($inv['amount_due'],2) ?>
          </div>
        </div>
        <div class="col-6 text-end">
          <div class="inv-meta-label">Due Date</div>
          <div class="fw-700 <?= $isOverdue?'text-danger':'' ?>" style="font-size:.95rem;">
            <?= date('d M Y', strtotime($inv['due_date'])) ?>
            <?php if ($isOverdue): ?><div class="small text-danger fw-700">OVERDUE</div><?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Notes / description -->
      <?php if (!empty($inv['notes'])): ?>
        <div class="small text-muted mb-3" style="background:#f8fafc;padding:8px 12px;border-radius:6px;border-left:3px solid #e2e8f0;">
          <?= h($inv['notes']) ?>
        </div>
      <?php endif; ?>

      <!-- Pending BACS info -->
      <?php if ($hasPending): ?>
        <div class="pending-bacs-info">
          <i class="bi bi-clock me-1"></i>
          Bank transfer recorded<?= $inv['pending_bacs_ref'] ? ' &mdash; Ref: <strong>'.h($inv['pending_bacs_ref']).'</strong>' : '' ?>.
          Our team will verify and mark this invoice as paid.
        </div>
      <?php endif; ?>

      <!-- Action buttons -->
      <?php if ($canPay && ($isUnpaid || $isOverdue || $isPartial)): ?>
      <div class="inv-actions">
        <?php if ($hasToken): ?>
        <a href="<?= $payBase ?>/pay-invoice.php?token=<?= urlencode($inv['payment_token']) ?>"
           class="btn-pay-online">
          <i class="bi bi-credit-card-2-front me-2"></i>Pay Online Now
          <span class="badge bg-light text-dark ms-2" style="font-size:.65rem;">Card / Stripe</span>
        </a>
        <?php endif; ?>
        <button type="button" class="btn-bank-transfer" onclick="openBacsModal(<?= $inv['id'] ?>, '<?= h($inv['invoice_number']) ?>', '<?= h(number_format($inv['amount_due'],2)) ?>')">
          <i class="bi bi-bank me-2"></i>I've Paid by Bank Transfer
        </button>
      </div>
      <?php elseif ($isPaid): ?>
      <div class="d-flex align-items-center gap-2 mt-2" style="color:#16a34a;font-weight:700;font-size:.88rem;">
        <i class="bi bi-check-circle-fill"></i> Paid — Thank you!
      </div>
      <?php endif; ?>

      <!-- Download PDF link -->
      <div class="mt-3 pt-3" style="border-top:1px solid #f1f5f9;">
        <a href="<?= SITE_URL ?>/fees/download-invoice.php?id=<?= $inv['id'] ?>" target="_blank"
           class="text-muted small text-decoration-none">
          <i class="bi bi-file-earmark-pdf me-1"></i>Download Invoice PDF
        </a>
        <span class="text-muted small ms-3">Issued <?= date('d M Y', strtotime($inv['created_at'])) ?></span>
      </div>

    </div>
    <?php endforeach; ?>
  </div>

  <!-- ── Sidebar: Bank Details + Help ──────────────────────────────────── -->
  <div class="col-lg-4">

    <!-- Bank Transfer Details -->
    <div class="bank-card mb-4">
      <div class="bank-card-header">
        <i class="bi bi-bank me-2"></i>Bank Transfer Details
      </div>
      <div class="bank-card-body">
        <div class="bank-field">
          <div class="bank-field-label">Account Name</div>
          <div class="bank-field-value"><?= h($bankName ?: 'Contact academy') ?></div>
        </div>
        <div class="row g-3">
          <div class="col-6">
            <div class="bank-field">
              <div class="bank-field-label">Sort Code</div>
              <div class="bank-field-value"><?= h($bankSort ?: '—') ?></div>
            </div>
          </div>
          <div class="col-6">
            <div class="bank-field">
              <div class="bank-field-label">Account Number</div>
              <div class="bank-field-value"><?= h($bankAccount ?: '—') ?></div>
            </div>
          </div>
        </div>
        <div class="bank-reference-note">
          <i class="bi bi-tag me-1"></i>
          Always use your child's name and invoice number as the payment reference.<br>
          <strong>Example:</strong> <?= h($bacsPrefix) ?> <?= h($activeChild['name']) ?> INV-0001
        </div>
      </div>
    </div>

    <!-- Step-by-step payment guide -->
    <div class="stat-card">
      <h6 class="fw-700 mb-3" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.08em;color:#888;">Payment Guide</h6>
      <div class="payment-step">
        <div class="step-num">1</div>
        <div><strong>Pay Online</strong> — Click the <em>Pay Online Now</em> button for instant secure payment by card.</div>
      </div>
      <div class="payment-step">
        <div class="step-num">2</div>
        <div><strong>Or use Bank Transfer</strong> — Transfer the exact amount to the account details on the left, using your child's name as the reference.</div>
      </div>
      <div class="payment-step">
        <div class="step-num">3</div>
        <div><strong>Tell us you've paid</strong> — Click <em>"I've Paid by Bank Transfer"</em> so our team can match and verify your payment quickly.</div>
      </div>
      <div class="payment-step" style="border-bottom:none;">
        <div class="step-num">4</div>
        <div><strong>Verification</strong> — We'll confirm within 1–2 working days and update your invoice status.</div>
      </div>
    </div>

  </div>
</div>

<!-- ── Bank Transfer Modal ───────────────────────────────────────────────── -->
<div class="modal fade" id="bacsModal" tabindex="-1" aria-labelledby="bacsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:16px;overflow:hidden;">
      <div class="modal-header" style="background:var(--navy);border-bottom:none;padding:20px 24px;">
        <h5 class="modal-title fw-800 text-white" id="bacsModalLabel">
          <i class="bi bi-bank me-2" style="color:var(--gold);"></i>Record Bank Transfer
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" style="padding:24px;">
        <div class="alert alert-info d-flex gap-2 py-2 mb-4">
          <i class="bi bi-info-circle mt-1 flex-shrink-0"></i>
          <div class="small">Fill in the details below to notify us of your bank transfer. We'll verify and update your invoice within 1–2 working days.</div>
        </div>
        <div id="bacsInvoiceInfo" class="mb-4 p-3" style="background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0;">
          <!-- Populated by JS -->
        </div>
        <form id="bacsForm">
          <input type="hidden" name="action" value="claim_bacs">
          <input type="hidden" name="csrf_token" value="<?= h($csrfToken) ?>">
          <input type="hidden" name="invoice_id" id="bacsInvoiceId">
          <div class="mb-3">
            <label class="form-label fw-600 small">Payment Date <span class="text-danger">*</span></label>
            <input type="date" name="payment_date" id="bacsPayDate" class="form-control" value="<?= date('Y-m-d') ?>" required max="<?= date('Y-m-d') ?>">
          </div>
          <div class="mb-3">
            <label class="form-label fw-600 small">Bank Reference / Transaction ID <span class="text-muted fw-400">(optional but helps us match faster)</span></label>
            <input type="text" name="reference" class="form-control" placeholder="e.g. 123456789">
          </div>
          <div class="mb-3">
            <label class="form-label fw-600 small">Notes <span class="text-muted fw-400">(optional)</span></label>
            <textarea name="notes" class="form-control" rows="2" placeholder="Anything else we should know…"></textarea>
          </div>
        </form>
        <div id="bacsError" class="alert alert-danger d-none"></div>
        <div id="bacsSuccess" class="alert alert-success d-none"></div>
      </div>
      <div class="modal-footer" style="border-top:1px solid #f1f5f9;padding:16px 24px;">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success fw-700" id="bacsSubmitBtn" onclick="submitBacsClaim()">
          <i class="bi bi-check-circle me-1"></i>Confirm — I've Made the Transfer
        </button>
      </div>
    </div>
  </div>
</div>

<style>
.how-to-pay-banner {
  background: linear-gradient(135deg, var(--navy) 0%, #1e3a5f 100%);
  border-radius: 14px;
  padding: 18px 24px;
  margin-bottom: 24px;
  color: #fff;
}
.invoice-card {
  background: #fff;
  border: 1.5px solid #e2e8f0;
  border-radius: 16px;
  padding: 22px 24px;
  margin-bottom: 16px;
  transition: box-shadow .15s;
}
.invoice-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.08); }
.inv-meta-label { font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#94a3b8; margin-bottom:2px; }
.status-badge { font-size:.76rem; font-weight:700; padding:4px 10px; border-radius:20px; white-space:nowrap; }
.pending-bacs-info { background:#fffbeb; border:1px solid #fde68a; border-radius:8px; padding:10px 14px; font-size:.83rem; color:#92400e; margin-bottom:14px; }
.inv-actions { display:flex; flex-direction:column; gap:10px; margin-top:4px; }
.btn-pay-online {
  display:flex; align-items:center; justify-content:center;
  background: linear-gradient(135deg, var(--navy) 0%, #1e3a5f 100%);
  color:#fff; font-weight:800; font-size:.9rem;
  padding:14px 20px; border-radius:10px; text-decoration:none;
  border:none; cursor:pointer; text-align:center;
  transition: transform .1s, box-shadow .15s;
}
.btn-pay-online:hover { color:#F5A623; box-shadow:0 6px 20px rgba(10,22,40,.3); transform:translateY(-1px); }
.btn-bank-transfer {
  display:flex; align-items:center; justify-content:center;
  background:#fff; color:var(--navy); font-weight:700; font-size:.88rem;
  padding:12px 20px; border-radius:10px;
  border:2px solid var(--navy); cursor:pointer; text-align:center;
  transition: background .15s, color .15s;
}
.btn-bank-transfer:hover { background:var(--navy); color:#fff; }
.bank-card { background:var(--navy); border-radius:16px; overflow:hidden; }
.bank-card-header { padding:16px 20px; font-weight:800; color:var(--gold); font-size:.88rem; text-transform:uppercase; letter-spacing:.06em; border-bottom:1px solid rgba(255,255,255,.1); }
.bank-card-body { padding:20px; }
.bank-field { margin-bottom:16px; }
.bank-field-label { font-size:.65rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:rgba(255,255,255,.5); margin-bottom:3px; }
.bank-field-value { font-weight:800; color:#fff; font-size:1rem; font-family:'Courier New',monospace; letter-spacing:.04em; }
.bank-reference-note { background:rgba(245,166,35,.15); border:1px solid rgba(245,166,35,.3); border-radius:8px; padding:12px 14px; font-size:.8rem; color:rgba(255,255,255,.85); margin-top:4px; line-height:1.5; }
.payment-step { display:flex; gap:12px; align-items:flex-start; padding:10px 0; border-bottom:1px solid #f1f5f9; font-size:.85rem; color:#374151; }
.step-num { width:24px; height:24px; min-width:24px; background:var(--navy); color:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:.75rem; flex-shrink:0; margin-top:1px; }
@media (max-width:576px) {
  .invoice-card { padding:16px; }
  .btn-pay-online, .btn-bank-transfer { font-size:.84rem; padding:12px 16px; }
}
</style>

<script>
function openBacsModal(invId, invNum, amount) {
  document.getElementById('bacsInvoiceId').value = invId;
  document.getElementById('bacsInvoiceInfo').innerHTML =
    '<div class="d-flex justify-content-between"><div><div style="font-size:.7rem;font-weight:700;text-transform:uppercase;color:#94a3b8;">Invoice</div><div class="fw-800">' + invNum + '</div></div>' +
    '<div class="text-end"><div style="font-size:.7rem;font-weight:700;text-transform:uppercase;color:#94a3b8;">Amount</div><div class="fw-900 text-danger" style="font-size:1.2rem;">£' + amount + '</div></div></div>';
  document.getElementById('bacsError').classList.add('d-none');
  document.getElementById('bacsSuccess').classList.add('d-none');
  document.getElementById('bacsForm').querySelectorAll('input[type=text],textarea').forEach(el => el.value = '');
  document.getElementById('bacsPayDate').value = new Date().toISOString().slice(0,10);
  new bootstrap.Modal(document.getElementById('bacsModal')).show();
}

function submitBacsClaim() {
  const btn = document.getElementById('bacsSubmitBtn');
  const errEl = document.getElementById('bacsError');
  const okEl  = document.getElementById('bacsSuccess');
  errEl.classList.add('d-none');
  okEl.classList.add('d-none');

  const formData = new FormData(document.getElementById('bacsForm'));
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Submitting…';

  fetch(window.location.pathname, { method:'POST', body: formData })
    .then(r => r.json())
    .then(data => {
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Confirm — I\'ve Made the Transfer';
      if (data.ok) {
        okEl.textContent = data.msg;
        okEl.classList.remove('d-none');
        setTimeout(() => { bootstrap.Modal.getInstance(document.getElementById('bacsModal')).hide(); location.reload(); }, 2200);
      } else {
        errEl.textContent = data.msg;
        errEl.classList.remove('d-none');
      }
    })
    .catch(() => {
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Confirm — I\'ve Made the Transfer';
      errEl.textContent = 'Something went wrong. Please try again.';
      errEl.classList.remove('d-none');
    });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
