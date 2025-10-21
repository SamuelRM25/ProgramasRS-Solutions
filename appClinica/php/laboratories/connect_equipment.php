<?php
// Endpoint para recibir datos del MINDRAY CB-20 u otros equipos
// Este endpoint recibe JSON y guarda los resultados automáticamente

require_once '../../config/database.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['id_solicitud'], $input['resultados'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    $stmt = $conn->prepare("
        UPDATE solicitudes_laboratorio
        SET resultados = ?, estado = 'Completado', fecha_resultado = NOW()
        WHERE id_solicitud = ?
    ");
    $stmt->execute([json_encode($input['resultados']), $input['id_solicitud']]);

    echo json_encode(['status' => 'success', 'message' => 'Resultados recibidos y guardados']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}