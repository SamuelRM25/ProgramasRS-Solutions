<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header('Location: ../index.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmacia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="../css/dashboard.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar" class="active">
            <div class="sidebar-header">
                <i class="fas fa-clinic-medical"></i>
                <h3>Farmacia</h3>
            </div>

            <div class="user-info">
                <span><?php echo $_SESSION['name_user']; ?></span>
            </div>

            <ul class="list-unstyled components">
                <li>
                    <a href="#" data-module="despacho">
                        <i class="fas fa-truck-medical"></i>
                        <span>Despacho</span>
                    </a>
                </li>
                <li>
                    <a href="#" data-module="compras">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Compras</span>
                    </a>
                </li>
                <li>
                    <a href="#" data-module="ventas">
                        <i class="fas fa-cash-register"></i>
                        <span>Ventas</span>
                    </a>
                </li>
                <li>
                    <a href="#" data-module="proveedores">
                        <i class="fas fa-truck"></i>
                        <span>Proveedores</span>
                    </a>
                </li>
                <li>
                    <a href="#" data-module="inventario">
                        <i class="fas fa-boxes"></i>
                        <span>Inventario</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-footer">
                <a href="../php/logout.php" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Cerrar Sesión</span>
                </a>
            </div>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <button type="button" id="sidebarCollapse" class="btn btn-info">
                <i class="fas fa-bars"></i>
            </button>
            <div id="main-content" class="container-fluid">
                <!-- Dynamic content will be loaded here -->
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/dashboard.js"></script>
</body>
</html>