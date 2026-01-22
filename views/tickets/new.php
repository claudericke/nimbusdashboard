<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <main class="flex-grow-1 p-3 p-md-5">
        <div class="container-fluid">
            <div class="bento-grid">
                <div class="card-graphic full-width reveal-up">
                    <span class="label-graphic">SUPPORT TASKS</span>
                    <h2 class="mb-4">Create Ticket</h2>

                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <p class="text-white fs-5 mb-4">Create a new ticket on Trello. Tickets logged on Trello will
                                be shown on the dashboard.</p>

                            <div class="p-4 bg-vibrant-dark rounded-4 mb-5"
                                style="border: 1px solid var(--border-main);">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fab fa-trello text-accent-indigo fs-3 me-3"></i>
                                    <h3 class="h5 fw-bold mb-0 text-white">Trello Integrated Workflow</h3>
                                </div>
                                <p class="text-secondary small mb-0">Our support infrastructure is powered by Trello.
                                    Clicking below will teleport you to the operational board where you can append a new
                                    card to the backlog.</p>
                            </div>

                            <a href="https://trello.com" target="_blank"
                                class="btn-complete-graphic shadow-glow text-decoration-none d-inline-block text-center"
                                style="min-width: 250px;">
                                OPEN TRELLO <i class="fas fa-external-link-alt ms-2"></i>
                            </a>
                        </div>
                        <div class="col-lg-4 d-none d-lg-block text-center">
                            <i class="fas fa-ticket-alt opacity-10" style="font-size: 12rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>