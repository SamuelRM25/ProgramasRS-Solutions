<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Clientes</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Sistema de Gestión de Clientes y Giras</h1>
        </header>
        <nav>
            <a href="registrar_cliente.php">Registrar Cliente</a>
            <a href="ver_clientes.php">Ver Clientes</a>
        </nav>
        <main>
            <h2>Bienvenido</h2>
            <p>Utiliza el menú de navegación para registrar nuevos clientes o consultar el listado existente.</p>
            <?php
            if (isset($_SESSION['message'])) {
                echo '<div class="message success">' . $_SESSION['message'] . '</div>';
                unset($_SESSION['message']);
            }
            ?>
        </main>
    </div>
</body>
</html>