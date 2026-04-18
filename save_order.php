<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit(0); }

$file = __DIR__ . '/orders.json';

// GET: return all orders
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $orders = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
    echo json_encode($orders ?: []);
    exit;
}

// POST: save new order OR update status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) { http_response_code(400); echo json_encode(['error'=>'Invalid JSON']); exit; }

    $orders = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
    if (!is_array($orders)) $orders = [];

    // If updating status of existing order
    if (isset($data['action']) && $data['action'] === 'update_status') {
        foreach ($orders as &$o) {
            if ($o['id'] === $data['id']) { $o['status'] = $data['status']; break; }
        }
        file_put_contents($file, json_encode($orders, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo json_encode(['ok' => true]);
        exit;
    }

    // New order
    $order = [
        'id'       => $data['id'] ?? strval(time()),
        'name'     => $data['name'] ?? '',
        'phone'    => $data['phone'] ?? '',
        'email'    => $data['email'] ?? '',
        'note'     => $data['note'] ?? '',
        'products' => $data['products'] ?? [],
        'status'   => 'nuevo',
        'date'     => $data['date'] ?? date('c'),
    ];
    array_unshift($orders, $order); // newest first
    file_put_contents($file, json_encode($orders, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo json_encode(['ok' => true, 'id' => $order['id']]);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
