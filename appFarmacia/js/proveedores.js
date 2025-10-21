document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchProveedor');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const tbody = document.getElementById('proveedoresTableBody');
            const rows = tbody.getElementsByTagName('tr');

            Array.from(rows).forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }
});

function editarProveedor(id) {
    window.location.href = `../php/proveedores/editar_proveedor.php?id=${id}`;
}

function eliminarProveedor(id) {
    if (confirm('¿Está seguro de que desea eliminar este proveedor?')) {
        fetch(`../../php/proveedores/eliminar.php?id=${id}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Proveedor eliminado exitosamente');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar el proveedor');
        });
    }
}