<?php
// Config
define('SITE_NAME', 'Talent Pool Academy');
define('SITE_URL', str_contains($_SERVER['HTTP_HOST'] ?? 'localhost', 'localhost') ? '/tpaAG' : '');
define('SITE_CANONICAL', 'https://www.talentpoolacademy.com');
define('PHONE', '07772 922943');
define('WHATSAPP', '447772922943');
define('EMAIL', 'enquiry@talentpoolacademy.com');

// Helper: active page
function isActive($page) {
    $current = basename($_SERVER['PHP_SELF']);
    return ($current === $page) ? 'active' : '';
}
?>
