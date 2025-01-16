<?php
include 'config.php';
include 'db.php';

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$product_query = "SELECT p.*, c.name as category_name 
                  FROM products p 
                  LEFT JOIN product_categories pc ON p.id = pc.product_id
                  LEFT JOIN categories c ON pc.category_id = c.id
                  WHERE p.id = ?";
$stmt = $conn->prepare($product_query); // Changed to $conn
$stmt->bind_param("i", $product_id); // More efficient binding
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc(); //Simplified fetching

if (!$product) {
    header("Location: index.php");
    exit();
}

$images_query = "SELECT image FROM product_images WHERE product_id = ?";
$stmt = $conn->prepare($images_query); // Changed to $conn
$stmt->bind_param("i", $product_id); // More efficient binding
$stmt->execute();
$images_result = $stmt->get_result(); //Simplified fetching


$recommendations_query = "SELECT p.*, pi.image 
                          FROM products p 
                          LEFT JOIN product_images pi ON p.id = pi.product_id
                          WHERE p.id != ? 
                          ORDER BY RAND() 
                          LIMIT 5";
$stmt = $conn->prepare($recommendations_query); // Changed to $conn
$stmt->bind_param("i", $product_id); // More efficient binding
$stmt->execute();
$recommendations_result = $stmt->get_result(); //Simplified fetching

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - <?php echo COMPANY_NAME; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <meta name="description" content="<?php echo htmlspecialchars($product['description']); ?>">
    <link rel="icon" href="<?php echo SITE_LOGO; ?>" type="image/png">
    <meta property="og:title" content="<?php echo COMPANY_NAME; ?> - Tu tienda única para repuestos de automóviles">
    <meta property="og:description" content="Encuentra las piezas de automóvil perfectas para tu vehículo.">
    <meta property="og:image" content="<?php echo SITE_URL; ?>/portada.png">
    <meta property="og:url" content="<?php echo SITE_URL; ?>">
</head>
<body>
    <header class="d-flex align-items-center">
        <nav class="navbar navbar-expand-lg navbar-dark container">
            <a class="navbar-brand d-flex align-items-center text-light" href="index.php">
                <img src="<?php echo SITE_LOGO; ?>" alt="Logo" width="40" height="40" class="me-2">
                <b style="font-size:30px;"><?php echo COMPANY_NAME; ?></b>
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
                        <a class="nav-link text-light" href="#">Contacto</a>
                    </li>
                </ul>
            </div>
            <a href="<?php echo COMPANY_WHATSAPP_LINK; ?>" class="btn btn-danger d-none d-lg-block">Contáctanos</a>
        </nav>
    </header>

    <div class="container my-5">
<div class="row">
    <!-- Carrusel de imágenes -->
    <div class="col-md-6">
        <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php 
                $active = true;
                while ($image = $images_result->fetch_assoc()):
                ?>
                    <div class="carousel-item <?php echo $active ? 'active' : ''; ?>">
                        <div class="d-flex justify-content-center align-items-center" style="height: 400px; width: 400px; background-color: #f8f9fa;">
                            <img src="<?php echo htmlspecialchars($image['image']); ?>" class="d-block" style="max-width: 100%; max-height: 100%; object-fit: contain;" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </div>
                    </div>
                <?php 
                $active = false;
                endwhile; 
                ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Siguiente</span>
            </button>
        </div>
    </div>

    <!-- Detalles del producto -->
    <div class="col-md-6">
        <h1><?php echo htmlspecialchars($product['name']); ?></h1>
        <p class="lead">Precio: $<?php echo number_format($product['price'], 2); ?></p>
        <p>Categoría: <?php echo htmlspecialchars($product['category_name'] ?? 'Sin categoría'); ?></p>
        <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
        <a href="<?php echo COMPANY_WHATSAPP_LINK; ?>?text=<?php echo urlencode('Hola, estoy interesado en el producto: ' . $product['name']); ?>" class="btn btn-success btn-lg">
            <i class="fab fa-whatsapp"></i> Contactar por WhatsApp
        </a>
    </div>
</div>

        <!-- Productos recomendados -->
        <div class="row mt-5">
            <h4 class="mb-4">Productos Recomendados</h4>
            <?php while ($recommendation = $recommendations_result->fetch_assoc()): ?>
                <div class="col-md-4 col-lg-2 mb-4">
                    <div class="card border-0 shadow-lg h-100">
                        <img src="<?= htmlspecialchars($recommendation['image']) ?>" class="card-img-top" style="height:270px;width:100%;object-fit:cover;" alt="<?= htmlspecialchars($recommendation['name']) ?>">
                        <div class="card-body text-center">
                            <h5 class="card-title fw-bold"><?php echo htmlspecialchars($recommendation['name']); ?></h5>
                            <p class="card-text text-muted">$<?php echo number_format($recommendation['price'], 2); ?></p>
                            <a href="product.php?id=<?php echo htmlspecialchars($recommendation['id']); ?>" class="btn btn-outline-danger">Ver más</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

    </div>

    <a href="<?php echo COMPANY_WHATSAPP_LINK; ?>" class="whatsapp-float" target="_blank">
        <i class="fab fa-whatsapp my-float"></i>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
