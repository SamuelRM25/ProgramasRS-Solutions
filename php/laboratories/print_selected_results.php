<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';
verify_session();

if (empty($_POST['ids'])) {
    die("No se seleccionaron laboratorios.");
}

$ids = array_map('intval', $_POST['ids']);

try {
    $database = new Database();
    $conn = $database->getConnection();

    $stmt = $conn->prepare("
        SELECT sl.id_solicitud, sl.resultados, tl.nombre as laboratorio, sl.fecha_resultado,
            p.nombre, p.apellido, p.fecha_nacimiento, p.genero
        FROM solicitudes_laboratorio sl
        JOIN tipos_laboratorio tl ON sl.id_tipo_laboratorio = tl.id_tipo_laboratorio
        JOIN pacientes p ON sl.id_paciente = p.id_paciente
        WHERE sl.id_solicitud IN (" . implode(',', $ids) . ")
        ORDER BY sl.fecha_resultado DESC
    ");
    $stmt->execute();
    $labs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$labs) {
        die("No se encontraron resultados.");
    }

    $paciente = $labs[0];
    $edad = (new DateTime($paciente['fecha_nacimiento']))->diff(new DateTime())->y;

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultados de Laboratorio - <?= htmlspecialchars($paciente['nombre'] . ' ' . $paciente['apellido']) ?></title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 40px; background: #fff; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #007bff; padding-bottom: 20px; margin-bottom: 30px; }
        .header h2 { margin: 0; font-size: 26px; color: #007bff; }
        .section { margin-bottom: 30px; }
        .section h4 { text-transform: uppercase; border-bottom: 1px solid #ccc; padding-bottom: 5px; color: #007bff; }
        .result-box { background: #f8f9fa; padding: 15px; border-radius: 8px; white-space: pre-wrap; border: 1px solid #dee2e6; }
        .footer { margin-top: 40px; font-size: 12px; color: #777; text-align: center; }
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
        <p><strong>Nombre:</strong> <?= htmlspecialchars($paciente['nombre'] . ' ' . $paciente['apellido']) ?></p>
        <p><strong>Edad:</strong> <?= $edad ?> años</p>
        <p><strong>Género:</strong> <?= htmlspecialchars($paciente['genero']) ?></p>
    </div>

    <?php
    foreach ($labs as $lab):
        // Obtener valores de referencia para este tipo de laboratorio
        $refStmt = $conn->prepare("
            SELECT parametro, valor_min, valor_max, unidad
            FROM valores_referencia
            WHERE id_tipo_laboratorio = (
                SELECT id_tipo_laboratorio
                FROM solicitudes_laboratorio
                WHERE id_solicitud = ?
            )
        ");
        $refStmt->execute([$lab['id_solicitud']]);
         $referencias = $refStmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
        <div class="section">
            <h4><?= htmlspecialchars($lab['laboratorio']) ?> - <?= date('d/m/Y', strtotime($lab['fecha_resultado'])) ?></h4>
            <div class="result-box"><?= nl2br(htmlspecialchars($lab['resultados'])) ?></div>

            <?php if (!$referencias): ?>
                <p style="font-size: 12px; color: gray;">No hay valores de referencia para este examen.</p>
            <?php endif; ?>

            <?php if ($referencias): ?>
                <h5 style="margin-top: 15px; font-size: 14px; color: #555;">Valores de Referencia</h5>
                <table style="width: 100%; font-size: 13px; border-collapse: collapse; margin-top: 10px;">
                    <thead>
                        <tr style="background-color: #f0f0f0;">
                            <th style="border: 1px solid #ccc; padding: 6px;">Parámetro</th>
                            <th style="border: 1px solid #ccc; padding: 6px;">Mínimo</th>
                            <th style="border: 1px solid #ccc; padding: 6px;">Máximo</th>
                            <th style="border: 1px solid #ccc; padding: 6px;">Unidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($referencias as $ref): ?>
                            <tr>
                                <td style="border: 1px solid #ccc; padding: 6px;"><?= htmlspecialchars($ref['parametro']) ?></td>
                                <td style="border: 1px solid #ccc; padding: 6px;"><?= $ref['valor_min'] ?></td>
                                <td style="border: 1px solid #ccc; padding: 6px;"><?= $ref['valor_max'] ?></td>
                                <td style="border: 1px solid #ccc; padding: 6px;"><?= htmlspecialchars($ref['unidad']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <div class="footer">
        <p>Resultados validados por el laboratorio clínico.</p>
    </div>

    <script>window.print();</script>
</body>
</html>