<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';
verify_session();

header('Content-Type: application/json');

$search = $_GET['q'] ?? '';

try {
    $database = new Database();
    $conn = $database->getConnection();

    $stmt = $conn->prepare("
        SELECT id_tipo_laboratorio, nombre
        FROM tipos_laboratorio
        WHERE activo = 1 AND nombre LIKE ?
        ORDER BY nombre
        LIMIT 50
    ");
    $stmt->execute(["%$search%"]);
    $labs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'success', 'labs' => $labs]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>