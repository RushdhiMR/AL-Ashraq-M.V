<?php
// ============================================================
// lecturer_register.php - Lecturer Registration
// ============================================================
$page_title = 'Lecturer Registration';
require_once __DIR__ . '/includes/header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name    = sanitize($conn, $_POST['full_name'] ?? '');
    $email        = sanitize($conn, $_POST['email'] ?? '');
    $phone        = sanitize($conn, $_POST['phone'] ?? '');
    $subject_spec = sanitize($conn, $_POST['subject_specialization'] ?? '');
    $qualification= sanitize($conn, $_POST['qualification'] ?? '');
    $address      = sanitize($conn, $_POST['address'] ?? '');
    $password     = $_POST['password'] ?? '';
    $confirm      = $_POST['confirm_password'] ?? '';

    if (!$full_name || !$email || !$phone || !$subject_spec || !$qualification || !$password) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $chk = mysqli_query($conn, "SELECT id FROM lecturers WHERE email='$email'");
        if (mysqli_num_rows($chk) > 0) {
            $error = 'An account with this email already exists.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO lecturers (full_name, email, phone, subject_specialization, qualification, address, password)
                    VALUES ('$full_name','$email','$phone','$subject_spec','$qualification','$address','$hashed')";
            if (mysqli_query($conn, $sql)) {
                setFlash('success', 'Registration successful! Your account is pending admin approval. You will be notified once approved.');
                redirect('login.php');
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>

<div class="page-hero">
    <h1>Lecturer Registration</h1>
    <p>Create your lecturer account to join the Al-Ashraq M.V staff portal</p>
    <div class="breadcrumb"><a href="index.php">Home</a><span>/</span><span>Lecturer Registration</span></div>
</div>

<section class="form-section">
    <div class="container">
        <div class="form-card" style="max-width:750px;">
            <h2><i class="fas fa-chalkboard-teacher" style="color:var(--primary);margin-right:10px;"></i>Lecturer Registration</h2>
            <p class="form-subtitle">All fields marked with <span style="color:red;">*</span> are required. Your account will be reviewed and approved by an administrator.</p>

            <?php if ($error): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="lecturer_register.php" novalidate>

                <div class="form-row">
                    <div class="form-group">
                        <label>Full Name <span class="required">*</span></label>
                        <input type="text" name="full_name" required placeholder="Enter your full name" value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Email Address <span class="required">*</span></label>
                        <input type="email" name="email" required placeholder="your@email.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Phone Number <span class="required">*</span></label>
                        <input type="tel" name="phone" required placeholder="+94 XX XXX XXXX" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Subject Specialization <span class="required">*</span></label>
                        <input type="text" name="subject_specialization" required placeholder="e.g. Mathematics, English, Science" value="<?php echo htmlspecialchars($_POST['subject_specialization'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Highest Qualification <span class="required">*</span></label>
                    <select name="qualification" required>
                        <option value="">-- Select Qualification --</option>
                        <?php
                        $quals = ["Diploma","Bachelor's Degree","Postgraduate Diploma","Master's Degree","PhD","Other"];
                        foreach ($quals as $q):
                            $sel = (($_POST['qualification'] ?? '') === $q) ? 'selected' : '';
                        ?>
                            <option value="<?php echo $q; ?>" <?php echo $sel; ?>><?php echo $q; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" placeholder="Enter your residential address"><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Password <span class="required">*</span></label>
                        <div style="position:relative;">
                            <input type="password" name="password" id="password" required placeholder="Min. 6 characters">
                            <button type="button" class="toggle-password" data-target="password" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--gray);"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password <span class="required">*</span></label>
                        <div style="position:relative;">
                            <input type="password" name="confirm_password" id="confirm_password" required placeholder="Re-enter your password">
                            <button type="button" class="toggle-password" data-target="confirm_password" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--gray);"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info" style="margin-bottom:20px;">
                    <i class="fas fa-info-circle"></i>
                    Your account will be in <strong>pending</strong> status until approved by an administrator.
                </div>

                <button type="submit" class="btn btn-green w-100"><i class="fas fa-chalkboard-teacher"></i> Register as Lecturer</button>
            </form>

            <div class="form-footer">
                Already have an account? <a href="login.php">Login here</a> &nbsp;|&nbsp;
                Are you a student? <a href="student_register.php">Register here</a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
