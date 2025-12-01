<?php
header('Content-Type: application/json');
$SERVER_TOKEN = "TOKEN-SECRETO-XYZ-123";

// Verificar Token
$headers = apache_request_headers();
$authHeader = $headers['Authorization'] ?? '';
if ($authHeader !== "Bearer " . $SERVER_TOKEN) {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Token inválido"]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$carritoCliente = $input['carrito'] ?? [];

// Cargar datos originales
$tiendaData = json_decode(file_get_contents('../data/tienda.json'), true);
$productosOriginales = $tiendaData['productos'];

$totalCalculado = 0;
$precioManipulado = false;

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
    echo json_encode(["success" => true, "message" => "Compra realizada con éxito. Total: $" . $totalCalculado]);
}
?>