<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AtlasGym - Panel de Control</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <img src="img/logo.jpg" alt="AtlasGym Logo">
                <h3>AtlasSystem</h3>
            </div>
            
            <ul class="nav-links">
                <li class="active">
                    <a href="clients">
                        <i class="fas fa-users"></i>
                        <span>Clientes</span>
                    </a>
                </li>
                <li>
                        <a href="memberships">
                            <i class="fas fa-id-card"></i>
                            <span>Membresías</span>
                        </a>
                </li>
                <?php if($_SESSION['user_type'] == 1): // Admin only ?>
                    <li>
                        <a href="inventory">
                            <i class="fas fa-dumbbell"></i>
                            <span>Inventario</span>
                        </a>
                    </li>
                    <li>
                        <a href="finance">
                            <i class="fas fa-wallet"></i>
                            <span>Caja</span>
                        </a>
                    </li>
                    <li>
                        <a href="staff">
                            <i class="fas fa-user-tie"></i>
                            <span>Personal</span>
                        </a>
                    </li>
                <?php endif; ?>
           
            </ul>

            <div class="sidebar-footer">
                <div class="user-info">
                    <img src="img/avatar.jpg" alt="User Avatar">
                    <div class="user-details">
                        <span><?php echo $_SESSION['username']; ?></span>
                        <small><?php echo $_SESSION['user_type'] == 1 ? 'Administrador' : 'Empleado'; ?></small>
                    </div>
                </div>
                <a href="php/logout.php" class="logout">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <header>
                <div class="header-left">
                    <button id="sidebar-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h2>Panel de Control</h2>
                </div>
            </header>

            <div id="main-content" class="content">
                <!-- Content will be loaded here -->
            </div>
        </main>
    </div>
    <script src="js/dashboard.js"></script>
    <script src="js/memberships.js"></script>
</body>
</html>