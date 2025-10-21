<?php
require_once dirname(dirname(dirname(__FILE__))) . '/php/conexion.php';

try {
    $query = "SELECT i.*, p.casa_farmaceutica 
              FROM inventario i 
              LEFT JOIN proveedores p ON i.proveedor_med = p.id_proveedor 
              ORDER BY i.id_inventario ASC";
    $stmt = $conexion->prepare($query);
    $stmt->execute();
    $inventario = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $inventario = [];
}
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h2><i class="fas fa-boxes"></i> Inventario de Medicamentos</h2>
        </div>
        <div class="col text-end">
            <a href="../php/inventario/nuevo_inventario.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Agregar Inventario
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Molécula</th>
                    <th>Presentación</th>
                    <th>Proveedor</th>
                    <th>Fecha Adquisición</th>
                    <th>Fecha Vencimiento</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($inventario as $item): ?>
                <tr>
                    <td><?php echo $item['codigo_med']; ?></td>
                    <td><?php echo $item['nombre_med']; ?></td>
                    <td><?php echo $item['molecula_med']; ?></td>
                    <td><?php echo $item['presentacion_med']; ?></td>
                    <td><?php echo $item['casa_farmaceutica']; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($item['fecha_adquisicion'])); ?></td>
                    <td><?php 
                        $fecha_vencimiento = strtotime($item['fecha_vencimiento']);
                        $hoy = time();
                        $diff = $fecha_vencimiento - $hoy;
                        $dias = floor($diff / (60 * 60 * 24));
                        
                        $clase = '';
                        if ($dias <= 30) {
                            $clase = 'text-danger fw-bold';
                        } elseif ($dias <= 90) {
                            $clase = 'text-warning';
                        }
                        echo "<span class='$clase'>" . date('d/m/Y', $fecha_vencimiento) . "</span>";
                    ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.table td {
    vertical-align: middle;
}
.text-danger {
    background-color: rgba(255, 0, 0, 0.1);
}
.text-warning {
    background-color: rgba(255, 193, 7, 0.1);
}
</style>