<?php
session_start();
require_once '../../config/db.php';
require_once '../../admin/includes/security.php';

// Check admin access
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$id = intval($_GET['id'] ?? 0);
if ($id === 0) {
    header('Location: team.php');
    exit;
}

// Get team member
$stmt = $conn->prepare("SELECT * FROM team_members WHERE id = ?");
$stmt->execute([$id]);
$member = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$member) {
    header('Location: team.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Security token verification failed. Please try again.';
    } else {
    $name = sanitize_input($_POST['name'] ?? '');
    $position = sanitize_input($_POST['position'] ?? '');
    $bio = sanitize_input($_POST['bio'] ?? '');
    $skills = sanitize_input($_POST['skills'] ?? '');
    $phone = sanitize_input($_POST['phone'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL) ? sanitize_input($_POST['email'] ?? '') : '';
    $status = $_POST['status'] ?? 'active';
    $position_order = intval($_POST['position_order'] ?? 0);
    $image_path = $member['image_path'];

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
            // Delete old image
            if ($member['image_path'] && file_exists('../../uploads/' . $member['image_path'])) {
                unlink('../../uploads/' . $member['image_path']);
            }

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
            UPDATE team_members SET name = ?, position = ?, bio = ?, skills = ?, phone = ?, email = ?, image_path = ?, status = ?, position_order = ?
            WHERE id = ?
        ");
        $stmt->execute([$name, $position, $bio, $skills, $phone, $email, $image_path, $status, $position_order, $id]);
        header('Location: team.php?success=1');
        exit;
    }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Team Member - Digital Revive Admin</title>
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
                <h2 class="mt-4 mb-4"><i class="fas fa-edit"></i> Edit Team Member</h2>

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
                            <?php csrf_input(); ?>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="name" name="name" required value="<?php echo htmlspecialchars($member['name']); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="position" class="form-label">Position <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="position" name="position" required value="<?php echo htmlspecialchars($member['position']); ?>">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($member['email']); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="phone" class="form-label">Phone</label>
                                            <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($member['phone']); ?>">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="skills" class="form-label">Skills</label>
                                        <input type="text" class="form-control" id="skills" name="skills" value="<?php echo htmlspecialchars($member['skills']); ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label for="bio" class="form-label">Bio</label>
                                        <textarea class="form-control" id="bio" name="bio" rows="4"><?php echo htmlspecialchars($member['bio']); ?></textarea>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="position_order" class="form-label">Display Order</label>
                                            <input type="number" class="form-control" id="position_order" name="position_order" min="0" value="<?php echo htmlspecialchars($member['position_order']); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <select class="form-select" id="status" name="status">
                                                <option value="active" <?php echo $member['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                                <option value="inactive" <?php echo $member['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label for="image" class="form-label">Profile Photo</label>
                                    <div class="card border-secondary">
                                        <div class="card-body text-center">
                                            <div id="imagePreview">
                                                <?php if ($member['image_path']): ?>
                                                    <img src="../../uploads/<?php echo htmlspecialchars($member['image_path']); ?>" style="max-width: 200px; border-radius: 10px;" alt="Preview">
                                                <?php else: ?>
                                                    <i class="fas fa-image" style="font-size: 80px; color: #ccc;"></i>
                                                    <p class="text-muted mt-2">No image</p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="file" class="form-control mt-2" id="image" name="image" accept="image/*" onchange="previewImage(event)">
                                    <small class="text-muted">Max 5MB. Formats: JPG, PNG, GIF, WebP</small>
                                </div>
                            </div>

                            <div class="d-flex gap-2 mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Team Member
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
