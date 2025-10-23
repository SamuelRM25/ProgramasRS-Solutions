<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';
verify_session();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$id_ingreso = $_POST['id_ingreso'] ?? null;
if (!$id_ingreso) {
    $_SESSION['message'] = "ID de ingreso no válido.";
    $_SESSION['message_type'] = "danger";
    header("Location: index.php");
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    // Actualizar ingreso
    $conn->prepare("UPDATE encamamiento_ingresos SET fecha_alta = NOW(), estado = 'Alta' WHERE id_ingreso = ?")->execute([$id_ingreso]);

    // Liberar cama
    $conn->prepare("
        UPDATE camas c
        JOIN encamamiento_ingresos i ON i.id_cama = c.id_cama
        SET c.estado = 'Disponible'
        WHERE i.id_ingreso = ?
    ")->execute([$id_ingreso]);

    $_SESSION['message'] = "Alta registrada correctamente.";
    $_SESSION['message_type'] = "success";

    // Redirigir a impresión
    header("Location: print_resumen.php?id=$id_ingreso");
} catch (Exception $e) {
    $_SESSION['message'] = "Error: " . $e->getMessage();
    $_SESSION['message_type'] = "danger";
    header("Location: index.php");
}