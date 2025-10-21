<?php
require_once '../php/connection.php';

try {
    // Get users with their types
    $stmt = $conn->prepare("
        SELECT 
            u.id_user,
            u.name_user,
            u.phone,
            u.email,
            t.type_user as user_type
        FROM usuarios u
        JOIN TipoUsuario t ON u.type_user = t.id_typeUser
        ORDER BY u.name_user
    ");
    $stmt->execute();
    $staff = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<div class="module-container">
    <div class="module-header">
        <h1>Personal Registrado</h1>
    </div>

    <div class="staff-content">
        <div class="staff-table-container">
            <table class="staff-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Tipo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($staff as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['name_user']); ?></td>
                        <td><?php echo htmlspecialchars($user['phone']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['user_type']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para Agregar/Editar Usuario -->
<div id="userModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Agregar Usuario</h2>
            <button class="close-modal">&times;</button>
        </div>
        <form id="userForm">
            <input type="hidden" id="userId" name="userId">
            <div class="form-group">
                <label for="userName">Nombre</label>
                <input type="text" id="userName" name="userName" required>
            </div>
            <div class="form-group">
                <label for="userPhone">Teléfono</label>
                <input type="tel" id="userPhone" name="userPhone" required>
            </div>
            <div class="form-group">
                <label for="userEmail">Email</label>
                <input type="email" id="userEmail" name="userEmail" required>
            </div>
            <div class="form-group">
                <label for="userType">Tipo de Usuario</label>
                <select id="userType" name="userType" required>
                    <?php foreach($userTypes as $type): ?>
                        <option value="<?php echo $type['id_typeUser']; ?>">
                            <?php echo htmlspecialchars($type['type_user']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="userPassword">Contraseña</label>
                <input type="password" id="userPassword" name="userPassword">
                <small>(Dejar en blanco para mantener la contraseña actual al editar)</small>
            </div>
            <div class="modal-actions">
                <button type="submit" class="btn-submit">Guardar</button>
            </div>
        </form>
    </div>
</div>

<link rel="stylesheet" href="/appGym/css/modules/staff.css">
<script src="/appGym/js/staff.js"></script>