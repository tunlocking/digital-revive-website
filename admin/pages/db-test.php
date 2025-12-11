<?php
// Database Test & Connection Page
require_once '../../config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Connection Test - Digital Revive</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <h2 class="mb-4">Database Connection Test</h2>
                
                <?php
                // Test database connection
                try {
                    $result = $conn->query("SELECT 1");
                    $connected = true;
                } catch(Exception $e) {
                    $connected = false;
                    $error = $e->getMessage();
                }
                ?>
                
                <!-- Connection Status -->
                <div class="card mb-4">
                    <div class="card-header <?php echo $connected ? 'bg-success' : 'bg-danger'; ?> text-white">
                        <h5 class="mb-0">
                            <?php echo $connected ? '✅ Database Connected' : '❌ Database Connection Failed'; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Database Host:</strong> localhost</p>
                        <p><strong>Database Name:</strong> digital_revive</p>
                        <p><strong>Username:</strong> root</p>
                        <?php if (!$connected): ?>
                            <div class="alert alert-danger">
                                <strong>Error:</strong> <?php echo htmlspecialchars($error ?? 'Unknown error'); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Database Tables -->
                <?php if ($connected): ?>
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Database Tables</h5>
                        </div>
                        <div class="card-body">
                            <?php
                            try {
                                $tables = $conn->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
                                
                                if (!empty($tables)) {
                                    echo '<table class="table table-sm table-striped">';
                                    echo '<thead><tr><th>Table Name</th><th>Record Count</th></tr></thead>';
                                    echo '<tbody>';
                                    
                                    foreach ($tables as $table) {
                                        $count = $conn->query("SELECT COUNT(*) FROM " . $table)->fetchColumn();
                                        echo "<tr><td><code>$table</code></td><td>$count records</td></tr>";
                                    }
                                    
                                    echo '</tbody></table>';
                                    echo '<p class="text-success"><strong>✅ Total Tables:</strong> ' . count($tables) . '</p>';
                                } else {
                                    echo '<div class="alert alert-warning">No tables found. Run the database.sql script.</div>';
                                }
                            } catch(Exception $e) {
                                echo '<div class="alert alert-danger">Error fetching tables: ' . htmlspecialchars($e->getMessage()) . '</div>';
                            }
                            ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Instructions -->
                <div class="card mt-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Setup Instructions</h5>
                    </div>
                    <div class="card-body">
                        <ol>
                            <li>Install <strong>XAMPP</strong> from <a href="https://www.apachefriends.org/" target="_blank">https://www.apachefriends.org/</a></li>
                            <li>Start <strong>MySQL</strong> and <strong>Apache</strong> from XAMPP Control Panel</li>
                            <li>Open phpMyAdmin: <a href="http://localhost/phpmyadmin" target="_blank">http://localhost/phpmyadmin</a></li>
                            <li>Create a new database named <strong>digital_revive</strong></li>
                            <li>Import <strong>config/database.sql</strong> into the database</li>
                            <li>Place this project in <strong>htdocs</strong> folder</li>
                            <li>Access admin at: <a href="http://localhost/digital-revive-website/admin/pages/login.php" target="_blank">http://localhost/digital-revive-website/admin/pages/login.php</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
