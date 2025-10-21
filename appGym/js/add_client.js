document.getElementById('clientForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    try {
        const formData = new FormData(this);
        const response = await fetch('/appGym/php/client_operations.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        
        if (result.success) {
            alert('Cliente registrado exitosamente');
            window.location.href = '/appGym/dashboard.php';
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al procesar la solicitud');
    }
});