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
    <link rel="stylesheet" href="GB12i.css">
    <title>Dashboard</title>
</head>
<body>
    <?php $profilePic = isset($_SESSION['profile_pic']) ? htmlspecialchars($_SESSION['profile_pic'], ENT_QUOTES, 'UTF-8') : ''; ?>
    <div class="container">
        <div class="dashboard-card">
            <div class="dashboard-header">
                <div>
                    <h1>Dashboard</h1>
                    <p>Welcome back, <strong><?php echo $userUid; ?></strong>!</p>
                </div>
                <a href="includes/logout.inc.php" class="btn btn-primary">Logout</a>
            </div>

            <?php if ($profilePic): ?>
                <div class="profile-card">
                    <img src="<?php echo $profilePic; ?>" alt="Profile picture">
                    <div>
                        <p><strong>Logged in as</strong></p>
                        <p class="profile-username"><?php echo $userUid; ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <div class="dashboard-info">
                <div class="dashboard-row"><span>User ID:</span><strong><?php echo $userId; ?></strong></div>
                <div class="dashboard-row"><span>Username:</span><strong><?php echo $userUid; ?></strong></div>
            </div>
            <p class="dashboard-note">You have successfully logged in or completed registration. Use the logout button above when you are finished.</p>
        </div>
    </div>
</body>
</html>
