document.addEventListener('DOMContentLoaded', function() {
    const clientForm = document.getElementById('clientForm');
    const captureBtn = document.getElementById('captureFingerprint');
    const resetBtn = document.getElementById('resetFingerprint');
    const fingerprintStatus = document.getElementById('fingerprint-status');
    
    // Configuración del lector HIKVISION DS-KAS261
    let hikDevice = null;
    
    // Inicializar el dispositivo HIKVISION
    function initializeHikDevice() {
        try {
            // Cargar la librería HIKVISION (asegúrate de incluir el SDK en tu proyecto)
            if (typeof HikFingerprintSDK !== 'undefined') {
                hikDevice = new HikFingerprintSDK.FingerprintReader();
                
                // Configurar el dispositivo (estos parámetros pueden variar según la documentación del SDK)
                hikDevice.init({
                    deviceIP: '192.168.1.100', // Reemplaza con la IP de tu dispositivo
                    port: 8000,               // Puerto estándar, ajusta según tu configuración
                    username: 'admin',        // Usuario por defecto, cámbialo según tu configuración
                    password: 'admin12345'    // Contraseña por defecto, cámbiala según tu configuración
                });
                
                console.log('Dispositivo HIKVISION inicializado correctamente');
                fingerprintStatus.innerHTML = '<div class="info"><i class="fas fa-info-circle"></i> Dispositivo listo</div>';
            } else {
                console.error('SDK de HIKVISION no encontrado');
                fingerprintStatus.innerHTML = '<div class="error"><i class="fas fa-exclamation-circle"></i> Error: SDK no encontrado</div>';
            }
        } catch (error) {
            console.error('Error al inicializar el dispositivo HIKVISION:', error);
            fingerprintStatus.innerHTML = '<div class="error"><i class="fas fa-exclamation-circle"></i> Error al inicializar el dispositivo</div>';
        }
    }
    
    // Llamar a la función de inicialización cuando se carga la página
    initializeHikDevice();
    
    // Evento para capturar huella
    captureBtn.addEventListener('click', function() {
        if (!hikDevice) {
            alert('El dispositivo no está inicializado correctamente');
            return;
        }
        
        fingerprintStatus.innerHTML = '<div class="scanning"><i class="fas fa-spinner fa-spin"></i> Escaneando huella...</div>';
        
        // Capturar huella usando el SDK de HIKVISION
        hikDevice.captureFingerprint({
            timeout: 30000, // 30 segundos de timeout
            quality: 80,    // Calidad mínima requerida (0-100)
            callback: function(result) {
                if (result.success) {
                    // Guardar los datos de la huella en el campo oculto
                    document.getElementById('fingerprintData').value = result.templateData;
                    fingerprintStatus.innerHTML = '<div class="success"><i class="fas fa-check-circle"></i> Huella capturada correctamente</div>';
                    document.getElementById('fingerprint-placeholder').classList.add('captured');
                } else {
                    fingerprintStatus.innerHTML = '<div class="error"><i class="fas fa-exclamation-circle"></i> Error: ' + result.errorMessage + '</div>';
                }
            }
        });
    });
    
    // Evento para reiniciar la captura
    resetBtn.addEventListener('click', function() {
        document.getElementById('fingerprintData').value = '';
        fingerprintStatus.innerHTML = '<div class="info"><i class="fas fa-info-circle"></i> Dispositivo listo</div>';
        document.getElementById('fingerprint-placeholder').classList.remove('captured');
    });
    
    // Evento de envío del formulario
    clientForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Verificar si se ha capturado la huella
        const fingerprintData = document.getElementById('fingerprintData').value;
        if (!fingerprintData) {
            alert('Por favor, capture la huella digital antes de registrar al cliente.');
            return;
        }
        
        // Recopilar datos del formulario
        const formData = new FormData(clientForm);
        
        // Enviar datos al servidor
        fetch('/appGymHD/php/save_client.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Cliente registrado correctamente');
                window.location.href = '/appGymHD/dashboard.php';
            } else {
                alert('Error al registrar cliente: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error al procesar la solicitud');
        });
    });
});