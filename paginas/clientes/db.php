<?php
// db.php
 $host = 'b55zqeouapy9mfsjlhmo-mysql.services.clever-cloud.com';
 $dbname = 'b55zqeouapy9mfsjlhmo';
 $user = 'ufr3pgsf8dqwh08c';
 $pass = '3Pf1C9iOSrGSDdYOk5yo';
 $charset = 'utf8mb4';

 $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
 $options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>