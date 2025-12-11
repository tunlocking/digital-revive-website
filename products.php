<?php
session_start();
require_once 'config/db.php';

// Get website settings
$stmt = $conn->query("SELECT setting_key, setting_value FROM settings");
$settings = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Pagination
$page = intval($_GET['page'] ?? 1);
$limit = 12;
$offset = ($page - 1) * $limit;

// Get total products
$count_stmt = $conn->query("SELECT COUNT(*) as total FROM products WHERE status = 'active'");
$total = $count_stmt->fetch()['total'];
$total_pages = ceil($total / $limit);

// Get products with pagination
$stmt = $conn->prepare("
    SELECT p.*, c.name as category_name FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.status = 'active'
    ORDER BY c.name, p.name
    LIMIT ? OFFSET ?
");
$stmt->execute([$limit, $offset]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories
$stmt = $conn->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - <?php echo htmlspecialchars($settings['site_name'] ?? 'Digital Revive'); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/modern-theme.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="./">
                <i class="fas fa-tools"></i> <?php echo htmlspecialchars($settings['site_name'] ?? 'Digital Revive'); ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="./">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
                    <li class="nav-item"><a class="nav-link active" href="products.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="blog.php">Blog</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin/pages/login.php"><i class="fas fa-lock"></i> Admin</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="bg-dark text-white py-5">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-3">Products</h1>
            <p class="lead">Quality parts and accessories for your devices</p>
        </div>
    </section>

    <!-- Products Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- Sidebar Filter -->
                <div class="col-md-3 mb-4 mb-md-0">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-3">
                                <i class="fas fa-filter"></i> Filter
                            </h5>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Categories</label>
                                <div class="list-group list-group-flush">
                                    <a href="products.php" class="list-group-item list-group-item-action <?php echo !isset($_GET['category']) ? 'active' : ''; ?>">
                                        All Products
                                    </a>
                                    <?php foreach ($categories as $cat): ?>
                                        <a href="products.php?category=<?php echo urlencode($cat['id']); ?>" class="list-group-item list-group-item-action <?php echo isset($_GET['category']) && $_GET['category'] == $cat['id'] ? 'active' : ''; ?>">
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="col-md-9">
                    <?php if (!empty($products)): ?>
                        <div class="row g-4">
                            <?php foreach ($products as $product): ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 shadow-sm product-card">
                                        <?php if ($product['image_path']): ?>
                                            <img src="uploads/<?php echo htmlspecialchars($product['image_path']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>" style="height: 250px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 250px;">
                                                <i class="fas fa-cube" style="font-size: 80px; color: #ddd;"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="card-body">
                                            <span class="badge bg-info mb-2"><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></span>
                                            <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                            <p class="card-text text-muted"><?php echo htmlspecialchars(substr($product['description'], 0, 60)); ?>...</p>

                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge bg-success fs-6">$<?php echo number_format($product['price'], 2); ?></span>
                                                <span class="text-muted small">
                                                    <i class="fas fa-box"></i> <?php echo $product['stock_quantity']; ?> in stock
                                                </span>
                                            </div>

                                            <button class="btn btn-primary btn-sm w-100 mt-3" data-bs-toggle="modal" data-bs-target="#contactModal" onclick="setProductName('<?php echo htmlspecialchars(addslashes($product['name'])); ?>')">
                                                <i class="fas fa-shopping-cart"></i> Order Now
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                            <nav aria-label="Page navigation" class="mt-5">
                                <ul class="pagination justify-content-center">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="products.php?page=1">First</a>
                                        </li>
                                        <li class="page-item">
                                            <a class="page-link" href="products.php?page=<?php echo $page - 1; ?>">Previous</a>
                                        </li>
                                    <?php endif; ?>

                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="products.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="products.php?page=<?php echo $page + 1; ?>">Next</a>
                                        </li>
                                        <li class="page-item">
                                            <a class="page-link" href="products.php?page=<?php echo $total_pages; ?>">Last</a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i> No products available at the moment.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-4 mb-3 mb-md-0">
                    <h6><i class="fas fa-info-circle"></i> About</h6>
                    <p class="text-muted"><?php echo htmlspecialchars($settings['about_description'] ?? ''); ?></p>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <h6><i class="fas fa-map-marker-alt"></i> Address</h6>
                    <p class="text-muted"><?php echo htmlspecialchars($settings['site_address'] ?? ''); ?></p>
                </div>
                <div class="col-md-4">
                    <h6><i class="fas fa-phone"></i> Contact</h6>
                    <p class="text-muted">
                        Email: <a href="mailto:<?php echo htmlspecialchars($settings['site_email'] ?? ''); ?>" class="text-warning"><?php echo htmlspecialchars($settings['site_email'] ?? ''); ?></a><br>
                        Phone: <a href="tel:<?php echo htmlspecialchars($settings['site_phone'] ?? ''); ?>" class="text-warning"><?php echo htmlspecialchars($settings['site_phone'] ?? ''); ?></a><br>
                        WhatsApp: <a href="https://wa.me/<?php echo preg_replace('/\D/', '', $settings['whatsapp_number'] ?? ''); ?>" class="text-warning" target="_blank">Message Us</a>
                    </p>
                </div>
            </div>

            <div class="text-center py-3 border-top border-secondary">
                <p class="text-muted mb-0">&copy; 2024 <?php echo htmlspecialchars($settings['site_name'] ?? 'Digital Revive'); ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Contact Modal -->
    <div class="modal fade" id="contactModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Interested in a product?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Contact us to order <strong id="productName"></strong></p>
                    <a href="contact.php" class="btn btn-primary w-100">Go to Contact Form</a>
                    <a href="https://wa.me/<?php echo preg_replace('/\D/', '', $settings['whatsapp_number'] ?? ''); ?>" target="_blank" class="btn btn-success w-100 mt-2">
                        <i class="fab fa-whatsapp"></i> WhatsApp Us
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        function setProductName(name) {
            document.getElementById('productName').textContent = name;
        }
    </script>
</body>
</html>
