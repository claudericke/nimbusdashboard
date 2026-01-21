<?php if (Session::has('success')): ?>
    <div class="alert alert-success alert-dismissible fade show reveal-up mb-4" role="alert"
        style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #10b981; border-radius: 1rem;">
        <div class="d-flex align-items-center">
            <i class="fas fa-check-circle me-3 fs-5"></i>
            <div>
                <strong>SUCCESS:</strong>
                <?php echo h(Session::get('success')); ?>
            </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        <?php Session::remove('success'); ?>
    </div>
<?php endif; ?>

<?php if (Session::has('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show reveal-up mb-4" role="alert"
        style="background: rgba(244, 63, 94, 0.1); border: 1px solid rgba(244, 63, 94, 0.2); color: #f43f5e; border-radius: 1rem;">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-triangle me-3 fs-5"></i>
            <div>
                <strong>ERROR:</strong>
                <?php echo h(Session::get('error')); ?>
            </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        <?php Session::remove('error'); ?>
    </div>
<?php endif; ?>