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

    // Datos del ingreso y paciente
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

    // Medicamentos ya administrados
    $stmt = $conn->prepare("
        SELECT m.*, i.nom_medicamento, i.presentacion_med, i.precio_venta
        FROM encamamiento_medicamentos m
        JOIN inventario i ON i.id_inventario = m.id_inventario
        WHERE m.id_ingreso = ?
        ORDER BY m.fecha_hora DESC
    ");
    $stmt->execute([$id_ingreso]);
    $medicamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Inventario disponible
    $stmt = $conn->query("SELECT * FROM inventario WHERE cantidad_med > 0 ORDER BY nom_medicamento");
    $inventario = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $page_title = "Medicamentos - Ingreso #$id_ingreso";
    include_once '../../includes/header.php';
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<div class="container-fluid py-4">
    <h2><i class="bi bi-capsule"></i> Medicamentos - <?= htmlspecialchars($ingreso['paciente']) ?></h2>

    <form id="formMedicamento" class="row g-3 mb-4">
        <input type="hidden" name="id_ingreso" value="<?= $id_ingreso ?>">
        <div class="col-md-4">
            <label class="form-label">Medicamento</label>
            <select name="id_inventario" class="form-select" required>
                <option value="">Seleccionar...</option>
                <?php foreach ($inventario as $item): ?>
                    <option value="<?= $item['id_inventario'] ?>" data-precio="<?= $item['precio_venta'] ?>">
                        <?= htmlspecialchars($item['nom_medicamento'] . ' - ' . $item['presentacion_med']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Cantidad</label>
            <input type="number" name="cantidad" class="form-control" min="1" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Dosis</label>
            <input type="text" name="dosis" class="form-control" placeholder="Ej. 1 amp c/8h">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary me-2">Agregar</button>
            <span id="totalMedicamento" class="fw-bold text-success">Total: Q0.00</span>
        </div>
    </form>

    <h5>Medicamentos Administrados</h5>
    <div class="table-responsive">
        <table class="table table-sm table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Medicamento</th>
                    <th>Cantidad</th>
                    <th>Dosis</th>
                    <th>Precio Unit.</th>
                    <th>Subtotal</th>
                    <th>Fecha/Hora</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php $total = 0; ?>
                <?php foreach ($medicamentos as $m): ?>
                    <?php $sub = $m['cantidad'] * $m['precio_venta']; $total += $sub; ?>
                    <tr>
                        <td><?= htmlspecialchars($m['nom_medicamento']) ?></td>
                        <td><?= $m['cantidad'] ?></td>
                        <td><?= htmlspecialchars($m['dosis']) ?></td>
                        <td>Q<?= number_format($m['precio_venta'], 2) ?></td>
                        <td>Q<?= number_format($sub, 2) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($m['fecha_hora'])) ?></td>
                        <td>
                            <button class="btn btn-sm btn-danger" onclick="deleteMedicamento(<?= $m['id_medicamento_admin'] ?>)">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-end">Total Medicamentos:</th>
                    <th>Q<?= number_format($total, 2) ?></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <a href="index.php" class="btn btn-secondary">Volver</a>
</div>

<script>
document.querySelector('[name="id_inventario"]').addEventListener('change', function () {
    const opt = this.selectedOptions[0];
    const precio = parseFloat(opt.dataset.precio || 0);
    const cant = parseInt(document.querySelector('[name="cantidad"]').value || 1);
    document.getElementById('totalMedicamento').textContent = `Total: Q${(precio * cant).toFixed(2)}`;
});

document.getElementById('formMedicamento').addEventListener('submit', function (e) {
    e.preventDefault();
    fetch('save_medicamento.php', {
        method: 'POST',
        body: new FormData(this)
    }).then(res => res.text()).then(() => location.reload());
});

function deleteMedicamento(id) {
    if (!confirm('¿Eliminar este medicamento?')) return;
    fetch('delete_medicamento.php?id=' + id).then(() => location.reload());
}
</script>

<?php include_once '../../includes/footer.php'; ?>