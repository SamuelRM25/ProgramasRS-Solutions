<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';
verify_session();

// NO se guarda en BD hasta que estén completos
// Se usa solo para generar resultados temporales

try {
    $database = new Database();
    $conn = $database->getConnection();

    // Obtener pacientes
    $stmt = $conn->prepare("SELECT id_paciente, nombre, apellido FROM pacientes ORDER BY apellido, nombre");
    $stmt->execute();
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener laboratorios
    $stmt = $conn->query("SELECT id_tipo_laboratorio, nombre FROM tipos_laboratorio WHERE activo = 1 ORDER BY nombre");
    $labs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $page_title = "Laboratorio Express";
    include_once '../../includes/header.php';
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-lightning"></i> Laboratorio Express</h2>
        <a href="index.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Regresar
        </a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-person-plus"></i> Seleccionar Paciente</h5>
                </div>
                <div class="card-body">
                    <label class="form-label">Paciente</label>
                    <select class="form-select" id="patientSelect">
                        <option value="">Seleccionar...</option>
                        <?php foreach ($patients as $p): ?>
                            <option value="<?= $p['id_paciente'] ?>"><?= htmlspecialchars($p['apellido'] . ' ' . $p['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-flask"></i> Seleccionar Exámenes</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($labs as $lab): ?>
                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input lab-check" type="checkbox" value="<?= $lab['id_tipo_laboratorio'] ?>" id="lab-<?= $lab['id_tipo_laboratorio'] ?>">
                                    <label class="form-check-label" for="lab-<?= $lab['id_tipo_laboratorio'] ?>">
                                        <?= htmlspecialchars($lab['nombre']) ?>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="text-end mt-3">
                        <button class="btn btn-success" onclick="generateExpressResults()">
                            <i class="bi bi-check-circle"></i> Generar Resultados
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resultados temporales -->
    <div id="expressResults" class="mt-4 d-none">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-clipboard-check"></i> Resultados Express</h5>
            </div>
            <div class="card-body" id="resultsContent"></div>
            <div class="card-footer text-end">
                <button class="btn btn-primary" onclick="printExpressResults()">
                    <i class="bi bi-printer"></i> Imprimir
                </button>
                <button class="btn btn-danger" onclick="clearExpressResults()">
                    <i class="bi bi-trash"></i> Limpiar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let expressData = {};

function generateExpressResults() {
    const patientId = document.getElementById('patientSelect').value;
    if (!patientId) {
        alert('Selecciona un paciente');
        return;
    }

    const selected = Array.from(document.querySelectorAll('.lab-check:checked'));
    if (selected.length === 0) {
        alert('Selecciona al menos un examen');
        return;
    }

    const resultsContent = document.getElementById('resultsContent');
    resultsContent.innerHTML = '';
    expressData = { patientId, results: [] };

    selected.forEach(check => {
        const labId = check.value;
        const labName = check.nextElementSibling.textContent.trim();
        const result = generateRandomResult(labName);
        expressData.results.push({ labName, result });
        resultsContent.innerHTML += `
            <div class="mb-3 border rounded p-3 bg-light">
                <h6 class="text-primary">${labName}</h6>
                <pre>${result}</pre>
            </div>
        `;
    });

    document.getElementById('expressResults').classList.remove('d-none');
}

function generateRandomResult(labName) {
    const values = {
        'Glucosa en Ayunas': `Glucosa: ${(Math.random() * 50 + 80).toFixed(0)} mg/dL`,
        'Colesterol Total': `Colesterol: ${(Math.random() * 100 + 150).toFixed(0)} mg/dL`,
        'Creatinina': `Creatinina: ${(Math.random() * 0.5 + 0.8).toFixed(1)} mg/dL`,
        'Urea': `Urea: ${(Math.random() * 20 + 20).toFixed(0)} mg/dL`,
        'HbA1c': `HbA1c: ${(Math.random() * 2 + 4).toFixed(1)} %`,
    };
    return values[labName] || `${labName}: Valor normal`;
}

function printExpressResults() {
    const printWindow = window.open('', '', 'width=800,height=600');
    printWindow.document.write(`
        <html>
        <head><title>Resultados Express</title></head>
        <body>
            <h2>Resultados Express - Laboratorio Clínico</h2>
            <p><strong>Paciente:</strong> ${document.getElementById('patientSelect').selectedOptions[0].text}</p>
            <p><strong>Fecha:</strong> ${new Date().toLocaleDateString()}</p>
            <hr>
            ${document.getElementById('resultsContent').innerHTML}
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

function clearExpressResults() {
    document.getElementById('expressResults').classList.add('d-none');
    document.getElementById('resultsContent').innerHTML = '';
    expressData = {};
}
</script>

<?php include_once '../../includes/footer.php'; ?>