<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

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

            <div class="bento-grid">
                <div class="card-graphic full-width reveal-up">
                    <span class="label-graphic">Access Control</span>
                    <h2 class="mb-4">Manage Permissions</h2>

                    <ul class="nav nav-tabs mb-5">
                        <li class="nav-item"><a class="nav-link" href="/admin/users">Personnel</a></li>
                        <li class="nav-item"><a class="nav-link" href="/admin/quotes">Inspiration</a></li>
                        <li class="nav-item"><a class="nav-link active" href="/admin/permissions">Privileges</a></li>
                    </ul>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex gap-2">
                             <button type="button" class="btn btn-sm btn-outline-secondary" onclick="resetChanges()">CANCEL</button>
                             <button type="button" class="btn btn-sm btn-complete-graphic" onclick="showConfirmation()">SAVE CHANGES</button>
                        </div>
                    </div>
                    
                    <form id="permissionsForm" method="POST" action="/admin/permissions/update">
                        <?php echo CSRF::field(); ?>
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
                                <?php foreach ($permissions as $role => $perms):
                                    $allowedMenus = array_column($perms, 'menu_item');
                                    ?>
                                    <tr>
                                        <td><strong><?php echo h(ucfirst($role)); ?></strong></td>
                                        <?php foreach (['dashboard', 'domains', 'emails', 'ssl', 'billing', 'settings', 'tickets', 'admin'] as $menu):
                                            $isEnabled = in_array($menu, $allowedMenus);
                                            $key = $role . '|' . $menu;
                                            ?>
                                            <td class="text-center">
                                                <input type="hidden" name="permissions[<?php echo h($key); ?>]"
                                                    id="input_<?php echo h($key); ?>"
                                                    value="<?php echo $isEnabled ? '1' : '0'; ?>"
                                                    data-initial="<?php echo $isEnabled ? '1' : '0'; ?>">
                                                <i class="cursor-pointer fas <?php echo $isEnabled ? 'fa-check-circle text-accent-emerald' : 'fa-times-circle'; ?>"
                                                    id="icon_<?php echo h($key); ?>"
                                                    style="font-size:1.2rem; transition: all 0.2s; <?php echo $isEnabled ? 'filter: drop-shadow(0 0 8px rgba(16, 185, 129, 0.4));' : 'opacity: 0.3; color: var(--accent-rose);'; ?>"
                                                    onclick="togglePermission('<?php echo h($key); ?>')"></i>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Permission Changes</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-secondary mb-3">You are about to modify access privileges. Please review the changes:</p>
                <div id="changesList" style="max-height: 200px; overflow-y: auto; background: rgba(0,0,0,0.2); padding: 1rem; border-radius: 0.5rem; font-size: 0.9rem;">
                    <!-- Changes populated by JS -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link text-decoration-none text-secondary fw-bold" data-bs-dismiss="modal">ABORT</button>
                <button type="button" class="btn-complete-graphic" onclick="submitForm()">CONFIRM & SAVE</button>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePermission(key) {
        const input = document.getElementById('input_' + key);
        const icon = document.getElementById('icon_' + key);
        
        let currentVal = parseInt(input.value);
        let newVal = currentVal === 1 ? 0 : 1;
        
        input.value = newVal;
        
        if (newVal === 1) {
            icon.className = "cursor-pointer fas fa-check-circle text-accent-emerald";
            icon.style.filter = "drop-shadow(0 0 8px rgba(16, 185, 129, 0.4))";
            icon.style.opacity = "1";
            icon.style.color = "";
        } else {
            icon.className = "cursor-pointer fas fa-times-circle";
            icon.style.filter = "none";
            icon.style.opacity = "0.3";
            icon.style.color = "var(--accent-rose)";
        }
    }

    function resetChanges() {
        if(confirm('Discard all unsaved changes?')) {
            location.reload();
        }
    }

    function showConfirmation() {
        const inputs = document.querySelectorAll('input[name^="permissions"]');
        const changesList = document.getElementById('changesList');
        let changesHTML = '';
        let hasChanges = false;
        
        inputs.forEach(input => {
            if(input.value !== input.getAttribute('data-initial')) {
                hasChanges = true;
                const key = input.id.replace('input_', '');
                const [role, menu] = key.split('|');
                const action = input.value === '1' ? '<span class="text-accent-emerald">GRANTED</span>' : '<span class="text-accent-rose">REVOKED</span>';
                
                changesHTML += `<div class="mb-2 d-flex justify-content-between border-bottom border-secondary pb-1" style="border-opacity:0.2">
                    <span>${role} <i class="fas fa-arrow-right mx-1 text-muted" style="font-size:0.7rem"></i> ${menu}</span>
                    <strong class="ms-2">${action}</strong>
                </div>`;
            }
        });
        
        if(!hasChanges) {
            alert('No changes to save.');
            return;
        }
        
        changesList.innerHTML = changesHTML;
        new bootstrap.Modal(document.getElementById('confirmationModal')).show();
    }

    function submitForm() {
        document.getElementById('permissionsForm').submit();
    }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>