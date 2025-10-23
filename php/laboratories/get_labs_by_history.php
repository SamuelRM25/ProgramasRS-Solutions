<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';
verify_session();

header('Content-Type: application/json');

$id_historial = $_GET['id_historial'] ?? 0;

try {
    $database = new Database();
    $conn = $database->getConnection();
    if (!$conn) {
        throw new Exception("Failed to obtain database connection.");
    }

    $stmt = $conn->prepare("
        SELECT sl.id_solicitud, tl.nombre as laboratorio, sl.estado, sl.resultados, sl.fecha_solicitud
        FROM solicitudes_laboratorio sl
        JOIN tipos_laboratorio tl ON sl.id_tipo_laboratorio = tl.id_tipo_laboratorio
        WHERE sl.id_historial = ?
        ORDER BY sl.fecha_solicitud DESC
    ");
    $stmt->execute([$id_historial]);
    $labs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($stmt->rowCount() > 0) { 
        echo json_encode(['status' => 'success', 'labs' => $labs]); 
    } else { 
        echo json_encode(['status' => 'success', 'labs' => []]); 
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>