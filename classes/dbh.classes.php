<?php

class Dbh {
    public function connect() {
        try {
            $configPath = __DIR__ . '/../config/db.php';
            $config = file_exists($configPath) ? require $configPath : [];

            $host = $config['DB_HOST'] ?? getenv('DB_HOST') ?: 'localhost';
            $dbname = $config['DB_NAME'] ?? getenv('DB_NAME') ?: 'if0_42065649_ooplogin';
            $username = $config['DB_USERNAME'] ?? getenv('DB_USERNAME') ?: 'root';
            $password = $config['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?: '';

            $dbh = new PDO(
                'mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8mb4',
                $username,
                $password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            return $dbh;
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }

    protected function columnExists(string $column): bool {
        try {
            $stmt = $this->connect()->prepare("SHOW COLUMNS FROM `users` LIKE ?");
            $stmt->execute([$column]);
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    protected function getEmailColumn(): ?string {
        if ($this->columnExists('email')) {
            return 'email';
        }
        if ($this->columnExists('users_email')) {
            return 'users_email';
        }
        return null;
    }

    protected function deleteExpiredUnverifiedUsers() {
        $stmt = $this->connect()->prepare("DELETE FROM users WHERE email_verified = 0 AND verification_expires < NOW()");
        $stmt->execute();
    }
}