<?php

if (isset($_POST["submit"]))
{
    // Data
    $uid = $_POST["uid"];
    $pwd = $_POST["pwd"];
    $pwdRepeat = $_POST["pwdrepeat"];
    $email = $_POST["email"];

    // Instantiate RegisterContr class
    include "../classes/register.classes.php";
    include "../classes/register-contr.classes.php";
    $register = new RegisterContr($uid, $pwd, $pwdRepeat, $email);
}