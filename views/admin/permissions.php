<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <main class="flex-grow-1 p-3 p-md-5">
        <div class="container-fluid">
            <br>
            <div class="bento-grid">
                <div class="card full-width p-4">
                    <div class="card-body">
                        <h2 class="card-title text-highlight text-white mb-4">Admin: Manage Permissions</h2>
                        <ul class="nav nav-tabs mb-4">
                            <li class="nav-item"><a class="nav-link" href="/admin/users">Manage Users</a></li>
                            <li class="nav-item"><a class="nav-link" href="/admin/quotes">Manage Quotes</a></li>
                            <li class="nav-item"><a class="nav-link active" href="/admin/permissions">Manage Permissions</a></li>
                        </ul>
                        
                        <div class="table-responsive">
                            <table class="table-custom">
                                <thead>
                                    <tr>
                                        <th>Role</th>
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
                                    <?php foreach($permissions as $role=>$perms): ?>
                                    <tr>
                                        <td><strong><?php echo h(ucfirst($role)); ?></strong></td>
                                        <?php foreach(['dashboard','domains','emails','ssl','billing','settings','tickets','admin'] as $menu): ?>
                                        <td>
                                            <form method="POST" action="/admin/permissions/update" style="margin:0">
                                                <?php echo CSRF::field(); ?>
                                                <input type="hidden" name="permissions[<?php echo h($role.'|'.$menu); ?>]" value="<?php echo in_array($menu,$perms)?'0':'1'; ?>">
                                                <button type="submit" class="btn btn-sm <?php echo in_array($menu,$perms)?'btn-success':'btn-secondary'; ?>" style="min-width:60px">
                                                    <?php echo in_array($menu,$perms)?'✓ Yes':'✗ No'; ?>
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
        </div>
    </main>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
