<?php
// =====================================================
// TPA IMS — Communications
// =====================================================

$page_title   = 'Communications';
$page_section = 'communications';
require_once __DIR__ . '/../includes/header.php';

$db = getDB();

$filter = $_GET['type'] ?? '';
$page   = max(1, (int)($_GET['page'] ?? 1));
$perPage = 30;

$where  = '1=1';
$params = [];
if ($filter) { $where .= ' AND type = ?'; $params[] = $filter; }

$total = $db->prepare("SELECT COUNT(*) FROM communications WHERE $where");
$total->execute($params); $total = (int)$total->fetchColumn();

$offset = ($page - 1) * $perPage;
$comms  = $db->prepare("SELECT c.*, u.name as sent_by_name FROM communications c LEFT JOIN users u ON u.id = c.sent_by WHERE $where ORDER BY c.created_at DESC LIMIT $perPage OFFSET $offset");
$comms->execute($params);
$comms = $comms->fetchAll();
?>

<div class="page-header">
  <h1><i class="bi bi-chat-dots me-2" style="color:var(--gold);"></i>Communications Log</h1>
</div>

<!-- Filter tabs -->
<div class="d-flex gap-2 mb-4 flex-wrap">
  <?php foreach (['' => 'All', 'whatsapp' => 'WhatsApp', 'email' => 'Email'] as $v => $l): ?>
    <a href="?type=<?= $v ?>" class="btn btn-sm <?= $filter === $v ? 'btn-dark' : 'btn-outline-secondary' ?>"><?= $l ?></a>
  <?php endforeach; ?>
</div>

<div class="tpa-table table-responsive">
  <table class="table table-hover mb-0">
    <thead><tr><th>Type</th><th>To</th><th>Message</th><th>Status</th><th>Sent By</th><th>Date</th></tr></thead>
    <tbody>
      <?php foreach ($comms as $c): ?>
        <tr>
          <td>
            <?php if ($c['type'] === 'whatsapp'): ?>
              <span class="badge bg-success"><i class="bi bi-whatsapp me-1"></i>WhatsApp</span>
            <?php elseif ($c['type'] === 'email'): ?>
              <span class="badge bg-primary"><i class="bi bi-envelope me-1"></i>Email</span>
            <?php else: ?>
              <span class="badge bg-secondary"><?= h(ucfirst($c['type'])) ?></span>
            <?php endif; ?>
          </td>
          <td class="small fw-600"><?= h($c['to_number_or_email'] ?? '—') ?></td>
          <td class="small text-muted" style="max-width:300px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;" title="<?= h($c['message']) ?>"><?= h($c['message']) ?></td>
          <td>
            <?php
              $sBadge = ['pending'=>'warning text-dark','sent'=>'info text-dark','delivered'=>'success','failed'=>'danger'];
              $cls = $sBadge[$c['status']] ?? 'secondary';
            ?>
            <span class="badge bg-<?= $cls ?>"><?= h(ucfirst($c['status'])) ?></span>
          </td>
          <td class="small"><?= h($c['sent_by_name'] ?? 'System') ?></td>
          <td class="small text-muted"><?= formatDateTime($c['created_at']) ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($comms)): ?>
        <tr><td colspan="6" class="text-center text-muted py-4">No communications recorded yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?= paginate($total, $page, $perPage, '?type='.urlencode($filter)) ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
