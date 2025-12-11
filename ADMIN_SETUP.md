# Database & Admin Setup Guide

## 📋 Prerequisites

- PHP 7.4+ installed
- MySQL 5.7+ or MariaDB
- A local server (XAMPP, WAMP, MAMP, or Laragon)

---

## 🚀 Installation Steps

### Step 1: Install XAMPP (Recommended)

1. Download XAMPP from: https://www.apachefriends.org/
2. Choose your OS version (Windows, macOS, or Linux)
3. Install XAMPP on your computer
4. Start the XAMPP Control Panel
5. Click **Start** next to MySQL and Apache

### Step 2: Create MySQL Database

1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Click on **Databases** tab
3. Create a new database named: `digital_revive`
4. Select the new database
5. Click on **Import** tab
6. Choose the file: `config/database.sql`
7. Click **Go** to import all tables

### Step 3: Configure Database Connection

The file `config/db.php` is already configured with default values:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'digital_revive');
define('DB_USER', 'root');
define('DB_PASS', '');
```

If your MySQL password is different, update `DB_PASS` value.

### Step 4: Place Project in htdocs

1. Copy this project folder to: `C:\xampp\htdocs\` (Windows) or `/Applications/XAMPP/htdocs/` (macOS)
2. Folder should be named: `digital-revive-website`

### Step 5: Test Database Connection

1. Open browser and go to: http://localhost/digital-revive-website/admin/pages/db-test.php
2. You should see green **"Database Connected"** message
3. You should see all database tables listed with record counts

---

## 🔐 Admin Panel Access

### Login Credentials (Default)

- **Email:** `admin@digitalrevive.ma`
- **Password:** `admin123`

### Access Admin Panel

1. Go to: http://localhost/digital-revive-website/admin/pages/login.php
2. Enter email and password
3. Click **Login**

### Dashboard Features

- View statistics (products, blog posts, orders, repairs, messages)
- Add/Edit products
- Manage blog posts
- View database information

---

## 📁 Database Tables

| Table | Purpose |
|-------|---------|
| **users** | Admin user accounts |
| **categories** | Product categories |
| **products** | Product catalog |
| **blog_categories** | Blog post categories |
| **blog_posts** | Blog articles |
| **customers** | Customer information |
| **orders** | Customer orders |
| **order_items** | Items in orders |
| **services** | Repair services |
| **repair_orders** | Repair tickets |
| **repair_tracking** | Repair timeline tracking |
| **contact_messages** | Contact form submissions |
| **testimonials** | Customer reviews |
| **gallery** | Gallery images |
| **newsletter_subscribers** | Email subscribers |
| **settings** | Site configuration |

---

## 🔒 Security Features Implemented

### 1. **Input Validation**
- Email validation
- Password length checking
- Price/numeric validation
- File type & size validation

### 2. **SQL Injection Prevention**
- Prepared statements (PDO)
- Parameter binding
- No direct SQL concatenation

### 3. **Password Security**
- BCrypt hashing (password_hash)
- Secure password verification
- 8+ character minimum

### 4. **File Upload Security**
- MIME type checking
- File extension validation
- File size limits (5MB max)
- Unique filename generation

### 5. **Session Management**
- Session start on login
- Session destruction on logout
- Login requirement checks

### 6. **Output Escaping**
- htmlspecialchars() for HTML output
- htmlspecialchars() for form values
- No direct output of user input

---

## 📝 Form Validation

All forms include:

```php
// Check required fields
if (empty($field)) {
    $error = 'Field is required';
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Invalid email';
}

// Sanitize input
$input = htmlspecialchars(trim($input));

// Validate numbers
if (!is_numeric($price) || $price <= 0) {
    $error = 'Invalid price';
}

// Validate files
if (!validate_image_upload($file)) {
    $error = 'Invalid file';
}
```

---

## 🔧 Helper Functions (config/security.php)

### Sanitization Functions
- `sanitize_input()` - HTML escape with trim
- `sanitize_email()` - Remove invalid characters
- `sanitize_text()` - Filter special characters
- `sanitize_url()` - Validate and sanitize URLs

### Validation Functions
- `validate_email()` - Check valid email
- `validate_password()` - Min 8 characters
- `validate_phone()` - Phone number format
- `validate_price()` - Positive number

### File Functions
- `validate_image_upload()` - Check image validity
- `upload_file()` - Secure file upload
- `get_safe_filename()` - Generate safe filenames

### Password Functions
- `hash_password()` - BCrypt hashing
- `verify_password()` - Check password hash

### Session Functions
- `check_admin_logged_in()` - Require login
- `logout()` - Destroy session

---

## 🐛 Troubleshooting

### Database Connection Error

**Problem:** "Connection Error" on db-test.php

**Solutions:**
1. Check MySQL is running in XAMPP
2. Verify database name is `digital_revive`
3. Check `config/db.php` settings
4. Ensure database.sql was imported

### Can't Login

**Problem:** "Invalid email or password" message

**Solutions:**
1. Verify email: `admin@digitalrevive.ma`
2. Verify password: `admin123` (default)
3. Check if users table was imported
4. Check browser console for errors

### File Upload Fails

**Problem:** "Failed to upload file"

**Solutions:**
1. Check uploads/ folder permissions (777)
2. Verify file size < 5MB
3. Check file format (JPG, PNG, GIF)
4. Check server max upload size in php.ini

### 404 Errors

**Problem:** Page not found

**Solutions:**
1. Verify project is in htdocs folder
2. Check folder name: `digital-revive-website`
3. Restart Apache and MySQL
4. Clear browser cache

---

## 📊 Next Steps

1. ✅ Database created and tested
2. ✅ Admin login page with validation
3. ✅ Dashboard with statistics
4. ✅ Product management with file uploads
5. ✅ Security functions implemented

**Coming Next:**
- Blog management functionality
- Order tracking system
- Repair order management
- Customer dashboard
- Email notifications
- Backup system

---

## 💡 Important Notes

- Change default password after first login
- Create additional admin accounts as needed
- Regularly backup database
- Keep PHP, MySQL, and libraries updated
- Review error logs regularly
- Test all forms after updates

---

## 🆘 Support

For issues or questions:
- Check the troubleshooting section above
- Review error messages in browser console
- Check PHP error logs in XAMPP
- Verify all files are in correct locations
- Test database connection regularly

**Email:** info@digitalrevive.ma  
**Phone:** +212 638 038 932
