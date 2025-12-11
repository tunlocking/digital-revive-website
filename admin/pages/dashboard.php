<?php
session_start();
require_once '../../config/db.php';

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$admin_name = $_SESSION['admin_username'] ?? 'Admin';
$stats = [];

try {
    // Get statistics
    $stats['products'] = $conn->query("SELECT COUNT(*) as count FROM products")->fetch()['count'];
    $stats['blog_posts'] = $conn->query("SELECT COUNT(*) as count FROM blog_posts")->fetch()['count'];
    $stats['orders'] = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch()['count'];
    $stats['repair_orders'] = $conn->query("SELECT COUNT(*) as count FROM repair_orders")->fetch()['count'];
    $stats['messages'] = $conn->query("SELECT COUNT(*) as count FROM contact_messages WHERE status = 'new'")->fetch()['count'];
} catch(PDOException $e) {
    // Stats not available if database error
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Digital Revive</title>
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
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            text-align: center;
        }
        .stat-card h6 {
            color: #999;
            margin-top: 10px;
            text-transform: uppercase;
            font-size: 12px;
        }
        .stat-card .number {
            font-size: 28px;
            font-weight: bold;
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <h4 class="text-white px-3 mb-4">Digital Revive</h4>
                <a href="dashboard.php" class="active">
                    <i class="fas fa-chart-line"></i> Dashboard
                </a>
                <a href="products.php">
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
            <div class="col-md-10">
                <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom mb-4">
                    <div class="container-fluid">
                        <span class="navbar-text">Welcome, <strong><?php echo htmlspecialchars($admin_name); ?></strong></span>
                    </div>
                </nav>
                
                <div class="px-4">
                    <h2 class="mb-4">Dashboard</h2>
                    
                    <!-- Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <div class="stat-card">
                                <i class="fas fa-box fa-2x" style="color: #667eea;"></i>
                                <div class="number"><?php echo $stats['products'] ?? '0'; ?></div>
                                <h6>Products</h6>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="stat-card">
                                <i class="fas fa-file-alt fa-2x" style="color: #764ba2;"></i>
                                <div class="number"><?php echo $stats['blog_posts'] ?? '0'; ?></div>
                                <h6>Blog Posts</h6>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="stat-card">
                                <i class="fas fa-shopping-cart fa-2x" style="color: #f093fb;"></i>
                                <div class="number"><?php echo $stats['orders'] ?? '0'; ?></div>
                                <h6>Orders</h6>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="stat-card">
                                <i class="fas fa-tools fa-2x" style="color: #4facfe;"></i>
                                <div class="number"><?php echo $stats['repair_orders'] ?? '0'; ?></div>
                                <h6>Repairs</h6>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="stat-card">
                                <i class="fas fa-envelope fa-2x" style="color: #fa709a;"></i>
                                <div class="number"><?php echo $stats['messages'] ?? '0'; ?></div>
                                <h6>Messages</h6>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <a href="add_product.php" class="btn btn-primary me-2">
                                <i class="fas fa-plus"></i> Add Product
                            </a>
                            <a href="add_blog.php" class="btn btn-info me-2">
                                <i class="fas fa-pen"></i> Write Blog Post
                            </a>
                            <a href="products.php" class="btn btn-secondary me-2">
                                <i class="fas fa-list"></i> View Products
                            </a>
                            <a href="db-test.php" class="btn btn-warning">
                                <i class="fas fa-stethoscope"></i> Test Database
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
