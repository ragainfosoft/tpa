<?php
// =====================================================
// TPA Contact Form → Auto Lead API
// Called from website contact form via AJAX POST
// =====================================================

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Load admin config
define('ADMIN_PATH', __DIR__ . '/../admin');
require_once __DIR__ . '/../admin/includes/config.php';
require_once __DIR__ . '/../admin/includes/db.php';
require_once __DIR__ . '/../admin/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success'=>false,'error'=>'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

// Name: accept parent name, legacy name field, or fall back to child_name
$childName = trim($input['child_name'] ?? '');
$name      = trim($input['name'] ?? $input['parent_name'] ?? '');
if (!$name) $name = $childName; // no parent name submitted — use child name as lead identifier

$email     = trim($input['email']     ?? '');
$phone     = trim($input['phone']     ?? '');
$childYear = trim($input['year_group'] ?? $input['child_year'] ?? '');

// Build course_interest: prefer 'subject', append subject_detail if different
$courseInt  = trim($input['subject'] ?? $input['course_interest'] ?? $input['course'] ?? '');
$subjDetail = trim($input['subject_detail'] ?? '');
if ($subjDetail && $subjDetail !== $courseInt) {
    $courseInt = $courseInt ? $courseInt . ' — ' . $subjDetail : $subjDetail;
}

// Build notes: merge message/notes + any extra context
$notes = trim($input['notes'] ?? $input['message'] ?? '');

$centre    = trim($input['centre']    ?? '');
$rawSource = trim($input['source']    ?? $input['hear'] ?? 'Website');

// Normalise source to DB enum values
$sourceMap = [
    'google'   => 'Google Search',
    'word'     => 'Word of Mouth',
    'mouth'    => 'Word of Mouth',
    'referral' => 'Word of Mouth',
    'social'   => 'Social Media',
    'flyer'    => 'Flyer',
    'leaflet'  => 'Flyer',
    'other'    => 'Other',
];
$sourceFinal = 'Website';
foreach ($sourceMap as $key => $val) {
    if (str_contains(strtolower($rawSource), $key)) { $sourceFinal = $val; break; }
}
if (str_starts_with($rawSource, 'Website')) $sourceFinal = 'Website';

// Normalise centre to DB values
$centreNorm = 'No preference';
$cl = strtolower($centre);
if (str_contains($cl, 'romford') || str_contains($cl, 'chadwell')) $centreNorm = 'Chadwell Heath';
elseif (str_contains($cl, 'chelmsford'))                            $centreNorm = 'Chelmsford';
elseif (str_contains($cl, 'online'))                                $centreNorm = 'Online';
elseif (str_contains($cl, 'parkwood'))                              $centreNorm = 'Parkwood Academy';
elseif ($centre && $centre !== 'No preference')                     $centreNorm = $centre; // pass-through for future partner schools
$centre = $centreNorm;

// Validation — name now optional (child_name fallback applied above); email or phone required
$errors = [];
if (!$email && !$phone)        $errors[] = 'Email or phone is required';
if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address';

if ($errors) {
    http_response_code(422);
    echo json_encode(['success'=>false,'errors'=>$errors,'error'=>implode('. ', $errors)]);
    exit;
}

try {
    $db = getDB();

    // Check for duplicate
    $dup = $db->prepare('SELECT id FROM leads WHERE (phone=? AND phone!="") OR (email=? AND email!="") LIMIT 1');
    $dup->execute([$phone, $email]);
    $existing = $dup->fetch();

    if ($existing) {
        // Add a follow-up note instead of creating duplicate
        $db->prepare('INSERT INTO lead_followups (lead_id, user_id, type, notes, outcome) VALUES (?,0,"other",?,"Website enquiry")')
           ->execute([$existing['id'], "Follow-up message received:\n{$notes}"]);
        echo json_encode(['success'=>true,'duplicate'=>true,'lead_id'=>$existing['id']]);
        exit;
    }

    $db->prepare('INSERT INTO leads (name,email,phone,whatsapp,child_name,child_year,course_interest,centre,source,notes,status) VALUES (?,?,?,?,?,?,?,?,?,?,?)')
       ->execute([$name,$email,$phone,$phone,$childName,$childYear,$courseInt,$centre,$sourceFinal,$notes,'new']);
    $leadId = $db->lastInsertId();

    // Optional: fire WhatsApp welcome
    $waTemplate = getSetting('wa_template_new_lead') ?? '';
    if ($waTemplate && $phone) {
        require_once __DIR__ . '/../admin/includes/WhatsAppService.php';
        $wa  = new WhatsAppService();
        $msg = str_replace(['{parent_name}','{child_name}','{course}'],
                           [$name, $childName ?: 'your child', $courseInt ?: 'our courses'],
                           $waTemplate);
        $wa->sendText($phone, $msg);
    }

    echo json_encode(['success'=>true,'lead_id'=>$leadId]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>'Server error — please try again']);
}
