<?php
require_once '../php/connection.php';

try {
    // Consulta para clientes
    $stmt = $conn->prepare("
        SELECT c.*, tc.type_client as client_type 
        FROM clientes c 
        LEFT JOIN TipoCliente tc ON c.type_client = tc.id_typeClient 
        ORDER BY c.id_client DESC
    ");
    $stmt->execute();
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Consulta para membresías
    $stmtMem = $conn->prepare("SELECT * FROM membresia ORDER BY id_membresia");
    $stmtMem->execute();
    $memberships = $stmtMem->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes - Panel Administrador</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/modules/clients.css">
</head>

<div class="module-container">
    <div class="module-header">
        <h1>Gestión de Clientes</h1>
    </div>

    <div class="clients-content">
        <div class="search-bar">
            <div class="search-input">
                <i class="fas fa-search"></i>
                <input type="text" id="searchClient" placeholder="Buscar cliente...">
            </div>
            <button class="btn-add" onclick="window.location.href='views/add_client.php'">
                <i class="fas fa-user-plus"></i>
                Nuevo Cliente
            </button>
        </div>

        <div class="clients-table-container">
            <table class="clients-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Teléfono</th>
                        <th>Tipo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($clients as $client): ?>
                    <tr>
                        <td><?php echo $client['name_clien']; ?></td>
                        <td><?php echo $client['last_name_client']; ?></td>
                        <td><?php echo $client['phone_client']; ?></td>
                        <td>
                            <span class="client-type <?php echo strtolower($client['client_type']); ?>">
                                <?php echo $client['client_type']; ?>
                            </span>
                        </td>
                        <td class="actions">
                            <button class="btn-icon" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn-icon" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-icon" title="Asignar membresía">
                                <i class="fas fa-id-card"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para Nuevo Cliente -->
<div class="modal" id="newClientModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Registrar Nuevo Cliente</h2>
            <button class="close-modal">×</button>
        </div>
        <form id="newClientForm">
            <div class="form-section">
                <h3>Información Personal</h3>
                <div class="form-group">
                    <label for="name">Nombre</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="lastname">Apellido</label>
                    <input type="text" id="lastname" name="lastname" required>
                </div>
                <div class="form-group">
                    <label for="phone">Teléfono</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>
            </div>

            <div class="form-section">
                <h3>Selección de Membresía</h3>
                <div class="membership-options">
                    <?php foreach($memberships as $membership): ?>
                    <div class="membership-option">
                        <input type="radio" 
                               id="membership<?php echo $membership['id_membresia']; ?>" 
                               name="membership" 
                               value="<?php echo $membership['id_membresia']; ?>" 
                               required>
                        <label for="membership<?php echo $membership['id_membresia']; ?>">
                            <span class="membership-name"><?php echo $membership['name_membership']; ?></span>
                            <span class="membership-price">$<?php echo $membership['price_membership']; ?></span>
                            <span class="membership-duration"><?php echo $membership['duration_membership']; ?> días</span>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">Registrar Cliente</button>
            </div>
        </form>
    </div>
</div>

<script src="../js/clients.js"></script>