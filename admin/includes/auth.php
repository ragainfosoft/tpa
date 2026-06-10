<?php
// =====================================================
// TPA IMS — Authentication & Session Helper v2
// Supports: admin, branch_manager, teacher, student, parent
// =====================================================

require_once __DIR__ . '/db.php';

function startSecureSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_name(SESSION_NAME);
        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path'     => '/',
            'secure'   => false, // set true in production (HTTPS)
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }
}

function isLoggedIn(): bool {
    startSecureSession();
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

function requireLogin(string $redirect = ''): void {
    if (!isLoggedIn()) {
        $url = $redirect ?: SITE_URL . '/login.php';
        header('Location: ' . $url);
        exit;
    }
}

function requireRole(array $roles): void {
    requireLogin();
    if (!in_array($_SESSION['role'], $roles, true)) {
        header('Location: ' . roleDashboardUrl(currentRole()) . '?error=access_denied');
        exit;
    }
}

function roleDashboardUrl(string $role): string {
    $map = [
        'admin'          => SITE_URL . '/index.php',
        'branch_manager' => SITE_URL . '/index.php',
        'teacher'        => defined('TEACHER_URL') ? TEACHER_URL . '/index.php' : '/tpaAG/teacher/index.php',
        'student'        => defined('STUDENT_URL') ? STUDENT_URL . '/index.php' : '/tpaAG/student/index.php',
        'parent'         => defined('PARENT_URL')  ? PARENT_URL  . '/index.php' : '/tpaAG/parent/index.php',
    ];
    return $map[$role] ?? SITE_URL . '/index.php';
}

function currentUser(): array {
    return $_SESSION['user'] ?? [];
}

function currentUserId(): int {
    return (int) ($_SESSION['user_id'] ?? 0);
}

function currentRole(): string {
    return $_SESSION['role'] ?? '';
}

function currentBranchId(): ?int {
    $b = $_SESSION['user']['branch_id'] ?? null;
    return $b ? (int)$b : null;
}

function isAdmin(): bool   { return currentRole() === 'admin'; }
function isBranchManager(): bool { return in_array(currentRole(), ['admin','branch_manager']); }
function isTeacher(): bool { return in_array(currentRole(), ['admin','branch_manager','teacher']); }
function isStaff(): bool   { return in_array(currentRole(), ['admin','branch_manager','teacher','staff']); }

function loginUser(string $email, string $password): array {
    $db = getDB();
    $stmt = $db->prepare('SELECT * FROM users WHERE email = ? AND is_active = 1 LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        return ['success' => false, 'message' => 'Invalid email or password.'];
    }

    startSecureSession();
    session_regenerate_id(true);

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role']    = $user['role'];
    $_SESSION['user']    = [
        'id'         => $user['id'],
        'name'       => $user['name'],
        'email'      => $user['email'],
        'role'       => $user['role'],
        'branch_id'  => $user['branch_id'] ?? null,
        'student_id' => $user['student_id'] ?? null,
    ];

    $db->prepare('UPDATE users SET last_login = NOW() WHERE id = ?')->execute([$user['id']]);

    return ['success' => true, 'role' => $user['role'], 'dashboard' => roleDashboardUrl($user['role'])];
}

function logoutUser(): void {
    startSecureSession();
    session_unset();
    session_destroy();
}

function csrfToken(): string {
    startSecureSession();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf(bool $fatal = true): bool {
    $token = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    $valid = hash_equals($_SESSION['csrf_token'] ?? '', $token) && $token !== '';
    if (!$valid && $fatal) {
        http_response_code(403);
        die('CSRF token mismatch.');
    }
    return $valid;
}

function logActivity(string $action, string $description = ''): void {
    try {
        $db = getDB();
        $db->prepare('INSERT INTO activity_log (user_id, action, description, ip_address) VALUES (?,?,?,?)')
           ->execute([currentUserId(), $action, $description, $_SERVER['REMOTE_ADDR'] ?? '']);
    } catch (Exception $e) { /* fail silently */ }
}
