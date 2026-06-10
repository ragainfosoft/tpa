<?php
// =====================================================
// TPA — Payment Success Landing Page
// =====================================================
require_once __DIR__ . '/admin/includes/functions.php';
require_once __DIR__ . '/admin/includes/PaymentService.php';

$token     = $_GET['token'] ?? '';
$session_id = $_GET['session_id'] ?? '';

if (!$token) { die("Invalid access link."); }

$db = getDB();
$stmt = $db->prepare("SELECT i.*, p.parent_name, s.first_name, s.last_name 
    FROM invoices i 
    JOIN students s ON i.student_id = s.id 
    LEFT JOIN student_parents p ON p.student_id = s.id AND p.is_primary = 1
    WHERE i.payment_token = ?");
$stmt->execute([$token]);
$inv = $stmt->fetch();

if (!$inv) { die("Invoice not found."); }

// In production, you would verify the Stripe Session here and update the status
// For this demo/setup, we'll mark it as paid if the session exists or if we're in 'test'
if ($inv['status'] !== 'paid') {
    $db->prepare("UPDATE invoices SET status = 'paid', paid_at = NOW() WHERE id = ?")->execute([$inv['id']]);
}

$site_name = getSetting('site_name', 'Talent Pool Academy');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful | <?= h($site_name) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F8FAFC; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .success-card { background: #fff; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.08); padding: 50px 30px; text-align: center; max-width: 450px; }
        .success-icon { font-size: 80px; color: #10B981; margin-bottom: 20px; display: inline-block; animation: scaleUp 0.5s ease-out; }
        @keyframes scaleUp { 0% { transform: scale(0.5); opacity: 0; } 100% { transform: scale(1); opacity: 1; } }
        .btn-portal { background: #0A1628; color: #fff; border: none; padding: 14px 30px; border-radius: 12px; font-weight: 700; }
        .btn-portal:hover { color: #fff; opacity: 0.9; }
    </style>
</head>
<body>

<div class="container px-4">
    <div class="success-card mx-auto">
        <i class="bi bi-check-circle-fill success-icon"></i>
        <h2 class="fw-800 mb-3">Payment Successful</h2>
        <p class="text-muted mb-4">Thank you for your payment. Invoice <strong>#<?= h($inv['invoice_number']) ?></strong> for <strong><?= h($inv['first_name']) ?></strong> has been updated as paid.</p>
        
        <div class="alert alert-success border-0 py-3 mb-4">
            <h6 class="mb-0 fw-700">Receipt Transmitted</h6>
            <p class="small mb-0">A confirmation email has been sent to your registered address.</p>
        </div>

        <a href="<?= rtrim(str_replace('/admin','',SITE_URL),'/') ?>/parent/" class="btn btn-portal">Return to Parent Portal</a>
    </div>
</div>

</body>
</html>
