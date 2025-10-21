<?php
require_once '../conexion.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
        throw new Exception('Método no permitido');
    }

    $id = $_GET['id'] ?? null;
    if (!$id) {
        throw new Exception('ID no proporcionado');
    }

    // Check if provider exists
    $check = $conexion->prepare("SELECT id_proveedor FROM proveedores WHERE id_proveedor = ?");
    $check->execute([$id]);
    if (!$check->fetch()) {
        throw new Exception('Proveedor no encontrado');
    }

    // Delete provider
    $query = "DELETE FROM proveedores WHERE id_proveedor = ?";
    $stmt = $conexion->prepare($query);
    $result = $stmt->execute([$id]);

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception('Error al eliminar el proveedor');
    }

} catch(Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>