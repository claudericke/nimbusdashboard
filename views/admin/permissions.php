<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<style>
    .nav-tabs {
        border-bottom: 1px solid var(--border-main);
    }

    .nav-tabs .nav-link {
        color: var(--text-secondary);
        border: none;
        padding: 1rem 2rem;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.1em;
    }

    .nav-tabs .nav-link.active {
        background: transparent;
        color: var(--accent-indigo) !important;
        border-bottom: 3px solid var(--accent-indigo);
    }
</style>

<div class="main-content">
    <main class="flex-grow-1 p-3 p-md-5">
        <div class="container-fluid">
            <?php require __DIR__ . '/../layouts/alerts.php'; ?>

            <div class="bento-grid">
                <div class="card-graphic full-width reveal-up">
                    <span class="label-graphic">Authority Center</span>
                    <h2 class="mb-4">Internal Directory</h2>

                    <ul class="nav nav-tabs mb-5">
                        <li class="nav-item"><a class="nav-link" href="/admin/users">Personnel</a></li>
                        <li class="nav-item"><a class="nav-link" href="/admin/quotes">Inspiration</a></li>
                        <li class="nav-item"><a class="nav-link active" href="/admin/permissions">Privileges</a></li>
                    </ul>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="h4 fw-bold mb-0">Role Matrix</h3>
                    </div>

                    <div class="table-responsive">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th>Designation</th>
                                    <th>Dashboard</th>
                                    <th>Domains</th>
                                    <th>Emails</th>
                                    <th>SSL</th>
                                    <th>Billing</th>
                                    <th>Settings</th>
                                    <th>Tickets</th>
                                    <th>Admin</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($permissions as $role => $perms): ?>
                                    <tr>
                                        <td class="fw-bold" style="color: white;"><?php echo h(ucfirst($role)); ?></td>
                                        <?php foreach (['dashboard', 'domains', 'emails', 'ssl', 'billing', 'settings', 'tickets', 'admin'] as $menu): ?>
                                            <td>
                                                <form method="POST" action="/admin/permissions/update" style="margin:0">
                                                    <?php echo CSRF::field(); ?>
                                                    <input type="hidden" name="permissions[<?php echo h($role . '|' . $menu); ?>]"
                                                        value="<?php echo in_array($menu, $perms) ? '0' : '1'; ?>">
                                                    <button type="submit"
                                                        class="btn btn-sm btn-complete-graphic <?php echo in_array($menu, $perms) ? '' : 'bg-vibrant-dark'; ?>"
                                                        style="min-width:80px">
                                                        <?php echo in_array($menu, $perms) ? 'ENABLED' : 'DISABLED'; ?>
                                                    </button>
                                                </form>
                                            </td>
                                        <?php endforeach; ?>
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