<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <main class="flex-grow-1 p-3 p-md-5">
        <div class="container-fluid">
            <br>
            <div class="bento-grid">
                <div class="card full-width p-4">
                    <div class="card-body">
                        <h2 class="card-title text-highlight text-white mb-4">Billing</h2>
                        <div class="table-responsive">
                            <table class="table-custom">
                                <thead>
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Date</th>
                                        <th>Due Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($invoices)): ?>
                                        <?php foreach($invoices as $invoice): ?>
                                        <tr>
                                            <td><?php echo h($invoice['invoice_number']??'N/A'); ?></td>
                                            <td><?php echo h($invoice['date']??'N/A'); ?></td>
                                            <td><?php echo h($invoice['due_date']??'N/A'); ?></td>
                                            <td><?php echo h($invoice['total']??'N/A'); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo ($invoice['balance']>0)?'danger':'success'; ?>">
                                                    <?php echo ($invoice['balance']>0)?'UNPAID':'PAID'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if($invoice['balance']>0 && isset($invoice['payment_url'])): ?>
                                                <a href="<?php echo h($invoice['payment_url']); ?>" target="_blank" class="btn btn-sm btn-success">Make Payment</a>
                                                <?php else: ?>
                                                <span class="text-success">Paid</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="6" class="text-center text-white">No invoices found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
