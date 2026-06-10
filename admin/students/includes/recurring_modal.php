<!-- Modal for Recurring Payment -->
<div class="modal fade" id="recurringModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-light"><h5 class="modal-title fw-700">Setup Recurring Payment</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <form action="../fees/edit-schedule.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
        <input type="hidden" name="student_id" value="<?= $id ?>">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label small fw-600">Select Fee Structure</label>
            <select name="fee_structure_id" class="form-select" required>
              <option value="">-- Choose template --</option>
              <?php foreach ($allStructures as $st): ?>
                <option value="<?= $st['id'] ?>"><?= h($st['name']) ?> (<?= formatMoney($st['amount']) ?> / <?= str_replace('_', ' ', $st['frequency']) ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="row g-3">
            <div class="col-sm-6">
              <label class="form-label small fw-600">Next Invoice Date</label>
              <input type="date" name="next_invoice_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="col-sm-6">
              <label class="form-label small fw-600">Payment Method</label>
              <select name="payment_method" class="form-select">
                <option value="bacs">BACS / Transfer</option>
                <option value="cash">Cash</option>
                <option value="gocardless">GoCardless (Direct Debit)</option>
                <option value="stripe">Stripe (Card)</option>
              </select>
            </div>
          </div>
          <div class="mt-3 form-check">
            <input type="checkbox" class="form-check-input" name="auto_generate" id="autoGen" value="1" checked>
            <label class="form-check-label small" for="autoGen">Automatically generate invoice when due</label>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-sm btn-primary">Enable Plan</button>
        </div>
      </form>
    </div>
  </div>
</div>
