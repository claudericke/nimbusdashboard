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
.sidebar{position:fixed;top:0;left:0;height:100vh;width:260px;background:#1e1e1e;padding:1.5rem 0;overflow-y:auto;z-index:1000;transition:transform .3s ease}
@media (max-width:768px){.sidebar{transform:translateX(-100%)}.sidebar.show{transform:translateX(0)}}
.sidebar-logo{padding:0 1.5rem 1.5rem;border-bottom:1px solid #2d2d2d;margin-bottom:1rem}
.sidebar-nav{list-style:none;padding:0;margin:0}
.sidebar-nav-item{margin:.25rem .75rem}
.sidebar-nav-link{display:flex;align-items:center;padding:.75rem 1rem;color:#b0b0b0;text-decoration:none;border-radius:.375rem;transition:all .2s;font-size:.95rem}
.sidebar-nav-link:hover{background:#2a2a2a;color:#fff}
.sidebar-nav-link.active{background:#3a3a3a;color:#fff}
.sidebar-nav-link i{width:20px;margin-right:.75rem;font-size:1rem}
.sidebar-nav-link .chevron{margin-left:auto;font-size:.75rem;transition:transform .2s}
.sidebar-nav-link[aria-expanded="true"] .chevron{transform:rotate(90deg)}
.sidebar-submenu{list-style:none;padding:0;margin:.25rem 0 .5rem 2.5rem}
.sidebar-submenu-link{display:block;padding:.5rem 1rem;color:#909090;text-decoration:none;border-radius:.375rem;font-size:.9rem;transition:all .2s}
.sidebar-submenu-link:hover{background:#2a2a2a;color:#fff}
.sidebar-user{padding:1rem 1.5rem;border-top:1px solid #2d2d2d;margin-top:auto}
.sidebar-user-info{display:flex;align-items:center;color:#b0b0b0;text-decoration:none;padding:.5rem;border-radius:.375rem;transition:background .2s}
.sidebar-user-info:hover{background:#2a2a2a}
.sidebar-user-info img{margin-right:.75rem}
.main-content{margin-left:260px;min-height:100vh;display:flex;flex-direction:column}
@media (max-width:768px){.main-content{margin-left:0}}
.sidebar-toggle{position:fixed;top:1rem;left:1rem;z-index:1001;background:#1e1e1e;border:1px solid #3a3a3a;color:#fff;padding:.5rem .75rem;border-radius:.375rem;cursor:pointer}
@media (min-width:769px){.sidebar-toggle{display:none}}
.sidebar-overlay{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.5);z-index:999}
@media (max-width:768px){.sidebar-overlay.show{display:block}}
</style>

<button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <a href="/dashboard">
            <img src="https://hosting.driftnimbus.com/wp-content/uploads/2025/02/nimbus-logo-horizontal-white.svg" alt="Drift Nimbus" style="width:100%;max-width:180px">
        </a>
    </div>
    
    <?php if ($isSuperuser): ?>
    <div style="padding:0 1.5rem 1rem">
        <label class="form-label text-white" style="font-size:.85rem;margin-bottom:.5rem">Switch Account</label>
        <select class="form-select form-select-sm" id="adminDomainSwitch" style="background:#2a2a2a;color:#fff;border:1px solid #3a3a3a">
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
    
    <div class="sidebar-user" style="border-bottom:1px solid #2d2d2d;padding-bottom:1rem;margin-bottom:1rem">
        <a href="/settings" class="sidebar-user-info">
            <img src="<?php echo h($profilePicture); ?>" class="rounded-circle" width="36" height="36" alt="Profile">
            <div>
                <div style="font-size:.9rem;color:#fff"><?php echo h($profileName); ?></div>
                <div style="font-size:.75rem;color:#707070"><?php echo h($domain); ?></div>
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
            <a href="#emailsSubmenu" class="sidebar-nav-link <?php echo $currentPage === 'emails' ? 'active' : ''; ?>" data-bs-toggle="collapse" aria-expanded="<?php echo $currentPage === 'emails' ? 'true' : 'false'; ?>">
                <i class="fas fa-envelope"></i><span>Emails</span><i class="fas fa-chevron-right chevron"></i>
            </a>
            <ul class="collapse sidebar-submenu <?php echo $currentPage === 'emails' ? 'show' : ''; ?>" id="emailsSubmenu">
                <li><a href="/emails" class="sidebar-submenu-link">Email Accounts</a></li>
                <li><a href="/emails/create" class="sidebar-submenu-link">Add Email</a></li>
                <li><a href="https://mail.driftnimbus.com" target="_blank" class="sidebar-submenu-link">Nimbus Mail <i class="fas fa-external-link-alt" style="font-size:.75rem;margin-left:.25rem"></i></a></li>
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
            <a href="#ticketsSubmenu" class="sidebar-nav-link <?php echo $currentPage === 'tickets' ? 'active' : ''; ?>" data-bs-toggle="collapse" aria-expanded="<?php echo $currentPage === 'tickets' ? 'true' : 'false'; ?>">
                <i class="fas fa-ticket-alt"></i><span>Tickets</span><i class="fas fa-chevron-right chevron"></i>
            </a>
            <ul class="collapse sidebar-submenu <?php echo $currentPage === 'tickets' ? 'show' : ''; ?>" id="ticketsSubmenu">
                <li><a href="/tickets/new" class="sidebar-submenu-link">New Ticket</a></li>
                <li><a href="/tickets/open" class="sidebar-submenu-link">Open Tickets</a></li>
                <li><a href="/tickets/awaiting" class="sidebar-submenu-link">Awaiting Response</a></li>
                <li><a href="/tickets/closed" class="sidebar-submenu-link">Closed Tickets</a></li>
            </ul>
        </li>
        <li class="sidebar-nav-item">
            <a href="#adminSubmenu" class="sidebar-nav-link <?php echo $currentPage === 'admin' ? 'active' : ''; ?>" data-bs-toggle="collapse" aria-expanded="<?php echo $currentPage === 'admin' ? 'true' : 'false'; ?>">
                <i class="fas fa-user-shield"></i><span>Admin</span><i class="fas fa-chevron-right chevron"></i>
            </a>
            <ul class="collapse sidebar-submenu <?php echo $currentPage === 'admin' ? 'show' : ''; ?>" id="adminSubmenu">
                <li><a href="/admin/users" class="sidebar-submenu-link">Manage Users</a></li>
                <li><a href="/admin/quotes" class="sidebar-submenu-link">Manage Quotes</a></li>
                <li><a href="/admin/permissions" class="sidebar-submenu-link">Manage Permissions</a></li>
            </ul>
        </li>
        <?php endif; ?>
        <li class="sidebar-nav-item">
            <a href="/logout" class="sidebar-nav-link">
                <i class="fas fa-sign-out-alt"></i><span>Log Off</span>
            </a>
        </li>
    </ul>
</aside>
