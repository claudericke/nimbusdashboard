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
                <div class="card wide p-4">
                    <div class="card-body">
                        <h2 class="card-title text-highlight text-white mb-4">Add Email Account</h2>
                        <form method="POST" action="/emails/store">
                            <?php echo CSRF::field(); ?>
                            <div class="mb-3">
                                <label class="form-label text-white">Username</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="email" placeholder="username" required>
                                    <span class="input-group-text">@<?php echo h(Session::getDomain()); ?></span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-white">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="email_password" name="password" required>
                                    <span class="input-group-text bg-transparent text-white toggle-password" data-target="#email_password"><i class="fas fa-eye"></i></span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-white">Quota (MB)</label>
                                <input type="number" class="form-control" name="quota" value="250">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Add Account</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
