<?php
// =====================================================
// TPA IMS — Admin Layout Header
// =====================================================
// Must be included after $page_title, $page_section
// are defined in each page file.

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/helpers.php';

requireLogin(SITE_URL . '/login.php');

$currentUser = currentUser();
$role        = currentRole();

if ($role === 'teacher') {
    require_once __DIR__ . '/../../teacher/includes/header.php';
    return;
}

// Live badge counts for sidebar
$_badgeCounts = [];
try {
    $db_h = getDB();
    $_badgeCounts['leads']     = (int)$db_h->query('SELECT COUNT(*) FROM leads WHERE status NOT IN ("enrolled","lost")')->fetchColumn();
    $_badgeCounts['fees']      = (int)$db_h->query('SELECT COUNT(*) FROM invoices WHERE status="overdue"')->fetchColumn();
    $_badgeCounts['reminders'] = (int)$db_h->query('SELECT COUNT(*) FROM invoices WHERE status IN ("unpaid","overdue") AND (reminder_sent_at IS NULL OR DATE(reminder_sent_at) < DATE_SUB(CURDATE(),INTERVAL 6 DAY))')->fetchColumn();
    $_badgeCounts['unmatched'] = (int)$db_h->query('SELECT COUNT(*) FROM payments WHERE reconciled=0')->fetchColumn();
    $_badgeCounts['assessments'] = 0;
} catch (Exception $e) { $_badgeCounts = []; }

// Sidebar menu definition
$menu = [
    ['icon' => 'speedometer2',   'label' => 'Dashboard',        'href' => SITE_URL . '/index.php',                  'section' => 'dashboard',    'roles' => ['admin','staff']],
    ['icon' => 'funnel',          'label' => 'Lead CRM',         'href' => SITE_URL . '/leads/index.php',             'section' => 'leads',        'roles' => ['admin','staff'], 'badge' => $_badgeCounts['leads'] ?? 0],
    ['icon' => 'people',          'label' => 'Students',         'href' => SITE_URL . '/students/index.php',          'section' => 'students',     'roles' => ['admin','staff']],
    ['icon' => 'person-badge',    'label' => 'Teachers',         'href' => SITE_URL . '/teachers/index.php',          'section' => 'teachers',     'roles' => ['admin','staff']],
    ['icon' => 'collection',      'label' => 'Batches',          'href' => SITE_URL . '/batches/index.php',           'section' => 'batches',      'roles' => ['admin','staff']],
    ['icon' => 'camera-video',    'label' => 'Online Classes',   'href' => SITE_URL . '/classes/index.php',           'section' => 'classes',      'roles' => ['admin','branch_manager','teacher','staff']],
    ['icon' => 'calendar-check',  'label' => 'Attendance',       'href' => SITE_URL . '/attendance/index.php',        'section' => 'attendance',   'roles' => ['admin','staff']],
    ['icon' => 'receipt',         'label' => 'Fees',             'href' => SITE_URL . '/fees/index.php',              'section' => 'fees',         'roles' => ['admin','staff'], 'badge' => $_badgeCounts['fees'] ?? 0],
    ['icon' => 'bell',            'label' => 'Reminders',        'href' => SITE_URL . '/reminders/index.php',         'section' => 'reminders',    'roles' => ['admin','staff'], 'badge' => $_badgeCounts['reminders'] ?? 0],
    ['icon' => 'arrow-left-right','label' => 'Reconciliation',   'href' => SITE_URL . '/payments/reconcile.php',      'section' => 'reconcile',    'roles' => ['admin','branch_manager'], 'badge' => $_badgeCounts['unmatched'] ?? 0],
    ['icon' => 'clipboard-data',  'label' => 'Assessments',      'href' => SITE_URL . '/assessments/index.php',       'section' => 'assessments',  'roles' => ['admin','branch_manager','teacher','staff']],
    ['icon' => 'question-circle', 'label' => 'Quizzes',          'href' => SITE_URL . '/quizzes/index.php',            'section' => 'quizzes',      'roles' => ['admin','branch_manager','teacher','staff']],
    ['icon' => 'pencil-square',   'label' => 'Homework',         'href' => SITE_URL . '/homework/index.php',           'section' => 'homework',     'roles' => ['admin','branch_manager','teacher','staff']],
    ['icon' => 'chat-dots',       'label' => 'Communication',    'href' => SITE_URL . '/communication/index.php',     'section' => 'comms',        'roles' => ['admin','staff']],
    ['icon' => 'bar-chart',       'label' => 'Reports',          'href' => SITE_URL . '/reports/index.php',           'section' => 'reports',      'roles' => ['admin','branch_manager']],
    ['icon' => 'gear',            'label' => 'Settings',         'href' => SITE_URL . '/settings/index.php',          'section' => 'settings',     'roles' => ['admin']],
];

$page_title   = $page_title ?? 'TPA Admin';
$page_section = $page_section ?? '';
?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="<?= h(csrfToken()) ?>">
  <title><?= h($page_title) ?> | <?= h(getSetting('site_name','TPA Admin')) ?></title>
  <!-- Bootstrap 5 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
  <!-- Chart.js (loaded in footer) -->
  <!-- Custom Admin CSS -->
  <style>
    :root {
      --navy:      #0A1628;
      --navy-mid:  #132040;
      --gold:      #F5A623;
      --gold-pale: rgba(245,166,35,0.12);
      --sidebar-w: 260px;
      --topbar-h:  64px;
      --bs-body-bg:#f0f2f5;
    }

    /* ── Sidebar ── */
    #sidebar {
      width: var(--sidebar-w);
      height: 100vh;
      position: fixed;
      top: 0; left: 0;
      background: var(--navy);
      display: flex;
      flex-direction: column;
      z-index: 1040;
      transition: transform .3s ease;
      overflow-y: auto;
    }
    #sidebar .sidebar-logo {
      padding: 20px 24px 16px;
      border-bottom: 1px solid rgba(255,255,255,0.08);
      flex-shrink: 0;
    }
    #sidebar .sidebar-logo span { font-size: 1.15rem; font-weight: 900; color: #fff; letter-spacing: -.5px; }
    #sidebar .sidebar-logo em   { color: var(--gold); font-style: normal; }
    #sidebar .sidebar-logo small{ display: block; font-size: .7rem; color: rgba(255,255,255,.4); letter-spacing:.08em; text-transform:uppercase; margin-top:2px; }

    .nav-section-label {
      font-size: .68rem; font-weight: 700; color: rgba(255,255,255,.35);
      text-transform: uppercase; letter-spacing: .1em;
      padding: 20px 24px 6px;
    }

    .sidebar-nav a {
      display: flex; align-items: center; gap: 10px;
      padding: 10px 24px; font-size: .875rem; font-weight: 500;
      color: rgba(255,255,255,.65); text-decoration: none;
      border-left: 3px solid transparent;
      transition: all .18s;
    }
    .sidebar-nav a:hover { color: #fff; background: rgba(255,255,255,.06); }
    .sidebar-nav a.active {
      color: var(--gold); background: rgba(245,166,35,.1);
      border-left-color: var(--gold); font-weight: 700;
    }
    .sidebar-nav a i { font-size: 1rem; width: 20px; text-align: center; }

    .sidebar-footer {
      margin-top: auto; padding: 16px 24px;
      border-top: 1px solid rgba(255,255,255,.08);
      font-size: .8rem; color: rgba(255,255,255,.4);
    }

    /* ── Topbar ── */
    #topbar {
      height: var(--topbar-h);
      background: #fff;
      border-bottom: 1px solid rgba(0,0,0,.07);
      position: fixed;
      top: 0; left: var(--sidebar-w); right: 0;
      display: flex; align-items: center; justify-content: space-between;
      padding: 0 24px; z-index: 1030;
      box-shadow: 0 1px 4px rgba(0,0,0,.05);
    }

    /* ── Main content ── */
    #main-content {
      margin-left: var(--sidebar-w);
      padding-top: var(--topbar-h);
      min-height: 100vh;
    }
    .content-area { padding: 28px 28px 40px; }

    /* ── Page header ── */
    .page-header {
      display: flex; align-items: center; justify-content: space-between;
      margin-bottom: 24px; flex-wrap: wrap; gap: 12px;
    }
    .page-header h1 { font-size: 1.45rem; font-weight: 800; color: var(--navy); margin: 0; }

    /* ── Cards ── */
    .stat-card {
      border-radius: 12px; padding: 20px 22px;
      background: #fff; border: 1px solid rgba(0,0,0,.07);
      box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }
    .stat-card .stat-label { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #8898a0; margin-bottom: 4px; }
    .stat-card .stat-value { font-size: 1.8rem; font-weight: 900; color: var(--navy); line-height: 1; }
    .stat-card .stat-icon  { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }

    /* ── Tables ── */
    .tpa-table { border-radius: 10px; overflow: hidden; border: 1px solid rgba(0,0,0,.07); }
    .tpa-table thead th { background: var(--navy); color: rgba(255,255,255,.8); font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; border: none; padding: 12px 14px; }
    .tpa-table tbody tr:hover { background: #f8f9ff; }
    .tpa-table td { vertical-align: middle; font-size: .875rem; padding: 10px 14px; }

    /* ── Kanban ── */
    .kanban-board { display: flex; gap: 16px; overflow-x: auto; padding-bottom: 16px; align-items: flex-start; }
    .kanban-col { min-width: 260px; max-width: 280px; background: #f4f5f7; border-radius: 14px; padding: 14px; flex-shrink: 0; }
    .kanban-col-header { display: flex; align-items: center; justify-content: space-between; padding: 2px 0 12px; border-bottom: 2px solid rgba(0,0,0,.06); margin-bottom: 10px; }
    .kanban-col-header h6 { font-size: .72rem; font-weight: 800; text-transform: uppercase; letter-spacing: .09em; margin: 0; }
    .kanban-drop { min-height: 60px; }
    .kanban-card { background: #fff; border-radius: 10px; padding: 13px 14px; margin-bottom: 9px; box-shadow: 0 1px 3px rgba(0,0,0,.06); cursor: grab; font-size: .83rem; border-left: 3px solid transparent; transition: box-shadow .2s, transform .15s; position: relative; }
    .kanban-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,.12); transform: translateY(-1px); }
    .kanban-card:active { cursor: grabbing; }
    .kanban-avatar { width: 30px; height: 30px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: .7rem; font-weight: 800; color: #fff; flex-shrink: 0; }
    .kanban-name { font-size: .84rem; font-weight: 700; color: #0A1628; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex: 1; min-width: 0; }
    .kanban-meta { font-size: .72rem; color: #8898a0; }
    .kanban-course { font-size: .74rem; color: #4a5568; background: #f0f2f5; border-radius: 5px; padding: 2px 7px; display: inline-block; max-width: 100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 5px; }
    .kanban-footer { display: flex; align-items: center; justify-content: space-between; margin-top: 9px; padding-top: 8px; border-top: 1px solid #f0f2f5; }
    .kanban-time { font-size: .68rem; color: #b0bec5; }
    .kanban-actions { display: flex; gap: 4px; }

    /* ── Form controls ── */
    .form-control:focus, .form-select:focus { border-color: var(--gold); box-shadow: 0 0 0 3px rgba(245,166,35,.15); }

    /* ── Badges ── */
    .bg-purple { background-color: #7c3aed !important; }

    /* ── Responsive ── */
    @media (max-width: 991px) {
      #sidebar { transform: translateX(-100%); }
      #sidebar.show { transform: translateX(0); }
      #topbar, #main-content { left: 0; margin-left: 0; }
    }
  </style>
</head>
<body>

<!-- ── Sidebar ───────────────────────────────────────── -->
<nav id="sidebar">
  <div class="sidebar-logo">
    <span>Talent<em>Pool</em> Academy</span>
    <small>Institute Management</small>
  </div>

  <div class="nav-section-label">Main Menu</div>
  <div class="sidebar-nav">
    <?php foreach ($menu as $item):
      if (!in_array($role, $item['roles'], true)) continue;
      $active = ($page_section === $item['section']) ? ' active' : '';
      $badge = (isset($item['badge']) && $item['badge'] > 0) ? $item['badge'] : 0;
    ?>
      <a href="<?= h($item['href']) ?>" class="<?= $active ?>">
        <i class="bi bi-<?= h($item['icon']) ?>"></i>
        <?= h($item['label']) ?>
        <?php if ($badge): ?><span style="margin-left:auto;background:rgba(220,53,69,.85);color:#fff;font-size:.6rem;font-weight:800;padding:1px 6px;border-radius:10px;line-height:1.6;"><?= $badge ?></span><?php endif; ?>
      </a>
    <?php endforeach; ?>
  </div>

  <!-- Box Music Academy Partner Link -->
  <div style="margin:0 12px 12px;padding:10px 12px;background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.2);border-radius:10px;">
    <a href="/boxmusic/index.php" target="_blank" style="display:flex;align-items:center;gap:8px;font-size:.75rem;color:rgba(255,255,255,.65);text-decoration:none;transition:color .2s;" onmouseover="this.style.color='#f59e0b'" onmouseout="this.style.color='rgba(255,255,255,.65)'">
      <span style="font-size:1rem;">🎵</span>
      <span style="font-weight:600;">Box Music Academy</span>
      <i class="bi bi-arrow-up-right-square ms-auto" style="font-size:.65rem;"></i>
    </a>
  </div>

  <div class="sidebar-footer">
    <div class="fw-700 text-white mb-1"><?= h($currentUser['name'] ?? '') ?></div>
    <div><?= ucfirst(h($role)) ?></div>
  </div>
</nav>

<!-- ── Topbar ─────────────────────────────────────────── -->
<div id="topbar">
  <div class="d-flex align-items-center gap-3">
    <button class="btn btn-sm btn-light d-lg-none" id="sidebarToggle"><i class="bi bi-list fs-5"></i></button>
    <nav aria-label="breadcrumb" class="d-none d-md-block">
      <ol class="breadcrumb mb-0 small">
        <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/index.php" class="text-decoration-none">TPA Admin</a></li>
        <?php if (!empty($page_title)): ?>
          <li class="breadcrumb-item active"><?= h($page_title) ?></li>
        <?php endif; ?>
      </ol>
    </nav>
  </div>
  <div class="d-flex align-items-center gap-3">
    <!-- Quick actions -->
    <a href="<?= SITE_URL ?>/leads/add.php" class="btn btn-sm" style="background:var(--gold-pale);color:var(--navy);font-weight:700;border-radius:8px;" title="New Lead">
      <i class="bi bi-plus-lg me-1"></i><span class="d-none d-sm-inline">New Lead</span>
    </a>
    <!-- Notifications placeholder -->
    <div class="dropdown">
      <button class="btn btn-sm btn-light position-relative" data-bs-toggle="dropdown">
        <i class="bi bi-bell"></i>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notif-count" style="font-size:.55rem;display:none;"></span>
      </button>
      <div class="dropdown-menu dropdown-menu-end p-3" style="min-width:300px;">
        <div class="fw-700 mb-2 small">Notifications</div>
        <div id="notif-list" class="small text-muted">No new notifications</div>
      </div>
    </div>
    <!-- User menu -->
    <div class="dropdown">
      <button class="btn btn-sm d-flex align-items-center gap-2" data-bs-toggle="dropdown" style="background:#f0f2f5;border-radius:8px;padding:6px 12px;">
        <div style="width:28px;height:28px;border-radius:50%;background:var(--navy);color:var(--gold);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:.8rem;">
          <?= strtoupper(substr($currentUser['name'] ?? 'A', 0, 1)) ?>
        </div>
        <span class="d-none d-sm-inline fw-600 small"><?= h(explode(' ', $currentUser['name'] ?? '')[0]) ?></span>
        <i class="bi bi-chevron-down" style="font-size:.65rem;"></i>
      </button>
      <ul class="dropdown-menu dropdown-menu-end shadow-sm">
        <li><span class="dropdown-item-text small text-muted"><?= h($currentUser['email'] ?? '') ?></span></li>
        <li><hr class="dropdown-divider my-1"></li>
        <?php if (isAdmin()): ?>
          <li><a class="dropdown-item" href="<?= SITE_URL ?>/settings/index.php"><i class="bi bi-gear me-2"></i>Settings</a></li>
        <?php endif; ?>
        <li><a class="dropdown-item text-danger" href="<?= SITE_URL ?>/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
      </ul>
    </div>
  </div>
</div>

<!-- ── Main ───────────────────────────────────────────── -->
<main id="main-content">
  <div class="content-area">
    <?= renderFlash() ?>
