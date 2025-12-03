<?php
// Desactivar que los errores se impriman en el HTML
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

// Función para devolver error JSON y salir limpiamente
function sendError($message, $code = 500) {
    http_response_code($code);
    echo json_encode(["success" => false, "message" => $message]);
    exit;
}

$SERVER_TOKEN = "TOKEN-SECRETO-XYZ-123";

// Recibir datos
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    sendError("No se recibieron datos JSON válidos (usuario/password)", 400);
}

$user = $input['usuario'] ?? '';
$pass = $input['password'] ?? '';

// Definir rutas absolutas usando __DIR__
$pathUsuarios = __DIR__ . '/../data/usuarios.json';
$pathTienda   = __DIR__ . '/../data/tienda.json';

// Verificar que los archivos existen
if (!file_exists($pathUsuarios)) {
    sendError("Error del Servidor: No se encuentra el archivo: " . $pathUsuarios);
}
if (!file_exists($pathTienda)) {
    sendError("Error del Servidor: No se encuentra el archivo: " . $pathTienda);
}

// Leer y procesar usuarios
$jsonUsuarios = file_get_contents($pathUsuarios);
$usuarios = json_decode($jsonUsuarios, true);

if ($usuarios === null) {
    sendError("Error: El archivo usuarios.json tiene un formato inválido.");
}

// Validar Credenciales
$autenticado = false;
foreach ($usuarios as $u) {
    if ($u['usuario'] === $user && $u['password'] === $pass) {
        $autenticado = true;
        break;
    }
}

// Respuesta final
if ($autenticado) {
    // Leemos la tienda para enviarla
    $tiendaData = json_decode(file_get_contents($pathTienda));
    
    echo json_encode([
        "success" => true,
        "token" => $SERVER_TOKEN,
        "data" => $tiendaData
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Usuario o contraseña incorrectos"]);
}

?>