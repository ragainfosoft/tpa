<?php
// TPA — Student Profile
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header"><h1><i class="bi bi-person me-2 text-gold"></i>My Profile</h1></div>
<div class="row g-4">
  <div class="col-md-6">
    <div class="stat-card">
      <h5 class="fw-700 mb-3 border-bottom pb-2">Personal Details</h5>
      <table class="table table-sm mb-0">
        <tr><th class="text-muted fw-400 border-0">Name</th><td class="fw-600 border-0"><?= h($student['first_name'].' '.$student['last_name']) ?></td></tr>
        <tr><th class="text-muted fw-400">Student Ref</th><td class="fw-600"><?= h($student['student_ref']) ?></td></tr>
        <tr><th class="text-muted fw-400">Year Group</th><td><?= h($student['year_group'] ?? '—') ?></td></tr>
        <tr><th class="text-muted fw-400">Date of Birth</th><td><?= $student['dob'] ? date('d M Y', strtotime($student['dob'])) : '—' ?></td></tr>
        <tr><th class="text-muted fw-400">School</th><td><?= h($student['school'] ?? '—') ?></td></tr>
      </table>
    </div>
  </div>
  <div class="col-md-6">
    <div class="stat-card">
      <h5 class="fw-700 mb-3 border-bottom pb-2">Account Security</h5>
      <p class="small text-muted mb-3">If you need to change your portal password, please ask your parent/guardian or your centre manager.</p>
      <div class="alert alert-info py-2 small mb-0"><i class="bi bi-shield-lock me-2"></i>Account Active</div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
