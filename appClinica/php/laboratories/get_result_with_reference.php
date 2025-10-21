<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';
verify_session();

header('Content-Type: application/json');

$id = $_GET['id'] ?? 0;

try {
    $database = new Database();
    $conn = $database->getConnection();

    $stmt = $conn->prepare("
        SELECT sl.resultados, tl.nombre as laboratorio, p.nombre, p.apellido, sl.fecha_resultado
        FROM solicitudes_laboratorio sl
        JOIN tipos_laboratorio tl ON sl.id_tipo_laboratorio = tl.id_tipo_laboratorio
        JOIN pacientes p ON sl.id_paciente = p.id_paciente
        WHERE sl.id_solicitud = ?
    ");
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        echo json_encode(['status' => 'error', 'message' => 'No se encontraron resultados']);
        exit;
    }

    $refStmt = $conn->prepare("SELECT parametro, valor_min, valor_max, unidad FROM valores_referencia WHERE id_tipo_laboratorio = (SELECT id_tipo_laboratorio FROM solicitudes_laboratorio WHERE id_solicitud = ?)");
    $refStmt->execute([$id]);
    $referencias = $refStmt->fetchAll(PDO::FETCH_ASSOC);

    ob_start(); ?>
        <div class="row">
            <div class="col-md-6">
                <h6>Paciente: <?= htmlspecialchars($data['nombre'] . ' ' . $data['apellido']) ?></h6>
                <h6>Laboratorio: <?= htmlspecialchars($data['laboratorio']) ?></h6>
                <h6>Fecha de Resultado: <?= date('d/m/Y H:i', strtotime($data['fecha_resultado'])) ?></h6>
                <hr>
                <h6>Resultados:</h6>
                <pre><?= nl2br(htmlspecialchars($data['resultados'])) ?></pre>
            </div>
            <div class="col-md-6">
                <h6>Valores de Referencia:</h6>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Parámetro</th>
                            <th>Valor Min</th>
                            <th>Valor Max</th>
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
        </div>
    <?php
    $html = ob_get_clean();
    echo json_encode(['status' => 'success', 'html' => $html]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>