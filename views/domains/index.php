<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <main class="flex-grow-1 p-3 p-md-5">
        <div class="container-fluid">
            <br>
            <div class="bento-grid">
                <div class="card full-width p-4">
                    <div class="card-body">
                        <h2 class="card-title text-white mb-4">Domains</h2>
                        <div class="table-responsive">
                            <table class="table-custom">
                                <thead>
                                    <tr>
                                        <th>Domain</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($mainDomain)): ?>
                                    <tr>
                                        <td><?php echo h($mainDomain); ?></td>
                                        <td><a href="https://cpanel.<?php echo h($mainDomain); ?>" target="_blank" class="btn btn-sm btn-primary me-2">Edit in cPanel <i class="fas fa-external-link-alt"></i></a></td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php foreach($subdomains as $domain): ?>
                                    <tr>
                                        <td><?php echo h($domain); ?></td>
                                        <td><a href="https://cpanel.<?php echo h($domain); ?>" target="_blank" class="btn btn-sm btn-primary me-2">Edit in cPanel <i class="fas fa-external-link-alt"></i></a></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php foreach($addonDomains as $domain): ?>
                                    <tr>
                                        <td><?php echo h($domain); ?></td>
                                        <td><a href="https://cpanel.<?php echo h($domain); ?>" target="_blank" class="btn btn-sm btn-primary me-2">Edit in cPanel <i class="fas fa-external-link-alt"></i></a></td>
                                    </tr>
                                    <?php endforeach; ?>
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
