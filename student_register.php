<?php
// ============================================================
// student_register.php - Student Registration
// ============================================================
$page_title = 'Student Registration';
require_once __DIR__ . '/includes/header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name   = sanitize($conn, $_POST['full_name'] ?? '');
    $email       = sanitize($conn, $_POST['email'] ?? '');
    $phone       = sanitize($conn, $_POST['phone'] ?? '');
    $gender      = sanitize($conn, $_POST['gender'] ?? '');
    $dob         = sanitize($conn, $_POST['dob'] ?? '');
    $class_grade = sanitize($conn, $_POST['class_grade'] ?? '');
    $address     = sanitize($conn, $_POST['address'] ?? '');
    $password    = $_POST['password'] ?? '';
    $confirm     = $_POST['confirm_password'] ?? '';

    // Validate
    if (!$full_name || !$email || !$phone || !$gender || !$dob || !$class_grade || !$password) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        // Check if email exists
        $chk = mysqli_query($conn, "SELECT id FROM students WHERE email='$email'");
        if (mysqli_num_rows($chk) > 0) {
            $error = 'An account with this email already exists.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO students (full_name, email, phone, gender, dob, class_grade, address, password)
                    VALUES ('$full_name','$email','$phone','$gender','$dob','$class_grade','$address','$hashed')";
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
    <h1>Student Registration</h1>
    <p>Create your student account to access the school portal</p>
    <div class="breadcrumb"><a href="index.php">Home</a><span>/</span><span>Student Registration</span></div>
</div>

<section class="form-section">
    <div class="container">
        <div class="form-card" style="max-width:750px;">
            <h2><i class="fas fa-user-graduate" style="color:var(--primary);margin-right:10px;"></i>Student Registration</h2>
            <p class="form-subtitle">All fields marked with <span style="color:red;">*</span> are required. Your account will be reviewed and approved by an administrator.</p>

            <?php if ($error): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="student_register.php" novalidate>

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
                        <label>Gender <span class="required">*</span></label>
                        <select name="gender" required>
                            <option value="">-- Select Gender --</option>
                            <option value="Male"   <?php echo (($_POST['gender'] ?? '')==='Male')  ?'selected':''; ?>>Male</option>
                            <option value="Female" <?php echo (($_POST['gender'] ?? '')==='Female')?'selected':''; ?>>Female</option>
                            <option value="Other"  <?php echo (($_POST['gender'] ?? '')==='Other') ?'selected':''; ?>>Other</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Date of Birth <span class="required">*</span></label>
                        <input type="date" name="dob" required value="<?php echo htmlspecialchars($_POST['dob'] ?? ''); ?>" max="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="form-group">
                        <label>Class / Grade <span class="required">*</span></label>
                        <select name="class_grade" required>
                            <option value="">-- Select Grade --</option>
                            <?php for ($g = 1; $g <= 11; $g++): ?>
                                <option value="Grade <?php echo $g; ?>" <?php echo (($_POST['class_grade'] ?? '')==="Grade $g")?'selected':''; ?>>Grade <?php echo $g; ?></option>
                            <?php endfor; ?>
                            <option value="1st Year A/L" <?php echo (($_POST['class_grade'] ?? '')==="1st Year A/L")?'selected':''; ?>>1st Year A/L</option>
                            <option value="2nd Year A/L" <?php echo (($_POST['class_grade'] ?? '')==="2nd Year A/L")?'selected':''; ?>>2nd Year A/L</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Home Address</label>
                    <textarea name="address" placeholder="Enter your home address"><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
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
                    Your account will be in <strong>pending</strong> status until approved by an administrator. You will be able to log in once approved.
                </div>

                <button type="submit" class="btn btn-green w-100"><i class="fas fa-user-plus"></i> Register as Student</button>
            </form>

            <div class="form-footer">
                Already have an account? <a href="login.php">Login here</a> &nbsp;|&nbsp;
                Are you a lecturer? <a href="lecturer_register.php">Register here</a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
