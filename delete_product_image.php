<?php
// delete_product_image.php
header('Content-Type: application/json');

session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$conn = new mysqli("localhost", "u541681057_repuestos_db", "6Kx&XZE=", "u541681057_repuestos_db");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión']);
    exit;
}

$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
$image_id = isset($_GET['image_id']) ? intval($_GET['image_id']) : 0;

if ($product_id && $image_id) {
    // Obtener la ruta de la imagen
    $stmt = $conn->prepare("SELECT image FROM product_images WHERE id = ? AND product_id = ?");
    $stmt->bind_param("ii", $image_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($image = $result->fetch_assoc()) {
        // Eliminar el archivo
        if (file_exists($image['image'])) {
            unlink($image['image']);
        }
        
        // Eliminar el registro de la base de datos
        $stmt = $conn->prepare("DELETE FROM product_images WHERE id = ? AND product_id = ?");
        $stmt->bind_param("ii", $image_id, $product_id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar la imagen de la base de datos']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Imagen no encontrada']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Parámetros inválidos']);
}