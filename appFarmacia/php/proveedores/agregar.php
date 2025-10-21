<?php
require_once '../conexion.php';

header('Content-Type: application/json');

try {
    // Validate input data
    if (empty($_POST['cf_visitador']) || empty($_POST['casa_farmaceutica']) || empty($_POST['phone_visitador'])) {
        throw new Exception('Todos los campos son requeridos');
    }

    // Prepare and execute the insert query
    $query = "INSERT INTO proveedores (cf_visitador, casa_farmaceutica, phone_visitador) 
              VALUES (:visitador, :casa, :telefono)";
    
    $stmt = $conexion->prepare($query);
    $result = $stmt->execute([
        'visitador' => $_POST['cf_visitador'],
        'casa' => $_POST['casa_farmaceutica'],
        'telefono' => $_POST['phone_visitador']
    ]);

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception('Error al guardar el proveedor');
    }

} catch(Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>