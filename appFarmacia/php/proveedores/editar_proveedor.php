<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header('Location: ../../index.html');
    exit;
}
require_once '../conexion.php';

if (!isset($_GET['id'])) {
    header('Location: ../../views/dashboard.php?module=proveedores');
    exit;
}

try {
    $query = "SELECT * FROM proveedores WHERE id_proveedor = :id";
    $stmt = $conexion->prepare($query);
    $stmt->execute(['id' => $_GET['id']]);
    $proveedor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$proveedor) {
        header('Location: ../../views/dashboard.php?module=proveedores');
        exit;
    }
} catch(PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    header('Location: ../../views/dashboard.php?module=proveedores');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Proveedor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0"><i class="fas fa-edit"></i> Editar Proveedor</h4>
                    </div>
                    <div class="card-body">
                        <form id="editarProveedorForm">
                            <input type="hidden" name="id_proveedor" value="<?php echo $proveedor['id_proveedor']; ?>">
                            <div class="mb-3">
                                <label for="cf_visitador" class="form-label">Nombre del Visitador</label>
                                <input type="text" class="form-control" id="cf_visitador" name="cf_visitador" 
                                       value="<?php echo $proveedor['cf_visitador']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="casa_farmaceutica" class="form-label">Casa Farmacéutica</label>
                                <input type="text" class="form-control" id="casa_farmaceutica" name="casa_farmaceutica" 
                                       value="<?php echo $proveedor['casa_farmaceutica']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone_visitador" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="phone_visitador" name="phone_visitador" 
                                       value="<?php echo $proveedor['phone_visitador']; ?>" required>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-warning">Actualizar</button>
                                <a href="../../views/dashboard.php?module=proveedores" class="btn btn-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('editarProveedorForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('actualizar.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Proveedor actualizado exitosamente');
                    window.location.href = '../../views/dashboard.php?module=proveedores';
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al procesar la solicitud');
            });
        });
    </script>
</body>
</html>