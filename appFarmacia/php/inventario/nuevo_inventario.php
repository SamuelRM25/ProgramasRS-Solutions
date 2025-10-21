<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header('Location: ../../index.html');
    exit;
}
require_once '../conexion.php';

// Obtener lista de proveedores para el select
try {
    $query = "SELECT id_proveedor, casa_farmaceutica FROM proveedores ORDER BY casa_farmaceutica ASC";
    $stmt = $conexion->prepare($query);
    $stmt->execute();
    $proveedores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $proveedores = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Medicamento - Inventario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-pills"></i> Nuevo Medicamento</h4>
                    </div>
                    <div class="card-body">
                        <form id="nuevoInventarioForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="codigo_med" class="form-label">Código de Barras</label>
                                    <input type="text" class="form-control" id="codigo_med" name="codigo_med" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="nombre_med" class="form-label">Nombre del Medicamento</label>
                                    <input type="text" class="form-control" id="nombre_med" name="nombre_med" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="molecula_med" class="form-label">Molécula</label>
                                    <input type="text" class="form-control" id="molecula_med" name="molecula_med" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="presentacion_med" class="form-label">Presentación</label>
                                    <input type="text" class="form-control" id="presentacion_med" name="presentacion_med" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="proveedor_med" class="form-label">Proveedor</label>
                                    <select class="form-select" id="proveedor_med" name="proveedor_med" required>
                                        <option value="">Seleccione un proveedor</option>
                                        <?php foreach($proveedores as $proveedor): ?>
                                            <option value="<?php echo $proveedor['id_proveedor']; ?>">
                                                <?php echo $proveedor['casa_farmaceutica']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="fecha_adquisicion" class="form-label">Fecha de Adquisición</label>
                                    <input type="date" class="form-control" id="fecha_adquisicion" name="fecha_adquisicion" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento</label>
                                    <input type="date" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento" required>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Guardar</button>
                                <a href="../../views/dashboard.php?module=inventario" class="btn btn-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('nuevoInventarioForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('agregar.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Medicamento agregado exitosamente');
                    window.location.href = '../../views/dashboard.php';
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al procesar la solicitud');
            });
        });

        // Set default date for fecha_adquisicion
        document.getElementById('fecha_adquisicion').valueAsDate = new Date();
    </script>
</body>
</html>