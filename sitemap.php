<?php
ob_clean(); // Clear any previous output
header("Content-Type: application/xml; charset=UTF-8");
include 'db.php';

// Ensure no whitespace before XML declaration
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// Indexar la página principal
echo '  <url>' . "\n";
echo '    <loc>https://repuestosmarcus.com.ar/index.php</loc>' . "\n"; 
echo '    <priority>1.0</priority>' . "\n";
echo '  </url>' . "\n";

// Consulta para obtener los IDs de todos los productos
$result = $conn->query("SELECT id FROM products");

// Indexar cada product.php?id= existente
while($row = $result->fetch_assoc()) {
    echo '  <url>' . "\n";
    echo '    <loc>https://repuestosmarcus.com.ar/product.php?id=' . $row['id'] . '</loc>' . "\n";
    echo '    <priority>0.8</priority>' . "\n";
    echo '  </url>' . "\n";
}

// Fin del XML
echo '</urlset>';
?>
