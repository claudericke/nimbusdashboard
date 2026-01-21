<?php
$currentPage = $_GET['page'] ?? 'dashboard';
$isSuperuser = Session::isSuperuser();
$userRole = Session::getUserRole();
$domain = Session::getDomain();
$username = Session::getUsername();
$profileName = Session::get('profile_name', $username);
$profilePicture = Session::get('profile_picture', 'https://placehold.co/40x40/64748b/e2e8f0?text=PFP');
?>
<style>
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 280px;
        background: #0b0d12;
        padding: 2rem 0;
        overflow-y: auto;
        z-index: 1000;
        transition: transform .3s ease;
        border-right: 1px solid var(--border-main);
    }

    @media (max-width:768px) {
        .sidebar {
            transform: translateX(-100%);
        }

        .sidebar.show {
            transform: translateX(0);
        }
    }

    .sidebar-logo {
        padding: 0 2rem 2.5rem;
        margin-bottom: 1rem;
    }

    .sidebar-nav {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .sidebar-nav-item {
        margin: 0.25rem 1rem;
    }

    .sidebar-nav-link {
        display: flex;
        align-items: center;
        padding: 1rem 1.25rem;
        color: var(--text-secondary);
        text-decoration: none;
        border-radius: 1rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        font-size: 0.95rem;
        font-weight: 500;
    }

    .sidebar-nav-link:hover {
        color: var(--text-primary);
        background: rgba(255, 255, 255, 0.03);
    }

    .sidebar-nav-link.active {
        background: var(--grad-indigo);
        color: #ffffff !important;
        font-weight: 700;
        box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3);
    }

    .sidebar-nav-link i {
        width: 24px;
        margin-right: 1rem;
        font-size: 1.1rem;
        text-align: center;
    }

    .sidebar-nav-link .chevron {
        margin-left: auto;
        font-size: .75rem;
        transition: transform .3s;
        opacity: 0.5;
    }

    .sidebar-nav-link[aria-expanded="true"] .chevron {
        transform: rotate(90deg);
    }

    .sidebar-submenu {
        list-style: none;
        padding: 0;
        margin: 0.5rem 0 0.5rem 3.5rem;
    }

    .sidebar-submenu-link {
        display: block;
        padding: 0.6rem 1rem;
        color: var(--text-secondary);
        text-decoration: none;
        border-radius: 0.75rem;
        font-size: 0.85rem;
        transition: all 0.2s;
    }

    .sidebar-submenu-link:hover {
        color: var(--text-primary);
        background: rgba(255, 255, 255, 0.03);
    }

    .sidebar-user {
        padding: 1.5rem 2rem;
        border-top: 1px solid var(--border-main);
        margin-top: auto;
    }

    .sidebar-user-info {
        display: flex;
        align-items: center;
        color: var(--text-secondary);
        text-decoration: none;
        padding: 0.75rem;
        border-radius: 1rem;
        transition: all 0.3s ease;
    }

    .sidebar-user-info:hover {
        background: rgba(255, 255, 255, 0.03);
    }

    .sidebar-user-info img {
        margin-right: 1rem;
        border: 2px solid var(--border-main);
    }

    .main-content {
        margin-left: 280px;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    @media (max-width:768px) {
        .main-content {
            margin-left: 0;
        }
    }

    .sidebar-toggle {
        position: fixed;
        top: 1.5rem;
        left: 1.5rem;
        z-index: 1001;
        background: #0b0d12;
        border: 1px solid var(--border-main);
        color: #fff;
        padding: 0.75rem 1rem;
        border-radius: 1rem;
        cursor: pointer;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.4);
    }

    @media (min-width:769px) {
        .sidebar-toggle {
            display: none;
        }
    }

    .sidebar-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, .6);
        backdrop-filter: blur(4px);
        z-index: 999;
    }

    @media (max-width:768px) {
        .sidebar-overlay.show {
            display: block;
        }
    }

    /* Account Switcher Graphic Styling */
    .account-switcher {
        padding: 0 1.5rem 1.5rem;
    }

    .account-switcher label {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        opacity: 0.6;
        margin-left: 0.5rem;
        margin-bottom: 0.5rem;
        display: block;
    }

    .account-switcher select {
        background: #11141d;
        border: 1px solid var(--border-main);
        border-radius: 1rem;
        padding: 0.6rem 1rem;
        color: white;
        font-size: 0.85rem;
    }
</style>

<button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <a href="/dashboard">
            <img src="https://hosting.driftnimbus.com/wp-content/uploads/2025/02/nimbus-logo-horizontal-white.svg"
                alt="Drift Nimbus" style="width:100%;max-width:180px">
        </a>
    </div>

    <?php if ($isSuperuser): ?>
        <div class="account-switcher">
            <label>Node Selection</label>
            <select class="form-select form-select-sm" id="adminDomainSwitch">
                <option value="">Select domain...</option>
                <?php
                $userModel = new User();
                $allUsers = $userModel->all();
                foreach ($allUsers as $user) {
                    $selected = ($user['domain'] === $domain && $user['cpanel_username'] === $username) ? 'selected' : '';
                    echo '<option value="' . h($user['id']) . '" ' . $selected . '>' . h($user['domain']) . ' (' . h($user['cpanel_username']) . ')</option>';
                }
                ?>
            </select>
        </div>
    <?php endif; ?>

    <div class="sidebar-user" style="border-bottom:1px solid var(--border-main);padding-bottom:2rem;margin-bottom:1rem">
        <a href="/settings" class="sidebar-user-info">
            <img src="<?php echo h($profilePicture); ?>" class="rounded-circle" width="40" height="40" alt="Profile">
            <div class="overflow-hidden">
                <div style="font-size:1rem;color:#fff;font-weight:700;line-height:1.2"><?php echo h($profileName); ?>
                </div>
                <div style="font-size:0.75rem;color:var(--text-secondary);"><?php echo h($domain); ?></div>
            </div>
        </a>
    </div>

    <ul class="sidebar-nav">
        <li class="sidebar-nav-item">
            <a href="/dashboard" class="sidebar-nav-link <?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>">
                <i class="fas fa-th-large"></i><span>Dashboard</span>
            </a>
        </li>
        <li class="sidebar-nav-item">
            <a href="/domains" class="sidebar-nav-link <?php echo $currentPage === 'domains' ? 'active' : ''; ?>">
                <i class="fas fa-globe"></i><span>Domains</span>
            </a>
        </li>
        <li class="sidebar-nav-item">
            <a href="#emailsSubmenu"
                class="sidebar-nav-link <?php echo in_array($currentPage, ['emails', 'emails/create']) ? 'active' : ''; ?>"
                data-bs-toggle="collapse"
                aria-expanded="<?php echo in_array($currentPage, ['emails', 'emails/create']) ? 'true' : 'false'; ?>">
                <i class="fas fa-envelope"></i><span>Emails</span><i class="fas fa-chevron-right chevron"></i>
            </a>
            <ul class="collapse sidebar-submenu <?php echo in_array($currentPage, ['emails', 'emails/create']) ? 'show' : ''; ?>"
                id="emailsSubmenu">
                <li><a href="/emails" class="sidebar-submenu-link">Email Accounts</a></li>
                <li><a href="/emails/create" class="sidebar-submenu-link">Add Email</a></li>
                <li><a href="https://mail.driftnimbus.com" target="_blank" class="sidebar-submenu-link">Nimbus Mail <i
                            class="fas fa-external-link-alt" style="font-size:.75rem;margin-left:.25rem"></i></a></li>
            </ul>
        </li>
        <li class="sidebar-nav-item">
            <a href="/ssl" class="sidebar-nav-link <?php echo $currentPage === 'ssl' ? 'active' : ''; ?>">
                <i class="fas fa-lock"></i><span>SSL Certificates</span>
            </a>
        </li>
        <li class="sidebar-nav-item">
            <a href="/billing" class="sidebar-nav-link <?php echo $currentPage === 'billing' ? 'active' : ''; ?>">
                <i class="fas fa-file-invoice"></i><span>Billing</span>
            </a>
        </li>
        <li class="sidebar-nav-item">
            <a href="/settings" class="sidebar-nav-link <?php echo $currentPage === 'settings' ? 'active' : ''; ?>">
                <i class="fas fa-cog"></i><span>Settings</span>
            </a>
        </li>
        <?php if ($isSuperuser): ?>
            <li class="sidebar-nav-item">
                <a href="#ticketsSubmenu"
                    class="sidebar-nav-link <?php echo str_starts_with($currentPage, 'tickets') ? 'active' : ''; ?>"
                    data-bs-toggle="collapse"
                    aria-expanded="<?php echo str_starts_with($currentPage, 'tickets') ? 'true' : 'false'; ?>">
                    <i class="fas fa-ticket-alt"></i><span>Tickets</span><i class="fas fa-chevron-right chevron"></i>
                </a>
                <ul class="collapse sidebar-submenu <?php echo str_starts_with($currentPage, 'tickets') ? 'show' : ''; ?>"
                    id="ticketsSubmenu">
                    <li><a href="/tickets/new" class="sidebar-submenu-link">New Ticket</a></li>
                    <li><a href="/tickets/open" class="sidebar-submenu-link">Open Tickets</a></li>
                    <li><a href="/tickets/awaiting" class="sidebar-submenu-link">Awaiting Response</a></li>
                    <li><a href="/tickets/closed" class="sidebar-submenu-link">Closed Tickets</a></li>
                </ul>
            </li>
            <li class="sidebar-nav-item">
                <a href="#adminSubmenu"
                    class="sidebar-nav-link <?php echo str_starts_with($currentPage, 'admin') ? 'active' : ''; ?>"
                    data-bs-toggle="collapse"
                    aria-expanded="<?php echo str_starts_with($currentPage, 'admin') ? 'true' : 'false'; ?>">
                    <i class="fas fa-user-shield"></i><span>Admin</span><i class="fas fa-chevron-right chevron"></i>
                </a>
                <ul class="collapse sidebar-submenu <?php echo str_starts_with($currentPage, 'admin') ? 'show' : ''; ?>"
                    id="adminSubmenu">
                    <li><a href="/admin/users" class="sidebar-submenu-link">Manage Users</a></li>
                    <li><a href="/admin/quotes" class="sidebar-submenu-link">Manage Quotes</a></li>
                    <li><a href="/admin/permissions" class="sidebar-submenu-link">Manage Permissions</a></li>
                </ul>
            </li>
        <?php endif; ?>
        <li class="sidebar-nav-item" style="margin-top:2rem">
            <a href="/logout" class="sidebar-nav-link" style="color:var(--accent-rose)">
                <i class="fas fa-sign-out-alt"></i><span>Log Off</span>
            </a>
        </li>
    </ul>
</aside>