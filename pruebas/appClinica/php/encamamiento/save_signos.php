<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

verify_session();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../encamamiento/signos.php?id=" . $_POST['id_ingreso']);
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    $id_ingreso = $_POST['id_ingreso'];
    $pa = $_POST['pa'];
    $fc = $_POST['fc'];
    $fr = $_POST['fr'];
    $glucosa = $_POST['glucosa'];
    $temperatura = $_POST['temperatura'];

    $stmt = $conn->prepare("
        INSERT INTO encamamiento_signos (id_ingreso, pa, fc, fr, glucosa, temperatura, fecha_hora)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([$id_ingreso, $pa, $fc, $fr, $glucosa, $temperatura]);

    $_SESSION['message'] = "Signos vitales registrados correctamente.";
    $_SESSION['message_type'] = "success";
    header("Location: ../encamamiento/signos.php?id=" . $id_ingreso);
} catch (Exception $e) {
    $_SESSION['message'] = "Error: " . $e->getMessage();
    $_SESSION['message_type'] = "danger";
    header("Location: ../encamamiento/signos.php?id=" . $id_ingreso);
}