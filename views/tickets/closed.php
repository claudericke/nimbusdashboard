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
            <div class="bento-grid">
                <div class="card-graphic full-width reveal-up">
                    <span class="label-graphic">TICKETS</span>
                    <h2 class="mb-4"><?php echo h($title ?? 'Closed Tickets'); ?></h2>

                    <?php if (!empty($tickets)): ?>
                        <div class="table-responsive">
                            <table class="table-custom">
                                <thead>
                                    <tr>
                                        <th style="width:60px">ARCHIVE</th>
                                        <th>Ticket Alias <span class="sort-icon"></span></th>
                                        <th>Tags <span class="sort-icon"></span></th>
                                        <th>Initiated <span class="sort-icon"></span></th>
                                        <th>Deadline <span class="sort-icon"></span></th>
                                        <th>Operatives <span class="sort-icon"></span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tickets as $card): ?>
                                        <tr>
                                            <td class="text-center">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input class="form-check-input" type="checkbox"
                                                        style="width:24px; height:24px; cursor:pointer;"
                                                        onclick="confirmClose('<?php echo h($card['id']); ?>','<?php echo h($card['name']); ?>')">
                                                </div>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0)"
                                                    onclick="openCardModal('<?php echo h($card['id']); ?>')"
                                                    class="fw-bold text-white text-decoration-none hover-glow">
                                                    <?php echo h(formatTicketName($card['name'])); ?>
                                                </a>
                                            </td>
                                            <td>
                                                <?php if (!empty($card['labels'])): ?>
                                                    <?php foreach ($card['labels'] as $label): ?>
                                                        <span class="badge-graphic small px-2 py-1 me-1"
                                                            style="background-color:<?php echo h($label['color']); ?>33; color:<?php echo h($label['color']); ?>; border:1px solid <?php echo h($label['color']); ?>55;">
                                                            <?php echo h($label['name'] ?: $label['color']); ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <span class="text-muted small">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-secondary small">
                                                <?php echo !empty($card['start']) ? date('M d, Y', strtotime($card['start'])) : '—'; ?>
                                            </td>
                                            <td
                                                class="small <?php echo !empty($card['due']) && strtotime($card['due']) < time() ? 'text-accent-rose fw-bold' : 'text-secondary'; ?>">
                                                <?php echo !empty($card['due']) ? date('M d, Y', strtotime($card['due'])) : '—'; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($card['members'])): ?>
                                                    <div class="d-flex align-items-center">
                                                        <?php foreach ($card['members'] as $member): ?>
                                                            <span class="badge-graphic text-accent-indigo me-1"
                                                                style="font-size:0.65rem">
                                                                <?php echo h($member['fullName'] ?? $member['username']); ?>
                                                            </span>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted small">STANDBY</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle mb-3 fs-1 opacity-25"></i>
                            <p class="text-secondary fs-5">Zero active missions detected. Systems clear.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Modals -->
<div class="modal fade" id="cardModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cardModalTitle">Transmission Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="cardModalBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-accent-indigo"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="closeTicketModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="/tickets/close" class="modal-content">
            <?php echo CSRF::field(); ?>
            <input type="hidden" name="card_id" id="close_card_id">
            <div class="modal-header">
                <h5 class="modal-title text-accent-rose">ARCHIVE TICKET</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-white fs-5">You are about to archive:</p>
                <p class="h4 fw-bold text-accent-emerald mb-4" id="close_ticket_name"></p>
                <p class="text-secondary small">This will close all ticket activity.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link text-decoration-none text-secondary fw-bold"
                    data-bs-dismiss="modal">CANCEL</button>
                <button type="submit" class="btn-complete-graphic">ARCHIVE TICKET</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCardModal(cardId) {
        const modal = new bootstrap.Modal(document.getElementById('cardModal'));
        modal.show();
        fetch('/tickets/card/' + cardId)
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const card = data.card;
                    document.getElementById('cardModalTitle').textContent = card.name;
                    let html = '<div class="reveal-up">';

                    if (card.desc) {
                        html += '<div class="mb-4"><label class="label-graphic">Manifest Description</label><div class="p-3 bg-vibrant-dark rounded-4 text-white" style="white-space:pre-wrap; border:1px solid var(--border-main)">' + escapeHtml(card.desc) + '</div></div>';
                    }

                    html += '<div class="row mb-4">';
                    if (card.due) {
                        html += '<div class="col-md-6"><label class="label-graphic">Strategic Deadline</label><div class="text-white h5 fw-bold">' + new Date(card.due).toLocaleString() + '</div></div>';
                    }
                    if (card.labels && card.labels.length > 0) {
                        html += '<div class="col-md-6"><label class="label-graphic">Tags</label><div>';
                        card.labels.forEach(label => {
                            html += '<span class="badge-graphic me-2" style="background-color:' + label.color + '; color:white;">' + escapeHtml(label.name || label.color) + '</span>';
                        });
                        html += '</div></div>';
                    }
                    html += '</div>';

                    html += '<a href="' + card.url + '" target="_blank" class="btn-complete-graphic d-inline-block text-decoration-none text-center" style="width:200px">VIEW SOURCE</a>';
                    html += '</div>';
                    document.getElementById('cardModalBody').innerHTML = html;
                }
            });
    }

    function confirmClose(cardId, cardName) {
        document.getElementById('close_card_id').value = cardId;
        document.getElementById('close_ticket_name').textContent = cardName;
        new bootstrap.Modal(document.getElementById('closeTicketModal')).show();
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>