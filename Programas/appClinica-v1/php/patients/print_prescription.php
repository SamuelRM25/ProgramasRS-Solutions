<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

verify_session();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de receta inválido");
}

$id_historial = $_GET['id'];

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Obtener receta y datos del paciente
    $stmt = $conn->prepare("
        SELECT 
            h.receta_medica, 
            h.fecha_consulta, 
            h.medico_responsable,
            h.especialidad_medico,
            p.nombre, 
            p.apellido,
            p.fecha_nacimiento,
            p.genero
        FROM historial_clinico h
        JOIN pacientes p ON h.id_paciente = p.id_paciente
        WHERE h.id_historial = ?
    ");
    $stmt->execute([$id_historial]);
    $receta = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$receta) {
        die("Receta médica no encontrada");
    }
    
    // Calcular edad
    $fecha_nac = new DateTime($receta['fecha_nacimiento']);
    $hoy = new DateTime();
    $edad = $hoy->diff($fecha_nac)->y;
    
    // Formatear fecha
    $fecha_consulta = new DateTime($receta['fecha_consulta']);
    $fecha_formateada = $fecha_consulta->format('d/m/Y');
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receta Médica #<?php echo $id_historial; ?></title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .prescription-container {
            width: 80mm;
            margin: 0 auto;
            background-color: white;
            padding: 15px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .text-center {
            text-align: center;
        }
        .mb-2 {
            margin-bottom: 10px;
        }
        .mb-3 {
            margin-bottom: 15px;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        .prescription-content {
            white-space: pre-wrap;
            font-size: 14px;
            line-height: 1.4;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 14px;
        }
        .print-button {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        @media print {
            .print-button {
                display: none;
            }
            body {
                background-color: white;
                padding: 0;
            }
            .prescription-container {
                box-shadow: none;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="prescription-container">
        <div class="text-center mb-3">
            <h2 style="margin: 0;">Servicios Médicos Siloé</h2>
            <p style="margin: 5px 0;">Nentón, Huehuetenango</p>
            <p style="margin: 5px 0;">Tel: 4623-2418</p>
        </div>
        
        <div class="divider"></div>
        
        <div class="mb-3">
            <p><strong>Fecha:</strong> <?php echo $fecha_formateada; ?></p>
            <p><strong>Paciente:</strong> <?php echo htmlspecialchars($receta['nombre'] . ' ' . $receta['apellido']); ?></p>
            <p><strong>Edad:</strong> <?php echo $edad; ?> años</p>
            <p><strong>Género:</strong> <?php echo htmlspecialchars($receta['genero']); ?></p>
        </div>
        
        <div class="divider"></div>
        
        <div class="mb-3">
            <h3 class="text-center" style="margin: 5px 0;">RECETA MÉDICA</h3>
            <div class="prescription-content">
                <?php echo nl2br(htmlspecialchars($receta['receta_medica'])); ?>
            </div>
        </div>
        
        <div class="divider"></div>
        
        <div class="footer">
            <p>Médico Responsable: <?php echo htmlspecialchars($receta['medico_responsable']); ?></p>
            <p>Especialidad: <?php echo htmlspecialchars($receta['especialidad_medico']); ?></p>
            <p>Firma: __________________________</p>
        </div>
    </div>
    
    <button class="print-button" onclick="window.print();">Imprimir Receta</button>
    
    <script>
        // Autoimpresión opcional
        window.onload = function() {
            // Descomentar para imprimir automáticamente
            // window.print();
        };
    </script>
</body>
</html>