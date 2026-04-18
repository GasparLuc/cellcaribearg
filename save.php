<?php
define('ADMIN_PASSWORD', 'YOUR_PASSWORD_HERE');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Admin-Password');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$headers  = function_exists('getallheaders') ? getallheaders() : [];
$password = $headers['X-Admin-Password'] ?? $headers['x-admin-password'] ?? $_GET['pwd'] ?? '';

if ($password !== ADMIN_PASSWORD) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Contraseña incorrecta']);
    exit;
}

$type = $_GET['type'] ?? 'products';
$file = $type === 'stock' ? __DIR__ . '/stock.json' : __DIR__ . '/products.json';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!file_exists($file)) { echo $type === 'stock' ? '{"new":[],"used":[]}' : '[]'; exit; }
    header('Content-Type: application/json; charset=utf-8');
    readfile($file);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if ($data === null) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'JSON invalido']);
        exit;
    }
    $result = file_put_contents($file, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    if ($result === false) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => 'Error al guardar. Verificar permisos (chmod 644)']);
        exit;
    }
    $label = $type === 'stock' ? 'stock' : 'productos';
    echo json_encode(['ok' => true, 'saved' => "Archivo de $label guardado"]);
    exit;
}

http_response_code(405);
echo json_encode(['ok' => false, 'error' => 'Metodo no permitido']);
