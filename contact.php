<?php
// ============================================================
// contact.php - Contact Page
// ============================================================
$page_title = 'Contact Us';
require_once __DIR__ . '/includes/header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_contact'])) {
    $full_name = sanitize($conn, $_POST['full_name'] ?? '');
    $email     = sanitize($conn, $_POST['email'] ?? '');
    $subject   = sanitize($conn, $_POST['subject'] ?? '');
    $message   = sanitize($conn, $_POST['message'] ?? '');

    if (!$full_name || !$email || !$message) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $sql = "INSERT INTO contact_messages (full_name, email, subject, message)
                VALUES ('$full_name','$email','$subject','$message')";
        if (mysqli_query($conn, $sql)) {
            setFlash('success', 'Thank you for contacting us! We will get back to you within 2 working days.');
            redirect('contact.php');
        } else {
            $error = 'Something went wrong. Please try again.';
        }
    }
}
// School coordinates
$lat = 7.461404971197599;
$lng = 80.46624711510599;
?>

<div class="page-hero">
    <h1>Contact Us</h1>
    <p>We'd love to hear from you – reach out to our friendly team</p>
    <div class="breadcrumb"><a href="index.php">Home</a><span>/</span><span>Contact</span></div>
</div>

<section class="section">
    <div class="container">
        <div class="contact-grid">

            <!-- Contact Info -->
            <div>
                <h2 style="margin-bottom:24px;">Get In Touch</h2>
                <p style="color:var(--gray);margin-bottom:30px;line-height:1.8;">Have a question or want to learn more about our school? We're here to help. Reach out using any of the options below, or fill in the form and we will respond promptly.</p>

                <ul class="contact-info-list">
                    <li>
                        <div class="ci-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="ci-text">
                            <strong>Address</strong>
                            Al-Ashraq Maha Vidyalaya<br>Sri Lanka
                        </div>
                    </li>
                    <li>
                        <div class="ci-icon"><i class="fas fa-phone"></i></div>
                        <div class="ci-text">
                            <strong>Phone</strong>
                            +94 76 333 0090
                        </div>
                    </li>
                    <li>
                        <div class="ci-icon"><i class="fas fa-envelope"></i></div>
                        <div class="ci-text">
                            <strong>Email</strong>
                            info@alashraq.edu
                        </div>
                    </li>
                    <li>
                        <div class="ci-icon"><i class="fas fa-clock"></i></div>
                        <div class="ci-text">
                            <strong>Office Hours</strong>
                            Monday – Friday: 7:30 AM – 3:30 PM<br>
                            Saturday: 8:00 AM – 12:00 PM
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Contact Form -->
            <div class="form-card" style="max-width:100%;">
                <h2>Send a Message</h2>
                <p class="form-subtitle">Fill in the form and we'll respond within 2 working days.</p>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="POST" action="contact.php">
                    <div class="form-group">
                        <label>Full Name <span class="required">*</span></label>
                        <input type="text" name="full_name" required placeholder="Your full name" value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Email Address <span class="required">*</span></label>
                        <input type="email" name="email" required placeholder="your@email.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Subject</label>
                        <input type="text" name="subject" placeholder="What is this about?" value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Message <span class="required">*</span></label>
                        <textarea name="message" required placeholder="Your message..."><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                    </div>
                    <button type="submit" name="submit_contact" class="btn btn-green w-100"><i class="fas fa-paper-plane"></i> Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Google Map -->
<section class="section section-alt">
    <div class="container">
        <div class="section-title">
            <h2>Find Us on the Map</h2>
            <p>Al-Ashraq M.V – Coordinates: <?php echo $lat; ?>, <?php echo $lng; ?></p>
            <div class="title-line"></div>
        </div>
        <div class="map-container">
            <iframe
                src="https://www.google.com/maps/embed/v1/place?key=AIzaSyD-9tSrke72PouQMnMX-a7eZSW0jkFMBWY&q=<?php echo $lat; ?>,<?php echo $lng; ?>&zoom=15"
                allowfullscreen
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                title="Al-Ashraq M.V Location">
            </iframe>
        </div>
        <p class="text-center text-muted mt-20" style="font-size:13px;">
            <i class="fas fa-info-circle"></i>
            If the map does not load, <a href="https://www.google.com/maps?q=<?php echo $lat; ?>,<?php echo $lng; ?>" target="_blank" style="color:var(--primary);font-weight:600;">click here to open in Google Maps</a>.
        </p>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
