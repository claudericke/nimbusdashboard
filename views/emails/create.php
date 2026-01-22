<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <main class="flex-grow-1 p-3 p-md-5">
        <div class="container-fluid">
            <br>
            <?php if (Session::has('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?php echo h(Session::get('success'));
                    Session::remove('success'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if (Session::has('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?php echo h(Session::get('error'));
                    Session::remove('error'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="bento-grid">
                <div class="card-graphic full-width reveal-up">
                    <span class="label-graphic">Provisioning</span>
                    <h2 class="mb-4">New Email Account</h2>
                    <form method="POST" action="/emails/store" class="mt-4">
                        <?php echo CSRF::field(); ?>
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label
                                    class="form-label text-secondary small text-uppercase fw-bold ls-1">Username</label>
                                <div class="input-group">
                                    <input type="text" class="form-control bg-dark border-secondary text-white"
                                        name="email" placeholder="username" required
                                        style="border-color: rgba(255,255,255,0.1) !important;">
                                    <span class="input-group-text bg-dark border-secondary text-secondary"
                                        style="border-color: rgba(255,255,255,0.1) !important;">@<?php echo h(Session::getDomain()); ?></span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label
                                    class="form-label text-secondary small text-uppercase fw-bold ls-1">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control bg-dark border-secondary text-white"
                                        id="email_password" name="password" required
                                        style="border-color: rgba(255,255,255,0.1) !important;">
                                    <button class="btn btn-outline-secondary border-secondary text-secondary"
                                        type="button" id="generate_email_password" title="Generate Password"
                                        style="border-color: rgba(255,255,255,0.1) !important;"><i
                                            class="fas fa-key"></i></button>
                                    <span class="input-group-text bg-dark border-secondary text-white toggle-password"
                                        data-target="#email_password"
                                        style="cursor:pointer; border-color: rgba(255,255,255,0.1) !important;"><i
                                            class="fas fa-eye text-secondary"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="mb-5">
                            <label class="form-label text-secondary small text-uppercase fw-bold ls-1">Storage
                                Quota</label>
                            <div class="d-flex align-items-center">
                                <input type="number" class="form-control bg-dark border-secondary text-white me-3"
                                    name="quota" value="250"
                                    style="width: 150px; border-color: rgba(255,255,255,0.1) !important;">
                                <span class="text-secondary">MB</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <a href="/emails"
                                class="btn btn-link text-decoration-none text-secondary fw-bold ms-3 order-1">CANCEL</a>
                            <button type="submit" class="btn-complete-graphic bg-vibrant-emerald px-5 py-2">CREATE
                                ACCOUNT</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="/public/js/password-generator.js"></script>
<script>
    attachPasswordGenerator('email_password', 'generate_email_password');
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>