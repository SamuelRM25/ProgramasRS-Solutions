<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';
verify_session();

$id_ingreso = $_GET['id'] ?? null;
if (!$id_ingreso) die("ID inválido");

try {
    $database = new Database();
    $conn = $database->getConnection();

    // Datos del ingreso
    $stmt = $conn->prepare("
        SELECT i.*, CONCAT(p.nombre,' ',p.apellido) AS paciente, p.id_paciente
        FROM encamamiento_ingresos i
        JOIN pacientes p ON p.id_paciente = i.id_paciente
        WHERE i.id_ingreso = ?
    ");
    $stmt->execute([$id_ingreso]);
    $ingreso = $stmt->fetch(PDO::FETCH_ASSOC);

    // Medicamentos
    $stmt = $conn->prepare("
        SELECT m.*, i.nom_medicamento, i.precio_venta
        FROM encamamiento_medicamentos m
        JOIN inventario i ON i.id_inventario = m.id_inventario
        WHERE m.id_ingreso = ?
    ");
    $stmt->execute([$id_ingreso]);
    $medicamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Noches
    $fechaIng = new DateTime($ingreso['fecha_ingreso']);
    $fechaAlt = new DateTime($ingreso['fecha_alta']);
    $noches = $fechaAlt->diff($fechaIng)->days ?: 1;
    $totalNoches = $noches * $ingreso['cobro_por_noche'];

    // Total medicamentos
    $totalMeds = 0;
    foreach ($medicamentos as $m) $totalMeds += $m['cantidad'] * $m['precio_venta'];

    $totalGeneral = $totalNoches + $totalMeds;
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Resumen de Alta - Encamamiento</title>
  <style>
    body{font-family:'Courier New',monospace;margin:0;padding:15px;background:#fff}
    .ticket{width:80mm;margin:0 auto;background:#fff;padding:10px}
    .center{text-align:center}.line{border-top:1px dashed #000;margin:10px 0}
    @media print{body{background:#fff}.ticket{box-shadow:none;width:100%}}
  </style>
</head>
<body onload="window.print()">
  <div class="ticket">
    <div class="center">
      <h4 style="margin:0">Servicios Médicos Siloé</h4>
      <p style="margin:3px 0">Nentón, Huehuetenango<br>Tel: 4623-2418</p>
    </div>
    <div class="line"></div>
    <p><strong>Paciente:</strong> <?= htmlspecialchars($ingreso['paciente']) ?></p>
    <p><strong>Ingreso:</strong> <?= date('d/m/Y H:i', strtotime($ingreso['fecha_ingreso'])) ?></p>
    <p><strong>Alta:</strong> <?= date('d/m/Y H:i', strtotime($ingreso['fecha_alta'])) ?></p>
    <p><strong>Noches:</strong> <?= $noches ?></p>
    <p><strong>Cobro x noche:</strong> Q<?= number_format($ingreso['cobro_por_noche'], 2) ?></p>
    <p><strong>Total Noches:</strong> Q<?= number_format($totalNoches, 2) ?></p>
    <div class="line"></div>
    <p><strong>Medicamentos/Insumos:</strong></p>
    <?php foreach ($medicamentos as $m): ?>
      <p><?= htmlspecialchars($m['nom_medicamento']) ?> × <?= $m['cantidad'] ?> - Q<?= number_format($m['precio_venta'], 2) ?></p>
    <?php endforeach; ?>
    <p><strong>Total Medicamentos:</strong> Q<?= number_format($totalMeds, 2) ?></p>
    <div class="line"></div>
    <p><strong>TOTAL GENERAL:</strong> Q<?= number_format($totalGeneral, 2) ?></p>
    <div class="line"></div>
    <p class="center">¡Gracias por su preferencia!<br>¡Recupérese pronto!</p>
  </div>
</body>
</html>