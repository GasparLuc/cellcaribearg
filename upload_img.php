<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Admin-Password');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit(0); }

define('ADMIN_PWD', 'Sarita2024');
define('IMG_DIR', __DIR__ . '/imgs/');
define('IMG_URL', '/imgs/');

// Auth
$headers = function_exists('getallheaders') ? getallheaders() : [];
$pwd = $headers['X-Admin-Password'] ?? $headers['x-admin-password'] ?? $_GET['pwd'] ?? '';
if ($pwd !== ADMIN_PWD) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'No autorizado']);
    exit;
}

// Create imgs dir if needed
if (!is_dir(IMG_DIR)) {
    mkdir(IMG_DIR, 0755, true);
}

// POST: upload file or save URL
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Mode 1: File upload
    if (isset($_FILES['image'])) {
        $file = $_FILES['image'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'Error al subir archivo']);
            exit;
        }

        // Validate type
        $allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        if (!in_array($mime, $allowed)) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'Solo se permiten JPG, PNG, WEBP']);
            exit;
        }

        // Generate unique filename
        $ext = match($mime) {
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif',
            default      => 'jpg'
        };
        $key = $_POST['key'] ?? ('img_' . time());
        $key = preg_replace('/[^a-z0-9_\-]/', '_', strtolower($key));
        $filename = $key . '.' . $ext;
        $dest = IMG_DIR . $filename;

        // Max 5MB
        if ($file['size'] > 5 * 1024 * 1024) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'Imagen demasiado grande (máx 5MB)']);
            exit;
        }

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            http_response_code(500);
            echo json_encode(['ok' => false, 'error' => 'No se pudo guardar la imagen']);
            exit;
        }

        echo json_encode(['ok' => true, 'url' => IMG_URL . $filename, 'key' => $key]);
        exit;
    }

    // Mode 2: URL — just validate and return
    $body = json_decode(file_get_contents('php://input'), true);
    if (isset($body['url'])) {
        $url = filter_var($body['url'], FILTER_VALIDATE_URL);
        if (!$url) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'URL inválida']);
            exit;
        }
        echo json_encode(['ok' => true, 'url' => $url]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Nada que procesar']);
    exit;
}

http_response_code(405);
echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
