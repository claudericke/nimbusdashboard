<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <main class="flex-grow-1 p-3 p-md-5">
        <div class="container-fluid">
            <br>
            <?php if(Session::has('success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo h(Session::get('success')); Session::remove('success'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <div class="bento-grid">
                <div class="card full-width p-4">
                    <div class="card-body">
                        <h2 class="card-title text-highlight text-white mb-4">Admin: Manage Users</h2>
                        <ul class="nav nav-tabs mb-4">
                            <li class="nav-item"><a class="nav-link active" href="/admin/users">Manage Users</a></li>
                            <li class="nav-item"><a class="nav-link" href="/admin/quotes">Manage Quotes</a></li>
                            <li class="nav-item"><a class="nav-link" href="/admin/permissions">Manage Permissions</a></li>
                        </ul>
                        
                        <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#addUserModal">Add New User</button>
                        <div class="table-responsive">
                            <table class="table-custom">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Domain</th>
                                        <th>Package</th>
                                        <th>Superuser</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($users as $user): ?>
                                    <tr>
                                        <td><?php echo h($user['id']); ?></td>
                                        <td><?php echo h($user['cpanel_username']); ?></td>
                                        <td><?php echo h($user['domain']); ?></td>
                                        <td><?php echo h($user['package']??'N/A'); ?></td>
                                        <td><?php echo $user['is_superuser']?'Yes':'No'; ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editUserModal" data-id="<?php echo h($user['id']); ?>" data-username="<?php echo h($user['cpanel_username']); ?>" data-domain="<?php echo h($user['domain']); ?>">Edit</button>
                                            <form method="POST" action="/admin/users/delete" style="display:inline">
                                                <?php echo CSRF::field(); ?>
                                                <input type="hidden" name="delete_user" value="<?php echo h($user['id']); ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')">Delete</button>
                                            </form>
                                        </td>
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

<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/admin/users/create">
                <?php echo CSRF::field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-white">cPanel Username</label>
                        <input type="text" class="form-control" name="cpanel_username" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Domain</label>
                        <input type="text" class="form-control" name="domain" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="cpanel_password" name="cpanel_password" required>
                            <button class="btn btn-outline-secondary" type="button" id="generate_cpanel_password" title="Generate Password"><i class="fas fa-key"></i></button>
                            <span class="input-group-text bg-transparent text-white toggle-password" data-target="#cpanel_password"><i class="fas fa-eye"></i></span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Full Name</label>
                        <input type="text" class="form-control" name="full_name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Package</label>
                        <input type="text" class="form-control" name="package" value="Solopreneur">
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_superuser" value="1">
                        <label class="form-check-label text-white">Is Superuser?</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/public/js/password-generator.js"></script>
<script>
attachPasswordGenerator('cpanel_password', 'generate_cpanel_password');
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
