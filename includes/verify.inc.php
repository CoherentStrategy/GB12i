<?php

if (isset($_POST["submit"]) || (isset($_GET["email"]) && isset($_GET["token"]))) {
    $email = isset($_POST["email"]) ? $_POST["email"] : $_GET["email"];
    $token = isset($_POST["token"]) ? $_POST["token"] : $_GET["token"];

    include "../classes/dbh.classes.php";
    include "../classes/verify.classes.php";
    include "../classes/verify-contr.classes.php";

    $verify = new VerifyContr($email, $token);
    $verify->verifyUser();
}
