<?php
session_start();
require_once '../php/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    try {
        $stmt = $conexion->prepare("SELECT id_user, name_user, lastname_user, user, pass, phone_user 
                                  FROM usuarios 
                                  WHERE user = :usuario");
        $stmt->execute(['usuario' => $usuario]);
        $user = $stmt->fetch();

        // Debug information
        if ($user) {
            if ($password == $user['pass']) {
                $_SESSION['id_user'] = $user['id_user'];
                $_SESSION['name_user'] = $user['name_user'];
                $_SESSION['lastname_user'] = $user['lastname_user'];
                $_SESSION['user'] = $user['user'];
                $_SESSION['phone_user'] = $user['phone_user'];
                echo "success";
                exit;
            }
        }
        echo "error";
        exit;
        
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
}
?>