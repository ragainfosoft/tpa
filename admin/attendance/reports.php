<?php
// =====================================================
// TPA IMS — Attendance Reports
// =====================================================

$page_title   = 'Attendance Reports';
$page_section = 'attendance';
require_once __DIR__ . '/../includes/header.php';

$db = getDB();

// Filters
$fBatch  = (int)($_GET['batch_id'] ?? 0);
$fFrom   = $_GET['from'] ?? date('Y-m-d', strtotime('-30 days'));
$fTo     = $_GET['to']   ?? date('Y-m-d');
$fStatus = $_GET['status'] ?? '';

// All active batches for filter
$batches = $db->query("SELECT id, name FROM batches WHERE is_active=1 ORDER BY name")->fetchAll();

// Build WHERE
$where  = 'a.date BETWEEN ? AND ?';
$params = [$fFrom, $fTo];
if ($fBatch)  { $where .= ' AND a.batch_id = ?'; $params[] = $fBatch; }
if ($fStatus) { $where .= ' AND a.status = ?';   $params[] = $fStatus; }

// Summary stats
$stats = $db->prepare("SELECT
    COUNT(*) as total,
    SUM(a.status='present') as present,
    SUM(a.status='absent')  as absent,
    SUM(a.status='late')    as late,
    SUM(a.status='excused') as excused
    FROM attendance a WHERE $where");
$stats->execute($params); $stats = $stats->fetch();
$attPct = $stats['total'] > 0 ? round($stats['present'] / $stats['total'] * 100) : 0;

// Per-student summary
$perStudent = $db->prepare("SELECT
    s.id, CONCAT(s.first_name,' ',s.last_name) as student_name, s.year_group,
    COUNT(*) as total,
    SUM(a.status='present') as present,
    SUM(a.status='absent')  as absent,
    SUM(a.status='late')    as late
    FROM attendance a JOIN students s ON s.id=a.student_id
    WHERE $where
    GROUP BY a.student_id ORDER BY s.first_name");
$perStudent->execute($params); $perStudent = $perStudent->fetchAll();

// Per-batch summary (when no batch filter)
$perBatch = [];
if (!$fBatch) {
    $batchQuery = $db->prepare("SELECT
        b.name as batch_name,
        COUNT(*) as total,
        SUM(a.status='present') as present,
        SUM(a.status='absent')  as absent
        FROM attendance a JOIN batches b ON b.id=a.batch_id
        WHERE a.date BETWEEN ? AND ?
        GROUP BY a.batch_id ORDER BY b.name");
    $batchQuery->execute([$fFrom, $fTo]); $perBatch = $batchQuery->fetchAll();
}
?>

<div class="page-header">
  <div>
    <h1><i class="bi bi-bar-chart me-2" style="color:var(--gold);"></i>Attendance Reports</h1>
    <p class="text-muted mb-0 small"><?= formatDate($fFrom) ?> — <?= formatDate($fTo) ?></p>
  </div>
  <a href="index.php" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<!-- Filters -->
<div class="stat-card mb-4">
  <form method="GET" class="row g-2 align-items-end">
    <div class="col-sm-3">
      <label class="form-label small fw-600">Batch</label>
      <select name="batch_id" class="form-select form-select-sm">
        <option value="">All Batches</option>
        <?php foreach ($batches as $b): ?>
          <option value="<?= $b['id'] ?>" <?= $fBatch === (int)$b['id'] ? 'selected' : '' ?>><?= h($b['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-sm-2">
      <label class="form-label small fw-600">From Date</label>
      <input type="date" name="from" class="form-control form-control-sm" value="<?= h($fFrom) ?>">
    </div>
    <div class="col-sm-2">
      <label class="form-label small fw-600">To Date</label>
      <input type="date" name="to" class="form-control form-control-sm" value="<?= h($fTo) ?>">
    </div>
    <div class="col-sm-2">
      <label class="form-label small fw-600">Status</label>
      <select name="status" class="form-select form-select-sm">
        <option value="">All Statuses</option>
        <?php foreach (['present','absent','late','excused'] as $s): ?>
          <option value="<?= $s ?>" <?= $fStatus === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-sm-auto">
      <button class="btn btn-sm btn-dark">Apply</button>
      <a href="reports.php" class="btn btn-sm btn-outline-secondary ms-1">Clear</a>
    </div>
  </form>
</div>

<!-- Summary KPIs -->
<div class="row g-3 mb-4">
  <?php
  $kpis = [
    ['Overall Rate', $attPct.'%', 'graph-up', '#e0f2fe', '#0369a1'],
    ['Present', $stats['present'] ?? 0, 'check-circle', '#d1fae5', '#065f46'],
    ['Absent',  $stats['absent']  ?? 0, 'x-circle', '#fee2e2', '#991b1b'],
    ['Late',    $stats['late']    ?? 0, 'clock', '#fef9c3', '#854d0e'],
    ['Excused', $stats['excused'] ?? 0, 'info-circle', '#e0e7ff', '#3730a3'],
    ['Total Sessions', $stats['total'] ?? 0, 'calendar3', '#f3f4f6', '#374151'],
  ];
  foreach ($kpis as [$label, $val, $icon, $bg, $color]):
  ?>
  <div class="col-6 col-lg-2">
    <div class="stat-card d-flex align-items-center gap-3">
      <div class="stat-icon" style="background:<?= $bg ?>;color:<?= $color ?>;"><i class="bi bi-<?= $icon ?>"></i></div>
      <div><div class="stat-label"><?= $label ?></div><div class="stat-value" style="font-size:1.4rem;"><?= $val ?></div></div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<?php if (!$fBatch && !empty($perBatch)): ?>
<!-- Per-batch summary -->
<div class="stat-card mb-4">
  <h6 class="fw-700 mb-3 text-uppercase" style="font-size:.7rem;letter-spacing:.1em;color:#888;">Attendance by Batch</h6>
  <div class="table-responsive">
    <table class="table table-sm mb-0">
      <thead><tr><th>Batch</th><th>Sessions</th><th>Present</th><th>Absent</th><th>Rate</th></tr></thead>
      <tbody>
        <?php foreach ($perBatch as $b):
          $pct = $b['total'] > 0 ? round($b['present']/$b['total']*100) : 0;
          $cls = $pct >= 80 ? 'text-success' : ($pct >= 60 ? 'text-warning' : 'text-danger');
        ?>
          <tr>
            <td class="fw-600 small"><?= h($b['batch_name']) ?></td>
            <td class="small"><?= $b['total'] ?></td>
            <td class="small text-success fw-600"><?= $b['present'] ?></td>
            <td class="small text-danger fw-600"><?= $b['absent'] ?></td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div class="progress flex-grow-1" style="height:6px;max-width:80px;">
                  <div class="progress-bar bg-success" style="width:<?= $pct ?>%"></div>
                </div>
                <span class="fw-700 small <?= $cls ?>"><?= $pct ?>%</span>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>

<!-- Per-student breakdown -->
<div class="tpa-table table-responsive">
  <table class="table table-hover mb-0">
    <thead><tr><th>Student</th><th>Year</th><th>Total</th><th>Present</th><th>Absent</th><th>Late</th><th>Rate</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($perStudent as $s):
        $pct = $s['total'] > 0 ? round($s['present']/$s['total']*100) : 0;
        $cls = $pct >= 80 ? 'success' : ($pct >= 60 ? 'warning' : 'danger');
      ?>
        <tr>
          <td class="fw-600"><?= h($s['student_name']) ?></td>
          <td><span class="badge bg-light text-dark border"><?= h($s['year_group'] ?? '—') ?></span></td>
          <td><?= $s['total'] ?></td>
          <td class="text-success fw-600"><?= $s['present'] ?></td>
          <td class="text-danger fw-600"><?= $s['absent'] ?></td>
          <td class="text-warning fw-600"><?= $s['late'] ?></td>
          <td>
            <div class="d-flex align-items-center gap-2">
              <div class="progress flex-grow-1" style="height:6px;max-width:80px;">
                <div class="progress-bar bg-<?= $cls ?>" style="width:<?= $pct ?>%"></div>
              </div>
              <span class="fw-700 small text-<?= $cls ?>"><?= $pct ?>%</span>
            </div>
          </td>
          <td><a href="../students/view.php?id=<?= $s['id'] ?>&tab=attendance" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:.75rem;">View</a></td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($perStudent)): ?>
        <tr><td colspan="8" class="text-center text-muted py-4">No attendance data for the selected period.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
