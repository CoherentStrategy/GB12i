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
    <div class="container" style="max-width: 600px; margin: 48px auto; padding: 32px;">
        <h1>Dashboard</h1>
        <p>Welcome back, <strong><?php echo $userUid; ?></strong>!</p>
        <div style="margin-top: 24px; padding: 20px; background: #f7f9ff; border-radius: 16px; border: 1px solid rgba(1, 107, 227, 0.12);">
            <p><strong>User ID:</strong> <?php echo $userId; ?></p>
            <p><strong>Username:</strong> <?php echo $userUid; ?></p>
        </div>
        <p style="margin-top: 24px;">You have successfully logged in or completed registration. Use the button below to log out when you are finished.</p>
        <a href="includes/logout.inc.php" style="display: inline-block; margin-top: 24px; padding: 12px 24px; background: #016be3; color: #fff; border-radius: 999px; text-decoration: none;">Logout</a>
    </div>
</body>
</html>
