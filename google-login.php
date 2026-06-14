<?php
require __DIR__ . '/vendor/autoload.php';
session_start();

$client = new Google_Client();
// Load client config if available
$configPath = __DIR__ . '/config/client_secret.json';
if (!file_exists($configPath)) {
    $configPath = __DIR__ . '/client_secret.json';
}
if (!file_exists($configPath)) {
    header('Location: /index.php?error=oauthconfigmissing');
    exit;
}
try {
    $client->setAuthConfig($configPath);
} catch (Exception $e) {
    header('Location: /index.php?error=oauthconfiginvalid&reason=' . urlencode($e->getMessage()));
    exit;
}

$scheme = 'http';
if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
    || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
    || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower($_SERVER['HTTP_X_FORWARDED_SSL']) === 'on')) {
    $scheme = 'https';
}
$client->setRedirectUri($scheme . '://' . $_SERVER['HTTP_HOST'] . '/google-callback.php');
$client->addScope(['email', 'profile']);

$authUrl = $client->createAuthUrl();
header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
exit;
