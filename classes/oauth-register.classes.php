<?php

class OauthRegister extends Dbh {
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

    protected function setOauthUser($uid, $pwd, $email, $provider, $oauthUid) {
        $emailColumn = $this->getEmailColumn();
        $columns = ['users_uid', 'users_pwd'];
        $placeholders = ['?', '?'];
        $values = [$uid, password_hash($pwd, PASSWORD_DEFAULT)];

        if ($emailColumn) {
            $columns[] = $emailColumn;
            $placeholders[] = '?';
            $values[] = $email;
        }

        if ($this->columnExists('email_verified')) {
            $columns[] = 'email_verified';
            $placeholders[] = '?';
            $values[] = 1;
        }

        if ($this->columnExists('oauth_provider') && $this->columnExists('oauth_uid')) {
            $columns[] = 'oauth_provider';
            $placeholders[] = '?';
            $values[] = $provider;
            $columns[] = 'oauth_uid';
            $placeholders[] = '?';
            $values[] = $oauthUid;
        }

        $stmt = $this->connect()->prepare('INSERT INTO users (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $placeholders) . ')');
        if (!$stmt->execute($values)) {
            $stmt = null;
            header('Location: /index.php?error=stmtfailed');
            exit();
        }

        return $this->connect()->lastInsertId();
    }

    protected function checkUser($uid, $email) {
        $this->deleteExpiredUnverifiedUsers();

        $emailColumn = $this->getEmailColumn();
        $query = 'SELECT users_uid FROM users WHERE users_uid = ?';
        $params = [$uid];

        if ($emailColumn) {
            $query .= ' OR ' . $emailColumn . ' = ?';
            $params[] = $email;
        }

        $query .= ';';
        $stmt = $this->connect()->prepare($query);
        if (!$stmt->execute($params)) {
            $stmt = null;
            header('Location: /index.php?error=stmtfailed');
            exit();
        }

        return $stmt->rowCount() === 0;
    }
}
