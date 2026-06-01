<?php

if (isset($_POST["submit"]))
{
    // Grabbing the data
    $uid = $_POST["uid"];
    $pwd = $_POST["pwd"];
    $pwdRepeat = $_POST["pwdrepeat"];
    $email = $_POST["email"];

    // Instantiate RegisterContr class
    include "../classes/dbh.classes.php";
    include "../classes/register.classes.php";
    include "../classes/register-contr.classes.php";
    $register = new RegisterContr($uid, $pwd, $pwdRepeat, $email);

    // Running error handlers and user registration
    $register->registerUser();

    // Send the user to verification step
    header("location: ../verify.php?email=" . urlencode($email));
    exit();

}