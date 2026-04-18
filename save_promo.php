<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Admin-Password');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit(0); }

define('ADMIN_PWD', 'clonewhitoutmypassword');
$file = __DIR__ . '/promo.json';

function isAdmin() {
    $h = function_exists('getallheaders') ? getallheaders() : [];
    $pwd = $h['X-Admin-Password'] ?? $h['x-admin-password'] ?? $_GET['pwd'] ?? '';
    return $pwd === ADMIN_PWD;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!file_exists($file)) {
        echo json_encode(['active' => false]);
    } else {
        readfile($file);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isAdmin()) { http_response_code(401); echo json_encode(['ok'=>false,'error'=>'No autorizado']); exit; }
    $data = json_decode(file_get_contents('php://input'), true);
    if ($data === null) { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'JSON inválido']); exit; }
    file_put_contents($file, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    echo json_encode(['ok' => true]);
    exit;
}
