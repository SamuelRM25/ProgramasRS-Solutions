<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';
verify_session();

$id_ingreso = $_GET['id'] ?? null;
if (!$id_ingreso) {
    header("Location: index.php");
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    $stmt = $conn->prepare("
        SELECT i.*, CONCAT(p.nombre,' ',p.apellido) AS paciente, p.id_paciente
        FROM encamamiento_ingresos i
        JOIN pacientes p ON p.id_paciente = i.id_paciente
        WHERE i.id_ingreso = ?
    ");
    $stmt->execute([$id_ingreso]);
    $ingreso = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ingreso || $ingreso['estado'] !== 'Activo') {
        $_SESSION['message'] = "No se puede dar alta a este ingreso.";
        $_SESSION['message_type'] = "danger";
        header("Location: index.php");
        exit;
    }

    // Medicamentos administrados
    $stmt = $conn->prepare("
        SELECT m.*, i.nom_medicamento, i.precio_venta
        FROM encamamiento_medicamentos m
        JOIN inventario i ON i.id_inventario = m.id_inventario
        WHERE m.id_ingreso = ?
    ");
    $stmt->execute([$id_ingreso]);
    $medicamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Signos vitales
    $stmt = $conn->prepare("SELECT * FROM encamamiento_signos WHERE id_ingreso = ? ORDER BY fecha_hora DESC");
    $stmt->execute([$id_ingreso]);
    $signos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Laboratorios
    $stmt = $conn->prepare("
        SELECT sl.*, tl.nombre AS laboratorio
        FROM solicitudes_laboratorio sl
        JOIN tipos_laboratorio tl ON tl.id_tipo_laboratorio = sl.id_tipo_laboratorio
        JOIN historial_clinico h ON h.id_historial = sl.id_historial
        WHERE h.id_paciente = ? AND sl.estado = 'Completado'
        ORDER BY sl.fecha_resultado DESC
    ");
    $stmt->execute([$ingreso['id_paciente']]);
    $labs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Cálculo de noches
    $fechaIng = new DateTime($ingreso['fecha_ingreso']);
    $fechaAlt = new DateTime();
    $noches = $fechaAlt->diff($fechaIng)->days ?: 1;
    $totalNoches = $noches * $ingreso['cobro_por_noche'];

    // Total medicamentos
    $totalMeds = 0;
    foreach ($medicamentos as $m) $totalMeds += $m['cantidad'] * $m['precio_venta'];

    $totalGeneral = $totalNoches + $totalMeds;

    $page_title = "Dar Alta - Ingreso #$id_ingreso";
    include_once '../../includes/header.php';
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<div class="container-fluid py-4">
    <h2><i class="bi bi-box-arrow-up"></i> Dar Alta - <?= htmlspecialchars($ingreso['paciente']) ?></h2>

    <div class="row">
        <div class="col-md-6">
            <h5>Resumen de Ingreso</h5>
            <ul class="list-group mb-3">
                <li class="list-group-item"><strong>Ingreso:</strong> <?= date('d/m/Y H:i', strtotime($ingreso['fecha_ingreso'])) ?></li>
                <li class="list-group-item"><strong>Noches:</strong> <?= $noches ?></li>
                <li class="list-group-item"><strong>Cobro por noche:</strong> Q<?= number_format($ingreso['cobro_por_noche'], 2) ?></li>
                <li class="list-group-item"><strong>Total Noches:</strong> Q<?= number_format($totalNoches, 2) ?></li>
                <li class="list-group-item"><strong>Total Medicamentos:</strong> Q<?= number_format($totalMeds, 2) ?></li>
                <li class="list-group-item"><strong class="text-primary">TOTAL GENERAL:</strong> Q<?= number_format($totalGeneral, 2) ?></li>
            </ul>

            <form action="save_alta.php" method="POST" onsubmit="return confirm('¿Confirmar alta?');">
                <input type="hidden" name="id_ingreso" value="<?= $id_ingreso ?>">
                <button type="submit" class="btn btn-success">Confirmar Alta e Imprimir</button>
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>

        <div class="col-md-6">
            <h5>Evolución - Signos Vitales</h5>
            <div class="table-responsive">
                <table class="table table-sm table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Fecha/Hora</th>
                            <th>PA</th>
                            <th>FC</th>
                            <th>FR</th>
                            <th>Glucosa</th>
                            <th>Temp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($signos as $s): ?>
                            <tr>
                                <td><?= date('d/m/Y H:i', strtotime($s['fecha_hora'])) ?></td>
                                <td><?= htmlspecialchars($s['pa']) ?></td>
                                <td><?= htmlspecialchars($s['fc']) ?></td>
                                <td><?= htmlspecialchars($s['fr']) ?></td>
                                <td><?= htmlspecialchars($s['glucosa']) ?></td>
                                <td><?= htmlspecialchars($s['temperatura']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <h5 class="mt-4">Medicamentos Administrados</h5>
            <ul class="list-group">
                <?php foreach ($medicamentos as $m): ?>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><?= htmlspecialchars($m['nom_medicamento']) ?> × <?= $m['cantidad'] ?> (<?= htmlspecialchars($m['dosis']) ?>)</span>
                        <strong>Q<?= number_format($m['cantidad'] * $m['precio_venta'], 2) ?></strong>
                    </li>
                <?php endforeach; ?>
            </ul>

            <h5 class="mt-4">Laboratorios Completados</h5>
            <ul class="list-group">
                <?php foreach ($labs as $l): ?>
                    <li class="list-group-item"><?= htmlspecialchars($l['laboratorio']) ?> - <?= date('d/m/Y', strtotime($l['fecha_resultado'])) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<?php include_once '../../includes/footer.php'; ?>