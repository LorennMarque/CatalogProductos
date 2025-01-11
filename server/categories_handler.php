<?php
session_start();
include __DIR__ . '/../config/db.php';

$response = ['success' => false, 'message' => '', 'data' => null];

try {
    switch ($_POST['action']) {
        case 'create':
            $name = trim($_POST['name']);
            $slug = createSlug($name);
            
            $stmt = $conn->prepare("INSERT INTO categories (name, slug) VALUES (:name, :slug)");
            $stmt->execute(['name' => $name, 'slug' => $slug]);
            
            $response['success'] = true;
            $response['data'] = [
                'id' => $conn->lastInsertId(),
                'name' => $name,
                'slug' => $slug
            ];
            $response['message'] = 'Categoría creada exitosamente';
            break;
            
        case 'list':
            $stmt = $conn->query("SELECT * FROM categories ORDER BY name");
            $response['success'] = true;
            $response['data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
    }
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);

function createSlug($name) {
    $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $name);
    $slug = strtolower($slug);
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    return trim($slug, '-');
} 