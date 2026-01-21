<?php require __DIR__ . '/../layouts/header.php'; ?>
<style>
    .login-card {
        background: #1e1e1e;
        border: none
    }

    .login-img-col {
        background: url('assets/images/auth/nimbus-woman-1.jpg') center/cover no-repeat
    }
</style>
<div class="d-flex justify-content-center align-items-center px-3" style="min-height:100vh;background-color:#252427">
    <div class="card my-5 col-lg-6 mx-auto login-card rounded-4 p-0">
        <div class="row g-0">
            <div class="col-lg-6 d-flex flex-column justify-content-center p-5">
                <a href="https://hosting.driftnimbus.com"><img
                        src="https://hosting.driftnimbus.com/wp-content/uploads/2025/02/nimbus-logo-horizontal-white.svg"
                        class="mb-4" width="145" alt="Drift Nimbus Logo"></a>
                <h4 class="fw-bold text-white">Dashboard</h4>
                <p class="mb-5 text-white">Log in to manage your hosting account powered by Drift Nimbus.</p>
                <?php if (Session::has('error')): ?>
                    <div class="alert alert-danger"><?php echo h(Session::get('error'));
                    Session::remove('error'); ?></div>
                <?php endif; ?>
                <div class="form-body mt-4">
                    <form class="row g-3" action="/login" method="POST">
                        <?php echo CSRF::field(); ?>
                        <div class="col-12">
                            <label for="domain" class="form-label text-white">Domain</label>
                            <input type="text" class="form-control" id="domain" name="domain"
                                placeholder="yourdomain.com" required>
                        </div>
                        <div class="col-12">
                            <label for="password" class="form-label text-white">cPanel Password</label>
                            <div class="input-group" id="show_hide_password">
                                <input type="password" class="form-control border-end-0" id="password" name="password"
                                    placeholder="Enter Password" required>
                                <a href="javascript:;" class="input-group-text bg-transparent text-white"><i
                                        class="fas fa-eye"></i></a>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-grid"><button type="submit" class="btn btn-primary">Login</button></div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block login-img-col"></div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('show_hide_password')?.querySelector('a').addEventListener('click', function (e) {
        e.preventDefault();
        const input = this.previousElementSibling,
            icon = this.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
</script>
</body>

</html>