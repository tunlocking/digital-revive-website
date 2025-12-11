<?php
// Load settings if not already loaded
if (!isset($settings)) {
    $stmt = $conn->query("SELECT setting_key, setting_value FROM settings");
    $settings = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}
?>

<!-- Footer -->
<footer class="bg-dark text-white py-4">
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-4 mb-3 mb-md-0">
                <h6><i class="fas fa-info-circle"></i> About</h6>
                <p class="text-muted"><?php echo htmlspecialchars($settings['about_description'] ?? ''); ?></p>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
                <h6><i class="fas fa-map-marker-alt"></i> Address</h6>
                <p class="text-muted"><?php echo htmlspecialchars($settings['site_address'] ?? ''); ?></p>
            </div>
            <div class="col-md-4">
                <h6><i class="fas fa-phone"></i> Contact</h6>
                <p class="text-muted">
                    Email: <a href="mailto:<?php echo htmlspecialchars($settings['site_email'] ?? ''); ?>" class="text-warning"><?php echo htmlspecialchars($settings['site_email'] ?? ''); ?></a><br>
                    Phone: <a href="tel:<?php echo htmlspecialchars($settings['site_phone'] ?? ''); ?>" class="text-warning"><?php echo htmlspecialchars($settings['site_phone'] ?? ''); ?></a><br>
                    WhatsApp: <a href="https://wa.me/<?php echo preg_replace('/\D/', '', $settings['whatsapp_number'] ?? ''); ?>" class="text-warning" target="_blank">Message Us</a>
                </p>
            </div>
        </div>

        <div class="text-center py-3 border-top border-secondary">
            <p class="text-muted mb-0">&copy; 2024 <?php echo htmlspecialchars($settings['site_name'] ?? 'Digital Revive'); ?>. All rights reserved.</p>
        </div>
    </div>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
