<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

verify_session();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../encamamiento/laboratorios.php?id=" . $_POST['id_ingreso']);
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    $id_paciente = $_POST['id_paciente'];
    $id_ingreso = $_POST['id_ingreso'];
    $id_tipo_laboratorio = $_POST['id_tipo_laboratorio'];

    $stmt = $conn->prepare("
        INSERT INTO solicitudes_laboratorio (id_historial, id_paciente, id_tipo_laboratorio, estado)
        VALUES ((SELECT id_historial FROM encamamiento_ingresos WHERE id_ingreso = ?), ?, ?, 'Pendiente')
    ");
    $stmt->execute([$id_ingreso, $id_paciente, $id_tipo_laboratorio]);

    $_SESSION['message'] = "Solicitud de laboratorio registrada correctamente.";
    $_SESSION['message_type'] = "success";
    header("Location: ../encamamiento/laboratorios.php?id=" . $id_ingreso);
} catch (Exception $e) {
    $_SESSION['message'] = "Error: " . $e->getMessage();
    $_SESSION['message_type'] = "danger";
    header("Location: ../encamamiento/laboratorios.php?id=" . $id_ingreso);
}