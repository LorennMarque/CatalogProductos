<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include 'db.php';

// Función para obtener todas las categorías
function getCategories($conn) {
    $result = $conn->query("SELECT * FROM categories ORDER BY name");
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Función para obtener o crear una categoría
function getOrCreateCategory($conn, $name) {
    $stmt = $conn->prepare("SELECT id FROM categories WHERE name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row['id'];
    } else {
        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        return $conn->insert_id;
    }
}

// Crear o actualizar producto
if (isset($_POST['submit'])) {
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $categories = isset($_POST['categories']) ? json_decode($_POST['categories']) : [];

    // Manejo de imágenes
    $target_dir = "uploads/";
    $imageFiles = [];
    
    // Crear el directorio si no existe
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['name'] as $key => $fileName) {
            if ($_FILES['images']['size'][$key] > 0) {
                $target_file = $target_dir . uniqid() . "_" . basename($fileName); // Nombre único
                
                // Verificar permisos de escritura
                if (!is_writable($target_dir)) {
                    chmod($target_dir, 0777);
                }
                
                if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $target_file)) {
                    $imageFiles[] = $target_file;
                } else {
                    // Manejar error de carga
                    $error = error_get_last();
                    error_log("Error al subir imagen: " . $error['message']);
                }
            }
        }
    }

    // Guardar el producto
    if ($id) {
        // Actualizar producto existente
        $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ? WHERE id = ?");
        $stmt->bind_param("ssdi", $name, $description, $price, $id);
        $stmt->execute();
        $product_id = $id;

        // Eliminar imágenes existentes solo si se suben nuevas imágenes
        if (!empty($imageFiles)) {
            $conn->query("DELETE FROM product_images WHERE product_id = $product_id");
            foreach ($imageFiles as $file) {
                $stmt = $conn->prepare("INSERT INTO product_images (product_id, image) VALUES (?, ?)");
                $stmt->bind_param("is", $product_id, $file);
                $stmt->execute();
            }
        }
    } else {
        // Crear nuevo producto
        $stmt = $conn->prepare("INSERT INTO products (name, description, price) VALUES (?, ?, ?)");
        $stmt->bind_param("ssd", $name, $description, $price);
        $stmt->execute();
        $product_id = $conn->insert_id;

        // Añadir imágenes si se suben nuevas imágenes
        foreach ($imageFiles as $file) {
            $stmt = $conn->prepare("INSERT INTO product_images (product_id, image) VALUES (?, ?)");
            $stmt->bind_param("is", $product_id, $file);
            $stmt->execute();
        }
    }

    // Eliminar categorías existentes y añadir las nuevas
    $conn->query("DELETE FROM product_categories WHERE product_id = $product_id");
    if (!empty($categories)) {
        foreach ($categories as $category) {
            $category_id = getOrCreateCategory($conn, $category->value);
            $conn->query("INSERT INTO product_categories (product_id, category_id) VALUES ($product_id, $category_id)");
        }
    }
}

// Eliminar producto
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM product_images WHERE product_id = $id");
    $conn->query("DELETE FROM product_categories WHERE product_id = $id");
    $conn->query("DELETE FROM products WHERE id = $id");
}

// Obtener todos los productos
$result = $conn->query("SELECT p.*, 
                        GROUP_CONCAT(DISTINCT c.name SEPARATOR ',') AS categories 
                        FROM products p 
                        LEFT JOIN product_categories pc ON p.id = pc.product_id 
                        LEFT JOIN categories c ON pc.category_id = c.id 
                        GROUP BY p.id");
$products = $result->fetch_all(MYSQLI_ASSOC);

// Obtener todas las categorías para autocompletado
$categories = getCategories($conn);
$category_names = array_column($categories, 'name');
$category_json = json_encode($category_names);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />
</head>
<body class="bg-gray-100 p-8">
    <div class="mb-4 text-right">
        <a href="login.php?logout" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Cerrar Sesión</a>
    </div>
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">Gestión de Productos</h1>

        <div class="flex space-x-8">
            <!-- Lista de Productos -->
            <div class="w-2/3 bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-semibold mb-4">Productos <span class="text-sm text-gray-600 mb-4">(<?php echo count($products); ?> Registrados)</span></h2> 
                
                <!-- Búsqueda y Filtros -->
                <div class="mb-4 flex space-x-4">
                    <input type="text" id="search" placeholder="Buscar productos..." class="p-2 border rounded flex-grow">
                    <select id="categoryFilter" class="p-2 border rounded">
                        <option value="">Todas las categorías</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['name']; ?>"><?php echo $category['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full" id="productsTable">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="p-2 text-left">Imágenes</th>
                                <th class="p-2 text-left">Nombre</th>
                                <th class="p-2 text-left">Descripción</th>
                                <th class="p-2 text-left">Categorías</th>
                                <th class="p-2 text-left">Precio</th>
                                <th class="p-2 text-left">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr class="border-b">
                                    <td class="p-2">
                                        <?php
                                        $image_result = $conn->query("SELECT image FROM product_images WHERE product_id = " . $product['id']);
                                        $images = $image_result->fetch_all(MYSQLI_ASSOC);
                                        foreach ($images as $img):
                                        ?>
                                            <img src="<?php echo $img['image']; ?>" alt="<?php echo $product['name']; ?>" class="w-20 h-20 object-cover mb-2">
                                        <?php endforeach; ?>
                                    </td>
                                    <td class="p-2"><?php echo $product['name']; ?></td>
                                    <td class="p-2"><?php echo $product['description']; ?></td>
                                    <td class="p-2"><?php echo $product['categories']; ?></td>
                                    <td class="p-2"><?php echo $product['price']; ?></td>
                                    <td class="p-2 flex space-x-2">
                                        <a href="#" onclick="editProduct(<?php echo htmlspecialchars(json_encode(['id' => $product['id'], 'name' => $product['name'], 'description' => $product['description'], 'price' => $product['price'], 'categories' => $product['categories']])); ?>)" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Editar</a>
                                        <a href="?delete=<?php echo $product['id']; ?>" onclick="return confirm('¿Estás seguro?')" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Formulario -->
            <div class="w-1/3 bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-semibold mb-4">Añadir/Editar Producto</h2>
                <form id="productForm" method="post" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="id" id="productId">
                    <input type="text" name="name" id="productName" placeholder="Nombre del Producto" required class="w-full p-2 border rounded">
                    <textarea name="description" id="productDescription" placeholder="Descripción del Producto" required class="w-full p-2 border rounded"></textarea>
                    <input type="number" name="price" id="productPrice" placeholder="Precio" step="0.01" required class="w-full p-2 border rounded">
                    
                    <!-- Imagenes -->
                    <div class="relative border-2 border-gray-300 border-dashed rounded-md p-6">
                        <input type="file" name="images[]" id="imageUpload" accept="image/*" multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <p class="mt-1 text-sm text-gray-600">
                                <span class="font-medium text-indigo-600 hover:text-indigo-500 focus:outline-none focus:underline transition duration-150 ease-in-out">Sube imágenes</span>
                                o arrastra y suelta
                            </p>
                            <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF hasta 10MB cada una</p>
                        </div>
                        <div id="imagePreview" class="mt-4"></div>
                    </div>

                    <input name="categories" id="productCategories" placeholder="Categorías (escribe o selecciona)" class="w-full p-2 border rounded">
                    <button type="submit" name="submit" class="w-100 bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600">
                        Guardar Producto
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
    <script>
        // Inicializar Tagify para las categorías
        var input = document.querySelector('input[name=categories]');
        var tagify = new Tagify(input, {
            whitelist: <?php echo $category_json; ?>,
            maxTags: 10,
            dropdown: {
                maxItems: 20,
                classname: "tags-look",
                enabled: 0,
                closeOnSelect: false
            }
        });

        // Función para editar un producto
        function editProduct(product) {
    document.getElementById('productId').value = product.id;
    document.getElementById('productName').value = product.name;
    document.getElementById('productDescription').value = product.description;
    document.getElementById('productPrice').value = product.price;
    tagify.removeAllTags();
    if (product.categories) {
        tagify.addTags(product.categories.split(','));
    }

    // Mostrar imágenes actuales
    let previewContainer = document.getElementById('imagePreview');
    previewContainer.innerHTML = '';
    
    // Obtener las imágenes del producto
    fetch(`get_product_images.php?id=${product.id}`)
        .then(response => response.json())
        .then(images => {
            images.forEach(img => {
                let imgContainer = document.createElement('div');
                imgContainer.className = 'relative inline-block mr-2 mb-2';

                let imgElement = document.createElement('img');
                imgElement.src = img.image;
                imgElement.alt = product.name;
                imgElement.className = 'w-20 h-20 object-cover';
                imgContainer.appendChild(imgElement);

                let deleteIcon = document.createElement('button');
                deleteIcon.innerHTML = '&#x2715;'; // Cruz (×)
                deleteIcon.className = 'absolute top-0 right-0 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center cursor-pointer';
                deleteIcon.onclick = function(e) {
                    e.preventDefault();
                    if (confirm('¿Estás seguro de que quieres eliminar esta imagen?')) {
                        deleteProductImage(product.id, img.id, imgContainer);
                    }
                };
                imgContainer.appendChild(deleteIcon);

                previewContainer.appendChild(imgContainer);
            });
        });
}

function deleteProductImage(productId, imageId, imgContainer) {
    fetch(`delete_product_image.php?product_id=${productId}&image_id=${imageId}`, {
        method: 'DELETE'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            imgContainer.remove();
        } else {
            alert('Error al eliminar la imagen');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al eliminar la imagen');
    });
}
        // Función para filtrar productos
        function filterProducts() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("search");
            filter = input.value.toUpperCase();
            table = document.getElementById("productsTable");
            tr = table.getElementsByTagName("tr");

            var categoryFilter = document.getElementById("categoryFilter").value.toUpperCase();

            for (i = 0; i < tr.length; i++) {
                var nameColumn = tr[i].getElementsByTagName("td")[1];
                var categoryColumn = tr[i].getElementsByTagName("td")[3];
                if (nameColumn && categoryColumn) {
                    var nameValue = nameColumn.textContent || nameColumn.innerText;
                    var categoryValue = categoryColumn.textContent || categoryColumn.innerText;
                    if (nameValue.toUpperCase().indexOf(filter) > -1 && 
                        (categoryFilter === "" || categoryValue.toUpperCase().indexOf(categoryFilter) > -1)) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }

        // Event listeners para búsqueda y filtro
        document.getElementById("search").addEventListener("keyup", filterProducts);
        document.getElementById("categoryFilter").addEventListener("change", filterProducts);

        // Previsualizar imágenes antes de subirlas
        document.getElementById('imageUpload').addEventListener('change', function(e) {
            let previewContainer = document.getElementById('imagePreview');
            previewContainer.innerHTML = '';
            Array.from(this.files).forEach(file => {
                if (file.size > 10485760) { // 10MB
                    alert('Por favor, selecciona imágenes menores de 10MB.');
                    this.value = '';
                    return;
                }
                let reader = new FileReader();
                reader.onload = function(e) {
                    let imgElement = document.createElement('img');
                    imgElement.src = e.target.result;
                    imgElement.className = 'w-20 h-20 object-cover mb-2';
                    previewContainer.appendChild(imgElement);
                };
                reader.readAsDataURL(file);
            });
        });
    </script>
</body>
</html>
