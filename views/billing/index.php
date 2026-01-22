<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <main class="flex-grow-1 p-3 p-md-5">
        <div class="container-fluid">
            <div class="bento-grid">
                <div class="card-graphic full-width reveal-up">
                    <span class="label-graphic">Treasury</span>
                    <h2 class="mb-4">Financial History</h2>
                    <div class="table-responsive">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th>Ref ID <span class="sort-icon"></span></th>
                                    <th>Issued <span class="sort-icon"></span></th>
                                    <th>Deadline <span class="sort-icon"></span></th>
                                    <th>Amount <span class="sort-icon"></span></th>
                                    <th>Status <span class="sort-icon"></span></th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($invoices)): ?>
                                    <?php foreach ($invoices as $invoice): ?>
                                        <tr>
                                            <td class="fw-bold"><?php echo h($invoice['invoice_number'] ?? 'N/A'); ?></td>
                                            <td class="text-secondary"><?php echo h($invoice['date'] ?? 'N/A'); ?></td>
                                            <td class="text-secondary"><?php echo h($invoice['due_date'] ?? 'N/A'); ?></td>
                                            <td class="fw-bold">
                                                <?php echo h(($invoice['currency_symbol'] ?? '$') . ($invoice['total'] ?? '0.00')); ?>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge-graphic <?php echo ($invoice['balance'] > 0) ? 'text-accent-rose' : 'text-accent-emerald'; ?>">
                                                    <?php echo ($invoice['balance'] > 0) ? 'UNPAID' : 'PAID'; ?>
                                                </span>
                                            </td>
                                            <td>

                                                <?php if ($invoice['balance'] > 0 && !empty($invoice['payment_url'])): ?>
                                                    <a href="<?php echo h($invoice['payment_url']); ?>" target="_blank"
                                                        class="btn btn-sm btn-complete-graphic bg-vibrant-emerald">PAY
                                                        NOW</a>
                                                <?php elseif ($invoice['balance'] > 0): ?>
                                                    <span class="text-secondary small">Payment Link Unavailable</span>
                                                <?php else: ?>
                                                    <span class="text-accent-emerald fw-bold small"><i
                                                            class="fas fa-check-double me-1"></i> CLEARED</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <p class="text-secondary fs-5 mb-0">No financial transactions archived.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>