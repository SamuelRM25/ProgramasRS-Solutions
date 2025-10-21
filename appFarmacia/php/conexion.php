<?php
$host = "bwdsdlrptblgyhzv0auq-mysql.services.clever-cloud.com";
$user = "u4tttpy2rjdmhe4x";
$password = "mzqQCxyf2ANQCvbCs0kv";
$database = "bwdsdlrptblgyhzv0auq";
$port = "3306";

try {
    $conexion = new PDO("mysql:host=$host;port=$port;dbname=$database", $user, $password);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    die();
}
?>
