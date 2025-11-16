<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <main class="flex-grow-1 p-3 p-md-5">
        <div class="container-fluid">
            <br>
            <div class="bento-grid">
                <div class="card full-width p-4">
                    <div class="card-body">
                        <h2 class="card-title text-highlight text-white mb-4">Open Support Tickets</h2>
                        <?php if(!empty($tickets)): ?>
                        <div class="table-responsive">
                            <table class="table-custom">
                                <thead>
                                    <tr>
                                        <th style="width:40px"></th>
                                        <th>Ticket</th>
                                        <th>Labels</th>
                                        <th>Start Date</th>
                                        <th>Due Date</th>
                                        <th>Assigned To</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($tickets as $card): ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" onclick="confirmClose('<?php echo h($card['id']); ?>','<?php echo h($card['name']); ?>')" style="width:20px;height:20px;cursor:pointer">
                                        </td>
                                        <td><a href="javascript:void(0)" onclick="openCardModal('<?php echo h($card['id']); ?>')" class="text-white text-decoration-none"><?php echo h($card['name']); ?></a></td>
                                        <td>
                                            <?php if(!empty($card['labels'])): ?>
                                                <?php foreach($card['labels'] as $label): ?>
                                                <span class="badge me-1" style="background-color:<?php echo h($label['color']); ?>"><?php echo h($label['name']?:$label['color']); ?></span>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                            <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo !empty($card['start'])?date('M d, Y',strtotime($card['start'])):'<span class="text-muted">-</span>'; ?></td>
                                        <td><?php echo !empty($card['due'])?date('M d, Y',strtotime($card['due'])):'<span class="text-muted">-</span>'; ?></td>
                                        <td>
                                            <?php if(!empty($card['members'])): ?>
                                                <?php foreach($card['members'] as $member): ?>
                                                <span class="badge bg-secondary me-1"><?php echo h($member['fullName']??$member['username']); ?></span>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                            <span class="text-muted">Unassigned</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <p class="text-white">No open tickets.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<div class="modal fade" id="cardModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="cardModalTitle">Loading...</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="cardModalBody">
                <div class="text-center"><div class="spinner-border text-primary"></div></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="closeTicketModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="/tickets/close" class="modal-content">
            <?php echo CSRF::field(); ?>
            <input type="hidden" name="card_id" id="close_card_id">
            <div class="modal-header">
                <h5 class="modal-title text-white">Close Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-white">Are you sure you want to close this ticket?</p>
                <p class="text-white fw-bold" id="close_ticket_name"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Close Ticket</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCardModal(cardId){
    const modal=new bootstrap.Modal(document.getElementById('cardModal'));
    modal.show();
    fetch('/tickets/card/'+cardId)
        .then(r=>r.json())
        .then(data=>{
            if(data.success){
                const card=data.card;
                document.getElementById('cardModalTitle').textContent=card.name;
                let html='';
                if(card.desc)html+='<div class="mb-3"><h6 class="text-white">Description</h6><p class="text-white" style="white-space:pre-wrap">'+escapeHtml(card.desc)+'</p></div>';
                if(card.due)html+='<div class="mb-3"><h6 class="text-white">Due Date</h6><p class="text-white">'+new Date(card.due).toLocaleString()+'</p></div>';
                if(card.labels&&card.labels.length>0){
                    html+='<div class="mb-3"><h6 class="text-white">Labels</h6>';
                    card.labels.forEach(label=>{
                        html+='<span class="badge me-2" style="background-color:'+label.color+'">'+escapeHtml(label.name||label.color)+'</span>';
                    });
                    html+='</div>';
                }
                html+='<a href="'+card.url+'" target="_blank" class="btn btn-primary">Open in Trello</a>';
                document.getElementById('cardModalBody').innerHTML=html;
            }
        });
}
function confirmClose(cardId,cardName){
    document.getElementById('close_card_id').value=cardId;
    document.getElementById('close_ticket_name').textContent=cardName;
    new bootstrap.Modal(document.getElementById('closeTicketModal')).show();
}
function escapeHtml(text){
    const div=document.createElement('div');
    div.textContent=text;
    return div.innerHTML;
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
