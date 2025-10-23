<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

verify_session();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_solicitud'] ?? null;
    $resultados = $_POST['resultados'] ?? '';

    if (!$id || !$resultados) {
        $_SESSION['message'] = "Datos incompletos";
        $_SESSION['message_type'] = "danger";
        header("Location: index.php");
        exit;
    }

    try {
        $database = new Database();
        $conn = $database->getConnection();

        $stmt = $conn->prepare("
            UPDATE solicitudes_laboratorio
            SET resultados = ?, estado = 'Completado', fecha_resultado = NOW()
            WHERE id_solicitud = ?
        ");
        $stmt->execute([$resultados, $id]);

        $_SESSION['message'] = "Resultado registrado correctamente";
        $_SESSION['message_type'] = "success";
    } catch (Exception $e) {
        $_SESSION['message'] = "Error: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
    }

    header("Location: index.php");
    exit;
}