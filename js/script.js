// Digital Revive Website - Main JavaScript

// Constants
const DARK_MODE_KEY = 'digitalrevive-darkmode';
const WHATSAPP = 'https://wa.me/212638038932';
const INSTAGRAM = 'https://www.instagram.com/digitalr.ma?igsh=MXIzbTdiOWZtNDBsdA==';
const MAPS_URL = 'https://maps.app.goo.gl/yZGQuZbydDC7CrL67';

// DOM Content Loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('Digital Revive website loaded');
    initializeApp();
});

// Initialize App
function initializeApp() {
    loadThemePreference();
    initializeEventListeners();
    highlightActiveNavLink();
    createFloatingButtons();
    createThemeToggle();
}

// Theme Management
function loadThemePreference() {
    const isDarkMode = localStorage.getItem(DARK_MODE_KEY) === 'true';
    if (isDarkMode) {
        enableDarkMode();
    }
}

function toggleTheme() {
    if (document.body.classList.contains('dark-mode')) {
        disableDarkMode();
    } else {
        enableDarkMode();
    }
}

function enableDarkMode() {
    document.body.classList.add('dark-mode');
    localStorage.setItem(DARK_MODE_KEY, 'true');
    updateThemeToggleIcon();
}

function disableDarkMode() {
    document.body.classList.remove('dark-mode');
    localStorage.setItem(DARK_MODE_KEY, 'false');
    updateThemeToggleIcon();
}

function updateThemeToggleIcon() {
    const themeToggle = document.querySelector('.theme-toggle');
    if (themeToggle) {
        if (document.body.classList.contains('dark-mode')) {
            themeToggle.innerHTML = '☀️';
        } else {
            themeToggle.innerHTML = '🌙';
        }
    }
}

// Event Listeners
function initializeEventListeners() {
    // Form submissions
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', handleFormSubmit);
    });

    // Navigation links
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            const navbar = document.querySelector('.navbar-collapse');
            if (navbar && navbar.classList.contains('show')) {
                const toggle = document.querySelector('.navbar-toggler');
                toggle.click();
            }
        });
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Form Handling
function handleFormSubmit(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    
    console.log('Form submitted:', data);
    
    // Show success message
    const successMsg = document.createElement('div');
    successMsg.className = 'alert alert-success alert-dismissible fade show';
    successMsg.innerHTML = `
        <strong>Success!</strong> Thank you for your submission. We'll get back to you soon.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    form.parentNode.insertBefore(successMsg, form);
    
    // Reset form
    form.reset();
    
    // Remove success message after 5 seconds
    setTimeout(() => {
        successMsg.remove();
    }, 5000);
}

// Navigation
function highlightActiveNavLink() {
    const currentPage = getCurrentPage();
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href && href.includes(currentPage)) {
            link.classList.add('active');
        }
    });
}

function getCurrentPage() {
    const pathname = window.location.pathname;
    return pathname.substring(pathname.lastIndexOf('/') + 1) || 'index.html';
}

// Floating Buttons
function createFloatingButtons() {
    const floatingContainer = document.createElement('div');
    floatingContainer.className = 'floating-buttons';
    floatingContainer.innerHTML = `
        <a href="${WHATSAPP}" target="_blank" class="floating-btn floating-whatsapp" title="Chat with us on WhatsApp">
            <i class="fab fa-whatsapp"></i>
        </a>
        <a href="tel:+212638038932" class="floating-btn floating-call" title="Call us">
            <i class="fas fa-phone"></i>
        </a>
        <a href="${INSTAGRAM}" target="_blank" class="floating-btn floating-instagram" title="Follow us on Instagram">
            <i class="fab fa-instagram"></i>
        </a>
    `;
    document.body.appendChild(floatingContainer);
}

// Theme Toggle Button
function createThemeToggle() {
    const themeToggle = document.createElement('button');
    themeToggle.className = 'theme-toggle';
    themeToggle.innerHTML = document.body.classList.contains('dark-mode') ? '☀️' : '🌙';
    themeToggle.title = 'Toggle Dark/Light Mode';
    themeToggle.addEventListener('click', toggleTheme);
    document.body.appendChild(themeToggle);
}

// Analytics
function logPageView() {
    const page = getCurrentPage();
    const timestamp = new Date().toISOString();
    console.log(`Page view: ${page} at ${timestamp}`);
    
    // You can send this to an analytics service
}

// Call analytics on page load
logPageView();

// Intersection Observer for animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -100px 0px'
};

const observer = new IntersectionObserver(function(entries) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.animation = 'slideInUp 0.6s ease forwards';
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

// Observe cards and sections
document.querySelectorAll('.card, .feature-card').forEach(el => {
    observer.observe(el);
});

// Scroll animations
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
        navbar.style.boxShadow = '0 8px 25px rgba(0, 0, 0, 0.15)';
    } else {
        navbar.style.boxShadow = 'none';
    }
});

// Export functions for global use
window.digitalRevive = {
    toggleTheme,
    getCurrentPage,
    highlightActiveNavLink,
    logPageView
};
