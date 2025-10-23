<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

verify_session();

$id = $_GET['id'] ?? 0;

try {
    $database = new Database();
    $conn = $database->getConnection();

    $stmt = $conn->prepare("
        SELECT sl.*, tl.nombre as laboratorio, p.nombre, p.apellido, p.fecha_nacimiento, p.genero
        FROM solicitudes_laboratorio sl
        JOIN tipos_laboratorio tl ON sl.id_tipo_laboratorio = tl.id_tipo_laboratorio
        JOIN pacientes p ON sl.id_paciente = p.id_paciente
        WHERE sl.id_solicitud = ?
    ");
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data || $data['estado'] !== 'Completado') {
        die("Resultado no disponible");
    }

    $edad = (new DateTime($data['fecha_nacimiento']))->diff(new DateTime())->y;

// Obtener valores de referencia
    $refStmt = $conn->prepare("
        SELECT parametro, valor_min, valor_max, unidad
        FROM valores_referencia
        WHERE id_tipo_laboratorio = (
            SELECT id_tipo_laboratorio FROM solicitudes_laboratorio WHERE id_solicitud = ?
        )
    ");
    $refStmt->execute([$id]);
    $referencias = $refStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultados de Laboratorio</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 40px;
            background: #fff;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            font-size: 26px;
            color: #007bff;
        }

        .header p {
            margin: 5px 0;
            font-size: 14px;
            color: #555;
        }

        .section {
            margin-bottom: 30px;
        }

        .section h4 {
            margin-bottom: 10px;
            font-size: 18px;
            text-transform: uppercase;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
            color: #007bff;
        }

        .result-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            font-size: 15px;
            white-space: pre-wrap;
            border: 1px solid #dee2e6;
        }

        .reference-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .reference-table th,
        .reference-table td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }

        .reference-table th {
            background-color: #e9ecef;
            font-weight: bold;
        }

        .footer {
            margin-top: 40px;
            font-size: 12px;
            color: #777;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Resultados de Laboratorio Clínico</h2>
        <p><strong>Centro Médico de Pruebas</strong></p>
        <p>Huehuetenango, Huehuetenango | Tel: 3902-9076</p>
    </div>

    <div class="section">
        <h4>Datos del Paciente</h4>
        <p><strong>Nombre:</strong> <?= htmlspecialchars($data['nombre'] . ' ' . $data['apellido']) ?></p>
        <p><strong>Edad:</strong> <?= $edad ?> años</p>
        <p><strong>Género:</strong> <?= htmlspecialchars($data['genero']) ?></p>
        <p><strong>Laboratorio:</strong> <?= htmlspecialchars($data['laboratorio']) ?></p>
        <p><strong>Fecha de Resultado:</strong> <?= date('d/m/Y H:i', strtotime($data['fecha_resultado'])) ?></p>
    </div>

    <div class="section">
        <h4>Resultados</h4>
        <div class="result-box"><?= nl2br(htmlspecialchars($data['resultados'])) ?></div>
    </div>

    <?php if ($referencias): ?>
        <div class="section">
            <h4>Valores de Referencia</h4>
            <table class="reference-table">
                <thead>
                    <tr>
                        <th>Parámetro</th>
                        <th>Valor Mínimo</th>
                        <th>Valor Máximo</th>
                        <th>Unidad</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($referencias as $ref): ?>
                        <tr>
                            <td><?= htmlspecialchars($ref['parametro']) ?></td>
                            <td><?= $ref['valor_min'] ?></td>
                            <td><?= $ref['valor_max'] ?></td>
                            <td><?= htmlspecialchars($ref['unidad']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <div class="footer">
        <p>Resultado validado por el laboratorio clínico.</p>
        <p>Firma digital: <?= md5($data['id_solicitud'] . $data['fecha_resultado']) ?></p>
    </div>

    <script>window.print();</script>
</body>
</html>