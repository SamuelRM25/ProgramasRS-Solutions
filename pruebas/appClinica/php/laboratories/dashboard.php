<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';
verify_session();

try {
    $database = new Database();
    $conn = $database->getConnection();

    // Métricas generales
    $stmt = $conn->query("SELECT COUNT(*) FROM solicitudes_laboratorio WHERE estado = 'Pendiente'");
    $pendientes = $stmt->fetchColumn();

    $stmt = $conn->query("SELECT COUNT(*) FROM solicitudes_laboratorio WHERE estado = 'Completado' AND DATE(fecha_resultado) = CURDATE()");
    $completadosHoy = $stmt->fetchColumn();

    $stmt = $conn->query("SELECT COUNT(*) FROM solicitudes_laboratorio WHERE estado = 'Completado' AND MONTH(fecha_resultado) = MONTH(CURDATE())");
    $completadosMes = $stmt->fetchColumn();

    $stmt = $conn->query("SELECT COUNT(*) FROM insumos_laboratorio WHERE stock <= stock_minimo");
    $stockBajo = $stmt->fetchColumn();

    $stmt = $conn->query("SELECT COUNT(*) FROM insumos_laboratorio WHERE fecha_vencimiento <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)");
    $proximosVencer = $stmt->fetchColumn();

    $page_title = "Dashboard - Laboratorios";
    include_once '../../includes/header.php';
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-graph-up"></i> Dashboard - Laboratorios</h2>
        <div>
            <a href="index.php" class="btn btn-outline-primary me-2">Ver Lista</a>
            <a href="inventario.php" class="btn btn-outline-success">
                <i class="bi bi-box-seam"></i> Inventario
            </a>
        </div>
    </div>

    <!-- Tarjetas de métricas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-flask"></i> Pendientes</h5>
                    <h2><?= $pendientes ?></h2>
                    <p>Pacientes sin resultados</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-check-circle"></i> Completados Hoy</h5>
                    <h2><?= $completadosHoy ?></h2>
                    <p>Resultados entregados</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-calendar-month"></i> Completados Mes</h5>
                    <h2><?= $completadosMes ?></h2>
                    <p>Este mes</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-exclamation-triangle"></i> Stock Bajo</h5>
                    <h2><?= $stockBajo ?></h2>
                    <p>Insumos críticos</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfica de tendencia -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Completados por Día (Últimos 15 días)</h5>
                </div>
                <div class="card-body">
                    <canvas id="dailyChart" width="400" height="150"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-pie-chart"></i> Proporción por Estado</h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Gráfica de completados por día
fetch('get_daily_stats.php')
    .then(res => res.json())
    .then(data => {
        const ctx = document.getElementById('dailyChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Completados',
                    data: data.values,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0,123,255,0.1)',
                    tension: 0.3
                }]
            },
            options: { responsive: true }
        });
    });

// Gráfica de proporción por estado
fetch('get_status_stats.php')
    .then(res => res.json())
    .then(data => {
        const ctx = document.getElementById('statusChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Completado', 'Pendiente'],
                datasets: [{
                    data: [data.completado, data.pendiente],
                    backgroundColor: ['#28a745', '#dc3545']
                }]
            },
            options: { responsive: true }
        });
    });
</script>

<?php include_once '../../includes/footer.php'; ?>