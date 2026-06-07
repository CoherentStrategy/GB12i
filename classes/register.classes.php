<?php

class Register extends Dbh{

    protected function setUser($uid, $pwd, $email, $token, $expires) {
        $emailColumn = $this->getEmailColumn();
        if ($emailColumn === null) {
            header("location: ../index.php?error=stmtfailed");
            exit();
        }

        $columns = ["users_uid", "users_pwd", "`$emailColumn`", "verification_code", "verification_expires"];
        $placeholders = ["?", "?", "?", "?", "?"];
        $values = [$uid, password_hash($pwd, PASSWORD_DEFAULT), $email, $token, $expires];

        if ($this->columnExists('email_verified')) {
            array_splice($columns, 3, 0, 'email_verified');
            array_splice($placeholders, 3, 0, '?');
            array_splice($values, 3, 0, 0);
        }

        $stmt = $this->connect()->prepare('INSERT INTO users (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $placeholders) . ')');

        if (!$stmt->execute($values)) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }

        $stmt = null;
    }

    protected function checkUser($uid, $email) {
        $this->deleteExpiredUnverifiedUsers();

        $emailColumn = $this->getEmailColumn() ?? 'email';
        $stmt = $this->connect()->prepare("SELECT users_uid FROM users WHERE users_uid = ? OR `$emailColumn` = ?;");

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