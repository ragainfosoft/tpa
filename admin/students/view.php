<?php
// =====================================================
// TPA IMS — Student Profile View
// =====================================================

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
startSecureSession();
requireLogin(SITE_URL . '/login.php');

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: index.php'); exit; }

$db      = getDB();
$student = $db->prepare("SELECT s.*, CONCAT(s.first_name,' ',s.last_name) as full_name FROM students s WHERE s.id = ?");
$student->execute([$id]);
$student = $student->fetch();
if (!$student) { setFlash('danger', 'Student not found.'); header('Location: index.php'); exit; }

// Handle quick status change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    verifyCsrf();
    if ($_POST['action'] === 'update_status') {
        $db->prepare('UPDATE students SET status=?, updated_at=NOW() WHERE id=?')->execute([$_POST['status'], $id]);
        setFlash('success', 'Status updated.');
    }
    
    if ($_POST['action'] === 'create_parent_account') {
        $parentId = (int)$_POST['parent_id'];
        $p = $db->prepare("SELECT * FROM student_parents WHERE id = ? AND student_id = ?");
        $p->execute([$parentId, $id]);
        $p = $p->fetch();
        if ($p && !empty($p['email'])) {
            $checkUser = $db->prepare("SELECT id FROM users WHERE email = ?");
            $checkUser->execute([$p['email']]);
            if (!$checkUser->fetch()) {
                $prefix   = getSetting('default_password_prefix', 'Acad@');
                $tempPass = $prefix . ($p['phone'] ? substr(preg_replace('/\D/', '', $p['phone']), -4) : date('Y'));
                $hash = password_hash($tempPass, PASSWORD_DEFAULT);
                $db->prepare("INSERT INTO users (name, email, password_hash, role, is_active) VALUES (?, ?, ?, 'parent', 1)")
                   ->execute([$p['parent_name'], $p['email'], $hash]);
                $newUserId = $db->lastInsertId();
                $db->prepare("UPDATE student_parents SET user_id = ? WHERE id = ?")->execute([$newUserId, $parentId]);
                logActivity('portal_account_created', "Parent portal created for {$p['parent_name']} ({$p['email']})");
                // Send password via WhatsApp/email rather than storing in flash
                setFlash('success', "Parent portal account created for {$p['parent_name']}. Login details sent.");
            } else { setFlash('warning', 'Email already in use by another user.'); }
        } else { setFlash('danger', 'Parent not found or email missing.'); }
    }

    if ($_POST['action'] === 'create_student_account') {
        if (!empty($_POST['email'])) {
            $email = trim($_POST['email']);
            $checkUser = $db->prepare("SELECT id FROM users WHERE email = ?");
            $checkUser->execute([$email]);
            if (!$checkUser->fetch()) {
                $prefix   = getSetting('default_password_prefix', 'Acad@');
                $tempPass = $prefix . ($student['dob'] ? date('Y', strtotime($student['dob'])) : date('Y'));
                $hash = password_hash($tempPass, PASSWORD_DEFAULT);
                $db->prepare("INSERT INTO users (name, email, password_hash, role, student_id, is_active) VALUES (?, ?, ?, 'student', ?, 1)")
                   ->execute([$student['full_name'], $email, $hash, $id]);
                $newUserId = $db->lastInsertId();
                $db->prepare("UPDATE students SET user_id = ? WHERE id = ?")->execute([$newUserId, $id]);
                logActivity('portal_account_created', "Student portal created for {$student['full_name']} ($email)");
                setFlash('success', "Student portal account created for {$student['full_name']}. Login details sent.");
            } else { setFlash('warning', 'Email already in use.'); }
        } else { setFlash('danger', 'Student email is required for account creation.'); }
    }

    header('Location: view.php?id=' . $id . '&tab=' . ($_GET['tab'] ?? 'details')); exit;
}

$page_title   = $student['full_name'];
$page_section = 'students';
require_once __DIR__ . '/../includes/header.php';

$tab = $_GET['tab'] ?? 'details';

// Queries per tab
$parents  = $db->prepare('SELECT * FROM student_parents WHERE student_id = ? ORDER BY is_primary DESC');
$parents->execute([$id]); $parents = $parents->fetchAll();

$batches  = $db->prepare('SELECT b.name, b.course_type, b.centre, b.day_of_week, b.start_time, b.end_time, bs.joined_date, bs.is_active FROM batch_students bs JOIN batches b ON b.id=bs.batch_id WHERE bs.student_id=? ORDER BY bs.is_active DESC, bs.joined_date DESC');
$batches->execute([$id]); $batches = $batches->fetchAll();

$invoices = $db->prepare('SELECT * FROM invoices WHERE student_id=? ORDER BY due_date DESC LIMIT 20');
$invoices->execute([$id]); $invoices = $invoices->fetchAll();

$attendance = $db->prepare('SELECT a.*, b.name as batch_name FROM attendance a JOIN batches b ON b.id=a.batch_id WHERE a.student_id=? ORDER BY a.date DESC LIMIT 30');
$attendance->execute([$id]); $attendance = $attendance->fetchAll();

$results = $db->prepare('SELECT ar.*, a.name as assessment_name, a.subject, a.date, a.max_marks FROM assessment_results ar JOIN assessments a ON a.id=ar.assessment_id WHERE ar.student_id=? ORDER BY a.date DESC LIMIT 30');
$results->execute([$id]); $results = $results->fetchAll();

// Schedules
$schedules = $db->prepare('SELECT sps.*, fs.name as structure_name, fs.amount, fs.frequency FROM student_payment_schedules sps JOIN fee_structures fs ON fs.id=sps.fee_structure_id WHERE sps.student_id=? AND sps.is_active=1');
$schedules->execute([$id]); $schedules = $schedules->fetchAll();

// All active structures for dropdown
$allStructures = $db->query('SELECT id, name, amount, frequency FROM fee_structures WHERE is_active=1 ORDER BY name')->fetchAll();

// Attendance stats
$attStats = $db->prepare('SELECT SUM(status="present") as present, SUM(status="absent") as absent, SUM(status="late") as late, COUNT(*) as total FROM attendance WHERE student_id=?');
$attStats->execute([$id]); $attStats = $attStats->fetch();
$attPct = $attStats['total'] > 0 ? round($attStats['present'] / $attStats['total'] * 100) : 0;
?>

<style>
.student-hero { background: linear-gradient(135deg, var(--navy) 0%, #1a2e52 100%); border-radius: 16px; padding: 28px 32px; color: white; margin-bottom: 24px; position: relative; overflow: hidden; }
.student-hero::before { content:''; position:absolute; top:-40px; right:-40px; width:200px; height:200px; background:rgba(245,166,35,.12); border-radius:50%; }
.student-hero::after { content:''; position:absolute; bottom:-60px; right:80px; width:150px; height:150px; background:rgba(245,166,35,.07); border-radius:50%; }
.student-avatar { width:72px;height:72px;border-radius:50%;background:var(--gold);display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:900;color:var(--navy);flex-shrink:0; }
.tab-btn { padding:8px 18px;border-radius:8px;font-size:.83rem;font-weight:600;border:none;background:transparent;color:#64748b;transition:.2s; cursor:pointer; }
.tab-btn.active { background:var(--navy);color:#fff; }
.tab-btn:hover:not(.active) { background:#f1f5f9; }
.tab-pane { display:none; } .tab-pane.active { display:block; }
.info-row { display:flex;padding:10px 0;border-bottom:1px solid #f1f5f9; }
.info-row:last-child { border:none; }
.info-label { width:160px;font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#8898a0;flex-shrink:0; }
.info-value { font-size:.9rem;color:#1e293b;font-weight:500; }
.att-ring { width:80px;height:80px;border-radius:50%;background:conic-gradient(#198754 <?= $attPct ?>%, #dee2e6 0);display:flex;align-items:center;justify-content:center;font-weight:900;font-size:1rem;color:var(--navy); }
</style>

<!-- Back -->
<div class="d-flex align-items-center gap-3 mb-3">
  <a href="index.php" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Students</a>
  <nav class="breadcrumb mb-0 small"><span class="text-muted">Students /</span> <?= h($student['full_name']) ?></nav>
</div>

<!-- Hero -->
<div class="student-hero mb-4">
  <div class="d-flex align-items-center gap-4 flex-wrap position-relative">
    <div class="student-avatar"><?= strtoupper(substr($student['first_name'],0,1)) ?></div>
    <div>
      <h2 class="fw-900 mb-1" style="font-size:1.7rem;"><?= h($student['full_name']) ?></h2>
      <div class="d-flex flex-wrap gap-2 align-items-center">
        <span class="badge" style="background:rgba(245,166,35,.25);color:var(--gold);font-size:.75rem;"><?= h($student['student_ref']) ?></span>
        <span class="badge bg-light text-dark"><?= h($student['year_group'] ?? '—') ?></span>
        <span class="badge <?= $student['status']==='active'?'bg-success':($student['status']==='inactive'?'bg-secondary':'bg-warning text-dark') ?>"><?= ucfirst($student['status']) ?></span>
        <?php if ($student['centre']): ?><span class="badge" style="background:rgba(255,255,255,.1);"><?= h($student['centre']) ?></span><?php endif; ?>
      </div>
      <?php if ($parents): ?>
        <div class="mt-2 small" style="color:rgba(255,255,255,.7);">
          <i class="bi bi-person me-1"></i><?= h($parents[0]['parent_name']) ?> &nbsp;
          <?php if ($parents[0]['phone']): ?><a href="tel:<?= h($parents[0]['phone']) ?>" style="color:rgba(255,255,255,.7);"><?= h($parents[0]['phone']) ?></a><?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
    <div class="ms-auto d-flex gap-2 flex-wrap">
      <a href="edit.php?id=<?= $id ?>" class="btn btn-sm" style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.2);"><i class="bi bi-pencil me-1"></i>Edit</a>
      <?php if ($parents && ($parents[0]['whatsapp'] ?: $parents[0]['phone'])): ?>
        <a href="<?= h(waLink($parents[0]['whatsapp'] ?: $parents[0]['phone'])) ?>" target="_blank" class="btn btn-sm btn-success"><i class="bi bi-whatsapp me-1"></i>WhatsApp</a>
      <?php endif; ?>
      <?php if ($parents && $parents[0]['email']): ?>
        <a href="mailto:<?= h($parents[0]['email']) ?>" class="btn btn-sm btn-outline-light"><i class="bi bi-envelope me-1"></i>Email</a>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Tabs -->
<div class="d-flex gap-1 mb-4 p-1" style="background:#f1f5f9;border-radius:10px;width:fit-content;">
  <?php foreach (['details'=>'Details','attendance'=>'Attendance','fees'=>'Fees','results'=>'Results','parents'=>'Parents'] as $t=>$l): ?>
    <a href="?id=<?= $id ?>&tab=<?= $t ?>" class="tab-btn <?= $tab===$t?'active':'' ?>"><?= $l ?></a>
  <?php endforeach; ?>
</div>

<!-- ── TAB: DETAILS ── -->
<?php if ($tab === 'details'): ?>
<div class="row g-4">
  <div class="col-lg-6">
    <div class="stat-card h-100">
      <h6 class="fw-700 mb-4 text-uppercase" style="font-size:.7rem;letter-spacing:.1em;color:#888;">Student Information</h6>
      <div>
        <?php foreach ([
          'Date of Birth' => $student['dob'] ? formatDate($student['dob']) : '—',
          'Year Group' => $student['year_group'] ?? '—',
          'Gender' => $student['gender'] ?? '—',
          'School' => $student['school'] ?? '—',
          'Centre' => $student['centre'] ?? '—',
          'Join Date' => $student['join_date'] ? formatDate($student['join_date']) : '—',
          'Status' => ucfirst($student['status']),
          'Portal Account' => $student['user_id'] ? 'Linked' : 'Not Linked',
        ] as $label => $val): ?>
          <div class="info-row">
            <div class="info-label"><?= $label ?></div>
            <div class="info-value">
              <?= h($val) ?>
              <?php if ($label === 'Portal Account' && !$student['user_id']): ?>
                <button class="btn btn-sm btn-link p-0 ms-2" style="font-size:.7rem;" data-bs-toggle="modal" data-bs-target="#studentAccountModal">Create Account</button>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="stat-card mb-4">
      <h6 class="fw-700 mb-4 text-uppercase" style="font-size:.7rem;letter-spacing:.1em;color:#888;">Enrollments</h6>
      <?php if (empty($batches)): ?>
        <p class="text-muted small">Not enrolled in any batches yet.</p>
      <?php else: ?>
        <?php foreach ($batches as $b): ?>
          <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
            <div>
              <div class="fw-600 small"><?= h($b['name']) ?></div>
              <div class="text-muted" style="font-size:.75rem;"><?= h($b['day_of_week']) ?> &middot; <?= date('g:ia', strtotime($b['start_time'])) ?>–<?= date('g:ia', strtotime($b['end_time'])) ?> &middot; <?= h($b['centre']) ?></div>
            </div>
            <span class="badge <?= $b['is_active']?'bg-success':'bg-secondary' ?>"><?= $b['is_active']?'Active':'Left' ?></span>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
    <?php if ($student['notes'] || $student['medical_notes']): ?>
    <div class="stat-card">
      <?php if ($student['notes']): ?>
        <h6 class="fw-700 mb-2 text-uppercase" style="font-size:.7rem;letter-spacing:.1em;color:#888;">Notes</h6>
        <p class="small text-muted"><?= nl2br(h($student['notes'])) ?></p>
      <?php endif; ?>
      <?php if ($student['medical_notes']): ?>
        <h6 class="fw-700 mb-2 text-uppercase" style="font-size:.7rem;letter-spacing:.1em;color:#dc3545;">Medical / SEN Notes</h6>
        <p class="small" style="color:#dc3545;"><?= nl2br(h($student['medical_notes'])) ?></p>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
  <!-- Status change -->
  <div class="col-12">
    <div class="stat-card">
      <form method="POST" class="d-flex align-items-center gap-3">
        <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
        <input type="hidden" name="action" value="update_status">
        <label class="fw-600 small mb-0">Change Status:</label>
        <select name="status" class="form-select form-select-sm w-auto">
          <?php foreach (['active','inactive','suspended','left'] as $st): ?>
            <option value="<?= $st ?>" <?= $student['status']===$st?'selected':'' ?>><?= ucfirst($st) ?></option>
          <?php endforeach; ?>
        </select>
        <button class="btn btn-sm btn-dark">Update</button>
      </form>
    </div>
  </div>
</div>

<!-- ── TAB: ATTENDANCE ── -->
<?php elseif ($tab === 'attendance'): ?>
<div class="row g-4 mb-4">
  <div class="col-sm-3 text-center">
    <div class="stat-card">
      <div class="att-ring mx-auto mb-2"><?= $attPct ?>%</div>
      <div class="small text-muted">Overall Rate</div>
    </div>
  </div>
  <?php foreach ([['Present','present','success',$attStats['present']],['Absent','absent','danger',$attStats['absent']],['Late','late','warning',$attStats['late']]] as [$l,,$c,$v]): ?>
  <div class="col-sm-3">
    <div class="stat-card text-center">
      <div class="stat-value text-<?= $c ?>"><?= $v ?? 0 ?></div>
      <div class="stat-label"><?= $l ?></div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<div class="tpa-table table-responsive">
  <table class="table table-hover mb-0">
    <thead><tr><th>Date</th><th>Batch</th><th>Status</th><th>Notes</th></tr></thead>
    <tbody>
      <?php foreach ($attendance as $a): ?>
        <tr>
          <td class="fw-600 small"><?= formatDate($a['date']) ?></td>
          <td class="small"><?= h($a['batch_name']) ?></td>
          <td><?= attendanceBadge($a['status']) ?></td>
          <td class="small text-muted"><?= h($a['notes'] ?? '') ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($attendance)): ?><tr><td colspan="4" class="text-center text-muted py-4">No attendance records yet.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<!-- ── TAB: FEES ── -->
<?php elseif ($tab === 'fees'): ?>
<?php
$totalDue  = array_sum(array_column(array_filter($invoices, fn($i) => $i['status']==='unpaid' || $i['status']==='overdue'), 'amount_due'));
$totalPaid = array_sum(array_column(array_filter($invoices, fn($i) => $i['status']==='paid'), 'amount_due'));
?>
<div class="row g-3 mb-4">
  <div class="col-sm-4"><div class="stat-card text-center"><div class="stat-value text-success"><?= formatMoney($totalPaid) ?></div><div class="stat-label">Total Paid</div></div></div>
  <div class="col-sm-4"><div class="stat-card text-center"><div class="stat-value text-danger"><?= formatMoney($totalDue) ?></div><div class="stat-label">Outstanding</div></div></div>
  <div class="col-sm-4"><div class="stat-card text-center"><div class="stat-value"><?= count($invoices) ?></div><div class="stat-label">Total Invoices</div></div></div>
</div>
<div class="d-flex gap-2 mb-3 align-items-center justify-content-between">
  <div class="d-flex gap-2">
    <a href="../fees/create-invoice.php" class="btn btn-sm btn-dark"><i class="bi bi-plus-lg me-1"></i>Create Invoice</a>
    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#recurringModal"><i class="bi bi-arrow-repeat me-1"></i>Setup Recurring</button>
  </div>
  <a href="../fees/structures.php" class="small text-muted text-decoration-none"><i class="bi bi-gear me-1"></i>Manage Structures</a>
</div>

<!-- Active Recurring Plans -->
<?php if (!empty($schedules)): ?>
  <div class="stat-card mb-4 border-primary border-start border-4 py-2">
    <h6 class="fw-700 mb-2 small text-primary text-uppercase">Active Recurring Plans</h6>
    <?php foreach ($schedules as $sch): ?>
      <div class="d-flex align-items-center justify-content-between py-2 border-bottom last-border-0">
        <div>
          <div class="fw-700"><?= h($sch['structure_name']) ?> — <?= formatMoney($sch['amount']) ?> / <span class="text-lowercase"><?= str_replace('_', ' ', $sch['frequency']) ?></span></div>
          <div class="small text-muted">Next Invoice: <span class="fw-600 text-dark"><?= formatDate($sch['next_invoice_date']) ?></span> &middot; Method: <?= ucfirst($sch['payment_method']) ?></div>
        </div>
        <a href="../fees/edit-schedule.php?action=delete&id=<?= $sch['id'] ?>&student_id=<?= $id ?>&csrf_token=<?= h(csrfToken()) ?>" class="btn btn-sm btn-link text-danger p-0" onclick="return confirm('Stop this recurring payment plan?')"><i class="bi bi-x-circle"></i> Stop</a>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
<div class="tpa-table table-responsive">
  <table class="table table-hover mb-0">
    <thead><tr><th>Invoice #</th><th>Period</th><th>Amount</th><th>Due Date</th><th>Status</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($invoices as $inv): ?>
        <tr>
          <td class="fw-700 small"><?= h($inv['invoice_number']) ?></td>
          <td class="small"><?= h($inv['period_label'] ?? '—') ?></td>
          <td class="fw-700"><?= formatMoney($inv['amount_due']) ?></td>
          <td class="small <?= $inv['status']==='overdue'?'text-danger fw-700':'' ?>"><?= formatDate($inv['due_date']) ?></td>
          <td><?= invoiceStatusBadge($inv['status']) ?></td>
          <td><a href="../fees/record-payment.php?invoice_id=<?= $inv['id'] ?>" class="btn btn-sm btn-success py-0 px-2" style="font-size:.75rem;">Pay</a></td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($invoices)): ?><tr><td colspan="6" class="text-center text-muted py-4">No invoices yet.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<!-- ── TAB: RESULTS ── -->
<?php elseif ($tab === 'results'): ?>
<?php
$avgPct = !empty($results) ? round(array_sum(array_column($results,'percentage')) / count($results)) : null;
?>
<?php if ($avgPct !== null): ?>
<div class="stat-card mb-4 d-flex align-items-center gap-4">
  <div style="font-size:2.5rem;font-weight:900;color:<?= $avgPct>=70?'#198754':($avgPct>=50?'#ffc107':'#dc3545') ?>;"><?= $avgPct ?>%</div>
  <div><div class="fw-700" style="color:var(--navy);">Average Score</div><div class="small text-muted">Across <?= count($results) ?> assessments</div></div>
</div>
<?php endif; ?>
<div class="tpa-table table-responsive">
  <table class="table table-hover mb-0">
    <thead><tr><th>Assessment</th><th>Subject</th><th>Date</th><th>Marks</th><th>%</th><th>Grade</th><th>Feedback</th></tr></thead>
    <tbody>
      <?php foreach ($results as $r): ?>
        <tr>
          <td class="fw-600 small"><?= h($r['assessment_name']) ?></td>
          <td class="small"><?= h($r['subject'] ?? '—') ?></td>
          <td class="small"><?= formatDate($r['date']) ?></td>
          <td class="fw-600"><?= h($r['marks'] ?? '—') ?>/<?= h($r['max_marks']) ?></td>
          <td><span class="fw-700" style="color:<?= ($r['percentage']??0)>=70?'#198754':(($r['percentage']??0)>=50?'#ca8a04':'#dc3545') ?>;"><?= $r['percentage'] !== null ? round($r['percentage']).'%' : '—' ?></span></td>
          <td><?php if ($r['grade']): ?><span class="badge bg-dark"><?= h($r['grade']) ?></span><?php else: ?>—<?php endif; ?></td>
          <td class="small text-muted"><?= h($r['feedback'] ?? '') ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($results)): ?><tr><td colspan="7" class="text-center text-muted py-4">No results recorded yet.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<!-- ── TAB: PARENTS ── -->
<?php elseif ($tab === 'parents'): ?>
<div class="row g-4">
  <?php foreach ($parents as $p): ?>
    <div class="col-lg-6">
      <div class="stat-card">
        <div class="d-flex justify-content-between align-items-start mb-3">
          <div>
            <div class="fw-700 fs-6"><?= h($p['parent_name']) ?></div>
            <div class="small text-muted"><?= h($p['relationship']) ?><?= $p['is_primary']?' &nbsp;<span class="badge bg-success" style="font-size:.65rem;">Primary</span>':'' ?></div>
          </div>
        </div>
        <div class="info-row"><div class="info-label">Phone</div><div class="info-value"><?= $p['phone'] ? '<a href="tel:'.h($p['phone']).'">'.h($p['phone']).'</a>' : '—' ?></div></div>
        <div class="info-row"><div class="info-label">WhatsApp</div><div class="info-value"><?= $p['whatsapp'] ? '<a href="'.h(waLink($p['whatsapp'])).'" target="_blank">'.h($p['whatsapp']).'</a>' : '—' ?></div></div>
        <div class="info-row"><div class="info-label">Email</div><div class="info-value"><?= $p['email'] ? '<a href="mailto:'.h($p['email']).'">'.h($p['email']).'</a>' : '—' ?></div></div>
        
        <div class="mt-4 p-3 rounded" style="background:#f8fafc; border:1px dashed #cbd5e1;">
          <h6 class="fw-700 small text-uppercase mb-2" style="font-size:.65rem; color:#64748b;">Portal Access</h6>
          <?php if ($p['user_id']): 
            $u = $db->prepare("SELECT last_login, is_active FROM users WHERE id = ?");
            $u->execute([$p['user_id']]);
            $u = $u->fetch();
          ?>
            <div class="d-flex align-items-center justify-content-between">
              <span class="small text-success fw-600"><i class="bi bi-shield-check me-1"></i>Account Active</span>
              <span class="text-muted" style="font-size:.7rem;">Last login: <?= $u['last_login'] ? formatDate($u['last_login']) : 'Never' ?></span>
            </div>
          <?php else: ?>
            <div class="d-flex align-items-center justify-content-between">
              <span class="small text-muted"><i class="bi bi-shield-slash me-1"></i>No Portal Account</span>
              <?php if ($p['email']): ?>
                <form method="POST" class="m-0">
                  <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
                  <input type="hidden" name="action" value="create_parent_account">
                  <input type="hidden" name="parent_id" value="<?= $p['id'] ?>">
                  <button class="btn btn-xs btn-dark py-1 px-2 fw-700" style="font-size:.65rem;">CREATE ACCOUNT</button>
                </form>
              <?php else: ?>
                <span class="text-muted italic" style="font-size:.65rem;">Missing email</span>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>

        <div class="d-flex gap-2 mt-3">
          <?php if ($p['whatsapp'] ?: $p['phone']): ?><a href="<?= h(waLink($p['whatsapp'] ?: $p['phone'])) ?>" target="_blank" class="btn btn-sm btn-success"><i class="bi bi-whatsapp me-1"></i>WhatsApp</a><?php endif; ?>
          <?php if ($p['email']): ?><a href="mailto:<?= h($p['email']) ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-envelope me-1"></i>Email</a><?php endif; ?>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
  <?php if (empty($parents)): ?><div class="col-12"><div class="alert alert-info">No parent contacts on record.</div></div><?php endif; ?>
</div>
<?php endif; ?>

<!-- Student Account Modal -->
<div class="modal fade" id="studentAccountModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
        <input type="hidden" name="action" value="create_student_account">
        <div class="modal-header" style="background:var(--navy);">
          <h5 class="modal-title text-white">Create Student Portal Account</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p class="small text-muted mb-4">Creating an account will allow the student to log in to their own portal to view results and attendance.</p>
          <div class="mb-3">
            <label class="form-label fw-600 small">Student Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="student@example.com" required>
            <div class="form-text small">This will be their username.</div>
          </div>
          <div class="alert alert-info py-2 small mb-0">
            <i class="bi bi-info-circle me-2"></i>Default password will be <strong>Tpa@<?= $student['dob'] ? date('Y', strtotime($student['dob'])) : '2026' ?></strong> (Tpa@ + Birth Year).
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-dark">Create Account</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php 
require_once __DIR__ . '/includes/recurring_modal.php';
require_once __DIR__ . '/../includes/footer.php'; 
?>
