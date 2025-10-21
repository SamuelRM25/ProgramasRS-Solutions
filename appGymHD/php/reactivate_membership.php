<?php
require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->beginTransaction();

        // Insert new membership record
        $stmt = $conn->prepare("
            INSERT INTO registroClientes (name_client, membership_client, registration_date) 
            VALUES (:clientId, :membershipId, NOW())
        ");
        
        $stmt->execute([
            ':clientId' => $_POST['clientId'],
            ':membershipId' => $_POST['membership']
        ]);

        $conn->commit();
        echo json_encode(['success' => true]);

    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>