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

$errors = [];
$success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Security token verification failed. Please try again.';
    } else {
        $name = sanitize_input($_POST['name'] ?? '');
        $email = sanitize_email($_POST['email'] ?? '');
        $phone = sanitize_input($_POST['phone'] ?? '');
        $subject = sanitize_input($_POST['subject'] ?? '');
        $message = sanitize_input($_POST['message'] ?? '');

        // Validation
        if (empty($name)) $errors[] = 'Name is required';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';
        if (empty($phone)) $errors[] = 'Phone number is required';
        if (empty($subject)) $errors[] = 'Subject is required';
        if (empty($message)) $errors[] = 'Message is required';

        if (empty($errors)) {
            try {
                // Save inquiry to database
                $stmt = $conn->prepare("
                    INSERT INTO contact_messages (name, email, phone, subject, message, status, created_at)
                    VALUES (?, ?, ?, ?, ?, 'new', NOW())
                ");
                $stmt->execute([$name, $email, $phone, $subject, $message]);
                
                // Send email to admin with proper sanitization
                $admin_email = $settings['site_email'] ?? 'admin@digitalrevive.ma';
                
                // Sanitize email to prevent header injection
                $email_clean = str_replace(["\r", "\n"], '', $email);
                $email_clean = filter_var($email_clean, FILTER_VALIDATE_EMAIL);
                
                $email_subject = 'New Contact Form Submission - ' . substr($subject, 0, 50);
                $email_body = "
                <h2>New Contact Form Submission</h2>
                <p><strong>Name:</strong> " . htmlspecialchars($name) . "</p>
                <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
                <p><strong>Phone:</strong> " . htmlspecialchars($phone) . "</p>
                <p><strong>Subject:</strong> " . htmlspecialchars($subject) . "</p>
                <p><strong>Message:</strong></p>
                <p>" . htmlspecialchars($message) . "</p>
                <p><hr></p>
                <p>Reply to: " . htmlspecialchars($email) . "</p>
                ";

                $headers = "MIME-Version: 1.0\r\n";
                $headers .= "Content-type: text/html; charset=UTF-8\r\n";
                $headers .= "From: noreply@digitalrevive.ma\r\n";
                $headers .= "Reply-To: " . $email_clean . "\r\n";

                // Send email
                mail($admin_email, $email_subject, $email_body, $headers);

                // Send confirmation email to user
                $user_subject = 'We received your message - ' . ($settings['site_name'] ?? 'Digital Revive');
                $user_body = "
                <h2>Thank you for contacting us!</h2>
                <p>Dear " . htmlspecialchars($name) . ",</p>
                <p>We have received your message and will get back to you as soon as possible.</p>
                <p><strong>Your Subject:</strong><br>" . htmlspecialchars($subject) . "</p>
                <p>Best regards,<br>" . htmlspecialchars($settings['site_name'] ?? 'Digital Revive') . " Team</p>
                ";
                
                $user_headers = "MIME-Version: 1.0\r\n";
                $user_headers .= "Content-type: text/html; charset=UTF-8\r\n";
                $user_headers .= "From: noreply@digitalrevive.ma\r\n";
                
                mail($email, $user_subject, $user_body, $user_headers);

                $success = true;
            } catch (Exception $e) {
                $errors[] = 'Error saving message. Please try again later.';
            }
        }
    }
}

// Get services for dropdown
$stmt = $conn->query("SELECT id, name, slug FROM services WHERE status = 'active' ORDER BY name");
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - <?php echo htmlspecialchars($settings['site_name'] ?? 'Digital Revive'); ?></title>
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
                    <li class="nav-item"><a class="nav-link" href="blog.php">Blog</a></li>
                    <li class="nav-item"><a class="nav-link active" href="contact.php">Contact</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin/pages/login.php"><i class="fas fa-lock"></i> Admin</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="bg-dark text-white py-5">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-3">Contact Us</h1>
            <p class="lead">Get in touch with us today</p>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- Contact Form -->
                <div class="col-lg-8 mb-5 mb-lg-0">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h3 class="card-title mb-4">Send us a Message</h3>

                            <?php if ($success): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle"></i> <strong>Success!</strong> Your message has been sent. We'll get back to you soon!
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger">
                                    <strong>Errors:</strong>
                                    <ul class="mb-0">
                                        <?php foreach ($errors as $error): ?>
                                            <li><?php echo htmlspecialchars($error); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <form method="POST">
                                <?php csrf_input(); ?>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control" id="phone" name="phone" required value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="subject" name="subject" required value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="message" name="message" rows="5" required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane"></i> Send Message
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="col-lg-4">
                    <!-- Contact Details -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-4">
                                <i class="fas fa-phone"></i> Contact Information
                            </h5>

                            <div class="mb-4">
                                <h6 class="text-muted">Email</h6>
                                <a href="mailto:<?php echo htmlspecialchars($settings['site_email'] ?? ''); ?>" class="text-decoration-none">
                                    <?php echo htmlspecialchars($settings['site_email'] ?? ''); ?>
                                </a>
                            </div>

                            <div class="mb-4">
                                <h6 class="text-muted">Phone</h6>
                                <a href="tel:<?php echo htmlspecialchars($settings['site_phone'] ?? ''); ?>" class="text-decoration-none">
                                    <?php echo htmlspecialchars($settings['site_phone'] ?? ''); ?>
                                </a>
                            </div>

                            <div class="mb-4">
                                <h6 class="text-muted">WhatsApp</h6>
                                <a href="https://wa.me/<?php echo preg_replace('/\D/', '', $settings['whatsapp_number'] ?? ''); ?>" target="_blank" class="btn btn-sm btn-success">
                                    <i class="fab fa-whatsapp"></i> Chat on WhatsApp
                                </a>
                            </div>

                            <div class="mb-4">
                                <h6 class="text-muted">Address</h6>
                                <p><?php echo nl2br(htmlspecialchars($settings['site_address'] ?? '')); ?></p>
                            </div>

                            <div>
                                <h6 class="text-muted">Business Hours</h6>
                                <p><?php echo nl2br(htmlspecialchars($settings['business_hours'] ?? '')); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Social Links -->
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-3">
                                <i class="fas fa-share-alt"></i> Follow Us
                            </h5>
                            <div class="d-flex gap-2">
                                <?php if ($settings['facebook_url']): ?>
                                    <a href="<?php echo htmlspecialchars($settings['facebook_url']); ?>" target="_blank" class="btn btn-sm btn-primary">
                                        <i class="fab fa-facebook"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if ($settings['twitter_url']): ?>
                                    <a href="<?php echo htmlspecialchars($settings['twitter_url']); ?>" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if ($settings['instagram_handle']): ?>
                                    <a href="https://instagram.com/<?php echo htmlspecialchars(str_replace('@', '', $settings['instagram_handle'])); ?>" target="_blank" class="btn btn-sm btn-danger">
                                        <i class="fab fa-instagram"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if ($settings['linkedin_url']): ?>
                                    <a href="<?php echo htmlspecialchars($settings['linkedin_url']); ?>" target="_blank" class="btn btn-sm btn-primary">
                                        <i class="fab fa-linkedin"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
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
