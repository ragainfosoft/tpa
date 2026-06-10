<?php
// =====================================================
// TPA — Teacher Dashboard
// =====================================================
$page_title   = 'My Dashboard';
$page_section = 'dashboard';
require_once __DIR__ . '/includes/header.php';

// Fetch teacher record
$teacherRow = $db->prepare('SELECT t.id FROM teachers t JOIN users u ON u.id=t.user_id WHERE u.id=?');
$teacherRow->execute([$currentUser['id']]);
$teacherId = (int)($teacherRow->fetchColumn() ?: 0);

// My batches today
$dayName = date('l');
$myBatchesToday = [];
if ($teacherId) {
    $q = $db->prepare("SELECT b.*, (SELECT COUNT(*) FROM batch_students bs WHERE bs.batch_id=b.id AND bs.is_active=1) as enrolled
        FROM batches b WHERE b.teacher_id=? AND b.is_active=1 AND FIND_IN_SET(?,REPLACE(b.day_of_week,' ','')) ORDER BY b.start_time");
    $q->execute([$teacherId, $dayName]); $myBatchesToday = $q->fetchAll();
}

// All my batches
$allMyBatches = [];
if ($teacherId) {
    $q = $db->prepare("SELECT b.*, s.name as subject_name, (SELECT COUNT(*) FROM batch_students bs WHERE bs.batch_id=b.id AND bs.is_active=1) as enrolled FROM batches b LEFT JOIN subjects s ON s.id=b.subject_id WHERE b.teacher_id=? AND b.is_active=1 ORDER BY b.name");
    $q->execute([$teacherId]); $allMyBatches = $q->fetchAll();
}

// Pending homework to grade
$pendingGrading = $db->prepare("SELECT h.title, h.due_date, b.name as batch_name,
    COUNT(hs.id) as subs, SUM(hs.status='submitted') as ungraded
    FROM homework h JOIN batches b ON b.id=h.batch_id
    LEFT JOIN homework_submissions hs ON hs.homework_id=h.id
    WHERE h.set_by=? GROUP BY h.id ORDER BY h.due_date DESC LIMIT 5");
$pendingGrading->execute([$currentUser['id']]); $pendingGrading = $pendingGrading->fetchAll();

// Upcoming online classes
$upcomingClasses = $db->prepare("SELECT oc.*, b.name as batch_name FROM online_classes oc JOIN batches b ON b.id=oc.batch_id WHERE b.teacher_id=? AND oc.scheduled_at >= NOW() ORDER BY oc.scheduled_at ASC LIMIT 5");
$upcomingClasses->execute([$teacherId ?: 0]); $upcomingClasses = $upcomingClasses->fetchAll();
?>

<div class="page-header">
  <h1>👋 Good <?= date('H') < 12 ? 'morning' : (date('H') < 17 ? 'afternoon' : 'evening') ?>, <?= h(explode(' ', $currentUser['name'])[0]) ?>!</h1>
  <span class="badge" style="background:#f0fdf4;color:#166534;padding:8px 14px;font-size:.8rem;"><?= date('l, d F Y') ?></span>
</div>

<!-- Stat cards -->
<div class="row g-3 mb-4">
  <div class="col-sm-3">
    <div class="stat-card text-center">
      <div class="stat-value" style="color:var(--teal);"><?= count($allMyBatches) ?></div>
      <div class="stat-label">My Batches</div>
    </div>
  </div>
  <div class="col-sm-3">
    <div class="stat-card text-center">
      <div class="stat-value" style="color:#7c3aed;"><?= count($myBatchesToday) ?></div>
      <div class="stat-label">Classes Today</div>
    </div>
  </div>
  <div class="col-sm-3">
    <div class="stat-card text-center">
      <div class="stat-value" style="color:#ca8a04;"><?= array_sum(array_column($pendingGrading,'ungraded')) ?></div>
      <div class="stat-label">To Grade</div>
    </div>
  </div>
  <div class="col-sm-3">
    <div class="stat-card text-center">
      <div class="stat-value" style="color:#dc2626;"><?= count($upcomingClasses) ?></div>
      <div class="stat-label">Online Sessions</div>
    </div>
  </div>
</div>

<div class="row g-4">
  <!-- Today's classes -->
  <div class="col-lg-7">
    <div class="stat-card">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="fw-700" style="color:var(--navy);">Today's Classes <span class="text-muted fw-400 small">(<?= $dayName ?>)</span></div>
        <a href="<?= SITE_URL ?>/attendance/mark.php" class="btn btn-sm" style="background:var(--teal);color:#fff;font-size:.75rem;">Mark Attendance</a>
      </div>
      <?php if (empty($myBatchesToday)): ?>
        <div class="text-center text-muted py-4"><i class="bi bi-calendar-x fs-2 d-block mb-2"></i>No classes scheduled today.</div>
      <?php else: ?>
        <?php foreach ($myBatchesToday as $b): ?>
          <div class="d-flex align-items-center gap-3 py-3 border-bottom">
            <div style="width:48px;height:48px;border-radius:12px;background:#e0f2fe;display:flex;align-items:center;justify-content:center;font-size:1.3rem;">📚</div>
            <div class="flex-grow-1">
              <div class="fw-700"><?= h($b['name']) ?></div>
              <div class="small text-muted"><?= date('g:ia', strtotime($b['start_time'])) ?>–<?= date('g:ia', strtotime($b['end_time'])) ?> · <?= $b['enrolled'] ?> students</div>
            </div>
            <a href="<?= SITE_URL ?>/attendance/mark.php?batch_id=<?= $b['id'] ?>&date=<?= date('Y-m-d') ?>" class="btn btn-sm btn-outline-secondary">Mark</a>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- Quick actions -->
  <div class="col-lg-5">
    <div class="stat-card mb-3">
      <div class="fw-700 mb-3" style="color:var(--navy);">Quick Actions</div>
      <div class="d-grid gap-2">
        <a href="homework.php?action=create" class="btn" style="background:#ede9fe;color:#4c1d95;font-weight:700;text-align:left;"><i class="bi bi-pencil-square me-2"></i>Set Homework</a>
        <a href="classes.php?action=create" class="btn" style="background:#e0f2fe;color:#0369a1;font-weight:700;text-align:left;"><i class="bi bi-camera-video me-2"></i>Schedule Online Class</a>
        <a href="<?= SITE_URL ?>/quizzes/create.php" class="btn" style="background:#fce7f3;color:#9d174d;font-weight:700;text-align:left;"><i class="bi bi-question-circle me-2"></i>Create Quiz</a>
        <a href="assessments.php?action=results" class="btn" style="background:#fef3c7;color:#b45309;font-weight:700;text-align:left;"><i class="bi bi-journal-check me-2"></i>Enter Results</a>
      </div>
    </div>

    <?php if (!empty($upcomingClasses)): ?>
    <div class="stat-card">
      <div class="fw-700 mb-3" style="color:var(--navy);">Upcoming Online Sessions</div>
      <?php foreach ($upcomingClasses as $oc): ?>
        <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
          <div>
            <div class="fw-600 small"><?= h($oc['title'] ?: $oc['batch_name']) ?></div>
            <div class="text-muted" style="font-size:.72rem;"><?= date('D d M, g:ia', strtotime($oc['scheduled_at'])) ?> · <?= $oc['duration_min'] ?>min</div>
          </div>
          <a href="<?= h($oc['meeting_url']) ?>" target="_blank" class="btn btn-sm btn-success py-0 px-2" style="font-size:.72rem;"><i class="bi bi-camera-video me-1"></i>Join</a>
        </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

  <!-- All my batches -->
  <div class="col-12">
    <div class="tpa-table">
      <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
        <div class="fw-700" style="color:var(--navy);">My Batches</div>
        <a href="batches.php" class="btn btn-sm btn-outline-secondary">View All</a>
      </div>
      <table class="table table-hover mb-0">
        <thead><tr><th>Batch</th><th>Subject</th><th>Schedule</th><th>Students</th><th>Centre</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($allMyBatches as $b): ?>
          <tr>
            <td class="fw-600"><?= h($b['name']) ?></td>
            <td class="small text-muted"><?= h($b['subject_name'] ?? $b['course_type'] ?? '—') ?></td>
            <td class="small"><?= h($b['day_of_week']) ?> <?= date('g:ia', strtotime($b['start_time'])) ?>–<?= date('g:ia', strtotime($b['end_time'])) ?></td>
            <td><span class="badge bg-light text-dark border"><?= $b['enrolled'] ?>/<?= $b['max_capacity'] ?></span></td>
            <td class="small"><?= h($b['centre'] ?? '—') ?></td>
            <td>
              <a href="batches.php?view=<?= $b['id'] ?>" class="btn btn-sm btn-outline-secondary py-0">View</a>
              <a href="homework.php?batch_id=<?= $b['id'] ?>" class="btn btn-sm btn-outline-warning py-0 ms-1">HW</a>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($allMyBatches)): ?><tr><td colspan="6" class="text-center text-muted py-4">No batches assigned yet.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
