<?php
/**
 * Security & Validation Helper Functions
 * Digital Revive Admin Panel
 */

// Input Sanitization Functions
function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function sanitize_email($email) {
    return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
}

function sanitize_text($text) {
    return filter_var($text, FILTER_SANITIZE_STRING);
}

function sanitize_url($url) {
    return filter_var($url, FILTER_SANITIZE_URL);
}

// Validation Functions
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validate_password($password) {
    return strlen($password) >= 8;
}

function validate_phone($phone) {
    return preg_match('/^[\d\s\-\+\(\)]{10,}$/', $phone);
}

function validate_price($price) {
    return is_numeric($price) && floatval($price) > 0;
}

// CSRF Protection
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Render CSRF input field
function csrf_input() {
    echo '<input type="hidden" name="csrf_token" value="' . generate_csrf_token() . '">';
}

// File Upload Validation
function validate_image_upload($file) {
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return ['valid' => false, 'message' => 'No file selected'];
    }
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_ext)) {
        return ['valid' => false, 'message' => 'Invalid file type. Allowed: JPG, PNG, GIF, WEBP'];
    }
    
    if ($file['size'] > $max_size) {
        return ['valid' => false, 'message' => 'File too large. Max size: 5MB'];
    }
    
    // Check MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    $allowed_mime = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($mime, $allowed_mime)) {
        return ['valid' => false, 'message' => 'File is not a valid image'];
    }
    
    return ['valid' => true, 'message' => 'File is valid'];
}

// Secure file upload
function upload_file($file, $destination_dir) {
    // Validate
    $validation = validate_image_upload($file);
    if (!$validation['valid']) {
        throw new Exception($validation['message']);
    }
    
    // Create directory if doesn't exist
    if (!is_dir($destination_dir)) {
        mkdir($destination_dir, 0755, true);
    }
    
    // Generate unique filename
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = time() . '_' . uniqid() . '.' . $ext;
    $filepath = $destination_dir . $filename;
    
    // Move file
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new Exception('Failed to upload file');
    }
    
    return $filename;
}

// Password hashing
function hash_password($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
}

function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

// Session Management
function check_admin_logged_in() {
    if (!isset($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit();
    }
}

function logout() {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}

// Error handling
function show_error($message) {
    return '<div class="alert alert-danger alert-dismissible fade show" role="alert">' .
           htmlspecialchars($message) .
           '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
}

function show_success($message) {
    return '<div class="alert alert-success alert-dismissible fade show" role="alert">' .
           htmlspecialchars($message) .
           '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
}

function show_warning($message) {
    return '<div class="alert alert-warning alert-dismissible fade show" role="alert">' .
           htmlspecialchars($message) .
           '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
}

// Rate limiting (prevent brute force)
function check_rate_limit($identifier, $limit = 5, $window = 300) {
    $cache_key = 'rate_limit_' . md5($identifier);
    
    if (!isset($_SESSION[$cache_key])) {
        $_SESSION[$cache_key] = ['attempts' => 0, 'first_attempt' => time()];
    }
    
    $data = $_SESSION[$cache_key];
    $elapsed = time() - $data['first_attempt'];
    
    if ($elapsed > $window) {
        $_SESSION[$cache_key] = ['attempts' => 1, 'first_attempt' => time()];
        return true;
    }
    
    if ($data['attempts'] >= $limit) {
        return false;
    }
    
    $_SESSION[$cache_key]['attempts']++;
    return true;
}

// Input filtering for database queries
function filter_search_input($input) {
    return '%' . addslashes($input) . '%';
}

// Generate safe pagination
function get_pagination_offset($page, $per_page = 10) {
    $page = max(1, intval($page));
    return ($page - 1) * $per_page;
}

// Redirect with message
function redirect_with_message($url, $message, $type = 'success') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
    header('Location: ' . $url);
    exit();
}

// Display session message
function show_session_message() {
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        $type = $_SESSION['message_type'] ?? 'info';
        echo show_error($message) if ($type == 'error') : '';
        echo show_success($message) if ($type == 'success') : '';
        echo show_warning($message) if ($type == 'warning') : '';
        unset($_SESSION['message'], $_SESSION['message_type']);
    }
}

?>