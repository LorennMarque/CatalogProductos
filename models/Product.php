<?php

class Product {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
        $query = "INSERT INTO products (title, description, slug, image, price) 
                  VALUES (:title, :description, :slug, :image, :price)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':slug', $data['slug']);
        $stmt->bindParam(':image', $data['image']);
        $stmt->bindParam(':price', $data['price']);

        if($stmt->execute()) {
            $productId = $this->conn->lastInsertId();
            
            // Handle categories if provided
            if (!empty($data['categories'])) {
                foreach ($data['categories'] as $categoryId) {
                    $query = "INSERT INTO product_categories (product_id, category_id) VALUES (:product_id, :category_id)";
                    $stmt = $this->conn->prepare($query);
                    $stmt->bindParam(':product_id', $productId);
                    $stmt->bindParam(':category_id', $categoryId);
                    $stmt->execute();
                }
            }
            
            return $productId;
        }
        return false;
    }

    public function read($id) {
        $query = "SELECT p.*, GROUP_CONCAT(c.id) as category_ids, GROUP_CONCAT(c.name) as category_names 
                  FROM products p 
                  LEFT JOIN product_categories pc ON p.id = pc.product_id
                  LEFT JOIN categories c ON pc.category_id = c.id
                  WHERE p.id = :id
                  GROUP BY p.id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $result['category_ids'] = $result['category_ids'] ? explode(',', $result['category_ids']) : [];
            $result['category_names'] = $result['category_names'] ? explode(',', $result['category_names']) : [];
        }
        return $result;
    }

    public function update($id, $data) {
        $fields = [];
        $values = [];

        foreach($data as $key => $value) {
            if($key !== 'id' && $key !== 'categories') {
                $fields[] = "$key = :$key";
                $values[":$key"] = $value;
            }
        }

        $query = "UPDATE products SET " . implode(', ', $fields) . " WHERE id = :id";
        $values[':id'] = $id;

        $stmt = $this->conn->prepare($query);
        $success = $stmt->execute($values);

        // Handle categories update if provided
        if ($success && isset($data['categories'])) {
            // Remove existing categories
            $query = "DELETE FROM product_categories WHERE product_id = :product_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':product_id', $id);
            $stmt->execute();

            // Add new categories
            foreach ($data['categories'] as $categoryId) {
                $query = "INSERT INTO product_categories (product_id, category_id) VALUES (:product_id, :category_id)";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':product_id', $id);
                $stmt->bindParam(':category_id', $categoryId);
                $stmt->execute();
            }
        }

        return $success;
    }

    public function delete($id) {
        // First delete related category associations
        $query = "DELETE FROM product_categories WHERE product_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Then delete the product
        $query = "DELETE FROM products WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function getBySlug($slug) {
        $query = "SELECT p.*, GROUP_CONCAT(c.id) as category_ids, GROUP_CONCAT(c.name) as category_names 
                  FROM products p 
                  LEFT JOIN product_categories pc ON p.id = pc.product_id
                  LEFT JOIN categories c ON pc.category_id = c.id
                  WHERE p.slug = :slug
                  GROUP BY p.id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':slug', $slug);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $result['category_ids'] = $result['category_ids'] ? explode(',', $result['category_ids']) : [];
            $result['category_names'] = $result['category_names'] ? explode(',', $result['category_names']) : [];
        }
        return $result;
    }
}
