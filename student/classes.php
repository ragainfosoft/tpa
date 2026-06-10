<?php
// =====================================================
// TPA — Student Online Classes View
// =====================================================
$page_title   = 'Online Classes';
$page_section = 'classes';
require_once __DIR__ . '/includes/header.php';

if (!$studentId) { echo '<div class="alert alert-warning">No student account linked.</div>'; require_once __DIR__ . '/includes/footer.php'; exit; }

$classes = $db->prepare("SELECT oc.*, b.name as batch_name, s.name as subject_name, u.name as teacher_name
    FROM online_classes oc JOIN batches b ON b.id=oc.batch_id JOIN batch_students bs ON bs.batch_id=b.id
    LEFT JOIN subjects s ON s.id=b.subject_id LEFT JOIN teachers t ON t.id=b.teacher_id LEFT JOIN users u ON u.id=t.user_id
    WHERE bs.student_id=? AND bs.is_active=1 AND oc.is_active=1 ORDER BY oc.scheduled_at DESC LIMIT 50");
$classes->execute([$studentId]); $classes = $classes->fetchAll();

$upcoming = array_filter($classes, fn($c)=>strtotime($c['scheduled_at']) >= strtotime('-2 hours'));
$past     = array_filter($classes, fn($c)=>strtotime($c['scheduled_at']) < strtotime('-2 hours'));
?>

<div class="page-header">
  <div>
    <h1><i class="bi bi-camera-video me-2" style="color:var(--purple);"></i>Online Classes</h1>
    <p class="text-muted mb-0">Join your live sessions</p>
  </div>
</div>

<?php if (!empty($upcoming)): ?>
<h6 class="text-uppercase fw-700 mb-3" style="font-size:.7rem;letter-spacing:.1em;color:#888;">⏳ Upcoming Live Sessions</h6>
<div class="row g-3 mb-5">
  <?php foreach ($upcoming as $c):
    $scheduled = strtotime($c['scheduled_at']);
    $timeStr = date('g:ia', $scheduled) . ' (' . $c['duration_min'] . 'm)';
    $isToday = date('Y-m-d') === date('Y-m-d', $scheduled);
    $isNow   = $scheduled <= time() + 900 && $scheduled + ($c['duration_min']*60) >= time(); // 15 mins before to end
  ?>
  <div class="col-md-6 col-lg-4">
    <div class="stat-card h-100" style="<?= $isNow?'border:2px solid #16a34a;box-shadow:0 0 0 4px rgba(22,163,74,.15);':'border-left:4px solid '.($isToday?'var(--gold)':'var(--purple)') ?>;">
      <div class="d-flex justify-content-between align-items-start mb-2">
        <span class="badge bg-light text-dark border" style="font-size:.67rem;"> <?= h($c['meeting_platform']) ?></span>
        <?php if ($isNow): ?><span class="badge bg-success shadow-sm" style="font-size:.65rem;animation:pulse 2s infinite;"><i class="bi bi-record-circle me-1"></i>Live Now</span>
        <?php elseif ($isToday): ?><span class="badge bg-warning text-dark" style="font-size:.65rem;">Today</span><?php endif; ?>
      </div>
      <div class="fw-700 mb-1"><?= h($c['title'] ?: $c['batch_name']) ?></div>
      <div class="small fw-600 text-muted mb-3"><?= h($c['batch_name']) ?> <?= $c['subject_name']?'· '.h($c['subject_name']):'' ?></div>
      <div class="d-flex gap-2 flex-wrap mb-4">
        <span class="badge" style="background:#f1f5f9;color:#334155;"><i class="bi bi-calendar me-1"></i><?= date('D d M', $scheduled) ?></span>
        <span class="badge" style="background:#f1f5f9;color:#334155;"><i class="bi bi-clock me-1"></i><?= $timeStr ?></span>
        <span class="badge" style="background:#f1f5f9;color:#334155;"><i class="bi bi-person me-1"></i><?= h($c['teacher_name']) ?></span>
      </div>
      <div class="d-flex gap-2">
        <?php if ($c['platform'] === 'Jitsi Meet (Built-in)'): ?>
            <a href="../live-class.php?id=<?= $c['id'] ?>" target="_blank" class="btn w-100 fw-800 btn-success">
                <i class="bi bi-camera-video-fill me-1"></i> Join live Session
            </a>
        <?php else: ?>
            <a href="<?= h($c['meeting_url']) ?>" target="_blank" class="btn w-100 fw-700 <?= $isNow?'btn-success':'' ?>" style="<?= !$isNow?'background:#ede9fe;color:#4c1d95;':'' ?>"><i class="bi bi-camera-video me-1"></i>Join Class</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<style>@keyframes pulse { 0% { opacity:1; } 50% { opacity:.6; } 100% { opacity:1; } }</style>
<?php endif; ?>

<?php if (!empty($past)): ?>
<h6 class="text-uppercase fw-700 mb-3 mt-5" style="font-size:.7rem;letter-spacing:.1em;color:#888;">🕒 Past Sessions & Attendance</h6>
<div class="tpa-table">
  <table class="table table-hover mb-0">
    <thead><tr><th>Session</th><th>Batch</th><th>Date & Time</th><th>Status</th></tr></thead>
    <tbody>
    <?php foreach ($past as $c): 
      $att = $db->prepare("SELECT id FROM online_class_attendance WHERE online_class_id = ? AND student_id = ?");
      $att->execute([$c['id'], $studentId]);
      $present = $att->fetch();
    ?>
      <tr>
        <td class="fw-600"><?= h($c['title'] ?: 'Class Session') ?></td>
        <td class="small text-muted"><?= h($c['batch_name']) ?></td>
        <td class="small"><?= date('D d M Y, g:ia', strtotime($c['scheduled_at'])) ?></td>
        <td>
          <?php if ($present): ?>
            <span class="badge bg-success-subtle text-success border border-success-subtle px-2"><i class="bi bi-check-circle-fill me-1"></i>Joined</span>
          <?php else: ?>
            <span class="badge bg-light text-muted border px-2">Not Joined</span>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<?php if (empty($classes)): ?>
  <div class="text-center py-5 text-muted" style="background:#fff;border-radius:16px;">
    <i class="bi bi-camera-video fs-1 d-block mb-3" style="color:#cbd5e1;"></i>
    <h5>No online classes scheduled.</h5>
    <p class="small">Your live session links will appear here when your teacher schedules them.</p>
  </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
