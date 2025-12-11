<?php
session_start();
require_once 'config/db.php';

// Get website settings
$stmt = $conn->query("SELECT setting_key, setting_value FROM settings");
$settings = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Get active services
$stmt = $conn->query("SELECT * FROM services WHERE status = 'active' ORDER BY category, name LIMIT 6");
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get testimonials
$stmt = $conn->query("SELECT * FROM testimonials WHERE status = 'active' ORDER BY created_at DESC LIMIT 3");
$testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get team members
$stmt = $conn->query("SELECT * FROM team_members WHERE status = 'active' ORDER BY position_order LIMIT 3");
$team = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($settings['site_name'] ?? 'Digital Revive'); ?> - Professional Device Repair</title>
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
                    <li class="nav-item"><a class="nav-link active" href="./">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="blog.php">Blog</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin/pages/login.php"><i class="fas fa-lock"></i> Admin</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section bg-dark text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4"><?php echo htmlspecialchars($settings['header_title'] ?? 'Welcome to Digital Revive'); ?></h1>
                    <p class="lead mb-4"><?php echo htmlspecialchars($settings['header_subtitle'] ?? 'Professional Device Repair Services'); ?></p>
                    <a href="contact.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-envelope"></i> Get In Touch
                    </a>
                    <a href="services.php" class="btn btn-outline-light btn-lg ms-2">
                        <i class="fas fa-wrench"></i> Our Services
                    </a>
                </div>
                <div class="col-lg-6">
                    <div class="text-center">
                        <i class="fas fa-tools" style="font-size: 150px; color: #ffc107; opacity: 0.8;"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services-section py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Our Services</h2>
                <p class="lead text-muted">Professional repair solutions for all your device needs</p>
            </div>

            <div class="row g-4">
                <?php foreach ($services as $service): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm service-card">
                            <div class="card-body">
                                <?php if ($service['icon']): ?>
                                    <div class="text-center mb-3">
                                        <i class="<?php echo htmlspecialchars($service['icon']); ?>" style="font-size: 48px; color: #ffc107;"></i>
                                    </div>
                                <?php endif; ?>
                                <h5 class="card-title"><?php echo htmlspecialchars($service['name']); ?></h5>
                                <p class="card-text text-muted"><?php echo htmlspecialchars($service['description']); ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <?php if ($service['price']): ?>
                                        <span class="badge bg-success">$<?php echo number_format($service['price'], 2); ?></span>
                                    <?php endif; ?>
                                    <a href="services.php#<?php echo htmlspecialchars($service['slug']); ?>" class="btn btn-sm btn-primary">Learn More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="text-center mt-5">
                <a href="services.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-arrow-right"></i> View All Services
                </a>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <?php if (!empty($team)): ?>
    <section class="team-section py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Our Expert Team</h2>
                <p class="lead text-muted">Skilled professionals dedicated to your device repair</p>
            </div>

            <div class="row g-4">
                <?php foreach ($team as $member): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card text-center shadow-sm">
                            <?php if ($member['image_path']): ?>
                                <img src="uploads/<?php echo htmlspecialchars($member['image_path']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($member['name']); ?>" style="height: 250px; object-fit: cover;">
                            <?php else: ?>
                                <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 250px;">
                                    <i class="fas fa-user-circle" style="font-size: 80px; color: white; opacity: 0.5;"></i>
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($member['name']); ?></h5>
                                <p class="card-text text-primary fw-bold"><?php echo htmlspecialchars($member['position']); ?></p>
                                <p class="card-text text-muted"><?php echo htmlspecialchars($member['bio']); ?></p>
                                <?php if ($member['email']): ?>
                                    <a href="mailto:<?php echo htmlspecialchars($member['email']); ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-envelope"></i> Contact
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Testimonials Section -->
    <?php if (!empty($testimonials)): ?>
    <section class="testimonials-section py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">What Our Customers Say</h2>
                <p class="lead text-muted">Real feedback from satisfied clients</p>
            </div>

            <div class="row g-4">
                <?php foreach ($testimonials as $testimonial): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <div class="mb-3">
                                    <?php for ($i = 0; $i < $testimonial['rating']; $i++): ?>
                                        <i class="fas fa-star text-warning"></i>
                                    <?php endfor; ?>
                                </div>
                                <p class="card-text">"<?php echo htmlspecialchars($testimonial['message']); ?>"</p>
                                <h6 class="card-title mt-3"><?php echo htmlspecialchars($testimonial['client_name']); ?></h6>
                                <?php if ($testimonial['client_position']): ?>
                                    <small class="text-muted"><?php echo htmlspecialchars($testimonial['client_position']); ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- CTA Section -->
    <section class="cta-section bg-primary text-white py-5">
        <div class="container text-center">
            <h2 class="display-5 fw-bold mb-4">Ready to Repair Your Device?</h2>
            <p class="lead mb-4">Contact us today for fast, professional service</p>
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
