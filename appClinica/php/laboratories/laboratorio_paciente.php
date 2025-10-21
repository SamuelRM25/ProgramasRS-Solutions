<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';
verify_session();

$id_paciente = $_GET['id'] ?? null;
if (!$id_paciente) {
    die("ID de paciente no especificado.");
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    // Datos del paciente
    $stmt = $conn->prepare("SELECT * FROM pacientes WHERE id_paciente = ?");
    $stmt->execute([$id_paciente]);
    $paciente = $stmt->fetch(PDO::FETCH_ASSOC);

    // Laboratorios pendientes
    $stmt = $conn->prepare("
        SELECT sl.*, tl.nombre as laboratorio_nombre
        FROM solicitudes_laboratorio sl
        JOIN tipos_laboratorio tl ON sl.id_tipo_laboratorio = tl.id_tipo_laboratorio
        WHERE sl.id_paciente = ? AND sl.estado = 'Pendiente'
        ORDER BY sl.fecha_solicitud DESC
    ");
    $stmt->execute([$id_paciente]);
    $pendientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Laboratorios completos
    $stmt = $conn->prepare("
        SELECT sl.*, tl.nombre as laboratorio_nombre
        FROM solicitudes_laboratorio sl
        JOIN tipos_laboratorio tl ON sl.id_tipo_laboratorio = tl.id_tipo_laboratorio
        WHERE sl.id_paciente = ? AND sl.estado = 'Completado'
        ORDER BY sl.fecha_resultado DESC
    ");
    $stmt->execute([$id_paciente]);
    $completados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Datos para gráfica de evolución
    $stmt = $conn->prepare("
        SELECT tl.nombre as laboratorio, sl.fecha_resultado, sl.resultados
        FROM solicitudes_laboratorio sl
        JOIN tipos_laboratorio tl ON sl.id_tipo_laboratorio = tl.id_tipo_laboratorio
        WHERE sl.id_paciente = ? AND sl.estado = 'Completado'
        ORDER BY sl.fecha_resultado ASC
    ");
    $stmt->execute([$id_paciente]);
    $historial = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $page_title = "Laboratorio - " . htmlspecialchars($paciente['nombre'] . ' ' . $paciente['apellido']);
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
    --border-radius: 1rem;
}

body {
    background-color: #f5f7fb;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.card-lab {
    border: none;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    transition: transform .2s ease, box-shadow .2s ease;
}

.card-lab:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.nav-tabs .nav-link {
    border: none;
    font-weight: 600;
    color: var(--dark);
    border-radius: .5rem .5rem 0 0;
}

.nav-tabs .nav-link.active {
    background-color: var(--primary);
    color: #fff;
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
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show">
            <?= $_SESSION['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php 
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        ?>
    <?php endif; ?>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="../laboratories/index.php" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Regresar
            </a>
            <h2 class="mt-2 section-title"><i class="bi bi-person-lines-fill"></i> Historial de Laboratorio</h2>
            <p class="mb-0 text-muted"><?= htmlspecialchars($paciente['nombre'] . ' ' . $paciente['apellido']) ?> · <?= htmlspecialchars($paciente['genero']) ?> · <?= (new DateTime($paciente['fecha_nacimiento']))->diff(new DateTime())->y ?> años</p>
        </div>
        <div>
            <a href="express.php" class="btn btn-success btn-action me-2">
                <i class="bi bi-lightning"></i> Express
            </a>
            <button class="btn btn-primary btn-action" onclick="window.print()">
                <i class="bi bi-printer"></i> Imprimir
            </button>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="labTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pendientes-tab" data-bs-toggle="tab" data-bs-target="#pendientes" type="button">
                <i class="bi bi-clock"></i> Pendientes
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="completados-tab" data-bs-toggle="tab" data-bs-target="#completados" type="button">
                <i class="bi bi-check-circle"></i> Completados
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="evolucion-tab" data-bs-toggle="tab" data-bs-target="#evolucion" type="button">
                <i class="bi bi-graph-up"></i> Evolución
            </button>
        </li>
    </ul>

    <div class="tab-content" id="labTabContent">
        <!-- Pendientes -->
        <div class="tab-pane fade show active" id="pendientes" role="tabpanel">
            <div class="card card-lab">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-clock"></i> Laboratorios Pendientes</h5>
                </div>
                <div class="card-body">
                    <?php if (count($pendientes) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Examen</th>
                                        <th>Fecha Solicitud</th>
                                        <th class="text-end">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendientes as $p): ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($p['laboratorio_nombre']) ?></strong></td>
                                            <td><?= date('d/m/Y', strtotime($p['fecha_solicitud'])) ?></td>
                                            <td class="text-end">
                                                <button class="btn btn-sm btn-primary" onclick="openManualForm(<?= $p['id_solicitud'] ?>)">
                                                    <i class="bi bi-pencil-square"></i> Registrar
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info mb-0"><i class="bi bi-info-circle"></i> No hay laboratorios pendientes.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Completados -->
        <div class="tab-pane fade" id="completados" role="tabpanel">
            <div class="card card-lab">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-check-circle"></i> Laboratorios Completados</h5>
                </div>
                <div class="card-body">
                    <form id="printForm" action="print_selected_results.php" method="POST" target="_blank">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="selectAll"></th>
                                        <th>Examen</th>
                                        <th>Fecha Resultado</th>
                                        <th>Resultado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($completados as $c): ?>
                                        <tr>
                                            <td><input type="checkbox" name="ids[]" value="<?= $c['id_solicitud'] ?>"></td>
                                            <td><strong><?= htmlspecialchars($c['laboratorio_nombre']) ?></strong></td>
                                            <td><?= date('d/m/Y', strtotime($c['fecha_resultado'])) ?></td>
                                            <td><pre class="mb-0"><?= htmlspecialchars($c['resultados']) ?></pre></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-printer"></i> Imprimir Seleccionados
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Evolución -->
        <div class="tab-pane fade" id="evolucion" role="tabpanel">
            <div class="card card-lab">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-graph-up"></i> Evolución por Examen</h5>
                </div>
                <div class="card-body">
                    <canvas id="evolucionChart" width="400" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Formulario Manual -->
<div class="modal fade" id="manualFormModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Resultado Manual</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="manualFormContent">
                <!-- Se llenará dinámicamente -->
            </div>
        </div>
    </div>
</div>

<script>
function openManualForm(id) {
    fetch('get_manual_form.php?id_solicitud=' + id)
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                document.getElementById('manualFormContent').innerHTML = data.html;
                new bootstrap.Modal(document.getElementById('manualFormModal')).show();
            } else {
                alert('Error: ' + data.message);
            }
        });
}

document.getElementById('selectAll').addEventListener('change', function () {
    const checkboxes = document.querySelectorAll('input[name="ids[]"]');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
<script>
const historial = <?= json_encode($historial) ?>;

const labs = [...new Set(historial.map(h => h.laboratorio))];
const datasets = labs.map((lab, i) => {
    const data = historial.filter(h => h.laboratorio === lab).map(h => ({
        x: h.fecha_resultado,
        y: parseFloat(h.resultados.match(/[\d.]+/)) || 0
    }));
    return {
        label: lab,
        data: data,
        borderColor: `hsl(${i * 60}, 70%, 50%)`,
        fill: false,
        tension: 0.3
    };
});

const ctx = document.getElementById('evolucionChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: { datasets },
    options: {
        responsive: true,
        scales: {
            x: {
                type: 'time',
                time: { unit: 'day' }
            },
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

<?php include_once '../../includes/footer.php'; ?>