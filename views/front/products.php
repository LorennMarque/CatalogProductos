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
    <meta name="description" content="Marcus Repuestos - Catálogo completo de repuestos automotrices de alta calidad">
    <title>Productos | Marcus Repuestos</title>
    <script src="https://unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .product-card {
            transition: all 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .category-item {
            transition: all 0.2s ease;
        }
        .category-item:hover {
            background-color: #f3f4f6;
            padding-left: 1rem;
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-zinc-900 text-white fixed w-full z-50">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-8">
                    <a href="inicio" class="text-2xl font-bold">MARCUS</a>
                    <div class="hidden md:flex space-x-4">
                        <a href="productos" class="hover:text-[#1d8ade] transition">Productos</a>    
                        <a href="#contacto" class="hover:text-[#1d8ade] transition">Contacto</a>
                    </div>
                </div>
                
                <!-- Search Bar -->
                <div class="flex-1 max-w-xl px-8">
                    <div class="relative" x-data="{ searchTerm: '' }">
                        <input type="text" 
                               x-model="searchTerm"
                               placeholder="Buscar productos..." 
                               class="w-full px-4 py-2 rounded-lg bg-zinc-800 border-none text-white placeholder-gray-400 focus:ring-2 focus:ring-[#1d8ade]">
                        <i data-feather="search" class="absolute right-3 top-2.5 text-gray-400 w-5 h-5"></i>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <button class="p-2 hover:text-[#1d8ade]">
                        <i data-feather="shopping-cart"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-4 pt-24">
        <!-- Breadcrumbs -->
        <div class="mb-8 flex items-center text-sm text-gray-600">
            <a href="inicio" class="hover:text-[#1d8ade]">Inicio</a>
            <i data-feather="chevron-right" class="w-4 h-4 mx-2"></i>
            <span class="text-[#1d8ade]">Productos</span>
        </div>

        <div class="flex flex-col md:flex-row gap-8">
            <!-- Sidebar with Categories -->
            <aside class="md:w-1/4">
                <div class="bg-white p-6 rounded-xl shadow-sm sticky top-24">
                    <h2 class="text-xl font-bold mb-6 flex items-center">
                        <i data-feather="list" class="w-5 h-5 mr-2 text-[#1d8ade]"></i>
                        Categorías
                    </h2>
                    <ul class="space-y-2">
                        <?php
                        $categories = [
                            ['icon' => 'box', 'name' => 'Todos los productos'],
                            ['icon' => 'engine', 'name' => 'Motor'],
                            ['icon' => 'tool', 'name' => 'Suspensión'],
                            ['icon' => 'disc', 'name' => 'Frenos'],
                            ['icon' => 'filter', 'name' => 'Filtros'],
                            ['icon' => 'droplet', 'name' => 'Aceites'],
                            ['icon' => 'tag', 'name' => 'Ofertas']
                        ];
                        foreach ($categories as $category): ?>
                            <li>
                                <a href="#" class="category-item flex items-center p-2 rounded-lg text-gray-600 hover:text-[#1d8ade]">
                                    <i data-feather="<?php echo $category['icon']; ?>" class="w-4 h-4 mr-3"></i>
                                    <span><?php echo $category['name']; ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <!-- Price Filter -->
                    <div class="mt-8 pt-6 border-t border-gray-100">
                        <h3 class="font-semibold mb-4">Filtrar por precio</h3>
                        <div class="space-y-2">
                            <input type="range" min="0" max="100000" class="w-full accent-[#1d8ade]">
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>$0</span>
                                <span>$100,000</span>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Product Grid -->
            <main class="md:w-3/4">
                <!-- Sort Options -->
                <div class="flex justify-between items-center mb-6">
                    <p class="text-gray-600">Mostrando <?php echo count($products); ?> productos</p>
                    <select class="bg-white border border-gray-200 rounded-lg px-4 py-2 focus:ring-[#1d8ade] focus:border-[#1d8ade]">
                        <option>Más relevantes</option>
                        <option>Menor precio</option>
                        <option>Mayor precio</option>
                        <option>Más nuevos</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($products as $product): ?>
                    <div class="product-card bg-white rounded-xl overflow-hidden">
                        <div class="relative group">
                            <img src="<?php echo htmlspecialchars($product->image); ?>" 
                                 alt="<?php echo htmlspecialchars($product->title); ?>"
                                 class="w-full h-48 object-cover">
                            <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <button class="bg-white text-gray-900 px-4 py-2 rounded-lg transform translate-y-4 group-hover:translate-y-0 transition-transform">
                                    Ver detalles
                                </button>
                            </div>
                            <?php if (!empty($product->category_names)): ?>
                            <div class="absolute top-2 right-2">
                                <span class="bg-[#1d8ade] text-white text-xs px-3 py-1 rounded-full">
                                    <?php echo htmlspecialchars($product->category_names[0]); ?>
                                </span>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-2 hover:text-[#1d8ade] transition">
                                <a href="#"><?php echo htmlspecialchars($product->title); ?></a>
                            </h3>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                <?php echo htmlspecialchars($product->description); ?>
                            </p>
                            <div class="flex justify-between items-center">
                                <span class="text-2xl font-bold text-[#1d8ade]">
                                    $<?php echo number_format($product->price, 2); ?>
                                </span>
                                <button class="bg-zinc-900 hover:bg-[#1d8ade] text-white px-6 py-2 rounded-full transition flex items-center space-x-2">
                                    <i data-feather="shopping-cart" class="w-4 h-4"></i>
                                    <span>Agregar</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <div class="mt-12 flex justify-center">
                    <nav class="flex space-x-2">
                        <a href="#" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-[#1d8ade] hover:text-white transition">Anterior</a>
                        <a href="#" class="px-4 py-2 border border-gray-300 rounded-lg bg-[#1d8ade] text-white">1</a>
                        <a href="#" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-[#1d8ade] hover:text-white transition">2</a>
                        <a href="#" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-[#1d8ade] hover:text-white transition">3</a>
                        <a href="#" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-[#1d8ade] hover:text-white transition">Siguiente</a>
                    </nav>
                </div>
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-zinc-900 text-white mt-20 py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">MARCUS</h3>
                    <p class="text-gray-400">Tu tienda de confianza en repuestos automotrices desde 1990.</p>
                    <div class="flex space-x-4 mt-4">
                        <a href="#" class="text-gray-400 hover:text-[#1d8ade]"><i data-feather="facebook"></i></a>
                        <a href="#" class="text-gray-400 hover:text-[#1d8ade]"><i data-feather="instagram"></i></a>
                        <a href="#" class="text-gray-400 hover:text-[#1d8ade]"><i data-feather="twitter"></i></a>
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Enlaces Rápidos</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-[#1d8ade] transition flex items-center space-x-2">
                            <i data-feather="chevron-right" class="w-4 h-4"></i>
                            <span>Inicio</span>
                        </a></li>
                        <li><a href="#" class="text-gray-400 hover:text-[#1d8ade] transition flex items-center space-x-2">
                            <i data-feather="chevron-right" class="w-4 h-4"></i>
                            <span>Productos</span>
                        </a></li>
                        <li><a href="#" class="text-gray-400 hover:text-[#1d8ade] transition flex items-center space-x-2">
                            <i data-feather="chevron-right" class="w-4 h-4"></i>
                            <span>Sobre Nosotros</span>
                        </a></li>
                        <li><a href="#" class="text-gray-400 hover:text-[#1d8ade] transition flex items-center space-x-2">
                            <i data-feather="chevron-right" class="w-4 h-4"></i>
                            <span>Contacto</span>
                        </a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Horarios de Atención</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li>Lunes a Viernes: 9:00 - 18:00</li>
                        <li>Sábados: 9:00 - 13:00</li>
                        <li>Domingos: Cerrado</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contacto</h4>
                    <ul class="space-y-4 text-gray-400">
                        <li class="flex items-center space-x-3">
                            <i data-feather="map-pin" class="w-5 h-5 text-[#1d8ade]"></i>
                            <span>Av. Siempreviva 742, Springfield</span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <i data-feather="phone" class="w-5 h-5 text-[#1d8ade]"></i>
                            <span>+54 9 11 1234-5678</span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <i data-feather="mail" class="w-5 h-5 text-[#1d8ade]"></i>
                            <span>info@marcus.com</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400">
                <p>&copy; 2024 Marcus Repuestos. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('productsPage', () => ({
            activeCategory: 'all',
            searchTerm: '',
            
            setActiveCategory(category) {
                this.activeCategory = category;
            }
        }));
    });

    feather.replace();
    </script>
</body>
</html>
