<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<!-- Cropper.js dependencies -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

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
                    <span class="label-graphic">Customer ID</span>
                    <h2 class="mb-5">Profile Configuration</h2>

                    <div class="row">
                        <!-- Avatar Section -->
                        <div class="col-md-3 text-center mb-4 mb-md-0 border-end border-main">
                            <div class="p-4 position-relative">
                                <div class="avatar-wrapper mx-auto mb-4"
                                    style="width:140px; height:140px; position:relative;">
                                    <img src="<?php echo h($profilePicture ?? 'https://placehold.co/140x140/1e293b/ffffff?text=AVATAR'); ?>"
                                        id="current-avatar" class="rounded-circle shadow-lg"
                                        style="width:140px; height:140px; object-fit:cover; border:3px solid var(--accent-indigo);">

                                    <button type="button"
                                        class="btn btn-sm btn-dark position-absolute bottom-0 end-0 rounded-circle p-2 shadow-lg"
                                        onclick="document.getElementById('avatar-input').click()"
                                        style="border: 2px solid var(--accent-indigo); background: var(--bg-card);">
                                        <i class="fas fa-camera"></i>
                                    </button>
                                </div>
                                <input type="file" id="avatar-input" class="d-none" accept="image/*">
                                <p class="small text-secondary fw-bold text-uppercase ls-1">Profile Photo</p>
                            </div>
                        </div>

                        <!-- Info Section -->
                        <div class="col-md-9 ps-md-5">
                            <form method="POST" action="/settings/update">
                                <?php echo CSRF::field(); ?>
                                <input type="hidden" name="profile_picture" id="profile_picture_input"
                                    value="<?php echo h($profilePicture ?? ''); ?>">

                                <div class="mb-5">
                                    <label class="label-graphic mb-2">Customer Name</label>
                                    <input type="text" name="profile_name" value="<?php echo h($profileName ?? ''); ?>"
                                        placeholder="Enter your operational name" class="form-control form-control-lg">
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn-complete-graphic px-5 py-3">
                                        COMMIT CHANGES
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Avatar Upload Modal -->
<div class="modal fade" id="cropModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="background: var(--bg-card); border-radius: 1.5rem;">
            <div class="modal-header border-0 p-4">
                <h5 class="modal-title font-syne fw-bold">Refine Interface ID</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 pt-0 text-center">
                <div class="crop-container bg-black rounded shadow-inner overflow-hidden mb-4"
                    style="max-height: 500px;">
                    <img id="image-to-crop" style="max-width: 100%;">
                </div>
                <div class="d-flex justify-content-center gap-3">
                    <button class="btn btn-dark rounded-circle p-3" onclick="cropper.rotate(-45)">
                        <i class="fas fa-undo"></i>
                    </button>
                    <button class="btn btn-dark rounded-circle p-3" onclick="cropper.rotate(45)">
                        <i class="fas fa-redo"></i>
                    </button>
                    <button class="btn btn-dark rounded-circle p-3" onclick="cropper.zoom(0.1)">
                        <i class="fas fa-search-plus"></i>
                    </button>
                    <button class="btn btn-dark rounded-circle p-3" onclick="cropper.zoom(-0.1)">
                        <i class="fas fa-search-minus"></i>
                    </button>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-link text-secondary text-decoration-none fw-bold"
                    data-bs-dismiss="modal">CANCEL</button>
                <button type="button" class="btn-complete-graphic px-4" id="save-crop">SAVE PROFILE</button>
            </div>
        </div>
    </div>
</div>

<script>
    let cropper;
    const avatarInput = document.getElementById('avatar-input');
    const image = document.getElementById('image-to-crop');
    const cropModal = new bootstrap.Modal(document.getElementById('cropModal'));

    avatarInput.addEventListener('change', function (e) {
        const files = e.target.files;
        if (files && files.length > 0) {
            const reader = new FileReader();
            reader.onload = function (event) {
                image.src = event.target.result;
                cropModal.show();
            };
            reader.readAsDataURL(files[0]);
        }
    });

    document.getElementById('cropModal').addEventListener('shown.bs.modal', function () {
        cropper = new Cropper(image, {
            aspectRatio: 1,
            viewMode: 2,
            dragMode: 'move',
            background: false,
            autoCropArea: 0.9,
            responsive: true,
            restore: false
        });
    });

    document.getElementById('cropModal').addEventListener('hidden.bs.modal', function () {
        cropper.destroy();
        cropper = null;
        avatarInput.value = '';
    });

    document.getElementById('save-crop').addEventListener('click', function () {
        const canvas = cropper.getCroppedCanvas({
            width: 400,
            height: 400
        });

        const base64Image = canvas.toDataURL('image/png');

        // Show loading state
        const saveBtn = document.getElementById('save-crop');
        const originalText = saveBtn.innerText;
        saveBtn.innerText = 'PROCESSING...';
        saveBtn.disabled = true;

        fetch('/settings/upload-avatar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo CSRF::getToken(); ?>'
            },
            body: JSON.stringify({ image: base64Image })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('current-avatar').src = data.url;
                    document.getElementById('profile_picture_input').value = data.url;
                    cropModal.hide();
                    // We also update the sidebar/header avatar if they exist
                    const globalAvatars = document.querySelectorAll('.rounded-circle[src*="avatar"]');
                    globalAvatars.forEach(img => img.src = data.url + '?t=' + Date.now());
                } else {
                    alert('Upload failed: ' + data.error);
                }
            })
            .catch(err => {
                console.error(err);
                alert('An error occurred during upload.');
            })
            .finally(() => {
                saveBtn.innerText = originalText;
                saveBtn.disabled = false;
            });
    });
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>