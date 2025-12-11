<?php
session_start();
require_once '../../config/db.php';

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$error = '';
$products = [];
$page = intval($_GET['page'] ?? 1);
$per_page = 10;
$offset = ($page - 1) * $per_page;

try {
    // Get total products
    $total = $conn->query("SELECT COUNT(*) as count FROM products")->fetch()['count'];
    $total_pages = ceil($total / $per_page);
    
    // Get products for current page
    $stmt = $conn->prepare("
        SELECT p.id, p.name, p.price, p.stock_quantity, p.status, c.name as category, p.created_at
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        ORDER BY p.created_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$per_page, $offset]);
    $products = $stmt->fetchAll();
    
} catch(PDOException $e) {
    $error = 'Error loading products: ' . $e->getMessage();
}

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete') {
    $product_id = intval($_POST['product_id'] ?? 0);
    
    try {
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $_SESSION['message'] = 'Product deleted successfully!';
        $_SESSION['message_type'] = 'success';
        header('Location: products.php');
        exit();
    } catch(PDOException $e) {
        $error = 'Error deleting product';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Digital Revive Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            background-color: #343a40;
            min-height: 100vh;
            padding: 20px 0;
            position: fixed;
            top: 0;
            left: 0;
            width: 16.666%;
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
        .main-content {
            margin-left: 16.666%;
            padding: 20px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h4 class="text-white px-3 mb-4">Digital Revive</h4>
        
        <!-- Main -->
        <a href="dashboard.php">
            <i class="fas fa-chart-line"></i> Dashboard
        </a>
        
        <!-- Products -->
        <a href="products.php" class="active">
            <i class="fas fa-box"></i> Products
        </a>
        <a href="add_product.php" style="padding-left: 40px; font-size: 14px;">
            <i class="fas fa-plus"></i> Add Product
        </a>
        
        <!-- Blog -->
        <a href="blog.php">
            <i class="fas fa-blog"></i> Blog Posts
        </a>
        <a href="add_blog.php" style="padding-left: 40px; font-size: 14px;">
            <i class="fas fa-plus"></i> Add Blog Post
        </a>
        
        <!-- Services -->
        <a href="services.php">
            <i class="fas fa-tools"></i> Services
        </a>
        <a href="add_service.php" style="padding-left: 40px; font-size: 14px;">
            <i class="fas fa-plus"></i> Add Service
        </a>
        
        <!-- Team -->
        <a href="team.php">
            <i class="fas fa-users"></i> Team Members
        </a>
        <a href="add_team_member.php" style="padding-left: 40px; font-size: 14px;">
            <i class="fas fa-plus"></i> Add Member
        </a>
        
        <!-- Settings -->
        <a href="settings.php">
            <i class="fas fa-cog"></i> Settings
        </a>
        
        <!-- Database -->
        <a href="db-test.php">
            <i class="fas fa-database"></i> Database Test
        </a>
        
        <hr class="text-white">
        <a href="logout.php" class="text-danger">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
    
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manage Products</h2>
            <a href="add_product.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Product
            </a>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message_type'] ?? 'info'; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_SESSION['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>
        
        <?php if (empty($products)): ?>
            <div class="alert alert-info">No products found. <a href="add_product.php">Add one now</a></div>
        <?php else: ?>
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price (MAD)</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Date Added</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td><?php echo htmlspecialchars($product['category'] ?? 'N/A'); ?></td>
                                    <td><strong><?php echo number_format($product['price'], 2); ?></strong></td>
                                    <td><?php echo $product['stock_quantity']; ?> units</td>
                                    <td>
                                        <span class="badge bg-<?php echo ($product['status'] == 'active') ? 'success' : 'secondary'; ?>">
                                            <?php echo ucfirst($product['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($product['created_at'])); ?></td>
                                    <td>
                                        <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item"><a class="page-link" href="products.php?page=<?php echo $page - 1; ?>">Previous</a></li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                <a class="page-link" href="products.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <li class="page-item"><a class="page-link" href="products.php?page=<?php echo $page + 1; ?>">Next</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div> <!-- main-content -->
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
