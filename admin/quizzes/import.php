<?php
// =====================================================
// TPA IMS — Import Quiz from CSV
// =====================================================
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
startSecureSession();
requireRole(['admin', 'branch_manager', 'teacher']);

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    verifyCsrf();
    
    $title      = trim($_POST['title']);
    $subjectId  = (int)$_POST['subject_id'];
    $yearGroup  = trim($_POST['year_group']);
    
    if (!$title || !$subjectId || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
        setFlash('danger', 'Invalid file or missing required fields.');
        header('Location: import.php'); exit;
    }

    $ext = pathinfo($_FILES['csv_file']['name'], PATHINFO_EXTENSION);
    if (strtolower($ext) !== 'csv') {
        setFlash('danger', 'Only CSV files are allowed. Please save your Excel file as CSV and try again.');
        header('Location: import.php'); exit;
    }

    $file = fopen($_FILES['csv_file']['tmp_name'], 'r');
    if (!$file) {
        setFlash('danger', 'Error opening uploaded file.');
        header('Location: import.php'); exit;
    }

    // Skip header row
    fgetcsv($file);
    
    try {
        $db->beginTransaction();
        
        // 1. Create Quiz Set
        $qStmt = $db->prepare("INSERT INTO quiz_sets (title, subject_id, year_group, created_by) VALUES (?, ?, ?, ?)");
        $qStmt->execute([$title, $subjectId, $yearGroup, currentUserId()]);
        $quizId = $db->lastInsertId();
        
        // 2. Parse and Insert Questions
        $qCount = 0;
        $qsStmt = $db->prepare("INSERT INTO quiz_questions (quiz_id, question, option_a, option_b, option_c, option_d, correct, explanation, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        while (($row = fgetcsv($file, 10000, ",")) !== FALSE) {
            // Expected columns: Question, A, B, C, D, Correct(A/B/C/D), Explanation
            if (count($row) < 6) continue;
            
            $question = trim($row[0]);
            $optA = trim($row[1]);
            $optB = trim($row[2]);
            $optC = trim($row[3]);
            $optD = trim($row[4]);
            $correct = strtolower(trim($row[5]));
            $explanation = isset($row[6]) ? trim($row[6]) : '';
            
            if (!$question || !$optA || !$optB || !in_array($correct, ['a','b','c','d'])) continue;
            
            $qsStmt->execute([$quizId, $question, $optA, $optB, $optC, $optD, $correct, $explanation, $qCount]);
            $qCount++;
        }
        
        $db->commit();
        setFlash('success', "Quiz imported successfully with $qCount questions.");
        header('Location: create.php?id=' . $quizId); exit;
        
    } catch (Exception $e) {
        $db->rollBack();
        setFlash('danger', 'Error importing quiz: ' . $e->getMessage());
        header('Location: import.php'); exit;
    }
}

$subjects = $db->query('SELECT id,name FROM subjects ORDER BY sort_order')->fetchAll();

$page_title   = 'Import Quiz (CSV/Excel)';
$page_section = 'quizzes';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
  <div>
    <h1><i class="bi bi-file-earmark-spreadsheet me-2" style="color:#107c41;"></i>Import Quiz from CSV</h1>
    <p class="text-muted mb-0">Upload questions from an Excel/CSV file to instantly create a new quiz.</p>
  </div>
  <a href="index.php" class="btn btn-sm btn-outline-secondary">← Back to Library</a>
</div>

<div class="row g-4">
  <div class="col-lg-7">
    <div class="stat-card">
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
        
        <h5 class="fw-700 mb-4 border-bottom pb-2">Quiz Details</h5>
        
        <div class="mb-3">
          <label class="form-label small fw-600">Quiz Title *</label>
          <input type="text" name="title" class="form-control" required placeholder="e.g. Fractions & Decimals Review">
        </div>
        
        <div class="row g-3 mb-4">
          <div class="col-md-6">
            <label class="form-label small fw-600">Subject *</label>
            <select name="subject_id" class="form-select" required>
              <option value="">-- Choose Subject --</option>
              <?php foreach($subjects as $s): ?>
                <option value="<?= $s['id'] ?>"><?= h($s['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-600">Year Group</label>
            <input type="text" name="year_group" class="form-control" placeholder="e.g. Year 5">
          </div>
        </div>

        <h5 class="fw-700 mb-4 border-bottom pb-2">Upload File</h5>
        
        <div class="mb-4">
          <label class="form-label small fw-600">CSV File * <span class="text-muted fw-normal">(Save your Excel as .csv)</span></label>
          <input type="file" name="csv_file" class="form-control" accept=".csv" required>
        </div>
        
        <button type="submit" class="btn fw-700 w-100" style="background:#107c41; color:white;"><i class="bi bi-cloud-upload me-2"></i>Import Quiz</button>
      </form>
    </div>
  </div>
  
  <div class="col-lg-5">
    <div class="stat-card bg-light border-0">
      <h6 class="fw-700 mb-3"><i class="bi bi-info-circle me-2"></i>Formatting Instructions</h6>
      <p class="small text-muted mb-3">To ensure a successful import, please format your Excel/CSV file with the following columns in exactly this order. The first row must be the header row (it will be skipped).</p>
      
      <ol class="small text-muted mb-4 ps-3" style="line-height:1.7;">
        <li><strong>Question</strong> (Required text)</li>
        <li><strong>Option A</strong> (Required text)</li>
        <li><strong>Option B</strong> (Required text)</li>
        <li><strong>Option C</strong> (Optional text)</li>
        <li><strong>Option D</strong> (Optional text)</li>
        <li><strong>Correct Answer</strong> (Required: just type A, B, C, or D)</li>
        <li><strong>Explanation</strong> (Optional text shown when reviewing results)</li>
      </ol>
      
      <div class="p-3 bg-white mt-3 border rounded shadow-sm">
        <h6 class="fw-600 small mb-2 text-dark">Example CSV structure:</h6>
        <div style="overflow-x:auto;">
          <table class="table table-bordered table-sm small mb-0" style="font-size:.7rem;">
            <tr class="table-light"><th>Question</th><th>A</th><th>B</th><th>C</th><th>D</th><th>Correct</th><th>Explanation</th></tr>
            <tr><td>What is 5 x 5?</td><td>15</td><td>25</td><td>30</td><td>55</td><td>B</td><td>5 times 5 equals 25</td></tr>
            <tr><td>Capital of UK?</td><td>London</td><td>Paris</td><td>Rome</td><td></td><td>A</td><td>London is the capital.</td></tr>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
