<?php

if (isset($_POST["submit"])) {
    // Get data from form
    $uid = $_POST["uid"];
    $pwd = $_POST["pwd"];

    // Instantiate LoginContr class
    include "../classes/dbh.classes.php";
    include "../classes/login.classes.php";
    include "../classes/login-contr.classes.php";
    $login = new LoginContr($uid, $pwd);

    // Running error handlers and user login
    $login->loginUser();

    // Redirect to dashboard after successful login
    header("location: /dashboard.php");
    exit();
}