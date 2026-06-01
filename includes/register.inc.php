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

    // Going back to front page
    header("location: ../index.php?error=none");

}