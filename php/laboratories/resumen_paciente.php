<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';
verify_session();

$patient_id = $_GET['paciente'] ?? 0;

try {
    $database = new Database();
    $conn = $database->getConnection();

    // Datos del paciente
    $stmt = $conn->prepare("SELECT * FROM pacientes WHERE id_paciente = ?");
    $stmt->execute([$patient_id]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    // Últimos resultados por tipo de laboratorio
    $stmt = $conn->prepare("
        SELECT 
            tl.nombre as laboratorio,
            sl.resultados,
            sl.fecha_resultado,
            sl.estado,
            vr.parametro,
            vr.valor_min,
            vr.valor_max,
            vr.unidad
        FROM solicitudes_laboratorio sl
        JOIN tipos_laboratorio tl ON sl.id_tipo_laboratorio = tl.id_tipo_laboratorio
        LEFT JOIN valores_referencia vr ON tl.id_tipo_laboratorio = vr.id_tipo_laboratorio
        WHERE sl.id_paciente = ? AND sl.estado = 'Completado'
        ORDER BY sl.fecha_resultado DESC
    ");
    $stmt->execute([$patient_id]);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Historial por examen (para gráficas)
    $stmt = $conn->prepare("
        SELECT 
            tl.nombre as laboratorio,
            sl.resultados,
            sl.fecha_resultado
        FROM solicitudes_laboratorio sl
        JOIN tipos_laboratorio tl ON sl.id_tipo_laboratorio = tl.id_tipo_laboratorio
        WHERE sl.id_paciente = ? AND sl.estado = 'Completado'
        ORDER BY tl.nombre, sl.fecha_resultado
    ");
    $stmt->execute([$patient_id]);
    $historial = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $page_title = "Resumen de Laboratorios - " . htmlspecialchars($patient['nombre'] . ' ' . $patient['apellido']);
    include_once '../../includes/header.php';
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-graph-up"></i> Resumen de Laboratorios</h2>
        <a href="index.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Regresar
        </a>
    </div>

    <!-- Tarjeta de paciente -->
    <div class="card mb-4">
        <div class="card-body">
            <h5><?= htmlspecialchars($patient['nombre'] . ' ' . $patient['apellido']) ?></h5>
            <p><strong>Edad:</strong> <?= (new DateTime($patient['fecha_nacimiento']))->diff(new DateTime())->y ?> años</p>
            <p><strong>Género:</strong> <?= htmlspecialchars($patient['genero']) ?></p>
        </div>
    </div>

    <!-- Resumen de resultados -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-clipboard-data"></i> Resultados Recientes</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <?php
                $grupos = [];
                foreach ($resultados as $r) {
                    $grupos[$r['laboratorio']][] = $r;
                }
                foreach ($grupos as $lab => $params): ?>
                    <div class="col-md-6 mb-3">
                        <div class="border rounded p-3 bg-light">
                            <h6 class="text-primary"><?= htmlspecialchars($lab) ?></h6>
                            <p class="mb-1"><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($params[0]['fecha_resultado'])) ?></p>
                            <pre class="mb-0"><?= htmlspecialchars($params[0]['resultados']) ?></pre>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Gráficas de evolución -->
    <div class="card">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-graph-up-arrow"></i> Evolución por Examen</h5>
        </div>
        <div class="card-body">
            <canvas id="evolutionChart" width="400" height="200"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const historial = <?= json_encode($historial) ?>;
const ctx = document.getElementById('evolutionChart').getContext('2d');

// Agrupar por examen
const grouped = {};
historial.forEach(h => {
    if (!grouped[h.laboratorio]) grouped[h.laboratorio] = [];
    const valor = parseFloat(h.resultados.match(/[\d.]+/)) || 0;
    grouped[h.laboratorio].push({
        x: h.fecha_resultado,
        y: valor
    });
});

const datasets = Object.keys(grouped).map((lab, i) => ({
    label: lab,
    data: grouped[lab],
    borderColor: `hsl(${i * 60}, 70%, 50%)`,
    fill: false,
    tension: 0.3
}));

new Chart(ctx, {
    type: 'line',
    data: { datasets },
    options: {
        responsive: true,
        scales: {
            x: { type: 'time', time: { unit: 'month' } },
            y: { beginAtZero: true }
        }
    }
});
</script>

<?php include_once '../../includes/footer.php'; ?>