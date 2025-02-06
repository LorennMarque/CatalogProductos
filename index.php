<?php
include 'config.php';
include 'db.php';

// Obtener categorías
$stmt = $conn->query("SELECT * FROM categories");
$categories = $stmt->fetch_all(MYSQLI_ASSOC);

// Obtener productos
$stmt = $conn->query("SELECT p.*, pi.image FROM products p LEFT JOIN product_images pi ON p.id = pi.product_id GROUP BY p.id");
$products = $stmt->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Repuestos Automotrices Marcus - Venta de Repuestos y Autopartes en Argentina</title>
    <meta name="description" content="La mejor tienda de repuestos automotrices en Argentina. Amplio catálogo de autopartes originales y alternativos, frenos, suspensión, motor y más. Envíos a todo el país y asesoramiento profesional. Precios competitivos y stock permanente.">
    <link rel="icon" href="<?php echo SITE_LOGO; ?>" type="image/png">
    <link rel="canonical" href="<?php echo SITE_URL; ?>" />

    <meta name="keywords" content="repuestos automotrices, autopartes, repuestos originales, repuestos alternativos, frenos, suspensión, motor, repuestos de autos, venta de repuestos, Marcus autopartes, repuestos Argentina">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo COMPANY_NAME; ?> - Líder en Venta de Autopartes">
    <meta property="og:description" content="Encuentra todos los repuestos que necesitas para tu vehículo. Calidad garantizada y los mejores precios del mercado.">
    <meta property="og:image" content="<?php echo SITE_URL; ?>/portada.png">
    <meta property="og:url" content="<?php echo SITE_URL; ?>">
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo COMPANY_NAME; ?> - Líder en Venta de Autopartes">
    <meta name="twitter:description" content="Encuentra todos los repuestos que necesitas para tu vehículo. Calidad garantizada y los mejores precios del mercado.">
    <meta name="twitter:image" content="<?php echo SITE_URL; ?>/portada.png">

    <style>
        .whatsapp-float {
            position: fixed;
            width: 60px;
            height: 60px;
            bottom: 40px;
            right: 40px;
            background-color: #25d366;
            color: #FFF;
            border-radius: 50px;
            text-align: center;
            font-size: 30px;
            box-shadow: 2px 2px 3px #999;
            z-index: 100;
        }

        .whatsapp-float:hover {
            text-decoration: none;
            color: #FFF;
            background-color: #1ab152;
        }

        .my-float {
            margin-top: 16px;
        }

        .social-links {
            position: fixed;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 100;
        }

        .social-links a {
            display: block;
            width: 45px;
            height: 45px;
            background: #333;
            color: white;
            text-align: center;
            line-height: 45px;
            border-radius: 50%;
            margin: 10px 0;
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            transform: scale(1.1);
        }

        .social-links .facebook { background: #3b5998; }
        .social-links .instagram { background: #e4405f; }
    </style>
</head>
<body>
<div class="social-links">
    <a href="https://www.instagram.com/repuestosmarcus30/" class="facebook" target="_blank"><i class="fab fa-facebook-f"></i></a>
    <a href="https://www.facebook.com/profile.php?id=61572431342743" class="instagram" target="_blank"><i class="fab fa-instagram"></i></a>
</div>

<header class="d-flex align-items-center">
    <nav class="navbar navbar-expand-lg navbar-dark container">
        <a class="navbar-brand d-flex align-items-center text-light" href="#">
            <img src="<?php echo SITE_LOGO; ?>" alt="Logo <?php echo COMPANY_NAME; ?>" width="40" height="40" class="me-2">
            <span class="navbar-brand-text"><?php echo COMPANY_NAME; ?></span>
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
                    <a class="nav-link text-light" href="#products">Productos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-light" href="#location">Ubicación</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-light" href="<?php echo COMPANY_WHATSAPP_LINK; ?>">Contacto</a>
                </li>
            </ul>
        </div>
        <a href="<?php echo COMPANY_WHATSAPP_LINK; ?>" class="btn btn-danger d-none d-lg-block">Contáctanos</a>
    </nav>
</header>

<div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="b1.png" class="d-block w-100" alt="Repuestos automotrices de calidad" style="height: 60vh; object-fit: cover;">
      <div class="carousel-caption d-flex flex-column justify-content-center align-items-center" style="height: 50vh;">
        <h1 class="display-3">Repuestos Automotrices de Calidad</h1>
        <p class="lead">Las mejores marcas y garantía asegurada.</p>
      </div>
    </div>
    <div class="carousel-item">
      <img src="b2.png" class="d-block w-100" alt="Autopartes y repuestos originales" style="height: 60vh; object-fit: cover;">
      <div class="carousel-caption d-flex flex-column justify-content-center align-items-center" style="height: 50vh;">
        <h1 class="display-3">Autopartes Originales y Alternativas</h1>
        <p class="lead">Amplio stock y precios competitivos.</p>
      </div>
    </div>
    <div class="carousel-item">
      <img src="b3.png" class="d-block w-100" alt="Repuestos para todas las marcas" style="height: 60vh; object-fit: cover;">
      <div class="carousel-caption d-flex flex-column justify-content-center align-items-center" style="height: 50vh;">
        <h1 class="display-3">Repuestos para Todas las Marcas</h1>
        <p class="lead">Asesoramiento experto y envíos a todo el país.</p>
      </div>
    </div>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>

<section id="products" class="container my-5">
    <!-- Barra de búsqueda -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="input-group">
                <input type="text" id="searchInput" class="form-control form-control-lg" placeholder="Buscar productos..." aria-label="Buscar productos">
                <button class="btn btn-danger btn-lg" type="button" onclick="searchProducts()">Buscar</button>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Categorías a la izquierda -->
        <div class="col-lg-3">
            <h4 class="mb-4">Categorías</h4>
            <div class="list-group list-group-flush">
                <?php foreach ($categories as $category): ?>
                    <a href="category.php?id=<?= htmlspecialchars($category['id']) ?>" class="list-group-item list-group-item-action bg-dark text-light rounded mb-2"><?= htmlspecialchars($category['name']) ?></a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Productos a la derecha -->
        <div class="col-lg-9">
            <h4 class="mb-4">Productos</h4>
            <div class="row" id="productsContainer">
                <?php foreach ($products as $product): ?>
                    <div class="col-md-6 col-lg-4 mb-4 product-card" data-name="<?= htmlspecialchars(strtolower($product['name'])) ?>">
                        <div class="card border-0 shadow-lg h-100">
                            <img src="<?= htmlspecialchars($product['image']) ?>" class="card-img-top" style="height:270px;width:100%;object-fit:cover;" alt="<?= htmlspecialchars($product['name']) ?>">
                            <div class="card-body text-center">
                                <h5 class="card-title fw-bold"><?= htmlspecialchars($product['name']) ?></h5>
                                <p class="card-text text-muted">$<?= htmlspecialchars($product['price']) ?></p>
                                <a href="product.php?id=<?= htmlspecialchars($product['id']) ?>" class="btn btn-outline-danger">Ver más</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<section id="location">
    <div class="container text-white p-5">
        <h2 class="mb-4">Podes Visitarnos</h2>
        <p><strong>Dirección:</strong> <?php echo COMPANY_ADDRESS; ?></p>
        <div class="embed-responsive embed-responsive-16by9">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3284.1459919934646!2d-58.451003187748064!3d-34.60046965721553!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x95bcca091f58710d%3A0x20a8f3be25064f44!2sDr.%20Luis%20Bel%C3%A1ustegui%20286%2C%20C1414DZB%20Cdad.%20Aut%C3%B3noma%20de%20Buenos%20Aires%2C%20Argentina!5e0!3m2!1ses!2scl!4v1736892846163!5m2!1ses!2scl" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
</section>

<a href="<?php echo COMPANY_WHATSAPP_LINK; ?>" class="whatsapp-float" target="_blank">
    <i class="fab fa-whatsapp my-float"></i>
</a>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script>
function searchProducts() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const products = document.getElementsByClassName('product-card');
    
    Array.from(products).forEach(product => {
        const productName = product.dataset.name;
        if (productName.includes(searchTerm)) {
            product.style.display = 'block';
        } else {
            product.style.display = 'none';
        }
    });
}

// Búsqueda en tiempo real
document.getElementById('searchInput').addEventListener('keyup', searchProducts);
</script>

</body>
</html>
