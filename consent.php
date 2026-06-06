<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$accept = isset($data['action']) && $data['action'] === 'accept';

// store a lightweight consent cookie (server-side readable, HttpOnly)
setcookie('consent', $accept ? '1' : '0', [
    'expires' => $accept ? time() + 31536000 : time() + 3600,
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);

// if accepted, enable a non-HttpOnly analytics cookie (optional)
if ($accept) {
    setcookie('analytics', '1', [
        'expires' => time() + 31536000,
        'path' => '/',
        'secure' => true,
        'httponly' => false,
        'samesite' => 'Lax'
    ]);
} else {
    // remove analytics cookie if present
    setcookie('analytics', '', [
        'expires' => time() - 3600,
        'path' => '/',
        'secure' => true,
        'httponly' => false,
        'samesite' => 'Lax'
    ]);
}

header('Content-Type: application/json');
echo json_encode(['consent' => $accept]);
