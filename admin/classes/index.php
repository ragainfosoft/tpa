<?php
// =====================================================
// TPA — Online Classes Overview (Admin/Teacher)
// =====================================================
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
startSecureSession();
requireRole(['admin','branch_manager','teacher','staff']);

$db = getDB();

// Handle scheduling
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['schedule_class'])) {
    verifyCsrf();
    $title      = trim($_POST['title'] ?? '');
    $batchIds   = $_POST['batch_ids'] ?? [];
    $dt         = $_POST['scheduled_at'] ?? '';
    $duration   = (int)($_POST['duration_min'] ?? 60);
    $platform   = trim($_POST['meeting_platform'] ?? 'Jitsi Meet (Built-in)');
    $manual_url = trim($_POST['meeting_url'] ?? '');
    
    if (!empty($batchIds) && $dt) {
        $roomName = bin2hex(random_bytes(8)); // Global room name for these records if multiple
        
        foreach ($batchIds as $batchId) {
            $batchId = (int)$batchId;
            $url = ($platform === 'Jitsi Meet (Built-in)') ? '' : $manual_url;
            
            $db->prepare('INSERT INTO online_classes (batch_id, title, scheduled_at, duration_min, meeting_url, platform, room_name, created_by) VALUES (?,?,?,?,?,?,?,?)')
               ->execute([$batchId, $title ?: null, $dt, $duration, $url, $platform, $roomName, currentUserId()]);
        }
        
        setFlash('success','Online class batches scheduled successfully.');
        header('Location: index.php'); exit;
    } else {
        setFlash('danger','Please fill all required fields (Batches and Date).');
    }
}

// Handle deletion
if (isset($_GET['delete'])) {
    $db->prepare('UPDATE online_classes SET is_active=0 WHERE id=?')->execute([(int)$_GET['delete']]);
    setFlash('success','Class cancelled.');
    header('Location: index.php'); exit;
}

$isTeacher = isTeacher();
$teacherId = 0;
if ($isTeacher) {
    $t = $db->prepare('SELECT id FROM teachers WHERE user_id=?');
    $t->execute([currentUserId()]); $teacherId = $t->fetchColumn();
}

// Handle AJAX attendance fetch
if (isset($_GET['fetch_attendance'])) {
    header('Content-Type: application/json');
    $cid = (int)$_GET['fetch_attendance'];
    $stmt = $db->prepare("SELECT a.joined_at, s.first_name, s.last_name, s.student_ref 
        FROM online_class_attendance a 
        JOIN students s ON s.id = a.student_id 
        WHERE a.online_class_id = ? ORDER BY a.joined_at ASC");
    $stmt->execute([$cid]);
    echo json_encode($stmt->fetchAll());
    exit;
}

// Stats & Data
$bWhere = "is_active=1";
$bParams = [];
if (currentRole() === 'teacher') { 
    $bWhere .= " AND teacher_id=?"; 
    $bParams[] = $teacherId; 
}
$batches = $db->prepare("SELECT id, name FROM batches WHERE $bWhere ORDER BY name");
$batches->execute($bParams); $batches = $batches->fetchAll();

$classes  = [];
$upcoming = [];
$past     = [];
$tablesMissing = false;

try {
    $cWhere = "oc.is_active=1";
    $cParams = [];
    if (currentRole() === 'teacher') { 
        $cWhere .= " AND b.teacher_id=?"; 
        $cParams[] = $teacherId; 
    }
    $classStmt = $db->prepare("SELECT oc.*, b.name as batch_name, u.name as teacher_name
        FROM online_classes oc JOIN batches b ON b.id=oc.batch_id
        LEFT JOIN teachers t ON t.id=b.teacher_id LEFT JOIN users u ON u.id=t.user_id
        WHERE $cWhere ORDER BY oc.scheduled_at DESC LIMIT 50");
    $classStmt->execute($cParams); $classes = $classStmt->fetchAll();

    $upcoming = array_filter($classes, fn($c)=>strtotime($c['scheduled_at']) >= strtotime('today'));
    $past     = array_filter($classes, fn($c)=>strtotime($c['scheduled_at']) <  strtotime('today'));
} catch (PDOException $e) {
    // online_classes table not yet created — show setup notice
    $tablesMissing = true;
}

$page_title   = 'Online Classes';
$page_section = 'classes';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
  <h1><i class="bi bi-camera-video me-2" style="color:var(--gold);"></i>Online Classes</h1>
  <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#newClassModal"><i class="bi bi-calendar-plus me-1"></i>Schedule Session</button>
</div>

<!-- Modal -->
<div class="modal fade" id="newClassModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-light"><h5 class="modal-title fw-700">Schedule Online Session</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <form method="POST">
        <div class="modal-body">
          <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
          <input type="hidden" name="schedule_class" value="1">
          <div class="mb-3">
            <label class="form-label small fw-600">Batches (Select multiple) *</label>
            <div class="p-3 border rounded-3 bg-white" style="max-height:160px; overflow-y:auto;">
              <?php foreach ($batches as $b): ?>
                <div class="form-check mb-1">
                  <input class="form-check-input" type="checkbox" name="batch_ids[]" value="<?= $b['id'] ?>" id="batch_<?= $b['id'] ?>">
                  <label class="form-check-label small" for="batch_<?= $b['id'] ?>"><?= h($b['name']) ?></label>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="mb-3"><label class="form-label small fw-600">Topic / Title (Optional)</label><input type="text" name="title" class="form-control" placeholder="e.g. Algebra Review"></div>
          <div class="row g-2 mb-3">
            <div class="col-sm-7"><label class="form-label small fw-600">Date & Time *</label><input type="datetime-local" name="scheduled_at" class="form-control" required></div>
            <div class="col-sm-5"><label class="form-label small fw-600">Duration (mins) *</label><input type="number" name="duration_min" class="form-control" value="60" required min="15" step="15"></div>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-sm-4">
                <label class="form-label small fw-600">Platform</label>
                <select name="meeting_platform" class="form-select" id="platformSelect" onchange="toggleUrlField()">
                    <option>Jitsi Meet (Built-in)</option>
                    <option>Zoom</option>
                    <option>Google Meet</option>
                    <option>Other</option>
                </select>
            </div>
            <div class="col-sm-8" id="urlField" style="display:none;">
                <label class="form-label small fw-600">Meeting Link *</label>
                <input type="url" name="meeting_url" class="form-control" placeholder="https://...">
            </div>
          </div>
          <script>
            function toggleUrlField() {
                const p = document.getElementById('platformSelect').value;
                document.getElementById('urlField').style.display = (p === 'Jitsi Meet (Built-in)') ? 'none' : 'block';
            }
          </script>
        </div>
        <div class="modal-footer bg-light"><button type="submit" class="btn btn-dark w-100">Schedule Session</button></div>
      </form>
    </div>
  </div>
</div>

<?php if (!empty($upcoming)): ?>
<h6 class="text-uppercase fw-700 mb-3" style="font-size:.7rem;letter-spacing:.1em;color:#888;">📅 Upcoming Sessions</h6>
<div class="row g-3 mb-4">
  <?php foreach ($upcoming as $c):
    $timeStr = date('g:ia', strtotime($c['scheduled_at'])) . ' (' . $c['duration_min'] . 'm)';
    $isToday = date('Y-m-d') === date('Y-m-d', strtotime($c['scheduled_at']));
  ?>
  <div class="col-md-6 col-lg-4">
    <div class="stat-card h-100" style="border-left:4px solid <?= $isToday?'#16a34a':'var(--navy)' ?>;">
      <div class="d-flex justify-content-between align-items-start mb-2">
        <span class="badge bg-light text-dark border" style="font-size:.67rem;"> <?= h($c['meeting_platform']) ?></span>
        <?php if ($isToday): ?><span class="badge bg-success" style="font-size:.65rem;">Today</span><?php endif; ?>
      </div>
      <div class="fw-700 mb-1"><?= h($c['title'] ?: $c['batch_name']) ?></div>
      <div class="small fw-600 text-muted mb-3"><?= h($c['batch_name']) ?></div>
      <div class="d-flex gap-2 flex-wrap mb-3">
        <span class="badge" style="background:#f1f5f9;color:#334155;"><i class="bi bi-calendar me-1"></i><?= date('D d M Y', strtotime($c['scheduled_at'])) ?></span>
        <span class="badge" style="background:#f1f5f9;color:#334155;"><i class="bi bi-clock me-1"></i><?= $timeStr ?></span>
      </div>
      <div class="d-flex gap-2">
        <?php if ($c['platform'] === 'Jitsi Meet (Built-in)'): ?>
            <a href="../../live-class.php?id=<?= $c['id'] ?>" target="_blank" class="btn btn-warning flex-grow-1 fw-800 text-uppercase" style="font-size:.7rem; letter-spacing:1px;">
                <i class="bi bi-play-circle-fill me-1"></i> Start Class
            </a>
        <?php else: ?>
            <a href="<?= h($c['meeting_url']) ?>" target="_blank" class="btn flex-grow-1 fw-700" style="background:#e0f2fe;color:#0369a1;font-size:.8rem;"><i class="bi bi-camera-video me-1"></i>Join Link</a>
        <?php endif; ?>
        <a href="index.php?delete=<?= $c['id'] ?>" class="btn btn-outline-danger px-2" onclick="return confirm('Cancel this class?')" style="font-size:.8rem;"><i class="bi bi-trash"></i></a>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if (!empty($past)): ?>
<h6 class="text-uppercase fw-700 mb-3" style="font-size:.7rem;letter-spacing:.1em;color:#888;">🕒 Past Sessions</h6>
<div class="tpa-table">
  <table class="table table-hover mb-0">
    <thead><tr><th>Session</th><th>Batch</th><th>Date & Time</th><th>Platform</th><th>Host</th></tr></thead>
    <tbody>
    <?php foreach ($past as $c): ?>
      <tr>
        <td class="fw-600"><?= h($c['title'] ?: 'Class Session') ?></td>
        <td class="small text-muted"><?= h($c['batch_name']) ?></td>
        <td class="small"><?= date('D d M Y, g:ia', strtotime($c['scheduled_at'])) ?></td>
        <td><span class="badge bg-light text-dark border"><?= h($c['meeting_platform']) ?></span></td>
        <td class="small text-muted"><?= h($c['teacher_name']) ?></td>
        <td>
            <button class="btn btn-xs btn-outline-primary py-0" style="font-size:.65rem;" onclick="viewAttendance(<?= $c['id'] ?>, '<?= addslashes($c['title'] ?: $c['batch_name']) ?>')">
                <i class="bi bi-people me-1"></i> VIEW
            </button>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<!-- Attendance Modal -->
<div class="modal fade" id="attendanceModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-light">
        <h5 class="modal-title fw-700" id="attModalTitle">Class Attendance</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-0">
        <div id="attLoading" class="text-center py-4"><div class="spinner-border text-primary spinner-border-sm"></div></div>
        <div id="attTable" style="display:none;">
            <table class="table table-sm table-hover mb-0">
                <thead class="bg-light small"><tr><th>Student</th><th>Joined At</th></tr></thead>
                <tbody id="attBody" class="small"></tbody>
            </table>
            <div id="attEmpty" class="text-center py-4 text-muted small" style="display:none;">No students joined this session yet.</div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function viewAttendance(id, title) {
    const modal = new bootstrap.Modal(document.getElementById('attendanceModal'));
    document.getElementById('attModalTitle').innerText = 'Attendance: ' + title;
    document.getElementById('attLoading').style.display = 'block';
    document.getElementById('attTable').style.display = 'none';
    document.getElementById('attBody').innerHTML = '';
    document.getElementById('attEmpty').style.display = 'none';
    
    modal.show();
    
    fetch('index.php?fetch_attendance=' + id)
        .then(r => r.json())
        .then(data => {
            document.getElementById('attLoading').style.display = 'none';
            document.getElementById('attTable').style.display = 'block';
            if (data.length === 0) {
                document.getElementById('attEmpty').style.display = 'block';
            } else {
                data.forEach(row => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `<td><strong>${row.first_name} ${row.last_name}</strong><br><span class='text-muted' style='font-size:10px;'>${row.student_ref}</span></td>
                                    <td class='text-muted'>${new Date(row.joined_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</td>`;
                    document.getElementById('attBody').appendChild(tr);
                });
            }
        });
}
</script>

<?php if ($tablesMissing): ?>
  <div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle me-2"></i>
    <strong>Database setup required.</strong> The <code>online_classes</code> table has not been created yet.
    Please run the SQL migration: <code>admin/database/tpa_schema_v3.sql</code> in phpMyAdmin to enable this feature.
  </div>
<?php elseif (empty($classes)): ?>
  <div class="text-center py-5 text-muted" style="background:#fff;border-radius:16px;">
    <i class="bi bi-camera-video fs-1 d-block mb-3"></i>
    <h5>No online classes scheduled.</h5>
    <p class="small">Click "Schedule Session" to create a meeting link for your batches.</p>
  </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
