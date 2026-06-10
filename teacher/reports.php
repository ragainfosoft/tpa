<?php
// TPA — Teacher Reports Redirect
require_once __DIR__ . '/../admin/includes/auth.php';
startSecureSession();
header('Location: ' . SITE_URL . '/reports/index.php');
exit;
