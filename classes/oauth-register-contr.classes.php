<?php

class OauthRegisterContr extends OauthRegister {
    private $uid;
    private $pwd;
    private $pwdRepeat;
    private $email;
    private $googleId;
    private $newUserId;

    public function __construct($uid, $pwd, $pwdRepeat, $email, $googleId) {
        $this->uid = trim($uid);
        $this->pwd = $pwd;
        $this->pwdRepeat = $pwdRepeat;
        $this->email = $email;
        $this->googleId = $googleId;
    }

    public function registerUser() {
        if ($this->emptyInput() == false) {
            header('Location: /oauth-complete.php?error=emptyinput');
            exit();
        }
        if ($this->invalidEmail() == false) {
            header('Location: /oauth-complete.php?error=invalidemail');
            exit();
        }
        if ($this->pwdMatch() == false) {
            header('Location: /oauth-complete.php?error=passwordmatch');
            exit();
        }
        if ($this->uidTakenCheck() == false) {
            header('Location: /oauth-complete.php?error=useroremailtaken');
            exit();
        }
        if ($this->invalidUid() == false) {
            header('Location: /oauth-complete.php?error=invaliduid');
            exit();
        }

        $this->newUserId = $this->setOauthUser($this->uid, $this->pwd, $this->email, 'google', $this->googleId);
        return $this->newUserId;
    }

    public function getCreatedUserId() {
        return $this->newUserId;
    }

    private function emptyInput() {
        return !empty($this->uid) && !empty($this->pwd) && !empty($this->pwdRepeat) && !empty($this->email);
    }

    private function invalidEmail() {
        return filter_var($this->email, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function pwdMatch() {
        return $this->pwd === $this->pwdRepeat;
    }

    private function uidTakenCheck() {
        return $this->checkUser($this->uid, $this->email);
    }

    private function invalidUid() {
        $length = strlen($this->uid);
        return $length >= 2 && $length <= 6;
    }
}
