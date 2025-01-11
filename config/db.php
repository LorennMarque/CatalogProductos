<?php
$host = 'localhost';
$dbname = 'u482331776_repuestosmarcu';
$user = 'u482331776_repuestosmarcu';
$password = '2D:pbQq~a';

// $dbname = 'catalogoproductos';
// $user = 'root';
// $password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

?>


