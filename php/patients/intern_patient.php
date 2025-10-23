<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

verify_session();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../patients/medical_history.php?id=" . $_POST['id_paciente']);
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    $id_paciente = $_POST['id_paciente'];
    $motivo_internacion = $_POST['motivo_internacion'];
    $diagnostico_preliminar = $_POST['diagnostico_preliminar'];
    $medicamentos_iniciales = $_POST['medicamentos_iniciales'];

    // Insertar en la tabla de ingresos
    $stmt = $conn->prepare("
        INSERT INTO encamamiento_ingresos (id_paciente, medico_encargado, cobro_por_noche, estado)
        VALUES (?, ?, 0, 'Activo')
    ");
    $stmt->execute([$id_paciente, $_SESSION['nombre'] . ' ' . $_SESSION['apellido']]);

    $id_ingreso = $conn->lastInsertId();

    // Insertar en la tabla de medicamentos administrados (si hay)
    if (!empty($medicamentos_iniciales)) {
        $stmt = $conn->prepare("
            INSERT INTO encamamiento_medicamentos (id_ingreso, id_inventario, cantidad, dosis, fecha_hora)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$id_ingreso, 1, 1, $medicamentos_iniciales, date('Y-m-d H:i:s')]);
    }

    $_SESSION['message'] = "Paciente internado correctamente.";
    $_SESSION['message_type'] = "success";
    header("Location: ../patients/medical_history.php?id=" . $id_paciente);
} catch (Exception $e) {
    $_SESSION['message'] = "Error: " . $e->getMessage();
    $_SESSION['message_type'] = "danger";
    header("Location: ../patients/medical_history.php?id=" . $id_paciente);
}