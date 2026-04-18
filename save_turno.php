<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Admin-Password');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit(0); }

define('ADMIN_PWD', 'Sarita2024');
$file = __DIR__ . '/turnos.json';

function loadTurnos($file) {
    if (!file_exists($file)) return [];
    $data = json_decode(file_get_contents($file), true);
    return is_array($data) ? $data : [];
}
function saveTurnos($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}
function isAdmin() {
    $h = function_exists('getallheaders') ? getallheaders() : [];
    $pwd = $h['X-Admin-Password'] ?? $h['x-admin-password'] ?? $_GET['pwd'] ?? '';
    return $pwd === ADMIN_PWD;
}

// GET — public: booked slots for a date  |  admin: all turnos
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $turnos = loadTurnos($file);
    if (isAdmin()) {
        echo json_encode($turnos); exit;
    }
    $date = $_GET['date'] ?? '';
    if (!$date) { echo json_encode([]); exit; }
    $booked = array_values(array_map(function($t){ return $t['time']; },
        array_filter($turnos, function($t) use ($date){
            return $t['date'] === $date && in_array($t['status'], ['pendiente','confirmado','bloqueado']);
        })));
    echo json_encode($booked); exit;
}

// POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true);
    if (!$body) { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'JSON inválido']); exit; }
    $turnos = loadTurnos($file);

    // Admin: update status or block
    if (isAdmin() && isset($body['action'])) {
        if ($body['action'] === 'update_status') {
            foreach ($turnos as &$t) {
                if ($t['id'] === $body['id']) { $t['status'] = $body['status']; break; }
            }
            saveTurnos($file, $turnos);
            echo json_encode(['ok'=>true]); exit;
        }
        if ($body['action'] === 'block') {
            $turnos[] = ['id'=>uniqid(),'date'=>$body['date'],'time'=>$body['time'],
                'name'=>'BLOQUEADO','phone'=>'','problem'=>$body['reason']??'',
                'status'=>'bloqueado','created'=>date('c')];
            saveTurnos($file, $turnos);
            echo json_encode(['ok'=>true]); exit;
        }
    }

    // Public: new turno
    $date = $body['date'] ?? ''; $time = $body['time'] ?? '';
    $name = $body['name'] ?? ''; $phone = $body['phone'] ?? '';
    if (!$date || !$time || !$name || !$phone) {
        http_response_code(400); echo json_encode(['ok'=>false,'error'=>'Faltan datos']); exit;
    }
    // Check slot not taken
    foreach ($turnos as $t) {
        if ($t['date']===$date && $t['time']===$time && in_array($t['status'],['pendiente','confirmado','bloqueado'])) {
            http_response_code(409); echo json_encode(['ok'=>false,'error'=>'Slot ya ocupado']); exit;
        }
    }
    $turno = ['id'=>strval(time()),'date'=>$date,'time'=>$time,'name'=>$name,
        'phone'=>$phone,'problem'=>$body['problem']??'','status'=>'pendiente','created'=>date('c')];
    array_unshift($turnos, $turno);
    saveTurnos($file, $turnos);
    echo json_encode(['ok'=>true,'id'=>$turno['id']]); exit;
}
http_response_code(405); echo json_encode(['ok'=>false,'error'=>'Método no permitido']);
