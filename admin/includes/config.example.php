<?php
// =====================================================
// TPA IMS — Database Configuration TEMPLATE
// Copy this file to config.php and fill in real values.
// NEVER commit config.php — it is in .gitignore.
// =====================================================

define('DB_HOST',    'localhost');
define('DB_NAME',    'your_database_name');
define('DB_USER',    'your_db_user');
define('DB_PASS',    'your_db_password');
define('DB_CHARSET', 'utf8mb4');

// Site URLs — no trailing slash
define('SITE_NAME',   'TPA Admin');
define('SITE_URL',    'https://www.talentpoolacademy.com');
define('PARENT_URL',  'https://www.talentpoolacademy.com/parent');

// Session
define('SESSION_LIFETIME', 3600 * 8); // 8 hours
define('SESSION_NAME',     'tpa_session');

// Timezone
date_default_timezone_set('Europe/London');

// Error reporting — set to 0 in production
error_reporting(0);
ini_set('display_errors', 0);
