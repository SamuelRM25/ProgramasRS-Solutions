<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

// Establecer la zona horaria correcta
date_default_timezone_set('America/Guatemala');

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
        SELECT i.*, CONCAT(p.nombre,' ',p.apellido) AS paciente
        FROM encamamiento_ingresos i
        JOIN pacientes p ON p.id_paciente = i.id_paciente
        WHERE i.id_ingreso = ?
    ");
    $stmt->execute([$id_ingreso]);
    $ingreso = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ingreso || $ingreso['estado'] !== 'Activo') {
        $_SESSION['message'] = "Ingreso no válido.";
        $_SESSION['message_type'] = "danger";
        header("Location: index.php");
        exit;
    }

    // Historial de signos
    $stmt = $conn->prepare("SELECT * FROM encamamiento_signos WHERE id_ingreso = ? ORDER BY fecha_hora DESC");
    $stmt->execute([$id_ingreso]);
    $signos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $page_title = "Signos Vitales - Ingreso #$id_ingreso";
    include_once '../../includes/header.php';
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<div class="container-fluid py-4">
    <h2><i class="bi bi-heart-pulse"></i> Signos Vitales - <?= htmlspecialchars($ingreso['paciente']) ?></h2>

    <form action="save_signos.php" method="POST" class="row g-3 mb-4">
        <input type="hidden" name="id_ingreso" value="<?= $id_ingreso ?>">
        <div class="col-md-2">
            <label class="form-label">PA (mmHg)</label>
            <input type="text" name="pa" class="form-control" placeholder="120/80">
        </div>
        <div class="col-md-2">
            <label class="form-label">FC (lpm)</label>
            <input type="text" name="fc" class="form-control" placeholder="80">
        </div>
        <div class="col-md-2">
            <label class="form-label">FR (rpm)</label>
            <input type="text" name="fr" class="form-control" placeholder="16">
        </div>
        <div class="col-md-2">
            <label class="form-label">Glucosa (mg/dL)</label>
            <input type="text" name="glucosa" class="form-control" placeholder="90">
        </div>
        <div class="col-md-2">
            <label class="form-label">Temp (°C)</label>
            <input type="text" name="temperatura" class="form-control" placeholder="36.5">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
    </form>

    <h5>Historial de Signos Vitales</h5>
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

    <a href="index.php" class="btn btn-secondary">Volver</a>
</div>

<?php include_once '../../includes/footer.php'; ?>