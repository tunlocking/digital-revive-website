<?php
// API endpoint for fetching dynamic website content
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'get_settings':
            // Get all website settings
            $stmt = $conn->query("SELECT setting_key, setting_value FROM settings");
            $settings = [];
            while ($row = $stmt->fetch()) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
            echo json_encode(['success' => true, 'data' => $settings]);
            break;

        case 'get_services':
            // Get all active services
            $stmt = $conn->query("
                SELECT id, name, slug, description, price, estimated_days, category 
                FROM services 
                WHERE status = 'active' 
                ORDER BY category, name
            ");
            $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $services]);
            break;

        case 'get_products':
            // Get all active products
            $stmt = $conn->query("
                SELECT p.id, p.name, p.slug, p.description, p.price, p.image_path, c.name as category
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'active'
                ORDER BY c.name, p.name
            ");
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $products]);
            break;

        case 'get_blog_posts':
            // Get published blog posts
            $limit = intval($_GET['limit'] ?? 10);
            $stmt = $conn->prepare("
                SELECT id, title, slug, content, featured_image, created_at
                FROM blog_posts
                WHERE published = TRUE
                ORDER BY created_at DESC
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $posts]);
            break;

        case 'get_team':
            // Get team members
            $stmt = $conn->query("
                SELECT id, name, position, bio, image_path
                FROM team_members
                WHERE status = 'active'
                ORDER BY position_order
            ");
            $team = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $team]);
            break;

        case 'get_social_links':
            // Get social media links
            $stmt = $conn->query("
                SELECT platform, url, icon
                FROM social_links
                WHERE status = 'active'
                ORDER BY platform
            ");
            $socials = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $socials]);
            break;

        case 'get_testimonials':
            // Get testimonials
            $stmt = $conn->query("
                SELECT id, client_name, message, rating
                FROM testimonials
                WHERE status = 'active'
                ORDER BY created_at DESC
            ");
            $testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $testimonials]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
