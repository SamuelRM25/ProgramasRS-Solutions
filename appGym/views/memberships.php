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
                    <tbody>
                        <?php foreach($registrations as $reg): 
                            $status = calculateStatus($reg['registration_date'], $reg['duration_membership']);
                            $remainingDays = calculateRemainingDays($reg['registration_date'], $reg['duration_membership']);
                        ?>
                        <tr>
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
                                <script>
                                // Define the function before it's used in the HTML
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
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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
    let selectedClientData = null;
    const floatingButton = document.getElementById('floatingButton');
    const reactivateBtn = document.getElementById('reactivateBtn');
    const modal = document.getElementById('reactivationModal');
    const closeBtn = document.querySelector('.close-modal');
    
    // Debug - check if we have caducado badges
    const caducadoBadges = document.querySelectorAll('.status-badge.caducado');
    console.log('Caducado badges found:', caducadoBadges.length);
    
    // Add click listeners to all caducado badges
    caducadoBadges.forEach(badge => {
        console.log('Badge data:', badge.dataset.client);
        
        badge.addEventListener('click', function(e) {
            console.log('Badge clicked!');
            try {
                const clientData = JSON.parse(this.dataset.client);
                console.log('Client data:', clientData);
                selectedClientData = clientData;
                
                // Remove selected class from all badges
                document.querySelectorAll('.status-badge').forEach(b => {
                    b.classList.remove('selected');
                });
                
                // Add selected class to clicked badge
                this.classList.add('selected');
                
                // Show floating button
                floatingButton.classList.add('show');
            } catch (error) {
                console.error('Error parsing client data:', error);
            }
        });
    });
    
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
</script>

<!-- Add this before the closing </div> of the module-container -->
<link rel="stylesheet" href="/appGym/css/modules/memberships.css">

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
</style>

