<?php
// Cabeceras para permitir que JavaScript se comunique con PHP
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Configuración del token
$SERVER_TOKEN = "TOKEN-SECRETO-XYZ-123";
$authHeader = null;

if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
} elseif (function_exists('apache_request_headers')) {
    $headers = apache_request_headers();
    $authHeader = $headers['Authorization'] ?? '';
}

// Validar el Token
if ($authHeader !== "Bearer " . $SERVER_TOKEN) {
    http_response_code(403); 
    echo json_encode(["success" => false, "message" => "Acceso denegado: Token inválido"]);
    exit;
}

// Recibir los datos del historial enviado por app.js
$input = json_decode(file_get_contents('php://input'), true);
$historial = $input['historial'] ?? [];

// 5. Simulación de guardado 

if (is_array($historial)) {    
    echo json_encode([
        "success" => true, 
        "message" => "Historial sincronizado correctamente. Productos recibidos: " . count($historial),
        "received_ids" => $historial 
    ]);
} else {
    http_response_code(400); 
    echo json_encode([
        "success" => false, 
        "message" => "Formato de datos incorrecto. Se esperaba un array de IDs."
    ]);
}
?>