<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

verify_session();

header('Content-Type: application/json');

$id_paciente = $_GET['id_paciente'] ?? 0;

try {
    $database = new Database();
    $conn = $database->getConnection();

    $stmt = $conn->prepare("
        SELECT sl.id_solicitud, tl.nombre as laboratorio, sl.estado, sl.resultados, sl.fecha_resultado
        FROM solicitudes_laboratorio sl
        JOIN tipos_laboratorio tl ON sl.id_tipo_laboratorio = tl.id_tipo_laboratorio
        WHERE sl.id_paciente = ? AND sl.estado = 'Completado' AND sl.fecha_resultado > DATE_SUB(NOW(), INTERVAL 10 MINUTE)
    ");
    $stmt->execute([$id_paciente]);
    $labs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'success', 'labs' => $labs]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}