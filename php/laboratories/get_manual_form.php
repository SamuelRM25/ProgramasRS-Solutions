<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';
verify_session();

header('Content-Type: application/json');

$id_solicitud = $_GET['id_solicitud'] ?? 0;

try {
    $database = new Database();
    $conn = $database->getConnection();

    // Obtener datos de la solicitud
    $stmt = $conn->prepare("
        SELECT sl.id_solicitud, tl.nombre as laboratorio, p.nombre, p.apellido
        FROM solicitudes_laboratorio sl
        JOIN tipos_laboratorio tl ON sl.id_tipo_laboratorio = tl.id_tipo_laboratorio
        JOIN pacientes p ON sl.id_paciente = p.id_paciente
        WHERE sl.id_solicitud = ?
    ");
    $stmt->execute([$id_solicitud]);
    $solicitud = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$solicitud) {
        echo json_encode(['status' => 'error', 'message' => 'Solicitud no encontrada']);
        exit;
    }

    // Obtener valores de referencia
    $refStmt = $conn->prepare("
        SELECT parametro, valor_min, valor_max, unidad
        FROM valores_referencia
        WHERE id_tipo_laboratorio = (
            SELECT id_tipo_laboratorio FROM solicitudes_laboratorio WHERE id_solicitud = ?
        )
    ");
    $refStmt->execute([$id_solicitud]);
    $referencias = $refStmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$referencias) {
        echo json_encode(['status' => 'error', 'message' => 'No hay valores de referencia para este examen']);
        exit;
    }

    ob_start(); ?>
        <form id="manualResultForm" action="save_manual_result.php" method="POST">
            <input type="hidden" name="id_solicitud" value="<?= $id_solicitud ?>">
            <div class="mb-3">
                <strong>Paciente:</strong> <?= htmlspecialchars($solicitud['nombre'] . ' ' . $solicitud['apellido']) ?><br>
                <strong>Examen:</strong> <?= htmlspecialchars($solicitud['laboratorio']) ?><br>
                <strong>Fecha de Resultado:</strong> <?= date('d/m/Y H:i') ?>
            </div>
            <hr>
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Parámetro</th>
                        <th>Resultado</th>
                        <th>Referencia</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($referencias as $ref): ?>
                        <tr>
                            <td><?= htmlspecialchars($ref['parametro']) ?></td>
                            <td>
                                <input type="text" name="resultados[<?= htmlspecialchars($ref['parametro']) ?>]" class="form-control form-control-sm" required
                                       onblur="validateResult(this, <?= $ref['valor_min'] ?>, <?= $ref['valor_max'] ?>, 'status_<?= md5($ref['parametro']) ?>')">
                            </td>
                            <td><?= "{$ref['valor_min']} - {$ref['valor_max']} {$ref['unidad']}" ?></td>
                            <td id="status_<?= md5($ref['parametro']) ?>"></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="mb-3">
                <label class="form-label">Comentario (opcional)</label>
                <textarea name="comentario" class="form-control" rows="2" placeholder="Agregue un comentario adicional si lo desea..."></textarea>
            </div>
            <div class="text-end">
                <button type="submit" class="btn btn-primary">Guardar Resultados</button>
            </div>
        </form>

        <script>
        document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('manualResultForm');
            if (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault(); // Evita el envío normal

                    const formData = new FormData(form);
                    
                    // Mostrar indicador de carga
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...';

                    fetch('save_manual_result.php', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // Cerrar el modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById('manualFormModal'));
                            if (modal) {
                                modal.hide();
                            }
                            
                            // Mostrar mensaje de éxito
                            const alertDiv = document.createElement('div');
                            alertDiv.className = 'alert alert-success alert-dismissible fade show';
                            alertDiv.innerHTML = `
                                <strong>¡Éxito!</strong> ${data.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            `;
                            document.querySelector('.container-fluid').prepend(alertDiv);
                            
                            // Recargar la página después de un breve retraso
                            setTimeout(() => {
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                } else {
                                    location.reload();
                                }
                            }, 1000);
                        } else {
                            alert('Error: ' + data.message);
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Ocurrió un error al guardar el examen.');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    });
                });
            }
        });
        </script>
        
    <?php
    $html = ob_get_clean();
    echo json_encode(['status' => 'success', 'html' => $html]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>