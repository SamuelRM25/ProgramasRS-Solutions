<?php
session_start();
require 'db.php';

 $giras = $pdo->query("SELECT id, nombre FROM giras ORDER BY nombre")->fetchAll();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // La lógica de recolección de datos permanece igual
    $codigo = trim($_POST['codigo']);
    $nombre = trim($_POST['nombre']);
    $telefono = trim($_POST['telefono']);
    $nit = trim($_POST['nit']);
    $direccion = trim($_POST['direccion']);
    $latitud = !empty($_POST['latitud']) ? trim($_POST['latitud']) : null;
    $longitud = !empty($_POST['longitud']) ? trim($_POST['longitud']) : null;
    $id_gira = !empty($_POST['id_gira']) ? $_POST['id_gira'] : null;
    $observaciones = trim($_POST['observaciones']);

    try {
        $sql = "INSERT INTO clientes (codigo, nombre, telefono, nit, direccion, latitud, longitud, id_gira, observaciones) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt= $pdo->prepare($sql);
        $stmt->execute([$codigo, $nombre, $telefono, $nit, $direccion, $latitud, $longitud, $id_gira, $observaciones]);
        
        // CAMBIO CLAVE: Redirigir a la misma página en lugar de a index.php
        $_SESSION['message'] = "¡Cliente '" . htmlspecialchars($nombre) . "' registrado con éxito!";
        header("Location: registrar_cliente.php");
        exit();

    } catch (\PDOException $e) {
        $error_message = "Error al registrar el cliente. Inténtelo de nuevo.";
        if ($e->getCode() == 23000) {
            $error_message = "Error: El código de cliente o NIT ya existe.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Nuevo Cliente</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        /* Estilo adicional para la barra de búsqueda sobre el mapa */
        .map-search-container {
            position: relative;
        }
        #map-search-input {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 1000; /* Asegurarse de que esté sobre el mapa */
            width: 250px;
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Registrar Nuevo Cliente</h1>
        <nav>
            <a href="index.php">Inicio</a>
            <a href="ver_clientes.php">Ver Clientes</a>
        </nav>

        <!-- AÑADIMOS EL MENSAJE DE ÉXITO AQUÍ -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message success">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="message" style="background-color: #f8d7da; color: #721c24; border-color: #f5c6cb;">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="registrar_cliente.php" method="post">
            <div class="form-group">
                <label for="codigo">Código de Cliente:</label>
                <input type="text" id="codigo" name="codigo" required>
            </div>
            <div class="form-group">
                <label for="nombre">Nombre Completo:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="tel" id="telefono" name="telefono" required>
            </div>
            <div class="form-group">
                <label for="nit">NIT / CF:</label>
                <input type="text" id="nit" name="nit">
            </div>
            <div class="form-group">
                <label for="id_gira">Gira:</label>
                <select id="id_gira" name="id_gira">
                    <option value="">-- Seleccione una gira --</option>
                    <?php foreach ($giras as $gira): ?>
                        <option value="<?= $gira['id'] ?>"><?= htmlspecialchars($gira['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="direccion">Dirección:</label>
                <input type="text" id="direccion" name="direccion">
            </div>
            
            <!-- SECCIÓN DE UBICACIÓN CON MAPA Y BÚSQUEDA -->
            <div class="form-group">
                <label>Ubicación en el Mapa:</label>
                <p style="font-size: 0.9em; color: #666;">
                    La aplicación intentará detectar tu ubicación actual automáticamente. 
                    Si no lo hace, o si deseas cambiarla, haz clic en el mapa o usa la barra de búsqueda.
                </p>
                <button type="button" id="get-location-btn" class="btn-geo">Usar mi ubicación actual</button>
                
                <!-- NUEVO: Contenedor para la búsqueda y el mapa -->
                <div class="map-search-container">
                    <input type="text" id="map-search-input" placeholder="Buscar una dirección...">
                    <div id="map"></div>
                </div>
                
                <!-- Campos ocultos para almacenar las coordenadas -->
                <input type="hidden" id="latitud" name="latitud">
                <input type="hidden" id="longitud" name="longitud">
            </div>

            <div class="form-group">
                <label for="observaciones">Observaciones:</label>
                <textarea id="observaciones" name="observaciones" rows="4"></textarea>
            </div>
            <button type="submit" class="btn">Registrar Cliente</button>
        </form>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="script.js"></script>
</body>
</html>