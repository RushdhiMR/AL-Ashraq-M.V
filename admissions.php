<?php
// ============================================================
// admissions.php - Admissions Page
// ============================================================
$page_title = 'Admissions';
require_once __DIR__ . '/includes/header.php';

$error = '';
$success = '';

// Handle inquiry form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_inquiry'])) {
    $student_name   = sanitize($conn, $_POST['student_name'] ?? '');
    $parent_name    = sanitize($conn, $_POST['parent_name'] ?? '');
    $email          = sanitize($conn, $_POST['email'] ?? '');
    $phone          = sanitize($conn, $_POST['phone'] ?? '');
    $applying_grade = sanitize($conn, $_POST['applying_grade'] ?? '');
    $message        = sanitize($conn, $_POST['message'] ?? '');

    if (!$student_name || !$parent_name || !$email || !$phone || !$applying_grade) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $sql = "INSERT INTO admission_inquiries (student_name, parent_name, email, phone, applying_grade, message)
                VALUES ('$student_name','$parent_name','$email','$phone','$applying_grade','$message')";
        if (mysqli_query($conn, $sql)) {
            setFlash('success', 'Your admission inquiry has been submitted successfully. We will contact you shortly.');
            redirect('admissions.php');
        } else {
            $error = 'Something went wrong. Please try again.';
        }
    }
}
?>

<div class="page-hero">
    <h1>Admissions</h1>
    <p>Begin your child's journey to excellence at Al-Ashraq M.V</p>
    <div class="breadcrumb"><a href="index.php">Home</a><span>/</span><span>Admissions</span></div>
</div>

<!-- Requirements -->
<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>Admission Requirements</h2>
            <p>What you need to know before applying to Al-Ashraq M.V</p>
            <div class="title-line"></div>
        </div>
        <div class="grid-2">
            <div class="dash-card">
                <h2><i class="fas fa-list-check" style="color:var(--primary);margin-right:8px;"></i> General Requirements</h2>
                <ul style="color:var(--gray);line-height:2.3;padding-left:20px;list-style:none;">
                    <li><i class="fas fa-check-circle" style="color:var(--primary);margin-right:8px;"></i> Student must be within the appropriate age range for the grade applied for</li>
                    <li><i class="fas fa-check-circle" style="color:var(--primary);margin-right:8px;"></i> Previous school reports / progress reports (for Grades 2 and above)</li>
                    <li><i class="fas fa-check-circle" style="color:var(--primary);margin-right:8px;"></i> Birth certificate (original and copy)</li>
                    <li><i class="fas fa-check-circle" style="color:var(--primary);margin-right:8px;"></i> National Identity Card or passport copy (parent/guardian)</li>
                    <li><i class="fas fa-check-circle" style="color:var(--primary);margin-right:8px;"></i> Recent passport-size photographs (x 4)</li>
                    <li><i class="fas fa-check-circle" style="color:var(--primary);margin-right:8px;"></i> Proof of residence (utility bill or letter from Grama Niladhari)</li>
                    <li><i class="fas fa-check-circle" style="color:var(--primary);margin-right:8px;"></i> Immunisation/vaccination records (for primary entry)</li>
                </ul>
            </div>
            <div class="dash-card">
                <h2><i class="fas fa-graduation-cap" style="color:var(--primary);margin-right:8px;"></i> For A/L Applicants</h2>
                <ul style="color:var(--gray);line-height:2.3;padding-left:20px;list-style:none;">
                    <li><i class="fas fa-check-circle" style="color:var(--primary);margin-right:8px;"></i> G.C.E. O/L result sheet (original and copy)</li>
                    <li><i class="fas fa-check-circle" style="color:var(--primary);margin-right:8px;"></i> Minimum 3 passes including required stream subjects</li>
                    <li><i class="fas fa-check-circle" style="color:var(--primary);margin-right:8px;"></i> School leaving certificate from previous school</li>
                    <li><i class="fas fa-check-circle" style="color:var(--primary);margin-right:8px;"></i> Birth certificate (original and copy)</li>
                    <li><i class="fas fa-check-circle" style="color:var(--primary);margin-right:8px;"></i> Recent passport-size photographs (x 4)</li>
                    <li><i class="fas fa-check-circle" style="color:var(--primary);margin-right:8px;"></i> Parental/guardian consent letter</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Steps -->
<section class="section section-alt">
    <div class="container">
        <div class="section-title">
            <h2>Admission Steps</h2>
            <p>A simple process to join the Al-Ashraq M.V family</p>
            <div class="title-line"></div>
        </div>
        <div style="max-width:800px;margin:0 auto;">
            <?php
            $steps = [
                ['fas fa-file-alt', 'Step 1: Submit Inquiry', 'Complete the admission inquiry form below or visit the school administration office to express your interest.'],
                ['fas fa-phone', 'Step 2: Initial Contact', 'Our admissions team will contact you within 3 working days to provide further guidance and schedule a meeting.'],
                ['fas fa-folder-open', 'Step 3: Submit Documents', 'Bring all required documents to the school office. Our team will verify and process your application.'],
                ['fas fa-user-check', 'Step 4: Assessment (if applicable)', 'Depending on the grade, a short placement assessment may be conducted to determine the appropriate class.'],
                ['fas fa-handshake', 'Step 5: Confirmation & Enrollment', 'Upon acceptance, complete enrollment formalities, pay the applicable fees, and receive your student details.'],
            ];
            foreach ($steps as $i => $step): ?>
            <div style="display:flex;gap:20px;margin-bottom:28px;align-items:flex-start;">
                <div style="background:var(--primary);color:white;width:50px;height:50px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:18px;">
                    <i class="<?php echo $step[0]; ?>"></i>
                </div>
                <div>
                    <h4 style="color:var(--primary-dark);margin-bottom:6px;"><?php echo $step[1]; ?></h4>
                    <p style="color:var(--gray);"><?php echo $step[2]; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Inquiry Form -->
<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>Admission Inquiry Form</h2>
            <p>Fill in the form below and we will get back to you promptly</p>
            <div class="title-line"></div>
        </div>

        <div class="form-card" style="max-width:750px;">
            <?php if ($error): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="admissions.php">
                <div class="form-row">
                    <div class="form-group">
                        <label>Student's Full Name <span class="required">*</span></label>
                        <input type="text" name="student_name" required placeholder="Enter student's full name" value="<?php echo htmlspecialchars($_POST['student_name'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Parent / Guardian Name <span class="required">*</span></label>
                        <input type="text" name="parent_name" required placeholder="Enter parent/guardian name" value="<?php echo htmlspecialchars($_POST['parent_name'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Email Address <span class="required">*</span></label>
                        <input type="email" name="email" required placeholder="your@email.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Phone Number <span class="required">*</span></label>
                        <input type="tel" name="phone" required placeholder="+94 XX XXX XXXX" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Grade Applying For <span class="required">*</span></label>
                    <select name="applying_grade" required>
                        <option value="">-- Select Grade --</option>
                        <?php for ($g = 1; $g <= 11; $g++): ?>
                            <option value="Grade <?php echo $g; ?>" <?php echo (($_POST['applying_grade'] ?? '') === "Grade $g") ? 'selected' : ''; ?>>Grade <?php echo $g; ?></option>
                        <?php endfor; ?>
                        <option value="1st Year A/L" <?php echo (($_POST['applying_grade'] ?? '') === "1st Year A/L") ? 'selected' : ''; ?>>1st Year A/L</option>
                        <option value="2nd Year A/L" <?php echo (($_POST['applying_grade'] ?? '') === "2nd Year A/L") ? 'selected' : ''; ?>>2nd Year A/L</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Message / Additional Information</label>
                    <textarea name="message" placeholder="Any additional information or questions you may have..."><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                </div>
                <button type="submit" name="submit_inquiry" class="btn btn-green w-100"><i class="fas fa-paper-plane"></i> Submit Inquiry</button>
            </form>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
