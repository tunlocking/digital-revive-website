<?php
// Edit Blog Post
require_once '../../config/db.php';

$post_id = $_GET['id'] ?? null;

if ($post_id) {
    // Fetch post from database
    // $stmt = $conn->prepare("SELECT * FROM blog_posts WHERE id = ?");
    // $stmt->execute([$post_id]);
    // $post = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category = $_POST['category'];
    $author = $_POST['author'];
    
    // Update in database
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
