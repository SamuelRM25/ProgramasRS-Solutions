<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';
verify_session();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_solicitud = $_POST['id_solicitud'] ?? 0;
    $resultados = $_POST['resultados'] ?? [];
    $comentario = $_POST['comentario'] ?? '';

    if (!$id_solicitud || empty($resultados)) {
        $_SESSION['message'] = "Datos incompletos";
        $_SESSION['message_type'] = "danger";
        header("Location: index.php");
        exit;
    }

    try {
        $database = new Database();
        $conn = $database->getConnection();

        // Obtener id_paciente y validar existencia
        $stmt = $conn->prepare("SELECT id_paciente, id_tipo_laboratorio FROM solicitudes_laboratorio WHERE id_solicitud = ?");
        $stmt->execute([$id_solicitud]);
        $solicitud = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$solicitud) {
            throw new Exception("Solicitud no encontrada");
        }

        // Construir resultado como texto
        $resultadoTexto = "";
        foreach ($resultados as $parametro => $valor) {
            $valor = trim($valor);
            if ($valor !== '') {
                $resultadoTexto .= "$parametro: $valor\n";
            }
        }

        if ($comentario) {
            $resultadoTexto .= "\nComentario:\n$comentario";
        }

        // Actualizar solicitud
        $updateStmt = $conn->prepare("
            UPDATE solicitudes_laboratorio
            SET resultados = ?, estado = 'Completado', fecha_resultado = NOW()
            WHERE id_solicitud = ?
        ");
        $updateStmt->execute([$resultadoTexto, $id_solicitud]);

        $_SESSION['message'] = "Examen guardado correctamente";
        $_SESSION['message_type'] = "success";
        
        // Verificar si es una solicitud AJAX
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'message' => 'Examen guardado correctamente',
                'redirect' => 'laboratorio_paciente.php?id=' . $solicitud['id_paciente']
            ]);
        } else {
            // Redirección normal
            header("Location: laboratorio_paciente.php?id=" . $solicitud['id_paciente']);
        }
        exit;

    } catch (Exception $e) {
        $_SESSION['message'] = "Error: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
        
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        } else {
            header("Location: index.php");
        }
        exit;
    }
}
?>