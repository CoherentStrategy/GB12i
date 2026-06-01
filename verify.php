<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="GB12i.css">
    <link rel="icon" type="image/png" href="img/logo.png">
    <title>Verify Email</title>
</head>
<body>

<?php
    include "includes/verify.inc.php";

    if (isset($_GET["error"])) {
        if ($_GET["error"] == "emptyinput") {
            echo "<script>window.addEventListener('DOMContentLoaded', function(){ openPopup1('Please enter both your email and verification code.'); });</script>";
        }
        else if ($_GET["error"] == "invalidtoken") {
            echo "<script>window.addEventListener('DOMContentLoaded', function(){ openPopup1('The verification code is invalid. Please try again.'); });</script>";
        }
        else if ($_GET["error"] == "expired") {
            echo "<script>window.addEventListener('DOMContentLoaded', function(){ openPopup1('Your verification code expired. Please register again.'); });</script>";
        }
        else if ($_GET["error"] == "alreadyverified") {
            echo "<script>window.addEventListener('DOMContentLoaded', function(){ openPopup1('Your account is already verified. Please log in.'); });</script>";
        }
        else if ($_GET["error"] == "usernotfound") {
            echo "<script>window.addEventListener('DOMContentLoaded', function(){ openPopup1('User not found. Please register again.'); });</script>";
        }
        else if ($_GET["error"] == "stmtfailed") {
            echo "<script>window.addEventListener('DOMContentLoaded', function(){ openPopup1('Something went wrong. Please try again.'); });</script>";
        }
    }

    $email = isset($_GET["email"]) ? htmlspecialchars($_GET["email"]) : "";
?>

<div class="container" id="container">
    <div class="form-container register">
        <form action="includes/verify.inc.php" method="post">
            <h2>Verify Email</h2>
            <input type="hidden" name="email" value="<?php echo $email; ?>">
            <input type="text" name="token" placeholder="Verification code">
            <button type="submit" name="submit">Verify</button>
            <p id="session-id-display">Your device doesn't support JavaScript</p>
        </form>
    </div>
</div>

<script src="GB12i.js"></script>
</body>
</html>
