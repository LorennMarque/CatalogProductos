<?php
class AuthController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($email, $password) {
        try {
            // Buscar en tabla de Owners
            $query = "SELECT id, password FROM owners WHERE email = :email LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['email' => $email]);
            $owner = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($owner && password_verify($password, $owner['password'])) {
                $_SESSION['user_id'] = $owner['id'];
                $_SESSION['role'] = 'owner';
                header("Location: owner-home");
                exit;
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>
