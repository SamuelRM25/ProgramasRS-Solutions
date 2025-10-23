<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

verify_session();

try {
    $database = new Database();
    $conn = $database->getConnection();

    $stmt = $conn->query("
        SELECT id_inventario, nom_medicamento, presentacion_med, precio_venta, cantidad_med
        FROM inventario
        WHERE cantidad_med > 0
        ORDER BY nom_medicamento
    ");
    $meds = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($meds);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}