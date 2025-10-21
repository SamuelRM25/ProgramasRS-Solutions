document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const usuario = document.getElementById('usuario').value;
    const password = document.getElementById('password').value;
    const errorMessage = document.getElementById('errorMessage');
    
    fetch('php/validar.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `usuario=${encodeURIComponent(usuario)}&password=${encodeURIComponent(password)}`
    })
    .then(response => response.text())
    .then(data => {
        if (data === 'success') {
            window.location.href = 'views/dashboard.php';
        } else {
            errorMessage.classList.remove('d-none');
            errorMessage.textContent = 'Usuario o contraseña incorrectos';
        }
    })
    .catch(error => {
        errorMessage.classList.remove('d-none');
        errorMessage.textContent = 'Error en el servidor';
        console.error('Error:', error);
    });
});