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
$limit = 6;
$offset = ($page - 1) * $limit;

// Get total blog posts
$count_stmt = $conn->query("SELECT COUNT(*) as total FROM blog_posts WHERE published = TRUE");
$total = $count_stmt->fetch()['total'];
$total_pages = ceil($total / $limit);

// Get blog posts with pagination
$stmt = $conn->prepare("
    SELECT bp.*, bc.name as category_name, u.username as author_name FROM blog_posts bp
    LEFT JOIN blog_categories bc ON bp.category_id = bc.id
    LEFT JOIN users u ON bp.author_id = u.id
    WHERE bp.published = TRUE
    ORDER BY bp.created_at DESC
    LIMIT ? OFFSET ?
");
$stmt->execute([$limit, $offset]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories
$stmt = $conn->query("SELECT * FROM blog_categories ORDER BY name");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - <?php echo htmlspecialchars($settings['site_name'] ?? 'Digital Revive'); ?></title>
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
                    <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link active" href="blog.php">Blog</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin/pages/login.php"><i class="fas fa-lock"></i> Admin</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="bg-dark text-white py-5">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-3">Blog</h1>
            <p class="lead">Tips, tricks, and news about device repair</p>
        </div>
    </section>

    <!-- Blog Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- Sidebar -->
                <div class="col-lg-3 mb-4 mb-lg-0">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-3">
                                <i class="fas fa-list"></i> Categories
                            </h5>
                            <div class="list-group list-group-flush">
                                <a href="blog.php" class="list-group-item list-group-item-action <?php echo !isset($_GET['category']) ? 'active' : ''; ?>">
                                    All Posts
                                </a>
                                <?php foreach ($categories as $cat): ?>
                                    <a href="blog.php?category=<?php echo urlencode($cat['id']); ?>" class="list-group-item list-group-item-action <?php echo isset($_GET['category']) && $_GET['category'] == $cat['id'] ? 'active' : ''; ?>">
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Blog Posts -->
                <div class="col-lg-9">
                    <?php if (!empty($posts)): ?>
                        <div class="row g-4">
                            <?php foreach ($posts as $post): ?>
                                <div class="col-12">
                                    <article class="card h-100 shadow-sm blog-card">
                                        <div class="row g-0">
                                            <?php if ($post['featured_image']): ?>
                                                <div class="col-md-4">
                                                    <img src="uploads/<?php echo htmlspecialchars($post['featured_image']); ?>" class="img-fluid rounded-start" alt="<?php echo htmlspecialchars($post['title']); ?>" style="height: 250px; object-fit: cover;">
                                                </div>
                                                <div class="col-md-8">
                                            <?php else: ?>
                                                <div class="col-12">
                                            <?php endif; ?>
                                                    <div class="card-body">
                                                        <div class="mb-2">
                                                            <?php if ($post['category_name']): ?>
                                                                <span class="badge bg-primary"><?php echo htmlspecialchars($post['category_name']); ?></span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                                                        <p class="card-text text-muted"><?php echo htmlspecialchars(substr(strip_tags($post['content']), 0, 150)); ?>...</p>

                                                        <div class="small text-muted mb-3">
                                                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($post['author_name'] ?? 'Admin'); ?> |
                                                            <i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($post['created_at'])); ?>
                                                        </div>

                                                        <a href="blog-post.php?id=<?php echo $post['id']; ?>" class="btn btn-primary btn-sm">
                                                            Read More <i class="fas fa-arrow-right"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                        </div>
                                    </article>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                            <nav aria-label="Page navigation" class="mt-5">
                                <ul class="pagination justify-content-center">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="blog.php?page=1">First</a>
                                        </li>
                                        <li class="page-item">
                                            <a class="page-link" href="blog.php?page=<?php echo $page - 1; ?>">Previous</a>
                                        </li>
                                    <?php endif; ?>

                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="blog.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="blog.php?page=<?php echo $page + 1; ?>">Next</a>
                                        </li>
                                        <li class="page-item">
                                            <a class="page-link" href="blog.php?page=<?php echo $total_pages; ?>">Last</a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i> No blog posts available at the moment. Check back soon!
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
