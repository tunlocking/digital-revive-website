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
$blog_post = null;
$blog_id = intval($_GET['id'] ?? 0);

if (!$blog_id) {
    header('Location: blog.php');
    exit();
}

try {
    // Get blog post
    $stmt = $conn->prepare("SELECT * FROM blog_posts WHERE id = ?");
    $stmt->execute([$blog_id]);
    $blog_post = $stmt->fetch();
    
    if (!$blog_post) {
        header('Location: blog.php');
        exit();
    }
} catch(PDOException $e) {
    $error = 'Error loading blog post: ' . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $slug = strtolower(preg_replace('/[^a-z0-9-]+/', '-', $title));
    
    // Validation
    if (empty($title) || empty($content)) {
        $error = 'Title and content are required.';
    } else {
        try {
            $banner_image = $blog_post['banner_image'];
            
            // Handle file upload
            if (!empty($_FILES['banner_image']['name'])) {
                $file = $_FILES['banner_image'];
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
                    // Delete old image
                    if ($blog_post['banner_image'] && file_exists('../../' . $blog_post['banner_image'])) {
                        unlink('../../' . $blog_post['banner_image']);
                    }
                    $banner_image = 'uploads/blog/' . $new_filename;
                }
            }
            
            // Update blog post
            $stmt = $conn->prepare("
                UPDATE blog_posts 
                SET title = ?, slug = ?, content = ?, banner_image = ?, updated_at = NOW()
                WHERE id = ?
            ");
            
            $stmt->execute([$title, $slug, $content, $banner_image, $blog_id]);
            
            $_SESSION['message'] = 'Blog post updated successfully!';
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
    <title>Edit Blog Post - Digital Revive</title>
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
                <h2 class="mb-4"><i class="fas fa-edit me-2"></i>Edit Blog Post</h2>
                
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
                                <input type="text" class="form-control" name="title" placeholder="Enter blog post title" value="<?php echo htmlspecialchars($blog_post['title'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Content <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="content" rows="10" placeholder="Write your blog post content here..." required><?php echo htmlspecialchars($blog_post['content'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Featured Image</label>
                                <div class="file-upload-wrapper">
                                    <input type="file" id="bannerImage" name="banner_image" accept="image/*" class="form-control">
                                    <small class="form-text d-block mt-2">Max file size: 5MB. Allowed formats: JPG, PNG, GIF, WEBP</small>
                                </div>
                                <?php if ($blog_post['banner_image']): ?>
                                    <div class="mt-3">
                                        <img src="../../<?php echo $blog_post['banner_image']; ?>" class="img-fluid" style="max-height: 200px;" alt="Current banner">
                                        <small class="d-block mt-2 text-muted">Current image. Upload a new one to replace.</small>
                                    </div>
                                <?php endif; ?>
                                <img id="bannerPreview" style="display:none;" class="img-fluid mt-3" alt="Banner preview">
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Post
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
        document.getElementById('bannerImage').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('bannerPreview').src = event.target.result;
                    document.getElementById('bannerPreview').style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
    // $stmt = $conn->prepare("UPDATE blog_posts SET title = ?, content = ?, category = ?, author = ? WHERE id = ?");
    // $stmt->execute([$title, $content, $category, $author, $post_id]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Blog Post - Digital Revive</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Edit Blog Post</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" class="form-control" name="title" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Author</label>
                <input type="text" class="form-control" name="author" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Category</label>
                <input type="text" class="form-control" name="category">
            </div>
            <div class="mb-3">
                <label class="form-label">Content</label>
                <textarea class="form-control" name="content" rows="8" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update Post</button>
            <a href="blog.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
