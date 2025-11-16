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
            <?php if(Session::has('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo h(Session::get('error')); Session::remove('error'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <div class="bento-grid">
                <div class="card full-width p-4">
                    <div class="card-body">
                        <h2 class="card-title text-highlight text-white mb-4">Email Accounts</h2>
                        <div class="table-responsive">
                            <table class="table-custom">
                                <thead>
                                    <tr>
                                        <th>Email</th>
                                        <th>Disk Usage</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($emails)): ?>
                                        <?php foreach($emails as $email): ?>
                                        <tr>
                                            <td><?php echo h($email['email']??''); ?></td>
                                            <td><?php echo round($email['diskused']??0,2).'/'.($email['diskquota']??'unlimited').' MB'; ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-warning me-2" data-bs-toggle="modal" data-bs-target="#changePasswordModal" data-email="<?php echo h($email['email']??''); ?>">Change Password</button>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteEmailModal" data-email="<?php echo h($email['email']??''); ?>">Delete</button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="3" class="text-center text-white">No email accounts found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <nav class="mt-4">
                            <ul class="pagination justify-content-center">
                                <?php for($i=1;$i<=$metadata['paginate']['total_pages']??1;$i++): ?>
                                <li class="page-item <?php echo $currentPage==$i?'active':''; ?>">
                                    <a class="page-link" href="/emails?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="/emails/change-password" class="modal-content">
            <?php echo CSRF::field(); ?>
            <input type="hidden" name="email" id="change-password-email">
            <div class="modal-header">
                <h5 class="modal-title">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-white">Change password for <span class="fw-bold" id="change-password-label"></span></p>
                <div class="mb-3">
                    <label class="form-label text-white">New Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                        <button class="btn btn-outline-secondary" type="button" id="generate_new_password" title="Generate Password"><i class="fas fa-key"></i></button>
                        <span class="input-group-text bg-transparent text-white toggle-password" data-target="#new_password"><i class="fas fa-eye"></i></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Change Password</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="deleteEmailModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="/emails/delete" class="modal-content">
            <?php echo CSRF::field(); ?>
            <input type="hidden" name="delete_email" id="delete-email">
            <div class="modal-header">
                <h5 class="modal-title">Delete Email Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <span class="fw-bold" id="delete-email-label"></span>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Delete</button>
            </div>
        </form>
    </div>
</div>

<script src="/public/js/password-generator.js"></script>
<script>
document.getElementById('changePasswordModal')?.addEventListener('show.bs.modal',function(e){
    const email=e.relatedTarget.getAttribute('data-email');
    document.getElementById('change-password-email').value=email;
    document.getElementById('change-password-label').textContent=email;
});
document.getElementById('deleteEmailModal')?.addEventListener('show.bs.modal',function(e){
    const email=e.relatedTarget.getAttribute('data-email');
    document.getElementById('delete-email').value=email;
    document.getElementById('delete-email-label').textContent=email;
});
attachPasswordGenerator('new_password', 'generate_new_password');
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
