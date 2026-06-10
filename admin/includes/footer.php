<?php
if (function_exists('currentRole') && currentRole() === 'teacher') {
    require_once __DIR__ . '/../../teacher/includes/footer.php';
    return;
}
?>
  </div><!-- /.content-area -->
</main>

<!-- ── Scripts ───────────────────────────────────────── -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
// Sidebar mobile toggle
document.getElementById('sidebarToggle')?.addEventListener('click', () => {
  document.getElementById('sidebar').classList.toggle('show');
});

// Auto-init DataTables on any .dt-table
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.dt-table').forEach(el => {
    $(el).DataTable({ pageLength: 25, order: [], responsive: true });
  });
});
</script>
<?php if (isset($extra_js)) echo $extra_js; ?>
</body>
</html>
