<?php
// set-cookie.php
// Simple example to set a secure HttpOnly session cookie
try {
    $value = bin2hex(random_bytes(16));
} catch (Exception $e) {
    $value = bin2hex(openssl_random_pseudo_bytes(16));
}

setcookie('session', $value, [
    'expires' => time() + 3600,
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);

header('Content-Type: application/json');
echo json_encode(['session' => $value]);
