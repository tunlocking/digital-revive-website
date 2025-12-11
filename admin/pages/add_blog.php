<?php
session_start();
require_once '../../config/db.php';

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $slug = strtolower(preg_replace('/[^a-z0-9-]+/', '-', $title));
    
    // Validation
    if (empty($title) || empty($content)) {
        $error = 'Title and content are required.';
    } else {
        try {
            // Handle file upload
            $featured_image = null;
            if (!empty($_FILES['featured_image']['name'])) {
                $file = $_FILES['featured_image'];
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $filename = basename($file['name']);
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                
                if (!in_array($ext, $allowed)) {
                    throw new Exception('Invalid file type. Only JPG, PNG, GIF, WEBP allowed.');
                }
                
                if ($file['size'] > 5000000) {
                    throw new Exception('File too large. Max 5MB allowed.');
                }
                
                $upload_dir = '../../uploads/blog/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $new_filename = time() . '_' . preg_replace('/[^a-z0-9_-]/i', '_', $filename);
                if (move_uploaded_file($file['tmp_name'], $upload_dir . $new_filename)) {
                    $featured_image = 'uploads/blog/' . $new_filename;
                }
            }
            
            // Insert blog post
            $stmt = $conn->prepare("
                INSERT INTO blog_posts (title, slug, content, featured_image, author_id, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, NOW(), NOW())
            ");
            
            $stmt->execute([$title, $slug, $content, $featured_image, $_SESSION['admin_id']]);
            
            $success = 'Blog post added successfully!';
            $_SESSION['message'] = $success;
            $_SESSION['message_type'] = 'success';
            header('Location: blog.php');
            exit();
            
        } catch(Exception $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Blog Post - Digital Revive</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
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
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <h4 class="text-white px-3 mb-4">Digital Revive</h4>
                <a href="dashboard.php">
                    <i class="fas fa-chart-line"></i> Dashboard
                </a>
                <a href="products.php">
                    <i class="fas fa-box"></i> Products
                </a>
                <a href="blog.php" class="active">
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
            <div class="col-md-10 p-4">
                <h2 class="mb-4"><i class="fas fa-plus me-2"></i>Add New Blog Post</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="title" placeholder="Enter blog post title" value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" required>
                                <small class="form-text">This will be the main heading of your blog post</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Content <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="content" rows="10" placeholder="Write your blog post content here..." required><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
                                <small class="form-text">HTML and Markdown tags are supported</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Featured Image</label>
                                <div class="file-upload-wrapper">
                                    <input type="file" id="featuredImage" name="featured_image" accept="image/*" class="form-control">
                                    <small class="form-text d-block mt-2">Max file size: 5MB. Allowed formats: JPG, PNG, GIF, WEBP</small>
                                </div>
                                <img id="featuredPreview" style="display:none;" class="img-fluid mt-3" alt="Featured preview">
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Publish Post
                                </button>
                                <a href="blog.php" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/admin.js"></script>
    <script>
        // Image preview
        document.getElementById('featuredImage').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('featuredPreview').src = event.target.result;
                    document.getElementById('featuredPreview').style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
            <button type="submit" class="btn btn-primary">Publish Post</button>
            <a href="blog.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
