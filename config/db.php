<?php
$host = 'localhost';
// $dbname = 'u541681057_AndesSoftware';
// $user = 'u541681057_AndesSoftware';
// $password = '5?*:J>VPj';

$dbname = 'catalogoproductos';
$user = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

?>


