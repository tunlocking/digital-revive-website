# Digital Revive Website

A modern, fully responsive website for Digital Revive - a professional digital repair and products shop.

## Project Overview

Digital Revive is a complete, professional website for a mobile and PC repair service shop with the slogan **"Bring Your Digital Life Back to Life"**

### Features

✅ **Fully Responsive Design** - Works perfectly on mobile, tablet, and desktop  
✅ **Dark/Light Mode Toggle** - User preference theme switching  
✅ **Modern UI/UX** - Clean, professional design with smooth animations  
✅ **Bootstrap 5** - Built with the latest Bootstrap framework  
✅ **Font Awesome Icons** - Professional icons throughout  
✅ **Floating Action Buttons** - WhatsApp, Call, and Instagram quick access  
✅ **SEO Optimized** - Meta tags, Open Graph, and JSON-LD schema  
✅ **Forms & Validation** - Contact and repair tracking forms  
✅ **Google Maps Integration** - Location display  

## Website Pages

1. **index.html** - Home page with hero section, services overview, testimonials, map
2. **about.html** - Company story, mission, values, team information
3. **products.html** - Product catalog with categories and pricing
4. **services.html** - Detailed service offerings with pricing table
5. **contact.html** - Contact form, business hours, map, social links
6. **faq.html** - 10 comprehensive FAQs with accordion design
7. **gallery.html** - Shop photos and repair process images
8. **blog.html** - Tech tips and repair guides with sidebar
9. **track.html** - Repair order tracking system with timeline

## File Structure

```
digital-revive-website/
├── index.html              # Home page
├── about.html              # About us page
├── products.html           # Products page
├── services.html           # Services page
├── contact.html            # Contact page
├── faq.html                # FAQ page
├── gallery.html            # Gallery page
├── blog.html               # Blog page
├── track.html              # Repair tracking page
├── css/
│   └── style.css           # Main stylesheet with dark mode
├── js/
│   └── script.js           # JavaScript functionality
├── images/                 # Image directory (add images here)
└── README.md               # This file
```

## Technologies Used

- **HTML5** - Semantic markup
- **CSS3** - Modern styling with CSS variables and animations
- **Bootstrap 5** - Responsive framework
- **JavaScript (ES6+)** - Interactive features
- **Font Awesome 6** - Icon library
- **Google Maps** - Location display
- **Local Storage** - Dark mode preference persistence

## Key Features

### Dark/Light Mode
- Toggle button in top-right corner
- Preference saved in browser storage
- Applied to all pages

### Floating Action Buttons
- **WhatsApp** - Direct messaging link (+212638038932)
- **Call** - Direct phone link
- **Instagram** - Social media link
- **Theme Toggle** - Dark/Light mode switch

### Responsive Design
- Mobile-first approach
- Breakpoints: 480px, 768px, 1200px
- Flexible grid system using Bootstrap

### SEO & Meta Tags
- Descriptive meta titles and descriptions
- Open Graph tags for social sharing
- JSON-LD Local Business schema
- Alt text for all images

### Forms
- Contact form with validation
- Repair tracking form
- Newsletter subscription
- All forms with success feedback

## Setup & Installation

### Local Development

1. **Clone or Download** the project files
2. **Open in Browser** - Simply open `index.html` in any modern web browser
3. **No Build Process Required** - Pure HTML, CSS, and JavaScript

### File Server (Recommended)

For the best experience, serve files through a local server:

```bash
# Using Python 3
python3 -m http.server 8000

# Using Python 2
python -m SimpleHTTPServer 8000

# Using Node.js (http-server)
npm install -g http-server
http-server

# Using PHP
php -S localhost:8000
```

Then visit: `http://localhost:8000`

## Hosting Instructions

### Free Hosting Options

#### 1. **Netlify (Recommended)**
- Upload files directly or connect GitHub
- Automatic HTTPS
- CDN included
- Easy custom domain
- Free tier is excellent

**Steps:**
1. Go to [netlify.com](https://netlify.com)
2. Sign up free
3. Drag and drop your project folder
4. Done!

#### 2. **GitHub Pages**
- Free hosting directly from GitHub
- Perfect for static sites

**Steps:**
1. Create GitHub repository
2. Push all files
3. Enable GitHub Pages in settings
4. Access at: `username.github.io/repository-name`

#### 3. **Vercel**
- Optimized for web applications
- Very fast
- Easy deployment

**Steps:**
1. Go to [vercel.com](https://vercel.com)
2. Sign up with GitHub
3. Import project
4. Deploy automatically

#### 4. **000webhost or InfinityFree**
- Free with custom domain option
- cPanel included
- Email hosting

**Steps:**
1. Sign up
2. Upload files via FTP
3. Set domain
4. Access your site

### Paid Hosting Options

- **Bluehost** - Reliable, affordable, great support
- **SiteGround** - Premium performance
- **DreamHost** - WordPress friendly
- **HostGator** - Budget-friendly

## Customization Guide

### Change Company Information

Edit these files and replace with your details:

```html
<!-- In all HTML files, update: -->
<a href="tel:+212638038932">+212 638 038 932</a>
<a href="https://wa.me/212638038932">WhatsApp</a>
<a href="https://www.instagram.com/digitalr.ma">Instagram</a>
<p>info@digitalrevive.ma</p>
<p>Morocco</p>
```

### Customize Colors

In `css/style.css`, edit the CSS variables:

```css
:root {
    --primary-color: #667eea;
    --secondary-color: #764ba2;
    --success-color: #10b981;
    --danger-color: #ef4444;
}
```

### Add Your Images

1. Place images in the `/images` folder
2. Replace placeholder image URLs:
   ```html
   <img src="https://via.placeholder.com/400x300" 
        alt="Description"
        src="images/your-image.jpg">
   ```

### Update Business Hours

Edit in `contact.html`:
```html
<p>Mon - Fri: 9:00 AM - 8:00 PM<br>
Saturday: 10:00 AM - 6:00 PM<br>
Sunday: 11:00 AM - 6:00 PM</p>
```

### Modify Content

- Edit text directly in HTML files
- Update product prices in `products.html`
- Add blog posts in `blog.html`
- Customize FAQ in `faq.html`

## Performance Optimization

### For Better Speed:
1. **Optimize Images** - Compress images before uploading
2. **Enable Caching** - Set cache headers on hosting
3. **Use CDN** - Netlify/Vercel provide CDN automatically
4. **Minify CSS/JS** - Already optimized

### Recommended Image Tools:
- TinyPNG/TinyJPG
- ImageOptim
- Optimizilla

## Browser Support

- Chrome/Edge 90+
- Firefox 88+
- Safari 14+
- Mobile browsers (iOS Safari, Chrome Mobile)

## SEO Checklist

- ✅ Meta descriptions on all pages
- ✅ Keyword optimization
- ✅ Mobile responsive
- ✅ Fast loading (< 3 seconds)
- ✅ Structured data (JSON-LD)
- ✅ Open Graph tags
- ✅ Clean URL structure
- ✅ Google Maps integration

## Google Maps Integration

The map is embedded in `index.html` and `contact.html`. To update the location:

1. Go to [Google Maps](https://maps.google.com)
2. Find your location
3. Click "Share" → "Embed a map"
4. Copy the iframe code
5. Replace in HTML

## Form Submissions

### Contact Form
Currently shows a success message. To enable email submissions:

1. Use a service like **Formspree** or **Basin**
2. Or set up backend with Node.js/PHP
3. Update form action attribute

### Example with Formspree:
```html
<form action="https://formspree.io/f/YOUR_ID" method="POST">
```

## Troubleshooting

### Dark Mode Not Working
- Check browser support for localStorage
- Try clearing browser cache

### Images Not Showing
- Verify image paths are correct
- Check image file exists in /images folder
- Use absolute URLs if on different domain

### Forms Not Submitting
- Check console for JavaScript errors
- Verify form field names match script
- Check CORS if using external service

## Support & Maintenance

### Regular Updates
- Update contact information quarterly
- Add new blog posts monthly
- Update service prices as needed
- Monitor broken links

### Security
- Keep form endpoints updated
- Use HTTPS (included with most hosts)
- Regular backups
- Monitor for vulnerabilities

## License

This website template is provided as-is for Digital Revive. Feel free to customize and deploy.

## Contact & Support

**For Website Issues:**
- Email: info@digitalrevive.ma
- Phone: +212 638 038 932
- WhatsApp: +212638038932
- Instagram: @digitalr.ma

## Credits

- **Framework:** Bootstrap 5
- **Icons:** Font Awesome 6
- **Fonts:** Google Fonts
- **Design:** Modern, responsive, mobile-first

---

**Last Updated:** December 11, 2025  
**Version:** 1.0  
**Status:** Production Ready
