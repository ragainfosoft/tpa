<?php
// =====================================================
// TPA IMS — Fee structures (Templates)
// =====================================================

// Role check BEFORE any HTML output
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
startSecureSession();
requireRole(['admin']);

$page_title   = 'Fee Structures';
$page_section = 'fees';
require_once __DIR__ . '/../includes/header.php';

$db = getDB();

// --- Handle Delete ---
if (isset($_GET['delete'])) {
    verifyCsrf();
    $db->prepare('UPDATE fee_structures SET is_active=0 WHERE id=?')->execute([(int)$_GET['delete']]);
    setFlash('success', 'Fee structure deactivated.');
    header('Location: structures.php'); exit;
}

// --- Handle Create/Edit ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_structure'])) {
    verifyCsrf();
    $id      = (int)($_POST['id'] ?? 0);
    $name    = trim($_POST['name'] ?? '');
    $amount  = (float)($_POST['amount'] ?? 0);
    $freq    = $_POST['frequency'] ?? 'monthly';
    $desc    = trim($_POST['description'] ?? '');
    
    if ($name && $amount > 0) {
        if ($id) {
            $db->prepare('UPDATE fee_structures SET name=?, amount=?, frequency=?, description=? WHERE id=?')
               ->execute([$name, $amount, $freq, $desc, $id]);
            setFlash('success', 'Fee structure updated.');
        } else {
            $db->prepare('INSERT INTO fee_structures (name, amount, frequency, description) VALUES (?,?,?,?)')
               ->execute([$name, $amount, $freq, $desc]);
            setFlash('success', 'Fee structure created.');
        }
        header('Location: structures.php'); exit;
    } else {
        setFlash('danger', 'Please provide a name and valid amount.');
    }
}

$structures = $db->query('SELECT * FROM fee_structures WHERE is_active=1 ORDER BY name')->fetchAll();
?>

<div class="page-header">
  <h1><i class="bi bi-layers me-2" style="color:var(--gold);"></i>Fee Structures</h1>
  <button class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#editModal"><i class="bi bi-plus-lg me-1"></i>New Structure</button>
</div>

<div class="alert alert-info small">
  <i class="bi bi-info-circle me-2"></i>
  Fee structures are templates used for recurring billing. You can assign these to students from their profile page.
</div>

<div class="stat-card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead>
        <tr>
          <th>Name</th>
          <th>Amount</th>
          <th>Frequency</th>
          <th>Description</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($structures as $s): ?>
          <tr>
            <td class="fw-700"><?= h($s['name']) ?></td>
            <td class="fw-600 text-success"><?= formatMoney($s['amount']) ?></td>
            <td class="small text-uppercase fw-700 text-muted"><?= str_replace('_', ' ', $s['frequency']) ?></td>
            <td class="small text-muted"><?= h($s['description'] ?: '—') ?></td>
            <td class="text-end">
              <button class="btn btn-sm btn-outline-primary py-0 px-2 edit-btn" 
                data-id="<?= $s['id'] ?>" 
                data-name="<?= h($s['name']) ?>" 
                data-amount="<?= $s['amount'] ?>" 
                data-freq="<?= h($s['frequency']) ?>" 
                data-desc="<?= h($s['description'] ?? '') ?>">
                <i class="bi bi-pencil"></i>
              </button>
              <a href="?delete=<?= $s['id'] ?>&csrf_token=<?= h(csrfToken()) ?>" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="return confirm('Deactivate this structure?')"><i class="bi bi-trash"></i></a>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($structures)): ?>
          <tr><td colspan="5" class="text-center py-4 text-muted small">No active fee structures found. Click "New Structure" to create one.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-light"><h5 class="modal-title fw-700">Fee Structure</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
        <input type="hidden" name="save_structure" value="1">
        <input type="hidden" name="id" id="m_id" value="">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label small fw-600">Structure Name *</label>
            <input type="text" name="name" id="m_name" class="form-control" placeholder="e.g. 11+ Weekly Tuition" required>
          </div>
          <div class="row g-3">
            <div class="col-sm-6">
              <label class="form-label small fw-600">Amount *</label>
              <div class="input-group">
                <span class="input-group-text">£</span>
                <input type="number" step="0.01" name="amount" id="m_amount" class="form-control" required>
              </div>
            </div>
            <div class="col-sm-6">
              <label class="form-label small fw-600">Frequency</label>
              <select name="frequency" id="m_freq" class="form-select">
                <?php foreach (['per_session','weekly','fortnightly','monthly','half_termly','termly','annual'] as $f): ?>
                  <option value="<?= $f ?>"><?= ucfirst(str_replace('_', ' ', $f)) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="mt-3">
            <label class="form-label small fw-600">Private Description (Optional)</label>
            <textarea name="description" id="m_desc" class="form-control" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-sm btn-dark">Save Structure</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('m_id').value = btn.dataset.id;
        document.getElementById('m_name').value = btn.dataset.name;
        document.getElementById('m_amount').value = btn.dataset.amount;
        document.getElementById('m_freq').value = btn.dataset.freq;
        document.getElementById('m_desc').value = btn.dataset.desc;
        new bootstrap.Modal(document.getElementById('editModal')).show();
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
