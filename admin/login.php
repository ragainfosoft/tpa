<?php
// =====================================================
// TPA IMS — Login Page
// =====================================================

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

startSecureSession();

if (isLoggedIn()) {
    header('Location: ' . roleDashboardUrl(currentRole()));
    exit;
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $result   = loginUser($email, $password);

    if ($result['success']) {
        header('Location: ' . $result['dashboard']);
        exit;
    } else {
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login | TPA Institute Management</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <style>
    :root { --navy:#0A1628; --gold:#F5A623; }
    body { background: linear-gradient(135deg, #0A1628 0%, #1a3a6b 60%, #0d2240 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
    .login-card { background: #fff; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,.35); width: 100%; max-width: 420px; overflow: hidden; }
    .login-header { background: var(--navy); padding: 32px 36px 24px; text-align: center; }
    .login-header h1 { color: #fff; font-size: 1.5rem; font-weight: 900; margin-bottom: 4px; }
    .login-header h1 em { color: var(--gold); font-style: normal; }
    .login-header p  { color: rgba(255,255,255,.5); font-size: .8rem; margin: 0; }
    .login-body { padding: 32px 36px; }
    .form-floating label { font-size: .875rem; }
    .btn-login { background: var(--navy); color: #fff; border: none; padding: 12px; font-weight: 700; border-radius: 10px; width: 100%; font-size: .95rem; transition: background .2s; }
    .btn-login:hover { background: #1a3a6b; color: #fff; }
    .divider-gold { height: 3px; background: var(--gold); width: 40px; margin: 12px auto 0; border-radius: 2px; }
    .toggle-pass { cursor: pointer; }
  </style>
</head>
<body>
<div class="login-card">
  <div class="login-header">
    <h1>Talent<em>Pool</em> Academy</h1>
    <div class="divider-gold"></div>
    <p class="mt-3">Institute Management System</p>
  </div>
  <div class="login-body">
    <h5 class="fw-700 mb-1" style="color:#0A1628;">Welcome back</h5>
    <p class="text-muted small mb-4">Sign in to your account to continue.</p>

    <?php if ($error): ?>
      <div class="alert alert-danger d-flex align-items-center gap-2 py-2 small">
        <i class="bi bi-exclamation-circle-fill"></i><?= h($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" autocomplete="on" novalidate>
      <div class="form-floating mb-3">
        <input type="email" class="form-control" id="email" name="email"
               placeholder="you@example.com" value="<?= h($email) ?>" required autocomplete="email">
        <label for="email"><i class="bi bi-envelope me-1"></i>Email address</label>
      </div>
      <div class="form-floating mb-4 position-relative">
        <input type="password" class="form-control" id="password" name="password"
               placeholder="Password" required autocomplete="current-password">
        <label for="password"><i class="bi bi-lock me-1"></i>Password</label>
        <span class="position-absolute top-50 end-0 translate-middle-y me-3 toggle-pass text-muted" onclick="togglePwd()">
          <i class="bi bi-eye" id="eyeIcon"></i>
        </span>
      </div>
      <button type="submit" class="btn-login">
        <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
      </button>
    </form>

    <p class="text-center mt-4 mb-0 small text-muted">
      &copy; <?= date('Y') ?> Talent Pool Academy &middot; All rights reserved
    </p>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePwd() {
  const p = document.getElementById('password');
  const i = document.getElementById('eyeIcon');
  if (p.type === 'password') { p.type = 'text'; i.className = 'bi bi-eye-slash'; }
  else                       { p.type = 'password'; i.className = 'bi bi-eye'; }
}
</script>
</body>
</html>
