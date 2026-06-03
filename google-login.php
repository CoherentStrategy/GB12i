<?php
require __DIR__ . '/vendor/autoload.php';
session_start();

$client = new Google_Client();
// Load client config if available
$configPath = __DIR__ . '/client_secret.json';
if (file_exists($configPath)) {
    $client->setAuthConfig($configPath);
}

$client->setRedirectUri((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/google-callback.php');
$client->addScope(['email', 'profile']);

$authUrl = $client->createAuthUrl();
header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
exit;
