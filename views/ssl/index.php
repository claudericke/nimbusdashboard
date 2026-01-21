<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <main class="flex-grow-1 p-3 p-md-5">
        <div class="container-fluid">
            <div class="bento-grid">
                <div class="card-graphic full-width reveal-up">
                    <span class="label-graphic">Fortress Security</span>
                    <h2 class="mb-4">SSL Encryption</h2>
                    <div class="table-responsive">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th>Secure Endpoint <span class="sort-icon"></span></th>
                                    <th>Status <span class="sort-icon"></span></th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($certificates)): ?>
                                    <?php foreach ($certificates as $cert): ?>
                                        <tr>
                                            <td class="fw-bold"><?php echo h($cert['domains'][0] ?? 'N/A'); ?></td>
                                            <td>
                                                <span class="badge-graphic text-accent-emerald">
                                                    <i class="fas fa-shield-alt me-1"></i> PROTECTED
                                                </span>
                                            </td>
                                            <td>
                                                <a href="https://cpanel.<?php echo h(Session::getDomain()); ?>" target="_blank"
                                                    class="btn btn-sm btn-complete-graphic bg-vibrant-dark">ACCESS CPANEL® <i
                                                        class="fas fa-external-link-alt ms-1"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-5">
                                            <p class="text-secondary fs-5 mb-0">No active TLS/SSL protocols detected.</p>
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