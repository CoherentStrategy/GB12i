<?php

class VerifyContr extends Verify {
    private $email;
    private $token;

    public function __construct($email, $token) {
        $this->email = $email;
        $this->token = $token;
    }

    public function verifyUser() {
        if ($this->emptyInput() == false) {
            header("location: ../verify.php?error=emptyinput");
            exit();
        }

        $result = $this->verifyToken($this->email, $this->token);

        if ($result === "invalid") {
            header("location: ../verify.php?error=invalidtoken");
            exit();
        }

        if ($result === "expired") {
            header("location: ../verify.php?error=expired");
            exit();
        }

        if ($result === "already") {
            header("location: ../verify.php?error=alreadyverified");
            exit();
        }

        header("location: ../index.php?error=verified");
        exit();
    }

    private function emptyInput() {
        if (empty($this->email) || empty($this->token)) {
            return false;
        }
        return true;
    }
}
