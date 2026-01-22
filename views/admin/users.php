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
                    <span class="label-graphic">USER LISTING</span>
                    <h2 class="mb-4">Internal Directory</h2>

                    <ul class="nav nav-tabs mb-5">
                        <li class="nav-item"><a class="nav-link active" href="/admin/users">Clients</a></li>
                        <li class="nav-item"><a class="nav-link" href="/admin/quotes">Inspiration</a></li>
                        <li class="nav-item"><a class="nav-link" href="/admin/permissions">Privileges</a></li>
                    </ul>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="h4 fw-bold mb-0">Active Operatives</h3>
                        <button type="button" class="btn-complete-graphic" data-bs-toggle="modal"
                            data-bs-target="#addUserModal">ADD USER</button>
                    </div>

                    <div class="table-responsive">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th>ID <span class="sort-icon"></span></th>
                                    <th>DOMAN OWNER <span class="sort-icon"></span></th>
                                    <th>DOMAIN <span class="sort-icon"></span></th>
                                    <th>E-MAIL <span class="sort-icon"></span></th>
                                    <th>PACKAGE <span class="sort-icon"></span></th>
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
                                                    data-email="<?php echo h($user['email'] ?? ''); ?>"
                                                    data-fullname="<?php echo h($user['full_name'] ?? ''); ?>"
                                                    data-package="<?php echo h($user['package'] ?? ''); ?>"
                                                    data-superuser="<?php echo $user['is_superuser'] ?? 0; ?>"
                                                    data-role="<?php echo h($user['user_role'] ?? 'client'); ?>"
                                                    data-api-token="<?php echo h($user['api_token'] ?? ''); ?>">EDIT
                                                    USER</button>

                                                <form method="POST" action="/admin/users/delete">
                                                    <?php echo CSRF::field(); ?>
                                                    <input type="hidden" name="delete_user"
                                                        value="<?php echo h($user['id']); ?>">
                                                    <button type="submit"
                                                        class="btn btn-sm btn-complete-graphic bg-vibrant-rose"
                                                        onclick="return confirm('Initiate user purge?')">DELETE
                                                        USER</button>
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
                        <label class="label-graphic">USER NAME</label>
                        <input type="text" class="form-control" name="cpanel_username" required
                            placeholder="cPanel Username">
                    </div>
                    <div class="mb-4">
                        <label class="label-graphic">DOMAIN</label>
                        <input type="text" class="form-control" name="domain" required placeholder="example.com">
                    </div>
                    <div class="mb-4">
                        <label class="label-graphic">E-MAIL ADDRESS</label>
                        <input type="email" class="form-control" name="email" required
                            placeholder="operative@domain.com">
                    </div>
                    <div class="mb-4">
                        <label class="label-graphic">API TOKEN</label>
                        <input type="text" class="form-control" name="api_token" required
                            placeholder="Enter cPanel API Token">
                        <small class="text-secondary">A valid API token is required for dashboard connections.</small>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="label-graphic">DOMAIN OWNER</label>
                            <input type="text" class="form-control" name="full_name" placeholder="Agent Name">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="label-graphic">PACKAGE</label>
                            <select class="form-select" name="package">
                                <option value="Solopreneur">Solopreneur</option>
                                <option value="Small Business">Small Business</option>
                                <option value="Enterprise">Enterprise</option>
                            </select>
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
                        data-bs-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn-complete-graphic">CREATE USER</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Operative Dossier</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/admin/users/edit">
                <?php echo CSRF::field(); ?>
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="label-graphic">USER NAME</label>
                        <input type="text" class="form-control" name="cpanel_username" id="edit_username" required>
                    </div>
                    <div class="mb-4">
                        <label class="label-graphic">DOMAIN</label>
                        <input type="text" class="form-control" name="cpanel_domain" id="edit_domain" required>
                    </div>
                    <div class="mb-4">
                        <label class="label-graphic">E-MAIL ADDRESS</label>
                        <input type="email" class="form-control" name="email" id="edit_email" required>
                    </div>
                    <div class="mb-4">
                        <label class="label-graphic">API TOKEN</label>
                        <input type="text" class="form-control" name="api_token" id="edit_api_token"
                            placeholder="Update API Token (Optional)">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="label-graphic">DOMAIN OWNER</label>
                            <input type="text" class="form-control" name="full_name" id="edit_fullname">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="label-graphic">PACKAGE</label>
                            <select class="form-select" name="package" id="edit_package">
                                <option value="Solopreneur">Solopreneur</option>
                                <option value="Small Business">Small Business</option>
                                <option value="Enterprise">Enterprise</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="label-graphic">Clearance Role</label>
                            <select class="form-select" name="user_role" id="edit_role">
                                <option value="client">Client</option>
                                <option value="viewer">Viewer</option>
                                <option value="admin">Administrator</option>
                                <option value="superuser">Super User</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-4 pt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_superuser" value="1"
                                    id="edit_isSuper">
                                <label class="form-check-label text-white fw-bold ms-2" for="edit_isSuper">ELITE
                                    PRIVILEGES</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link text-decoration-none text-secondary fw-bold"
                        data-bs-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn-complete-graphic">UPDATE LEDGER</button>
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

    function generateRandomPassword(targetId) {
        const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+";
        let password = "";
        for (let i = 0; i < 16; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById(targetId).value = password;
    }

    // Populate Edit Modal
    var editUserModal = document.getElementById('editUserModal');
    editUserModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;

        var id = button.getAttribute('data-id');
        var username = button.getAttribute('data-username');
        var domain = button.getAttribute('data-domain');
        var email = button.getAttribute('data-email');
        var password = button.getAttribute('data-password');
        var fullname = button.getAttribute('data-fullname');
        var package = button.getAttribute('data-package');
        var isSuper = button.getAttribute('data-superuser');
        var role = button.getAttribute('data-role');
        var apiToken = button.getAttribute('data-api-token'); // New attribute

        var modal = this;
        modal.querySelector('#edit_id').value = id;
        modal.querySelector('#edit_username').value = username;
        modal.querySelector('#edit_domain').value = domain;
        modal.querySelector('#edit_email').value = email;
        // modal.querySelector('#edit_password').value = password; // Removed
        modal.querySelector('#edit_fullname').value = fullname;
        modal.querySelector('#edit_package').value = package;
        modal.querySelector('#edit_role').value = role;

        // Populate API Token if field exists (though logic might handle updates differently)
        var tokenInput = modal.querySelector('#edit_api_token');
        if (tokenInput) tokenInput.value = apiToken || '';

        if (isSuper == '1') {
            modal.querySelector('#edit_isSuper').checked = true;
        } else {
            modal.querySelector('#edit_isSuper').checked = false;
        }
    });
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>