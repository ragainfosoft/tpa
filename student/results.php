<?php
// TPA — Student Results
require_once __DIR__ . '/includes/header.php';

// Assessments
$assessments = $db->prepare("SELECT ar.marks_obtained, a.max_marks, a.name as assessment_name, a.date, a.type 
    FROM assessment_results ar JOIN assessments a ON a.id=ar.assessment_id 
    WHERE ar.student_id=? ORDER BY a.date DESC LIMIT 15");
$assessments->execute([$studentId]); $assessments = $assessments->fetchAll();

// Quizzes
$quizzes = $db->prepare("SELECT qa.score, qa.max_score, qa.percentage, qa.passed, qa.submitted_at, qs.title as quiz_name 
    FROM quiz_attempts qa JOIN quiz_sets qs ON qs.id=qa.quiz_id 
    WHERE qa.student_id=? AND qa.status='submitted' ORDER BY qa.submitted_at DESC LIMIT 15");
$quizzes->execute([$studentId]); $quizzes = $quizzes->fetchAll();
?>

<div class="page-header"><h1><i class="bi bi-trophy me-2 text-warning"></i>My Results</h1></div>

<div class="row g-4">
  <div class="col-md-6">
    <div class="stat-card p-0">
      <div class="p-3 border-bottom fw-700">Exam & Classwork Assessments</div>
      <table class="table mb-0">
        <tbody>
          <?php foreach ($assessments as $a): ?>
            <tr>
              <td><div class="fw-600"><?= h($a['assessment_name']) ?></div><div class="small text-muted"><?= date('d M Y', strtotime($a['date'])) ?></div></td>
              <td class="text-end fw-700"><?= $a['marks_obtained'] ?> / <?= $a['max_marks'] ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($assessments)): ?><tr><td colspan="2" class="text-center py-4 text-muted small">No assessment results.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  
  <div class="col-md-6">
    <div class="stat-card p-0">
      <div class="p-3 border-bottom fw-700">Online Quizzes</div>
      <table class="table mb-0">
        <tbody>
          <?php foreach ($quizzes as $q): ?>
            <tr>
              <td><div class="fw-600"><?= h($q['quiz_name']) ?></div><div class="small text-muted"><?= date('d M Y', strtotime($q['submitted_at'])) ?></div></td>
              <td class="text-end">
                <span class="badge <?= $q['passed']?'bg-success':'bg-danger' ?>"><?= $q['percentage'] ?>%</span>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($quizzes)): ?><tr><td colspan="2" class="text-center py-4 text-muted small">No quiz results.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
