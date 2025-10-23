<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';
verify_session();

try {
    $database = new Database();
    $conn = $database->getConnection();

    // Listar insumos
    $stmt = $conn->query("SELECT * FROM insumos_laboratorio ORDER BY nombre");
    $insumos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $page_title = "Inventario - Laboratorios";
    include_once '../../includes/header.php';
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-box-seam"></i> Inventario de Laboratorio</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newInsumoModal">
            <i class="bi bi-plus-circle"></i> Nuevo Insumo
        </button>
    </div>

    <!-- Tarjetas de resumen -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5>Stock Bajo</h5>
                    <h2 id="stockBajoCount">0</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h5>Próximos a Vencer</h5>
                    <h2 id="vencerCount">0</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de insumos -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Stock</th>
                            <th>Mínimo</th>
                            <th>Vencimiento</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($insumos as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['nombre']) ?></td>
                                <td><?= htmlspecialchars($item['categoria']) ?></td>
                                <td><?= $item['stock'] ?></td>
                                <td><?= $item['stock_minimo'] ?></td>
                                <td><?= date('d/m/Y', strtotime($item['fecha_vencimiento'])) ?></td>
                                <td>
                                    <?php if ($item['stock'] <= $item['stock_minimo']): ?>
                                        <span class="badge bg-danger">Crítico</span>
                                    <?php elseif (strtotime($item['fecha_vencimiento']) <= strtotime('+30 days')): ?>
                                        <span class="badge bg-warning">Por Vencer</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">OK</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="editInsumo(<?= $item['id_insumo'] ?>)">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Nuevo Insumo -->
<div class="modal fade" id="newInsumoModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="guardar_insumo.php" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Insumo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Categoría</label>
                        <select class="form-select" name="categoria" required>
                            <option value="Reactivo">Reactivo</option>
                            <option value="Tubo">Tubo</option>
                            <option value="Hisopo">Hisopo</option>
                            <option value="Micropipeta">Micropipeta</option>
                            <option value="Otros">Otros</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stock Inicial</label>
                        <input type="number" class="form-control" name="stock" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stock Mínimo</label>
                        <input type="number" class="form-control" name="stock_minimo" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha de Vencimiento</label>
                        <input type="date" class="form-control" name="fecha_vencimiento" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include_once '../../includes/footer.php'; ?>