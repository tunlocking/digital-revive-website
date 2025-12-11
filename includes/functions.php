<?php
/**
 * Global Helper Functions
 * Digital Revive Website
 */

// Get all website settings (cached in session)
function get_site_settings($conn) {
    if (isset($_SESSION['site_settings'])) {
        return $_SESSION['site_settings'];
    }
    
    try {
        $stmt = $conn->query("SELECT setting_key, setting_value FROM settings");
        $settings = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        $_SESSION['site_settings'] = $settings;
        return $settings;
    } catch (Exception $e) {
        return [];
    }
}

// Format date for display
function format_date($date_string, $format = 'M d, Y') {
    try {
        $date = new DateTime($date_string);
        return $date->format($format);
    } catch (Exception $e) {
        return $date_string;
    }
}

// Truncate text with ellipsis
function truncate_text($text, $length = 150, $ellipsis = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $ellipsis;
}

// Get page meta description
function get_meta_description($page) {
    $descriptions = [
        'home' => 'Professional device repair services. Expert technicians for smartphones, tablets, and computers.',
        'services' => 'Our repair services including screen replacement, battery repair, water damage restoration, and more.',
        'products' => 'Browse our selection of repair parts, tools, and accessories for device maintenance.',
        'blog' => 'Latest tips, tricks, and news about device repair and maintenance.',
        'contact' => 'Get in touch with us for quick repairs and professional service.',
    ];
    return $descriptions[$page] ?? '';
}

// Check if user is admin (for frontend display purposes)
function is_admin() {
    return isset($_SESSION['admin_id']);
}

// Get safe URL for redirects
function safe_redirect_url($url, $fallback = './') {
    if (empty($url)) return $fallback;
    
    // Only allow relative URLs
    if (strpos($url, 'http') === 0) {
        return $fallback;
    }
    
    return filter_var($url, FILTER_VALIDATE_URL) ? $url : $fallback;
}

// Log errors securely
function log_error($message, $context = []) {
    $log_file = __DIR__ . '/../logs/error.log';
    
    // Create logs directory if doesn't exist
    if (!is_dir(dirname($log_file))) {
        mkdir(dirname($log_file), 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $context_str = !empty($context) ? ' | ' . json_encode($context) : '';
    $log_message = "[$timestamp] $message$context_str\n";
    
    error_log($log_message, 3, $log_file);
}

// Sanitize file path
function sanitize_file_path($path) {
    // Remove any path traversal attempts
    $path = str_replace(['../', '..\\', '..'], '', $path);
    return filter_var($path, FILTER_SANITIZE_URL);
}

// Get image URL with fallback
function get_image_url($image_path, $fallback = '/images/placeholder.png') {
    if (empty($image_path)) {
        return $fallback;
    }
    
    $safe_path = sanitize_file_path($image_path);
    if (file_exists(__DIR__ . '/../' . $safe_path)) {
        return $safe_path;
    }
    
    return $fallback;
}

// Format currency
function format_currency($amount, $currency = 'MAD') {
    return number_format(floatval($amount), 2, '.', ',') . ' ' . $currency;
}

// Validate phone number
function is_valid_phone($phone) {
    return preg_match('/^[\d\s\-\+\(\)]{10,}$/', $phone);
}

// Convert markdown-like text to HTML (basic)
function markdown_to_html($text) {
    // Basic markdown conversion
    $text = htmlspecialchars($text);
    $text = preg_replace('/\*\*(.*?)\*\*/m', '<strong>$1</strong>', $text);
    $text = preg_replace('/\*(.*?)\*/m', '<em>$1</em>', $text);
    $text = preg_replace('/\n\n/', '</p><p>', $text);
    return '<p>' . $text . '</p>';
}

?>
