<?php

class RegisterContr {

    private $uid;
    private $pwd;
    private $pwdRepeat;
    
    public function __construct($uid, $pwd, $pwdRepeat) {
        $this->$uid = $uid;
        $this->$pwd = $pwd;
        $this->$pwdRepeat = $pwdRepeat;
    }

    private function emptyInput() {
        $result;
        if(empty($this->uid) || empty($this->pwd) || empty($this->pwdRepeat)) {
            $result = false;
        }
        else {
            $result = true;
        }
        return $result;
    }

    private function pwdMatch() {
        $result;
        if ($this->pwd !== $this->pwdRepeat){
            $result = false;
        } else {
            $result = true;
        }
        return $result;
    }
}