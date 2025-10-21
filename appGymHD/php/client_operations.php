<?php
require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->beginTransaction();

        // Check if client already exists
        $checkStmt = $conn->prepare("
            SELECT id_client 
            FROM clientes 
            WHERE name_clien = :name 
            AND last_name_client = :lastname 
            AND phone_client = :phone
        ");
        
        $checkStmt->execute([
            ':name' => $_POST['name'],
            ':lastname' => $_POST['lastname'],
            ':phone' => $_POST['phone']
        ]);

        $existingClient = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($existingClient) {
            // Client exists, use existing ID and type 2 (Registrado)
            $clientId = $existingClient['id_client'];
            
            // Update client type to "Registrado"
            $updateStmt = $conn->prepare("
                UPDATE clientes 
                SET type_client = 2 
                WHERE id_client = :clientId
            ");
            $updateStmt->execute([':clientId' => $clientId]);
        } else {
            // New client, insert with type 1 (Nuevo)
            $insertStmt = $conn->prepare("
                INSERT INTO clientes (name_clien, last_name_client, phone_client, type_client) 
                VALUES (:name, :lastname, :phone, 1)
            ");
            
            $insertStmt->execute([
                ':name' => $_POST['name'],
                ':lastname' => $_POST['lastname'],
                ':phone' => $_POST['phone']
            ]);

            $clientId = $conn->lastInsertId();
        }

        // Register membership
        $membershipStmt = $conn->prepare("
            INSERT INTO registroClientes (name_client, membership_client, registration_date) 
            VALUES (:clientId, :membershipId, NOW())
        ");
        
        $membershipStmt->execute([
            ':clientId' => $clientId,
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