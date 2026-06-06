<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$accept = isset($data['action']) && $data['action'] === 'accept';

$isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https');

// store a lightweight consent cookie; make it readable by JS so the banner logic can detect acceptance
setcookie('consent', $accept ? '1' : '0', [
    'expires' => $accept ? time() + 31536000 : time() + 3600,
    'path' => '/',
    'secure' => $isSecure,
    'httponly' => false,
    'samesite' => 'Lax'
]);

// if accepted, enable a non-HttpOnly analytics cookie (optional)
if ($accept) {
    setcookie('analytics', '1', [
        'expires' => time() + 31536000,
        'path' => '/',
        'secure' => $isSecure,
        'httponly' => false,
        'samesite' => 'Lax'
    ]);
} else {
    // remove analytics cookie if present
    setcookie('analytics', '', [
        'expires' => time() - 3600,
        'path' => '/',
        'secure' => $isSecure,
        'httponly' => false,
        'samesite' => 'Lax'
    ]);
}

header('Content-Type: application/json');
echo json_encode(['consent' => $accept]);
