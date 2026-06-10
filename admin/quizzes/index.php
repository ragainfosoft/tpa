<?php
// =====================================================
// TPA — Admin Quiz Library (index)
// =====================================================
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
startSecureSession();
requireRole(['admin','branch_manager','teacher']);

$db = getDB();

// Filters
$subjectId = (int)($_GET['subject'] ?? 0) ?: null;
$yearGroup = trim($_GET['year'] ?? '');
$search    = trim($_GET['q'] ?? '');

$where = ['1=1']; $params = [];
if ($subjectId) { $where[] = 'qs.subject_id=?'; $params[] = $subjectId; }
if ($yearGroup) { $where[] = 'qs.year_group=?'; $params[] = $yearGroup; }
if ($search)    { $where[] = '(qs.title LIKE ? OR qs.lesson LIKE ?)'; $params[] = "%$search%"; $params[] = "%$search%"; }

$quizzes = $db->prepare("SELECT qs.*, sub.name as subject_name, u.name as created_by_name,
    (SELECT COUNT(*) FROM quiz_questions WHERE quiz_id=qs.id) as question_count,
    (SELECT COUNT(*) FROM quiz_attempts att WHERE att.quiz_id=qs.id AND att.status='submitted') as attempts
    FROM quiz_sets qs LEFT JOIN subjects sub ON sub.id=qs.subject_id LEFT JOIN users u ON u.id=qs.created_by
    WHERE ".implode(' AND ', $where)." ORDER BY qs.updated_at DESC");
$quizzes->execute($params); $quizzes = $quizzes->fetchAll();

$subjects = $db->query('SELECT id,name FROM subjects ORDER BY sort_order')->fetchAll();

$page_title   = 'Quiz Library';
$page_section = 'quizzes';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
  <div>
    <h1><i class="bi bi-check2-square me-2" style="color:var(--navy);"></i>Quiz Library</h1>
    <p class="text-muted mb-0">Manage multiple-choice quizzes and assessments</p>
  </div>
  <div class="d-flex gap-2">
    <a href="import.php" class="btn btn-outline-success"><i class="bi bi-file-earmark-spreadsheet me-1"></i>Import CSV</a>
    <?php if (in_array(currentRole(), ['admin','branch_manager','teacher'])): ?>
    <a href="create.php" class="btn btn-dark"><i class="bi bi-plus-lg me-1"></i>New Quiz</a>
    <?php endif; ?>
  </div>
</div>
<!-- Filters -->
<div class="stat-card mb-4">
  <form method="GET" class="d-flex gap-3 flex-wrap align-items-end">
    <div class="flex-grow-1"><label class="form-label fw-600 small">Search</label><input type="text" name="q" class="form-control" value="<?= h($search) ?>" placeholder="Quiz title or lesson…"></div>
    <div><label class="form-label fw-600 small">Subject</label>
      <select name="subject" class="form-select"><option value="">All Subjects</option><?php foreach ($subjects as $s): ?><option value="<?= $s['id'] ?>" <?= $s['id']==$subjectId?'selected':'' ?>><?= h($s['name']) ?></option><?php endforeach; ?></select>
    </div>
    <div><label class="form-label fw-600 small">Year Group</label>
      <select name="year" class="form-select"><option value="">All Years</option><?php foreach (['Year 1','Year 2','Year 3','Year 4','Year 5','Year 6','Year 7','Year 8','Year 9','Year 10','Year 11'] as $y): ?><option <?= $y===$yearGroup?'selected':'' ?>><?= $y ?></option><?php endforeach; ?></select>
    </div>
    <div class="d-flex gap-2"><button type="submit" class="btn btn-dark">Filter</button><a href="index.php" class="btn btn-outline-secondary">Clear</a></div>
  </form>
</div>

<!-- Stats row -->
<div class="row g-3 mb-4">
  <div class="col-4"><div class="stat-card text-center"><div class="stat-value"><?= count($quizzes) ?></div><div class="stat-label">Quizzes</div></div></div>
  <div class="col-4"><div class="stat-card text-center"><div class="stat-value"><?= array_sum(array_column($quizzes,'question_count')) ?></div><div class="stat-label">Questions</div></div></div>
  <div class="col-4"><div class="stat-card text-center"><div class="stat-value"><?= array_sum(array_column($quizzes,'attempts')) ?></div><div class="stat-label">Attempts</div></div></div>
</div>

<div class="tpa-table">
  <table class="table table-hover mb-0">
    <thead><tr><th>Quiz</th><th>Subject / Year</th><th>Questions</th><th>Time</th><th>Pass</th><th>Attempts</th><th>By</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($quizzes as $q): ?>
      <tr>
        <td>
          <div class="fw-700"><?= h($q['title']) ?></div>
          <?php if ($q['lesson']): ?><div class="small text-muted"><?= h($q['lesson']) ?></div><?php endif; ?>
        </td>
        <td>
          <div class="small font-weight-600"><?= h($q['subject_name'] ?? '—') ?></div>
          <?php if ($q['year_group']): ?><span class="badge bg-light text-dark border" style="font-size:.67rem;"><?= h($q['year_group']) ?></span><?php endif; ?>
        </td>
        <td><span class="badge bg-light text-dark border"><?= $q['question_count'] ?></span></td>
        <td class="small"><?= $q['time_limit_min']>0?$q['time_limit_min'].'min':'Unlimited' ?></td>
        <td class="small"><?= $q['pass_mark_pct'] ?>%</td>
        <td><?= $q['attempts'] ?></td>
        <td class="small text-muted"><?= h($q['created_by_name'] ?? '—') ?></td>
        <td>
          <div class="d-flex gap-1">
            <a href="create.php?id=<?= $q['id'] ?>" class="btn btn-sm btn-outline-secondary py-0 px-2" title="Edit">Edit</a>
            <a href="assign.php?quiz_id=<?= $q['id'] ?>" class="btn btn-sm btn-warning py-0 px-2 text-dark" title="Assign">Assign</a>
            <a href="results.php?quiz_id=<?= $q['id'] ?>" class="btn btn-sm btn-outline-primary py-0 px-2" title="Results">Results</a>
          </div>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (empty($quizzes)): ?><tr><td colspan="8" class="text-center text-muted py-4">No quizzes found. <a href="create.php">Create one</a>.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
