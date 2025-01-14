<?php
$host = 'localhost';
$dbname = 'u482331776_repuestosmarcu';
$user = 'u482331776_repuestosmarcu';
$password = '2D:pbQq~a';

try {
    $conn = new mysqli($host, $user, $password, $dbname);
    if ($conn->connect_error) {
        die('Error de conexión: ' . $conn->connect_error);
    }
} catch (Exception $e) {
    die('Error de conexión: ' . $e->getMessage());
}
?> 