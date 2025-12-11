<?php
session_start();
require_once 'config/db.php';

// Get website settings
$stmt = $conn->query("SELECT setting_key, setting_value FROM settings");
$settings = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Get categories
$stmt = $conn->query("SELECT DISTINCT category FROM services WHERE status = 'active' ORDER BY category");
$categories = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $categories[] = $row['category'];
}

// Get all services grouped by category
$stmt = $conn->query("SELECT * FROM services WHERE status = 'active' ORDER BY category, name");
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group services by category
$servicesByCategory = [];
foreach ($services as $service) {
    if (!isset($servicesByCategory[$service['category']])) {
        $servicesByCategory[$service['category']] = [];
    }
    $servicesByCategory[$service['category']][] = $service;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services - <?php echo htmlspecialchars($settings['site_name'] ?? 'Digital Revive'); ?></title>
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
                    <li class="nav-item"><a class="nav-link active" href="services.php">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
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
            <h1 class="display-4 fw-bold mb-3">Our Services</h1>
            <p class="lead">Professional repair solutions for all your device needs</p>
        </div>
    </section>

    <!-- Services Section -->
    <section class="py-5">
        <div class="container">
            <?php if (!empty($servicesByCategory)): ?>
                <?php foreach ($servicesByCategory as $category => $categoryServices): ?>
                    <div class="mb-5">
                        <h3 class="mb-4 border-bottom pb-3">
                            <i class="fas fa-cogs text-primary"></i> <?php echo htmlspecialchars($category); ?>
                        </h3>

                        <div class="row g-4">
                            <?php foreach ($categoryServices as $service): ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 shadow-sm service-card">
                                        <?php if ($service['image_path']): ?>
                                            <img src="uploads/<?php echo htmlspecialchars($service['image_path']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($service['name']); ?>" style="height: 200px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                                <?php if ($service['icon']): ?>
                                                    <i class="<?php echo htmlspecialchars($service['icon']); ?>" style="font-size: 80px; color: #ddd;"></i>
                                                <?php else: ?>
                                                    <i class="fas fa-wrench" style="font-size: 80px; color: #ddd;"></i>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($service['name']); ?></h5>
                                            <p class="card-text text-muted"><?php echo htmlspecialchars($service['description']); ?></p>

                                            <div class="row text-center text-muted mb-3">
                                                <?php if ($service['price']): ?>
                                                    <div class="col-6 border-end">
                                                        <small><strong>Price</strong></small>
                                                        <div>$<?php echo number_format($service['price'], 2); ?></div>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if ($service['estimated_days']): ?>
                                                    <div class="col-6">
                                                        <small><strong>Time</strong></small>
                                                        <div><?php echo $service['estimated_days']; ?> day(s)</div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            <?php if ($service['detailed_description']): ?>
                                                <p class="small text-muted"><?php echo htmlspecialchars(substr($service['detailed_description'], 0, 80)); ?>...</p>
                                            <?php endif; ?>

                                            <a href="contact.php?service=<?php echo urlencode($service['slug']); ?>" class="btn btn-primary btn-sm w-100">
                                                <i class="fas fa-envelope"></i> Request Service
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i> No services available at the moment. Please check back later.
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-primary text-white py-5">
        <div class="container text-center">
            <h2 class="display-5 fw-bold mb-4">Need Our Services?</h2>
            <p class="lead mb-4">Contact us today to schedule your repair appointment</p>
            <a href="contact.php" class="btn btn-light btn-lg">
                <i class="fas fa-phone"></i> Contact Us Now
            </a>
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
