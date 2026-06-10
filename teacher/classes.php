<?php
// =====================================================
// TPA — Teacher Portal Classes Redirect
// =====================================================
require_once __DIR__ . '/../admin/includes/auth.php';
startSecureSession();
header('Location: ' . SITE_URL . '/classes/index.php');
exit;
