<?php
// Prevenir cualquier output antes de la respuesta JSON
ob_start();

// Habilitar la visualización de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/productController.php';

$productController = new ProductController($conn);
$response = ['success' => false, 'message' => '', 'data' => null];

// Ensure the uploads directory exists
$uploadDir = __DIR__ . '/../public/uploads/products/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

try {
    if (!isset($_POST['action'])) {
        throw new Exception('No action specified');
    }

    switch ($_POST['action']) {
        case 'create':
        case 'update':
            if (!isset($_POST['title']) || !isset($_POST['description']) || !isset($_POST['price']) || !isset($_POST['categories'])) {
                throw new Exception('Missing required fields');
            }

            $data = [
                'title' => trim($_POST['title']),
                'description' => trim($_POST['description']),
                'price' => floatval($_POST['price']),
                'categories' => $_POST['categories'],
                'slug' => createSlug($_POST['title'])
            ];

            // Manejar imagen
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($_FILES['image']['type'], $allowedTypes)) {
                    throw new Exception('Invalid file type. Only JPG, PNG and GIF are allowed.');
                }

                $fileInfo = pathinfo($_FILES['image']['name']);
                $fileName = uniqid() . '.' . $fileInfo['extension'];
                $uploadPath = $uploadDir . $fileName;
                
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                    throw new Exception('Failed to upload image');
                }
                
                $data['image'] = 'public/uploads/products/' . $fileName;
                
                // Si es una actualización y existe una imagen anterior, eliminarla
                if ($_POST['action'] === 'update' && !empty($_POST['id'])) {
                    $oldProduct = $productController->view($_POST['id']);
                    if ($oldProduct && !empty($oldProduct['image'])) {
                        $oldImagePath = __DIR__ . '/../' . $oldProduct['image'];
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }
                }
            }

            if ($_POST['action'] === 'create') {
                $result = $productController->create($data);
            } else {
                if (!isset($_POST['id'])) {
                    throw new Exception('ID is required for update');
                }
                $result = $productController->update($_POST['id'], $data);
            }

            if ($result) {
                $response['success'] = true;
                $response['message'] = $_POST['action'] === 'create' ? 
                    'Producto creado exitosamente' : 
                    'Producto actualizado exitosamente';
            } else {
                throw new Exception('Failed to ' . $_POST['action'] . ' product');
            }
            break;

        case 'delete':
            if (!isset($_POST['id'])) {
                throw new Exception('ID is required for delete');
            }

            $product = $productController->view($_POST['id']);
            if (!$product) {
                throw new Exception('Product not found');
            }

            if ($productController->delete($_POST['id'])) {
                // Delete associated image if exists
                if (!empty($product['image'])) {
                    $imagePath = __DIR__ . '/../' . ltrim($product['image'], '/');
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
                $response['success'] = true;
                $response['message'] = 'Producto eliminado exitosamente';
            } else {
                throw new Exception('Failed to delete product');
            }
            break;

        case 'get':
            if (!isset($_POST['id'])) {
                throw new Exception('ID is required');
            }

            $product = $productController->view($_POST['id']);
            if (!$product) {
                throw new Exception('Product not found');
            }
            
            $response['success'] = true;
            $response['data'] = $product;
            break;

        case 'list':
            $products = $productController->index();
            if ($products === false) {
                throw new Exception('Error fetching products');
            }
            $response['success'] = true;
            $response['data'] = $products;
            break;

        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    error_log('Error in products_handler.php: ' . $e->getMessage());
}

// Asegurar headers correctos
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Limpiar cualquier output previo
while (ob_get_level()) {
    ob_end_clean();
}

// Enviar respuesta
echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;

function createSlug($title) {
    $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $title);
    $slug = strtolower($slug);
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    return trim($slug, '-');
}
