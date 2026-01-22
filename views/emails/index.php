<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<style>
    .modal-content {
        background: var(--bg-card);
        border: 1px solid var(--border-main);
        border-radius: 1.5rem;
    }

    .modal-header {
        border-bottom: 1px solid var(--border-main);
        padding: 1.5rem 2rem;
    }

    .modal-footer {
        border-top: 1px solid var(--border-main);
        padding: 1.5rem 2rem;
    }

    .modal-title {
        font-family: 'Syne', sans-serif;
        font-weight: 800;
        color: white;
    }
</style>

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
                    <span class="label-graphic">Communications</span>
                    <div class="d-flex align-items-center mb-4">
                        <h2 class="mb-0">Email Accounts</h2>
                        <a href="/emails/create" class="btn btn-sm btn-complete-graphic bg-vibrant-emerald ms-auto"><i
                                class="fas fa-plus me-2"></i>CREATE ACCOUNT</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th>Address <span class="sort-icon"></span></th>
                                    <th>Storage Allocation <span class="sort-icon"></span></th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($emails)): ?>
                                    <?php foreach ($emails as $email): ?>
                                        <tr>
                                            <td class="fw-bold"><?php echo h($email['email'] ?? ''); ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge-graphic text-accent-indigo me-3">
                                                        <?php echo round($email['diskused'] ?? 0, 2); ?> MB USED
                                                    </span>
                                                    <span class="small text-secondary">
                                                        Quota: <?php echo ($email['diskquota'] ?? 'Unlimited'); ?> MB
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <button type="button"
                                                    class="btn btn-sm btn-complete-graphic bg-vibrant-dark me-2"
                                                    data-bs-toggle="modal" data-bs-target="#changePasswordModal"
                                                    data-email="<?php echo h($email['email'] ?? ''); ?>">CHANGE
                                                    PASSWORD</button>
                                                <button type="button" class="btn btn-sm btn-complete-graphic bg-vibrant-rose"
                                                    data-bs-toggle="modal" data-bs-target="#deleteEmailModal"
                                                    data-email="<?php echo h($email['email'] ?? ''); ?>">DELETE</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-5">
                                            <p class="text-secondary fs-5 mb-0">No digital footprints found in this sector.
                                            </p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if (isset($metadata['paginate']['total_pages']) && $metadata['paginate']['total_pages'] > 1): ?>
                        <nav class="mt-4">
                            <ul class="pagination pagination-graphic justify-content-center">
                                <?php for ($i = 1; $i <= $metadata['paginate']['total_pages']; $i++): ?>
                                    <li class="page-item <?php echo ($currentPage ?? 1) == $i ? 'active' : ''; ?>">
                                        <a class="page-link" href="/emails?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Modals -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="/emails/change-password" class="modal-content">
            <?php echo CSRF::field(); ?>
            <input type="hidden" name="email" id="change-password-email">
            <div class="modal-header">
                <h5 class="modal-title">Security Overhaul</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-secondary small text-uppercase fw-bold mb-3 ls-1">Modifying Credentials For</p>
                <div class="h5 fw-bold text-white mb-4" id="change-password-label"></div>
                <div class="mb-3">
                    <label class="form-label">Nexus Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                        <button class="btn btn-outline-secondary px-3" type="button" id="generate_new_password"
                            title="Generate Password"><i class="fas fa-key"></i></button>
                        <span class="input-group-text bg-transparent border-start-0 toggle-password"
                            data-target="#new_password" style="cursor:pointer"><i
                                class="fas fa-eye text-secondary"></i></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link text-decoration-none text-secondary fw-bold"
                    data-bs-dismiss="modal">ABORT</button>
                <button type="submit" class="btn-complete-graphic">UPDATE ACCESS</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="deleteEmailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="/emails/delete" class="modal-content">
            <?php echo CSRF::field(); ?>
            <input type="hidden" name="delete_email" id="delete-email">
            <div class="modal-header">
                <h5 class="modal-title text-accent-rose">Confirm Deletion</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-white fs-5">Are you sure you want to permanently purge <span
                        class="fw-bold text-accent-rose" id="delete-email-label"></span>?</p>
                <p class="text-secondary small">This action is irreversible and all data will be lost.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link text-decoration-none text-secondary fw-bold"
                    data-bs-dismiss="modal">ESCAPE</button>
                <button type="submit" class="btn-complete-graphic bg-vibrant-rose">CONFIRM PURGE</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="clipboardModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clipboardTitle">Notification</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-secondary small mb-3">Copy the details below to share with the user.</p>
                <textarea id="clipboardText" class="form-control bg-dark text-white border-secondary" rows="10" readonly
                    style="font-family: monospace; font-size: 0.9rem;"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link text-decoration-none text-secondary fw-bold"
                    data-bs-dismiss="modal">CLOSE</button>
                <button type="button" class="btn-complete-graphic bg-vibrant-emerald" id="copyButton"><i
                        class="fas fa-copy me-2"></i>COPY DETAILS</button>
            </div>
        </div>
    </div>
</div>

<script src="/public/js/password-generator.js"></script>
<script>
    // Clipboard Modal Logic
    <?php if (Session::has('clipboard_message')): ?>
        document.addEventListener('DOMContentLoaded', function () {
            const clipboardModalElement = document.getElementById('clipboardModal');
            if (clipboardModalElement) {
                const clipboardModal = new bootstrap.Modal(clipboardModalElement);
                document.getElementById('clipboardText').value = `<?php echo str_replace('`', '\`', Session::get('clipboard_message')); ?>`;
                document.getElementById('clipboardTitle').textContent = `<?php echo addslashes(Session::get('clipboard_title', 'Notification')); ?>`;
                clipboardModal.show();
            }
        });
        <?php
        Session::remove('clipboard_message');
        Session::remove('clipboard_title');
        ?>
    <?php endif; ?>

    document.getElementById('copyButton')?.addEventListener('click', function () {
        const copyText = document.getElementById("clipboardText");
        copyText.select();
        copyText.setSelectionRange(0, 99999); // For mobile devices

        // Try modern API first, fallback to execCommand
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(copyText.value).then(() => {
                showCopiedFeedback(this);
            });
        } else {
            document.execCommand('copy');
            showCopiedFeedback(this);
        }
    });

    function showCopiedFeedback(btn) {
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check me-2"></i>COPIED';
        setTimeout(() => {
            btn.innerHTML = originalText;
        }, 2000);
    }

    document.getElementById('changePasswordModal')?.addEventListener('show.bs.modal', function (e) {
        const email = e.relatedTarget.getAttribute('data-email');
        document.getElementById('change-password-email').value = email;
        document.getElementById('change-password-label').textContent = email;
    });
    document.getElementById('deleteEmailModal')?.addEventListener('show.bs.modal', function (e) {
        const email = e.relatedTarget.getAttribute('data-email');
        document.getElementById('delete-email').value = email;
        document.getElementById('delete-email-label').textContent = email;
    });
    if (typeof attachPasswordGenerator === 'function') {
        attachPasswordGenerator('new_password', 'generate_new_password');
    }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>