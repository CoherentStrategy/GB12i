<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$action = isset($data['action']) ? $data['action'] : null;
$prefs = isset($data['preferences']) && is_array($data['preferences']) ? $data['preferences'] : [];

$isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https');

// Helper to set or clear cookies in a consistent manner
function gb_set_cookie($name, $value, $expires, $isSecure) {
    setcookie($name, $value, [
        'expires' => $expires,
        'path' => '/',
        'secure' => $isSecure,
        'httponly' => false,
        'samesite' => 'Lax'
    ]);
}

$result = ['ok' => false];
if ($action === 'accept') {
    // User accepts optional cookies
    gb_set_cookie('consent', '1', time() + 31536000, $isSecure);
    gb_set_cookie('analytics', '1', time() + 31536000, $isSecure);
    $result = ['ok' => true, 'consent' => 1, 'analytics' => 1];
} elseif ($action === 'reject') {
    // User rejects optional cookies
    gb_set_cookie('consent', '0', time() + 3600, $isSecure);
    gb_set_cookie('analytics', '', time() - 3600, $isSecure);
    $result = ['ok' => true, 'consent' => 0, 'analytics' => 0];
} elseif ($action === 'set') {
    // Set granular preferences passed in `preferences` object
    $analytics = isset($prefs['analytics']) ? (bool)$prefs['analytics'] : false;
    // Consent cookie indicates a user choice exists (1 = accepted optional, 0 = declined)
    gb_set_cookie('consent', $analytics ? '1' : '0', time() + 31536000, $isSecure);
    if ($analytics) {
        gb_set_cookie('analytics', '1', time() + 31536000, $isSecure);
    } else {
        gb_set_cookie('analytics', '', time() - 3600, $isSecure);
    }
    $result = ['ok' => true, 'consent' => $analytics ? 1 : 0, 'analytics' => $analytics ? 1 : 0];
} else {
    http_response_code(400);
    $result = ['ok' => false, 'error' => 'invalid_action'];
}

header('Content-Type: application/json');
echo json_encode($result);
