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
$client->setRedirectUri((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/google-callback.php');

if (!isset($_GET['code'])) {
    header('Location: /index.php');
    exit;
}

$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
if (isset($token['error'])) {
    header('Location: /index.php?error=google_auth_failed');
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

$user = null;

// 1) Try finding user by oauth columns (if present)
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE oauth_provider = 'google' AND oauth_uid = ? LIMIT 1");
    $stmt->execute([$googleId]);
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    // oauth columns may not exist; ignore and continue
}

// 2) Fallback: find by email (try different email column names)
if (!$user && $email) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    } catch (Exception $e) {
        // ignore
    }
}
if (!$user && $email) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE users_email = ? LIMIT 1");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    } catch (Exception $e) {
        // ignore
    }
}

// 3) If user not found create a new user with minimal fields
if (!$user) {
    $username = $email ? explode('@', $email)[0] : 'googleuser_' . substr($googleId, 0, 8);
    $randomPwd = bin2hex(random_bytes(8));
    $hashed = password_hash($randomPwd, PASSWORD_DEFAULT);

    // Try inserting using common column names (users_uid, users_pwd, email)
    try {
        $stmt = $pdo->prepare("INSERT INTO users (users_uid, users_pwd, email, email_verified) VALUES (?, ?, ?, 1)");
        $stmt->execute([$username, $hashed, $email]);
        $lastId = $pdo->lastInsertId();
    } catch (Exception $e) {
        // try alternative column name users_email
        try {
            $stmt = $pdo->prepare("INSERT INTO users (users_uid, users_pwd, users_email, email_verified) VALUES (?, ?, ?, 1)");
            $stmt->execute([$username, $hashed, $email]);
            $lastId = $pdo->lastInsertId();
        } catch (Exception $e2) {
            header('Location: /index.php?error=stmtfailed');
            exit;
        }
    }
    // Fetch the new user row if possible
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE users_id = ? LIMIT 1");
        $stmt->execute([$lastId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // best effort
    }
}

// 4) Update oauth columns if available
try {
    $stmt = $pdo->prepare("UPDATE users SET oauth_provider = 'google', oauth_uid = ? WHERE email = ? OR users_email = ?");
    $stmt->execute([$googleId, $email, $email]);
} catch (Exception $e) {
    // ignore if columns don't exist
}

// 5) Log the user in (set session similar to existing login)
session_regenerate_id(true);
if ($user && (isset($user['users_id']) || isset($lastId))) {
    $uid = $user['users_id'] ?? $lastId;
    $_SESSION['userid'] = $uid;
    $_SESSION['useruid'] = $user['users_uid'] ?? ($email ? explode('@', $email)[0] : null);
    header('Location: /dashboard.php');
    exit;
}

// Fallback redirect
header('Location: /index.php');
exit;
