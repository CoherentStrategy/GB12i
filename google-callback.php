<?php
require __DIR__ . '/vendor/autoload.php';
session_start();
require_once __DIR__ . '/classes/dbh.classes.php';

$client = new Google_Client();
$configPath = __DIR__ . '/config/client_secret.json';
if (!file_exists($configPath)) {
    $configPath = __DIR__ . '/client_secret.json';
}
if (file_exists($configPath)) {
    $client->setAuthConfig($configPath);
}
$scheme = 'http';
if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
    || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
    || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower($_SERVER['HTTP_X_FORWARDED_SSL']) === 'on')) {
    $scheme = 'https';
}
$client->setRedirectUri($scheme . '://' . $_SERVER['HTTP_HOST'] . '/google-callback.php');

if (!isset($_GET['code'])) {
    header('Location: /index.php');
    exit;
}

$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
if (isset($token['error'])) {
    $reason = $token['error_description'] ?? $token['error'];
    header('Location: /index.php?error=google_auth_failed&reason=' . urlencode($reason));
    exit;
}

$client->setAccessToken($token);
$oauth2 = new Google_Service_Oauth2($client);
$googleUser = $oauth2->userinfo->get();

$email = $googleUser->email ?? null;
$googleId = $googleUser->id ?? null;
$name = $googleUser->name ?? null;

$dbh = new Dbh();
$pdo = $dbh->connect();

function columnExists(PDO $pdo, string $table, string $column): bool {
    try {
        $stmt = $pdo->prepare("SHOW COLUMNS FROM `$table` LIKE ?");
        $stmt->execute([$column]);
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        return false;
    }
}

$emailColumn = null;
if (columnExists($pdo, 'users', 'email')) {
    $emailColumn = 'email';
} elseif (columnExists($pdo, 'users', 'users_email')) {
    $emailColumn = 'users_email';
}

$haveEmailVerified = columnExists($pdo, 'users', 'email_verified');
$haveOauthColumns = columnExists($pdo, 'users', 'oauth_provider') && columnExists($pdo, 'users', 'oauth_uid');

$user = null;

// 1) Try finding user by oauth columns (if present)
if ($haveOauthColumns && $googleId) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE oauth_provider = 'google' AND oauth_uid = ? LIMIT 1");
        $stmt->execute([$googleId]);
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    } catch (Exception $e) {
        // oauth columns may not exist; ignore and continue
    }
}

// 2) Fallback: find by email
if (!$user && $email && $emailColumn) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE `$emailColumn` = ? LIMIT 1");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    } catch (Exception $e) {
        // ignore
    }
}

// 3) If user not found continue to the OAuth completion flow
if (!$user) {
    if (!$email || !$googleId) {
        header('Location: /index.php?error=oauthinvalid');
        exit;
    }

    $_SESSION['oauth_pending'] = [
        'email' => $email,
        'googleId' => $googleId,
        'name' => $name,
    ];
    header('Location: /oauth-complete.php');
    exit;
}

// 4) Update oauth columns if available
if ($haveOauthColumns && $googleId && $email) {
    $updateConditions = [];
    $updateParams = [$googleId];

    if (columnExists($pdo, 'users', 'email')) {
        $updateConditions[] = 'email = ?';
        $updateParams[] = $email;
    }
    if (columnExists($pdo, 'users', 'users_email')) {
        $updateConditions[] = 'users_email = ?';
        $updateParams[] = $email;
    }

    if (!empty($updateConditions)) {
        try {
            $stmt = $pdo->prepare('UPDATE users SET oauth_provider = \'google\', oauth_uid = ? WHERE ' . implode(' OR ', $updateConditions));
            $stmt->execute($updateParams);
        } catch (Exception $e) {
            // ignore if columns don't exist
        }
    }
}

// 5) Log the user in (set session similar to existing login)
session_regenerate_id(true);
if (($user && isset($user['users_id'])) || isset($lastId)) {
    $uid = $user['users_id'] ?? $lastId;
    $_SESSION['userid'] = $uid;
    $_SESSION['useruid'] = $user['users_uid'] ?? $username;
    header('Location: /dashboard.php');
    exit;
}

// Fallback redirect
header('Location: /index.php?error=stmtfailed');
exit;
