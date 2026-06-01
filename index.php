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
    <title>GB12i</title>
</head>
<body>

<?php
    include "includes/register.inc.php";
    include "includes/login.inc.php";

    if (isset($_GET["error"])) {
        if ($_GET["error"] == "emptyinput") {
            echo "<script>window.addEventListener('DOMContentLoaded', function(){ openPopup1('Please fill in all fields!'); });</script>";
        }
        else if ($_GET["error"] == "invalidemail") {
            echo "<script>window.addEventListener('DOMContentLoaded', function(){ openPopup1('Please enter a valid email address!'); });</script>";
        }
        else if ($_GET["error"] == "passwordmatch") {
            echo "<script>window.addEventListener('DOMContentLoaded', function(){ openPopup1('Passwords do not match!'); });</script>";
        }
        else if ($_GET["error"] == "useroremailtaken") {
            echo "<script>window.addEventListener('DOMContentLoaded', function(){ openPopup1('Username or email already taken!'); });</script>";
        }
        else if ($_GET["error"] == "invaliduid") {
            echo "<script>window.addEventListener('DOMContentLoaded', function(){ openPopup1('Username must be between 2 and 6 characters long!'); });</script>";
        }
        else if ($_GET["error"] == "stmtfailed") {
            echo "<script>window.addEventListener('DOMContentLoaded', function(){ openPopup1('Something went wrong, please try again!'); });</script>";
        }
        else if ($_GET["error"] == "none") {
            echo "<script>window.addEventListener('DOMContentLoaded', function(){ openPopup1('You have been successfully registered! Please verify your email.'); });</script>";
        }
        else if ($_GET["error"] == "notverified") {
            echo "<script>window.addEventListener('DOMContentLoaded', function(){ openPopup1('Your email is not verified yet. Please check your inbox and verify your account.'); });</script>";
        }
        else if ($_GET["error"] == "expiredanddeleted") {
            echo "<script>window.addEventListener('DOMContentLoaded', function(){ openPopup1('Your account was not verified within 15 minutes and has been deleted. Please register again.'); });</script>";
        }
        else if ($_GET["error"] == "verified") {
            echo "<script>window.addEventListener('DOMContentLoaded', function(){ openPopup1('Your email has been verified. You can now log in.'); });</script>";
        }
    }
?>
<!--LOGIN AND REGISTER-->
<div class="container" id="container">
    <div class="form-container register">
        <form action="includes/register.inc.php" method="post">
            <h2>Create Account</h2>
            <input type="text" name="uid" placeholder="Username">
            <input type="password" id="registerPwd" name="pwd" placeholder="Password">
            <label class="show-password-label"><input type="checkbox" id="showRegisterPwd"> Show password</label>
            <input type="password" name="pwdrepeat" placeholder="Repeat Password">
            <input type="text" name="email" placeholder="Email">
            <button type="submit" name="submit">Register</button>
            <p id="session-id-display">Your device doesn't support JavaScript</p>
        </form>
    </div>
    <div class="form-container login">
        <form action="includes/login.inc.php" method="post">
            <h2>Login</h2>
            <input type="text" name="uid" placeholder="Username">
            <input type="password" id="loginPwd" name="pwd" placeholder="Password">
            <label class="show-password-label"><input type="checkbox" id="showLoginPwd"> Show password</label>
            <a href="#" id="forgotPassword">Forgot Password?</a>
            <button type="submit" name="submit">Confirm Login</button>
        </form>
    </div>
    <div class="toggle-container">
        <div class="toggle">
            <div class="toggle-panel toggle-right">
                <h1>Welcome to Gamebook 12i!</h1>
                <p>Fill in your username and password (keep it secure) and click <b>Register</b></p>
                <br>
                <p>Already have a account?</p>
                <button class="hide" id="loginBtn">Login</button>
            </div>
            <div class="toggle-panel toggle-left">
                <h1>Welcome Again!</h1>
                <p>Fill in your previous username and password and click <b>Confirm Login</b></p>
                <br>
                <p>Don't have a account yet?</p>
                <button class="hide" id="registerBtn">Register</button>
            </div>
        </div>
    </div>
</div>

<!--POPUP1 (with Close button)-->
<div id="popupOverlay1" class="hidden">
    <div id="popupBox1">
        <div id="popupContent1"></div>
        <div style="text-align:right;margin-top:10px;">
            <button onclick="closePopup1()">Close</button>
        </div>
    </div>
</div>

<!--POPUP2 (Custom POPUP)-->
<div id="popupOverlay2" class="hidden">
    <div id="popupBox2">
        <div id="popupContent2"></div>
    </div>
</div>

<!--AD DISPLAY-->
<div id="ad-backdrop" hidden>
    <div class="ad-popup">
        <iframe
        id="ad-wrapper"
        src="https://coherentstrategy.github.io/ads/"
        width="500px"
        height="600px"
        sandbox="allow-scripts allow-popups allow-downloads allow-same-origin"
        referrerpolicy="no-referrer"
        loading="lazy"
        frameborder="0"
        title="Advertisement">
    </iframe>
    </div>
</div>

    <!--TAB-->
<div id="duplicate-overlay">
    <div class="warning-box">
        <h1>DUPLICATE TAB DETECTED</h1>
        <p>Another tab is already running this game session. Would you like to resume previous session on this tab instead?</p>
        <button class="takeover-btn" id="takeover-button" onclick="claimSession()">Use This Tab Instead</button>
    </div>
</div>

<script src="GB12i.js"></script>
</body>
</html>
