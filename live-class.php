<?php
// =====================================================
// TPA — Live Class Container (Jitsi Meet)
// =====================================================
require_once __DIR__ . '/admin/includes/auth.php';
require_once __DIR__ . '/admin/includes/functions.php';

startSecureSession();
requireLogin();

$db = getDB();
$classId = (int)($_GET['id'] ?? 0);

if (!$classId) { die('Invalid class ID.'); }

// Handle AJAX status check for students (must be before ANY output)
if (isset($_GET['check_status'])) {
    header('Content-Type: application/json');
    $stmt = $db->prepare("SELECT started_at FROM online_classes WHERE id = ?");
    $stmt->execute([$classId]);
    $st = $stmt->fetchColumn();
    echo json_encode(['started' => !empty($st)]);
    exit;
}

// Fetch class details
$stmt = $db->prepare("SELECT oc.*, b.name as batch_name FROM online_classes oc JOIN batches b ON b.id=oc.batch_id WHERE oc.id = ? AND oc.is_active = 1");
$stmt->execute([$classId]);
$class = $stmt->fetch();

if (!$class) { die('Class not found or inactive.'); }

$user = currentUser();
$isModerator = in_array($user['role'], ['admin', 'teacher', 'branch_manager']);
$studentId   = $user['student_id'] ?? 0;

// Teacher starting logic
if ($isModerator && isset($_POST['start_session'])) {
    $db->prepare("UPDATE online_classes SET started_at = NOW() WHERE id = ? AND started_at IS NULL")->execute([$classId]);
    header("Location: live-class.php?id=$classId");
    exit;
}

$isStarted = !empty($class['started_at']);
?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Class: <?= h($class['title'] ?: $class['batch_name']) ?> | TPA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root { --navy: #0A1628; --gold: #F5A623; }
        body, html { height: 100%; margin: 0; background: #000; overflow: hidden; font-family: sans-serif; }
        #jitsi-container { width: 100%; height: calc(100% - 60px); background: #111; }
        .live-header { height: 60px; background: var(--navy); color: #fff; padding: 0 20px; display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid var(--gold); }
        .wait-screen { height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; background: radial-gradient(circle at center, #1a3a6b 0%, #0A1628 100%); color: #fff; text-align: center; }
        .pulse-icon { font-size: 4rem; color: var(--gold); animation: pulse 2s infinite; margin-bottom: 20px; }
        @keyframes pulse { 0% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.1); opacity: 0.7; } 100% { transform: scale(1); opacity: 1; } }
    </style>
</head>
<body>

<?php if (!$isStarted && !$isModerator): ?>
    <!-- STUDENT WAITING SCREEN -->
    <div class="wait-screen" id="waitScreen">
        <div class="pulse-icon"><i class="bi bi-camera-video-fill"></i></div>
        <h2 class="fw-bold mb-2">Waiting for Teacher</h2>
        <p class="text-white-50">The session for <strong><?= h($class['batch_name']) ?></strong> hasn't started yet.<br>Please stay on this page, it will automatically refresh when the teacher joins.</p>
        <div class="spinner-border text-warning mt-4" role="status"></div>
    </div>
    <script>
        // Poll every 5 seconds to check if class started
        setInterval(() => {
            fetch('live-class.php?id=<?= $classId ?>&check_status=1')
                .then(r => r.json())
                .then(data => { if (data.started) window.location.reload(); });
        }, 5000);
    </script>
<?php 
elseif (!$isStarted && $isModerator): ?>
    <!-- TEACHER START SCREEN -->
    <div class="wait-screen">
        <div class="pulse-icon"><i class="bi bi-shield-lock-fill"></i></div>
        <h2 class="fw-bold mb-3">Initialize Live Session</h2>
        <p class="text-white-50 mb-4">Click below to start the session for <strong><?= h($class['batch_name']) ?></strong>.<br>Students will be able to join immediately once you start.</p>
        <form method="POST">
            <button type="submit" name="start_session" class="btn btn-warning btn-lg px-5 fw-800 shadow-lg text-uppercase" style="letter-spacing:1px;">
                <i class="bi bi-play-circle-fill me-2"></i> Start Session Now
            </button>
        </form>
        <a href="admin/classes/index.php" class="btn btn-link text-white-50 mt-3 text-decoration-none small">Go Back</a>
    </div>
<?php else: ?>
    <!-- LIVE CLASS VIEW -->
    <div class="live-header">
        <div class="d-flex align-items-center gap-3">
            <div class="badge bg-danger px-2 py-1"><i class="bi bi-record-circle me-1"></i> LIVE</div>
            <div class="fw-bold d-none d-sm-block"><?= h($class['title'] ?: $class['batch_name']) ?> <span class="text-white-50 fw-normal">|</span> <?= h($class['batch_name']) ?></div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="small d-none d-md-block text-white-50"><i class="bi bi-person-circle me-1"></i><?= h($user['name']) ?></span>
            <button onclick="window.close();" class="btn btn-sm btn-outline-light border-0"><i class="bi bi-box-arrow-right me-1"></i> Exit</button>
        </div>
    </div>

    <div id="jitsi-container"></div>

    <script src="https://meet.jit.si/external_api.js"></script>
    <script>
        const domain = "meet.jit.si";
        const options = {
            roomName: "TPA-Class-<?= $class['room_name'] ?: 'Room'.$classId ?>",
            width: '100%',
            height: '100%',
            parentNode: document.querySelector('#jitsi-container'),
            userInfo: {
                displayName: "<?= addslashes($user['name']) ?>",
                email: "<?= addslashes($user['email']) ?>"
            },
            interfaceConfigOverwrite: {
                TOOLBAR_BUTTONS: [
                    'microphone', 'camera', 'closedcaptions', 'desktop', 'fullscreen',
                    'fodeviceselection', 'hangup', 'profile', 'chat', 'recording',
                    'livestreaming', 'etherpad', 'sharedvideo', 'settings', 'raisehand',
                    'videoquality', 'filmstrip', 'invite', 'feedback', 'stats', 'shortcuts',
                    'tileview', 'videobackgroundblur', 'download', 'help', 'mute-everyone',
                    'security'
                ],
                SETTINGS_SECTIONS: [ 'devices', 'language', 'moderator', 'profile', 'calendar' ],
            },
            configOverwrite: {
                disableInviteFunctions: true,
                startWithAudioMuted: <?= $isModerator ? 'false' : 'true' ?>,
                startWithVideoMuted: false
            }
        };
        const api = new JitsiMeetExternalAPI(domain, options);

        // Record Attendance for Students
        <?php if ($studentId): ?>
        api.addEventListener('participantJoined', (event) => {
            // Check if it's the local user
            // In Jitsi API, local participant doesn't trigger 'participantJoined' to themselves usually
            // but we can trigger it on ready
        });

        api.on('videoConferenceJoined', () => {
            fetch('api/mark_online_attendance.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ class_id: <?= $classId ?> })
            })
            .then(r => r.json())
            .then(data => console.log('Attendance status:', data.message))
            .catch(err => console.error('Attendance error:', err));
        });
        <?php endif; ?>
    </script>
<?php endif; ?>
</body>
</html>
