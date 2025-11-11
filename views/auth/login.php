<?php require __DIR__ . '/../layouts/header.php'; ?>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-5">
                <div class="card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <img src="/public/assets/images/logo.png" alt="Drift Nimbus" style="max-width: 200px;">
                            <h4 class="mt-3">Welcome Back</h4>
                        </div>

                        <?php if (Session::has('error')): ?>
                            <div class="alert alert-danger">
                                <?php echo h(Session::get('error')); ?>
                                <?php Session::remove('error'); ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="/login">
                            <?php echo CSRF::field(); ?>
                            
                            <div class="mb-3">
                                <label class="form-label">Domain</label>
                                <input type="text" name="domain" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelector('#show_hide_password a')?.addEventListener('click',function(e){
    e.preventDefault();
    const input=this.previousElementSibling;
    const icon=this.querySelector('i');
    if(input.type==='password'){
        input.type='text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    }else{
        input.type='password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});
</script>
</body>
</html>
