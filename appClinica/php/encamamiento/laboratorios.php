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

    // Laboratorios asignados a este ingreso (vía historial_clinico)
    $stmt = $conn->prepare("
        SELECT sl.id_solicitud, tl.nombre AS laboratorio, sl.estado, sl.fecha_solicitud, sl.resultados
        FROM solicitudes_laboratorio sl
        JOIN tipos_laboratorio tl ON tl.id_tipo_laboratorio = sl.id_tipo_laboratorio
        JOIN historial_clinico h ON h.id_historial = sl.id_historial
        WHERE h.id_paciente = ? AND sl.estado IN ('Pendiente','Completado')
        ORDER BY sl.fecha_solicitud DESC
    ");
    $stmt->execute([$ingreso['id_paciente']]);
    $labs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Tipos de laboratorio disponibles
    $stmt = $conn->query("SELECT * FROM tipos_laboratorio WHERE activo = 1 ORDER BY nombre");
    $tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $page_title = "Laboratorios - Ingreso #$id_ingreso";
    include_once '../../includes/header.php';
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<div class="container-fluid py-4">
    <h2><i class="bi bi-flask"></i> Laboratorios - <?= htmlspecialchars($ingreso['paciente']) ?></h2>

    <h5 class="mt-4">Asignar Nuevo Laboratorio</h5>
    <form action="save_laboratorios  .php" method="POST" class="row g-3 mb-4">
        <input type="hidden" name="id_paciente" value="<?= $ingreso['id_paciente'] ?>">
        <input type="hidden" name="id_ingreso" value="<?= $id_ingreso ?>">
        <div class="col-md-8">
            <label class="form-label">Examen</label>
            <select name="id_tipo_laboratorio" class="form-select" required>
                <option value="">Seleccionar...</option>
                <?php foreach ($tipos as $t): ?>
                    <option value="<?= $t['id_tipo_laboratorio'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">Solicitar</button>
        </div>
    </form>

    <h5>Laboratorios Solicitados</h5>
    <div class="table-responsive">
        <table class="table table-sm table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Examen</th>
                    <th>Fecha Solicitud</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($labs as $l): ?>
                    <tr>
                        <td><?= htmlspecialchars($l['laboratorio']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($l['fecha_solicitud'])) ?></td>
                        <td>
                            <span class="badge bg-<?= $l['estado'] === 'Completado' ? 'success' : 'warning' ?>"><?= $l['estado'] ?></span>
                        </td>
                        <td>
                            <?php if ($l['estado'] === 'Completado'): ?>
                                <button class="btn btn-sm btn-info" onclick="verResultado(<?= $l['id_solicitud'] ?>)">Ver Resultado</button>
                            <?php else: ?>
                                <a href="../laboratories/save_manual_result.php?id_solicitud=<?= $l['id_solicitud'] ?>" class="btn btn-sm btn-primary">Registrar Resultado</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <a href="index.php" class="btn btn-secondary">Volver</a>
</div>

<script>
function verResultado(id) {
    fetch('../laboratories/get_result_with_reference.php?id=' + id)
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                const modal = document.createElement('div');
                modal.className = 'modal fade';
                modal.tabIndex = -1;
                modal.innerHTML = `
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Resultado de Laboratorio</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">${data.html}</div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                `;
                document.body.appendChild(modal);
                const bsModal = new bootstrap.Modal(modal);
                bsModal.show();
                modal.addEventListener('hidden.bs.modal', () => modal.remove());
            } else {
                alert('Error: ' + data.message);
            }
        });
}
</script>

<?php include_once '../../includes/footer.php'; ?>