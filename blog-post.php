<?php
session_start();
require_once 'config/db.php';
require_once 'admin/includes/security.php';

// Get website settings
$stmt = $conn->query("SELECT setting_key, setting_value FROM settings");
$settings = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Get blog post with validation
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: blog.php');
    exit;
}

$stmt = $conn->prepare("
    SELECT bp.*, bc.name as category_name, u.username as author_name FROM blog_posts bp
    LEFT JOIN blog_categories bc ON bp.category_id = bc.id
    LEFT JOIN users u ON bp.author_id = u.id
    WHERE bp.id = ? AND bp.published = TRUE
");
$stmt->execute([$id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    header('Location: blog.php');
    exit;
}

// Get related posts with proper error handling
try {
    $stmt = $conn->prepare("
        SELECT id, title, slug, featured_image, created_at FROM blog_posts
        WHERE published = TRUE AND id != ? AND category_id = ?
        ORDER BY created_at DESC
        LIMIT 3
    ");
    $stmt->execute([$id, $post['category_id'] ?? 0]);
    $related = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $related = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?> - <?php echo htmlspecialchars($settings['site_name'] ?? 'Digital Revive'); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
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

    <!-- Article Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Breadcrumb -->
                    <nav aria-label="breadcrumb" class="mb-4">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="./">Home</a></li>
                            <li class="breadcrumb-item"><a href="blog.php">Blog</a></li>
                            <li class="breadcrumb-item active"><?php echo htmlspecialchars($post['title']); ?></li>
                        </ol>
                    </nav>

                    <!-- Featured Image -->
                    <?php if ($post['featured_image']): ?>
                        <img src="uploads/<?php echo htmlspecialchars($post['featured_image']); ?>" class="img-fluid rounded mb-4" alt="<?php echo htmlspecialchars($post['title']); ?>" style="max-height: 400px; object-fit: cover;">
                    <?php endif; ?>

                    <!-- Post Header -->
                    <h1 class="display-5 fw-bold mb-3"><?php echo htmlspecialchars($post['title']); ?></h1>

                    <div class="d-flex align-items-center mb-4 text-muted">
                        <?php if ($post['category_name']): ?>
                            <span class="badge bg-primary me-2"><?php echo htmlspecialchars($post['category_name']); ?></span>
                        <?php endif; ?>
                        <span><i class="fas fa-user"></i> By <?php echo htmlspecialchars($post['author_name'] ?? 'Admin'); ?></span>
                        <span class="ms-3"><i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($post['created_at'])); ?></span>
                    </div>

                    <!-- Post Content -->
                    <div class="post-content border-top border-bottom py-4 mb-4">
                        <?php echo $post['content']; ?>
                    </div>

                    <!-- Share -->
                    <div class="mb-5">
                        <h6>Share This Post:</h6>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); ?>" class="btn btn-sm btn-primary" target="_blank">
                            <i class="fab fa-facebook"></i> Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($post['title']); ?>" class="btn btn-sm btn-info" target="_blank">
                            <i class="fab fa-twitter"></i> Twitter
                        </a>
                        <a href="https://wa.me/?text=<?php echo urlencode($post['title'] . ' ' . 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); ?>" class="btn btn-sm btn-success" target="_blank">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </a>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Related Posts -->
                    <?php if (!empty($related)): ?>
                        <div class="card shadow-sm mb-4">
                            <div class="card-body">
                                <h5 class="card-title mb-3">
                                    <i class="fas fa-related"></i> Related Posts
                                </h5>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($related as $relPost): ?>
                                        <a href="blog-post.php?id=<?php echo $relPost['id']; ?>" class="list-group-item list-group-item-action">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($relPost['title']); ?></h6>
                                            <small class="text-muted"><?php echo date('M d, Y', strtotime($relPost['created_at'])); ?></small>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- CTA -->
                    <div class="card bg-primary text-white shadow-sm">
                        <div class="card-body text-center">
                            <h5 class="card-title">Need Service?</h5>
                            <p class="card-text">Contact us for professional device repair</p>
                            <a href="contact.php" class="btn btn-light btn-sm">Get in Touch</a>
                        </div>
                    </div>
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
