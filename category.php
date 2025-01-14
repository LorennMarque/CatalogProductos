<?php
include 'db.php';

$category_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$category_query = "SELECT * FROM categories WHERE id = :id";
$stmt = $pdo->prepare($category_query);
$stmt->bindParam(':id', $category_id, PDO::PARAM_INT);
$stmt->execute();
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    header('Location: index.php');
    exit;
}

$products_query = "SELECT p.*, pi.image as product_image 
                   FROM products p 
                   LEFT JOIN product_images pi ON p.id = pi.product_id 
                   LEFT JOIN product_categories pc ON p.id = pc.product_id
                   WHERE pc.category_id = :category_id
                   GROUP BY p.id";
$stmt = $pdo->prepare($products_query);
$stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
$stmt->execute();
$products_result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($category['name']) ?> - Repuestos Automotrices Marcus</title>
    <meta name="description" content="Encuentra los mejores productos de <?= htmlspecialchars($category['name']) ?> en Repuestos Automotrices Marcus.">
    <link rel="icon" href="roundedLogo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <metaproperty="og:title"content="Venta de Repuestos - Tu tienda única para repuestos de automóviles">
    <metaproperty="og:description"content="Encuentra las piezas de automóvil perfectas para tu vehículo.">
    <metaproperty="og:image"content="https://encendidoelrayo.com.ar/portada.png">
    <metaproperty="og:url"content="https://encendidoelrayo.com.ar">
</head>
<body>
<header class="d-flex align-items-center">
    <nav class="navbar navbar-expand-lg navbar-dark container">
        <a class="navbar-brand d-flex align-items-center text-light" href="index.php">
            <img src="roundedLogo.png" alt="Logo" width="40" height="40" class="me-2">
            <b style="font-size:30px;">Repuestos Automotrices Marcus</b>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <ul class="navbar-nav text-center">
                <li class="nav-item">
                    <a class="nav-link active text-light" aria-current="page" href="#">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-light" href="#">Servicios</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-light" href="#">Nosotros</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-light" href="https://wa.me/+541169651171">Contacto</a>
                </li>
            </ul>
        </div>
        <a href="https://wa.me/+541169651171" class="btn btn-danger d-none d-lg-block">Contáctanos</a>
    </nav>
</header>

<div class="container my-5">

            <h4 class="mb-4">Productos en <?= htmlspecialchars($category['name']) ?></h4>
            <div class="row">
                <?php if (empty($products_result)): ?>
                    <div class="col-12">
                        <div class="alert alert-warning" role="alert">
                            No hay productos disponibles en esta categoría.
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($products_result as $product): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card border-0 shadow-lg h-100">
                                <img src="<?= htmlspecialchars($product['product_image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                                <div class="card-body text-center">
                                    <h5 class="card-title fw-bold"><?= htmlspecialchars($product['name']) ?></h5>
                                    <p class="card-text text-muted">$<?= htmlspecialchars($product['price']) ?></p>
                                    <a href="product.php?id=<?= htmlspecialchars($product['id']) ?>" class="btn btn-outline-danger">Ver más</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

<a href="https://wa.me/+541169651171" class="whatsapp-float" target="_blank">
    <i class="fab fa-whatsapp my-float"></i>
</a>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$pdo = null; // Cerrar la conexión a la base de datos
?>
