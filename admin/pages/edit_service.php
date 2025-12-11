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
    header('Location: services.php');
    exit;
}

// Get service
$stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
$stmt->execute([$id]);
$service = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$service) {
    header('Location: services.php');
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Security token verification failed. Please try again.';
    } else {
    $name = sanitize_input($_POST['name'] ?? '');
    $slug = sanitize_input($_POST['slug'] ?? '');
    $category = sanitize_input($_POST['category'] ?? '');
    $description = sanitize_input($_POST['description'] ?? '');
    $detailed_desc = sanitize_input($_POST['detailed_description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $estimated_days = intval($_POST['estimated_days'] ?? 1);
    $status = sanitize_input($_POST['status'] ?? 'active');
    $icon = sanitize_input($_POST['icon'] ?? '');

    // Validation
    if (empty($name)) $errors[] = 'Service name is required';
    if (empty($slug)) $errors[] = 'Service slug is required';
    if (empty($category)) $errors[] = 'Category is required';
    if (empty($description)) $errors[] = 'Description is required';
    if ($price < 0) $errors[] = 'Price must be positive';

    // Check if slug already exists (different from current)
    if ($slug !== $service['slug']) {
        $check = $conn->prepare("SELECT id FROM services WHERE slug = ?");
        $check->execute([$slug]);
        if ($check->rowCount() > 0) {
            $errors[] = 'This slug already exists';
        }
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("
            UPDATE services SET name = ?, slug = ?, category = ?, description = ?, detailed_description = ?, price = ?, estimated_days = ?, status = ?, icon = ?
            WHERE id = ?
        ");
        $stmt->execute([$name, $slug, $category, $description, $detailed_desc, $price, $estimated_days, $status, $icon, $id]);
        header('Location: services.php?success=1');
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
    <title>Edit Service - Digital Revive Admin</title>
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
                <h2 class="mt-4 mb-4"><i class="fas fa-edit"></i> Edit Service</h2>

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
                            <?php csrf_input(); ?>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Service Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" required value="<?php echo htmlspecialchars($service['name']); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="slug" name="slug" required value="<?php echo htmlspecialchars($service['slug']); ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="category" name="category" required value="<?php echo htmlspecialchars($service['category']); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="icon" class="form-label">Icon (Font Awesome Class)</label>
                                    <input type="text" class="form-control" id="icon" name="icon" value="<?php echo htmlspecialchars($service['icon']); ?>" placeholder="e.g., fas fa-mobile-alt">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="price" class="form-label">Price ($)</label>
                                    <input type="number" class="form-control" id="price" name="price" step="0.01" value="<?php echo htmlspecialchars($service['price']); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="estimated_days" class="form-label">Estimated Days to Complete</label>
                                    <input type="number" class="form-control" id="estimated_days" name="estimated_days" min="1" value="<?php echo htmlspecialchars($service['estimated_days']); ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Short Description <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="description" name="description" rows="2" required><?php echo htmlspecialchars($service['description']); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="detailed_description" class="form-label">Detailed Description</label>
                                <textarea class="form-control" id="detailed_description" name="detailed_description" rows="4"><?php echo htmlspecialchars($service['detailed_description']); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="active" <?php echo $service['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo $service['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Service
                                </button>
                                <a href="services.php" class="btn btn-secondary">
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
