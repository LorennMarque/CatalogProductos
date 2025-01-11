<?php
require_once __DIR__ . '/../models/Product.php';

class ProductController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }
    // Get all products
    public function index() {
        try {
            $query = "SELECT p.*, 
                      GROUP_CONCAT(DISTINCT c.id) as category_ids,
                      GROUP_CONCAT(DISTINCT c.name) as category_names
                      FROM products p 
                      LEFT JOIN product_categories pc ON p.id = pc.product_id
                      LEFT JOIN categories c ON pc.category_id = c.id
                      GROUP BY p.id 
                      ORDER BY p.id DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            $products = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $product = new stdClass();
                $product->id = $row['id'];
                $product->title = $row['title'];
                $product->description = $row['description'];
                $product->slug = $row['slug'];
                $product->image = $row['image'];
                $product->price = $row['price'];
                $product->category_ids = $row['category_ids'] ? 
                    array_map('intval', explode(',', $row['category_ids'])) : [];
                $product->category_names = $row['category_names'] ? 
                    explode(',', $row['category_names']) : [];
                
                $products[] = $product;
            }
            
            return $products;
        } catch (PDOException $e) {
            error_log('Error en ProductController::index: ' . $e->getMessage());
            return [];
        }
    }

    // Get single product by ID
    public function view($id) {
        try {
            $query = "SELECT p.*, 
                      GROUP_CONCAT(DISTINCT c.id) as category_ids,
                      GROUP_CONCAT(DISTINCT c.name) as category_names
                      FROM products p 
                      LEFT JOIN product_categories pc ON p.id = pc.product_id
                      LEFT JOIN categories c ON pc.category_id = c.id
                      WHERE p.id = :id
                      GROUP BY p.id";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute(['id' => $id]);
            
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($product) {
                $product['category_ids'] = $product['category_ids'] ? 
                    array_map('intval', explode(',', $product['category_ids'])) : [];
                $product['category_names'] = $product['category_names'] ? 
                    explode(',', $product['category_names']) : [];
                    
                // Asegurar que la ruta de la imagen sea absoluta
                if (!empty($product['image'])) {
                    $product['image'] = '/' . $product['image'];
                }
            }
            
            return $product;
        } catch (PDOException $e) {
            error_log('Error en ProductController::view: ' . $e->getMessage());
            return null;
        }
    }

    // Get single product by slug
    public function viewBySlug($slug) {
        $product = new Product($this->db);
        return $product->getBySlug($slug);
    }

    // Create new product
    public function create($data) {
        try {
            $this->db->beginTransaction();
            
            // Insertar el producto
            $query = "INSERT INTO products (title, description, slug, image, price) 
                      VALUES (:title, :description, :slug, :image, :price)";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                'title' => $data['title'],
                'description' => $data['description'],
                'slug' => $data['slug'],
                'image' => $data['image'] ?? null,
                'price' => $data['price']
            ]);
            
            $productId = $this->db->lastInsertId();
            
            // Insertar categorías si existen
            if (!empty($data['categories'])) {
                $categories = explode(',', $data['categories']);
                foreach ($categories as $categoryId) {
                    $stmt = $this->db->prepare("INSERT INTO product_categories (product_id, category_id) VALUES (:product_id, :category_id)");
                    $stmt->execute([
                        'product_id' => $productId,
                        'category_id' => $categoryId
                    ]);
                }
            }
            
            $this->db->commit();
            return $productId;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log('Error en ProductController::create: ' . $e->getMessage());
            return false;
        }
    }

    // Update existing product
    public function update($id, $data) {
        try {
            $this->db->beginTransaction();
            
            // Actualizar el producto
            $fields = [];
            $values = ['id' => $id];
            
            foreach (['title', 'description', 'slug', 'price'] as $field) {
                if (isset($data[$field])) {
                    $fields[] = "$field = :$field";
                    $values[$field] = $data[$field];
                }
            }
            
            // Manejar la imagen solo si se proporciona una nueva
            if (isset($data['image'])) {
                $fields[] = "image = :image";
                $values['image'] = $data['image'];
            }
            
            if (!empty($fields)) {
                $query = "UPDATE products SET " . implode(', ', $fields) . " WHERE id = :id";
                $stmt = $this->db->prepare($query);
                $stmt->execute($values);
            }
            
            // Actualizar categorías
            $stmt = $this->db->prepare("DELETE FROM product_categories WHERE product_id = :product_id");
            $stmt->execute(['product_id' => $id]);
            
            if (!empty($data['categories'])) {
                $categories = explode(',', $data['categories']);
                foreach ($categories as $categoryId) {
                    $stmt = $this->db->prepare("INSERT INTO product_categories (product_id, category_id) VALUES (:product_id, :category_id)");
                    $stmt->execute([
                        'product_id' => $id,
                        'category_id' => $categoryId
                    ]);
                }
            }
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log('Error en ProductController::update: ' . $e->getMessage());
            return false;
        }
    }

    // Delete product
    public function delete($id) {
        $product = new Product($this->db);
        return $product->delete($id);
    }

    // Helper function to create URL-friendly slug
    private function createSlug($title) {
        // Convert to lowercase and transliterate accented characters
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $title);
        $slug = strtolower($slug);
        // Replace non-alphanumeric characters with hyphens
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        // Remove multiple consecutive hyphens
        $slug = preg_replace('/-+/', '-', $slug);
        // Trim hyphens from start and end
        $slug = trim($slug, '-');
        return $slug;
    }
}
?>
