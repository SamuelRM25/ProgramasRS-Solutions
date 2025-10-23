<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

// Establecer la zona horaria correcta
date_default_timezone_set('America/Guatemala');

// ✅ Crear la conexión ANTES de usarla
try {
    $database = new Database();
    $conn = $database->getConnection();
} catch (Exception $e) {
    die("Error: No se pudo conectar a la base de datos");
}

// ✅ Ahora sí podés usar $conn
verify_session();

$userType = $_SESSION['tipoUsuario'] ?? '';
$userId = $_SESSION['user_id'] ?? 0;

// Si es doctor, obtener solo sus pacientes
if ($userType === 'doc') {
    $stmt = $conn->prepare("
        SELECT p.* 
        FROM pacientes p
        WHERE p.id_medico = ?
        ORDER BY p.fecha_registro DESC
    ");
    $stmt->execute([$userId]);
    $patients = $stmt->fetchAll();
} else {
    // Si es admin o user, obtener todos los pacientes
    $stmt = $conn->prepare("SELECT * FROM pacientes ORDER BY apellido, nombre");
    $stmt->execute();
    $patients = $stmt->fetchAll();
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $page_title = "Gestión de Pacientes - Clínica";
    include_once '../../includes/header.php';

    // Fetch all patients
    $stmt = $conn->prepare("SELECT * FROM pacientes ORDER BY apellido, nombre");
    $stmt->execute();
    $patients = $stmt->fetchAll();
} catch (Exception $e) {
    die("Error: No se pudo conectar a la base de datos");
}
?>

<div class="d-flex">

    <div class="main-content flex-grow-1">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <a href="../dashboard/index.php" class="btn btn-outline-secondary me-3">
                        <i class="bi bi-arrow-left"></i> Regresar
                    </a>
                    <h2>Pacientes</h2>
                </div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newPatientModal">
                    <i class="bi bi-person-plus me-2"></i>Nuevo Paciente
                </button>
            </div>

            <!-- Buscador en tiempo real -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" id="searchInput" class="form-control" placeholder="Buscar por nombre, apellido, teléfono o correo...">
                    </div>
                </div>
            </div>

            <!-- Patients Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="patientsTable">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Apellido</th>
                                    <th>Fecha Nacimiento</th>
                                    <th>Género</th>
                                    <th>Teléfono</th>
                                    <th>Correo</th>
                                    <th>Historial Clinico</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($patients as $patient): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($patient['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($patient['apellido']); ?></td>
                                    <td><?php echo htmlspecialchars($patient['fecha_nacimiento']); ?></td>
                                    <td><?php echo htmlspecialchars($patient['genero']); ?></td>
                                    <td><?php echo htmlspecialchars($patient['telefono']); ?></td>
                                    <td><?php echo htmlspecialchars($patient['correo']); ?></td>
                                    <td>
                                        <a href="medical_history.php?id=<?php echo $patient['id_paciente']; ?>" class="btn btn-sm btn-success" title="Historial Clínico">
                                            <i class="bi bi-clipboard2-pulse"></i>
                                        </a>
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
</div>

<!-- New Patient Modal -->
<div class="modal fade" id="newPatientModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuevo Paciente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="newPatientForm" action="save_patient.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Apellido</label>
                        <input type="text" class="form-control" name="apellido" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha de Nacimiento</label>
                        <input type="date" class="form-control" name="fecha_nacimiento" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Género</label>
                        <select class="form-select" name="genero" required>
                            <option value="">Seleccionar...</option>
                            <option value="Masculino">Masculino</option>
                            <option value="Femenino">Femenino</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <input type="text" class="form-control" name="direccion">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="tel" class="form-control" name="telefono">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Correo</label>
                        <input type="email" class="form-control" name="correo">
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

<?php include_once '../../includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Buscador en tiempo real
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('keyup', function() {
        const searchText = this.value.toLowerCase();
        const rows = document.querySelectorAll('#patientsTable tbody tr');
        
        rows.forEach(row => {
            const nombre = row.cells[0].textContent.toLowerCase();
            const apellido = row.cells[1].textContent.toLowerCase();
            const telefono = row.cells[4].textContent.toLowerCase();
            const correo = row.cells[5].textContent.toLowerCase();
            
            if (nombre.includes(searchText) || 
                apellido.includes(searchText) || 
                telefono.includes(searchText) || 
                correo.includes(searchText)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});
</script>