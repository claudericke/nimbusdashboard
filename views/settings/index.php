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
                        <h2 class="card-title text-highlight text-white mb-4">Settings</h2>
                        <form method="POST" action="/settings/update">
                            <?php echo CSRF::field(); ?>
                            <div class="mb-3 d-flex flex-column flex-md-row align-items-center">
                                <div class="me-md-4 mb-3 mb-md-0">
                                    <img src="<?php echo h($profilePicture??'https://placehold.co/90x90/e2e8f0/64748b?text=PFP'); ?>" class="rounded-circle p-1" width="90" height="90" alt="Profile Picture">
                                </div>
                                <div class="flex-grow-1">
                                    <label class="form-label text-white">Profile Picture URL</label>
                                    <input type="url" name="profile_picture" value="<?php echo h($profilePicture??''); ?>" placeholder="https://example.com/image.jpg" class="form-control">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-white">Full Name</label>
                                <input type="text" name="profile_name" value="<?php echo h($profileName??''); ?>" placeholder="e.g., John Doe" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
