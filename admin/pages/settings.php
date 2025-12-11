<?php
session_start();
require_once '../../config/db.php';

// Check admin access
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$errors = [];
$success = false;

// Get all settings
$stmt = $conn->query("SELECT setting_key, setting_value, setting_type FROM settings ORDER BY setting_key");
$settings = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['setting_key']] = $row;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updates = [
        'site_name' => trim($_POST['site_name'] ?? ''),
        'site_email' => trim($_POST['site_email'] ?? ''),
        'site_phone' => trim($_POST['site_phone'] ?? ''),
        'whatsapp_number' => trim($_POST['whatsapp_number'] ?? ''),
        'instagram_handle' => trim($_POST['instagram_handle'] ?? ''),
        'site_address' => trim($_POST['site_address'] ?? ''),
        'business_hours' => trim($_POST['business_hours'] ?? ''),
        'about_description' => trim($_POST['about_description'] ?? ''),
        'header_title' => trim($_POST['header_title'] ?? ''),
        'header_subtitle' => trim($_POST['header_subtitle'] ?? ''),
        'facebook_url' => trim($_POST['facebook_url'] ?? ''),
        'twitter_url' => trim($_POST['twitter_url'] ?? ''),
        'linkedin_url' => trim($_POST['linkedin_url'] ?? ''),
    ];

    // Validate emails and URLs
    if (!filter_var($updates['site_email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email address';
    }

    if (empty($errors)) {
        foreach ($updates as $key => $value) {
            $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
            $stmt->execute([$value, $key]);
        }
        $success = true;
        header('Location: settings.php?success=1');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Settings - Digital Revive Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <?php include '../../admin/includes/navbar.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include '../../admin/includes/sidebar.php'; ?>
            
            <main class="col-md-9 col-lg-10 px-md-4">
                <h2 class="mt-4 mb-4"><i class="fas fa-sliders-h"></i> Website Settings</h2>

                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> Settings updated successfully!
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

                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="POST">
                            <!-- Basic Information -->
                            <h5 class="mb-3"><i class="fas fa-info-circle"></i> Basic Information</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="site_name" class="form-label">Site Name</label>
                                    <input type="text" class="form-control" id="site_name" name="site_name" value="<?php echo htmlspecialchars($settings['site_name']['setting_value'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="header_title" class="form-label">Header Title</label>
                                    <input type="text" class="form-control" id="header_title" name="header_title" value="<?php echo htmlspecialchars($settings['header_title']['setting_value'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="header_subtitle" class="form-label">Header Subtitle</label>
                                <input type="text" class="form-control" id="header_subtitle" name="header_subtitle" value="<?php echo htmlspecialchars($settings['header_subtitle']['setting_value'] ?? ''); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="about_description" class="form-label">About Us Description</label>
                                <textarea class="form-control" id="about_description" name="about_description" rows="3"><?php echo htmlspecialchars($settings['about_description']['setting_value'] ?? ''); ?></textarea>
                            </div>

                            <hr class="my-4">

                            <!-- Contact Information -->
                            <h5 class="mb-3"><i class="fas fa-phone"></i> Contact Information</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="site_email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="site_email" name="site_email" value="<?php echo htmlspecialchars($settings['site_email']['setting_value'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="site_phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="site_phone" name="site_phone" value="<?php echo htmlspecialchars($settings['site_phone']['setting_value'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="whatsapp_number" class="form-label">WhatsApp Number</label>
                                    <input type="tel" class="form-control" id="whatsapp_number" name="whatsapp_number" value="<?php echo htmlspecialchars($settings['whatsapp_number']['setting_value'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="instagram_handle" class="form-label">Instagram Handle</label>
                                    <div class="input-group">
                                        <span class="input-group-text">@</span>
                                        <input type="text" class="form-control" id="instagram_handle" name="instagram_handle" value="<?php echo htmlspecialchars(str_replace('@', '', $settings['instagram_handle']['setting_value'] ?? '')); ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="site_address" class="form-label">Business Address</label>
                                <textarea class="form-control" id="site_address" name="site_address" rows="2"><?php echo htmlspecialchars($settings['site_address']['setting_value'] ?? ''); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="business_hours" class="form-label">Business Hours</label>
                                <textarea class="form-control" id="business_hours" name="business_hours" rows="3" placeholder="Mon-Fri: 9AM-6PM&#10;Sat: 10AM-4PM"><?php echo htmlspecialchars($settings['business_hours']['setting_value'] ?? ''); ?></textarea>
                            </div>

                            <hr class="my-4">

                            <!-- Social Links -->
                            <h5 class="mb-3"><i class="fas fa-share-alt"></i> Social Media Links</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="facebook_url" class="form-label">Facebook URL</label>
                                    <input type="url" class="form-control" id="facebook_url" name="facebook_url" value="<?php echo htmlspecialchars($settings['facebook_url']['setting_value'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="twitter_url" class="form-label">Twitter URL</label>
                                    <input type="url" class="form-control" id="twitter_url" name="twitter_url" value="<?php echo htmlspecialchars($settings['twitter_url']['setting_value'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="linkedin_url" class="form-label">LinkedIn URL</label>
                                <input type="url" class="form-control" id="linkedin_url" name="linkedin_url" value="<?php echo htmlspecialchars($settings['linkedin_url']['setting_value'] ?? ''); ?>">
                            </div>

                            <div class="d-flex gap-2 mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Settings
                                </button>
                                <a href="dashboard.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
