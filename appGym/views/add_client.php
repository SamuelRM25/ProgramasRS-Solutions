<?php
require_once '../php/connection.php';

try {
    $stmtMem = $conn->prepare("SELECT * FROM membresia ORDER BY id_membresia");
    $stmtMem->execute();
    $memberships = $stmtMem->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Nuevo Cliente</title>
    <link rel="stylesheet" href="/appGym/css/modules/add_client.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="add-client-container">
        <div class="form-header">
            <a href="/appGym/dashboard.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <h2>Registrar Nuevo Cliente</h2>
        </div>

        <form id="clientForm" class="client-form">
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
                <div class="membership-grid">
                    <?php foreach($memberships as $membership): ?>
                    <div class="membership-card">
                        <input type="radio" 
                               id="membership<?php echo $membership['id_membresia']; ?>" 
                               name="membership" 
                               value="<?php echo $membership['id_membresia']; ?>" 
                               required>
                        <label for="membership<?php echo $membership['id_membresia']; ?>">
                            <div class="membership-header">
                                <h4><?php echo $membership['name_membership']; ?></h4>
                                <span class="price">Q<?php echo $membership['price_membership']; ?></span>
                            </div>
                            <div class="membership-details">
                                <span class="duration"><?php echo $membership['duration_membership']; ?> </span>
                            </div>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="submit-btn">
                    <i class="fas fa-save"></i> Registrar Cliente
                </button>
            </div>
        </form>
    </div>

    <script src="/appGym/js/add_client.js"></script>
</body>
</html>