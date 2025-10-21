<?php
require 'db.php';

// Obtener giras para el filtro
 $giras = $pdo->query("SELECT id, nombre FROM giras ORDER BY nombre")->fetchAll();

// La consulta SQL sigue siendo la misma, ya que necesitamos todos los datos
 $sql = "SELECT c.id, c.codigo, c.nombre, c.telefono, c.nit, c.direccion, c.observaciones, c.latitud, c.longitud, g.nombre as nombre_gira FROM clientes c LEFT JOIN giras g ON c.id_gira = g.id";
 $params = [];

if (isset($_GET['gira_id']) && !empty($_GET['gira_id'])) {
    $sql .= " WHERE c.id_gira = ?";
    $params[] = $_GET['gira_id'];
}

 $sql .= " ORDER BY c.nombre";

 $stmt = $pdo->prepare($sql);
 $stmt->execute($params);
 $clientes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ver Clientes Registrados</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
</head>
<body>
    <div class="container">
        <h1>Listado de Clientes</h1>
        <nav>
            <a href="index.php">Inicio</a>
            <a href="registrar_cliente.php">Registrar Cliente</a>
        </nav>

        <!-- NUEVO: BUSCADOR EN TIEMPO REAL -->
        <div class="form-group" style="margin-top: 20px;">
            <label for="search-input">Buscar Cliente:</label>
            <input type="text" id="search-input" placeholder="Escribe para buscar por nombre, código, teléfono...">
        </div>

        <form method="GET" action="ver_clientes.php" style="margin-top: 20px;">
            <div class="form-group" style="max-width: 300px; display: inline-block;">
                <label for="gira_id">Filtrar por Gira:</label>
                <select id="gira_id" name="gira_id" onchange="this.form.submit()">
                    <option value="">Todas las Giras</option>
                    <?php foreach ($giras as $gira): ?>
                        <option value="<?= $gira['id'] ?>" <?= (isset($_GET['gira_id']) && $_GET['gira_id'] == $gira['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($gira['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>

        <div class="table-container">
            <table id="clientes-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>NIT</th>
                        <th>Gira</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($clientes) > 0): ?>
                        <?php foreach ($clientes as $cliente): ?>
                            <tr>
                                <td><?= htmlspecialchars($cliente['codigo']) ?></td>
                                <td><?= htmlspecialchars($cliente['nombre']) ?></td>
                                <td><?= htmlspecialchars($cliente['telefono']) ?></td>
                                <td><?= htmlspecialchars($cliente['nit'] ?: 'N/A') ?></td>
                                <td><?= htmlspecialchars($cliente['nombre_gira'] ?: 'Sin Gira Asignada') ?></td>
                                <td>
                                    <!-- Botón de Mapa -->
                                    <?php if ($cliente['latitud'] && $cliente['longitud']): ?>
                                        <button class="btn-mapa" 
                                                data-nombre="<?= htmlspecialchars($cliente['nombre']) ?>"
                                                data-direccion="<?= htmlspecialchars($cliente['direccion']) ?>"
                                                data-lat="<?= $cliente['latitud'] ?>"
                                                data-lng="<?= $cliente['longitud'] ?>">
                                            Ver Mapa
                                        </button>
                                    <?php else: ?>
                                        <span style="color: #999; font-size: 12px;">Sin ubicación</span>
                                    <?php endif; ?>

                                    <!-- NUEVO: Botón de Observaciones -->
                                    <?php if (!empty($cliente['observaciones'])): ?>
                                        <button class="btn-obs" 
                                                data-nombre="<?= htmlspecialchars($cliente['nombre']) ?>"
                                                data-observaciones="<?= htmlspecialchars($cliente['observaciones']) ?>">
                                            Ver Obs.
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">No se encontraron clientes.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL DEL MAPA (sin observaciones) -->
    <div id="mapaModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="mapa-modal-title">Información del Cliente</h2>
                <span class="modal-close mapa-close">&times;</span>
            </div>
            <div class="modal-body">
                <p><strong>Dirección:</strong> <span id="mapa-modal-direccion"></span></p>
                <div id="modal-map"></div>
            </div>
        </div>
    </div>

    <!-- NUEVA MODAL PARA OBSERVACIONES -->
    <div id="obsModal" class="modal">
        <div class="modal-content obs-modal-content">
            <div class="modal-header">
                <h2 id="obs-modal-title">Observaciones</h2>
                <span class="modal-close obs-close">&times;</span>
            </div>
            <div class="modal-body">
                <p id="obs-modal-text"></p>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="script.js"></script>
</body>
</html>