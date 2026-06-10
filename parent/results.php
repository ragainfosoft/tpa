<?php
// =====================================================
// TPA — Parent Portal: Results
// =====================================================
$page_title   = 'Exam & Quiz Results';
$page_section = 'results';
require_once __DIR__ . '/includes/header.php';

if (!$activeChild) { echo '<div class="alert alert-warning">No child linked.</div>'; require_once __DIR__ . '/includes/footer.php'; exit; }
$childId = $activeChild['id'];

// Formal offline assessments
$assessments = $db->prepare("SELECT ar.*, a.name as assessment_name, a.subject, a.date, b.name as batch_name FROM assessment_results ar JOIN assessments a ON a.id=ar.assessment_id JOIN batches b ON b.id=a.batch_id WHERE ar.student_id=? ORDER BY a.date DESC");
$assessments->execute([$childId]); $assessments = $assessments->fetchAll();

// MCQ Quizzes
$quizzes = $db->prepare("SELECT qa.*, qs.title, qs.subject_id, sub.name as subject_name FROM quiz_attempts qa JOIN quiz_sets qs ON qs.id=qa.quiz_id LEFT JOIN subjects sub ON sub.id=qs.subject_id WHERE qa.student_id=? AND qa.status='submitted' ORDER BY qa.submitted_at DESC");
$quizzes->execute([$childId]); $quizzes = $quizzes->fetchAll();
?>

<div class="page-header">
  <div>
    <h1><i class="bi bi-trophy me-2 text-warning"></i>Performance Results</h1>
    <p class="text-muted mb-0">Overview for <?= h($activeChild['name']) ?></p>
  </div>
</div>

<div class="row g-4">
  <!-- Formal Assessments -->
  <div class="col-lg-6">
    <h6 class="text-uppercase fw-700 mb-3" style="font-size:.7rem;letter-spacing:.1em;color:#888;">📝 Offline Center Exam Results</h6>
    <div class="tpa-table">
      <table class="table table-hover mb-0">
        <thead><tr><th>Exam</th><th>Date</th><th>Score</th><th>%</th><th>Grade</th></tr></thead>
        <tbody>
        <?php foreach ($assessments as $r): ?>
          <tr>
            <td><div class="fw-600 small"><?= h($r['assessment_name']) ?></div><div class="text-muted" style="font-size:.7rem;"><?= h($r['subject']??'—') ?></div></td>
            <td class="small"><?= date('d M y', strtotime($r['date'])) ?></td>
            <td class="fw-700 small"><?= $r['marks']!==null?h($r['marks']).'/'.$r['max_marks']:'—' ?></td>
            <td><span class="fw-700" style="color:<?= ($r['percentage']??0)>=70?'#16a34a':(($r['percentage']??0)>=50?'#ca8a04':'#dc2626') ?>;"><?= $r['percentage']!==null?round($r['percentage']).'%':'—' ?></span></td>
            <td><?= $r['grade'] ? '<span class="badge bg-dark">'.h($r['grade']).'</span>' : '—' ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($assessments)): ?><tr><td colspan="5" class="text-center py-4 text-muted">No offline exam results recorded yet.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Quizzes -->
  <div class="col-lg-6">
    <h6 class="text-uppercase fw-700 mb-3" style="font-size:.7rem;letter-spacing:.1em;color:#888;">💻 Online Quiz Results</h6>
    <div class="tpa-table">
      <table class="table table-hover mb-0">
        <thead><tr><th>Quiz</th><th>Date completed</th><th>Score</th><th>%</th><th>Status</th></tr></thead>
        <tbody>
        <?php foreach ($quizzes as $q): ?>
          <tr>
            <td><div class="fw-600 small"><?= h($q['title']) ?></div><div class="text-muted" style="font-size:.7rem;"><?= h($q['subject_name']??'—') ?></div></td>
            <td class="small"><?= date('d M y', strtotime($q['submitted_at'])) ?></td>
            <td class="fw-700 small"><?= number_format($q['score'],1) ?>/<?= number_format($q['max_score'],1) ?></td>
            <td><span class="fw-700" style="color:<?= ($q['percentage']??0)>=70?'#16a34a':(($q['percentage']??0)>=50?'#ca8a04':'#dc2626') ?>;"><?= round($q['percentage']) ?>%</span></td>
            <td><?= $q['passed']?'<span class="badge bg-success">Pass</span>':'<span class="badge bg-danger">Fail</span>' ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($quizzes)): ?><tr><td colspan="5" class="text-center py-4 text-muted">No online quizzes completed yet.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
