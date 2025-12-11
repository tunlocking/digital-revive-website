<?php
// Edit Product
require_once '../../config/db.php';

$product_id = $_GET['id'] ?? null;

if ($product_id) {
    // Fetch product from database
    // $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    // $stmt->execute([$product_id]);
    // $product = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    
    // Update in database
    // $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, category = ?, description = ? WHERE id = ?");
    // $stmt->execute([$name, $price, $category, $description, $product_id]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Digital Revive</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Edit Product</h2>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Product Name</label>
                <input type="text" class="form-control" name="name" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Price (MAD)</label>
                <input type="number" class="form-control" name="price" step="0.01" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Category</label>
                <select class="form-control" name="category" required>
                    <option>PC Accessories</option>
                    <option>Smartphones</option>
                    <option>Digital Products</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control" name="description" rows="4"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update Product</button>
            <a href="products.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
