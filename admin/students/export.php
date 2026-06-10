<?php
// =====================================================
// TPA IMS — Students CSV Export
// =====================================================
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
startSecureSession();
requireRole(['admin','staff','branch_manager']);

$db      = getDB();
$search  = trim($_GET['q'] ?? '');
$fYear   = $_GET['year'] ?? '';
$fStatus = $_GET['status'] ?? '';
$fCentre = $_GET['centre'] ?? '';

$where  = '1=1';
$params = [];
if ($search)  { $where .= ' AND (s.first_name LIKE ? OR s.last_name LIKE ? OR s.student_ref LIKE ? OR p.email LIKE ? OR p.phone LIKE ?)'; $p = "%$search%"; $params = [$p,$p,$p,$p,$p]; }
if ($fYear)   { $where .= ' AND s.year_group = ?'; $params[] = $fYear; }
if ($fStatus) { $where .= ' AND s.status = ?'; $params[] = $fStatus; }
if ($fCentre) { $where .= ' AND s.centre = ?'; $params[] = $fCentre; }

$stmt = $db->prepare("
    SELECT s.student_ref, s.first_name, s.last_name, s.dob, s.year_group, s.gender,
           s.school, s.centre, s.status, s.join_date, s.notes,
           p.parent_name, p.relationship, p.email, p.phone, p.whatsapp
    FROM students s
    LEFT JOIN student_parents p ON p.student_id = s.id AND p.is_primary = 1
    WHERE $where
    GROUP BY s.id
    ORDER BY s.first_name, s.last_name ASC
");
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$filename = 'students-export-' . date('Y-m-d') . '.csv';
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');

// UTF-8 BOM for Excel compatibility
echo "\xEF\xBB\xBF";

$out = fopen('php://output', 'w');

fputcsv($out, [
    'Student Ref', 'First Name', 'Last Name', 'Date of Birth', 'Year Group',
    'Gender', 'School', 'Centre', 'Status', 'Join Date', 'Notes',
    'Parent/Guardian', 'Relationship', 'Parent Email', 'Parent Phone', 'Parent WhatsApp',
]);

foreach ($rows as $r) {
    fputcsv($out, [
        $r['student_ref'],
        $r['first_name'],
        $r['last_name'],
        $r['dob'] ?? '',
        $r['year_group'] ?? '',
        $r['gender'] ?? '',
        $r['school'] ?? '',
        $r['centre'] ?? '',
        $r['status'] ?? '',
        $r['join_date'] ?? '',
        $r['notes'] ?? '',
        $r['parent_name'] ?? '',
        $r['relationship'] ?? '',
        $r['email'] ?? '',
        $r['phone'] ?? '',
        $r['whatsapp'] ?? '',
    ]);
}

fclose($out);
logActivity('students_exported', 'CSV export: ' . count($rows) . ' students, filters: year=' . $fYear . ' status=' . $fStatus . ' centre=' . $fCentre);
exit;
