// script.js

document.addEventListener('DOMContentLoaded', function() {

    // --- LÓGICA DE MODALES ---
    const mapaModal = document.getElementById('mapaModal');
    const obsModal = document.getElementById('obsModal');
    let modalMap = null;
    let modalMarker = null;

    // Función para abrir la modal del mapa
    function openMapModal(clientData) {
        document.getElementById('mapa-modal-title').textContent = `Ubicación de ${clientData.nombre}`;
        document.getElementById('mapa-modal-direccion').textContent = clientData.direccion || 'No especificada';
        mapaModal.style.display = 'block';

        const lat = parseFloat(clientData.lat);
        const lng = parseFloat(clientData.lng);

        setTimeout(() => { // Pequeño delay para asegurar que el modal es visible
            if (!modalMap) {
                modalMap = L.map('modal-map').setView([lat, lng], 16);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19, attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(modalMap);
            } else {
                modalMap.setView([lat, lng], 16);
                modalMap.invalidateSize();
            }

            if (modalMarker) {
                modalMarker.setLatLng([lat, lng]);
            } else {
                modalMarker = L.marker([lat, lng]).addTo(modalMap);
            }
        }, 200);
    }

    // Función para abrir la modal de observaciones
    function openObsModal(clientData) {
        document.getElementById('obs-modal-title').textContent = `Observaciones de ${clientData.nombre}`;
        document.getElementById('obs-modal-text').textContent = clientData.observaciones;
        obsModal.style.display = 'block';
    }

    // Función para cerrar cualquier modal
    function closeModal(modalElement) {
        modalElement.style.display = 'none';
    }

    // Event Delegation para botones de la tabla
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-mapa')) {
            e.preventDefault();
            openMapModal({
                nombre: e.target.dataset.nombre,
                direccion: e.target.dataset.direccion,
                lat: e.target.dataset.lat,
                lng: e.target.dataset.lng
            });
        }
        if (e.target.classList.contains('btn-obs')) {
            e.preventDefault();
            openObsModal({
                nombre: e.target.dataset.nombre,
                observaciones: e.target.dataset.observaciones
            });
        }
    });

    // Eventos para cerrar modales
    document.querySelector('.mapa-close').onclick = () => closeModal(mapaModal);
    document.querySelector('.obs-close').onclick = () => closeModal(obsModal);
    window.onclick = function(event) {
        if (event.target == mapaModal) closeModal(mapaModal);
        if (event.target == obsModal) closeModal(obsModal);
    }


    // --- LÓGICA DE BÚSQUEDA EN TIEMPO REAL ---
    const searchInput = document.getElementById('search-input');
    const table = document.getElementById('clientes-table');
    const tableRows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase();

        for (let i = 0; i < tableRows.length; i++) {
            const row = tableRows[i];
            const cells = row.getElementsByTagName('td');
            let found = false;

            // Buscar en las columnas de Código, Nombre y Teléfono
            for (let j = 0; j < 3; j++) { // Solo busca en las primeras 3 columnas
                const cellText = cells[j].textContent.toLowerCase();
                if (cellText.includes(query)) {
                    found = true;
                    break;
                }
            }

            // Mostrar u ocultar la fila
            if (found) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
});