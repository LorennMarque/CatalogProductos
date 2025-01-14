<?php
$host = 'localhost';
$dbname = 'u482331776_repuestosmarcu';
$user = 'u482331776_repuestosmarcu';
$password = '2D:pbQq~a';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Error de conexión: ' . $e->getMessage());
}
?> 