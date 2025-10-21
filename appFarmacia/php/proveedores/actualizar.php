<?php
require_once '../conexion.php';

header('Content-Type: application/json');

try {
    if (empty($_POST['id_proveedor']) || empty($_POST['cf_visitador']) || 
        empty($_POST['casa_farmaceutica']) || empty($_POST['phone_visitador'])) {
        throw new Exception('Todos los campos son requeridos');
    }

    $query = "UPDATE proveedores 
              SET cf_visitador = :visitador,
                  casa_farmaceutica = :casa,
                  phone_visitador = :telefono
              WHERE id_proveedor = :id";
    
    $stmt = $conexion->prepare($query);
    $result = $stmt->execute([
        'visitador' => $_POST['cf_visitador'],
        'casa' => $_POST['casa_farmaceutica'],
        'telefono' => $_POST['phone_visitador'],
        'id' => $_POST['id_proveedor']
    ]);

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception('Error al actualizar el proveedor');
    }

} catch(Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>