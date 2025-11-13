    <footer class="text-center py-3 navbar-top mt-auto">
        <div class="container-fluid">
            <span class="text-white">© <?php echo date('Y'); ?>. Powered By Drift Nimbus. v<?php echo h(env('APP_VERSION', '1.0.0')); ?></span>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- <script src="//code.tidio.co/agjwvfyqtdf2zxvwva8yijqjkymuprf1.js" async></script>-->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            if (sidebarToggle && sidebar && sidebarOverlay) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                    sidebarOverlay.classList.toggle('show');
                });
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                });
            }
            const adminDomainSwitch = document.getElementById('adminDomainSwitch');
            if (adminDomainSwitch) {
                adminDomainSwitch.addEventListener('change', function() {
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

            document.querySelectorAll('.toggle-password').forEach(function(el) {
                el.addEventListener('click', function() {
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