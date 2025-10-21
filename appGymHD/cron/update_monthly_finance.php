<?php
require_once '../php/connection.php';

try {
    // Only execute on the last day of the month
    if (date('Y-m-t') === date('Y-m-d')) {
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
} catch(PDOException $e) {
    error_log("Error en actualización mensual: " . $e->getMessage());
}
?>