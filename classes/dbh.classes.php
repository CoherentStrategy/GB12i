<?php

class Dbh {
    protected function connect() {
        try {
            $username = "root";
            $password = "";
            $dbh = new PDO('mysql:host=localhost;dbname=ooplogin', $username, $password);
            return $dbh;
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }

    protected function deleteExpiredUnverifiedUsers() {
        $stmt = $this->connect()->prepare("DELETE FROM users WHERE email_verified = 0 AND verification_expires < NOW()");
        $stmt->execute();
    }
}