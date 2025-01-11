<?php
session_start();
require_once __DIR__ . '/config/db.php';

// Función para cargar la vista
function loadView($view) {
    $path = __DIR__ . "/views/$view.php"; // Ruta absoluta
    if (file_exists($path)) {
        require $path;
    } else {
        echo "<h1>404 - Página no encontrada</h1>";
    }
}

// Definir las rutas
$route = isset($_GET['page']) ? $_GET['page'] : 'index';

// Verificar si el usuario está logueado
$loggedIn = isset($_SESSION['role']);

// Rutas permitidas
switch ($route) {
    case 'index':
        loadView('front/index');
        break;

        case 'contacto':
            loadView('front/contact_form');
            break;

    case 'ingresar':
    case 'login':
        loadView('front/login');
        break;

    case 'projects':
        if ($loggedIn && $_SESSION['role'] === 'owner') {
            loadView('owner/projects');
        } else {
            header("Location: login");
            exit;
        }
        break;

    case 'owner-home':
        if ($loggedIn && $_SESSION['role'] === 'owner') {
            loadView('owner/inicio');
        } else {
            header("Location: login");
            exit;
        }
        break;

    case 'logout':
        session_destroy();
        header("Location: login");
        exit;

    case 'sitemap':
        require_once __DIR__ . '/helpers/sitemap.php';
        break;

    default:
        require __DIR__ . '/controllers/productController.php';

        // Check if route exists as a product slug
        $productController = new ProductController($conn);

        if ($product = $productController->viewBySlug($route)) {
            $_GET['slug'] = $route; // Set slug for product view
            loadView('front/products');
            break;
        }
        loadView('front/index');
        break;
}
?>
