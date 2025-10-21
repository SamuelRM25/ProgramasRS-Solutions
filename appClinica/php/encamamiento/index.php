<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';
verify_session();

try {
    $database = new Database();
    $conn = $database->getConnection();

    // Obtener listado de camas con paciente ocupando (si aplica)
    $stmt = $conn->query("
        SELECT c.*, 
               IFNULL(i.id_ingreso,0) AS id_ingreso,
               IFNULL(i.id_paciente,0) AS id_paciente,
               CONCAT(p.nombre,' ',p.apellido) AS paciente,
               i.estado AS ingreso_estado
        FROM camas c
        LEFT JOIN encamamiento_ingresos i ON i.id_cama = c.id_cama AND i.estado = 'Activo'
        LEFT JOIN pacientes p ON p.id_paciente = i.id_paciente
        ORDER BY c.numero
    ");
    $camas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $page_title = "Encamamiento - Gestión de Camas";
    include_once '../../includes/header.php';
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-bed"></i> Encamamiento - Camas</h2>
        <a href="../dashboard/index.php" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Regresar
        </a>
    </div>

    <div class="row g-3">
        <?php foreach ($camas as $c): ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card <?= $c['id_paciente'] ? 'bg-danger text-white' : 'bg-success text-white' ?> h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Cama <?= htmlspecialchars($c['numero']) ?></h5>
                        <p class="card-text">
                            <?= $c['id_paciente'] ? htmlspecialchars($c['paciente']) : 'Disponible' ?>
                        </p>
                        <?php if ($c['id_paciente']): ?>
                            <a href="alta.php?id=<?= $c['id_ingreso'] ?>" class="btn btn-sm btn-light">Dar Alta</a>
                            <a href="medicamentos.php?id=<?= $c['id_ingreso'] ?>" class="btn btn-sm btn-light">Medicamentos</a>
                            <a href="signos.php?id=<?= $c['id_ingreso'] ?>" class="btn btn-sm btn-light">Signos</a>
                            <a href="laboratorios.php?id=<?= $c['id_ingreso'] ?>" class="btn btn-sm btn-light">Laboratorios</a>
                        <?php else: ?>
                            <a href="ingresar.php?id_cama=<?= $c['id_cama'] ?>" class="btn btn-sm btn-light">Internar</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include_once '../../includes/footer.php'; ?>