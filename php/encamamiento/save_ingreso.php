<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

verify_session();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    $id_cama = $_POST['id_cama'];
    $id_paciente = $_POST['id_paciente'];
    $medico_encargado = trim($_POST['medico_encargado']);
    $cobro_por_noche = (float) $_POST['cobro_por_noche'];

    // Verificar que la cama siga disponible
    $stmt = $conn->prepare("SELECT * FROM camas WHERE id_cama = ? AND estado = 'Disponible'");
    $stmt->execute([$id_cama]);
    if (!$stmt->fetch()) {
        $_SESSION['message'] = "La cama ya no está disponible.";
        $_SESSION['message_type'] = "danger";
        header("Location: index.php");
        exit;
    }

    // Crear ingreso
    $stmt = $conn->prepare("
        INSERT INTO encamamiento_ingresos (id_paciente, id_cama, medico_encargado, cobro_por_noche, fecha_ingreso, estado)
        VALUES (?, ?, ?, ?, NOW(), 'Activo')
    ");
    $stmt->execute([$id_paciente, $id_cama, $medico_encargado, $cobro_por_noche]);

    // Marcar cama como ocupada
    $conn->prepare("UPDATE camas SET estado = 'Ocupada' WHERE id_cama = ?")->execute([$id_cama]);

    $_SESSION['message'] = "Paciente internado correctamente.";
    $_SESSION['message_type'] = "success";
    header("Location: index.php");
} catch (Exception $e) {
    $_SESSION['message'] = "Error: " . $e->getMessage();
    $_SESSION['message_type'] = "danger";
    header("Location: index.php");
}