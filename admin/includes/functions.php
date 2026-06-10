<?php
// =====================================================
// TPA IMS — Global Helper Functions
// =====================================================

require_once __DIR__ . '/db.php';
if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
}

// ── Formatting ──────────────────────────────────────

function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

function formatMoney($amount): string { return '£' . number_format((float)$amount, 2); }

function formatDate(?string $date, string $format = 'd M Y'): string {
    if (!$date) return '—';
    return date($format, strtotime($date));
}

function formatDateTime(?string $dt): string {
    if (!$dt) return '—';
    return date('d M Y H:i', strtotime($dt));
}

function timeAgo(string $datetime): string {
    $diff = time() - strtotime($datetime);
    if ($diff < 60) return 'just now';
    if ($diff < 3600) return floor($diff/60) . 'm ago';
    if ($diff < 86400) return floor($diff/3600) . 'h ago';
    return floor($diff/86400) . 'd ago';
}

// ── Student Ref Generator ───────────────────────────

function generateStudentRef(): string {
    $db = getDB();
    $val = (int) getSetting('student_ref_next');
    $db->prepare('UPDATE settings SET value = ? WHERE `key` = ?')
       ->execute([$val + 1, 'student_ref_next']);
    return getSetting('student_ref_prefix') . '-'. str_pad($val, 4, '0', STR_PAD_LEFT);
}

function generateInvoiceNumber(): string {
    $db = getDB();
    $val = (int) getSetting('invoice_next_number');
    $db->prepare('UPDATE settings SET value = ? WHERE `key` = ?')
       ->execute([$val + 1, 'invoice_next_number']);
    return getSetting('invoice_prefix') . '-' . date('Y') . '-' . str_pad($val, 4, '0', STR_PAD_LEFT);
}

// ── Settings ────────────────────────────────────────

function getSetting(string $key, string $default = ''): string {
    static $cache = [];
    if (!isset($cache[$key])) {
        $db = getDB();
        $stmt = $db->prepare('SELECT value FROM settings WHERE `key` = ? LIMIT 1');
        $stmt->execute([$key]);
        $row = $stmt->fetch();
        $cache[$key] = $row ? (string)$row['value'] : $default;
    }
    return $cache[$key];
}

// ── Status Badges ───────────────────────────────────

function leadStatusBadge(string $status): string {
    $map = [
        'new'               => 'badge bg-info text-dark',
        'contacted'         => 'badge bg-primary',
        'follow_up'         => 'badge bg-warning text-dark',
        'assessment_booked' => 'badge bg-purple text-white',
        'enrolled'          => 'badge bg-success',
        'lost'              => 'badge bg-secondary',
    ];
    $class = $map[$status] ?? 'badge bg-secondary';
    return '<span class="' . $class . '">' . h(str_replace('_', ' ', ucfirst($status))) . '</span>';
}

function invoiceStatusBadge(string $status): string {
    $map = [
        'draft'     => 'badge bg-light text-dark border',
        'unpaid'    => 'badge bg-warning text-dark',
        'partial'   => 'badge bg-info text-dark',
        'paid'      => 'badge bg-success',
        'overdue'   => 'badge bg-danger',
        'cancelled' => 'badge bg-secondary',
        'refunded'  => 'badge bg-dark',
    ];
    $class = $map[$status] ?? 'badge bg-secondary';
    return '<span class="' . $class . '">' . h(ucfirst($status)) . '</span>';
}

function attendanceBadge(string $status): string {
    $map = [
        'present' => 'badge bg-success',
        'absent'  => 'badge bg-danger',
        'late'    => 'badge bg-warning text-dark',
        'excused' => 'badge bg-info text-dark',
    ];
    $class = $map[$status] ?? 'badge bg-secondary';
    return '<span class="' . $class . '">' . h(ucfirst($status)) . '</span>';
}

function studentStatusBadge(string $status): string {
    $map = [
        'active'    => 'badge bg-success',
        'inactive'  => 'badge bg-secondary',
        'suspended' => 'badge bg-warning text-dark',
        'left'      => 'badge bg-dark',
    ];
    $class = $map[$status] ?? 'badge bg-secondary';
    return '<span class="' . $class . '">' . h(ucfirst($status)) . '</span>';
}

// ── UK Grade Calculator ─────────────────────────────

function calculateGrade(float $percentage): string {
    if ($percentage >= 90) return 'A*';
    if ($percentage >= 80) return 'A';
    if ($percentage >= 70) return 'B';
    if ($percentage >= 60) return 'C';
    if ($percentage >= 50) return 'D';
    if ($percentage >= 40) return 'E';
    return 'F';
}

// ── Flash Messages ──────────────────────────────────

function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array {
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function renderFlash(): string {
    $flash = getFlash();
    if (!$flash) return '';
    $icons = ['success' => 'check-circle', 'danger' => 'x-circle', 'warning' => 'exclamation-triangle', 'info' => 'info-circle'];
    $icon  = $icons[$flash['type']] ?? 'info-circle';
    return '<div class="alert alert-' . h($flash['type']) . ' alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
        <i class="bi bi-' . $icon . '-fill"></i>' . h($flash['message']) . '
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>';
}

// ── Pagination ──────────────────────────────────────

function paginate(int $total, int $page, int $perPage, string $url): string {
    $totalPages = (int) ceil($total / $perPage);
    if ($totalPages <= 1) return '';
    $out = '<nav><ul class="pagination pagination-sm mb-0">';
    for ($i = 1; $i <= $totalPages; $i++) {
        $active = $i === $page ? ' active' : '';
        $sep = strpos($url, '?') !== false ? '&' : '?';
        $out .= '<li class="page-item' . $active . '"><a class="page-link" href="' . h($url . $sep . 'page=' . $i) . '">' . $i . '</a></li>';
    }
    $out .= '</ul></nav>';
    return $out;
}

// ── WhatsApp deep link ──────────────────────────────

function waLink(string $number, string $message = ''): string {
    $number  = preg_replace('/\D/', '', $number);
    if (strlen($number) === 11 && $number[0] === '0') $number = '44' . substr($number, 1);
    $encoded = urlencode($message);
    return 'https://wa.me/' . $number . ($message ? '?text=' . $encoded : '');
}
