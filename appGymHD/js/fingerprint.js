document.addEventListener('DOMContentLoaded', function() {
    const captureBtn = document.getElementById('captureFingerprint');
    const resetBtn = document.getElementById('resetFingerprint');
    const fingerprintStatus = document.getElementById('fingerprint-status');
    const fingerprintData = document.getElementById('fingerprintData');
    
    // Simulación de captura de huella digital
    captureBtn.addEventListener('click', function() {
        fingerprintStatus.innerHTML = '<div class="scanning"><i class="fas fa-spinner fa-spin"></i> Escaneando huella...</div>';
        
        // Simulación de proceso de escaneo
        setTimeout(function() {
            // En un sistema real, aquí se conectaría con el API del lector de huellas
            const simulatedData = generateSimulatedFingerprintData();
            fingerprintData.value = simulatedData;
            
            fingerprintStatus.innerHTML = '<div class="success"><i class="fas fa-check-circle"></i> Huella capturada correctamente</div>';
            document.getElementById('fingerprint-placeholder').classList.add('captured');
        }, 2000);
    });
    
    resetBtn.addEventListener('click', function() {
        fingerprintStatus.innerHTML = '';
        fingerprintData.value = '';
        document.getElementById('fingerprint-placeholder').classList.remove('captured');
    });
    
    // Función para generar datos simulados de huella
    function generateSimulatedFingerprintData() {
        // En un sistema real, esto vendría del dispositivo de captura
        return 'fp_' + Math.random().toString(36).substring(2, 15);
    }
});