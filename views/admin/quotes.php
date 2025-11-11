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
            
            <div class="bento-grid">
                <div class="card full-width p-4">
                    <div class="card-body">
                        <h2 class="card-title text-highlight text-white mb-4">Admin: Manage Quotes</h2>
                        <ul class="nav nav-tabs mb-4">
                            <li class="nav-item"><a class="nav-link" href="/admin/users">Manage Users</a></li>
                            <li class="nav-item"><a class="nav-link active" href="/admin/quotes">Manage Quotes</a></li>
                            <li class="nav-item"><a class="nav-link" href="/admin/permissions">Manage Permissions</a></li>
                        </ul>
                        
                        <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#addQuoteModal">Add New Quote</button>
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
                                    <?php foreach($quotes as $quote): ?>
                                    <tr>
                                        <td><?php echo h($quote['quote_text']); ?></td>
                                        <td><?php echo h($quote['author']); ?></td>
                                        <td>
                                            <form method="POST" action="/admin/quotes/delete" style="display:inline">
                                                <?php echo CSRF::field(); ?>
                                                <input type="hidden" name="delete_quote" value="<?php echo h($quote['id']); ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this quote?')">Delete</button>
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
        </div>
    </main>
</div>

<div class="modal fade" id="addQuoteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white">Add New Quote</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/admin/quotes/create">
                <?php echo CSRF::field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-white">Quote Text</label>
                        <textarea class="form-control" name="quote_text" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Author</label>
                        <input type="text" class="form-control" name="author" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">Image URL (optional)</label>
                        <input type="url" class="form-control" name="image_url">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Quote</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
