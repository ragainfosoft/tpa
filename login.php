<?php
// =====================================================
// TPA IMS — Unified Login Landing
// =====================================================

// Include admin files for auth logic
require_once __DIR__ . '/admin/includes/auth.php';
require_once __DIR__ . '/admin/includes/functions.php';

startSecureSession();

// If already logged in, redirect to respective dashboard
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unified Login | Talent Pool Academy</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --navy: #0A1628;
            --navy-mid: #1a3a6b;
            --gold: #F5A623;
            --soft-white: #f8fafc;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--navy) 0%, #0d2240 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-container {
            width: 100%;
            max-width: 900px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: #fff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 40px 100px rgba(0,0,0,0.4);
        }
        @media (max-width: 768px) {
            .login-container { grid-template-columns: 1fr; }
            .login-info { display: none; }
        }
        .login-info {
            background: var(--navy);
            padding: 50px;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            z-index: 1;
        }
        .login-info::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: url('https://images.unsplash.com/photo-1543269865-cbf427effbad?auto=format&fit=crop&q=80&w=2670&ixlib=rb-4.0.3');
            background-size: cover;
            opacity: 0.15;
            z-index: -1;
        }
        .login-form-area {
            padding: 50px;
            background: #fff;
        }
        .logo-text {
            font-size: 1.5rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.5px;
        }
        .logo-text em {
            color: var(--gold);
            font-style: normal;
        }
        .form-control {
            border-radius: 12px;
            padding: 12px 16px;
            border: 1px solid #e2e8f0;
            background: #fdfdfd;
        }
        .form-control:focus {
            box-shadow: 0 0 0 4px rgba(245,166,35,0.1);
            border-color: var(--gold);
        }
        .btn-login {
            background: var(--navy);
            color: #fff;
            font-weight: 700;
            padding: 14px;
            border-radius: 12px;
            width: 100%;
            border: none;
            transition: all 0.2s;
            margin-top: 10px;
        }
        .btn-login:hover {
            background: var(--navy-mid);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            background: #f1f5f9;
            border-radius: 100px;
            font-size: 0.75rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 20px;
        }
        .role-badge i { color: var(--gold); }
        .info-card {
            background: rgba(255,255,255,0.05);
            padding: 20px;
            border-radius: 16px;
            border: 1px solid rgba(255,255,255,0.1);
            margin-top: 20px;
        }
        .toggle-pass { cursor: pointer; }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-info">
        <div>
            <div class="logo-text">Talent<em>Pool</em> Academy</div>
            <p class="mt-2 text-white-50" style="font-size: 0.9rem;">The premier institute management portal for students, parents, and teachers.</p>
        </div>
        
        <div>
            <div class="info-card">
                <h6 class="fw-700 text-white mb-2"><i class="bi bi-rocket-takeoff-fill me-2 text-warning"></i>Explore your portal</h6>
                <p class="small text-white-50 mb-0">Check your attendance, view assessment results, manage fee payments, and stay updated with academy announcements.</p>
            </div>
            <div class="mt-4 small text-white-50">
                &copy; <?= date('Y') ?> Talent Pool Academy. All rights reserved.
            </div>
        </div>
    </div>
    
    <div class="login-form-area">
        <div class="role-badge">
            <i class="bi bi-shield-lock-fill"></i> Secure Access Portal
        </div>
        
        <h2 class="fw-800 mb-2">Welcome back</h2>
        <p class="text-muted mb-4 small">Please enter your credentials to access your dedicated dashboard.</p>
        
        <?php if ($error): ?>
            <div class="alert alert-danger py-2 px-3 small d-flex align-items-center gap-2 border-0" style="background:#fff1f2; color:#be123c;">
                <i class="bi bi-exclamation-triangle-fill"></i> <?= h($error) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
            
            <div class="mb-3">
                <label class="form-label fw-600 small">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 12px 0 0 12px;"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" class="form-control border-start-0" placeholder="name@example.com" value="<?= h($email) ?>" style="border-radius: 0 12px 12px 0;" required autocomplete="email">
                </div>
            </div>
            
            <div class="mb-4">
                <label class="form-label fw-600 small">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 12px 0 0 12px;"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" id="password" class="form-control border-start-0 border-end-0" placeholder="••••••••" style="border-radius: 0;" required autocomplete="current-password">
                    <span class="input-group-text bg-white border-start-0 text-muted toggle-pass" style="border-radius: 0 12px 12px 0;" onclick="togglePwd()">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </span>
                </div>
                <div class="d-flex justify-content-end mt-2">
                    <a href="#" class="small text-decoration-none text-muted fw-600">Forgot password?</a>
                </div>
            </div>
            
            <button type="submit" class="btn-login">
                Sign In to Portal <i class="bi bi-arrow-right ms-2"></i>
            </button>
        </form>
        
        <div class="mt-5 text-center px-4">
            <p class="small text-muted">Trouble logging in? Please contact your branch administrator for assistance.</p>
        </div>
    </div>
</div>

<script>
function togglePwd() {
  const p = document.getElementById('password');
  const i = document.getElementById('eyeIcon');
  if (p.type === 'password') {
    p.type = 'text';
    i.classList.replace('bi-eye', 'bi-eye-slash');
  } else {
    p.type = 'password';
    i.classList.replace('bi-eye-slash', 'bi-eye');
  }
}
</script>
</body>
</html>
