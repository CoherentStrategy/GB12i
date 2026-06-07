<?php
session_start();

if (!isset($_SESSION['oauth_pending']) || empty($_SESSION['oauth_pending']['email']) || empty($_SESSION['oauth_pending']['googleId'])) {
    header('Location: /index.php');
    exit;
}

if (isset($_POST['submit'])) {
    $uid = $_POST['uid'] ?? '';
    $pwd = $_POST['pwd'] ?? '';
    $pwdRepeat = $_POST['pwdrepeat'] ?? '';
    $email = $_SESSION['oauth_pending']['email'];
    $googleId = $_SESSION['oauth_pending']['googleId'];

    include "../classes/dbh.classes.php";
    include "../classes/oauth-register.classes.php";
    include "../classes/oauth-register-contr.classes.php";

    $register = new OauthRegisterContr($uid, $pwd, $pwdRepeat, $email, $googleId);
    $userId = $register->registerUser();

    if ($userId) {
        $_SESSION['userid'] = $userId;
        $_SESSION['useruid'] = $uid;
        unset($_SESSION['oauth_pending']);
        header('Location: /dashboard.php');
        exit;
    }
}

header('Location: /oauth-complete.php');
exit;
