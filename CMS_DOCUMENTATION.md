# Digital Revive - Complete CMS System Documentation

## Overview
Digital Revive is a fully integrated Content Management System (CMS) for device repair services. The system includes:
- **Admin Dashboard**: Complete control panel for managing all website content
- **Dynamic Frontend**: Database-driven website that automatically displays all content
- **User Management**: Admin accounts with role-based access
- **Content Management**: Products, Services, Blog Posts, Team Members, Testimonials
- **Settings Management**: Centralized website configuration (phone, email, Instagram, addresses, etc.)

## System Architecture

### Database Structure
The system uses **8+ core tables**:

1. **users** - Admin/editor accounts
   - username, email, password (hashed), role (admin/editor)

2. **products** - Store inventory
   - name, description, price, category, image, stock quantity, status

3. **categories** - Product categories
   - name, slug, description

4. **blog_posts** - Blog articles
   - title, content, featured image, category, author, published status

5. **blog_categories** - Blog post categories
   - name, slug, description

6. **services** - Repair services
   - name, description, price, estimated days, icon, image, category, status

7. **settings** - Website configuration
   - site_name, site_email, site_phone, whatsapp_number, instagram_handle
   - site_address, business_hours, about_description, header info
   - Social media URLs (facebook, twitter, linkedin)

8. **team_members** - Staff profiles
   - name, position, bio, skills, phone, email, image, status

9. **testimonials** - Customer reviews
   - client_name, message, rating, status

10. **social_links** - Social media profiles
    - platform, url, icon, status

### File Structure

```
digital-revive-website/
├── admin/
│   ├── pages/
│   │   ├── dashboard.php          # Main admin dashboard
│   │   ├── login.php              # Admin login
│   │   ├── register.php           # User registration
│   │   ├── products.php           # Product list
│   │   ├── add_product.php        # Add product
│   │   ├── edit_product.php       # Edit product
│   │   ├── blog.php               # Blog list
│   │   ├── add_blog.php           # Add blog post
│   │   ├── edit_blog.php          # Edit blog post
│   │   ├── services.php           # Service list
│   │   ├── add_service.php        # Add service
│   │   ├── edit_service.php       # Edit service
│   │   ├── settings.php           # Website settings
│   │   ├── team.php               # Team members list
│   │   ├── add_team_member.php    # Add team member
│   │   ├── edit_team_member.php   # Edit team member
│   │   ├── logout.php             # Logout
│   │   └── db-test.php            # Database connection test
│   ├── assets/
│   │   ├── css/admin.css          # Admin styling (dark mode)
│   │   └── js/admin.js            # Admin utilities
│   └── includes/
│       ├── navbar.php              # Admin navigation bar
│       └── sidebar.php             # Admin sidebar menu
├── api/
│   └── get_content.php            # API endpoints for content retrieval
├── config/
│   ├── db.php                     # Database connection
│   └── database.sql               # Database schema
├── uploads/
│   ├── blog/                      # Blog images
│   ├── products/                  # Product images
│   └── team/                      # Team member photos
├── css/
│   └── style.css                  # Frontend styling
├── js/
│   └── script.js                  # Frontend utilities
├── index.php                      # Home page (dynamic)
├── services.php                   # Services page (dynamic)
├── products.php                   # Products page (dynamic)
├── blog.php                       # Blog listing (dynamic)
├── blog-post.php                  # Individual blog post
├── contact.php                    # Contact form with email
└── README.md                      # Setup instructions
```

## Frontend Pages (Dynamic)

All frontend pages are now PHP-based and pull content from the database:

### 1. **index.php** - Home Page
- Hero section with CTA
- Services preview (6 latest)
- Team member showcase
- Testimonials section
- Newsletter CTA

### 2. **services.php** - Services Page
- Services grouped by category
- Price and estimated completion time
- Icon/image for each service
- Request service buttons

### 3. **products.php** - Products Page
- Product grid with filtering
- Category sidebar filter
- Product images, prices, stock status
- Order/inquiry buttons
- Pagination support

### 4. **blog.php** - Blog Listing
- Blog post cards with featured images
- Category filter sidebar
- Author and date information
- Read more buttons
- Pagination

### 5. **blog-post.php** - Blog Post Detail
- Full article content
- Featured image
- Author and publish date
- Related posts sidebar
- Social sharing buttons
- Contact CTA

### 6. **contact.php** - Contact Page
- Contact form with validation
- Contact information sidebar
- Business hours
- Social media links
- Email notifications

## Admin Dashboard Pages

### Content Management

#### Products (`admin/pages/products.php`)
- List all products with pagination
- Quick edit/delete actions
- Search and filter functionality
- Status indicators

#### Add/Edit Product
- Form validation
- Image upload (5MB max, JPG/PNG/GIF/WebP)
- Category selection
- Price and stock management
- Status (active/inactive/discontinued)

#### Services (`admin/pages/services.php`)
- List all repair services
- Edit service details
- Delete services
- Price and turnaround time management

#### Add/Edit Service
- Service name and description
- Pricing and turnaround time
- Category assignment
- Font Awesome icon selection
- Detailed description field

#### Blog Posts (`admin/pages/blog.php`)
- List published and draft posts
- Create new blog posts
- Edit existing posts
- Delete posts
- Featured image management
- Category assignment
- Author tracking

#### Blog Editing Pages
- Rich text editor for content
- Featured image upload
- Category and author selection
- Publication status toggle
- Save as draft or publish

### Website Settings

#### Settings (`admin/pages/settings.php`)
Manage all website-wide configuration:
- **Basic Information**
  - Site name
  - Header title and subtitle
  - About description

- **Contact Information**
  - Email address
  - Phone number
  - WhatsApp number
  - Instagram handle
  - Business address
  - Business hours

- **Social Media**
  - Facebook URL
  - Twitter URL
  - LinkedIn URL

#### Team Management (`admin/pages/team.php`)
- List all team members
- View member details
- Edit member information
- Delete members
- Profile photo management

#### Add/Edit Team Member
- Personal information (name, position, bio)
- Contact details (email, phone)
- Skills list
- Profile photo upload
- Display order
- Active/inactive status

## Features

### 1. **Database-Driven Content**
- All website content stored in MySQL database
- No hardcoded information in templates
- Changes reflect immediately on frontend

### 2. **Image Management**
- Automatic image upload for:
  - Products
  - Blog posts
  - Team member photos
  - Services
- File validation (type and size)
- Old image deletion on update

### 3. **Auto-Upload Functionality**
- When admin adds/edits content, it automatically appears on website
- No manual publishing step required
- Status control (active/inactive)

### 4. **Email Notifications**
- Contact form submissions sent to admin email
- Confirmation email sent to user
- Automatic email handling

### 5. **Admin Features**
- Secure login with bcrypt hashing
- Session-based authentication
- Role-based access control (admin/editor)
- Dark/light mode toggle in admin panel
- Professional dashboard with statistics

### 6. **Frontend Features**
- Responsive Bootstrap design
- Mobile-first approach
- Category filtering
- Pagination support
- Social media integration
- Contact form with validation
- WhatsApp integration
- Instagram links

### 7. **Security**
- Prepared statements prevent SQL injection
- Password hashing with bcrypt
- Session validation
- File type validation on uploads
- Input sanitization with htmlspecialchars()

## API Endpoints

### GET /api/get_content.php

Available actions:
- `?action=get_settings` - Get website settings
- `?action=get_services` - Get active services
- `?action=get_products` - Get active products
- `?action=get_blog_posts&limit=10` - Get published blog posts
- `?action=get_team` - Get team members
- `?action=get_testimonials` - Get testimonials
- `?action=get_social_links` - Get social media links

## Setup Instructions

### 1. Database Setup
```bash
# Import the database schema
mysql -u root -p digital_revive < config/database.sql
```

### 2. Admin Login
- **Email**: admin@digitalrevive.com
- **Username**: admin
- **Password**: (set your own)

Register a new admin user or change the default password

### 3. File Permissions
```bash
chmod -R 755 uploads/
chmod -R 755 admin/assets/
```

### 4. Configuration
Edit `config/db.php` with your database credentials:
```php
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'digital_revive';
```

## Usage Workflow

### Adding a New Service
1. Go to Admin Dashboard → Services
2. Click "Add New Service"
3. Fill in details (name, price, description, etc.)
4. Set status to "Active"
5. Click Save
6. Service appears on website automatically

### Publishing a Blog Post
1. Go to Blog → Add New Post
2. Write content, add featured image
3. Select category
4. Set "Published" to TRUE
5. Save
6. Post appears on Blog page immediately

### Managing Website Info
1. Go to Settings
2. Update phone, email, Instagram handle, etc.
3. Click Save
4. Changes reflect on all pages automatically

### Adding Team Members
1. Go to Team Members
2. Click "Add Team Member"
3. Upload photo, enter details
4. Set as active
5. Save
6. Member appears on homepage and team page

## Technical Details

### Technologies Used
- **Backend**: PHP 7.4+
- **Database**: MySQL/PDO
- **Frontend**: Bootstrap 5.3.0, Vanilla JavaScript
- **Image Processing**: GD Library (file upload handling)
- **Email**: PHP mail() function

### Database Queries
- Prepared statements used throughout
- Foreign key relationships
- Proper indexing on commonly queried fields
- Cascade delete on related records

### Performance
- Pagination on large datasets (products, blog, services)
- Database indexes on status and date fields
- Static HTML caching possible with footer includes

## Future Enhancements

Possible additions:
- Image optimization on upload
- Blog post preview functionality
- Product inventory management
- Order tracking system
- Testimonial submission form
- Newsletter subscription
- SEO optimization
- Multi-language support
- Advanced analytics

## Troubleshooting

### Images not uploading
- Check upload directory permissions: `chmod -R 755 uploads/`
- Verify file size doesn't exceed 5MB
- Check file format (JPG, PNG, GIF, WebP only)

### Database connection error
- Verify credentials in `config/db.php`
- Ensure MySQL server is running
- Check database name is correct

### Email not sending
- Check server mail configuration
- Verify sender email is valid
- Check admin email in settings

### Login issues
- Clear browser cookies
- Check session directory is writable
- Verify user exists in database

## Support

For issues or questions:
1. Check the database is properly imported
2. Verify file permissions are correct
3. Check error logs in server
4. Test database connection with db-test.php

---

**Version**: 2.0 (Full CMS Implementation)
**Last Updated**: 2024
**Repository**: https://github.com/tunlocking/digital-revive-website
