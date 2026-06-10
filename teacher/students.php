<?php
// TPA — Teacher Students Roster
require_once __DIR__ . '/includes/header.php';

$students = $db->prepare("SELECT DISTINCT s.id, s.first_name, s.last_name, s.student_ref, s.year_group 
    FROM students s 
    JOIN batch_students bs ON bs.student_id=s.id 
    JOIN batches b ON b.id=bs.batch_id 
    WHERE b.teacher_id=? AND b.is_active=1 AND bs.is_active=1 
    ORDER BY s.first_name");
$students->execute([$teacherId]); $students = $students->fetchAll();
?>
<div class="page-header"><h1><i class="bi bi-person-lines-fill me-2 text-purple"></i>My Students</h1></div>
<div class="tpa-table">
  <table class="table table-hover mb-0 dt-table">
    <thead><tr><th>Name</th><th>Student Ref</th><th>Year Group</th></tr></thead>
    <tbody>
      <?php foreach ($students as $s): ?>
      <tr>
        <td class="fw-600"><?= h($s['first_name'].' '.$s['last_name']) ?></td>
        <td class="small text-muted"><?= h($s['student_ref']) ?></td>
        <td><?= h($s['year_group'] ?? '—') ?></td>
      </tr>
      <?php endforeach; ?>
      <?php if(empty($students)): ?><tr><td colspan="3" class="text-center py-4 text-muted">No students assigned to your batches.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
