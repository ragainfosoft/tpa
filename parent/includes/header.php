<?php
// =====================================================
// TPA — Parent Portal Layout Header
// =====================================================

require_once __DIR__ . '/../../admin/includes/auth.php';
require_once __DIR__ . '/../../admin/includes/functions.php';
startSecureSession();
requireRole(['parent','admin']);

$currentUser = currentUser();
$userId      = currentUserId();
$db          = getDB();

// Load children linked to this parent user
$children = $db->prepare("SELECT s.id, CONCAT(s.first_name,' ',s.last_name) as name, s.student_ref, s.year_group FROM students s JOIN student_parents sp ON sp.student_id=s.id JOIN users u ON u.email=sp.email WHERE u.id=? ORDER BY s.first_name");
try { $children->execute([$userId]); $children = $children->fetchAll(); } catch(Exception $e) { $children = []; }

// Active child (first child by default, or switch via ?child_id=)
$activeChild = null;
if (!empty($children)) {
    $cid = (int)($_GET['child_id'] ?? ($_SESSION['active_child_id'] ?? $children[0]['id']));
    foreach ($children as $c) { if ($c['id'] === $cid) { $activeChild = $c; break; } }
    if (!$activeChild) $activeChild = $children[0];
    $_SESSION['active_child_id'] = $activeChild['id'];
}

$page_title   = $page_title   ?? 'Parent Portal';
$page_section = $page_section ?? '';

// Overdue fees for active child
$overdueCount = 0;
if ($activeChild) {
    $oc = $db->prepare('SELECT COUNT(*) FROM invoices WHERE student_id=? AND status="overdue"');
    $oc->execute([$activeChild['id']]);
    $overdueCount = (int)$oc->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= h($page_title) ?> | TPA Parent</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <style>
    :root { --navy:#0A1628; --gold:#F5A623; --rose:#be123c; --sidebar-w:260px; }
    *,*::before,*::after { box-sizing:border-box; }
    body { font-family:'Inter',sans-serif; background:#fff1f2; margin:0; min-height:100vh; display:flex; }
    .portal-sidebar { width:var(--sidebar-w); background:linear-gradient(180deg,#9f1239 0%,#6f0e28 100%); position:fixed; top:0; left:0; height:100vh; display:flex; flex-direction:column; z-index:100; overflow-y:auto; }
    .portal-sidebar .brand { padding:22px 20px 16px; border-bottom:1px solid rgba(255,255,255,.12); }
    .portal-sidebar .brand h5 { color:#fff; font-weight:900; margin:0; font-size:.95rem; }
    .portal-sidebar .brand small { color:rgba(255,255,255,.5); font-size:.7rem; }
    .portal-sidebar nav a { display:flex; align-items:center; gap:10px; padding:10px 18px; color:rgba(255,255,255,.78); text-decoration:none; font-size:.84rem; font-weight:600; transition:.15s; }
    .portal-sidebar nav a:hover, .portal-sidebar nav a.active { background:rgba(255,255,255,.15); color:#fff; }
    .portal-sidebar nav a i { width:18px; text-align:center; font-size:1rem; }
    .child-switcher { margin:10px 12px; }
    .child-switcher select { width:100%; background:rgba(255,255,255,.1); color:#fff; border:1px solid rgba(255,255,255,.2); border-radius:8px; padding:6px 10px; font-size:.8rem; font-weight:600; }
    .child-switcher select option { color:#000; }
    .sidebar-footer { margin-top:auto; padding:16px 18px; border-top:1px solid rgba(255,255,255,.12); }
    .portal-main { margin-left:var(--sidebar-w); flex:1; display:flex; flex-direction:column; min-height:100vh; }
    .portal-topbar { background:#fff; border-bottom:1px solid #e2e8f0; padding:12px 28px; display:flex; align-items:center; justify-content:space-between; }
    .portal-topbar h6 { margin:0; font-weight:700; color:var(--navy); }
    .portal-content { padding:28px; flex:1; }
    .stat-card { background:#fff; border-radius:14px; padding:22px; box-shadow:0 1px 4px rgba(0,0,0,.07); }
    .stat-value { font-size:2rem; font-weight:900; color:var(--navy); line-height:1; }
    .stat-label { font-size:.75rem; font-weight:600; text-transform:uppercase; letter-spacing:.06em; color:#64748b; margin-top:4px; }
    .page-header { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:24px; }
    .page-header h1 { font-size:1.4rem; font-weight:900; color:var(--navy); margin:0; }
    .tpa-table { background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 1px 4px rgba(0,0,0,.07); }
    .tpa-table table { margin:0; }
    .tpa-table thead th { background:#f8fafc; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#64748b; border-bottom:1px solid #e2e8f0; padding:10px 14px; }
    .tpa-table tbody td { padding:12px 14px; border-bottom:1px solid #f1f5f9; font-size:.875rem; vertical-align:middle; }
    <?php if (isset($extra_css)) echo $extra_css; ?>
  </style>
</head>
<body>
<aside class="portal-sidebar">
  <div class="brand">
    <h5>👪 TPA Parent</h5>
    <small>Parent Portal</small>
  </div>
  <?php if (count($children) > 1): ?>
  <div class="child-switcher">
    <form method="GET">
      <select name="child_id" onchange="this.form.submit()">
        <?php foreach ($children as $c): ?>
          <option value="<?= $c['id'] ?>" <?= $activeChild['id']==$c['id']?'selected':'' ?>><?= h($c['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </form>
  </div>
  <?php endif; ?>
  <nav class="py-2">
    <?php
    $pmenu = [
      ['i'=>'speedometer2',  'l'=>'Dashboard',    's'=>'dashboard',  'h'=>'index.php'],
      ['i'=>'calendar-check','l'=>'Attendance',   's'=>'attendance', 'h'=>'attendance.php'],
      ['i'=>'trophy',        'l'=>'Results',       's'=>'results',    'h'=>'results.php'],
      ['i'=>'receipt',       'l'=>'Fees',          's'=>'fees',       'h'=>'fees.php',    'badge'=>$overdueCount],
      ['i'=>'pencil-square', 'l'=>'Homework',      's'=>'homework',   'h'=>'homework.php'],
      ['i'=>'chat-dots',     'l'=>'Messages',      's'=>'messages',   'h'=>'messages.php'],
      ['i'=>'person',        'l'=>'Profile',       's'=>'profile',    'h'=>'profile.php'],
    ];
    foreach ($pmenu as $m):
      $a = $page_section === $m['s'] ? ' active' : '';
      $b = $m['badge'] ?? 0;
    ?>
    <a href="<?= $m['h'] ?>" class="<?= $a ?>">
      <i class="bi bi-<?= $m['i'] ?>"></i><?= $m['l'] ?>
      <?php if ($b): ?><span style="margin-left:auto;background:#dc2626;color:#fff;font-size:.62rem;font-weight:800;padding:1px 6px;border-radius:10px;"><?= $b ?></span><?php endif; ?>
    </a>
    <?php endforeach; ?>
  </nav>
  <div class="sidebar-footer">
    <div style="color:#fff;font-weight:700;font-size:.83rem;"><?= h($currentUser['name'] ?? 'Parent') ?></div>
    <a href="<?= SITE_URL ?>/logout.php" class="btn btn-sm mt-2 w-100" style="background:rgba(255,255,255,.15);color:#fff;font-size:.75rem;">Sign Out</a>
  </div>
</aside>
<main class="portal-main">
  <div class="portal-topbar">
    <h6><?= h($page_title) ?></h6>
    <?php if ($activeChild): ?>
    <div class="d-flex align-items-center gap-2">
      <span class="badge" style="background:#fce7f3;color:#9d174d;"><?= h($activeChild['name']) ?></span>
      <span class="badge bg-light text-dark border"><?= h($activeChild['year_group'] ?? '') ?></span>
      <span class="badge" style="background:#fef2f2;color:#b91c1c;"><?= h($activeChild['student_ref']) ?></span>
    </div>
    <?php endif; ?>
    <span class="small text-muted"><?= date('D, d M Y') ?></span>
  </div>
  <div class="portal-content">
