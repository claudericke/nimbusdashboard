</div>
</footer>

<!-- Activity Summary Modal -->
<div class="modal fade" id="activityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: #1a1d26; border: 1px solid var(--border-main);">
            <div class="modal-header border-bottom border-secondary"
                style="border-color: rgba(255,255,255,0.1) !important;">
                <h5 class="modal-title text-white fw-bold"><i class="fas fa-bell me-2 text-accent-rose"></i>Activity
                    Summary</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" id="activityModalBody">
                <!-- content will be loaded via ajax -->
                <div class="p-4 text-center">
                    <div class="spinner-border text-light" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer p-2 border-top border-secondary justify-content-center"
                style="border-color: rgba(255,255,255,0.1) !important;">
                <button type="button" class="btn btn-link text-decoration-none text-secondary"
                    style="font-size: 0.8rem;" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const activityModal = document.getElementById('activityModal');
    if (activityModal) {
        activityModal.addEventListener('show.bs.modal', function () {
            const modalBody = document.getElementById('activityModalBody');
            
            fetch('/api/activity-logs')
                .then(response => response.json())
                .then(data => {
                    if (data.logs && data.logs.length > 0) {
                        let html = '';
                        data.logs.forEach(log => {
                            html += `
                                <div class="p-3 border-bottom border-secondary" style="border-color: rgba(255,255,255,0.05) !important;">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle ${log.bg_class} d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px;">
                                            <i class="fas ${log.icon} text-white" style="font-size: 0.8rem;"></i>
                                        </div>
                                        <div>
                                            <div class="text-white fw-bold" style="font-size: 0.9rem;">${log.action_type.charAt(0).toUpperCase() + log.action_type.slice(1)}</div>
                                            <div class="text-secondary small">${log.description}</div>
                                        </div>
                                        <span class="ms-auto text-secondary small">${log.time_ago}</span>
                                    </div>
                                </div>
                            `;
                        });
                        modalBody.innerHTML = html;
                    } else {
                        modalBody.innerHTML = `
                            <div class="p-4 text-center">
                                <i class="fas fa-history text-secondary mb-2" style="font-size: 2rem; opacity: 0.5;"></i>
                                <div class="text-secondary">No recent activity found</div>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error fetching activity logs:', error);
                    modalBody.innerHTML = `
                        <div class="p-3 text-center text-danger">
                            <i class="fas fa-exclamation-circle mb-2"></i><br>
                            Failed to load activity
                        </div>
                    `;
                });
        });
    }
});
</script>


<script src="/public/js/sorting.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- <script src="//code.tidio.co/agjwvfyqtdf2zxvwva8yijqjkymuprf1.js" async></script>-->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        if (sidebarToggle && sidebar && sidebarOverlay) {
            sidebarToggle.addEventListener('click', function () {
                sidebar.classList.toggle('show');
                sidebarOverlay.classList.toggle('show');
            });
            sidebarOverlay.addEventListener('click', function () {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            });
        }
        const adminDomainSwitch = document.getElementById('adminDomainSwitch');
        if (adminDomainSwitch) {
            adminDomainSwitch.addEventListener('change', function () {
                if (this.value) {
                    window.location.href = '/switch-domain?id=' + this.value;
                }
            });
        }

        function updateTime() {
            const now = new Date();
            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');
            const seconds = now.getSeconds().toString().padStart(2, '0');
            const timeString = `${hours}:${minutes}:${seconds}`;
            const timeWidget = document.getElementById('time-widget');
            if (timeWidget) {
                timeWidget.textContent = timeString;
            }
        }
        updateTime();
        setInterval(updateTime, 1000);

        document.querySelectorAll('.toggle-password').forEach(function (el) {
            el.addEventListener('click', function () {
                const target = document.querySelector(this.dataset.target);
                const icon = this.querySelector('i');
                if (target.type === 'password') {
                    target.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    target.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });

        <?php if (Session::isSuperuser()): ?>
            let lastTicketIds = [];
            const notificationSound = new Audio('/public/assets/sounds/ding.wav');

            function checkNewTickets() {
                fetch('/tickets/check-new')
                    .then(r => r.json())
                    .then(data => {
                        if (data.new_tickets && data.new_tickets.length > 0) {
                            notificationSound.play().catch(e => console.log('Audio play failed:', e));
                            if (Notification.permission === 'granted') {
                                new Notification('New Support Ticket', {
                                    body: data.new_tickets.length + ' new ticket(s) opened',
                                    icon: 'https://dashboard.driftnimbus.com/assets/images/favicon.ico'
                                });
                            }
                        }
                    })
                    .catch(e => console.log('Ticket check failed:', e));
            }
            if ('Notification' in window && Notification.permission === 'default') {
                Notification.requestPermission();
            }
            setInterval(checkNewTickets, 30000);
            checkNewTickets();
        <?php endif; ?>
    });
</script>
</body>

</html>