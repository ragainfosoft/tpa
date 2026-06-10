<?php
// TPA — Parent Messages (Placeholder)
require_once __DIR__ . '/includes/header.php';
?>
<div class="page-header"><h1><i class="bi bi-chat-dots me-2 text-primary"></i>Messages</h1></div>
<div class="stat-card text-center py-5">
  <i class="bi bi-envelope text-muted fs-1 d-block mb-3"></i>
  <h5 class="fw-600 text-dark">No Messages</h5>
  <p class="text-muted small">You have no new messages from the centre administration.</p>
  <a href="mailto:<?= EMAIL ?>" class="btn btn-outline-primary mt-3"><i class="bi bi-send me-1"></i>Contact Centre</a>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
