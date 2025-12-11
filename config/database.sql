-- Digital Revive Database Schema
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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ========================================
-- BLOG POSTS TABLE
-- ========================================
CREATE TABLE IF NOT EXISTS blog_posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    content LONGTEXT NOT NULL,
    category_id INT,
    author_id INT NOT NULL,
    featured_image VARCHAR(255),
    excerpt VARCHAR(500),
    views INT DEFAULT 0,
    published BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE SET NULL,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ========================================
-- CUSTOMERS TABLE
-- ========================================
CREATE TABLE IF NOT EXISTS customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(50),
    postal_code VARCHAR(10),
    country VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ========================================
-- ORDERS TABLE
-- ========================================
CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    customer_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    payment_method VARCHAR(50),
    payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    shipping_address TEXT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    INDEX idx_order_number (order_number),
    INDEX idx_customer_id (customer_id),
    INDEX idx_status (status)
);

-- ========================================
-- ORDER ITEMS TABLE
-- ========================================
CREATE TABLE IF NOT EXISTS order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
);

-- ========================================
-- SERVICES TABLE
-- ========================================
CREATE TABLE IF NOT EXISTS services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(150) UNIQUE NOT NULL,
    description TEXT,
    price DECIMAL(10, 2),
    estimated_days INT,
    category VARCHAR(100),
    icon VARCHAR(100),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ========================================
-- REPAIR ORDERS TABLE
-- ========================================
CREATE TABLE IF NOT EXISTS repair_orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    repair_number VARCHAR(50) UNIQUE NOT NULL,
    customer_id INT,
    customer_email VARCHAR(100),
    customer_phone VARCHAR(20),
    device_type VARCHAR(50),
    device_brand VARCHAR(50),
    device_model VARCHAR(100),
    issue_description TEXT,
    service_id INT,
    estimated_completion_date DATE,
    status ENUM('received', 'diagnosed', 'approved', 'in_progress', 'quality_check', 'ready_for_pickup', 'completed', 'cancelled') DEFAULT 'received',
    estimated_cost DECIMAL(10, 2),
    final_cost DECIMAL(10, 2),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL,
    INDEX idx_repair_number (repair_number),
    INDEX idx_status (status),
    INDEX idx_customer_email (customer_email)
);

-- ========================================
-- REPAIR ORDER TRACKING TABLE
-- ========================================
CREATE TABLE IF NOT EXISTS repair_tracking (
    id INT PRIMARY KEY AUTO_INCREMENT,
    repair_order_id INT NOT NULL,
    status VARCHAR(50) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (repair_order_id) REFERENCES repair_orders(id) ON DELETE CASCADE
);

-- ========================================
-- CONTACTS/INQUIRIES TABLE
-- ========================================
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(200),
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied') DEFAULT 'new',
    reply_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    replied_at TIMESTAMP NULL,
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- ========================================
-- TESTIMONIALS TABLE
-- ========================================
CREATE TABLE IF NOT EXISTS testimonials (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100),
    rating INT CHECK (rating >= 1 AND rating <= 5),
    message TEXT NOT NULL,
    service_type VARCHAR(100),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    approved_at TIMESTAMP NULL
);

-- ========================================
-- GALLERY TABLE
-- ========================================
CREATE TABLE IF NOT EXISTS gallery (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(150),
    description TEXT,
    image_path VARCHAR(255) NOT NULL,
    category VARCHAR(50),
    sort_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ========================================
-- NEWSLETTER SUBSCRIBERS TABLE
-- ========================================
CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) UNIQUE NOT NULL,
    status ENUM('subscribed', 'unsubscribed') DEFAULT 'subscribed',
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    unsubscribed_at TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_status (status)
);

-- ========================================
-- SETTINGS TABLE
-- ========================================
CREATE TABLE IF NOT EXISTS settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value LONGTEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ========================================
-- INSERT DEFAULT DATA
-- ========================================

-- Insert default categories
INSERT INTO categories (name, slug, description) VALUES
('PC Accessories', 'pc-accessories', 'High-quality PC accessories and components'),
('Smartphones', 'smartphones', 'Latest smartphones and mobile devices'),
('Digital Products', 'digital-products', 'Digital services and products');

-- Insert default blog categories
INSERT INTO blog_categories (name, slug) VALUES
('Tips & Tricks', 'tips-tricks'),
('Repair Guides', 'repair-guides'),
('Product Reviews', 'product-reviews'),
('News', 'news'),
('Tutorial', 'tutorial');

-- Insert default services
INSERT INTO services (name, slug, description, price, estimated_days, category, status) VALUES
('Phone Screen Repair', 'phone-screen-repair', 'Professional smartphone screen replacement', 300.00, 1, 'Smartphone Repair', 'active'),
('Battery Replacement', 'battery-replacement', 'Replace worn out batteries', 200.00, 1, 'Smartphone Repair', 'active'),
('Software Repair', 'software-repair', 'Fix software issues and malware removal', 150.00, 1, 'Software Repair', 'active'),
('PC Maintenance', 'pc-maintenance', 'Complete PC cleaning and optimization', 250.00, 1, 'PC Repair', 'active'),
('Hard Drive Recovery', 'hard-drive-recovery', 'Data recovery services', 400.00, 3, 'PC Repair', 'active');

-- Insert sample admin user (password: admin123 - hashed with bcrypt)
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@digitalrevive.ma', '$2y$10$YOixf5LxpjzKh1Y3O0Z1uOJr1Y3R5Q7V8X9A0B1C2D3E4F5G6H7I8J9', 'admin');

-- Insert default settings
INSERT INTO settings (setting_key, setting_value) VALUES
('site_name', 'Digital Revive'),
('site_email', 'info@digitalrevive.ma'),
('site_phone', '+212638038932'),
('site_address', 'Marrakech, Morocco'),
('whatsapp_number', '212638038932'),
('instagram_handle', 'digital_revive_ma'),
('currency', 'MAD');

-- ========================================
-- CREATE INDEXES FOR PERFORMANCE
-- ========================================
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_products_status ON products(status);
CREATE INDEX idx_products_slug ON products(slug);
CREATE INDEX idx_blog_posts_category ON blog_posts(category_id);
CREATE INDEX idx_blog_posts_author ON blog_posts(author_id);
CREATE INDEX idx_blog_posts_slug ON blog_posts(slug);
CREATE INDEX idx_blog_posts_published ON blog_posts(published);
CREATE INDEX idx_orders_customer ON orders(customer_id);
CREATE INDEX idx_repair_orders_customer ON repair_orders(customer_id);
CREATE INDEX idx_repair_orders_status ON repair_orders(status);

-- ========================================
-- DATABASE READY FOR USE
-- ========================================
