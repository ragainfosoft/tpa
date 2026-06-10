<?php
// =====================================================
// TPA — Teacher Portal Homework Redirect
// =====================================================
require_once __DIR__ . '/../admin/includes/auth.php';
startSecureSession();
header('Location: ' . SITE_URL . '/homework/index.php');
exit;
