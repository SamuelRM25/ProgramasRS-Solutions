document.addEventListener('DOMContentLoaded', function() {
    let selectedClientData = null;
    const floatingButton = document.getElementById('floatingButton');
    const reactivationModal = document.getElementById('reactivationModal');

    // Only add event listeners if elements exist
    if (floatingButton && reactivationModal) {
        // Add click listeners to all caducado badges
        document.querySelectorAll('.status-badge.caducado').forEach(badge => {
            badge.addEventListener('click', function() {
                const clientData = JSON.parse(this.dataset.client);
                showFloatingButton(this, clientData);
            });
        });

        function showFloatingButton(element, clientData) {
            selectedClientData = clientData;
            
            document.querySelectorAll('.status-badge').forEach(badge => {
                badge.classList.remove('selected');
            });
            
            element.classList.add('selected');
            floatingButton.classList.add('show');
        }

        // Hide floating button when clicking elsewhere
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.status-badge') && !e.target.closest('.floating-button') && floatingButton) {
                floatingButton.classList.remove('show');
                document.querySelectorAll('.status-badge').forEach(badge => {
                    badge.classList.remove('selected');
                });
            }
        });

        function showReactivationModal() {
            if (!selectedClientData) return;
            
            const modal = document.getElementById('reactivationModal');
            document.getElementById('clientId').value = selectedClientData.clientId;
            document.getElementById('clientName').textContent = selectedClientData.clientName;
            modal.classList.add('show');
            
            // Hide floating button when modal is shown
            document.getElementById('floatingButton').classList.remove('show');
        }

        // Close modal when clicking outside
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal')) {
                e.target.classList.remove('show');
            }
        });

        // Handle form submission
        document.getElementById('reactivationForm').addEventListener('submit', async (e) => {
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
    }
});
