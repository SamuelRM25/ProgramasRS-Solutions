<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

date_default_timezone_set('America/Guatemala');
verify_session();

try {
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        $_SESSION['message'] = "ID de paciente inválido";
        $_SESSION['message_type'] = "danger";
        header("Location: index.php");
        exit;
    }

    $patient_id = $_GET['id'];
    $database = new Database();
    $conn = $database->getConnection();

    // Datos del paciente
    $stmt = $conn->prepare("SELECT * FROM pacientes WHERE id_paciente = ?");
    $stmt->execute([$patient_id]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$patient) {
        $_SESSION['message'] = "Paciente no encontrado";
        $_SESSION['message_type'] = "danger";
        header("Location: index.php");
        exit;
    }

    // Historial clínico
    $stmt = $conn->prepare("SELECT * FROM historial_clinico WHERE id_paciente = ? ORDER BY fecha_consulta DESC");
    $stmt->execute([$patient_id]);
    $medical_records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $page_title = "Historial Clínico - " . htmlspecialchars($patient['nombre'] . ' ' . $patient['apellido']);
    include_once '../../includes/header.php';
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<div class="d-flex">
    <div class="main-content flex-grow-1">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <a href="index.php" class="btn btn-outline-secondary me-3">
                        <i class="bi bi-arrow-left"></i> Regresar
                    </a>
                    <h2>Historial Clínico: <?= htmlspecialchars($patient['nombre'] . ' ' . $patient['apellido']) ?></h2>
                </div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newMedicalRecordModal">
                    <i class="bi bi-plus-circle me-2"></i>Nueva Consulta
                </button>
            </div>

            <!-- Tarjeta de información del paciente -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Fecha de Nacimiento:</strong> <?= htmlspecialchars($patient['fecha_nacimiento']) ?></p>
                            <p><strong>Género:</strong> <?= htmlspecialchars($patient['genero']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Teléfono:</strong> <?= htmlspecialchars($patient['telefono']) ?></p>
                            <p><strong>Correo:</strong> <?= htmlspecialchars($patient['correo']) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historial clínico -->
            <?php if (count($medical_records) > 0): ?>
                <div class="accordion" id="medicalHistoryAccordion">
                    <?php foreach ($medical_records as $index => $record): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading<?= $index ?>">
                                <button class="accordion-button <?= $index > 0 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $index ?>" aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>">
                                    <div class="d-flex justify-content-between w-100 me-3">
                                        <span><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($record['fecha_consulta'])) ?></span>
                                        <span><strong>Médico:</strong> <?= htmlspecialchars($record['medico_responsable']) ?></span>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse<?= $index ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" data-bs-parent="#medicalHistoryAccordion">
                                <div class="accordion-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5>Motivo de Consulta</h5>
                                            <p><?= nl2br(htmlspecialchars($record['motivo_consulta'])) ?></p>

                                            <h5>Historia de la Enfermedad Actual</h5>
                                            <p><?= nl2br(htmlspecialchars($record['sintomas'])) ?></p>

                                            <?php if (!empty($record['examen_fisico'])): ?>
                                                <h5>Examen Físico</h5>
                                                <p><?= nl2br(htmlspecialchars($record['examen_fisico'])) ?></p>
                                            <?php endif; ?>

                                            <h5>Diagnóstico</h5>
                                            <p><?= nl2br(htmlspecialchars($record['diagnostico'])) ?></p>

                                            <h5>Tratamiento</h5>
                                            <p><?= nl2br(htmlspecialchars($record['tratamiento'])) ?></p>

                                            <?php if (!empty($record['receta_medica'])): ?>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h5>Receta Médica</h5>
                                                    <button class="btn btn-sm btn-outline-primary" onclick="printPrescription(<?= $record['id_historial'] ?>)">
                                                        <i class="bi bi-printer"></i> Imprimir
                                                    </button>
                                                </div>
                                                <p><?= nl2br(htmlspecialchars($record['receta_medica'])) ?></p>
                                            <?php endif; ?>
                                        </div>

                                        <div class="col-md-6">
                                            <?php if (!empty($record['antecedentes_personales'])): ?>
                                                <h5>Antecedentes Personales</h5>
                                                <p><?= nl2br(htmlspecialchars($record['antecedentes_personales'])) ?></p>
                                            <?php endif; ?>

                                            <?php if (!empty($record['antecedentes_familiares'])): ?>
                                                <h5>Antecedentes Familiares</h5>
                                                <p><?= nl2br(htmlspecialchars($record['antecedentes_familiares'])) ?></p>
                                            <?php endif; ?>

                                            <?php if (!empty($record['observaciones'])): ?>
                                                <h5>Observaciones</h5>
                                                <p><?= nl2br(htmlspecialchars($record['observaciones'])) ?></p>
                                            <?php endif; ?>

                                            <?php if (!empty($record['proxima_cita'])): ?>
                                                <h5>Próxima Cita</h5>
                                                <p><?= date('d/m/Y', strtotime($record['proxima_cita'])) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- ===== Laboratorios de esta consulta ===== -->
                                    <div class="mt-4">
                                        <h6><i class="bi bi-flask"></i> Laboratorios Solicitados:</h6>
                                        <div id="labs-history-<?= $record['id_historial'] ?>">
                                            <p class="text-muted">Cargando laboratorios...</p>
                                        </div>
                                    </div>

                                    <div class="mt-3 text-end">
                                        <button class="btn btn-sm btn-primary" onclick="editMedicalRecord(<?= $record['id_historial'] ?>)">
                                            <i class="bi bi-pencil"></i> Editar
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteMedicalRecord(<?= $record['id_historial'] ?>)">
                                            <i class="bi bi-trash"></i> Eliminar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">No hay registros de historial clínico para este paciente.</div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-hospital me-2"></i>Internar al Hospital</h5>
                        </div>
                        <div class="card-body">
                            <form id="internForm" action="intern_patient.php" method="POST">
                                <input type="hidden" name="id_paciente" value="<?= $patient_id ?>">
                                <div class="mb-3">
                                    <label class="form-label">Motivo de Internación</label>
                                    <textarea class="form-control" name="motivo_internacion" rows="3" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Diagnóstico Preliminar</label>
                                    <textarea class="form-control" name="diagnostico_preliminar" rows="3" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Medicamentos Iniciales</label>
                                    <textarea class="form-control" name="medicamentos_iniciales" rows="3"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Internar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===== Modal: Nueva Consulta Médica ===== -->
<div class="modal fade" id="newMedicalRecordModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nueva Consulta Médica</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="newMedicalRecordForm" action="save_medical_record.php" method="POST">
                <input type="hidden" name="id_paciente" value="<?= $patient_id ?>">

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Motivo de Consulta</label>
                                <textarea class="form-control" name="motivo_consulta" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Historia de la Enfermedad Actual</label>
                                <textarea class="form-control" name="sintomas" rows="3" required></textarea>
                            </div>

                            <!-- ===== EXAMEN FÍSICO COMPLETO ===== -->
                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-header bg-light" data-bs-toggle="collapse" data-bs-target="#collapseExamenFisico" aria-expanded="false" style="cursor: pointer;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label class="form-label mb-0">Examen Físico</label>
                                            <i class="bi bi-chevron-down"></i>
                                        </div>
                                    </div>
                                    <div class="collapse" id="collapseExamenFisico">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12 mb-2">
                                                    <label class="form-label small">Signos Vitales</label>
                                                    <div class="row g-2">
                                                        <div class="col-md-3">
                                                            <div class="input-group input-group-sm">
                                                                <span class="input-group-text">PA</span>
                                                                <input type="text" class="form-control form-control-sm" name="examen_fisico_pa" placeholder="120/80">
                                                                <span class="input-group-text">mmHg</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="input-group input-group-sm">
                                                                <span class="input-group-text">FC</span>
                                                                <input type="text" class="form-control form-control-sm" name="examen_fisico_fc" placeholder="80">
                                                                <span class="input-group-text">lpm</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="input-group input-group-sm">
                                                                <span class="input-group-text">FR</span>
                                                                <input type="text" class="form-control form-control-sm" name="examen_fisico_fr" placeholder="16">
                                                                <span class="input-group-text">rpm</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="input-group input-group-sm">
                                                                <span class="input-group-text">T°</span>
                                                                <input type="text" class="form-control form-control-sm" name="examen_fisico_temp" placeholder="36.5">
                                                                <span class="input-group-text">°C</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label small">Inspección General</label>
                                                    <textarea class="form-control form-control-sm" name="examen_fisico_inspeccion" rows="2" placeholder="Estado general, facies, piel..."></textarea>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label small">Cabeza y Cuello</label>
                                                    <textarea class="form-control form-control-sm" name="examen_fisico_cabeza" rows="2"></textarea>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label small">Tórax y Pulmones</label>
                                                    <textarea class="form-control form-control-sm" name="examen_fisico_torax" rows="2"></textarea>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label small">Cardiovascular</label>
                                                    <textarea class="form-control form-control-sm" name="examen_fisico_cardio" rows="2"></textarea>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label small">Abdomen</label>
                                                    <textarea class="form-control form-control-sm" name="examen_fisico_abdomen" rows="2"></textarea>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label small">Extremidades</label>
                                                    <textarea class="form-control form-control-sm" name="examen_fisico_extremidades" rows="2"></textarea>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label small">Neurológico</label>
                                                    <textarea class="form-control form-control-sm" name="examen_fisico_neuro" rows="2"></textarea>
                                                </div>
                                                <div class="col-md-12 mb-2">
                                                    <label class="form-label small">Otros Hallazgos</label>
                                                    <textarea class="form-control form-control-sm" name="examen_fisico_otros" rows="2"></textarea>
                                                </div>
                                            </div>
                                            <input type="hidden" name="examen_fisico" id="examen_fisico_completo">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Diagnóstico</label>
                                <textarea class="form-control" name="diagnostico" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tratamiento</label>
                                <textarea class="form-control" name="tratamiento" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Receta Médica</label>
                                <textarea class="form-control" name="receta_medica" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Antecedentes Personales</label>
                                <textarea class="form-control" name="antecedentes_personales" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Antecedentes Familiares</label>
                                <textarea class="form-control" name="antecedentes_familiares" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Exámenes Realizados</label>
                                <textarea class="form-control" name="examenes_realizados" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Observaciones</label>
                                <textarea class="form-control" name="observaciones" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Próxima Cita</label>
                                <input type="date" class="form-control" name="proxima_cita">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Hora de Próxima Cita</label>
                                <input type="time" class="form-control" name="hora_proxima_cita">
                                <small class="text-muted">Opcional. Si no se especifica, quedará como "Pendiente"</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Médico Responsable</label>
                                <input type="text" class="form-control" name="medico_responsable" value="<?= htmlspecialchars($_SESSION['nombre'] . ' ' . $_SESSION['apellido']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Especialidad</label>
                                <input type="text" class="form-control" name="especialidad_medico" value="<?= htmlspecialchars($_SESSION['especialidad']) ?>">
                            </div>
                        </div>
                    </div>

                    <!-- ===== Sección: Ordenar Laboratorios ===== -->
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="toggleLabs" onchange="toggleLabSelector()">
                            <label class="form-check-label fw-bold text-primary" for="toggleLabs">
                                <i class="bi bi-flask"></i> Ordenar Laboratorios
                            </label>
                        </div>

                        <div id="labSelector" class="d-none mt-3 p-3 border rounded bg-light">
                            <label class="form-label fw-semibold">Buscar y agregar laboratorios</label>
                            <input type="text" class="form-control mb-2" placeholder="Escribe para buscar..." onkeyup="searchLabs(this.value)">
                            <div id="labSearchResults" class="mb-3 border rounded p-2 bg-white" style="max-height: 250px; overflow-y: auto;">
                                <p class="text-muted mb-0">Empieza a escribir para ver laboratorios...</p>
                            </div>

                            <label class="form-label fw-semibold">Laboratorios a realizar:</label>
                            <ul id="selectedLabs" class="list-group shadow-sm"></ul>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===== Scripts ===== -->
<script>
// Funciones existentes (sin modificar)
function editMedicalRecord(id) {
    fetch('get_medical_record.php?id=' + id)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const record = data.record;
                document.getElementById('edit_id_historial').value = record.id_historial;
                document.getElementById('edit_motivo_consulta').value = record.motivo_consulta;
                document.getElementById('edit_sintomas').value = record.sintomas;
                document.getElementById('edit_examen_fisico').value = record.examen_fisico || '';
                document.getElementById('edit_diagnostico').value = record.diagnostico;
                document.getElementById('edit_tratamiento').value = record.tratamiento;
                document.getElementById('edit_receta_medica').value = record.receta_medica;
                document.getElementById('edit_antecedentes_personales').value = record.antecedentes_personales;
                document.getElementById('edit_antecedentes_familiares').value = record.antecedentes_familiares;
                document.getElementById('edit_examenes_realizados').value = record.examenes_realizados;
                document.getElementById('edit_resultados_examenes').value = record.resultados_examenes;
                document.getElementById('edit_observaciones').value = record.observaciones;

                if (record.proxima_cita) {
                    const date = new Date(record.proxima_cita);
                    const formattedDate = date.toISOString().split('T')[0];
                    document.getElementById('edit_proxima_cita').value = formattedDate;
                } else {
                    document.getElementById('edit_proxima_cita').value = '';
                }

                if (record.hora_proxima_cita) {
                    document.getElementById('edit_hora_proxima_cita').value = record.hora_proxima_cita;
                } else {
                    document.getElementById('edit_hora_proxima_cita').value = '';
                }

                document.getElementById('edit_medico_responsable').value = record.medico_responsable;
                document.getElementById('edit_especialidad_medico').value = record.especialidad_medico;

                const modal = new bootstrap.Modal(document.getElementById('editMedicalRecordModal'));
                modal.show();
            } else {
                alert('Error: ' + data.message);
            }
        });
}

function deleteMedicalRecord(id) {
    if (confirm('¿Está seguro de que desea eliminar este registro médico?')) {
        fetch('delete_medical_record.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Registro eliminado correctamente');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
}

function printPrescription(id) {
    window.open('print_prescription.php?id=' + id, '_blank');
}

// ===== NUEVAS FUNCIONES: Laboratorios =====
let selectedLabIds = [];

function toggleLabSelector() {
    const checkbox = document.getElementById('toggleLabs');
    const selector = document.getElementById('labSelector');
    if (checkbox.checked) {
        selector.classList.remove('d-none');
        searchLabs('');
    } else {
        selector.classList.add('d-none');
        selectedLabIds = [];
        document.getElementById('selectedLabs').innerHTML = '';
    }
}

function searchLabs(query) {
    fetch('../laboratories/get_all_labs.php?q=' + encodeURIComponent(query))
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('labSearchResults');
            container.innerHTML = '';
            if (data.status === 'success') {
                data.labs.forEach(lab => {
                    const div = document.createElement('div');
                    div.className = 'form-check';
                    div.innerHTML = `
                        <input class="form-check-input" type="checkbox" value="${lab.id_tipo_laboratorio}" 
                               id="lab-${lab.id_tipo_laboratorio}" 
                               onchange="toggleLabSelection(${lab.id_tipo_laboratorio}, '${lab.nombre}')">
                        <label class="form-check-label" for="lab-${lab.id_tipo_laboratorio}">
                            ${lab.nombre}
                        </label>
                    `;
                    container.appendChild(div);
                });
            } else {
                container.innerHTML = '<p class="text-muted">No se solicitaron laboratorios en esta consulta.</p>';
            }
        });
}

function toggleLabSelection(id, nombre) {
    const checkbox = document.getElementById(`lab-${id}`);
    const list = document.getElementById('selectedLabs');
    if (checkbox.checked) {
        if (!selectedLabIds.includes(id)) {
            selectedLabIds.push(id);
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            li.id = `selected-${id}`;
            li.innerHTML = `
                ${nombre}
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeLab(${id})">
                    <i class="bi bi-trash"></i>
                </button>
            `;
            list.appendChild(li);
        }
    } else {
        removeLab(id);
    }
}

function removeLab(id) {
    selectedLabIds = selectedLabIds.filter(labId => labId !== id);
    document.getElementById(`selected-${id}`)?.remove();
    document.getElementById(`lab-${id}`).checked = false;
}

// Enviar laboratorios seleccionados al guardar
document.getElementById('newMedicalRecordForm').addEventListener('submit', function () {
    const container = document.createElement('div');
    selectedLabIds.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'laboratorios[]';
        input.value = id;
        container.appendChild(input);
    });
    this.appendChild(container);
});

// Cargar laboratorios por consulta en el historial
function loadLabsForHistory(historyId, containerId) {
    fetch('../laboratories/get_labs_by_history.php?id_historial=' + historyId)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById(containerId);
            if (data.status === 'success' && data.labs.length > 0) {
                let html = '<div class="mt-3"><h6>Laboratorios solicitados:</h6><ul class="list-group">';
                data.labs.forEach(lab => {
                    html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                        ${lab.laboratorio}
                        <span class="badge ${lab.estado === 'Completado' ? 'bg-success' : 'bg-warning'} rounded-pill">${lab.estado}</span>
                        ${lab.estado === 'Completado' ? '<button class="btn btn-sm btn-info ms-2" onclick="showLabResult(`' + lab.resultados.replace(/`/g, '\\`') + '`, ' + lab.id_solicitud + ')">Ver Resultados</button>' : ''}
                    </li>`;
                });
                html += '</ul></div>';
                container.innerHTML = html;
            } else {
                container.innerHTML = '';
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

document.addEventListener('DOMContentLoaded', function () {
    const historyItems = document.querySelectorAll('[id^="labs-history-"]');
    historyItems.forEach(item => {
        const id = item.id.replace('labs-history-', '');
        loadLabsForHistory(id, item.id);
    });
});

function showLabResult(texto, idSolicitud) {
    // Cargar los resultados con valores de referencia
    fetch('../laboratories/get_result_with_reference.php?id=' + idSolicitud)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const modal = document.createElement('div');
                modal.className = 'modal fade';
                modal.tabIndex = -1;
                modal.innerHTML =
                    '<div class="modal-dialog modal-lg">' +
                        '<div class="modal-content">' +
                            '<div class="modal-header">' +
                                '<h5 class="modal-title">Resultados de Laboratorio</h5>' +
                                '<button type="button" class="btn-close" data-bs-dismiss="modal"></button>' +
                            '</div>' +
                            '<div class="modal-body">' + data.html + '</div>' +
                            '<div class="modal-footer">' +
                                '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>' +
                            '</div>' +
                        '</div>' +
                    '</div>';
                document.body.appendChild(modal);
                const bsModal = new bootstrap.Modal(modal);
                bsModal.show();
                modal.addEventListener('hidden.bs.modal', function () {
                    modal.remove();
                });
            } else {
                alert('Error al cargar los resultados: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Fallback al método original si hay un error
            const modal = document.createElement('div');
            modal.className = 'modal fade';
            modal.tabIndex = -1;
            modal.innerHTML =
                '<div class="modal-dialog modal-lg">' +
                    '<div class="modal-content">' +
                        '<div class="modal-header">' +
                            '<h5 class="modal-title">Resultados de Laboratorio</h5>' +
                            '<button type="button" class="btn-close" data-bs-dismiss="modal"></button>' +
                        '</div>' +
                        '<div class="modal-body">' +
                            '<pre style="white-space: pre-wrap; font-size: 14px;">' + texto + '</pre>' +
                        '</div>' +
                        '<div class="modal-footer">' +
                            '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>' +
                        '</div>' +
                    '</div>' +
                '</div>';
            document.body.appendChild(modal);
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
            modal.addEventListener('hidden.bs.modal', function () {
                modal.remove();
            });
        });
}
</script>

<?php include_once '../../includes/footer.php'; ?>