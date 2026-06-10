<?php
// TPA — Parent Profile
require_once __DIR__ . '/includes/header.php';
?>
<div class="page-header"><h1><i class="bi bi-person me-2 text-gold"></i>My Profile</h1></div>
<div class="row g-4">
  <div class="col-md-6">
    <div class="stat-card">
      <h5 class="fw-700 mb-3 border-bottom pb-2">Account Details</h5>
      <table class="table table-sm mb-0">
        <tr><th class="text-muted fw-400 border-0">Name</th><td class="fw-600 border-0"><?= h($currentUser['name']) ?></td></tr>
        <tr><th class="text-muted fw-400">Email</th><td><?= h($currentUser['email']) ?></td></tr>
        <tr><th class="text-muted fw-400">Phone</th><td><?= h($currentUser['phone'] ?? '—') ?></td></tr>
      </table>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
