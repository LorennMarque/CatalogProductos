<?php
// Verificar si la sesión ya está iniciada
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
    <title>Panel de Control - AndesSoftware</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .drawer-open {
            overflow: hidden;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-lg shadow-lg p-6 md:p-8">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Gestión de Productos</h1>
                <div class="mb-6 flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                    <div class="relative flex-1 max-w-md">
                        <input type="text" 
                            id="searchInput" 
                            placeholder="Buscar productos..." 
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            onkeyup="filterProducts()">
                        <i data-feather="search" class="absolute left-3 top-2.5 h-5 w-5 text-gray-400"></i>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="showProductDrawer()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg inline-flex items-center">
                            <i data-feather="plus" class="w-5 h-5 mr-2"></i>
                    Nuevo Producto
                </button>
                    </div>
                </div>
            </div>

            <!-- Products Table -->
            <div id="productsTable" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Imagen</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="productsTableBody">
                        <!-- Products will be loaded here -->
                    </tbody>
                </table>
                <!-- Empty state -->
                <div id="emptyState" class="hidden text-center py-12">
                    <i data-feather="package" class="w-16 h-16 mx-auto text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900">No hay productos</h3>
                    <p class="text-gray-500 mt-2">Comienza agregando tu primer producto</p>
                    <button onclick="showProductDrawer()" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                        Agregar Producto
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Drawer Overlay -->
    <div id="drawerOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 transition-opacity duration-300 opacity-0 pointer-events-none"></div>

    <!-- Product Drawer -->
    <div id="productDrawer" class="fixed inset-y-0 right-0 w-full md:w-[480px] bg-white shadow-xl transform translate-x-full transition-transform duration-300 ease-in-out z-50">
        <div class="h-full flex flex-col">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800" id="drawerTitle">Nuevo Producto</h2>
                <button onclick="closeProductDrawer()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="flex-1 overflow-y-auto p-6">
                <form id="productForm" onsubmit="handleProductSubmit(event)" class="space-y-6">
                    <input type="hidden" id="productId" name="id">
                    
                    <!-- Título -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Título del Producto <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="title" name="title" required 
                            placeholder="Ej: Camisa de Algodón"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-sm text-gray-500">Un nombre claro y descriptivo para tu producto</p>
                    </div>
                    
                    <!-- Descripción -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                        <textarea id="description" name="description" rows="4" 
                            placeholder="Describe las características del producto..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        <p class="mt-1 text-sm text-gray-500">Incluye detalles importantes como materiales, dimensiones, etc.</p>
                    </div>
                    
                    <!-- Precio -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Precio <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">$</span>
                        <input type="number" id="price" name="price" step="0.01" required 
                                placeholder="0.00"
                                class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Precio en dólares americanos (USD)</p>
                    </div>
                    
                    <!-- Categorías -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Categorías</label>
                        <div class="flex flex-wrap gap-2" id="selectedCategories">
                            <!-- Las categorías seleccionadas se mostrarán aquí -->
                        </div>
                        <div class="relative mt-1">
                            <input type="text" 
                                id="categoryInput" 
                                placeholder="Escribe para agregar una categoría..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <div id="categoryDropdown" class="hidden absolute z-10 w-full mt-1 bg-white rounded-lg shadow-lg border border-gray-200 max-h-48 overflow-y-auto">
                                <!-- Las sugerencias de categorías aparecerán aquí -->
                            </div>
                        </div>
                        <input type="hidden" id="categories" name="categories" value="">
                        <p class="mt-1 text-sm text-gray-500">Presiona Enter para agregar una nueva categoría</p>
                    </div>
                    
                    <!-- Imagen -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Imagen del Producto</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-500 transition-colors cursor-pointer"
                             onclick="document.getElementById('image').click()">
                            <div class="space-y-1 text-center">
                                <i data-feather="image" class="mx-auto h-12 w-12 text-gray-400"></i>
                                <div class="flex text-sm text-gray-600">
                                    <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                        <span>Subir imagen</span>
                                        <input id="image" name="image" type="file" class="sr-only" accept="image/*">
                                    </label>
                                    <p class="pl-1">o arrastra y suelta</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF hasta 10MB</p>
                            </div>
                        </div>
                        <div id="imagePreview" class="mt-2 hidden">
                            <div class="relative inline-block">
                                <img src="" alt="Preview" class="h-32 w-32 object-cover rounded-lg">
                                <button type="button" onclick="removeImage()" 
                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600">
                                    <i data-feather="x" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeProductDrawer()" 
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" form="productForm"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    let currentProducts = [];

    function loadProducts() {
        fetch('/CatalogProductos/server/products_handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=list'
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    console.error('Error response:', text);
                    throw new Error('Network response was not ok');
                });
            }
            return response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Error parsing JSON:', text);
                    throw new Error('Invalid JSON response');
                }
            });
        })
        .then(data => {
            console.log('Respuesta exitosa:', data);
            if (data.success) {
                currentProducts = data.data || [];
                renderProducts();
            } else {
                console.error('Error al cargar productos:', data.message);
                showNotification('Error al cargar productos: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error en la petición:', error);
            showNotification('Error al cargar productos: ' + error.message, 'error');
        });
    }

    function renderProducts(productsToRender = currentProducts) {
        const productsContainer = document.getElementById('productsTableBody');
        const emptyState = document.getElementById('emptyState');
        
        if (!productsToRender || productsToRender.length === 0) {
            productsContainer.innerHTML = '';
            emptyState.classList.remove('hidden');
            return;
        }

        emptyState.classList.add('hidden');
        productsContainer.innerHTML = '';
        
        productsToRender.forEach(product => {
            const productImage = product.image ? product.image.replace(/^\//, '') : null;
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="text-sm font-medium text-gray-900">${product.title || 'Sin título'}</div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm text-gray-900">${product.description || '-'}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">$${parseFloat(product.price || 0).toFixed(2)}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    ${productImage ? 
                        `<img src="${productImage}" alt="${product.title}" class="h-12 w-12 object-cover rounded">` :
                        `<div class="h-12 w-12 rounded bg-gray-100 flex items-center justify-center">
                            <i data-feather="image" class="w-6 h-6 text-gray-400"></i>
                         </div>`
                    }
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <button onclick="showProductDrawer(${product.id})" class="text-indigo-600 hover:text-indigo-900 mr-3">
                        <i data-feather="edit-2" class="w-5 h-5"></i>
                    </button>
                    <button onclick="deleteProduct(${product.id})" class="text-red-600 hover:text-red-900">
                        <i data-feather="trash-2" class="w-5 h-5"></i>
                    </button>
                </td>
            `;
            productsContainer.appendChild(row);
        });
        
        feather.replace();
    }

    function showProductDrawer(id = null) {
        const drawer = document.getElementById('productDrawer');
        const overlay = document.getElementById('drawerOverlay');
        const form = document.getElementById('productForm');
        const drawerTitle = document.getElementById('drawerTitle');
        const imagePreview = document.getElementById('imagePreview');
        
        form.reset();
        imagePreview.classList.add('hidden');
        drawerTitle.textContent = id ? 'Editar Producto' : 'Nuevo Producto';
        
        if (id) {
            const product = currentProducts.find(p => p.id === id);
            if (product) {
                document.getElementById('productId').value = product.id;
                document.getElementById('title').value = product.title;
                document.getElementById('description').value = product.description;
                document.getElementById('price').value = product.price;
                
                // Mostrar imagen existente
                if (product.image) {
                    imagePreview.querySelector('img').src = product.image;
                    imagePreview.classList.remove('hidden');
                }
                
                // Cargar categorías del producto
                currentCategories = new Set(product.category_ids || []);
                renderSelectedCategories();
            }
        } else {
            currentCategories = new Set();
            renderSelectedCategories();
        }
        
        // Show overlay first
        overlay.classList.remove('pointer-events-none');
        overlay.classList.add('opacity-100');
        
        // Then show drawer
        drawer.classList.remove('translate-x-full');
        document.body.classList.add('drawer-open');
        
        // Add click event to overlay
        overlay.onclick = closeProductDrawer;
    }

    function closeProductDrawer() {
        const drawer = document.getElementById('productDrawer');
        const overlay = document.getElementById('drawerOverlay');
        
        // Hide drawer first
        drawer.classList.add('translate-x-full');
        
        // Then hide overlay with a slight delay
        setTimeout(() => {
            overlay.classList.add('pointer-events-none');
            overlay.classList.remove('opacity-100');
            document.body.classList.remove('drawer-open');
        }, 300);
    }

    function handleProductSubmit(event) {
        event.preventDefault();
        
        // Validaciones
        const title = document.getElementById('title').value.trim();
        const price = document.getElementById('price').value;
        
        if (!title) {
            showNotification('El título es requerido', 'error');
            return;
        }
        
        if (!price || price <= 0) {
            showNotification('El precio debe ser mayor a 0', 'error');
            return;
        }

        // Obtener el botón de submit
        const submitButton = document.querySelector('button[type="submit"][form="productForm"]');
        if (!submitButton) {
            console.error('No se encontró el botón de submit');
            return;
        }

        const originalText = submitButton.innerHTML;
        submitButton.disabled = true;
        submitButton.innerHTML = `
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Guardando...
        `;

        const formData = new FormData(event.target);
        const id = formData.get('id');
        formData.append('action', id ? 'update' : 'create');

        fetch('server/products_handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeProductDrawer();
                loadProducts();
                showNotification(data.message, 'success');
            } else {
                showNotification(data.message || 'Error al guardar el producto', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al guardar el producto', 'error');
        })
        .finally(() => {
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            }
        });
    }

    function deleteProduct(id) {
        if (confirm('¿Estás seguro de que deseas eliminar este producto?')) {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', id);

            fetch('server/products_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadProducts();
                }
                alert(data.message);
            });
        }
    }

    // Initialize Feather icons
    document.addEventListener('DOMContentLoaded', () => {
        feather.replace();
        loadProducts();
        loadCategories();
    });

    // Add image preview functionality
    document.getElementById('image').addEventListener('change', function(e) {
        const preview = document.getElementById('imagePreview');
        const file = e.target.files[0];
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.querySelector('img').src = e.target.result;
                preview.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        } else {
            preview.classList.add('hidden');
        }
    });

    // Función para filtrar productos
    function filterProducts() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const filteredProducts = currentProducts.filter(product => 
            product.title.toLowerCase().includes(searchTerm) || 
            product.description.toLowerCase().includes(searchTerm)
        );
        renderProducts(filteredProducts);
    }

    // Función para remover imagen
    function removeImage() {
        const preview = document.getElementById('imagePreview');
        const input = document.getElementById('image');
        preview.classList.add('hidden');
        input.value = '';
    }

    // Mejorar el manejo de arrastrar y soltar imágenes
    const dropZone = document.querySelector('div[onclick="document.getElementById(\'image\').click()"]');

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-blue-500', 'bg-blue-50');
    });

    dropZone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-blue-500', 'bg-blue-50');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-blue-500', 'bg-blue-50');
        
        const files = e.dataTransfer.files;
        if (files.length > 0 && files[0].type.startsWith('image/')) {
            document.getElementById('image').files = files;
            const event = new Event('change');
            document.getElementById('image').dispatchEvent(event);
        }
    });

    // Función para mostrar notificaciones
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        } text-white transform transition-all duration-300 translate-y-full`;
        
        notification.innerHTML = message;
        document.body.appendChild(notification);
        
        setTimeout(() => notification.classList.remove('translate-y-full'), 100);
        setTimeout(() => {
            notification.classList.add('translate-y-full');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Variables globales para categorías
    let currentCategories = new Set();
    let allCategories = [];

    // Cargar categorías al iniciar
    function loadCategories() {
        fetch('server/categories_handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=list'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                allCategories = data.data;
            }
        });
    }

    // Manejar input de categorías
    document.getElementById('categoryInput').addEventListener('input', function(e) {
        const input = e.target.value.toLowerCase();
        const dropdown = document.getElementById('categoryDropdown');
        
        if (input.length < 1) {
            dropdown.classList.add('hidden');
            return;
        }

        const suggestions = allCategories.filter(cat => 
            cat.name.toLowerCase().includes(input) && 
            !currentCategories.has(cat.id)
        );

        if (suggestions.length > 0) {
            dropdown.innerHTML = suggestions.map(cat => `
                <div class="px-4 py-2 hover:bg-gray-100 cursor-pointer" 
                     onclick="addCategory(${cat.id}, '${cat.name}')">
                    ${cat.name}
                </div>
            `).join('');
            dropdown.classList.remove('hidden');
        } else {
            dropdown.innerHTML = `
                <div class="px-4 py-2 text-gray-500">
                    Presiona Enter para crear "${input}"
                </div>
            `;
            dropdown.classList.remove('hidden');
        }
    });

    // Manejar creación de nuevas categorías
    document.getElementById('categoryInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const input = e.target.value.trim();
            if (input) {
                createCategory(input);
            }
        }
    });

    // Crear nueva categoría
    function createCategory(name) {
        const formData = new FormData();
        formData.append('action', 'create');
        formData.append('name', name);

        fetch('server/categories_handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                allCategories.push(data.data);
                addCategory(data.data.id, data.data.name);
            }
        });
    }

    // Agregar categoría seleccionada
    function addCategory(id, name) {
        if (!currentCategories.has(id)) {
            currentCategories.add(id);
            const tag = document.createElement('div');
            tag.className = 'inline-flex items-center bg-blue-100 text-blue-800 rounded-full px-3 py-1 text-sm';
            tag.innerHTML = `
                ${name}
                <button type="button" onclick="removeCategory(${id})" class="ml-2 focus:outline-none">
                    <i data-feather="x" class="w-4 h-4"></i>
                </button>
            `;
            document.getElementById('selectedCategories').appendChild(tag);
            document.getElementById('categoryInput').value = '';
            document.getElementById('categoryDropdown').classList.add('hidden');
            document.getElementById('categories').value = Array.from(currentCategories).join(',');
            feather.replace();
        }
    }

    // Remover categoría
    function removeCategory(id) {
        currentCategories.delete(id);
        renderSelectedCategories();
    }

    // Renderizar categorías seleccionadas
    function renderSelectedCategories() {
        const container = document.getElementById('selectedCategories');
        container.innerHTML = '';
        
        Array.from(currentCategories).forEach(id => {
            const category = allCategories.find(c => c.id === id);
            if (category) {
                addCategory(category.id, category.name);
            }
        });
        
        document.getElementById('categories').value = Array.from(currentCategories).join(',');
    }
    </script>
</body>
</html>
