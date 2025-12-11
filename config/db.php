<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'digital_revive');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_PORT', 3306);

// PDO Connection
try {
    $conn = new PDO(
        "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch(PDOException $e) {
    die('Database Connection Error: ' . $e->getMessage());
}

// Helper function to test connection
function testDatabaseConnection() {
    global $conn;
    try {
        $result = $conn->query("SELECT 1");
        return true;
    } catch(PDOException $e) {
        return false;
    }
}
?>
