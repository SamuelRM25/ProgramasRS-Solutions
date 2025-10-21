<?php
require_once '../conexion.php';

header('Content-Type: application/json');

try {
    // Validate input data
    $required_fields = [
        'codigo_med', 'nombre_med', 'molecula_med', 
        'presentacion_med', 'proveedor_med',
        'fecha_adquisicion', 'fecha_vencimiento'
    ];

    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("El campo $field es requerido");
        }
    }

    // Validate dates
    $fecha_adquisicion = new DateTime($_POST['fecha_adquisicion']);
    $fecha_vencimiento = new DateTime($_POST['fecha_vencimiento']);
    
    if ($fecha_vencimiento <= $fecha_adquisicion) {
        throw new Exception('La fecha de vencimiento debe ser posterior a la fecha de adquisición');
    }

    // Insert into database
    $query = "INSERT INTO inventario (
                codigo_med, 
                nombre_med, 
                molecula_med, 
                presentacion_med, 
                proveedor_med, 
                fecha_adquisicion, 
                fecha_vencimiento
            ) VALUES (
                :codigo,
                :nombre,
                :molecula,
                :presentacion,
                :proveedor,
                :fecha_adq,
                :fecha_ven
            )";
    
    $stmt = $conexion->prepare($query);
    $result = $stmt->execute([
        'codigo' => $_POST['codigo_med'],
        'nombre' => $_POST['nombre_med'],
        'molecula' => $_POST['molecula_med'],
        'presentacion' => $_POST['presentacion_med'],
        'proveedor' => $_POST['proveedor_med'],
        'fecha_adq' => $_POST['fecha_adquisicion'],
        'fecha_ven' => $_POST['fecha_vencimiento']
    ]);

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception('Error al guardar el medicamento');
    }

} catch(Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>