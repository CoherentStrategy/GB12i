<?php

class RegisterContr extends Register {

    private $uid;
    private $pwd;
    private $pwdRepeat;
    private $email;
    
    public function __construct($uid, $pwd, $pwdRepeat, $email) {
        $this->uid = $uid;
        $this->pwd = $pwd;
        $this->pwdRepeat = $pwdRepeat;
        $this->email = $email;
    }

    public function registerUser() {
        if ($this->emptyInput() == false) {
            header("location: ../index.php?error=emptyinput");
            exit();
        }
        if ($this->invalidEmail() == false) {
            header("location: ../index.php?error=invalidemail");
            exit();
        }
        if ($this->pwdMatch() == false) {
            header("location: ../index.php?error=passwordmatch");
            exit();
        }
        if ($this->uidTakenCheck() == false) {
            header("location: ../index.php?error=useroremailtaken");
            exit();
        }
        if ($this->InvalidUid() == false) {
            header("location: ../index.php?error=invaliduid");
            exit();
        }

        $token = bin2hex(random_bytes(16));
        $expires = date("Y-m-d H:i:s", strtotime("+5 minutes"));

        $this->setUser($this->uid, $this->pwd, $this->email, $token, $expires);
        $this->sendVerificationEmail($this->email, $token);
    }

    private function sendVerificationEmail($email, $token) {
        $subject = "Verify your Gamebook 12i account";
        $verifyUrl = "http://" . $_SERVER["HTTP_HOST"] . "/verify.php?email=" . urlencode($email) . "&token=" . $token;
        $message = "Please verify your account by clicking the link below:\n\n" . $verifyUrl . "\n\nThis link expires in 5 minutes.";
        $headers = "From: no-reply@" . $_SERVER["HTTP_HOST"] . "\r\n";

        @mail($email, $subject, $message, $headers);
    }

    private function emptyInput() {
        $result;
        if(empty($this->uid) || empty($this->pwd) || empty($this->pwdRepeat) || empty($this->email)) {
            $result = false;
        }
        else {
            $result = true;
        }
        return $result;
    }

    private function invalidEmail () {
        $result;
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $result = false;
        } else {
            $result = true;
        }
        return $result;
    }

    private function pwdMatch() {
        $result;
        if ($this->pwd !== $this->pwdRepeat) {
            $result = false;
        } else {
            $result = true;
        }
        return $result;
    }
    private function uidTakenCheck() {
        $result;
        if (!$this->checkUser($this->uid, $this->email)) {
            $result = false;
        } else {
            $result = true;
        }
        return $result;
    }
    private function InvalidUid() {
        $result;
        if (strlen($this->uid) < 2 || strlen($this->uid) > 6) {
            $result = false;
        } else {
            $result = true;
        }
        return $result;
    }
}