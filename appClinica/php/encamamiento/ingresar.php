<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';
verify_session();

$id_cama = $_GET['id_cama'] ?? null;
if (!$id_cama) {
    $_SESSION['message'] = "No se seleccionó cama.";
    $_SESSION['message_type'] = "danger";
    header("Location: index.php");
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    // Verificar que la cama esté disponible
    $stmt = $conn->prepare("SELECT * FROM camas WHERE id_cama = ? AND estado = 'Disponible'");
    $stmt->execute([$id_cama]);
    $cama = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cama) {
        $_SESSION['message'] = "La cama no está disponible.";
        $_SESSION['message_type'] = "danger";
        header("Location: index.php");
        exit;
    }

    // Obtener pacientes
    $stmt = $conn->query("SELECT id_paciente, nombre, apellido FROM pacientes ORDER BY apellido, nombre");
    $pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $page_title = "Internar Paciente";
    include_once '../../includes/header.php';
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<div class="container-fluid py-4">
    <h2><i class="bi bi-person-plus"></i> Internar Paciente - Cama <?= htmlspecialchars($cama['numero']) ?></h2>
    <form action="save_ingreso.php" method="POST">
        <input type="hidden" name="id_cama" value="<?= $cama['id_cama'] ?>">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Paciente</label>
                <select name="id_paciente" class="form-select" required>
                    <option value="">Seleccionar...</option>
                    <?php foreach ($pacientes as $p): ?>
                        <option value="<?= $p['id_paciente'] ?>"><?= htmlspecialchars($p['apellido'] . ' ' . $p['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Médico Encargado</label>
                <input type="text" name="medico_encargado" class="form-control" value="<?= htmlspecialchars($_SESSION['nombre'] . ' ' . $_SESSION['apellido']) ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Cobro por Noche (Q)</label>
                <input type="number" step="0.01" name="cobro_por_noche" class="form-control" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Guardar Ingreso</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php include_once '../../includes/footer.php'; ?>