<?php

class Register extends Dbh{

    protected function setUser($uid, $pwd, $email) {
        // Check if the username or email already exists in the database
        $stmt = $this->connect()->prepare("INSERT INTO users (users_uid, users_pwd, email) VALUES (?, ?, ?)");

        $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);

        if (!$stmt->execute(array($uid, $hashedPwd, $email))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }

        $stmt = null;
    }

    protected function checkUser($uid, $email) {
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