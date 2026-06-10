<?php
// =====================================================
// TPA — Student Dashboard
// =====================================================
$page_title   = 'My Dashboard';
$page_section = 'dashboard';
require_once __DIR__ . '/includes/header.php';

if (!$studentId) {
    echo '<div class="alert alert-warning">Student account not linked. Please contact your administrator.</div>';
    require_once __DIR__ . '/includes/footer.php'; exit;
}

// Attendance stats (last 30 days)
$att = $db->prepare('SELECT SUM(status="present") as p, SUM(status="absent") as a, SUM(status="late") as l, COUNT(*) as t FROM attendance WHERE student_id=? AND date >= DATE_SUB(CURDATE(),INTERVAL 30 DAY)');
$att->execute([$studentId]); $att = $att->fetch();
$attPct = $att['t'] > 0 ? round($att['p'] / $att['t'] * 100) : 0;

// Pending quizzes
$pendingQuizzes = $db->prepare("SELECT DISTINCT qs.id, qs.title, qs.subject_id, qs.time_limit_min, s.name as subject_name, qa.due_date
    FROM quiz_assignments qa JOIN quiz_sets qs ON qs.id=qa.quiz_id LEFT JOIN subjects s ON s.id=qs.subject_id
    JOIN batch_students bs ON bs.batch_id=qa.batch_id
    WHERE bs.student_id=? AND bs.is_active=1 AND qs.is_active=1
    AND qs.id NOT IN (SELECT quiz_id FROM quiz_attempts WHERE student_id=? AND status='submitted')
    ORDER BY qa.due_date ASC LIMIT 5");
$pendingQuizzes->execute([$studentId, $studentId]); $pendingQuizzes = $pendingQuizzes->fetchAll();

// Recent results
$recentResults = $db->prepare("SELECT ar.*, a.name as assessment_name, a.subject, a.date, b.name as batch_name FROM assessment_results ar JOIN assessments a ON a.id=ar.assessment_id JOIN batches b ON b.id=a.batch_id WHERE ar.student_id=? ORDER BY a.date DESC LIMIT 5");
$recentResults->execute([$studentId]); $recentResults = $recentResults->fetchAll();

// Homework due
$hwDue = $db->prepare("SELECT h.*, b.name as batch_name, s.name as subject_name,
    (SELECT id FROM homework_submissions WHERE homework_id=h.id AND student_id=?) as submitted_id
    FROM homework h JOIN batches b ON b.id=h.batch_id JOIN batch_students bs ON bs.batch_id=h.batch_id LEFT JOIN subjects s ON s.id=h.subject_id
    WHERE bs.student_id=? AND bs.is_active=1 AND h.due_date >= CURDATE() ORDER BY h.due_date ASC LIMIT 5");
$hwDue->execute([$studentId, $studentId]); $hwDue = $hwDue->fetchAll();

// Outstanding fees
$outstandingFees = $db->query("SELECT COALESCE(SUM(amount_due),0) FROM invoices WHERE student_id=$studentId AND status IN ('unpaid','overdue')")->fetchColumn();

// Average result
$avgResult = $db->prepare('SELECT AVG(percentage) FROM assessment_results WHERE student_id=?');
$avgResult->execute([$studentId]); $avgResult = round((float)$avgResult->fetchColumn());
?>

<div class="page-header">
  <h1>👋 Hello, <?= h($student['first_name'] ?? 'Student') ?>!</h1>
  <span class="badge" style="background:#ede9fe;color:#4c1d95;padding:8px 14px;font-size:.8rem;"><?= h($student['student_ref'] ?? '') ?> · <?= h($student['year_group'] ?? '') ?></span>
</div>

<!-- Stat cards -->
<div class="row g-3 mb-4">
  <div class="col-6 col-lg-3">
    <div class="stat-card text-center">
      <div class="stat-value" style="color:<?= $attPct >= 85 ? '#16a34a' : ($attPct >= 70 ? '#ca8a04' : '#dc2626') ?>;"><?= $attPct ?>%</div>
      <div class="stat-label">Attendance (30d)</div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="stat-card text-center">
      <div class="stat-value" style="color:#7c3aed;"><?= count($pendingQuizzes) ?></div>
      <div class="stat-label">Quizzes Due</div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="stat-card text-center">
      <div class="stat-value" style="color:#0d9488;"><?= $avgResult > 0 ? $avgResult.'%' : '—' ?></div>
      <div class="stat-label">Avg Score</div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="stat-card text-center">
      <div class="stat-value" style="color:<?= $outstandingFees > 0 ? '#dc2626' : '#16a34a' ?>;">£<?= number_format($outstandingFees,0) ?></div>
      <div class="stat-label">Fees Due</div>
    </div>
  </div>
</div>

<div class="row g-4">
  <!-- Pending quizzes -->
  <div class="col-lg-6">
    <div class="stat-card h-100">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="fw-700" style="color:var(--navy);">📝 Quizzes To Attempt</div>
        <a href="quizzes.php" class="btn btn-sm btn-outline-secondary" style="font-size:.75rem;">View All</a>
      </div>
      <?php if (empty($pendingQuizzes)): ?>
        <div class="text-center text-muted py-4">🎉 All quizzes completed!</div>
      <?php else: ?>
        <?php foreach ($pendingQuizzes as $q): ?>
          <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
            <div>
              <div class="fw-600 small"><?= h($q['title']) ?></div>
              <div class="text-muted" style="font-size:.72rem;"><?= h($q['subject_name'] ?? '—') ?> · <?= $q['time_limit_min'] > 0 ? $q['time_limit_min'].'min' : 'No limit' ?><?= $q['due_date'] ? ' · Due '.date('d M',strtotime($q['due_date'])) : '' ?></div>
            </div>
            <a href="quiz-attempt.php?quiz_id=<?= $q['id'] ?>" class="btn btn-sm" style="background:#7c3aed;color:#fff;font-size:.75rem;white-space:nowrap;"><i class="bi bi-play-fill me-1"></i>Start</a>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- Homework due -->
  <div class="col-lg-6">
    <div class="stat-card h-100">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="fw-700" style="color:var(--navy);">📚 Homework Due</div>
        <a href="homework.php" class="btn btn-sm btn-outline-secondary" style="font-size:.75rem;">View All</a>
      </div>
      <?php if (empty($hwDue)): ?>
        <div class="text-center text-muted py-4">🎉 No homework due!</div>
      <?php else: ?>
        <?php foreach ($hwDue as $hw):
          $isSubmitted = !empty($hw['submitted_id']);
          $isLate      = strtotime($hw['due_date']) < strtotime('today');
        ?>
          <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
            <div>
              <div class="fw-600 small"><?= h($hw['title']) ?> <?= $isLate ? '<span class="badge bg-danger" style="font-size:.6rem;">Late</span>' : '' ?></div>
              <div class="text-muted" style="font-size:.72rem;"><?= h($hw['batch_name']) ?> · Due <?= date('D d M', strtotime($hw['due_date'])) ?></div>
            </div>
            <?php if ($isSubmitted): ?>
              <span class="badge bg-success" style="font-size:.7rem;">Submitted</span>
            <?php else: ?>
              <a href="homework.php?submit=<?= $hw['id'] ?>" class="btn btn-sm btn-outline-warning py-0" style="font-size:.75rem;">Submit</a>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- Recent results -->
  <div class="col-12">
    <div class="tpa-table">
      <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
        <div class="fw-700" style="color:var(--navy);">🏆 Recent Results</div>
        <a href="results.php" class="btn btn-sm btn-outline-secondary">View All</a>
      </div>
      <table class="table table-hover mb-0">
        <thead><tr><th>Assessment</th><th>Subject</th><th>Date</th><th>Score</th><th>%</th><th>Grade</th></tr></thead>
        <tbody>
        <?php foreach ($recentResults as $r): ?>
          <tr>
            <td class="fw-600 small"><?= h($r['assessment_name']) ?></td>
            <td class="small text-muted"><?= h($r['subject'] ?? '—') ?></td>
            <td class="small"><?= date('d M Y', strtotime($r['date'])) ?></td>
            <td class="fw-700"><?= $r['marks'] !== null ? h($r['marks']).'/'.$r['max_marks'] : '—' ?></td>
            <td><span class="fw-700" style="color:<?= ($r['percentage']??0)>=70?'#16a34a':(($r['percentage']??0)>=50?'#ca8a04':'#dc2626') ?>;"><?= $r['percentage'] !== null ? round($r['percentage']).'%' : '—' ?></span></td>
            <td><?= $r['grade'] ? '<span class="badge bg-dark">'.h($r['grade']).'</span>' : '—' ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($recentResults)): ?><tr><td colspan="6" class="text-center text-muted py-4">No results yet.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
