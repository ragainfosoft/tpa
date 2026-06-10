<?php
// =====================================================
// TPA IMS — Automated Recurring Invoice Generation
// =====================================================
// This script should be run periodically (e.g. daily cron job)
// It can also be triggered manually from the Fees dashboard.

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// If run from web, require Admin. If run from CLI, skip auth.
if (php_sapi_name() !== 'cli') {
    startSecureSession();
    requireRole(['admin']);
}

$db = getDB();
$today = date('Y-m-d');

// 1. Fetch active schedules due today or earlier
$stmt = $db->prepare("
    SELECT sps.*, fs.name as plan_name, fs.amount, fs.frequency, fs.description as plan_desc,
           s.first_name, s.last_name, s.student_ref
    FROM student_payment_schedules sps
    JOIN fee_structures fs ON fs.id = sps.fee_structure_id
    JOIN students s ON s.id = sps.student_id
    WHERE sps.is_active = 1 
      AND sps.auto_generate = 1
      AND sps.next_invoice_date <= ?
");
$stmt->execute([$today]);
$schedules = $stmt->fetchAll();

$generated = 0;
$logs = [];

foreach ($schedules as $sch) {
    try {
        $db->beginTransaction();

        // 2. Prepare Invoice Data
        $invoiceNumber = generateInvoiceNumber(); // This helper also increments the counter in settings
        $amount     = (float)$sch['amount'];
        $dueDate    = date('Y-m-d', strtotime('+7 days')); // Default 7 days due date
        $period     = date('F Y', strtotime($sch['next_invoice_date'])); // e.g. "April 2026"
        
        // 3. Insert Invoice
        $ins = $db->prepare("
            INSERT INTO invoices (
                invoice_number, student_id, schedule_id, amount, amount_due, 
                period_label, due_date, status, notes, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, 'unpaid', ?, NOW())
        ");
        
        $notes = "Automated invoice for " . $sch['plan_name'] . " (" . $sch['frequency'] . ")";
        $ins->execute([
            $invoiceNumber,
            $sch['student_id'],
            $sch['id'],
            $amount,
            $amount,
            $period,
            $dueDate,
            $notes
        ]);

        // 4. Calculate Next Invoice Date
        $currentDate = new DateTime($sch['next_invoice_date']);
        switch ($sch['frequency']) {
            case 'weekly':      $currentDate->modify('+1 week'); break;
            case 'fortnightly':  $currentDate->modify('+2 weeks'); break;
            case 'monthly':     $currentDate->modify('+1 month'); break;
            case 'half_termly': $currentDate->modify('+6 weeks'); break;
            case 'termly':      $currentDate->modify('+4 months'); break;
            case 'annual':      $currentDate->modify('+1 year'); break;
            default:            $currentDate->modify('+1 month'); break;
        }
        $nextDate = $currentDate->format('Y-m-d');

        // 5. Update Schedule
        $db->prepare("UPDATE student_payment_schedules SET next_invoice_date = ? WHERE id = ?")
           ->execute([$nextDate, $sch['id']]);

        $db->commit();
        $generated++;
        $logs[] = "Generated {$invoiceNumber} for {$sch['first_name']} {$sch['last_name']}";

    } catch (Exception $e) {
        $db->rollBack();
        $logs[] = "ERROR for Student ID {$sch['student_id']}: " . $e->getMessage();
    }
}

// Result Reporting
if (php_sapi_name() === 'cli') {
    echo "Processing complete. Generated: {$generated}\n";
    foreach ($logs as $l) echo "- $l\n";
} else {
    $page_title = "Processing Recurring Fees";
    require_once __DIR__ . '/../includes/header.php';
    ?>
    <div class="page-header">
        <h1><i class="bi bi-gear-wide-connected me-2"></i>Automation Engine</h1>
    </div>
    <div class="stat-card">
        <h5 class="fw-700">Recurring Invoice Processing</h5>
        <div class="alert alert-success">Successfully processed <strong><?= $generated ?></strong> plans.</div>
        <div class="bg-light p-3 rounded font-monospace small" style="max-height: 300px; overflow-y: auto;">
            <?php foreach ($logs as $l): ?>
                <div class="mb-1 border-bottom pb-1"><?= h($l) ?></div>
            <?php endforeach; ?>
            <?php if (empty($logs)): ?>No schedules due for processing today.<?php endif; ?>
        </div>
        <div class="mt-4">
            <a href="../fees/index.php" class="btn btn-dark">Back to Fees</a>
        </div>
    </div>
    <?php
    require_once __DIR__ . '/../includes/footer.php';
}
