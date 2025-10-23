<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

verify_session();

if (!isset($_GET['id'])) {
    header("Location: ../encamamiento/medicamentos.php?id=" . $_POST['id_ingreso']);
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    $id_medicamento_admin = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM encamamiento_medicamentos WHERE id_medicamento_admin = ?");
    $stmt->execute([$id_medicamento_admin]);

    $_SESSION['message'] = "Medicamento eliminado correctamente.";
    $_SESSION['message_type'] = "success";
    header("Location: ../encamamiento/medicamentos.php?id=" . $_POST['id_ingreso']);
} catch (Exception $e) {
    $_SESSION['message'] = "Error: " . $e->getMessage();
    $_SESSION['message_type'] = "danger";
    header("Location: ../encamamiento/medicamentos.php?id=" . $_POST['id_ingreso']);
}