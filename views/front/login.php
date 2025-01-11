<?php
// Verificar si la sesión ya está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include __DIR__ . '/../../config/db.php';
include __DIR__ . '/../../controllers/authController.php';

// Iniciar el controlador de autenticación
$auth = new AuthController($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $auth->login($email, $password);
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>Panel de Administración - Andes Software</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
</head>
<body class="min-h-screen bg-gray-50">
    <div class="flex flex-col md:flex-row min-h-screen">
        <!-- Columna izquierda - Información de marca -->
        <div class="hidden lg:flex lg:flex-col lg:w-2/5 bg-gradient-to-br from-blue-800 to-blue-600 text-white p-12">
            <div class="max-w-md mx-auto my-auto">
                <div class="mb-12">
                    <h1 class="text-4xl font-bold mb-4">Andes Software</h1>
                    <p class="text-xl text-blue-100">Panel de Administración</p>
                </div>
                
                <div class="space-y-8">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-700/30 rounded-xl flex items-center justify-center">
                                <i data-feather="package" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold mb-2">Gestión de Productos</h3>
                            <p class="text-blue-100">Administra tu catálogo de productos fácilmente</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-700/30 rounded-xl flex items-center justify-center">
                                <i data-feather="edit" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold mb-2">Actualización Simple</h3>
                            <p class="text-blue-100">Modifica y actualiza contenido al instante</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-700/30 rounded-xl flex items-center justify-center">
                                <i data-feather="bar-chart-2" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold mb-2">Análisis y Estadísticas</h3>
                            <p class="text-blue-100">Monitorea el rendimiento de tu catálogo</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna derecha - Login -->
        <div class="w-full md:w-3/5 bg-white p-6 md:p-12 flex flex-col justify-center min-h-screen md:min-h-0 animate__animated animate__fadeIn" x-data="{ showPassword: false }">
            <div class="mb-8 p-4">
                <h2 class="text-3xl font-bold text-gray-800">Acceso al Panel</h2>
                <p class="text-gray-600 mt-2">Ingresa tus credenciales para administrar tu catálogo</p>
            </div>

            <form method="POST" class="p-4" action="index.php?page=ingresar">
                <div class="mb-6">
                    <label for="email" class="block text-gray-700 font-semibold mb-2">Correo electrónico</label>
                    <input type="email" name="email" id="email" class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="admin@ejemplo.com" required>
                </div>

                <div class="mb-6 relative">
                    <label for="password" class="block text-gray-700 font-semibold mb-2">Contraseña</label>
                    <input :type="showPassword ? 'text' : 'password'" name="password" id="password" class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Ingresa tu contraseña" required>
                    <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-11 text-gray-500 focus:outline-none">
                        <i data-feather="eye" x-show="!showPassword"></i>
                        <i data-feather="eye-off" x-show="showPassword"></i>
                    </button>
                </div>

                <div class="space-y-4">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg p-4 transition duration-300">
                        Iniciar Sesión
                    </button>
                </div>
            </form>

            <div class="text-center mt-6">
                <a href="mailto:lorenn@andessoftware.com" class="text-blue-600 text-sm hover:text-blue-700 hover:underline font-medium">¿Problemas para acceder? Contacta a soporte</a>
            </div>
        </div>
    </div>
    <script>
        feather.replace();
    </script>
</body>
</html>
