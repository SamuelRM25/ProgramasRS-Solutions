<?php
require_once '../php/connection.php';

try {
    // Get equipment with their types
    $stmt = $conn->prepare("
        SELECT e.id_equip, e.cant_equip, te.type_equip
        FROM equipo e
        JOIN TipoEquipo te ON e.type_equip = te.id_typeEquip
        ORDER BY te.type_equip
    ");
    $stmt->execute();
    $equipment = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<div class="module-container">
    <div class="module-header">
        <h1>Inventario de Equipo</h1>
    </div>

    <div class="inventory-content">
        <div class="equipment-table-container">
            <table class="equipment-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipo de Equipo</th>
                        <th>Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($equipment as $equip): ?>
                    <tr>
                        <td>#<?php echo $equip['id_equip']; ?></td>
                        <td><?php echo $equip['type_equip']; ?></td>
                        <td><?php echo $equip['cant_equip']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>