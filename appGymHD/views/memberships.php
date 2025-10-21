<?php
require_once '../php/connection.php';

try {
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
        ORDER BY rc.registration_date DESC
    ");
    $stmt->execute();
    $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

function calculateStatus($registrationDate, $duration) {
    $regDate = new DateTime($registrationDate);
    $duration = intval($duration);
    $expirationDate = $regDate->modify("+$duration days");
    $today = new DateTime();
    
    return $today <= $expirationDate ? 'ACTIVO' : 'CADUCADO';
}

function calculateRemainingDays($registrationDate, $duration) {
    $regDate = new DateTime($registrationDate);
    $duration = intval($duration);
    $expirationDate = clone $regDate;
    $expirationDate->modify("+$duration days");
    $today = new DateTime();
    
    $remaining = $today->diff($expirationDate)->days;
    return $today <= $expirationDate ? $remaining : 0;
}
?>

<div class="module-container">
    <div class="module-header">
        <h1>Gestión de Membresías</h1>
        <!-- Buscador eliminado -->
    </div>

    <div class="memberships-content">
        <!-- Tabla de Registros -->
        <div class="registrations-section">
            <h2>Registros de Membresías</h2>
            <div class="table-container">
                <table class="registrations-table">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Membresía</th>
                            <th>Fecha de Registro</th>
                            <th>Duración</th>
                            <th>Restante</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody id="membershipsTableBody">
                        <?php foreach($registrations as $reg): 
                            $status = calculateStatus($reg['registration_date'], $reg['duration_membership']);
                            $remainingDays = calculateRemainingDays($reg['registration_date'], $reg['duration_membership']);
                        ?>
                        <tr class="membership-row">
                            <td><?php echo $reg['name_clien'] . ' ' . $reg['last_name_client']; ?></td>
                            <td><?php echo $reg['name_membership']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($reg['registration_date'])); ?></td>
                            <td><?php echo $reg['duration_membership']; ?></td>
                            <td><?php echo $remainingDays . ' días'; ?></td>
                            <td>
                                <span class="status-badge <?php echo strtolower($status); ?>" 
                                    <?php if($status == 'CADUCADO'): ?>
                                    onclick="handleCaducadoClick(this)" 
                                    data-client='<?php 
                                        echo htmlspecialchars(json_encode([
                                            'clientId' => $reg['id_recordClient'],
                                            'clientName' => $reg['name_clien'] . ' ' . $reg['last_name_client']
                                        ])); 
                                    ?>'
                                    <?php endif; ?>>
                                        <?php echo $status; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <!-- Mensaje de no resultados eliminado -->
            </div>
        </div>

        <!-- Tarjetas de Membresía -->
        <div class="memberships-grid">
            <!-- ... existing membership cards code ... -->
        </div>
    </div>
</div>

<!-- Add this at the end of the file -->
<!-- Modal de Reactivación -->
<div id="reactivationModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Reactivar Membresía</h2>
            <button class="close-modal">&times;</button>
        </div>
        <form id="reactivationForm">
            <input type="hidden" id="clientId" name="clientId">
            <div class="client-info">
                <h3>Cliente:</h3>
                <p id="clientName"></p>
            </div>
            <div class="membership-selection">
                <h3>Seleccionar Nueva Membresía</h3>
                <?php 
                try {
                    $stmtMem = $conn->prepare("SELECT * FROM membresia ORDER BY duration_membership");
                    $stmtMem->execute();
                    $memberships = $stmtMem->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach($memberships as $mem): ?>
                        <div class="membership-option">
                            <input type="radio" 
                                   id="mem<?php echo $mem['id_membresia']; ?>" 
                                   name="membership" 
                                   value="<?php echo $mem['id_membresia']; ?>" 
                                   required>
                            <label for="mem<?php echo $mem['id_membresia']; ?>">
                                <span class="mem-name"><?php echo $mem['name_membership']; ?></span>
                                <span class="mem-price">$<?php echo $mem['price_membership']; ?></span>
                                <span class="mem-duration"><?php echo $mem['duration_membership']; ?> días</span>
                            </label>
                        </div>
                    <?php endforeach;
                } catch(PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
                ?>
            </div>
            <div class="modal-actions">
                <button type="submit" class="btn-submit">Confirmar Reactivación</button>
            </div>
        </form>
    </div>
</div>

<!-- Floating Button -->
<div id="floatingButton" class="floating-button">
    <button id="reactivateBtn" class="btn-float">
        <i class="fas fa-sync-alt"></i>
        <span>Reactivar Membresía</span>
    </button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variables existentes
    let selectedClientData = null;
    const floatingButton = document.getElementById('floatingButton');
    const reactivateBtn = document.getElementById('reactivateBtn');
    const modal = document.getElementById('reactivationModal');
    const closeBtn = document.querySelector('.close-modal');
    
    // Inicializar badges caducadas
    function initCaducadoBadges() {
        const caducadoBadges = document.querySelectorAll('.status-badge.caducado');
        console.log('Caducado badges found:', caducadoBadges.length);
        
        caducadoBadges.forEach(badge => {
            if (!badge.hasAttribute('data-listener-added')) {
                badge.setAttribute('data-listener-added', 'true');
                
                badge.addEventListener('click', function(e) {
                    console.log('Badge clicked!');
                    try {
                        const clientData = JSON.parse(this.dataset.client);
                        console.log('Client data:', clientData);
                        selectedClientData = clientData;
                        
                        document.querySelectorAll('.status-badge').forEach(b => {
                            b.classList.remove('selected');
                        });
                        
                        this.classList.add('selected');
                        
                        floatingButton.classList.add('show');
                    } catch (error) {
                        console.error('Error parsing client data:', error);
                    }
                });
            }
        });
    }
    
    // Inicializar badges caducadas al cargar la página
    initCaducadoBadges();
    
    // Reactivate button click handler
    if (reactivateBtn) {
        reactivateBtn.addEventListener('click', function() {
            if (!selectedClientData) return;
            
            document.getElementById('clientId').value = selectedClientData.clientId;
            document.getElementById('clientName').textContent = selectedClientData.clientName;
            modal.classList.add('show');
            
            // Hide floating button when modal is shown
            floatingButton.classList.remove('show');
        });
    }
    
    // Close modal when clicking the close button
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            modal.classList.remove('show');
        });
    }
    
    // Hide floating button when clicking elsewhere
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.status-badge') && 
            !e.target.closest('.floating-button') && 
            !e.target.closest('.modal-content')) {
            floatingButton.classList.remove('show');
            document.querySelectorAll('.status-badge').forEach(badge => {
                badge.classList.remove('selected');
            });
        }
    });
    
    // Handle form submission
    document.getElementById('reactivationForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        try {
            const formData = new FormData(e.target);
            const response = await fetch('../php/reactivate_membership.php', {
                method: 'POST',
                body: formData
            });
    
            const result = await response.json();
            
            if (result.success) {
                alert('Membresía reactivada exitosamente');
                location.reload();
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al procesar la solicitud');
        }
    });
});

// Mantener la función handleCaducadoClick fuera del DOMContentLoaded
function handleCaducadoClick(badge) {
    console.log('Badge clicked via onclick!');
    try {
        const clientData = JSON.parse(badge.dataset.client);
        console.log('Client data:', clientData);
        window.selectedClientData = clientData;
        
        document.querySelectorAll('.status-badge').forEach(b => {
            b.classList.remove('selected');
        });
        
        badge.classList.add('selected');
        
        document.getElementById('floatingButton').classList.add('show');
    } catch (error) {
        console.error('Error parsing client data:', error);
    }
}
</script>

<style>
/* Estilos inline para asegurar que funcionen */
.status-badge.caducado {
    cursor: pointer !important;
    background-color: #f44336 !important;
    color: white !important;
    padding: 5px 10px !important;
    border-radius: 4px !important;
    font-weight: bold !important;
    display: inline-block !important;
    transition: all 0.3s ease !important;
}

.status-badge.caducado:hover {
    transform: scale(1.05) !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2) !important;
    opacity: 0.9 !important;
}

.status-badge.selected {
    outline: 3px solid #4CAF50 !important;
    transform: scale(1.05) !important;
}

.floating-button {
    position: fixed !important;
    bottom: 30px !important;
    right: 30px !important;
    display: none !important;
    z-index: 1000 !important;
}

.floating-button.show {
    display: block !important;
}

.btn-float {
    background: #4CAF50;
    color: white;
    border: none;
    border-radius: 50px;
    padding: 12px 20px;
    font-size: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    cursor: pointer;
    transition: all 0.3s ease;
}

.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
}

.modal.show {
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: white;
    border-radius: 10px;
    padding: 20px;
    width: 90%;
    max-width: 500px;
}

/* Estilos mejorados para el buscador */
.search-container {
    margin: 20px auto;
    width: 100%;
    max-width: 500px;
    display: block !important; /* Asegurar que sea visible */
}

.search-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.search-icon {
    position: absolute;
    left: 12px;
    color: #666;
    z-index: 1;
}

.search-input {
    width: 100%;
    padding: 12px 40px 12px 40px;
    border: 1px solid #ddd;
    border-radius: 30px;
    font-size: 16px;
    transition: all 0.3s;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.search-input:focus {
    outline: none;
    border-color: #4CAF50;
    box-shadow: 0 2px 8px rgba(76,175,80,0.2);
}

.clear-search-btn {
    position: absolute;
    right: 12px;
    background: none;
    border: none;
    color: #666;
    cursor: pointer;
    font-size: 16px;
    padding: 0;
    z-index: 1;
}

/* Eliminar estilos que podrían estar ocultando el buscador */
.search-container {
    display: block !important;
}

/* Estilos adicionales para la búsqueda */
.loading-indicator {
    text-align: center;
    padding: 20px;
    color: #666;
}

.loading-indicator i {
    margin-right: 10px;
    color: #4CAF50;
}

.error-message {
    text-align: center;
    padding: 20px;
    color: #f44336;
}

.highlight {
    background-color: rgba(76, 175, 80, 0.2);
    padding: 2px;
    border-radius: 3px;
}

.search-input:focus {
    outline: none;
    border-color: #4CAF50;
    box-shadow: 0 2px 8px rgba(76,175,80,0.2);
}

.clear-search-btn {
    position: absolute;
    right: 12px;
    background: none;
    border: none;
    color: #666;
    cursor: pointer;
    font-size: 16px;
    padding: 0;
    display: none;
    z-index: 1;
}

.clear-search-btn:hover {
    color: #f44336;
}

.no-results-message {
    display: none;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    text-align: center;
    color: #666;
}

.no-results-message i {
    font-size: 48px;
    margin-bottom: 15px;
    color: #ddd;
}

.no-results-message p {
    font-size: 18px;
}

/* Estilos para los buscadores integrados en la tabla */
.table-search-container {
    margin-top: 8px;
    width: 100%;
}

.table-search-input {
    width: 100%;
    padding: 8px 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    transition: all 0.3s;
}

.table-search-input:focus {
    outline: none;
    border-color: #4CAF50;
    box-shadow: 0 0 5px rgba(76,175,80,0.2);
}

/* Estilos para las cabeceras de la tabla */
.registrations-table th {
    position: relative;
    padding-bottom: 40px; /* Espacio para el buscador */
    vertical-align: top;
}

/* Ajustes para el mensaje de no resultados */
.no-results-message {
    margin-top: 20px;
}

/* Eliminar estilos del buscador anterior que ya no se usa */
.search-container {
    display: none;
}
</style>

