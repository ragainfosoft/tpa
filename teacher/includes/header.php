<?php
// =====================================================
// TPA — Teacher Portal Layout Header
// =====================================================

require_once __DIR__ . '/../../admin/includes/auth.php';
require_once __DIR__ . '/../../admin/includes/functions.php';
startSecureSession();
requireRole(['admin','branch_manager','teacher']);

$currentUser = currentUser();
$role        = currentRole();
$db          = getDB();

$page_title   = $page_title   ?? 'Teacher Portal';
$page_section = $page_section ?? '';

// Live counts for teacher
$myBatches = $db->prepare('SELECT COUNT(*) FROM batches b JOIN teachers t ON t.id=b.teacher_id JOIN users u ON u.id=t.user_id WHERE u.id=? AND b.is_active=1');
$myBatches->execute([$currentUser['id']]);
$myBatchCount = (int)$myBatches->fetchColumn();

$pendingHW = $db->prepare('SELECT COUNT(*) FROM homework h JOIN batches b ON b.id=h.batch_id JOIN teachers t ON t.id=b.teacher_id JOIN users u ON u.id=t.user_id WHERE u.id=? AND h.due_date >= CURDATE()');
$pendingHW->execute([$currentUser['id']]);
$pendingHWCount = (int)$pendingHW->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= h($page_title) ?> | TPA Teacher</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <style>
    :root { --navy:#0A1628; --gold:#F5A623; --teal:#0d9488; --sidebar-w:240px; }
    *,*::before,*::after { box-sizing:border-box; }
    body { font-family:'Inter',sans-serif; background:#f1f5f9; margin:0; min-height:100vh; display:flex; }

    /* Sidebar */
    .portal-sidebar { width:var(--sidebar-w); background:linear-gradient(180deg,var(--teal) 0%,#0a7a72 100%); position:fixed; top:0; left:0; height:100vh; display:flex; flex-direction:column; z-index:100; overflow-y:auto; }
    .portal-sidebar .brand { padding:22px 20px 16px; border-bottom:1px solid rgba(255,255,255,.12); }
    .portal-sidebar .brand h5 { color:#fff; font-weight:900; margin:0; font-size:.95rem; }
    .portal-sidebar .brand small { color:rgba(255,255,255,.55); font-size:.7rem; text-transform:uppercase; letter-spacing:.08em; }
    .portal-sidebar nav a { display:flex; align-items:center; gap:10px; padding:10px 18px; color:rgba(255,255,255,.78); text-decoration:none; font-size:.84rem; font-weight:600; transition:.15s; border-radius:0; }
    .portal-sidebar nav a:hover, .portal-sidebar nav a.active { background:rgba(255,255,255,.15); color:#fff; }
    .portal-sidebar nav a i { width:18px; text-align:center; font-size:1rem; }
    .sidebar-footer { margin-top:auto; padding:16px 18px; border-top:1px solid rgba(255,255,255,.12); }
    .sidebar-footer .name { color:#fff; font-weight:700; font-size:.83rem; }
    .sidebar-footer .role-tag { font-size:.67rem; text-transform:uppercase; letter-spacing:.08em; color:rgba(255,255,255,.5); }

    /* Main */
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
    <h5>🎓 TPA Teacher</h5>
    <small>Teacher Portal</small>
  </div>
  <nav class="py-2">
    <?php
    $t_base = str_replace('/admin', '/teacher', SITE_URL) . '/';
    $tmenu = [
      ['i'=>'speedometer2','l'=>'Dashboard',   's'=>'dashboard', 'h'=>$t_base.'index.php'],
      ['i'=>'collection',  'l'=>'My Batches',  's'=>'batches',   'h'=>$t_base.'batches.php'],
      ['i'=>'calendar3',   'l'=>'Timetable',   's'=>'timetable', 'h'=>$t_base.'timetable.php'],
      ['i'=>'pencil-square','l'=>'Homework',   's'=>'homework',  'h'=>$t_base.'homework.php'],
      ['i'=>'clipboard-check','l'=>'Assessments','s'=>'assessments','h'=>$t_base.'assessments.php'],
      ['i'=>'camera-video','l'=>'Online Classes','s'=>'classes', 'h'=>$t_base.'classes.php'],
      ['i'=>'person-lines-fill','l'=>'Students','s'=>'students', 'h'=>$t_base.'students.php'],
      ['i'=>'bar-chart',   'l'=>'Reports',     's'=>'reports',   'h'=>$t_base.'reports.php'],
    ];
    foreach ($tmenu as $m):
      $a = $page_section === $m['s'] ? ' active' : '';
    ?>
    <a href="<?= $m['h'] ?>" class="<?= $a ?>"><i class="bi bi-<?= $m['i'] ?>"></i><?= $m['l'] ?></a>
    <?php endforeach; ?>
  </nav>
  <div class="sidebar-footer">
    <div class="name"><?= h($currentUser['name'] ?? 'Teacher') ?></div>
    <div class="role-tag">Teacher</div>
    <a href="<?= str_replace('/admin', '', SITE_URL) ?>/logout.php" class="btn btn-sm mt-2 w-100" style="background:rgba(255,255,255,.15);color:#fff;font-size:.75rem;">Sign Out</a>
  </div>
</aside>
<main class="portal-main">
  <div class="portal-topbar">
    <h6><?= h($page_title) ?></h6>
    <div class="d-flex align-items-center gap-3">
      <span class="badge" style="background:#f0fdf4;color:#166534;font-size:.72rem;">My Batches: <?= $myBatchCount ?></span>
      <span class="badge" style="background:#fefce8;color:#854d0e;font-size:.72rem;">HW Due: <?= $pendingHWCount ?></span>
      <span class="small text-muted"><?= date('D, d M Y') ?></span>
    </div>
  </div>
  <div class="portal-content">
<?php if (function_exists('getFlash') && $flash = getFlash()): ?>
  <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show mb-3" role="alert"><?= h($flash['message']) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
