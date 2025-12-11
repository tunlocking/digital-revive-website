<!-- Admin Navigation Bar -->
<nav class="navbar navbar-dark bg-dark sticky-top" style="padding: 0.5rem 1rem;">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
            <i class="fas fa-tools" style="font-size: 28px; margin-right: 10px; color: #ffc107;"></i>
            <span class="d-none d-sm-inline">Digital Revive Admin</span>
        </a>

        <div class="d-flex align-items-center gap-3">
            <!-- Theme Toggle -->
            <button class="btn btn-sm btn-outline-light" id="themeToggle" title="Toggle Dark/Light Mode">
                <i class="fas fa-moon"></i>
            </button>

            <!-- User Menu -->
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-light dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle"></i>
                    <span class="d-none d-sm-inline ms-2"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                    <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<style>
    body {
        transition: background-color 0.3s, color 0.3s;
    }

    body.dark-mode {
        background-color: #1a1a1a;
        color: #fff;
    }

    body.dark-mode .bg-light {
        background-color: #2a2a2a !important;
    }

    body.dark-mode .card {
        background-color: #2a2a2a;
        border-color: #444;
        color: #fff;
    }

    body.dark-mode .table {
        color: #fff;
    }

    body.dark-mode .table-dark {
        background-color: #333 !important;
    }

    body.dark-mode .table tbody tr:hover {
        background-color: #333;
    }

    body.dark-mode .alert {
        background-color: #2a2a2a;
        color: #fff;
        border-color: #444;
    }

    body.dark-mode .form-control,
    body.dark-mode .form-select {
        background-color: #333;
        color: #fff;
        border-color: #555;
    }

    body.dark-mode .form-control:focus,
    body.dark-mode .form-select:focus {
        background-color: #333;
        color: #fff;
        border-color: #ffc107;
    }

    body.dark-mode .navbar {
        background-color: #1a1a1a !important;
        border-bottom: 1px solid #333;
    }

    body.dark-mode .sidebar {
        background-color: #2a2a2a !important;
        border-right: 1px solid #444;
    }

    body.dark-mode .nav-link {
        color: #aaa !important;
    }

    body.dark-mode .nav-link:hover {
        color: #fff !important;
    }

    body.dark-mode .nav-link.active {
        color: #ffc107 !important;
    }
</style>

<script>
    // Theme toggle functionality
    document.addEventListener('DOMContentLoaded', function() {
        const themeToggle = document.getElementById('themeToggle');
        const currentTheme = localStorage.getItem('adminTheme') || 'light';

        if (currentTheme === 'dark') {
            document.body.classList.add('dark-mode');
            updateThemeIcon();
        }

        themeToggle.addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            const newTheme = document.body.classList.contains('dark-mode') ? 'dark' : 'light';
            localStorage.setItem('adminTheme', newTheme);
            updateThemeIcon();
        });

        function updateThemeIcon() {
            const icon = themeToggle.querySelector('i');
            if (document.body.classList.contains('dark-mode')) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            } else {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            }
        }
    });
</script>
