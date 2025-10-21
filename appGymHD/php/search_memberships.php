<?php
require_once 'connection.php';

// Verificar si se recibió un término de búsqueda
if (isset($_GET['term'])) {
    $searchTerm = '%' . $_GET['term'] . '%';
    
    try {
        // Preparar la consulta para buscar clientes o membresías que coincidan
        $stmt = $conn->prepare("
            SELECT 
                rc.id_recordClient,
                c.name_clien,
                c.last_name_client,
                m.name_membership,
                m.duration_membership,
                rc.registration_date
            FROM registroClientes rc
            JOIN clientes c ON rc.name_client = c.id_client
            JOIN membresia m ON rc.membership_client = m.id_membresia
            WHERE 
                LOWER(CONCAT(c.name_clien, ' ', c.last_name_client)) LIKE LOWER(:term)
                OR LOWER(m.name_membership) LIKE LOWER(:term)
            ORDER BY rc.registration_date DESC
        ");
        
        $stmt->bindParam(':term', $searchTerm, PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Procesar los resultados para incluir información adicional
        $processedResults = [];
        foreach ($results as $reg) {
            // Calcular estado y días restantes
            $regDate = new DateTime($reg['registration_date']);
            $duration = intval($reg['duration_membership']);
            $expirationDate = clone $regDate;
            $expirationDate->modify("+$duration days");
            $today = new DateTime();
            
            $status = $today <= $expirationDate ? 'ACTIVO' : 'CADUCADO';
            $remaining = $today <= $expirationDate ? $today->diff($expirationDate)->days : 0;
            
            // Agregar datos procesados
            $reg['status'] = $status;
            $reg['remaining_days'] = $remaining;
            $reg['formatted_date'] = date('d/m/Y', strtotime($reg['registration_date']));
            
            $processedResults[] = $reg;
        }
        
        // Devolver resultados como JSON
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'results' => $processedResults
        ]);
        
    } catch(PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Error en la búsqueda: ' . $e->getMessage()
        ]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'No se proporcionó un término de búsqueda'
    ]);
}
?>