<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../controllers/productController.php';

$productController = new ProductController($conn);
$products = $productController->index();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Productos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
</head>
<body class="bg-gray-50">
    <!-- Hero Carousel -->
    <div class="swiper-container h-[500px] mb-12">
        <div class="swiper-wrapper">
            <div class="swiper-slide relative">
                <img src="/assets/images/hero1.jpg" alt="Hero 1" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                    <div class="text-center text-white">
                        <h2 class="text-4xl font-bold mb-4">Productos de Calidad</h2>
                        <p class="text-xl">Descubre nuestra selección premium</p>
                    </div>
                </div>
            </div>
            <div class="swiper-slide relative">
                <img src="/assets/images/hero2.jpg" alt="Hero 2" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                    <div class="text-center text-white">
                        <h2 class="text-4xl font-bold mb-4">Ofertas Especiales</h2>
                        <p class="text-xl">Los mejores precios del mercado</p>
                    </div>
                </div>
            </div>
            <div class="swiper-slide relative">
                <img src="/assets/images/hero3.jpg" alt="Hero 3" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                    <div class="text-center text-white">
                        <h2 class="text-4xl font-bold mb-4">Envíos Seguros</h2>
                        <p class="text-xl">A todo el país</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="swiper-pagination"></div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold mb-2 text-center text-gray-800">Nuestros Productos</h1>
        <p class="text-center text-gray-600 mb-8">Encuentra lo que necesitas en nuestro catálogo</p>
        
        <!-- Filtros -->
        <div class="mb-8 flex justify-center gap-4">
            <button class="px-4 py-2 bg-blue-100 text-blue-800 rounded-full hover:bg-blue-200 transition">
                Todos
            </button>
            <button class="px-4 py-2 bg-gray-100 text-gray-800 rounded-full hover:bg-gray-200 transition">
                Más Vendidos
            </button>
            <button class="px-4 py-2 bg-gray-100 text-gray-800 rounded-full hover:bg-gray-200 transition">
                Ofertas
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            <?php foreach ($products as $product): ?>
                <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                    <?php if ($product['image']): ?>
                        <div class="relative h-64">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['title']); ?>"
                                 class="w-full h-full object-cover">
                        </div>
                    <?php endif; ?>
                    
                    <div class="p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-2">
                            <?php echo htmlspecialchars($product['title']); ?>
                        </h2>
                        
                        <p class="text-gray-600 mb-4 line-clamp-2">
                            <?php echo htmlspecialchars($product['description']); ?>
                        </p>
                        
                        <?php if (!empty($product['category_names'])): ?>
                            <div class="mb-4 flex flex-wrap gap-2">
                                <?php foreach ($product['category_names'] as $category): ?>
                                    <span class="inline-block bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full">
                                        <?php echo htmlspecialchars($category); ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-2xl font-bold text-green-600">
                                $<?php echo number_format($product['price'], 2); ?>
                            </span>
                            <a href="product.php?slug=<?php echo urlencode($product['slug']); ?>" 
                               class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">
                                Ver Más
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script>
        feather.replace();
        
        const swiper = new Swiper('.swiper-container', {
            loop: true,
            autoplay: {
                delay: 5000,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });
    </script>
</body>
</html>
