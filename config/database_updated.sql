-- Digital Revive Database Schema - Complete Version
-- Create Database
CREATE DATABASE IF NOT EXISTS digital_revive;
USE digital_revive;

-- ========================================
-- USERS TABLE (Admin Users)
-- ========================================
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'editor') DEFAULT 'editor',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ========================================
-- CATEGORIES TABLE
-- ========================================
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ========================================
-- PRODUCTS TABLE
-- ========================================
CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(150) UNIQUE NOT NULL,
    category_id INT NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock_quantity INT DEFAULT 0,
    image_path VARCHAR(255),
    status ENUM('active', 'inactive', 'discontinued') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- ========================================
-- BLOG CATEGORIES TABLE
-- ========================================
CREATE TABLE IF NOT EXISTS blog_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ========================================
-- BLOG POSTS TABLE
-- ========================================
CREATE TABLE IF NOT EXISTS blog_posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    category_id INT,
    author_id INT NOT NULL,
    content LONGTEXT NOT NULL,
    featured_image VARCHAR(255),
    published BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE SET NULL,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ========================================
-- SERVICES TABLE
-- ========================================
CREATE TABLE IF NOT EXISTS services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(150) UNIQUE NOT NULL,
    description TEXT,
    detailed_description LONGTEXT,
    category VARCHAR(100),
    price DECIMAL(10, 2),
    estimated_days INT,
    icon VARCHAR(100),
    image_path VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ========================================
-- SETTINGS TABLE (Website Configuration)
-- ========================================
CREATE TABLE IF NOT EXISTS settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value LONGTEXT,
    setting_type ENUM('text', 'number', 'email', 'phone', 'textarea', 'url') DEFAULT 'text',
    description VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ========================================
-- TEAM MEMBERS TABLE
-- ========================================
CREATE TABLE IF NOT EXISTS team_members (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(150) NOT NULL,
    position VARCHAR(100),
    bio TEXT,
    skills VARCHAR(255),
    image_path VARCHAR(255),
    phone VARCHAR(20),
    email VARCHAR(100),
    position_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ========================================
-- SOCIAL LINKS TABLE
-- ========================================
CREATE TABLE IF NOT EXISTS social_links (
    id INT PRIMARY KEY AUTO_INCREMENT,
    platform VARCHAR(50) NOT NULL,
    url VARCHAR(255),
    icon VARCHAR(100),
    order_num INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ========================================
-- TESTIMONIALS TABLE
-- ========================================
CREATE TABLE IF NOT EXISTS testimonials (
    id INT PRIMARY KEY AUTO_INCREMENT,
    client_name VARCHAR(150) NOT NULL,
    client_position VARCHAR(100),
    client_image VARCHAR(255),
    message TEXT NOT NULL,
    rating INT DEFAULT 5,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ========================================
-- ORDERS TABLE
-- ========================================
CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ========================================
-- REPAIR ORDERS TABLE
-- ========================================
CREATE TABLE IF NOT EXISTS repair_orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    service_id INT,
    customer_name VARCHAR(150) NOT NULL,
    customer_email VARCHAR(100),
    customer_phone VARCHAR(20) NOT NULL,
    device_description TEXT,
    issue_description TEXT,
    cost DECIMAL(10, 2),
    status ENUM('pending', 'processing', 'completed', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL
);

-- ========================================
-- CUSTOMERS TABLE
-- ========================================
CREATE TABLE IF NOT EXISTS customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(100),
    country VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ========================================
-- INSERT DEFAULT WEBSITE SETTINGS
-- ========================================
INSERT IGNORE INTO settings (setting_key, setting_value, setting_type, description) VALUES
('site_name', 'Digital Revive', 'text', 'Website Title'),
('site_email', 'contact@digitalrevive.com', 'email', 'Contact Email'),
('site_phone', '+1234567890', 'phone', 'Contact Phone Number'),
('whatsapp_number', '+1234567890', 'phone', 'WhatsApp Number'),
('instagram_handle', '@digitalrevive', 'text', 'Instagram Handle'),
('site_address', '123 Tech Street, City, Country', 'textarea', 'Business Address'),
('business_hours', 'Mon-Fri: 9AM-6PM, Sat: 10AM-4PM', 'textarea', 'Business Hours'),
('about_description', 'We are dedicated to reviving your digital devices', 'textarea', 'About Us Description'),
('header_title', 'Welcome to Digital Revive', 'text', 'Header Title'),
('header_subtitle', 'Professional Device Repair Services', 'text', 'Header Subtitle'),
('facebook_url', 'https://facebook.com/digitalrevive', 'url', 'Facebook URL'),
('twitter_url', 'https://twitter.com/digitalrevive', 'url', 'Twitter URL'),
('linkedin_url', 'https://linkedin.com/company/digitalrevive', 'url', 'LinkedIn URL');

-- ========================================
-- INSERT DEFAULT SERVICES
-- ========================================
INSERT IGNORE INTO services (name, slug, description, category, price, estimated_days, icon, status) VALUES
('Smartphone Screen Repair', 'smartphone-screen-repair', 'Professional smartphone screen repair service', 'Smartphones', 89.99, 1, 'fas fa-mobile-alt', 'active'),
('Battery Replacement', 'battery-replacement', 'Quality battery replacement for all devices', 'General', 49.99, 2, 'fas fa-battery-full', 'active'),
('Water Damage Repair', 'water-damage-repair', 'Expert water damage restoration service', 'General', 129.99, 3, 'fas fa-water', 'active'),
('PC Motherboard Repair', 'pc-motherboard-repair', 'Computer motherboard diagnosis and repair', 'Computers', 149.99, 3, 'fas fa-microchip', 'active'),
('Phone Charging Port Repair', 'phone-charging-port', 'Charging port replacement and repair', 'Smartphones', 59.99, 1, 'fas fa-plug', 'active'),
('Electronics Repair', 'electronics-repair', 'General electronics repair and troubleshooting', 'General', 79.99, 2, 'fas fa-tools', 'active');

-- ========================================
-- INSERT DEFAULT TEAM MEMBERS
-- ========================================
INSERT IGNORE INTO team_members (name, position, bio, skills, phone, email, position_order, status) VALUES
('Ahmed Hassan', 'Founder & Lead Technician', 'Expert in smartphone and computer repairs with 10+ years experience', 'Smartphone Repair, PC Hardware, Diagnostics', '+1234567890', 'ahmed@digitalrevive.com', 1, 'active'),
('Fatima Ali', 'Head of Service', 'Specializes in water damage and complex device restoration', 'Water Damage Repair, Electronics, Problem Solving', '+1234567891', 'fatima@digitalrevive.com', 2, 'active'),
('Muhammad Khan', 'Senior Technician', 'Expert in PC and laptop repairs with certification', 'Computer Hardware, Software, Networking', '+1234567892', 'muhammad@digitalrevive.com', 3, 'active');

-- ========================================
-- INSERT DEFAULT TESTIMONIALS
-- ========================================
INSERT IGNORE INTO testimonials (client_name, client_position, message, rating, status) VALUES
('Sarah Johnson', 'Small Business Owner', 'Excellent service! Repaired my phone screen in less than an hour. Highly recommended!', 5, 'active'),
('Robert Martinez', 'Student', 'Very professional team. My laptop is working like new again. Great prices too!', 5, 'active'),
('Lisa Chen', 'IT Manager', 'Outstanding technical expertise. They fixed issues other shops couldn\'t handle. Impressed!', 5, 'active');

-- ========================================
-- INSERT DEFAULT SOCIAL LINKS
-- ========================================
INSERT IGNORE INTO social_links (platform, url, icon, order_num, status) VALUES
('Facebook', 'https://facebook.com/digitalrevive', 'fab fa-facebook-f', 1, 'active'),
('Twitter', 'https://twitter.com/digitalrevive', 'fab fa-twitter', 2, 'active'),
('Instagram', 'https://instagram.com/digitalrevive', 'fab fa-instagram', 3, 'active'),
('LinkedIn', 'https://linkedin.com/company/digitalrevive', 'fab fa-linkedin-in', 4, 'active'),
('WhatsApp', 'https://wa.me/1234567890', 'fab fa-whatsapp', 5, 'active');

-- ========================================
-- INSERT DEFAULT CATEGORIES
-- ========================================
INSERT IGNORE INTO categories (name, slug, description) VALUES
('Smartphones', 'smartphones', 'Mobile phone accessories and repairs'),
('Computers', 'computers', 'Computer and laptop parts'),
('Tablets', 'tablets', 'Tablet devices and accessories');

-- ========================================
-- INSERT DEFAULT PRODUCTS
-- ========================================
INSERT IGNORE INTO products (name, slug, category_id, description, price, stock_quantity, status) VALUES
('iPhone Screen Protector', 'iphone-screen-protector', 1, 'Tempered glass screen protector for iPhone', 9.99, 100, 'active'),
('Phone Case', 'phone-case', 1, 'Premium protective phone case', 19.99, 150, 'active'),
('Laptop Battery', 'laptop-battery', 2, 'High capacity replacement battery', 79.99, 50, 'active'),
('HDMI Cable', 'hdmi-cable', 2, 'High-speed HDMI 2.1 cable', 14.99, 200, 'active'),
('USB Charger', 'usb-charger', 3, 'Fast charging USB-C charger', 24.99, 120, 'active');

-- ========================================
-- INSERT DEFAULT ADMIN USER
-- ========================================
INSERT IGNORE INTO users (username, email, password, role) VALUES
('admin', 'admin@digitalrevive.com', '$2y$10$YOixghkjashkjashjkashkjashkjashkjashkjashkjashjkasjk', 'admin');

-- ========================================
-- INSERT DEFAULT BLOG CATEGORIES
-- ========================================
INSERT IGNORE INTO blog_categories (name, slug, description) VALUES
('Tips & Tricks', 'tips-tricks', 'Device care and maintenance tips'),
('News', 'news', 'Latest technology news'),
('Tutorials', 'tutorials', 'How-to guides and tutorials'),
('Announcements', 'announcements', 'Company announcements');

-- ========================================
-- INSERT SAMPLE BLOG POST
-- ========================================
INSERT IGNORE INTO blog_posts (title, slug, category_id, author_id, content, published) VALUES
('How to Protect Your Smartphone from Water Damage', 'protect-phone-water-damage', 1, 1, '<h3>Introduction</h3><p>Water damage is one of the most common issues affecting smartphones today. Here are some proven methods to protect your device.</p><h3>Best Practices</h3><ul><li>Use a waterproof case</li><li>Avoid using near water sources</li><li>Keep your phone dry at all times</li></ul><h3>Conclusion</h3><p>By following these tips, you can significantly reduce the risk of water damage to your smartphone.</p>', TRUE);

-- ========================================
-- CREATE INDEXES FOR PERFORMANCE
-- ========================================
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_products_status ON products(status);
CREATE INDEX idx_blog_published ON blog_posts(published);
CREATE INDEX idx_blog_category ON blog_posts(category_id);
CREATE INDEX idx_blog_author ON blog_posts(author_id);
CREATE INDEX idx_services_status ON services(status);
CREATE INDEX idx_team_status ON team_members(status);
CREATE INDEX idx_testimonials_status ON testimonials(status);
CREATE INDEX idx_social_status ON social_links(status);
CREATE INDEX idx_users_email ON users(email);
