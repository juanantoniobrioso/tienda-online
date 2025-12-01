<?php
// Desactivar que los errores se impriman en el HTML (ensucian el JSON)
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

// 1. Recibir datos
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    sendError("No se recibieron datos JSON válidos (usuario/password)", 400);
}

$user = $input['usuario'] ?? '';
$pass = $input['password'] ?? '';

// 2. Definir rutas absolutas usando __DIR__
// __DIR__ es la carpeta "api". Subimos un nivel para ir a "data"
$pathUsuarios = __DIR__ . '/../data/usuarios.json';
$pathTienda   = __DIR__ . '/../data/tienda.json';

// 3. Verificar que los archivos existen
if (!file_exists($pathUsuarios)) {
    sendError("Error del Servidor: No se encuentra el archivo: " . $pathUsuarios);
}
if (!file_exists($pathTienda)) {
    sendError("Error del Servidor: No se encuentra el archivo: " . $pathTienda);
}

// 4. Leer y procesar usuarios
$jsonUsuarios = file_get_contents($pathUsuarios);
$usuarios = json_decode($jsonUsuarios, true);

if ($usuarios === null) {
    sendError("Error: El archivo usuarios.json tiene un formato inválido.");
}

// 5. Validar Credenciales
$autenticado = false;
foreach ($usuarios as $u) {
    if ($u['usuario'] === $user && $u['password'] === $pass) {
        $autenticado = true;
        break;
    }
}

// 6. Respuesta final
if ($autenticado) {
    // Leemos la tienda para enviarla
    $tiendaData = json_decode(file_get_contents($pathTienda));
    
    echo json_encode([
        "success" => true,
        "token" => $SERVER_TOKEN,
        "data" => $tiendaData
    ]);
} else {
    // Devolvemos success:false pero con código 200 para que JS lo lea bien
    echo json_encode(["success" => false, "message" => "Usuario o contraseña incorrectos"]);
}

$totalCalculado = 0;
$precioManipulado = false;
/*
foreach ($carritoCliente as $itemCliente) {
    foreach ($productosOriginales as $prodOriginal) {
        if ($itemCliente['id'] == $prodOriginal['id']) {
            // Verificacion de precio
            if ($itemCliente['precio'] != $prodOriginal['precio']) {
                $precioManipulado = true;
            }
            $totalCalculado += $prodOriginal['precio'] * $itemCliente['cantidad'];
        }
    }
}

if ($precioManipulado) {
    echo json_encode(["success" => false, "message" => "ALERTA: Precios manipulados detectados."]);
} else {
    echo json_encode(["success" => true, "message" => "Compra realizada con éxito. Total: €" . $totalCalculado]);
}
    */
?>