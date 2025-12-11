# Digital Revive CMS - Quick Reference Guide

## 🚀 Quick Start

### Access Admin Dashboard
```
http://localhost/digital-revive-website/admin/pages/login.php
```

### Default Credentials
- **Email**: admin@digitalrevive.com
- **Username**: admin
- **Password**: (Set your own via registration)

## 📋 Main Admin Features

### Dashboard
Click on **Dashboard** in the sidebar to see:
- Total products, services, blog posts
- Recent content
- Quick action buttons

### Manage Services
1. **Services** → List all repair services
2. **Add Service** → Create new service with pricing
3. **Edit Service** → Modify existing service details
4. **Delete** → Remove services (icon button)

### Manage Products
1. **Products** → View all products
2. **Add Product** → Add new product with image
3. **Edit Product** → Update product information
4. **Delete** → Remove products

### Manage Blog
1. **Blog Posts** → List all articles
2. **Add Blog Post** → Write new article with featured image
3. **Edit** → Modify existing posts
4. **Delete** → Remove articles

### Manage Team
1. **Team Members** → View all staff
2. **Add Team Member** → Add staff profile with photo
3. **Edit** → Update member information
4. **Delete** → Remove team members

### Website Settings
**Settings** page to configure:
- Website name
- Contact phone & email
- Instagram handle
- Business address & hours
- Social media links (Facebook, Twitter, LinkedIn)
- Website description

## 🌐 Frontend Pages (Auto-Updated)

All pages automatically display content from the database:

| Page | URL | What It Shows |
|------|-----|---------------|
| Home | `index.php` | Services preview, team, testimonials |
| Services | `services.php` | All repair services by category |
| Products | `products.php` | Product catalog with filtering |
| Blog | `blog.php` | Blog articles with pagination |
| Article | `blog-post.php?id=X` | Full blog post with sharing |
| Contact | `contact.php` | Contact form & info |

## 🎯 Common Tasks

### Add a New Service
1. Go to **Admin** → **Services**
2. Click **Add New Service**
3. Fill in:
   - Service name (e.g., "iPhone Screen Repair")
   - Price ($89.99)
   - Category (e.g., "Smartphones")
   - Description
   - Estimated days (e.g., 1)
   - Icon (optional, Font Awesome class)
4. Click **Create Service**
✅ Service appears on website immediately

### Publish a Blog Post
1. Go to **Admin** → **Blog Posts**
2. Click **Add New Blog Post**
3. Fill in:
   - Title
   - Category
   - Content (text editor)
   - Featured image
4. Make sure **Published** is checked
5. Click **Save**
✅ Post appears on blog page automatically

### Update Website Phone Number
1. Go to **Admin** → **Settings**
2. Find "Phone Number" field
3. Update the number
4. Click **Save Settings**
✅ Phone number updates on website contact page

### Add Team Member
1. Go to **Admin** → **Team Members**
2. Click **Add Team Member**
3. Fill in:
   - Full name
   - Position (e.g., "Lead Technician")
   - Bio
   - Email & phone
   - Upload profile photo
4. Click **Add Team Member**
✅ Member appears on home page

## 🔐 Admin Navigation

**Sidebar Menu Structure**:
```
Dashboard                 (Statistics & quick actions)
CONTENT MANAGEMENT
├── Products            (Add, edit, delete products)
├── Blog Posts          (Create & manage articles)
└── Services            (Manage repair services)
WEBSITE
├── Settings            (Configure website info)
└── Team Members        (Manage staff profiles)
USERS
└── Logout              (Sign out)
```

## 🎨 Theme Control

**Dark/Light Mode**: Toggle button in top-right corner of admin panel
- Saves preference automatically
- Applies to entire admin dashboard

## 📁 File Upload Details

### Image Requirements
- **Max Size**: 5MB
- **Formats**: JPG, PNG, GIF, WebP
- **Auto Location**: `/uploads/` directory
- **Old images deleted** when updating with new image

### Upload Folders
```
uploads/
├── blog/       → Blog featured images
├── products/   → Product images
└── team/       → Team member photos
```

## 🛡️ Security

- All passwords encrypted with bcrypt
- Sessions expire for security
- SQL injection prevention
- File type validation
- Input sanitization

## 🔄 Content Auto-Updates

**When you save content in admin:**
1. Data stored in database
2. Website immediately pulls updated data
3. No additional publishing step needed
4. Status controls visibility (active/inactive)

## 📧 Contact Form Emails

**Automatic emails when someone submits contact form:**
- Email sent to admin with customer message
- Confirmation email sent to customer
- Set admin email in **Settings**

## 💡 Tips

✅ **Always set services/products to "Active"** to display on website
✅ **Upload quality images** - they display on website
✅ **Update Settings regularly** - phone, address, hours, etc.
✅ **Write SEO-friendly blog titles** for better rankings
✅ **Use categories** to organize content
✅ **Add team member photos** - makes team page professional
✅ **Test contact form** to ensure emails work

## ❓ Troubleshooting

**Images not showing?**
- Check file was uploaded (max 5MB)
- Verify format is JPG/PNG/GIF/WebP
- Check `/uploads/` folder has write permissions

**Page still shows old content?**
- Refresh browser (Ctrl+F5 or Cmd+Shift+R)
- Check content status is "Active"
- Verify database connection

**Can't login?**
- Clear browser cookies
- Check email/username is correct
- Verify user exists in database
- Try registering new admin user

**Email not sending?**
- Verify admin email in Settings
- Check server mail configuration
- Test with db-test.php

## 📚 Full Documentation

For complete documentation, see:
- `CMS_DOCUMENTATION.md` - Full system details
- `SYSTEM_IMPLEMENTATION_SUMMARY.md` - What was built

---

**Need Help?**
1. Check the documentation files
2. Review database structure in `config/database.sql`
3. Test database connection with `admin/pages/db-test.php`
4. Check error logs in server
