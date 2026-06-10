<?php
// =====================================================
// TPA — API: Record Online Attendance
// =====================================================
header('Content-Type: application/json');
require_once __DIR__ . '/../admin/includes/auth.php';
require_once __DIR__ . '/../admin/includes/functions.php';

startSecureSession();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$db = getDB();
$data = json_decode(file_get_contents('php://input'), true);

$classId   = (int)($data['class_id'] ?? 0);
$studentId = (int)($_SESSION['user']['student_id'] ?? 0);

if ($classId && $studentId) {
    // Check if already marked
    $check = $db->prepare("SELECT id FROM online_class_attendance WHERE online_class_id = ? AND student_id = ?");
    $check->execute([$classId, $studentId]);
    
    if (!$check->fetch()) {
        $db->prepare("INSERT INTO online_class_attendance (online_class_id, student_id, joined_at, is_present) VALUES (?, ?, NOW(), 1)")
           ->execute([$classId, $studentId]);
        
        // Also check if we should mark general attendance? 
        // For now, let's keep it specific to online_class_attendance
        
        echo json_encode(['success' => true, 'message' => 'Attendance recorded.']);
    } else {
        echo json_encode(['success' => true, 'message' => 'Already recorded.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing parameters.']);
}
