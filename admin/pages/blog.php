<?php
// Manage Blog Posts
require_once '../../config/db.php';

// Fetch all blog posts from database
// $stmt = $conn->prepare("SELECT * FROM blog_posts ORDER BY created_at DESC");
// $stmt->execute();
// $posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Blog - Digital Revive</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Blog Posts</h2>
            <a href="add_blog.php" class="btn btn-primary">Add Post</a>
        </div>
        
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Blog posts will be listed here -->
            </tbody>
        </table>
    </div>
</body>
</html>
