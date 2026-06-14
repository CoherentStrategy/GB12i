<?php
session_start();

if (!isset($_SESSION["userid"])) {
    header('Location: /index.php');
    exit;
}

$userId = htmlspecialchars($_SESSION["userid"], ENT_QUOTES, 'UTF-8');
$userUid = isset($_SESSION["useruid"]) ? htmlspecialchars($_SESSION["useruid"], ENT_QUOTES, 'UTF-8') : 'Guest';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="img/logo.png">
    <link rel="stylesheet" href="GB12i.css">
    <title>Dashboard</title>
</head>
<body>
    <div class="page-background"></div>
    <a href="includes/logout.inc.php" class="logout-topright btn btn-primary">Logout</a>
    <?php $profilePic = isset($_SESSION['profile_pic']) ? htmlspecialchars($_SESSION['profile_pic'], ENT_QUOTES, 'UTF-8') : ''; ?>
    <div class="container">
        <div class="dashboard-card">
            <div class="dashboard-layout">
                <aside class="dashboard-sidebar" id="dashboardSidebar" aria-expanded="true">
                    <div class="sidebar-toggle" id="sidebarToggle" role="button" tabindex="0" aria-label="Toggle sidebar">
                        <img id="sidebarToggleIcon" src="img/menu_open.png" alt="Close sidebar">
                    </div>
                    <div class="sidebar-brand">
                        <h2>Quick Links</h2>
                        <p>Navigate your account actions and settings.</p>
                    </div>
                    <nav class="sidebar-nav">
                        <a href="#profile" class="sidebar-link">Profile</a>
                        <a href="#details" class="sidebar-link">Account details</a>
                        <a href="#activity" class="sidebar-link">Activity</a>
                        <a href="#help" class="sidebar-link">Help & support</a>
                    </nav>
                </aside>

                <main class="dashboard-main">
                    <div class="dashboard-header">
                        <div>
                            <h1>Dashboard</h1>
                            <p>Welcome back, <strong><?php echo $userUid; ?></strong>!</p>
                        </div>
                    </div>

                    <?php if ($profilePic): ?>
                        <div class="profile-card" id="profile">
                            <img src="<?php echo $profilePic; ?>" alt="Profile picture">
                            <div>
                                <p><strong>Logged in as</strong></p>
                                <p class="profile-username"><?php echo $userUid; ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="dashboard-info" id="details">
                        <div class="dashboard-row"><span>User ID:</span><strong><?php echo $userId; ?></strong></div>
                        <div class="dashboard-row"><span>Username:</span><strong><?php echo $userUid; ?></strong></div>
                    </div>
                    <p class="dashboard-note">You have successfully logged in or completed registration. Use the logout button above when you are finished.</p>
                </main>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var sidebar = document.getElementById('dashboardSidebar');
            var toggle = document.getElementById('sidebarToggle');
            var icon = document.getElementById('sidebarToggleIcon');

            function toggleSidebar() {
                var collapsed = sidebar.classList.toggle('collapsed');
                sidebar.setAttribute('aria-expanded', String(!collapsed));
                icon.src = collapsed ? 'img/menu.png' : 'img/menu_open.png';
                icon.alt = collapsed ? 'Open sidebar' : 'Close sidebar';
            }

            toggle.addEventListener('click', toggleSidebar);
            toggle.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    toggleSidebar();
                }
            });
        });
    </script>
</body>
</html>
