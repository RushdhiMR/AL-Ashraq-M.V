<?php
// ============================================================
// includes/footer.php - Site Footer
// ============================================================

$root = '/school/';
?>
<footer class="site-footer">
    <div class="footer-top">
        <div class="container">
            <div class="footer-grid">

                <div class="footer-col">
                    <div class="footer-logo">
                        <i class="fas fa-graduation-cap"></i>
                        <span>Al-Ashraq M.V</span>
                    </div>
                    <p class="footer-about">Dedicated to providing quality education and nurturing the next generation of responsible citizens in Sri Lanka.</p>
                    <div class="footer-social">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>

                <div class="footer-col">
                    <h4>Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="<?php echo $root; ?>index.php"><i class="fas fa-chevron-right"></i> Home</a></li>
                        <li><a href="<?php echo $root; ?>about.php"><i class="fas fa-chevron-right"></i> About Us</a></li>
                        <li><a href="<?php echo $root; ?>academics.php"><i class="fas fa-chevron-right"></i> Academics</a></li>
                        <li><a href="<?php echo $root; ?>admissions.php"><i class="fas fa-chevron-right"></i> Admissions</a></li>
                        <li><a href="<?php echo $root; ?>announcements.php"><i class="fas fa-chevron-right"></i> Announcements</a></li>
                        <li><a href="<?php echo $root; ?>gallery.php"><i class="fas fa-chevron-right"></i> Gallery</a></li>
                        <li><a href="<?php echo $root; ?>contact.php"><i class="fas fa-chevron-right"></i> Contact</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>For Students</h4>
                    <ul class="footer-links">
                        <li><a href="<?php echo $root; ?>student_register.php"><i class="fas fa-chevron-right"></i> Student Registration</a></li>
                        <li><a href="<?php echo $root; ?>lecturer_register.php"><i class="fas fa-chevron-right"></i> Lecturer Registration</a></li>
                        <li><a href="<?php echo $root; ?>login.php"><i class="fas fa-chevron-right"></i> Login Portal</a></li>
                        <li><a href="<?php echo $root; ?>admissions.php"><i class="fas fa-chevron-right"></i> Admission Info</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Contact Us</h4>
                    <ul class="footer-contact">
                        <li><i class="fas fa-map-marker-alt"></i> Al-Ashraq Maha Vidyalaya, Sri Lanka</li>
                        <li><i class="fas fa-phone"></i> +94 76 333 0090</li>
                        <li><i class="fas fa-envelope"></i> info@alashraq.edu</li>
                        <li><i class="fas fa-clock"></i> Mon – Fri: 7:30 AM – 3:30 PM</li>
                    </ul>
                </div>

            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Al-Ashraq M.V. All rights reserved.</p>
            <p>Designed with <i class="fas fa-heart" style="color:#e74c3c;"></i> for educational excellence</p>
        </div>
    </div>
</footer>

<script src="<?php echo $root; ?>js/script.js"></script>
</body>
</html>