<?php
$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
?>
<aside class="sidebar" id="sidenav-main">
    <div class="sidebar-header">
        <i aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand" href="javascript:void(0);" onclick="window.location.href='index.php';">
            <img src="https://i.ibb.co/6yw7WFr/logos.png" alt="Logo" class="logo">
        </a>
        <span class="brand-name"></span>
    </div>

    <hr class="divider">

    <nav class="navbar-nav">
        <ul>
            <!-- Common Links -->
            <li class="nav-item">
                <a class="nav-link" href="javascript:void(0);" onclick="window.open('screenshare.php', '_blank');">
                    <i class="material-icons">tv</i>
                    <span>Screenshare</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="javascript:void(0);" onclick="window.location.href='index.php';">
                    <i class="material-icons">receipt_long</i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="javascript:void(0);" onclick="window.location.href='announcement.php';">
                    <i class="material-icons">record_voice_over</i>
                    <span>Announcement</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="javascript:void(0);" onclick="window.location.href='siren.php';">
                    <i class="material-icons">record_voice_over</i>
                    <span>Siren</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="javascript:void(0);" onclick="window.location.href='history.php';">
                    <i class="material-icons">history</i>
                    <span>History</span>
                </a>
            </li>

            <!-- Admin Only Links -->
            <?php if ($userRole === 'Admin'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0);" onclick="window.location.href='user-management.php';">
                        <i class="material-icons">group</i>
                        <span>User Management</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0);" onclick="window.location.href='add-user.php';">
                        <i class="material-icons">group_add</i>
                        <span>Add Users</span>
                    </a>
                </li>
            <?php endif; ?>

            <!-- Account Pages -->
            <li class="nav-items mt-32">
                <h6 class="brand-name">Account pages</h6>
            </li>
            <li class="nav-items">
                <a class="nav-link" href="javascript:void(0);" onclick="window.location.href='profile.php';">
                    <i class="material-icons">person</i>
                    <span>Profile</span>
                </a>
            </li>
            <li class="nav-items">
                <?php
                    // Include the connection if not already included in this scope.
                    include('./../connection.php');
                    $sql = "SELECT COUNT(*) AS count FROM notifications";
                    $result = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_assoc($result);
                    $count = $row['count'];
                ?>
                <a class="nav-link" href="javascript:void(0);" onclick="window.location.href='notifications.php';">
                    <i class="material-icons">notifications</i>
                    <span id="notif-count">Notifications<?php echo ($count > 0 ? " ($count)" : ""); ?></span>
                </a>
            </li>
            <li class="nav-items">
                <a class="nav-link" href="javascript:void(0);" onclick="window.location.href='./../logout.php';">
                    <i class="material-icons">logout</i>
                    <span>Log out</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>

<!-- Top Header -->
<header class="top-header" id="topHeader">
    <div class="navbar-left">
        <i class="material-icons menu-btn" id="menuButton">menu</i>
        <a href="javascript:void(0);" class="navbar-brand">
            <img src="https://i.ibb.co/6yw7WFr/logos.png" alt="Logo" class="logo" />
            <span class="brand-name">PUBLIC ANNOUNCEMENT SYSTEM</span>
        </a>
    </div>
</header>

<!-- Footer -->
<footer class="footer">
    <button class="footer-button" id="footerMenuButton">
        <i class="material-icons">menu</i>
    </button>
    <button class="footer-button" id="footerNotificationsButton" onclick="window.location.href='notifications.php';">
        <i class="material-icons">notifications</i>
        <span id="footerNotificationCount" class="notification-badge"></span> 
    </button>
    <button class="footer-button" id="footerHomeButton" onclick="window.location.href='index.php';">
        <i class="material-icons">home</i>
    </button>
    <button class="footer-button" id="footerProfileButton" onclick="window.location.href='profile.php';">
        <i class="material-icons">person</i>
    </button>
    <button class="footer-button" id="footerLogoutButton" onclick="window.location.href='./../logout.php';">
        <i class="material-icons">logout</i>
    </button>
</footer>

<script src="dist/js/notifications.js"></script>
