<?php
// =====================================================
// TPA — Public Payment Page (Guest Access)
// =====================================================
require_once __DIR__ . '/admin/includes/functions.php';
require_once __DIR__ . '/admin/includes/PaymentService.php';

$token  = $_GET['token'] ?? '';
$action = $_POST['action'] ?? '';

if (!$token) { die("Invalid access link."); }

$db = getDB();
$stmt = $db->prepare("SELECT i.*, s.first_name, s.last_name, 
    p.parent_name, p.email as parent_email 
    FROM invoices i 
    JOIN students s ON i.student_id = s.id 
    LEFT JOIN student_parents p ON p.student_id = s.id AND p.is_primary = 1
    WHERE i.payment_token = ?");
$stmt->execute([$token]);
$inv = $stmt->fetch();

if (!$inv) { die("Invoice not found or link expired."); }
if ($inv['status'] === 'paid') {
    $alreadyPaid = true;
} else {
    $alreadyPaid = false;
}

// ── Handle Payment Redirect ──────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$alreadyPaid) {
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
    $host     = $_SERVER['HTTP_HOST'];
    $uri      = str_replace('pay-invoice.php', '', $_SERVER['REQUEST_URI']);
    $baseUrl  = "$protocol://$host$uri";

    if ($action === 'stripe') {
        $successUrl = $baseUrl . "payment-success.php?token=" . $token;
        $cancelUrl  = $baseUrl . "pay-invoice.php?token=" . $token . "&status=cancelled";
        
        $sessionUrl = PaymentService::createStripeSession($inv, $inv, $successUrl, $cancelUrl);
        if ($sessionUrl) {
            header("Location: " . $sessionUrl);
            exit;
        } else {
            $error = "Unable to start Stripe checkout. Please ensure API keys are configured.";
        }
    }
}

$site_name = getSetting('site_name', 'Talent Pool Academy');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Payment | <?= h($site_name) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --navy: #0A1628; --gold: #C5A059; --light-bg: #F8FAFC; }
        body { font-family: 'Inter', sans-serif; background-color: var(--light-bg); color: #334155; }
        .payment-card { max-width: 500px; margin: 60px auto; background: #fff; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: none; overflow: hidden; }
        .card-header-custom { background: var(--navy); color: #fff; padding: 40px 30px; text-align: center; border: none; }
        .gold-line { height: 3px; background: var(--gold); width: 80px; margin: 15px auto; }
        .amount-display { font-size: 42px; font-weight: 800; color: var(--navy); margin: 20px 0; }
        .invoice-details { background: #f1f5f9; border-radius: 12px; padding: 20px; margin-bottom: 30px; }
        .btn-stripe { background: #6366F1; color: #fff; border: none; padding: 14px; font-weight: 700; border-radius: 10px; width: 100%; transition: all 0.3s; margin-bottom: 12px; }
        .btn-stripe:hover { background: #4F46E5; transform: translateY(-2px); color: #fff; }
        .btn-gocardless { background: #0EA5E9; color: #fff; border: none; padding: 14px; font-weight: 700; border-radius: 10px; width: 100%; transition: all 0.3s; }
        .btn-gocardless:hover { background: #0284C7; transform: translateY(-2px); color: #fff; }
        .footer-note { font-size: 12px; color: #94A3B8; text-align: center; margin-top: 30px; line-height: 1.6; }
        .paid-badge { background: #DCFCE7; color: #166534; padding: 10px 20px; border-radius: 50px; display: inline-block; font-weight: 700; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="container px-4">
    <div class="payment-card">
        <div class="card-header-custom">
            <h5 class="mb-0 fw-600"><?= h($site_name) ?></h5>
            <div class="gold-line"></div>
            <p class="mb-0 opacity-75">Secure Online Payment</p>
        </div>
        
        <div class="card-body p-4 p-md-5 text-center">
            <?php if ($alreadyPaid): ?>
                <div class="paid-badge"><i class="bi bi-check-circle-fill me-2"></i>Invoice Paid</div>
                <h3>Thank You!</h3>
                <p>This invoice (#<?= h($inv['invoice_number']) ?>) has already been cleared.</p>
                <a href="<?= rtrim(str_replace('/admin','',SITE_URL),'/') ?>/parent/" class="btn btn-outline-secondary mt-3">Go to Dashboard</a>
            <?php else: ?>
                <div class="text-muted small fw-600 text-uppercase letter-spacing-wide">Amount Due</div>
                <div class="amount-display">£<?= number_format($inv['amount_due'], 2) ?></div>
                
                <div class="invoice-details text-start">
                    <div class="row mb-2">
                        <div class="col-5 text-muted small">Invoice #</div>
                        <div class="col-7 fw-600 small"><?= h($inv['invoice_number']) ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted small">Student</div>
                        <div class="col-7 fw-600 small"><?= h($inv['first_name'] . ' ' . $inv['last_name']) ?></div>
                    </div>
                    <?php if ($inv['period_label']): ?>
                    <div class="row">
                        <div class="col-5 text-muted small">Period</div>
                        <div class="col-7 fw-600 small"><?= h($inv['period_label']) ?></div>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger small py-2"><?= $error ?></div>
                <?php endif; ?>

                <?php if (isset($_GET['status']) && $_GET['status'] === 'cancelled'): ?>
                    <div class="alert alert-warning small py-2">Payment was cancelled. You can try again below.</div>
                <?php endif; ?>

                <form method="POST">
                    <button type="submit" name="action" value="stripe" class="btn btn-stripe">
                        <i class="bi bi-credit-card me-2"></i>Pay with Card
                    </button>
                    <!-- <button type="submit" name="action" value="gocardless" class="btn btn-gocardless">
                        <i class="bi bi-bank me-2"></i>Pay via Direct Debit
                    </button> -->
                </form>

                <div class="mt-4 pt-4 border-top">
                    <p class="small text-muted mb-0">Other Payment Options</p>
                    <p class="small fw-600 mb-0">Bank Transfer (BACS)</p>
                    <p class="small text-muted mb-0"><?= h(getSetting('bank_name')) ?> | Acc: <?= h(getSetting('bank_account')) ?> | Sort: <?= h(getSetting('bank_sort_code')) ?></p>
                </div>
            <?php endif; ?>
            
            <div class="footer-note">
                <i class="bi bi-shield-lock-fill text-success me-1"></i> SSL Secured & Encrypted Processing<br>
                By paying online you agree to our Terms of Service.
            </div>
        </div>
    </div>
</div>

</body>
</html>
