<!-- Admin Navigation Sidebar -->
<aside class="col-md-3 col-lg-2 d-md-block bg-light sidebar position-relative" style="min-height: 100vh;">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>

            <li class="nav-item mt-3">
                <span class="nav-link text-muted" style="cursor: default;">
                    <strong>CONTENT MANAGEMENT</strong>
                </span>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'products.php' || basename($_SERVER['PHP_SELF']) === 'add_product.php' || basename($_SERVER['PHP_SELF']) === 'edit_product.php' ? 'active' : ''; ?>" href="products.php">
                    <i class="fas fa-cube"></i> Products
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'blog.php' || basename($_SERVER['PHP_SELF']) === 'add_blog.php' || basename($_SERVER['PHP_SELF']) === 'edit_blog.php' ? 'active' : ''; ?>" href="blog.php">
                    <i class="fas fa-blog"></i> Blog Posts
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'services.php' || basename($_SERVER['PHP_SELF']) === 'add_service.php' || basename($_SERVER['PHP_SELF']) === 'edit_service.php' ? 'active' : ''; ?>" href="services.php">
                    <i class="fas fa-wrench"></i> Services
                </a>
            </li>

            <li class="nav-item mt-3">
                <span class="nav-link text-muted" style="cursor: default;">
                    <strong>WEBSITE</strong>
                </span>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'settings.php' ? 'active' : ''; ?>" href="settings.php">
                    <i class="fas fa-sliders-h"></i> Settings
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'team.php' || basename($_SERVER['PHP_SELF']) === 'add_team_member.php' || basename($_SERVER['PHP_SELF']) === 'edit_team_member.php' ? 'active' : ''; ?>" href="team.php">
                    <i class="fas fa-users"></i> Team Members
                </a>
            </li>

            <li class="nav-item mt-3">
                <span class="nav-link text-muted" style="cursor: default;">
                    <strong>USERS</strong>
                </span>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</aside>
