<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

verify_session();

if (!isset($_GET['id'])) die("ID inválido");

$id = $_GET['id'];

try {
    $database = new Database();
    $conn = $database->getConnection();

    $stmt = $conn->prepare("
        SELECT sl.*, tl.nombre as laboratorio_nombre, p.nombre as paciente_nombre, p.apellido as paciente_apellido, p.fecha_nacimiento, p.genero
        FROM solicitudes_laboratorio sl
        JOIN tipos_laboratorio tl ON sl.id_tipo_laboratorio = tl.id_tipo_laboratorio
        JOIN pacientes p ON sl.id_paciente = p.id_paciente
        WHERE sl.id_solicitud = ?
    ");
    $stmt->execute([$id]);
    $lab = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$lab) die("Solicitud no encontrada");

    // Calcular edad
    $fecha_nac = new DateTime($lab['fecha_nacimiento']);
    $edad = $fecha_nac->diff(new DateTime())->y;

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitud de Laboratorio</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .info { margin-bottom: 20px; }
        .footer { margin-top: 40px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Centro Médico de Pruebas</h2>
        <p>Solicitud de Laboratorio Clínico</p>
    </div>

    <div class="info">
        <p><strong>Paciente:</strong> <?= htmlspecialchars($lab['paciente_nombre'] . ' ' . $lab['paciente_apellido']) ?></p>
        <p><strong>Edad:</strong> <?= $edad ?> años</p>
        <p><strong>Género:</strong> <?= htmlspecialchars($lab['genero']) ?></p>
        <p><strong>Fecha de Solicitud:</strong> <?= date('d/m/Y', strtotime($lab['fecha_solicitud'])) ?></p>
        <p><strong>Laboratorio Solicitado:</strong> <?= htmlspecialchars($lab['laboratorio_nombre']) ?></p>
        <p><strong>Observaciones:</strong> <?= nl2br(htmlspecialchars($lab['observaciones'])) ?></p>
    </div>

    <div class="footer">
        <p>Médico Solicitante: <?= htmlspecialchars($_SESSION['nombre'] . ' ' . $_SESSION['apellido']) ?></p>
        <p>Firma: __________________________</p>
    </div>

    <script>window.print();</script>
</body>
</html>