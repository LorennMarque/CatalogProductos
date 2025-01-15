<?php
session_start();
include 'db.php';

// Proceso de login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();


    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: product_manage.php');
        exit();
    } else {
        $error = 'Credenciales inválidas';
    }
}

// Proceso de logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Repuestos Automotrices Marcus</title>
    <link rel="icon" href="roundedLogo.png" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-800 to-gray-900 flex items-center justify-center min-h-screen">
    <div class="bg-white/10 backdrop-blur-lg p-8 rounded-xl shadow-2xl w-96 border border-white/20">
        <div class="flex justify-center mb-8">
            <img src="roundedLogo.png" alt="Logo" class="w-24 h-24">
        </div>
        <h2 class="text-3xl font-bold mb-6 text-center text-white">Iniciar Sesión</h2>
        <?php if (isset($error)): ?>
            <div class="bg-red-500/20 border border-red-500/50 text-red-100 px-4 py-3 rounded-lg relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $error; ?></span>
            </div>
        <?php endif; ?>
        <form method="POST" action="" class="space-y-6">
            <div>
                <label for="username" class="block text-gray-300 text-sm font-medium mb-2">Usuario</label>
                <input type="text" id="username" name="username" required
                       class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200">
            </div>
            <div>
                <label for="password" class="block text-gray-300 text-sm font-medium mb-2">Contraseña</label>
                <input type="password" id="password" name="password" required
                       class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200">
            </div>
            <div>
                <button type="submit" name="login"
                        class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-800 transition duration-200 transform hover:scale-[1.02]">
                    Iniciar Sesión
                </button>
            </div>
        </form>
    </div>
</body>
</html>