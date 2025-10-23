<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';
verify_session();

try {
    $database = new Database();
    $conn = $database->getConnection();

    // Métricas generales
    $pendientes = $conn->query("SELECT COUNT(*) FROM solicitudes_laboratorio WHERE estado = 'Pendiente'")->fetchColumn();
    $completadosHoy = $conn->query("SELECT COUNT(*) FROM solicitudes_laboratorio WHERE estado = 'Completado' AND DATE(fecha_resultado) = CURDATE()")->fetchColumn();
    $completadosMes = $conn->query("SELECT COUNT(*) FROM solicitudes_laboratorio WHERE estado = 'Completado' AND MONTH(fecha_resultado) = MONTH(CURDATE())")->fetchColumn();
    $stockBajo = $conn->query("SELECT COUNT(*) FROM insumos_laboratorio WHERE stock <= stock_minimo")->fetchColumn();
    $proximosVencer = $conn->query("SELECT COUNT(*) FROM insumos_laboratorio WHERE fecha_vencimiento <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)")->fetchColumn();

    // Pacientes con laboratorios pendientes
    $sqlPendientes = "
        SELECT p.id_paciente, p.nombre, p.apellido, COUNT(sl.id_solicitud) as total_pendientes
        FROM solicitudes_laboratorio sl
        JOIN pacientes p ON sl.id_paciente = p.id_paciente
        WHERE sl.estado = 'Pendiente'
        GROUP BY p.id_paciente
        ORDER BY total_pendientes DESC
    ";
    $pendientesPacientes = $conn->query($sqlPendientes)->fetchAll(PDO::FETCH_ASSOC);

    // Pacientes con laboratorios completos hoy
    $sqlCompletadosHoy = "
        SELECT p.id_paciente, p.nombre, p.apellido, COUNT(sl.id_solicitud) as total_completados
        FROM solicitudes_laboratorio sl
        JOIN pacientes p ON sl.id_paciente = p.id_paciente
        WHERE sl.estado = 'Completado' AND DATE(sl.fecha_resultado) = CURDATE()
        GROUP BY p.id_paciente
        ORDER BY total_completados DESC
    ";
    $completadosHoyPacientes = $conn->query($sqlCompletadosHoy)->fetchAll(PDO::FETCH_ASSOC);

    // Insumos críticos
    $insumosCriticos = $conn->query("SELECT nombre, stock, stock_minimo FROM insumos_laboratorio WHERE stock <= stock_minimo ORDER BY stock ASC")->fetchAll(PDO::FETCH_ASSOC);

    $page_title = "Dashboard - Laboratorios";
    include_once '../../includes/header.php';
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!-- ===== ESTILOS PROFESIONALES ===== -->
<style>
:root {
    --primary: #0d6efd;
    --success: #198754;
    --warning: #ffc107;
    --danger: #dc3545;
    --info: #0dcaf0;
    --light: #f8f9fa;
    --dark: #212529;
    --shadow: 0 .125rem .25rem rgba(0,0,0,.075);
    --shadow-lg: 0 1rem 3rem rgba(0,0,0,.175);
}

body {
    background-color: #f5f7fb;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.card-dashboard {
    border: none;
    border-radius: 1rem;
    box-shadow: var(--shadow);
    transition: transform .2s ease, box-shadow .2s ease;
}

.card-dashboard:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.metric-value {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
}

.metric-label {
    font-size: .9rem;
    letter-spacing: .5px;
    text-transform: uppercase;
}

.btn-action {
    border-radius: .5rem;
    font-weight: 600;
    padding: .5rem 1rem;
}

.table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, .05);
}

.badge-custom {
    font-size: .85rem;
    padding: .35em .65em;
    border-radius: .5rem;
}

.section-title {
    font-weight: 600;
    color: var(--dark);
    letter-spacing: .5px;
}
</style>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="../dashboard/index.php" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Regresar
            </a>
            <h2 class="mt-2 section-title"><i class="bi bi-graph-up"></i> Dashboard - Laboratorios</h2>
        </div>
        <div>
            <a href="index.php" class="btn btn-primary btn-action me-2">
                <i class="bi bi-list-ul"></i> Ver Lista
            </a>
            <a href="inventario.php" class="btn btn-outline-success btn-action">
                <i class="bi bi-box-seam"></i> Inventario
            </a>
        </div>
    </div>

    <!-- Métricas -->
    <div class="row g-4 mb-4">
        <div class="col-md-2">
            <div class="card card-dashboard text-white bg-danger">
                <div class="card-body text-center">
                    <h6 class="metric-label">Pendientes</h6>
                    <p class="metric-value"><?= $pendientes ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card card-dashboard text-white bg-success">
                <div class="card-body text-center">
                    <h6 class="metric-label">Completados Hoy</h6>
                    <p class="metric-value"><?= $completadosHoy ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card card-dashboard text-white bg-primary">
                <div class="card-body text-center">
                    <h6 class="metric-label">Completados Mes</h6>
                    <p class="metric-value"><?= $completadosMes ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card card-dashboard text-white bg-warning">
                <div class="card-body text-center">
                    <h6 class="metric-label">Stock Bajo</h6>
                    <p class="metric-value"><?= $stockBajo ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card card-dashboard text-white bg-info">
                <div class="card-body text-center">
                    <h6 class="metric-label">Próx. a Vencer</h6>
                    <p class="metric-value"><?= $proximosVencer ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Pacientes -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card card-dashboard">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-exclamation-circle"></i> Pacientes con Laboratorios Pendientes</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Paciente</th>
                                    <th class="text-end">Pendientes</th>
                                    <th class="text-end">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendientesPacientes as $p): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($p['nombre'] . ' ' . $p['apellido']) ?></td>
                                        <td class="text-end"><span class="badge badge-custom bg-danger"><?= $p['total_pendientes'] ?></span></td>
                                        <td class="text-end">
                                            <a href="laboratorio_paciente.php?id=<?= $p['id_paciente'] ?>" class="btn btn-sm btn-outline-primary">Ver</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-dashboard">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-check-circle"></i> Pacientes con Laboratorios Completados Hoy</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Paciente</th>
                                    <th class="text-end">Completados</th>
                                    <th class="text-end">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($completadosHoyPacientes as $p): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($p['nombre'] . ' ' . $p['apellido']) ?></td>
                                        <td class="text-end"><span class="badge badge-custom bg-success"><?= $p['total_completados'] ?></span></td>
                                        <td class="text-end">
                                            <a href="laboratorio_paciente.php?id=<?= $p['id_paciente'] ?>" class="btn btn-sm btn-outline-primary">Ver</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Insumos críticos -->
    <div class="row">
        <div class="col-md-8">
            <div class="card card-dashboard">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="bi bi-box-seam"></i> Insumos con Stock Crítico</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Insumo</th>
                                    <th class="text-end">Stock</th>
                                    <th class="text-end">Mínimo</th>
                                    <th class="text-end">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($insumosCriticos as $item): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['nombre']) ?></td>
                                        <td class="text-end"><?= $item['stock'] ?></td>
                                        <td class="text-end"><?= $item['stock_minimo'] ?></td>
                                        <td class="text-end">
                                            <a href="inventario.php" class="btn btn-sm btn-outline-danger">Gestionar</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-dashboard">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-gear"></i> Acciones Rápidas</h5>
                </div>
                <div class="card-body d-grid gap-2">
                    <a href="express.php" class="btn btn-success btn-action">
                        <i class="bi bi-lightning"></i> Laboratorio Express
                    </a>
                    <a href="inventario.php" class="btn btn-outline-success btn-action">
                        <i class="bi bi-box-seam"></i> Gestionar Inventario
                    </a>
                    <a href="../laboratories/index.php" class="btn btn-outline-primary btn-action">
                        <i class="bi bi-list-ul"></i> Ver Todos los Laboratorios
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once '../../includes/footer.php'; ?>