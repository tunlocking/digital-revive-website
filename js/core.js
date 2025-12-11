/**
 * Digital Revive - Core JavaScript
 * Modern, optimized, and production-ready
 */

'use strict';

// ====== UTILITY FUNCTIONS ======
const DR = {
    // DOM utilities
    el: (selector) => document.querySelector(selector),
    els: (selector) => document.querySelectorAll(selector),
    id: (id) => document.getElementById(id),
    
    // Classes
    addClass: (el, cls) => el?.classList.add(cls),
    removeClass: (el, cls) => el?.classList.remove(cls),
    toggleClass: (el, cls) => el?.classList.toggle(cls),
    hasClass: (el, cls) => el?.classList.contains(cls),
    
    // Attributes
    setAttr: (el, attr, val) => el?.setAttribute(attr, val),
    getAttr: (el, attr) => el?.getAttribute(attr),
    
    // Events
    on: (el, event, fn) => el?.addEventListener(event, fn),
    off: (el, event, fn) => el?.removeEventListener(event, fn),
    delegate: (parent, selector, event, fn) => {
        parent?.addEventListener(event, (e) => {
            if (e.target.matches(selector)) fn(e);
        });
    },
    
    // HTTP
    fetch: async (url, options = {}) => {
        try {
            const response = await fetch(url, {
                headers: { 'Content-Type': 'application/json' },
                ...options,
            });
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            return await response.json();
        } catch (error) {
            console.error('Fetch error:', error);
            return null;
        }
    },
    
    // Storage
    setStorage: (key, val) => localStorage.setItem(key, JSON.stringify(val)),
    getStorage: (key) => {
        try {
            return JSON.parse(localStorage.getItem(key));
        } catch {
            return null;
        }
    },
    
    // CSS
    css: (el, styles) => Object.assign(el?.style, styles),
    
    // Animation
    fadeIn: (el, duration = 300) => {
        if (!el) return;
        el.style.opacity = '0';
        el.style.display = 'block';
        el.offsetHeight; // Trigger reflow
        el.style.transition = `opacity ${duration}ms ease-in`;
        el.style.opacity = '1';
    },
    
    fadeOut: (el, duration = 300) => {
        if (!el) return;
        el.style.transition = `opacity ${duration}ms ease-out`;
        el.style.opacity = '0';
        setTimeout(() => (el.style.display = 'none'), duration);
    },
    
    // Notify
    notify: (message, type = 'info', duration = 3000) => {
        const alertClass = `alert alert-${type}`;
        const div = document.createElement('div');
        div.className = alertClass;
        div.innerHTML = `${message} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
        
        const container = DR.el('.alerts-container') || document.body;
        container.insertBefore(div, container.firstChild);
        
        if (duration > 0) {
            setTimeout(() => div.remove(), duration);
        }
    },
    
    // Format
    formatDate: (date, format = 'MMM D, YYYY') => {
        if (typeof date === 'string') date = new Date(date);
        // Basic date formatting - extend as needed
        return new Intl.DateTimeFormat('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        }).format(date);
    },
    
    formatCurrency: (amount, currency = 'MAD') => {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: currency,
        }).format(amount);
    },
};

// ====== SMOOTH SCROLLING ======
document.addEventListener('DOMContentLoaded', () => {
    // Smooth scroll for anchor links
    DR.delegate(document, 'a[href^="#"]', 'click', (e) => {
        const target = DR.el(e.target.getAttribute('href'));
        if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth' });
        }
    });
});

// ====== INTERSECTION OBSERVER FOR LAZY LOADING ======
const imageObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if (entry.isIntersecting) {
            const img = entry.target;
            img.src = img.dataset.src;
            img.removeAttribute('data-src');
            imageObserver.unobserve(img);
        }
    });
}, { rootMargin: '50px' });

document.addEventListener('DOMContentLoaded', () => {
    DR.els('img[data-src]').forEach((img) => {
        imageObserver.observe(img);
    });
});

// ====== FORM VALIDATION ======
class FormValidator {
    constructor(formSelector) {
        this.form = DR.el(formSelector);
        if (!this.form) return;
        this.setupListeners();
    }
    
    setupListeners() {
        this.form.addEventListener('submit', (e) => {
            if (!this.validate()) {
                e.preventDefault();
            }
        });
    }
    
    validate() {
        let isValid = true;
        const fields = this.form.querySelectorAll('[required]');
        
        fields.forEach((field) => {
            if (!this.validateField(field)) {
                isValid = false;
                this.showError(field);
            } else {
                this.clearError(field);
            }
        });
        
        return isValid;
    }
    
    validateField(field) {
        const value = field.value.trim();
        
        if (field.type === 'email') {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
        } else if (field.type === 'tel') {
            return /^[\d\s\-\+\(\)]{10,}$/.test(value);
        } else if (field.type === 'number') {
            return !isNaN(value) && value !== '';
        }
        
        return value !== '';
    }
    
    showError(field) {
        field.classList.add('is-invalid');
        const feedback = field.nextElementSibling;
        if (feedback?.classList.contains('invalid-feedback')) {
            feedback.style.display = 'block';
        }
    }
    
    clearError(field) {
        field.classList.remove('is-invalid');
        const feedback = field.nextElementSibling;
        if (feedback?.classList.contains('invalid-feedback')) {
            feedback.style.display = 'none';
        }
    }
}

// ====== FILE UPLOAD PREVIEW ======
function setupFilePreview(inputSelector, previewSelector) {
    const input = DR.el(inputSelector);
    const preview = DR.el(previewSelector);
    
    if (!input || !preview) return;
    
    input.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file) return;
        
        // Validate file type
        if (!['image/jpeg', 'image/png', 'image/gif', 'image/webp'].includes(file.type)) {
            DR.notify('Only image files (JPG, PNG, GIF, WebP) are allowed', 'danger');
            return;
        }
        
        // Validate file size (5MB max)
        if (file.size > 5 * 1024 * 1024) {
            DR.notify('File size must be less than 5MB', 'danger');
            return;
        }
        
        // Show preview
        const reader = new FileReader();
        reader.onload = (e) => {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    });
}

// ====== RICH TEXT EDITOR SETUP ======
function initTinyMCE(selector = 'textarea#description') {
    if (typeof tinymce === 'undefined') return;
    
    tinymce.init({
        selector: selector,
        plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking table contextmenu directionality emoticons paste textcolor colorpicker textpattern',
        toolbar: 'formatselect | bold italic strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | fullscreen',
        relative_urls: false,
        height: 400,
        branding: false,
    });
}

// ====== PAGINATION ======
function setupPagination(currentPage = 1, totalPages = 1) {
    const paginationEl = DR.el('.pagination');
    if (!paginationEl) return;
    
    const pages = [];
    
    // Previous button
    pages.push(`
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="?page=${currentPage - 1}">Previous</a>
        </li>
    `);
    
    // Page numbers
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);
    
    if (startPage > 1) {
        pages.push(`<li class="page-item"><a class="page-link" href="?page=1">1</a></li>`);
        if (startPage > 2) pages.push(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
    }
    
    for (let i = startPage; i <= endPage; i++) {
        pages.push(`
            <li class="page-item ${i === currentPage ? 'active' : ''}">
                <a class="page-link" href="?page=${i}">${i}</a>
            </li>
        `);
    }
    
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) pages.push(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
        pages.push(`<li class="page-item"><a class="page-link" href="?page=${totalPages}">${totalPages}</a></li>`);
    }
    
    // Next button
    pages.push(`
        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="?page=${currentPage + 1}">Next</a>
        </li>
    `);
    
    paginationEl.innerHTML = pages.join('');
}

// ====== MOBILE MENU TOGGLE ======
function setupMobileMenu() {
    const toggle = DR.el('.navbar-toggler');
    const menu = DR.el('.navbar-collapse');
    
    if (!toggle || !menu) return;
    
    toggle.addEventListener('click', () => {
        DR.toggleClass(menu, 'show');
    });
    
    // Close menu when link is clicked
    DR.delegate(menu, 'a', 'click', () => {
        DR.removeClass(menu, 'show');
    });
}

// ====== DARK MODE TOGGLE ======
function setupDarkMode() {
    const toggle = DR.el('[data-toggle="dark-mode"]');
    if (!toggle) return;
    
    const isDark = DR.getStorage('dark-mode') ?? window.matchMedia('(prefers-color-scheme: dark)').matches;
    
    if (isDark) {
        document.documentElement.setAttribute('data-theme', 'dark');
    }
    
    toggle.addEventListener('click', () => {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        document.documentElement.setAttribute('data-theme', isDark ? 'light' : 'dark');
        DR.setStorage('dark-mode', !isDark);
    });
}

// ====== PAGE INITIALIZATION ======
document.addEventListener('DOMContentLoaded', () => {
    setupMobileMenu();
    setupDarkMode();
    
    // Initialize form validators on forms with data-validate="true"
    DR.els('form[data-validate="true"]').forEach((form) => {
        new FormValidator(`#${form.id || ''}`);
    });
    
    // Log initialization (only in development)
    if (window.location.hostname === 'localhost') {
        console.log('Digital Revive: All systems initialized ✓');
    }
});

// ====== ERROR HANDLING ======
window.addEventListener('error', (e) => {
    console.error('JS Error:', e.error);
    if (window.location.hostname !== 'localhost') {
        DR.notify('An error occurred. Please refresh the page.', 'danger');
    }
});

// ====== UNHANDLED PROMISE REJECTION ======
window.addEventListener('unhandledrejection', (e) => {
    console.error('Promise Rejection:', e.reason);
});

// Export DR for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = DR;
}
