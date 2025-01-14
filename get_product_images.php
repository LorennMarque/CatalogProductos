<?php
// get_product_images.php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "u541681057_repuestos_db", "6Kx&XZE=", "u541681057_repuestos_db");
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conn->prepare("SELECT id, image FROM product_images WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$images = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($images);