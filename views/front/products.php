<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../controllers/postController.php';

$postController = new ProductController($conn);
$slug = $_GET['slug'] ?? '';
$post = $postController->viewBySlug($slug);

if (!$post) {
    echo "<h1>Post not found</h1>";
    return;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post->title); ?> | InventarioSimple Blog</title>
    <meta name="description" content="<?php echo htmlspecialchars($post->description); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($post->title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($post->description); ?>">
    <meta property="og:type" content="article">
    <meta property="article:published_time" content="<?php echo $post->created_at; ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/themes/prism.min.css">
    <script src="https://unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/prism.min.js"></script>
    <style>
        .prose img {
            max-width: 100%;
            height: auto;
            border-radius: 0.5rem;
        }
        .prose pre {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 0.5rem;
            overflow-x: auto;
        }
        .prose blockquote {
            border-left: 4px solid #818cf8;
            padding-left: 1rem;
            font-style: italic;
            color: #4b5563;
        }
        .prose h2 {
            margin-top: 2rem;
            margin-bottom: 1rem;
            font-size: 1.875rem;
            font-weight: 700;
            color: #111827;
        }
        .prose h3 {
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
            font-size: 1.5rem;
            font-weight: 600;
            color: #1f2937;
        }
        .prose p {
            margin-bottom: 1.25rem;
            line-height: 1.75;
        }
        .prose ul, .prose ol {
            margin-left: 2rem;
            margin-bottom: 1.25rem;
        }
        .prose li {
            margin-bottom: 0.5rem;
        }
        .prose a {
            color: #4f46e5;
            text-decoration: underline;
        }
        .prose a:hover {
            color: #4338ca;
        }
    </style>
</head>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-M7P458YLP2"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-M7P458YLP2');
</script>
<body class="font-sans antialiased bg-white">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center">
                    <a href="index" class="flex flex-col">
                        <div class="flex items-center space-x-3">
                            <i data-feather="bar-chart-2" class="w-6 h-6 md:w-8 md:h-8 text-indigo-600"></i>
                            <span class="text-lg md:text-2xl font-bold bg-gradient-to-r from-indigo-600 to-indigo-400 bg-clip-text text-transparent">InventarioSimple</span>
                        </div>
                        <span class="hidden md:block text-sm text-gray-700 mt-1">Sistema Online para Control de Ventas e Inventario</span>
                        <span class="block md:hidden text-sm text-gray-700 mt-1">Control de Ventas e Inventario Online</span>
                    </a>
                </div>
                <div class="flex items-center space-x-3 md:space-x-6">
                    <a href="login" class="text-gray-600 hover:text-indigo-600 transition duration-300 text-xs md:text-sm font-medium hidden md:block">
                        Iniciar Sesión
                    </a>
                    <a href="nuevo-usuario" class="bg-indigo-600 text-white px-3 md:px-6 py-2 md:py-3 rounded-lg hover:bg-indigo-700 transition duration-300 text-xs md:text-sm font-medium">
                        <span class="hidden md:inline">Iniciar Prueba de 7 días Gratis</span>
                        <span class="md:hidden">Probar Gratis</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-6 lg:px-8 py-12">
        <div class="flex flex-col lg:flex-row gap-12">
            <!-- Main Content -->
            <main class="lg:w-2/3">
                <article class="prose lg:prose-lg max-w-none">
                    <header class="mb-8">
                        <div class="flex items-center text-gray-500 text-sm">
                            <i data-feather="calendar" class="w-4 h-4 mr-2"></i>
                            <time datetime="<?php echo $post->created_at; ?>">
                                Publicado el <?php echo date('d/m/Y', strtotime($post->created_at)); ?>
                            </time>
                        </div>
                        <h1 class="text-4xl font-bold text-gray-900 mb-4"><?php echo htmlspecialchars($post->title); ?></h1>
                    </header>
                    <div class="text-gray-800 blog-content">
                        <?php echo $post->content; ?>
                    </div>
                </article>
            </main>

            <!-- Sticky Sidebar -->
            <aside class="lg:w-1/3">
                <div class="sticky top-24">
                    <div class="bg-white border border-indigo-100 rounded-xl p-8 shadow-lg">
                        <img src="public/images/lp/dashboard-preview.png" alt="Dashboard de InventarioSimple mostrando análisis de ventas" class="rounded-xl shadow-lg mb-8 w-full hover:opacity-90 transition-opacity">

                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Vende Más, Trabaja Menos</h2>
                        <p class="text-gray-600 mb-8 leading-relaxed">Registra tus ventas desde cualquier lugar, mantené tu stock al día y recibí alertas automáticas con nuestro sistema. Sin papeles, sin Excel, sin complicaciones.</p>

                        <div class="space-y-2">
                            <a href="index" class="block w-full bg-gradient-to-r from-indigo-600 to-indigo-500 text-white px-6 py-3 rounded-lg text-center hover:from-indigo-700 hover:to-indigo-600 transition duration-300 font-medium shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                Ver más información
                            </a>
                        </div>

                        <div class="mt-8 pt-2 border-t border-gray-100">
                            <div class="flex items-center space-x-3 text-gray-700 mb-4">
                                <i data-feather="check-circle" class="w-5 h-5 text-green-500"></i>
                                <span class="font-medium">Lleva el rendimiento de tu negocio en tu bolsillo</span>
                            </div>
                            <div class="flex items-center space-x-3 text-gray-700 mb-4">
                                <i data-feather="check-circle" class="w-5 h-5 text-green-500"></i>
                                <span class="font-medium">Probalo gratis por 7 días, sin compromiso</span>
                            </div>
                            <div class="flex items-center space-x-3 text-gray-700">
                                <i data-feather="check-circle" class="w-5 h-5 text-green-500"></i>
                                <span class="font-medium">Soporte 24/7 por WhatsApp</span>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-50 text-gray-600 py-8 mt-12 border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center text-sm">
                <p>&copy; <?php echo date('Y'); ?> InventarioSimple. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <script>
        feather.replace();
        // Highlight code blocks if any
        if (typeof Prism !== 'undefined') {
            Prism.highlightAll();
        }
    </script>
</body>
</html>
