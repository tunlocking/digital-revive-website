<?php
session_start();
require_once '../../config/db.php';

// Check admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $position = trim($_POST['position'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $skills = trim($_POST['skills'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $status = $_POST['status'] ?? 'active';
    $position_order = intval($_POST['position_order'] ?? 0);
    $image_path = null;

    // Validation
    if (empty($name)) $errors[] = 'Name is required';
    if (empty($position)) $errors[] = 'Position is required';

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $file = $_FILES['image'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if ($file['size'] > $maxSize) {
            $errors[] = 'Image file size must not exceed 5MB';
        } elseif (!in_array($file['type'], $allowedMimes)) {
            $errors[] = 'Only JPG, PNG, GIF, and WebP images are allowed';
        } else {
            $uploadDir = '../../uploads/team/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $filename = 'team_' . time() . '_' . basename($file['name']);
            $filepath = $uploadDir . $filename;

            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                $image_path = 'team/' . $filename;
            } else {
                $errors[] = 'Failed to upload image';
            }
        }
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("
            INSERT INTO team_members (name, position, bio, skills, phone, email, image_path, status, position_order)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$name, $position, $bio, $skills, $phone, $email, $image_path, $status, $position_order]);
        header('Location: team.php?success=1');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Team Member - Digital Revive Admin</title>
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
                <h2 class="mt-4 mb-4"><i class="fas fa-plus"></i> Add Team Member</h2>

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
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="name" name="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="position" class="form-label">Position <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="position" name="position" required value="<?php echo htmlspecialchars($_POST['position'] ?? ''); ?>" placeholder="e.g., Lead Technician">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="phone" class="form-label">Phone</label>
                                            <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="skills" class="form-label">Skills</label>
                                        <input type="text" class="form-control" id="skills" name="skills" value="<?php echo htmlspecialchars($_POST['skills'] ?? ''); ?>" placeholder="e.g., Smartphone Repair, PC Hardware">
                                        <small class="text-muted">Separate multiple skills with commas</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="bio" class="form-label">Bio</label>
                                        <textarea class="form-control" id="bio" name="bio" rows="4"><?php echo htmlspecialchars($_POST['bio'] ?? ''); ?></textarea>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="position_order" class="form-label">Display Order</label>
                                            <input type="number" class="form-control" id="position_order" name="position_order" min="0" value="<?php echo htmlspecialchars($_POST['position_order'] ?? '0'); ?>">
                                            <small class="text-muted">Lower numbers appear first</small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <select class="form-select" id="status" name="status">
                                                <option value="active" <?php echo ($_POST['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>Active</option>
                                                <option value="inactive" <?php echo ($_POST['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label for="image" class="form-label">Profile Photo</label>
                                    <div class="card border-secondary">
                                        <div class="card-body text-center">
                                            <div id="imagePreview">
                                                <i class="fas fa-image" style="font-size: 80px; color: #ccc;"></i>
                                                <p class="text-muted mt-2">No image selected</p>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="file" class="form-control mt-2" id="image" name="image" accept="image/*" onchange="previewImage(event)">
                                    <small class="text-muted">Max 5MB. Formats: JPG, PNG, GIF, WebP</small>
                                </div>
                            </div>

                            <div class="d-flex gap-2 mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Add Team Member
                                </button>
                                <a href="team.php" class="btn btn-secondary">
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
    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('imagePreview');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = '<img src="' + e.target.result + '" style="max-width: 200px; border-radius: 10px;" alt="Preview">';
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>
