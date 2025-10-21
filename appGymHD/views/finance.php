<?php
require_once '../php/connection.php';

try {
    // Get current month's statistics
    $stmt = $conn->prepare("
        SELECT 
            COUNT(rc.id_recordClient) as total_customers,
            SUM(m.price_membership) as total_amount,
            MONTH(CURRENT_DATE()) as current_month
        FROM registroClientes rc
        JOIN membresia m ON rc.membership_client = m.id_membresia
        WHERE MONTH(rc.registration_date) = MONTH(CURRENT_DATE())
        AND YEAR(rc.registration_date) = YEAR(CURRENT_DATE())
    ");
    $stmt->execute();
    $currentMonth = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if it's the last day of the month
    $lastDay = date('Y-m-t') === date('Y-m-d');
    
    if ($lastDay) {
        // Insert or update monthly record
        $stmt = $conn->prepare("
            INSERT INTO caja (amount_finance, amount_monthly, amount_customers)
            VALUES (:amount, :month, :customers)
            ON DUPLICATE KEY UPDATE 
            amount_finance = :amount,
            amount_monthly = :month,
            amount_customers = :customers
        ");
        
        $stmt->execute([
            ':amount' => $currentMonth['total_amount'] ?? 0,
            ':month' => $currentMonth['current_month'],
            ':customers' => $currentMonth['total_customers'] ?? 0
        ]);
    }

    // Get monthly history
    $stmt = $conn->prepare("
        SELECT 
            c.amount_monthly as month,
            c.amount_customers as total_customers,
            c.amount_finance as total_amount
        FROM caja c
        ORDER BY c.amount_monthly DESC
        LIMIT 12
    ");
    $stmt->execute();
    $monthlyStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<div class="module-container">
    <div class="module-header">
        <h1>Gestión de Caja</h1>
    </div>

    <div class="finance-content">
        <!-- Current Month Summary -->
        <div class="summary-cards">
            <div class="summary-card">
                <div class="card-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="card-content">
                    <h3>Clientes Este Mes</h3>
                    <p class="card-value"><?php echo $currentMonth['total_customers'] ?? 0; ?></p>
                </div>
            </div>
            <div class="summary-card">
                <div class="card-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="card-content">
                    <h3>Ingresos Este Mes</h3>
                    <p class="card-value">Q<?php echo number_format($currentMonth['total_amount'] ?? 0, 2); ?></p>
                </div>
            </div>
        </div>

        <!-- Monthly History Table -->
        <div class="monthly-history">
            <h2>Historial Mensual</h2>
            <div class="table-container">
                <table class="finance-table">
                    <thead>
                        <tr>
                            <th>Mes</th>
                            <th>Clientes Registrados</th>
                            <th>Ingresos Totales</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($monthlyStats as $stat): ?>
                            <tr>
                                <td><?php echo date('F Y', strtotime($stat['month'] . '-01')); ?></td>
                                <td><?php echo $stat['total_customers']; ?></td>
                                <td>Q<?php echo number_format($stat['total_amount'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>