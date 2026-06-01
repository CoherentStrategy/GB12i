<?php

class Register extends Dbh{

    protected function setUser($uid, $pwd, $email, $token, $expires) {
        $stmt = $this->connect()->prepare("INSERT INTO users (users_uid, users_pwd, email, email_verified, verification_code, verification_expires) VALUES (?, ?, ?, 0, ?, ?)");

        $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);

        if (!$stmt->execute(array($uid, $hashedPwd, $email, $token, $expires))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }

        $stmt = null;
    }

    protected function checkUser($uid, $email) {
        $this->deleteExpiredUnverifiedUsers();

        $stmt = $this->connect()->prepare("SELECT users_uid FROM users WHERE users_uid = ? OR email = ?;");

        if (!$stmt->execute(array($uid, $email))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }

        $resultCheck;
        if ($stmt->rowCount() > 0) {
            $resultCheck = false; // User exists
        } else {
            $resultCheck = true; // User does not exist
        }
        return $resultCheck;
    }
}