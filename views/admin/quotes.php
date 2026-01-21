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

    .nav-tabs {
        border-bottom: 1px solid var(--border-main);
    }

    .nav-tabs .nav-link {
        color: var(--text-secondary);
        border: none;
        padding: 1rem 2rem;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.1em;
    }

    .nav-tabs .nav-link.active {
        background: transparent;
        color: var(--accent-indigo) !important;
        border-bottom: 3px solid var(--accent-indigo);
    }
</style>

<div class="main-content">
    <main class="flex-grow-1 p-3 p-md-5">
        <div class="container-fluid">
            <?php require __DIR__ . '/../layouts/alerts.php'; ?>

            <div class="bento-grid">
                <div class="card-graphic full-width reveal-up">
                    <span class="label-graphic">Authority Center</span>
                    <h2 class="mb-4">Internal Directory</h2>

                    <ul class="nav nav-tabs mb-5">
                        <li class="nav-item"><a class="nav-link" href="/admin/users">Personnel</a></li>
                        <li class="nav-item"><a class="nav-link active" href="/admin/quotes">Inspiration</a></li>
                        <li class="nav-item"><a class="nav-link" href="/admin/permissions">Privileges</a></li>
                    </ul>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="h4 fw-bold mb-0">Daily Inspiration</h3>
                        <button type="button" class="btn-complete-graphic" data-bs-toggle="modal"
                            data-bs-target="#addQuoteModal">ENLIST NEW QUOTE</button>
                    </div>

                    <div class="table-responsive">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th>Quote Message</th>
                                    <th>Author</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($quotes as $quote): ?>
                                    <tr>
                                        <td class="fw-bold" style="color: white;">"<?php echo h($quote['quote_text']); ?>"
                                        </td>
                                        <td><span
                                                class="badge-graphic text-accent-indigo"><?php echo h($quote['author']); ?></span>
                                        </td>
                                        <td>
                                            <form method="POST" action="/admin/quotes/delete">
                                                <?php echo CSRF::field(); ?>
                                                <input type="hidden" name="delete_quote"
                                                    value="<?php echo h($quote['id']); ?>">
                                                <button type="submit"
                                                    class="btn btn-sm btn-complete-graphic bg-vibrant-rose"
                                                    onclick="return confirm('Purge this inspiration?')">PURGE</button>
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

<!-- Add Quote Modal -->
<div class="modal fade" id="addQuoteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Enlist New Inspiration</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/admin/quotes/create">
                <?php echo CSRF::field(); ?>
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="label-graphic">Inspiration Message</label>
                        <textarea class="form-control" name="quote_text" rows="3" required
                            placeholder="Enter the quote text..."></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="label-graphic">Author Alias</label>
                        <input type="text" class="form-control" name="author" required placeholder="e.g. Steve Jobs">
                    </div>
                    <div class="mb-4">
                        <label class="label-graphic">Visual URL (optional)</label>
                        <input type="url" class="form-control" name="image_url"
                            placeholder="https://image-source.com/photo.jpg">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link text-decoration-none text-secondary fw-bold"
                        data-bs-dismiss="modal">ABORT</button>
                    <button type="submit" class="btn-complete-graphic">CONFIRM ENLISTMENT</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>