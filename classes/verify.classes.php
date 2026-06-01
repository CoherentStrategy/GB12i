<?php

class Verify extends Dbh {
    protected function verifyToken($email, $token) {
        $stmt = $this->connect()->prepare('SELECT * FROM users WHERE email = ?;');

        if (!$stmt->execute(array($email))) {
            $stmt = null;
            header("location: ../verify.php?error=stmtfailed");
            exit();
        }

        if ($stmt->rowCount() == 0) {
            $stmt = null;
            header("location: ../verify.php?error=usernotfound");
            exit();
        }

        $user = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

        if ($user["email_verified"] == 1) {
            $stmt = null;
            return "already";
        }

        if ($user["verification_code"] !== $token) {
            $stmt = null;
            return "invalid";
        }

        if (!empty($user["verification_expires"]) && strtotime($user["verification_expires"]) < time()) {
            $delete = $this->connect()->prepare('DELETE FROM users WHERE users_id = ?;');
            $delete->execute(array($user["users_id"]));
            $delete = null;
            $stmt = null;
            return "expired";
        }

        $update = $this->connect()->prepare('UPDATE users SET email_verified = 1, verification_code = NULL, verification_expires = NULL WHERE users_id = ?;');

        if (!$update->execute(array($user["users_id"]))) {
            $update = null;
            $stmt = null;
            header("location: ../verify.php?error=stmtfailed");
            exit();
        }

        $update = null;
        $stmt = null;
        return "success";
    }
}
