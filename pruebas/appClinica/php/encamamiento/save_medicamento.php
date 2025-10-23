<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

verify_session();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../encamamiento/medicamentos.php?id=" . $_POST['id_ingreso']);
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    $id_ingreso = $_POST['id_ingreso'];
    $id_inventario = $_POST['id_inventario'];
    $cantidad = $_POST['cantidad'];
    $dosis = $_POST['dosis'];

    $stmt = $conn->prepare("
        INSERT INTO encamamiento_medicamentos (id_ingreso, id_inventario, cantidad, dosis, fecha_hora)
        VALUES (?, ?, ?, ?, NOW())
    ");
    $stmt->execute([$id_ingreso, $id_inventario, $cantidad, $dosis]);

    $_SESSION['message'] = "Medicamento agregado correctamente.";
    $_SESSION['message_type'] = "success";
    header("Location: ../encamamiento/medicamentos.php?id=" . $id_ingreso);
} catch (Exception $e) {
    $_SESSION['message'] = "Error: " . $e->getMessage();
    $_SESSION['message_type'] = "danger";
    header("Location: ../encamamiento/medicamentos.php?id=" . $id_ingreso);
}