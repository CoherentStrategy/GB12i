<?php

class Login extends Dbh {
    protected function getUser($uid, $pwd) {
        $this->deleteExpiredUnverifiedUsers();

        $emailColumn = $this->getEmailColumn() ?? 'users_email';
        $stmt = $this->connect()->prepare("SELECT * FROM users WHERE users_uid = ? OR `$emailColumn` = ?;");

        if (!$stmt->execute(array($uid, $uid))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }

        if ($stmt->rowCount() == 0) {
            $stmt = null;
            header("location: ../index.php?error=usernotfound");
            exit();
        }

        $user = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
        $checkPwd = password_verify($pwd, $user["users_pwd"]);

        if ($checkPwd == false) {
            $stmt = null;
            header("location: ../index.php?error=wrongpassword");
            exit();
        }

        if ($user["email_verified"] == 0) {
            if (!empty($user["verification_expires"]) && strtotime($user["verification_expires"]) < time()) {
                $delete = $this->connect()->prepare('DELETE FROM users WHERE users_id = ?;');
                $delete->execute(array($user["users_id"]));
                $delete = null;
                header("location: ../index.php?error=expiredanddeleted");
                exit();
            }
            $stmt = null;
            header("location: ../index.php?error=notverified");
            exit();
        }

        session_start();
        $_SESSION["userid"] = $user["users_id"];
        $_SESSION["useruid"] = $user["users_uid"];

        $stmt = null;
    }
}