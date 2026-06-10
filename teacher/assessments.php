<?php
// =====================================================
// TPA — Teacher Portal Assessments Redirect
// =====================================================
require_once __DIR__ . '/../admin/includes/auth.php';
startSecureSession();
header('Location: ' . SITE_URL . '/assessments/index.php');
exit;
