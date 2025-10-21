<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

// Establecer la zona horaria correcta
date_default_timezone_set('America/Guatemala');

verify_session();

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Obtener el tipo de usuario y sucursal
    $userType = $_SESSION['tipoUsuario'] ?? '';
    $userSucursal = $_SESSION['id_sucursal'] ?? 0;
    
    // Si es farmacia, obtener solo su sucursal
    if ($userType === 'farmacia') {
        $stmt = $conn->prepare("SELECT * FROM inventario WHERE id_sucursal = ? ORDER BY fecha_vencimiento ASC");
        $stmt->execute([$userSucursal]);
    } else {
        // Si es admin, obtener todas las sucursales
        $stmt = $conn->prepare("
            SELECT i.*, s.nombre as sucursal_nombre 
            FROM inventario i
            LEFT JOIN sucursales s ON i.id_sucursal = s.id_sucursal
            ORDER BY i.fecha_vencimiento ASC
        ");
        $stmt->execute();
    }
    
    $page_title = "Inventario - Clínica";
    include_once '../../includes/header.php';
} catch (Exception $e) {
    die("Error: No se pudo conectar a la base de datos");
}
?>

<div class="d-flex">

    <div class="main-content flex-grow-1">
        <div class="container-fluid">
            <?php if (isset($_SESSION['inventory_message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['inventory_status'] === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['inventory_message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php 
                unset($_SESSION['inventory_message']);
                unset($_SESSION['inventory_status']);
                ?>
            <?php endif; ?>
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <a href="../dashboard/index.php" class="btn btn-outline-secondary me-3">
                        <i class="bi bi-arrow-left"></i> Regresar
                    </a>
                    <h2>Gestión de Inventario</h2>
                </div>
                <div>
                    <!-- Botón para mostrar/ocultar agotados -->
                    <button type="button" class="btn btn-outline-warning me-2" id="toggleStockBtn">
                        <i class="bi bi-eye-slash"></i> Mostrar Agotados
                    </button>
                    <!-- Botón para generar reporte -->
                    <a href="generate_report.php" class="btn btn-outline-success me-2">
                        <i class="bi bi-file-earmark-spreadsheet"></i> Reporte
                    </a>
                    <?php if ($userType === 'admin'): ?>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMedicineModal">
                        <i class="bi bi-plus-circle me-2"></i> Agregar Medicamento
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Buscador en tiempo real -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" id="searchInput" class="form-control" placeholder="Buscar por nombre, molécula o casa farmacéutica...">
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="inventoryTable">
                            <thead>
                                <tr>
                                    <th>Medicamento</th>
                                    <th>Molécula</th>
                                    <th>Presentación</th>
                                    <th>Casa Farmacéutica</th>
                                    <th>Cantidad</th>
                                    <?php if ($userType === 'admin'): ?>
                                    <th>Sucursal</th>
                                    <?php endif; ?>
                                    <th>Fecha Adquisición</th>
                                    <th>Fecha Vencimiento</th>
                                    <?php if ($userType === 'admin'): ?>
                                    <th>Acciones</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($row = $stmt->fetch()) {
                                    // Highlight items close to expiration (within 30 days)
                                    $expiry_date = new DateTime($row['fecha_vencimiento']);
                                    $today = new DateTime();
                                    $days_until_expiry = $today->diff($expiry_date)->days;
                                    $row_class = '';
                                    
                                    if ($expiry_date < $today) {
                                        $row_class = 'table-danger'; // Expired
                                    } elseif ($days_until_expiry <= 30) {
                                        $row_class = 'table-warning'; // Close to expiry
                                    }

                                    // Clase para medicamentos agotados
                                    $stock_class = ($row['cantidad_med'] == 0) ? 'stock-zero' : '';
                                ?>
                                <tr class="<?php echo $row_class . ' ' . $stock_class; ?>">
                                    <td><?php echo htmlspecialchars($row['nom_medicamento']); ?></td>
                                    <td><?php echo htmlspecialchars($row['mol_medicamento']); ?></td>
                                    <td><?php echo htmlspecialchars($row['presentacion_med']); ?></td>
                                    <td><?php echo htmlspecialchars($row['casa_farmaceutica']); ?></td>
                                    <td><?php echo htmlspecialchars($row['cantidad_med']); ?></td>
                                    <?php if ($userType === 'admin'): ?>
                                    <td><?php echo htmlspecialchars($row['sucursal_nombre'] ?? 'Sin sucursal'); ?></td>
                                    <?php endif; ?>
                                    <td><?php echo date('d/m/Y', strtotime($row['fecha_adquisicion'])); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($row['fecha_vencimiento'])); ?></td>
                                    <?php if ($userType === 'admin'): ?>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary edit-btn" 
                                                data-id="<?php echo $row['id_inventario']; ?>"
                                                data-bs-toggle="modal" data-bs-target="#editMedicineModal">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger delete-btn"
                                                data-id="<?php echo $row['id_inventario']; ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Medicine Modal -->
<div class="modal fade" id="addMedicineModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Medicamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addMedicineForm" action="save_medicine.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nom_medicamento" class="form-label">Nombre del Medicamento</label>
                        <input type="text" class="form-control" id="nom_medicamento" name="nom_medicamento" required>
                    </div>
                    <div class="mb-3">
                                            <label for="mol_medicamento" class="form-label">Molécula</label>
                    <input type="text" class="form-control" id="mol_medicamento" name="mol_medicamento" required>
                </div>
                <div class="mb-3">
                    <label for="presentacion_med" class="form-label">Presentación</label>
                    <input type="text" class="form-control" id="presentacion_med" name="presentacion_med" required>
                </div>
                <div class="mb-3">
                    <label for="casa_farmaceutica" class="form-label">Casa Farmacéutica</label>
                    <input type="text" class="form-control" id="casa_farmaceutica" name="casa_farmaceutica" required>
                </div>
                <div class="mb-3">
                    <label for="cantidad_med" class="form-label">Cantidad</label>
                    <input type="number" class="form-control" id="cantidad_med" name="cantidad_med" min="1" required>
                </div>
                <div class="mb-3">
                    <label for="id_sucursal" class="form-label">Sucursal</label>
                    <select class="form-select" id="id_sucursal" name="id_sucursal" required>
                        <option value="">Seleccionar sucursal...</option>
                        <?php
                        $stmt = $conn->prepare("SELECT id_sucursal, nombre FROM sucursales WHERE activa = 1");
                        $stmt->execute();
                        $sucursales = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($sucursales as $sucursal):
                        ?>
                        <option value="<?php echo $sucursal['id_sucursal']; ?>">
                            <?php echo htmlspecialchars($sucursal['nombre']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="fecha_adquisicion" class="form-label">Fecha de Adquisición</label>
                    <input type="date" class="form-control" id="fecha_adquisicion" name="fecha_adquisicion" required>
                </div>
                <div class="mb-3">
                    <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento</label>
                    <input type="date" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento" required>
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
<!-- Edit Medicine Modal -->
<div class="modal fade" id="editMedicineModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Medicamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editMedicineForm" action="update_medicine.php" method="POST">
                <input type="hidden" name="id_inventario" id="edit_id_inventario">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_nom_medicamento" class="form-label">Nombre del Medicamento</label>
                        <input type="text" class="form-control" id="edit_nom_medicamento" name="nom_medicamento" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_mol_medicamento" class="form-label">Molécula</label>
                        <input type="text" class="form-control" id="edit_mol_medicamento" name="mol_medicamento" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_presentacion_med" class="form-label">Presentación</label>
                        <input type="text" class="form-control" id="edit_presentacion_med" name="presentacion_med" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_casa_farmaceutica" class="form-label">Casa Farmacéutica</label>
                        <input type="text" class="form-control" id="edit_casa_farmaceutica" name="casa_farmaceutica" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_cantidad_med" class="form-label">Cantidad</label>
                        <input type="number" class="form-control" id="edit_cantidad_med" name="cantidad_med" min="1" required>
                    </div>
                    <?php if ($userType === 'admin'): ?>
                    <div class="mb-3">
                        <label for="edit_id_sucursal" class="form-label">Sucursal</label>
                        <select class="form-select" id="edit_id_sucursal" name="id_sucursal" required>
                            <option value="">Seleccionar sucursal...</option>
                            <?php
                            $stmt = $conn->prepare("SELECT id_sucursal, nombre FROM sucursales WHERE activa = 1");
                            $stmt->execute();
                            $sucursales = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($sucursales as $sucursal):
                            ?>
                            <option value="<?php echo $sucursal['id_sucursal']; ?>">
                                <?php echo htmlspecialchars($sucursal['nombre']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label for="edit_fecha_adquisicion" class="form-label">Fecha de Adquisición</label>
                        <input type="date" class="form-control" id="edit_fecha_adquisicion" name="fecha_adquisicion" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_fecha_vencimiento" class="form-label">Fecha de Vencimiento</label>
                        <input type="date" class="form-control" id="edit_fecha_vencimiento" name="fecha_vencimiento" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include_once '../../includes/footer.php'; ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle edit button clicks
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            
            // Fetch medicine data
            fetch('get_medicine.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('edit_id_inventario').value = data.id_inventario;
                    document.getElementById('edit_nom_medicamento').value = data.nom_medicamento;
                    document.getElementById('edit_mol_medicamento').value = data.mol_medicamento;
                    document.getElementById('edit_presentacion_med').value = data.presentacion_med;
                    document.getElementById('edit_casa_farmaceutica').value = data.casa_farmaceutica;
                    document.getElementById('edit_cantidad_med').value = data.cantidad_med;
                    document.getElementById('edit_fecha_adquisicion').value = data.fecha_adquisicion;
                    document.getElementById('edit_fecha_vencimiento').value = data.fecha_vencimiento;
                    <?php if ($userType === 'admin'): ?>
                    if (data.id_sucursal) {
                        document.getElementById('edit_id_sucursal').value = data.id_sucursal;
                    }
                    <?php endif; ?>
                });
        });
    });

    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('keyup', function() {
        const searchText = this.value.toLowerCase();
        const rows = document.querySelectorAll('#inventoryTable tbody tr');
        
        rows.forEach(row => {
            const medicineName = row.cells[0].textContent.toLowerCase();
            const molecule = row.cells[1].textContent.toLowerCase();
            const pharma = row.cells[3].textContent.toLowerCase();
            
            if (medicineName.includes(searchText) || molecule.includes(searchText) || pharma.includes(searchText)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Botón para mostrar/ocultar agotados
    const toggleStockBtn = document.getElementById('toggleStockBtn');
    let showZeroStock = false;
    
    toggleStockBtn.addEventListener('click', function() {
        showZeroStock = !showZeroStock;
        const rows = document.querySelectorAll('#inventoryTable tbody tr.stock-zero');
        
        rows.forEach(row => {
            if (showZeroStock) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
        
        toggleStockBtn.innerHTML = showZeroStock ? 
            '<i class="bi bi-eye"></i> Ocultar Agotados' : 
            '<i class="bi bi-eye-slash"></i> Mostrar Agotados';
    });
    
    // Inicialmente ocultamos los agotados
    document.querySelectorAll('#inventoryTable tbody tr.stock-zero').forEach(row => {
        row.style.display = 'none';
    });

    
    // Handle delete button clicks
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede revertir",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'delete_medicine.php?id=' + id;
                }
            });
        });
    });
    
    // Set today's date as default for acquisition date
    document.getElementById('fecha_adquisicion').valueAsDate = new Date();
});
</script>