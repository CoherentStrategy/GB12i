<?php
session_start();

if (!isset($_SESSION['oauth_pending']) || empty($_SESSION['oauth_pending']['email']) || empty($_SESSION['oauth_pending']['googleId'])) {
    header('Location: /index.php');
    exit;
}

$email = htmlspecialchars($_SESSION['oauth_pending']['email']);
$name = htmlspecialchars($_SESSION['oauth_pending']['name'] ?? '');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="GB12i.css">
    <link rel="icon" type="image/png" href="img/logo.png">
    <title>Complete Google Signup</title>
</head>
<body>
    <div class="container" id="container">
        <div class="form-container register">
            <form action="includes/oauth-complete.inc.php" method="post">
                <h2>Complete Google Signup</h2>
                <p>Signed in with Google as <strong><?php echo $name ?: $email; ?></strong></p>
                <input type="text" name="uid" placeholder="Choose a username (2-6 chars)" required>
                <input type="password" name="pwd" placeholder="Password" required>
                <input type="password" name="pwdrepeat" placeholder="Repeat Password" required>
                <input type="text" value="<?php echo $email; ?>" disabled>
                <button type="submit" name="submit">Create Account</button>
            </form>
        </div>
    </div>
</body>
</html>
