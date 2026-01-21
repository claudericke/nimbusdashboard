<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <main class="flex-grow-1 p-3 p-md-5">
        <div class="container-fluid">
            <div class="bento-grid">
                <div class="card-graphic full-width reveal-up">
                    <span class="label-graphic">Infrastructure</span>
                    <h2 class="mb-4">Domains</h2>
                    <div class="table-responsive">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th>Domain Name <span class="sort-icon"></span></th>
                                    <th>Type <span class="sort-icon"></span></th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($mainDomain)): ?>
                                    <tr>
                                        <td class="fw-bold"><?php echo h($mainDomain); ?></td>
                                        <td><span class="badge-graphic text-accent-indigo">Primary</span></td>
                                        <td><a href="https://cpanel.<?php echo h($mainDomain); ?>" target="_blank"
                                                class="btn btn-sm btn-complete-graphic">EDIT CPANEL <i
                                                    class="fas fa-external-link-alt ms-1"></i></a></td>
                                    </tr>
                                <?php endif; ?>
                                <?php foreach ($subdomains as $domain_item): ?>
                                    <tr>
                                        <td><?php echo h($domain_item); ?></td>
                                        <td><span class="badge-graphic text-secondary">Subdomain</span></td>
                                        <td><a href="https://cpanel.<?php echo h($domain_item); ?>" target="_blank"
                                                class="btn btn-sm btn-complete-graphic">EDIT CPANEL <i
                                                    class="fas fa-external-link-alt ms-1"></i></a></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php foreach ($addonDomains as $domain_item): ?>
                                    <tr>
                                        <td><?php echo h($domain_item); ?></td>
                                        <td><span class="badge-graphic text-accent-purple">Addon</span></td>
                                        <td><a href="https://cpanel.<?php echo h($domain_item); ?>" target="_blank"
                                                class="btn btn-sm btn-complete-graphic">EDIT CPANEL <i
                                                    class="fas fa-external-link-alt ms-1"></i></a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>