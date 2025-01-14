<?php
include_once 'config/Database.php';
include_once 'models/Category.php';
include_once 'models/Product.php';

$database = new Database();
$db = $database->getConnection();

$category = new Category($db);
$product = new Product($db);

$category_stmt = $category->readAll();
$product_stmt = $product->readAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Repuestos Automotrices Marcus</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h1 class="mt-5">Products</h1>
    <div class="row">
        <div class="col-md-3">
            <h3>Categories</h3>
            <ul class="list-group">
                <?php while ($row = $category_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <li class="list-group-item"><?= $row['name'] ?></li>
                <?php endwhile; ?>
            </ul>
        </div>
        <div class="col-md-9">
            <h3>Products</h3>
            <div class="row">
                <?php while ($row = $product_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <img class="card-img-top" src="uploads/<?= $row['image'] ?>" alt="<?= $row['name'] ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= $row['name'] ?></h5>
                                <p class="card-text"><?= $row['description'] ?></p>
                                <p class="card-text"><small class="text-muted"><?= $row['category_name'] ?></small></p>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
