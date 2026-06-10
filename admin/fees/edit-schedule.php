<?php
// =====================================================
// TPA IMS — Handle Recurring Schedule Changes
// =====================================================

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
startSecureSession();
requireRole(['admin']);

$db = getDB();

// --- Action: Delete ---
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    verifyCsrf();
    $id = (int)$_GET['id'];
    $studentId = (int)$_GET['student_id'];
    $db->prepare('UPDATE student_payment_schedules SET is_active=0 WHERE id=? AND student_id=?')->execute([$id, $studentId]);
    setFlash('success', 'Recurring plan stopped.');
    header("Location: ../students/view.php?id=$studentId&tab=fees"); exit;
}

// --- Action: Create ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $studentId = (int)$_POST['student_id'];
    $structureId = (int)$_POST['fee_structure_id'];
    $nextDate = $_POST['next_invoice_date'];
    $method = $_POST['payment_method'] ?? 'bacs';
    $auto = isset($_POST['auto_generate']) ? 1 : 0;

    if ($studentId && $structureId && $nextDate) {
        $db->prepare('INSERT INTO student_payment_schedules (student_id, fee_structure_id, start_date, next_invoice_date, payment_method, auto_generate) VALUES (?, ?, CURDATE(), ?, ?, ?)')
           ->execute([$studentId, $structureId, $nextDate, $method, $auto]);
        setFlash('success', 'Recurring payment plan enabled.');
    } else {
        setFlash('danger', 'Please fill all required fields.');
    }
    header("Location: ../students/view.php?id=$studentId&tab=fees"); exit;
}

header('Location: ../index.php'); exit;
