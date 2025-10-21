<?php
session_start();

$host = "byhrxwbsgw3qn1pix9ky-mysql.services.clever-cloud.com";
$dbname = "byhrxwbsgw3qn1pix9ky";
$username = "utfeg78xjtoqdlac";
$password = "rmpr8nEU1yWB9UgJJxlp";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user = $_POST['username'];
        $pass = $_POST['password'];
        
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE user = :user AND password = :pass");
        $stmt->bindParam(':user', $user);
        $stmt->bindParam(':pass', $pass);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            $_SESSION['user_id'] = $usuario['id_user'];
            $_SESSION['user_type'] = $usuario['type_user'];
            $_SESSION['username'] = $usuario['name_user'];
            
            echo json_encode(['success' => true, 'redirect' => 'dashboard.php']);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Usuario o contraseña incorrectos'
            ]);
        }
    }
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error de conexión: ' . $e->getMessage()
    ]);
}
?>