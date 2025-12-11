<?php
session_start();
require_once '../../config/db.php';

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$error = '';
$product = null;
$categories = [];
$product_id = intval($_GET['id'] ?? 0);

if (!$product_id) {
    header('Location: products.php');
    exit();
}

try {
    // Get product
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    
    if (!$product) {
        header('Location: products.php');
        exit();
    }
    
    // Get categories
    $stmt = $conn->query("SELECT id, name FROM categories ORDER BY name");
    $categories = $stmt->fetchAll();
    
} catch(PDOException $e) {
    $error = 'Error loading product: ' . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $category_id = intval($_POST['category_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $stock_quantity = intval($_POST['stock_quantity'] ?? 0);
    $status = trim($_POST['status'] ?? 'active');
    
    // Validation
    if (empty($name) || $price <= 0 || $category_id <= 0) {
        $error = 'Please fill in all required fields with valid values.';
    } else {
        try {
            $image_path = $product['image_path'];
            
            // Handle file upload
            if (!empty($_FILES['image']['name'])) {
                $file = $_FILES['image'];
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $filename = basename($file['name']);
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                
                if (!in_array($ext, $allowed)) {
                    throw new Exception('Invalid file type. Only JPG, PNG, GIF, WEBP allowed.');
                }
                
                if ($file['size'] > 5000000) {
                    throw new Exception('File too large. Max 5MB allowed.');
                }
                
                $upload_dir = '../../uploads/products/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $new_filename = time() . '_' . preg_replace('/[^a-z0-9_-]/i', '_', $filename);
                if (move_uploaded_file($file['tmp_name'], $upload_dir . $new_filename)) {
                    // Delete old image
                    if ($product['image_path'] && file_exists('../../' . $product['image_path'])) {
                        unlink('../../' . $product['image_path']);
                    }
                    $image_path = 'uploads/products/' . $new_filename;
                }
            }
            
            // Update product
            $slug = strtolower(preg_replace('/[^a-z0-9-]+/', '-', $name));
            $stmt = $conn->prepare("
                UPDATE products 
                SET name = ?, slug = ?, category_id = ?, description = ?, price = ?, 
                    stock_quantity = ?, image_path = ?, status = ?, updated_at = NOW()
                WHERE id = ?
            ");
            
            $stmt->execute([$name, $slug, $category_id, $description, $price, $stock_quantity, $image_path, $status, $product_id]);
            
            $_SESSION['message'] = 'Product updated successfully!';
            $_SESSION['message_type'] = 'success';
            header('Location: products.php');
            exit();
            
        } catch(Exception $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Digital Revive</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar {
            background-color: #343a40;
            min-height: 100vh;
            padding: 20px 0;
        }
        .sidebar a {
            color: #fff;
            text-decoration: none;
            padding: 12px 20px;
            display: block;
            transition: all 0.3s;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: #667eea;
            padding-left: 30px;
        }
    </style>
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js"></script>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <h4 class="text-white px-3 mb-4">Digital Revive</h4>
                <a href="dashboard.php">
                    <i class="fas fa-chart-line"></i> Dashboard
                </a>
                <a href="products.php" class="active">
                    <i class="fas fa-box"></i> Products
                </a>
                <a href="blog.php">
                    <i class="fas fa-blog"></i> Blog Posts
                </a>
                <a href="db-test.php">
                    <i class="fas fa-database"></i> Database Test
                </a>
                <hr class="text-white">
                <a href="logout.php" class="text-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <h2 class="mb-4"><i class="fas fa-edit me-2"></i>Edit Product</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Product Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" placeholder="Enter product name" value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" name="price" placeholder="0.00" step="0.01" value="<?php echo htmlspecialchars($product['price'] ?? ''); ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="form-select" name="category_id" required>
                                        <option value="">Select a category</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo $cat['id']; ?>" <?php echo ($product['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($cat['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Stock Quantity</label>
                                    <input type="number" class="form-control" name="stock_quantity" value="<?php echo htmlspecialchars($product['stock_quantity'] ?? 0); ?>">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="active" <?php echo ($product['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo ($product['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                    <option value="discontinued" <?php echo ($product['status'] == 'discontinued') ? 'selected' : ''; ?>>Discontinued</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Description (HTML, CSS & JavaScript Supported)</label>
                                <textarea id="description" class="form-control" name="description" rows="6" placeholder="Enter product description..."><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                                <small class="text-muted">You can use HTML, CSS, and JavaScript code in descriptions</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Product Image</label>
                                <div class="file-upload-wrapper">
                                    <input type="file" id="productImage" name="image" accept="image/jpeg,image/png,image/gif,image/webp" class="form-control">
                                    <small class="form-text d-block mt-2">Max file size: 5MB. Supported: JPG, PNG, GIF, WEBP</small>
                                </div>
                                <?php if ($product['image_path']): ?>
                                    <div class="mt-3">
                                        <img src="../../<?php echo $product['image_path']; ?>" class="img-fluid" style="max-height: 200px; border-radius:8px; border:2px solid #ddd;" alt="Current product image">
                                        <small class="d-block mt-2 text-muted">Current image. Upload a new one to replace.</small>
                                    </div>
                                <?php endif; ?>
                                <img id="imagePreview" style="display:none; max-width:200px; max-height:200px; border-radius:8px; border:2px solid #ddd; padding:5px;" class="mt-3" alt="Image preview">
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Product
                                </button>
                                <a href="products.php" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/admin.js"></script>
    <script>
        // Initialize TinyMCE editor for description field
        tinymce.init({
            selector: '#description',
            height: 400,
            plugins: 'advlist autolink lists link image charmap code fullscreen',
            toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | link code fullscreen',
            menubar: false,
            statusbar: false,
            valid_elements: '*[*]',
            extended_valid_elements: '*[*]',
            entity_encoding: 'raw'
        });

        document.getElementById('productImage').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('imagePreview').src = event.target.result;
                    document.getElementById('imagePreview').style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
