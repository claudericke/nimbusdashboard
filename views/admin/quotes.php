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
                    <span class="label-graphic">Inspiration Database</span>
                    <h2 class="mb-4">Manage Quotes</h2>

                    <ul class="nav nav-tabs mb-5">
                        <li class="nav-item"><a class="nav-link" href="/admin/users">Personnel</a></li>
                        <li class="nav-item"><a class="nav-link active" href="/admin/quotes">Inspiration</a></li>
                        <li class="nav-item"><a class="nav-link" href="/admin/permissions">Privileges</a></li>
                    </ul>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="h4 fw-bold mb-0">Active Quotes</h3>
                        <button type="button" class="btn-complete-graphic" data-bs-toggle="modal"
                            data-bs-target="#addQuoteModal">ADD NEW QUOTE</button>
                    </div>

                    <div class="table-responsive">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th>Quote</th>
                                    <th>Author</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($quotes as $quote): ?>
                                    <tr>
                                        <td><?php echo h($quote['quote_text']); ?></td>
                                        <td><?php echo h($quote['author']); ?></td>
                                        <td>
                                            <form method="POST" action="/admin/quotes/delete" style="display:inline">
                                                <?php echo CSRF::field(); ?>
                                                <input type="hidden" name="delete_quote"
                                                    value="<?php echo h($quote['id']); ?>">
                                                <button type="submit"
                                                    class="btn btn-sm btn-complete-graphic bg-vibrant-rose"
                                                    onclick="return confirm('Delete this quote?')">DELETE</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<div class="modal fade" id="addQuoteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Inspiration</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/admin/quotes/create">
                <?php echo CSRF::field(); ?>
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="label-graphic">Quote Text</label>
                        <textarea class="form-control" name="quote_text" rows="3" required
                            placeholder="Enter quote..."></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="label-graphic">Author</label>
                        <input type="text" class="form-control" name="author" required placeholder="Author Name">
                    </div>
                    <div class="mb-4">
                        <label class="label-graphic">Image URL (optional)</label>
                        <input type="url" class="form-control" name="image_url" placeholder="https://...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link text-decoration-none text-secondary fw-bold"
                        data-bs-dismiss="modal">ABORT</button>
                    <button type="submit" class="btn-complete-graphic">CONFIRM ADDITION</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>