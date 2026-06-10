<?php
// =====================================================
// TPA — Student Portal Layout Header
// =====================================================

require_once __DIR__ . '/../../admin/includes/auth.php';
require_once __DIR__ . '/../../admin/includes/functions.php';
startSecureSession();
requireRole(['student','admin']);

$currentUser = currentUser();
$studentId   = (int)($currentUser['student_id'] ?? 0);
$db          = getDB();

// Load student record for display
$student = [];
if ($studentId) {
    $s = $db->prepare('SELECT * FROM students WHERE id=?');
    $s->execute([$studentId]);
    $student = $s->fetch() ?: [];
}

$page_title   = $page_title   ?? 'Student Portal';
$page_section = $page_section ?? '';

// Pending quizzes for this student
$quizCount = 0;
if ($studentId) {
    $qc = $db->prepare('SELECT COUNT(DISTINCT qa.quiz_id) FROM quiz_assignments qa
        JOIN batch_students bs ON bs.batch_id = qa.batch_id
        WHERE bs.student_id = ? AND bs.is_active=1
        AND qa.quiz_id NOT IN (SELECT quiz_id FROM quiz_attempts WHERE student_id=? AND status="submitted")');
    $qc->execute([$studentId, $studentId]);
    $quizCount = (int)$qc->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= h($page_title) ?> | TPA Student</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <style>
    :root { --navy:#0A1628; --gold:#F5A623; --purple:#7c3aed; --sidebar-w:240px; }
    *,*::before,*::after { box-sizing:border-box; }
    body { font-family:'Inter',sans-serif; background:#f5f3ff; margin:0; min-height:100vh; display:flex; }

    .portal-sidebar { width:var(--sidebar-w); background:linear-gradient(180deg,#6d28d9 0%,#4c1d95 100%); position:fixed; top:0; left:0; height:100vh; display:flex; flex-direction:column; z-index:100; overflow-y:auto; }
    .portal-sidebar .brand { padding:22px 20px 16px; border-bottom:1px solid rgba(255,255,255,.12); }
    .portal-sidebar .brand h5 { color:#fff; font-weight:900; margin:0; font-size:.95rem; }
    .portal-sidebar .brand .student-name { color:var(--gold); font-size:.8rem; font-weight:700; margin-top:4px; }
    .portal-sidebar nav a { display:flex; align-items:center; gap:10px; padding:10px 18px; color:rgba(255,255,255,.78); text-decoration:none; font-size:.84rem; font-weight:600; transition:.15s; }
    .portal-sidebar nav a:hover, .portal-sidebar nav a.active { background:rgba(255,255,255,.15); color:#fff; }
    .portal-sidebar nav a i { width:18px; text-align:center; font-size:1rem; }
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
    .quiz-badge { display:inline-flex;align-items:center;justify-content:center;background:#dc2626;color:#fff;border-radius:50%;width:18px;height:18px;font-size:.65rem;font-weight:800;margin-left:auto; }
    <?php if (isset($extra_css)) echo $extra_css; ?>
  </style>
</head>
<body>
<aside class="portal-sidebar">
  <div class="brand">
    <h5>🎓 TPA Student</h5>
    <div class="student-name"><?= h($student['first_name'] ?? $currentUser['name'] ?? 'Student') ?></div>
  </div>
  <nav class="py-2">
    <?php
    $smenu = [
      ['i'=>'speedometer2',  'l'=>'Dashboard',    's'=>'dashboard',  'h'=>'index.php'],
      ['i'=>'question-circle','l'=>'My Quizzes',  's'=>'quizzes',    'h'=>'quizzes.php', 'badge'=>$quizCount],
      ['i'=>'calendar-check','l'=>'Attendance',   's'=>'attendance', 'h'=>'attendance.php'],
      ['i'=>'trophy',        'l'=>'Results',       's'=>'results',    'h'=>'results.php'],
      ['i'=>'pencil-square', 'l'=>'Homework',      's'=>'homework',   'h'=>'homework.php'],
      ['i'=>'book',          'l'=>'Resources',     's'=>'resources',  'h'=>'resources.php'],
      ['i'=>'receipt',       'l'=>'Fees',          's'=>'fees',       'h'=>'fees.php'],
      ['i'=>'person',        'l'=>'My Profile',    's'=>'profile',    'h'=>'profile.php'],
    ];
    foreach ($smenu as $m):
      $a = $page_section === $m['s'] ? ' active' : '';
      $badge = $m['badge'] ?? 0;
    ?>
    <a href="<?= $m['h'] ?>" class="<?= $a ?>">
      <i class="bi bi-<?= $m['i'] ?>"></i><?= $m['l'] ?>
      <?php if ($badge): ?><span class="quiz-badge"><?= $badge ?></span><?php endif; ?>
    </a>
    <?php endforeach; ?>
  </nav>
  <div class="sidebar-footer">
    <div style="color:#fff;font-weight:700;font-size:.83rem;"><?= h($student['student_ref'] ?? '') ?></div>
    <a href="<?= SITE_URL ?>/logout.php" class="btn btn-sm mt-2 w-100" style="background:rgba(255,255,255,.15);color:#fff;font-size:.75rem;">Sign Out</a>
  </div>
</aside>
<main class="portal-main">
  <div class="portal-topbar">
    <h6><?= h($page_title) ?></h6>
    <span class="small text-muted"><?= date('D, d M Y') ?></span>
  </div>
  <div class="portal-content">
