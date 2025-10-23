<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

verify_session();
$page_title = "Procedimientos Menores";
include_once '../../includes/header.php';

try {
    $database = new Database();
    $conn = $database->getConnection();

    $stmt_patients = $conn->prepare("SELECT id_paciente, CONCAT(nombre, ' ', apellido) as nombre_completo FROM pacientes ORDER BY nombre_completo ASC");
    $stmt_patients->execute();
    $patients = $stmt_patients->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $patients = [];
    $error_message = "Error de conexión: " . $e->getMessage();
}
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

<div class="d-flex">

    <div class="main-content flex-grow-1 p-4">
        <div class="container-fluid">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-bandaid-fill me-2"></i>Procedimientos Menores</h1>
                <div>
                    <a href="historial_procedimientos.php" class="btn btn-info me-2">
                        <i class="bi bi-clock-history me-1"></i> Ver Historial
                    </a>
                    <a href="../dashboard/index.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Regresar al Dashboard
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <strong>¡Éxito!</strong> <?php echo htmlspecialchars($_GET['message']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php elseif (isset($_GET['status']) && $_GET['status'] == 'error'): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>¡Error!</strong> <?php echo htmlspecialchars($_GET['message']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary"><i class="bi bi-pencil-square me-2"></i>Nuevo Registro</h6>
                        </div>
                        <div class="card-body">
                            <form action="save_procedure.php" method="POST" id="procedureForm">
                                <div class="mb-4">
                                    <h5 class="mb-3"><span class="badge bg-primary rounded-circle me-2">1</span>Seleccionar Paciente</h5>
                                    <select id="id_paciente" name="id_paciente" required>
                                        <option value="">Escribe o selecciona un paciente...</option>
                                        <?php foreach ($patients as $patient): ?>
                                            <option value="<?php echo $patient['id_paciente']; ?>" data-nombre="<?php echo htmlspecialchars($patient['nombre_completo']); ?>">
                                                <?php echo htmlspecialchars($patient['nombre_completo']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="hidden" name="nombre_paciente" id="nombre_paciente">
                                </div>

                                <div class="mb-4">
                                    <h5 class="mb-3"><span class="badge bg-primary rounded-circle me-2">2</span>Elegir Procedimientos</h5>
                                    <div class="p-3 bg-light rounded">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="procedimientos[]" value="Sutura de herida" id="proc1"><label class="form-check-label" for="proc1">Sutura de herida</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="procedimientos[]" value="Curación de herida" id="proc2"><label class="form-check-label" for="proc2">Curación de herida</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="procedimientos[]" value="Extracción de uña encarnada" id="proc3"><label class="form-check-label" for="proc3">Extracción de uña encarnada</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="procedimientos[]" value="Drenaje de absceso" id="proc4"><label class="form-check-label" for="proc4">Drenaje de absceso</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="procedimientos[]" value="Retiro de puntos" id="proc5"><label class="form-check-label" for="proc5">Retiro de puntos</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="procedimientos[]" value="Infiltración" id="proc6"><label class="form-check-label" for="proc6">Infiltración</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="input-group mt-3">
                                            <div class="input-group-text">
                                                <input class="form-check-input mt-0" type="checkbox" id="otro_procedimiento_check">
                                            </div>
                                            <input type="text" class="form-control" placeholder="Especificar otro procedimiento..." name="procedimientos[]" id="otro_procedimiento_text" disabled>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h5 class="mb-3"><span class="badge bg-primary rounded-circle me-2">3</span>Finalizar y Cobrar</h5>
                                    <div class="input-group" style="max-width: 200px;">
                                        <span class="input-group-text">Q</span>
                                        <input type="number" class="form-control" id="cobro" name="cobro" step="0.01" min="0" required placeholder="0.00">
                                    </div>
                                </div>

                                <hr>
                                <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-save me-2"></i>Guardar Registro</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const choices = new Choices('#id_paciente', {
        searchEnabled: true,
        itemSelectText: 'Presiona para seleccionar',
        removeItemButton: true,
    });

    const otroCheck = document.getElementById('otro_procedimiento_check');
    const otroText = document.getElementById('otro_procedimiento_text');
    otroCheck.addEventListener('change', function() {
        otroText.disabled = !this.checked;
        if (this.checked) {
            otroText.focus();
        } else {
            otroText.value = '';
        }
    });

    const pacienteSelect = document.getElementById('id_paciente');
    const nombrePacienteInput = document.getElementById('nombre_paciente');
    pacienteSelect.addEventListener('change', function() {
        if (this.value) {
            const selectedOption = this.options[this.selectedIndex];
            nombrePacienteInput.value = selectedOption.getAttribute('data-nombre');
        } else {
            nombrePacienteInput.value = '';
        }
    });
});
</script>

<?php include_once '../../includes/footer.php'; ?>