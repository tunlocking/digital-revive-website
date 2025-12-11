<?php
session_start();
require_once '../../config/db.php';
require_once '../../admin/includes/security.php';

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$error = '';
$success = '';

// Get categories
$categories = [];
try {
    $stmt = $conn->query("SELECT id, name FROM categories ORDER BY name");
    $categories = $stmt->fetchAll();
} catch(PDOException $e) {
    $error = 'Error loading categories.';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verify CSRF token
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Security token verification failed. Please try again.';
    } else {
        $name = sanitize_input($_POST['name'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $category_id = intval($_POST['category_id'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $stock_quantity = intval($_POST['stock_quantity'] ?? 0);
        
        // Validation
        if (empty($name) || $price <= 0 || $category_id <= 0) {
            $error = 'Please fill in all required fields with valid values.';
        } else {
        try {
            // Handle file upload
            $image_path = null;
            if (!empty($_FILES['image']['name'])) {
                $file = $_FILES['image'];
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $filename = basename($file['name']);
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                
                if (!in_array($ext, $allowed)) {
                    throw new Exception('Invalid file type. Only JPG, PNG, GIF allowed.');
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
                    $image_path = 'uploads/products/' . $new_filename;
                }
            }
            
            // Insert product
            $stmt = $conn->prepare("
                INSERT INTO products (name, slug, category_id, description, price, stock_quantity, image_path, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'active')
            ");
            
            $slug = strtolower(preg_replace('/[^a-z0-9-]+/', '-', $name));
            $stmt->execute([$name, $slug, $category_id, $description, $price, $stock_quantity, $image_path]);
            
            $success = 'Product added successfully!';
            // Reset form
            $_POST = [];
            
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
    <title>Add Product - Digital Revive Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/modern-theme.css">
    <script src="https://cdn.tiny.cloud/1/dxx5wlmiegyqzsthfo3wciwhg29vyddbilrjzdma7o0czyvm/tinymce/6/tinymce.min.js"></script>
</head>
<body>
    <?php include '../../admin/includes/navbar.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include '../../admin/includes/sidebar.php'; ?>
            
            <main class="col-md-9 col-lg-10 px-md-4">
                <div class="card mt-4 shadow-sm">
                    <div class="card-body">
                <h2 class="mb-4">Add New Product</h2>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($success); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <?php csrf_input(); ?>
                    <div class="mb-3">
                        <label class="form-label">Product Name *</label>
                        <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Price (MAD) *</label>
                            <input type="number" class="form-control" name="price" step="0.01" min="0" value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Stock Quantity</label>
                            <input type="number" class="form-control" name="stock_quantity" min="0" value="<?php echo htmlspecialchars($_POST['stock_quantity'] ?? '0'); ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Category *</label>
                        <select class="form-control" name="category_id" required>
                            <option value="">-- Select Category --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo (($_POST['category_id'] ?? '') == $cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                    <label class="form-label">Description (HTML, CSS & JavaScript Supported)</label>
                    <textarea id="description" class="form-control" name="description" rows="6"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                    <small class="text-muted">You can use HTML, CSS, and JavaScript code in descriptions</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Product Image</label>
                        <input type="file" class="form-control" id="productImage" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
                        <small class="text-muted">Max 5MB. Supported: JPG, PNG, GIF, WebP</small>
                        <div id="imagePreview" class="mt-3" style="display:none;">
                            <img id="previewImg" src="" alt="Preview" style="max-width:200px; max-height:200px; border-radius:8px; border:2px solid #ddd; padding:5px;">
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                        <button type="submit" class="btn btn-primary">Add Product</button>
                        <a href="products.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
                    </div> <!-- card-body -->
                </div> <!-- card -->
            </main>
        </div> <!-- row -->
    </div> <!-- container-fluid -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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

        // Image preview functionality
        const productImageEl = document.getElementById('productImage');
        if (productImageEl) {
            productImageEl.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        const previewImg = document.getElementById('previewImg');
                        const imagePreview = document.getElementById('imagePreview');
                        if (previewImg) previewImg.src = event.target.result;
                        if (imagePreview) imagePreview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    </script>
</body>
</html>
