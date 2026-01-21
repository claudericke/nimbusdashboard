<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<style>
    .modal-content {
        background: var(--bg-card);
        border: 1px solid var(--border-main);
        border-radius: 1.5rem;
    }

    .modal-header {
        border-bottom: 1px solid var(--border-main);
        padding: 1.5rem 2rem;
    }

    .modal-footer {
        border-top: 1px solid var(--border-main);
        padding: 1.5rem 2rem;
    }

    .modal-title {
        font-family: 'Syne', sans-serif;
        font-weight: 800;
        color: white;
    }

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
            <?php if (Session::has('success')): ?>
                <div class="alert alert-success alert-dismissible fade show reveal-up">
                    <?php echo h(Session::get('success'));
                    Session::remove('success'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="bento-grid">
                <div class="card-graphic full-width reveal-up">
                    <span class="label-graphic">Authority Center</span>
                    <h2 class="mb-4">Internal Directory</h2>

                    <ul class="nav nav-tabs mb-5">
                        <li class="nav-item"><a class="nav-link active" href="/admin/users">Personnel</a></li>
                        <li class="nav-item"><a class="nav-link" href="/admin/quotes">Inspiration</a></li>
                        <li class="nav-item"><a class="nav-link" href="/admin/permissions">Privileges</a></li>
                    </ul>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="h4 fw-bold mb-0">Active Operatives</h3>
                        <button type="button" class="btn-complete-graphic" data-bs-toggle="modal"
                            data-bs-target="#addUserModal">ENLIST NEW USER</button>
                    </div>

                    <div class="table-responsive">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th>ID <span class="sort-icon"></span></th>
                                    <th>Alias <span class="sort-icon"></span></th>
                                    <th>Nexus Domain <span class="sort-icon"></span></th>
                                    <th>Frequency <span class="sort-icon"></span></th>
                                    <th>Tier <span class="sort-icon"></span></th>
                                    <th>Privilege <span class="sort-icon"></span></th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td class="text-secondary small">#<?php echo h($user['id']); ?></td>
                                        <td class="fw-bold"><?php echo h($user['cpanel_username']); ?></td>
                                        <td><?php echo h($user['domain']); ?></td>
                                        <td class="text-secondary small"><?php echo h($user['email'] ?? 'N/A'); ?></td>
                                        <td><span
                                                class="badge-graphic text-accent-indigo"><?php echo h($user['package'] ?? 'N/A'); ?></span>
                                        </td>
                                        <td>
                                            <?php if ($user['is_superuser']): ?>
                                                <span class="badge-graphic text-accent-amber">SUPER</span>
                                            <?php else: ?>
                                                <span class="badge-graphic text-secondary">USER</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <button type="button"
                                                    class="btn btn-sm btn-complete-graphic bg-vibrant-dark"
                                                    data-bs-toggle="modal" data-bs-target="#editUserModal"
                                                    data-id="<?php echo h($user['id']); ?>"
                                                    data-username="<?php echo h($user['cpanel_username']); ?>"
                                                    data-domain="<?php echo h($user['domain']); ?>"
                                                    data-email="<?php echo h($user['email']); ?>"
                                                    data-fullname="<?php echo h($user['full_name']); ?>"
                                                    data-package="<?php echo h($user['package']); ?>"
                                                    data-superuser="<?php echo $user['is_superuser']; ?>">REFINE</button>

                                                <form method="POST" action="/admin/users/delete">
                                                    <?php echo CSRF::field(); ?>
                                                    <input type="hidden" name="delete_user"
                                                        value="<?php echo h($user['id']); ?>">
                                                    <button type="submit"
                                                        class="btn btn-sm btn-complete-graphic bg-vibrant-rose"
                                                        onclick="return confirm('Initiate user purge?')">PURGE</button>
                                                </form>
                                            </div>
                                        </td>
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

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Enlist New Operative</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/admin/users/create">
                <?php echo CSRF::field(); ?>
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="label-graphic">Nexus Alias</label>
                        <input type="text" class="form-control" name="cpanel_username" required
                            placeholder="cPanel Username">
                    </div>
                    <div class="mb-4">
                        <label class="label-graphic">Operation Domain</label>
                        <input type="text" class="form-control" name="domain" required placeholder="example.com">
                    </div>
                    <div class="mb-4">
                        <label class="label-graphic">Frequency Address</label>
                        <input type="email" class="form-control" name="email" required
                            placeholder="operative@domain.com">
                    </div>
                    <div class="mb-4">
                        <label class="label-graphic">Security Credentials</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="cpanel_password" name="cpanel_password"
                                required>
                            <button class="btn btn-outline-secondary px-3" type="button" id="generate_cpanel_password"
                                title="Generate Access Key"><i class="fas fa-key"></i></button>
                            <span class="input-group-text bg-transparent border-start-0 toggle-password"
                                data-target="#cpanel_password" style="cursor:pointer"><i
                                    class="fas fa-eye text-secondary"></i></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="label-graphic">FullName</label>
                            <input type="text" class="form-control" name="full_name" placeholder="Agent Name">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="label-graphic">Service Tier</label>
                            <input type="text" class="form-control" name="package" value="Solopreneur">
                        </div>
                    </div>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="is_superuser" value="1"
                            id="isSuperSwitch">
                        <label class="form-check-label text-white fw-bold ms-2" for="isSuperSwitch">ELITE
                            PRIVILEGES</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link text-decoration-none text-secondary fw-bold"
                        data-bs-dismiss="modal">ABORT</button>
                    <button type="submit" class="btn-complete-graphic">CONFIRM ENLISTMENT</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/public/js/password-generator.js"></script>
<script>
    if (typeof attachPasswordGenerator === 'function') {
        attachPasswordGenerator('cpanel_password', 'generate_cpanel_password');
    }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>