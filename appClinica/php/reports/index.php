<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

verify_session();

$page_title = "Reportes - Clínica";
include_once '../../includes/header.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
} catch (Exception $e) {
    die("Error: No se pudo conectar a la base de datos");
}

// Obtener fechas para filtros
$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-t');

$start_datetime = $fecha_inicio . ' 08:00:00';
$end_datetime = date('Y-m-d', strtotime($fecha_fin . ' +1 day')) . ' 07:59:59';
?>

<div class="d-flex">

    <div class="main-content flex-grow-1">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <a href="../dashboard/index.php" class="btn btn-outline-secondary me-3">
                        <i class="bi bi-arrow-left"></i> Regresar
                    </a>
                    <h2>Reportes Generales</h2>
                </div>
            </div>

            <!-- Filtros de fecha -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" class="form-control" value="<?php echo $fecha_inicio; ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Fecha Fin</label>
                            <input type="date" name="fecha_fin" class="form-control" value="<?php echo $fecha_fin; ?>">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-filter"></i> Aplicar Filtros
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Resumen Big Data -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <h5 class="card-title">Pacientes Registrados</h5>
                            <?php
                            $stmt = $conn->query("SELECT COUNT(*) AS total FROM pacientes");
                            $totalPacientes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                            ?>
                            <p class="card-text display-4"><?php echo $totalPacientes; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <h5 class="card-title">Citas en Período</h5>
                            <?php
                            $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM citas WHERE fecha_cita BETWEEN ? AND ?");
                            $stmt->execute([$start_datetime, $end_datetime]);
                            $totalCitas = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                            ?>
                            <p class="card-text display-4"><?php echo $totalCitas; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <h5 class="card-title">Exámenes Realizados</h5>
                            <?php
                            $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM examenes_realizados WHERE fecha_examen BETWEEN ? AND ?");
                            $stmt->execute([$start_datetime, $end_datetime]);
                            $totalExamenes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                            ?>
                            <p class="card-text display-4"><?php echo $totalExamenes; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <h5 class="card-title">Medicamentos en Stock</h5>
                            <?php
                            $stmt = $conn->query("SELECT COUNT(*) AS total FROM inventario WHERE cantidad_med > 0");
                            $totalMedicamentos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                            ?>
                            <p class="card-text display-4"><?php echo $totalMedicamentos; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contabilidad -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h4 class="mb-0"><i class="bi bi-cash-coin me-2"></i> Contabilidad General</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    Ventas de Medicamentos
                                </div>
                                <div class="card-body">
                                    <?php
                                    $stmt = $conn->prepare("
                                        SELECT 
                                            SUM(dv.cantidad_vendida * dv.precio_unitario) AS total_ventas
                                        FROM detalle_ventas dv
                                        JOIN ventas v ON dv.id_venta = v.id_venta
                                        WHERE v.fecha_venta BETWEEN ? AND ?
                                    ");
                                    $stmt->execute([$start_datetime, $end_datetime]);
                                    $ventas = $stmt->fetch(PDO::FETCH_ASSOC);
                                    $totalVentas = $ventas['total_ventas'] ?? 0;
                                    ?>
                                    <h2 class="text-center">Q<?php echo number_format($totalVentas, 2); ?></h2>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    Procedimientos Menores
                                </div>
                                <div class="card-body">
                                    <?php
                                    $stmt = $conn->prepare("
                                        SELECT SUM(cobro) AS total 
                                        FROM procedimientos_menores 
                                        WHERE fecha_procedimiento BETWEEN ? AND ?
                                    ");
                                    $stmt->execute([$start_datetime, $end_datetime]);
                                    $procedimientos = $stmt->fetch(PDO::FETCH_ASSOC);
                                    $totalProcedimientos = $procedimientos['total'] ?? 0;
                                    ?>
                                    <h2 class="text-center">Q<?php echo number_format($totalProcedimientos, 2); ?></h2>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    Exámenes Médicos
                                </div>
                                <div class="card-body">
                                    <?php
                                    $stmt = $conn->prepare("
                                        SELECT SUM(cobro) AS total 
                                        FROM examenes_realizados 
                                        WHERE fecha_examen BETWEEN ? AND ?
                                    ");
                                    $stmt->execute([$start_datetime, $end_datetime]);
                                    $examenes = $stmt->fetch(PDO::FETCH_ASSOC);
                                    $totalExamenesCobro = $examenes['total'] ?? 0;
                                    ?>
                                    <h2 class="text-center">Q<?php echo number_format($totalExamenesCobro, 2); ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-warning">
                                    Compras de Medicamentos
                                </div>
                                <div class="card-body">
                                    <?php
                                    $stmt = $conn->prepare("
                                        SELECT SUM(total_compra) AS total 
                                        FROM compras 
                                        WHERE fecha_compra BETWEEN ? AND ?
                                    ");
                                    $stmt->execute([$start_datetime, $end_datetime]);
                                    $compras = $stmt->fetch(PDO::FETCH_ASSOC);
                                    $totalCompras = $compras['total'] ?? 0;
                                    ?>
                                    <h2 class="text-center">Q<?php echo number_format($totalCompras, 2); ?></h2>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-danger text-white">
                                    Ganancias Totales
                                </div>
                                <div class="card-body">
                                    <?php
                                    $ganancias = ($totalVentas + $totalProcedimientos + $totalExamenesCobro) - $totalCompras;
                                    $claseGanancias = $ganancias >= 0 ? 'text-success' : 'text-danger';
                                    ?>
                                    <h2 class="text-center <?php echo $claseGanancias; ?>">
                                        Q<?php echo number_format($ganancias, 2); ?>
                                    </h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reporte de Pacientes -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h4 class="mb-0"><i class="bi bi-people me-2"></i> Reporte de Pacientes</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Distribución por Género</h5>
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Género</th>
                                        <th>Cantidad</th>
                                        <th>Porcentaje</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $conn->query("
                                        SELECT genero, COUNT(*) AS total 
                                        FROM pacientes 
                                        GROUP BY genero
                                    ");
                                    $total = $totalPacientes;
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        $porcentaje = ($row['total'] / $total) * 100;
                                        echo "<tr>
                                            <td>{$row['genero']}</td>
                                            <td>{$row['total']}</td>
                                            <td>" . number_format($porcentaje, 2) . "%</td>
                                        </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Edad Promedio</h5>
                            <?php
                            $stmt = $conn->query("
                                SELECT AVG(TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE())) AS edad_promedio 
                                FROM pacientes
                            ");
                            $edad = $stmt->fetch(PDO::FETCH_ASSOC)['edad_promedio'];
                            ?>
                            <div class="text-center mt-4">
                                <h1 class="display-1"><?php echo number_format($edad, 1); ?></h1>
                                <p class="lead">años</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h5>Pacientes con más citas</h5>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Paciente</th>
                                    <th>Número de Citas</th>
                                    <th>Última Cita</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = $conn->query("
                                    SELECT 
                                        CONCAT(p.nombre, ' ', p.apellido) AS paciente,
                                        COUNT(c.id_cita) AS num_citas,
                                        MAX(c.fecha_cita) AS ultima_cita
                                    FROM pacientes p
                                    JOIN citas c ON p.id_paciente = c.historial_id
                                    GROUP BY p.id_paciente
                                    ORDER BY num_citas DESC
                                    LIMIT 5
                                ");
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<tr>
                                        <td>{$row['paciente']}</td>
                                        <td>{$row['num_citas']}</td>
                                        <td>{$row['ultima_cita']}</td>
                                    </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Reporte de Medicamentos -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h4 class="mb-0"><i class="bi bi-capsule me-2"></i> Reporte de Medicamentos</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Medicamentos más vendidos</h5>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Medicamento</th>
                                        <th>Cantidad Vendida</th>
                                        <th>Total Vendido</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $conn->query("
                                        SELECT 
                                            i.nom_medicamento,
                                            SUM(dv.cantidad_vendida) AS cantidad,
                                            SUM(dv.cantidad_vendida * dv.precio_unitario) AS total
                                        FROM detalle_ventas dv
                                        JOIN inventario i ON dv.id_inventario = i.id_inventario
                                        GROUP BY dv.id_inventario
                                        ORDER BY cantidad DESC
                                        LIMIT 5
                                    ");
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<tr>
                                            <td>{$row['nom_medicamento']}</td>
                                            <td>{$row['cantidad']}</td>
                                            <td>Q" . number_format($row['total'], 2) . "</td>
                                        </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Medicamentos menos vendidos</h5>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Medicamento</th>
                                        <th>Cantidad Vendida</th>
                                        <th>Total Vendido</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $conn->query("
                                        SELECT 
                                            i.nom_medicamento,
                                            SUM(dv.cantidad_vendida) AS cantidad,
                                            SUM(dv.cantidad_vendida * dv.precio_unitario) AS total
                                        FROM detalle_ventas dv
                                        JOIN inventario i ON dv.id_inventario = i.id_inventario
                                        GROUP BY dv.id_inventario
                                        ORDER BY cantidad ASC
                                        LIMIT 5
                                    ");
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<tr>
                                            <td>{$row['nom_medicamento']}</td>
                                            <td>{$row['cantidad']}</td>
                                            <td>Q" . number_format($row['total'], 2) . "</td>
                                        </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h5>Medicamentos próximos a vencer (30 días)</h5>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Medicamento</th>
                                    <th>Casa Farmacéutica</th>
                                    <th>Stock</th>
                                    <th>Fecha Vencimiento</th>
                                    <th>Días Restantes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = $conn->query("
                                    SELECT 
                                        nom_medicamento,
                                        casa_farmaceutica,
                                        cantidad_med,
                                        fecha_vencimiento,
                                        DATEDIFF(fecha_vencimiento, CURDATE()) AS dias_restantes
                                    FROM inventario
                                    WHERE fecha_vencimiento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
                                    ORDER BY fecha_vencimiento ASC
                                ");
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $clase = $row['dias_restantes'] <= 7 ? 'text-danger fw-bold' : 'text-warning';
                                    echo "<tr>
                                        <td>{$row['nom_medicamento']}</td>
                                        <td>{$row['casa_farmaceutica']}</td>
                                        <td>{$row['cantidad_med']}</td>
                                        <td>{$row['fecha_vencimiento']}</td>
                                        <td class='{$clase}'>{$row['dias_restantes']} días</td>
                                    </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once '../../includes/footer.php'; ?>