<?php
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
    switch ($_POST['action']) {
        case 'create':
        case 'update':
            $data = [
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'price' => $_POST['price'],
                'categories' => $_POST['categories'],
                'slug' => createSlug($_POST['title'])
            ];

            // Manejar imagen
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $fileInfo = pathinfo($_FILES['image']['name']);
                $fileName = uniqid() . '.' . $fileInfo['extension'];
                $uploadPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
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
            }

            if ($_POST['action'] === 'create') {
                $result = $productController->create($data);
            } else {
                $result = $productController->update($_POST['id'], $data);
            }

            if ($result) {
                $response['success'] = true;
                $response['message'] = $_POST['action'] === 'create' ? 
                    'Producto creado exitosamente' : 
                    'Producto actualizado exitosamente';
            }
            break;

        case 'delete':
            $product = $productController->view($_POST['id']);
            if ($product && $productController->delete($_POST['id'])) {
                // Delete associated image if exists
                if (!empty($product['image']) && file_exists(__DIR__ . '/../' . $product['image'])) {
                    unlink(__DIR__ . '/../' . $product['image']);
                }
                $response['success'] = true;
                $response['message'] = 'Producto eliminado exitosamente';
            }
            break;

        case 'get':
            $product = $productController->view($_POST['id']);
            if ($product) {
                $response['success'] = true;
                $response['data'] = $product;
            }
            break;

        case 'list':
            $products = $productController->index();
            // Debug
            error_log('Productos recuperados: ' . print_r($products, true));
            
            // Agregar URL base a las imágenes y asegurar que todos los campos necesarios existen
            foreach ($products as &$product) {
                $product = array_merge([
                    'id' => '',
                    'title' => '',
                    'description' => '',
                    'price' => 0,
                    'image' => null
                ], $product);
                
                if (!empty($product['image'])) {
                    $product['image'] = '/' . $product['image'];
                }
            }
            
            $response['success'] = true;
            $response['data'] = $products;
            break;
    }
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);

function createSlug($title) {
    $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $title);
    $slug = strtolower($slug);
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    return trim($slug, '-');
}
