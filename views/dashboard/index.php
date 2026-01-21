<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <main class="flex-grow-1 p-3 p-md-5">
        <div class="container-fluid">
            <?php require __DIR__ . '/../layouts/alerts.php'; ?>
            <!-- Header Section -->
            <div
                class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-5 reveal-up">
                <div>
                    <h1 class="display-4 fw-bold mb-1">Hello, <?php echo h($profileName); ?></h1>
                    <p class="text-secondary mb-0 fs-5">Experience total control in vibrant clarity.</p>
                </div>
                <div class="text-md-end mt-4 mt-md-0">
                    <div id="zimbabwe-time" class="time-display-graphic">00:00:00</div>
                    <div class="subheading-graphic fw-bold text-uppercase ls-2">HARARE • ZIMBABWE</div>
                </div>
            </div>

            <!-- Bento Grid -->
            <div class="bento-grid reveal-grid">

                <div
                    class="card-graphic reveal-up <?php echo $serverStatus ? 'bg-vibrant-emerald' : 'bg-vibrant-rose'; ?>">
                    <span class="label-graphic">Network Pulse</span>
                    <div class="d-flex align-items-center">
                        <div class="status-dot-pulse me-3" style="background: white;"></div>
                        <h2 class="heading-graphic mb-0"><?php echo $serverStatus ? 'Online' : 'Warning'; ?></h2>
                    </div>
                    <div class="mt-2">
                        <p class="small opacity-75 mb-0">Node: <?php echo h($_ENV['WHM_HOST'] ?? 'nimbus1'); ?></p>
                    </div>
                </div>

                <div class="card-graphic bg-vibrant-indigo reveal-up">
                    <span class="label-graphic">Current Tier</span>
                    <h2 class="heading-graphic mb-1"><?php echo h($packageName); ?></h2>
                    <div>
                        <span class="badge-graphic" style="background: rgba(255,255,255,0.15)">Elite Tier
                            Subscription</span>
                    </div>
                </div>

                <!-- 3. Weather (Small) -->
                <div class="card-graphic bg-vibrant-dark reveal-up">
                    <span class="label-graphic">Local Weather</span>
                    <div class="weather-graphic">
                        <?php if ($weather && !empty($weatherIcon)): ?>
                            <img src="<?php echo $weatherIcon; ?>" class="weather-icon-large" alt="Weather">
                            <div>
                                <h2 class="heading-graphic mb-0"><?php echo round($weather['temperature']); ?>°C</h2>
                                <p class="subheading-graphic mb-0">Clear Skies</p>
                            </div>
                        <?php else: ?>
                            <div class="py-2">
                                <h2 class="heading-graphic mb-0">N/A</h2>
                                <p class="subheading-graphic mb-0">Syncing Forecast...</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card-graphic bg-vibrant-dark reveal-up">
                    <span class="label-graphic">Cloud Vault</span>
                    <?php
                    $limit = $diskUsage['limit'] ?? 0;
                    $used = $diskUsage['used'] ?? 0;
                    $percentage = $diskUsage['percentage'] ?? 0;

                    // Show widget if we have any data (even 0 used)
                    // If both are 0, we might still be awaiting sync or it's a fresh account
                    if ($limit > 0 || $used > 0 || (isset($diskUsage) && is_array($diskUsage))): ?>
                        <?php
                        $barColor = $percentage > 85 ? 'var(--accent-rose)' : 'var(--accent-indigo)';
                        ?>
                        <div class="d-flex justify-content-between align-items-end mb-1">
                            <h2 class="heading-graphic mb-0"><?php echo $percentage; ?>%</h2>
                            <span class="subheading-graphic mb-1"><?php echo $used; ?>MB /
                                <?php echo $limit > 0 ? $limit . 'MB' : '∞'; ?></span>
                        </div>
                        <div class="progress-graphic">
                            <div class="progress-graphic-bar"
                                style="width: <?php echo $percentage; ?>%; background: <?php echo $barColor; ?>;"></div>
                        </div>
                        <p class="small text-secondary mb-0">Storage:
                            <?php echo $percentage > 85 ? 'Critical' : 'Healthy'; ?>
                        </p>
                    <?php else: ?>
                        <div class="py-2">
                            <h2 class="heading-graphic mb-0">N/A</h2>
                            <p class="subheading-graphic mb-0">Awaiting Sync...</p>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($isSuperuser): ?>
                    <!-- 5. Support Tickets (Wide - span 2) -->
                    <div class="card-graphic span-2 bg-vibrant-dark reveal-up">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <span class="label-graphic mb-1">Active Missions</span>
                                <h3 class="fw-bold mb-0">Your Tickets</h3>
                            </div>
                            <a href="/tickets/open"
                                class="btn btn-sm btn-link badge-graphic text-decoration-none border-0">EXPLORE ALL</a>
                        </div>
                        <div class="listing-container">
                            <?php if (!empty($openTickets)): ?>
                                <?php foreach (array_slice($openTickets, 0, 3) as $ticket):
                                    $ticketName = formatTicketName($ticket['name']);
                                    ?>
                                    <div class="listing-item-graphic">
                                        <div class="me-3 overflow-hidden">
                                            <div class="fw-bold text-truncate fs-5" style="color: white;">
                                                <?php echo h($ticketName); ?>
                                            </div>
                                            <div class="subheading-graphic" style="font-size: 0.8rem;">
                                                <?php echo h($ticket['list_name'] ?? 'In Queue'); ?>
                                            </div>
                                        </div>
                                        <form method="POST" action="/tickets/close">
                                            <?php echo CSRF::field(); ?>
                                            <input type="hidden" name="card_id" value="<?php echo h($ticket['id']); ?>">
                                            <button type="submit" class="btn-complete-graphic">COMPLETE</button>
                                        </form>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-check-circle mb-3 fs-1 opacity-25"></i>
                                    <p class="text-secondary fs-5">All systems are nominal. No active tickets.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- 6. Recent Invoices (Wide - span 2) -->
                <div class="card-graphic span-2 bg-vibrant-dark reveal-up">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <span class="label-graphic mb-1">Treasury</span>
                            <h3 class="fw-bold mb-0">Financial History</h3>
                        </div>
                        <a href="/billing"
                            class="btn btn-sm btn-link badge-graphic text-decoration-none border-0">INVOICES</a>
                    </div>
                    <div class="listing-container">
                        <?php if (!empty($invoices)): ?>
                            <?php foreach (array_slice($invoices, 0, 3) as $inv): ?>
                                <div class="listing-item-graphic">
                                    <div>
                                        <div class="fw-bold fs-5" style="color: white;"><?php echo h($inv['invoice_number']); ?>
                                        </div>
                                        <div class="subheading-graphic" style="font-size: 0.8rem;">
                                            <?php echo h($inv['date']); ?>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold fs-5 mb-1" style="color: white;">
                                            <?php echo h(($inv['currency_symbol'] ?? '$') . $inv['total']); ?>
                                        </div>
                                        <span
                                            class="badge-graphic <?php echo $inv['status'] === 'paid' ? 'text-accent-emerald' : 'text-accent-amber'; ?>">
                                            <?php echo strtoupper(h($inv['status'])); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-file-invoice-dollar mb-3 fs-1 opacity-25"></i>
                                <p class="text-secondary fs-5">No recent activity detected.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card-graphic bg-vibrant-dark reveal-up">
                    <span class="label-graphic">Fortress Security</span>
                    <div class="flex-grow-1">
                        <?php if (!empty($sslCerts)): ?>
                            <?php foreach (array_slice($sslCerts, 0, 2) as $cert): ?>
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-shield-alt text-accent-emerald me-3 fs-3"></i>
                                    <div class="overflow-hidden">
                                        <div class="fw-bold text-truncate" style="color: white; font-size: 0.9rem;">
                                            <?php echo h($cert['domain'] ?? $domain); ?>
                                        </div>
                                        <div class="subheading-graphic" style="font-size: 0.75rem;">TLS Encryption Active</div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle text-accent-amber me-3 fs-3"></i>
                                <div>
                                    <div class="fw-bold" style="color: white;">Unprotected</div>
                                    <div class="subheading-graphic">Request SSL Cert</div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <p class="small text-secondary mb-0">Monitoring Active</p>
                </div>

                <!-- 8. Quote of the Day (Wide - span 3) -->
                <div class="card-graphic span-3 d-flex flex-column justify-content-center reveal-up"
                    style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('<?php echo h($quote['image_url'] ?? ''); ?>'); background-size: cover; background-position: center; min-height: 250px;">
                    <div class="text-center p-4">
                        <i class="fas fa-quote-left mb-3 fs-2 opacity-50 text-accent-indigo"></i>
                        <h2 class="fw-bold mb-3 heading-graphic px-md-5" style="font-size: 1.75rem; line-height: 1.3;">
                            "<?php echo h($quote['quote_text'] ?? 'Innovation distinguishes between a leader and a follower.'); ?>"
                        </h2>
                        <div class="subheading-graphic fw-bold text-uppercase ls-3"
                            style="color: var(--accent-indigo);">— <?php echo h($quote['author'] ?? 'Steve Jobs'); ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>
</div>

<script>
    function updateZimbabweTime() {
        const options = {
            timeZone: 'Africa/Harare',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        };
        const formatter = new Intl.DateTimeFormat([], options);
        document.getElementById('zimbabwe-time').textContent = formatter.format(new Date());
    }

    setInterval(updateZimbabweTime, 1000);
    updateZimbabweTime();
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>