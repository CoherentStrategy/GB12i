<?php
session_start();
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
    <div class="container" style="max-width: 540px; margin: 48px auto; padding: 32px;">
        <h1>Dashboard</h1>
        <p>Welcome! You have successfully logged in or completed registration.</p>
        <p>This is your dashboard screen.</p>
        <a href="includes/logout.inc.php" style="display: inline-block; margin-top: 24px; padding: 12px 24px; background: #016be3; color: #fff; border-radius: 999px; text-decoration: none;">Logout</a>
    </div>
</body>
</html>
