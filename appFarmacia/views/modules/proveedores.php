<?php
require_once dirname(dirname(dirname(__FILE__))) . '/php/conexion.php';

try {
    $query = "SELECT * FROM proveedores ORDER BY id_proveedor ASC";
    $stmt = $conexion->prepare($query);
    $stmt->execute();
    $proveedores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $proveedores = [];
}
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h2><i class="fas fa-truck"></i> Proveedores</h2>
        </div>
        <div class="col-md-4">
            <div class="input-group mb-3">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control" id="searchProveedor" placeholder="Buscar proveedor...">
            </div>
        </div>
        <div class="col text-end">
            <a href="../php/proveedores/nuevo_proveedor.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Proveedor
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Nombre del Visitador</th>
                    <th>Casa Farmacéutica</th>
                    <th>Teléfono</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="proveedoresTableBody">
                <?php foreach($proveedores as $proveedor): ?>
                <tr>
                    <td><?php echo $proveedor['cf_visitador']; ?></td>
                    <td><?php echo $proveedor['casa_farmaceutica']; ?></td>
                    <td><?php echo $proveedor['phone_visitador']; ?></td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="editarProveedor(<?php echo $proveedor['id_proveedor']; ?>)">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="eliminarProveedor(<?php echo $proveedor['id_proveedor']; ?>)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="../../js/proveedores.js"></script>