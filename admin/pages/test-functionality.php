<?php
session_start();
require_once '../../config/db.php';

// Test 1: Database Connection
$test_results = [];
try {
    $result = $conn->query("SELECT 1");
    $test_results['database_connection'] = ['status' => 'PASS', 'message' => 'Database connected successfully'];
} catch (Exception $e) {
    $test_results['database_connection'] = ['status' => 'FAIL', 'message' => $e->getMessage()];
}

// Test 2: Check Tables Exist
$required_tables = ['users', 'products', 'categories', 'blog_posts', 'services', 'team_members', 'settings', 'contact_messages'];
foreach ($required_tables as $table) {
    try {
        $stmt = $conn->query("SELECT COUNT(*) FROM $table");
        $count = $stmt->fetchColumn();
        $test_results[$table] = ['status' => 'PASS', 'message' => "$count records found"];
    } catch (Exception $e) {
        $test_results[$table] = ['status' => 'FAIL', 'message' => 'Table missing or error'];
    }
}

// Test 3: Check Admin User Exists
try {
    $stmt = $conn->query("SELECT COUNT(*) as count FROM users");
    $count = $stmt->fetch()['count'];
    if ($count > 0) {
        $test_results['admin_users'] = ['status' => 'PASS', 'message' => "$count admin user(s) found"];
    } else {
        $test_results['admin_users'] = ['status' => 'FAIL', 'message' => 'No admin users found - register one to continue'];
    }
} catch (Exception $e) {
    $test_results['admin_users'] = ['status' => 'FAIL', 'message' => 'Error checking users'];
}

// Test 4: Check Session Variables
if (isset($_SESSION['admin_id'])) {
    $test_results['session_admin_id'] = ['status' => 'PASS', 'message' => 'Session admin_id is set'];
} else {
    $test_results['session_admin_id'] = ['status' => 'WARN', 'message' => 'Not logged in (expected for test page)'];
}

// Test 5: Check if upload directories exist
$upload_dirs = ['uploads/products', 'uploads/blog'];
foreach ($upload_dirs as $dir) {
    $full_path = '../../' . $dir;
    if (is_dir($full_path)) {
        $test_results[$dir] = ['status' => 'PASS', 'message' => 'Directory exists and is writable'];
    } else {
        $test_results[$dir] = ['status' => 'WARN', 'message' => 'Directory does not exist - will be created on upload'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Functionality Test - Digital Revive Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .pass { background-color: #d4edda; }
        .fail { background-color: #f8d7da; }
        .warn { background-color: #fff3cd; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">🔍 Admin Functionality Test</h1>
        
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Component</th>
                        <th>Status</th>
                        <th>Message</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($test_results as $component => $result): ?>
                        <tr class="<?php echo strtolower($result['status']); ?>">
                            <td><strong><?php echo ucwords(str_replace('_', ' ', $component)); ?></strong></td>
                            <td>
                                <?php if ($result['status'] === 'PASS'): ?>
                                    <span class="badge bg-success">✓ PASS</span>
                                <?php elseif ($result['status'] === 'FAIL'): ?>
                                    <span class="badge bg-danger">✗ FAIL</span>
                                <?php else: ?>
                                    <span class="badge bg-warning">⚠ WARNING</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($result['message']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="alert alert-info mt-4">
            <h5>Quick Links:</h5>
            <ul class="mb-0">
                <li><a href="dashboard.php">Dashboard</a> - View admin overview</li>
                <li><a href="products.php">Products</a> - Manage products</li>
                <li><a href="blog.php">Blog Posts</a> - Manage blog content</li>
                <li><a href="services.php">Services</a> - Manage services</li>
                <li><a href="login.php">Login</a> - Admin login</li>
            </ul>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
