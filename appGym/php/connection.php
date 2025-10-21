<?php
$host = "byhrxwbsgw3qn1pix9ky-mysql.services.clever-cloud.com";
$dbname = "byhrxwbsgw3qn1pix9ky";
$username = "utfeg78xjtoqdlac";
$password = "rmpr8nEU1yWB9UgJJxlp";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET NAMES utf8");
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>