<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <main class="flex-grow-1 p-3 p-md-5">
        <div class="container-fluid">
            <?php if (Session::has('success')): ?>
                <div class="alert alert-success alert-dismissible fade show reveal-up">
                    <?php echo h(Session::get('success'));
                    Session::remove('success'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (Session::has('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show reveal-up">
                    <?php echo h(Session::get('error'));
                    Session::remove('error'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="bento-grid">
                <div class="card-graphic full-width reveal-up">
                    <span class="label-graphic">System Maintenance</span>
                    <h2 class="mb-4">Database Migrations</h2>

                    <div class="table-responsive">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th>Filename</th>
                                    <th>Status</th>
                                    <th>Executed At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($migrations)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted p-4">No migrations found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($migrations as $migration): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-database text-secondary me-2"></i>
                                                    <?php echo h($migration['filename']); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($migration['status'] === 'completed'): ?>
                                                    <span class="badge"
                                                        style="background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2)">Completed</span>
                                                <?php else: ?>
                                                    <span class="badge"
                                                        style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.2)">Pending</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-secondary" style="font-size:0.9rem">
                                                <?php echo $migration['executed_at'] ? date('M j, Y H:i', strtotime($migration['executed_at'])) : '-'; ?>
                                            </td>
                                            <td>
                                                <?php if ($migration['status'] === 'pending'): ?>
                                                    <form method="POST" action="/admin/migrations/run"
                                                        onsubmit="return confirm('Are you sure you want to run this migration? This changes the database schema.');">
                                                        <?php echo CSRF::field(); ?>
                                                        <input type="hidden" name="filename"
                                                            value="<?php echo h($migration['filename']); ?>">
                                                        <button type="submit" class="btn btn-sm btn-complete-graphic">
                                                            <i class="fas fa-play me-1"></i> RUN
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-outline-secondary" disabled>
                                                        <i class="fas fa-check me-1"></i> DONE
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
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