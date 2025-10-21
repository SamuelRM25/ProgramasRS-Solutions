document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('php/login.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            document.getElementById('error-message').textContent = data.message;
        }
    })
    .catch(error => {
        document.getElementById('error-message').textContent = 'Error en el servidor';
    });
});