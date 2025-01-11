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
    <meta name="description" content="Marcus Repuestos - Tu tienda online de confianza para repuestos automotrices de alta calidad. Amplio catálogo de autopartes con las mejores marcas.">
    <meta name="keywords" content="repuestos automotor, autopartes, repuestos de autos, Marcus Repuestos, repuestos originales, repuestos alternativos">
    <meta name="author" content="Marcus Repuestos">
    <meta name="robots" content="index, follow">
    <meta property="og:title" content="Marcus Repuestos | Tienda Online de Repuestos Automotor">
    <meta property="og:description" content="Tu tienda online de confianza para repuestos automotrices de alta calidad. Amplio catálogo de autopartes con las mejores marcas.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://marcusrepuestos.com">
    <meta property="og:image" content="https://placehold.co/400x400">
    <link rel="canonical" href="https://marcusrepuestos.com">
    <title>Marcus Repuestos | Tienda Online de Repuestos Automotor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
    <link rel="icon" type="image/x-icon" href="https://placehold.co/32x32">
    <style>
        .product-card {
            transition: transform 0.2s ease-in-out;
        }
        .product-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body class="bg-zinc-100">
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

    <!-- Hero Section -->
    <div class="relative h-[75vh]">
        <div class="absolute inset-0">
            <img src="https://placehold.co/1920x1080" alt="Hero background" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black bg-opacity-60"></div>
        </div>
        <div class="relative h-full flex items-center justify-center text-white">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold mb-4">MARCUS REPUESTOS</h1>
                <p class="text-lg md:text-xl mb-8">Tu socio confiable en repuestos automotrices de alta calidad</p>
                <a href="#productos" class="bg-[#1d8ade] hover:bg-[#1778c4] text-white px-8 py-3 rounded-full text-lg font-semibold transition">
                    Ver productos
                </a>
            </div>
        </div>
    </div>

    <!-- Categorías Destacadas -->
    <section id="categorias" class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center mb-12">Categorías Principales</h2>
            <div class="flex overflow-x-auto md:grid md:grid-cols-2 lg:grid-cols-4 gap-8 pb-4">
                <div class="group relative overflow-hidden rounded-lg shadow-lg min-w-[280px] md:min-w-0">
                    <img src="https://placehold.co/400x300" alt="Categoría Motor" class="w-full h-64 object-cover transform group-hover:scale-110 transition duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent opacity-80"></div>
                    <div class="absolute bottom-0 left-0 p-6">
                        <h3 class="text-xl font-bold text-white mb-2">Motor</h3>
                        <p class="text-gray-200">+500 productos</p>
                    </div>
                </div>

                <div class="group relative overflow-hidden rounded-lg shadow-lg min-w-[280px] md:min-w-0">
                    <img src="https://placehold.co/400x300" alt="Categoría Frenos" class="w-full h-64 object-cover transform group-hover:scale-110 transition duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent opacity-80"></div>
                    <div class="absolute bottom-0 left-0 p-6">
                        <h3 class="text-xl font-bold text-white mb-2">Frenos</h3>
                        <p class="text-gray-200">+300 productos</p>
                    </div>
                </div>

                <div class="group relative overflow-hidden rounded-lg shadow-lg min-w-[280px] md:min-w-0">
                    <img src="https://placehold.co/400x300" alt="Categoría Suspensión" class="w-full h-64 object-cover transform group-hover:scale-110 transition duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent opacity-80"></div>
                    <div class="absolute bottom-0 left-0 p-6">
                        <h3 class="text-xl font-bold text-white mb-2">Suspensión</h3>
                        <p class="text-gray-200">+200 productos</p>
                    </div>
                </div>

                <div class="group relative overflow-hidden rounded-lg shadow-lg min-w-[280px] md:min-w-0">
                    <img src="https://placehold.co/400x300" alt="Categoría Transmisión" class="w-full h-64 object-cover transform group-hover:scale-110 transition duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent opacity-80"></div>
                    <div class="absolute bottom-0 left-0 p-6">
                        <h3 class="text-xl font-bold text-white mb-2">Transmisión</h3>
                        <p class="text-gray-200">+150 productos</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Productos Destacados -->
    <section id="productos" class="py-20 bg-zinc-100">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center mb-12">Productos Destacados</h2>
            
            <!-- Filtros -->
            <div class="flex justify-center gap-4 mb-12">
                <button class="px-6 py-2 bg-[#1d8ade] text-white rounded-full hover:bg-[#1778c4] transition">
                    Todos
                </button>
                <button class="px-6 py-2 bg-zinc-800 text-white rounded-full hover:bg-zinc-900 transition">
                    Más Vendidos
                </button>
                <button class="px-6 py-2 bg-zinc-800 text-white rounded-full hover:bg-zinc-900 transition">
                    Ofertas
                </button>
            </div>

            <!-- Grid de Productos -->
            <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                <?php foreach ($products as $product): ?>
                <a href="<?php echo htmlspecialchars($product->slug); ?>" class="block group bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden hover:-translate-y-1">
                    <div class="relative">
                        <img src="https://placehold.co/400x400" 
                             alt="<?php echo htmlspecialchars($product->title); ?>"
                             class="w-full h-56 object-cover group-hover:scale-105 transition-transform duration-500">
                             
                        <?php if (!empty($product->category_names)): ?>
                        <span class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm text-blue-600 text-xs font-medium px-3 py-1.5 rounded-full">
                            <?php echo htmlspecialchars($product->category_names[0]); ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="p-6">
                        <h3 class="font-semibold text-lg text-gray-900 mb-2 truncate group-hover:text-blue-600 transition-colors">
                            <?php echo htmlspecialchars($product->title); ?>
                        </h3>
                        
                        <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                            <?php echo htmlspecialchars($product->description); ?>
                        </p>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-xl font-bold text-gray-900">
                                $<?php echo number_format($product->price, 2); ?>
                            </span>
                            <span class="bg-blue-50 group-hover:bg-blue-100 text-blue-600 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                Ver detalles
                            </span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Marcas -->
    <section id="marcas" class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center mb-12">Marcas Destacadas</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-8">
                <div class="flex items-center justify-center">
                    <img src="https://placehold.co/200x100" alt="Marca SKF" class="h-16 grayscale hover:grayscale-0 transition">
                </div>
                <div class="flex items-center justify-center">
                    <img src="https://placehold.co/200x100" alt="Marca Gates" class="h-16 grayscale hover:grayscale-0 transition">
                </div>
                <div class="flex items-center justify-center">
                    <img src="https://placehold.co/200x100" alt="Marca Bosch" class="h-16 grayscale hover:grayscale-0 transition">
                </div>
                <div class="flex items-center justify-center">
                    <img src="https://placehold.co/200x100" alt="Marca Mann Filter" class="h-16 grayscale hover:grayscale-0 transition">
                </div>
                <div class="flex items-center justify-center">
                    <img src="https://placehold.co/200x100" alt="Marca NGK" class="h-16 grayscale hover:grayscale-0 transition">
                </div>
                <div class="flex items-center justify-center">
                    <img src="https://placehold.co/200x100" alt="Marca Sachs" class="h-16 grayscale hover:grayscale-0 transition">
                </div>
            </div>
        </div>
    </section>

    <!-- Contacto -->
    <section class="relative py-20 overflow-hidden">
        <!-- Fondo con imagen y overlay -->
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1487754180451-c456f719a1fc?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1974&q=80" 
                 alt="Taller mecánico" 
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-zinc-900/80"></div>
        </div>

        <div class="container mx-auto px-4 relative">
            <div class="flex flex-col lg:flex-row items-center gap-12">
                <!-- Texto -->
                <div class="lg:w-1/2 text-white">
                    <h2 class="text-4xl font-bold mb-6">¿Necesitás ayuda para encontrar la pieza correcta?</h2>
                    <p class="text-gray-300 text-lg mb-8">Nuestro equipo de expertos está listo para ayudarte. Contactanos y te responderemos a la brevedad.</p>
                    
                    <div class="space-y-4">
                        <div class="flex items-center space-x-4">
                            <div class="bg-[#1d8ade]/20 p-3 rounded-full">
                                <i data-feather="phone" class="w-6 h-6 text-[#1d8ade]"></i>
                            </div>
                            <div>
                                <p class="text-gray-400">Llamanos al</p>
                                <p class="text-lg font-semibold">+54 9 11 1234-5678</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <div class="bg-[#1d8ade]/20 p-3 rounded-full">
                                <i data-feather="mail" class="w-6 h-6 text-[#1d8ade]"></i>
                            </div>
                            <div>
                                <p class="text-gray-400">Escribinos a</p>
                                <p class="text-lg font-semibold">info@marcus.com</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formulario -->
                <div class="lg:w-1/2 w-full px-4 lg:px-0">
                    <div class="bg-white rounded-2xl shadow-xl p-6 lg:p-8 lg:-mr-24">
                        <h3 class="text-xl lg:text-2xl font-bold mb-4 lg:mb-6">Envianos tu consulta</h3>
                        <form class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre completo</label>
                                <input type="text" name="name" placeholder="Ingresá tu nombre" class="w-full px-3 lg:px-4 py-2 text-sm lg:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1d8ade] focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" name="email" placeholder="ejemplo@email.com" class="w-full px-3 lg:px-4 py-2 text-sm lg:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1d8ade] focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                                <input type="tel" name="phone" placeholder="+54 9 11 1234-5678" class="w-full px-3 lg:px-4 py-2 text-sm lg:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1d8ade] focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mensaje</label>
                                <textarea name="message" rows="4" placeholder="¿En qué podemos ayudarte?" class="w-full px-3 lg:px-4 py-2 text-sm lg:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1d8ade] focus:border-transparent"></textarea>
                            </div>
                            <button type="submit" class="w-full bg-zinc-900 hover:bg-zinc-800 text-white font-medium py-2.5 lg:py-3 text-sm lg:text-base rounded-lg transition">
                                Enviar mensaje
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-zinc-900 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">MARCUS</h3>
                    <p class="text-gray-400">Tu tienda de confianza en repuestos automotrices desde 1990.</p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Enlaces Rápidos</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-[#1d8ade] transition">Inicio</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-[#1d8ade] transition">Productos</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-[#1d8ade] transition">Contacto</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contacto</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li class="flex items-center">
                            <i data-feather="map-pin" class="w-4 h-4 mr-2"></i>
                            Dirección del Local
                        </li>
                        <li class="flex items-center">
                            <i data-feather="phone" class="w-4 h-4 mr-2"></i>
                            +54 9 11 1234-5678
                        </li>
                        <li class="flex items-center">
                            <i data-feather="mail" class="w-4 h-4 mr-2"></i>
                            info@marcus.com
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <script>
        feather.replace();
    </script>
</body>
</html>
