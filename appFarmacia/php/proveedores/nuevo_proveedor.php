<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header('Location: ../../index.html');
    exit;
}
require_once dirname(dirname(dirname(__FILE__))) . '/php/conexion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Proveedor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-truck"></i> Nuevo Proveedor</h4>
                    </div>
                    <div class="card-body">
                        <form id="nuevoProveedorForm">
                            <div class="mb-3">
                                <label for="cf_visitador" class="form-label">Nombre del Visitador</label>
                                <input type="text" class="form-control" id="cf_visitador" name="cf_visitador" required>
                            </div>
                            <div class="mb-3">
                                <label for="casa_farmaceutica" class="form-label">Casa Farmacéutica</label>
                                <input type="text" class="form-control" id="casa_farmaceutica" name="casa_farmaceutica" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone_visitador" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="phone_visitador" name="phone_visitador" required>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Guardar</button>
                                <a href="../dashboard.php?module=proveedores" class="btn btn-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('nuevoProveedorForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('../../php/proveedores/agregar.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Proveedor guardado exitosamente');
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
    </script>
</body>
</html>