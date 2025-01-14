<?php
header("Content-Type: application/xml; charset=UTF-8");
include 'db.php';

// Inicio del XML
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

// Indexar la página principal
echo '<url>';
echo '<loc>https://https://repuestosmarcus.com.ar//index.php</loc>';
echo '<priority>1.0</priority>';
echo '</url>';

// Consulta para obtener los IDs de todos los productos
$stmt = $pdo->query("SELECT id FROM products");
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Indexar cada product.php?id= existente
foreach ($result as $row) {
    echo '<url>';
    echo '<loc>https://https://repuestosmarcus.com.ar//product.php?id=' . $row['id'] . '</loc>';
    echo '<priority>0.8</priority>';
    echo '</url>';
}

// Fin del XML
echo '</urlset>';
?>
