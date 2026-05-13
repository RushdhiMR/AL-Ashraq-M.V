<?php
// ============================================================
// login.php - Unified Login Page
// ============================================================
$page_title = 'Login';
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/db.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['user_role'];
    if ($role === 'admin') {
        header('Location: /school/admin/dashboard.php');
    } elseif ($role === 'student') {
        header('Location: /school/dashboard.php');
    } elseif ($role === 'lecturer') {
        header('Location: /school/lecturer/dashboard.php');
    }
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role     = $_POST['role'] ?? '';
    $email    = sanitize($conn, $_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$role || !$email || !$password) {
        $error = 'Please fill in all fields and select your role.';
    } else {
        $user  = null;
        $table = '';

        if ($role === 'student') {
            $res = mysqli_query($conn, "SELECT * FROM students WHERE email='$email' LIMIT 1");
            if ($res) $user = mysqli_fetch_assoc($res);
        } elseif ($role === 'lecturer') {
            $res = mysqli_query($conn, "SELECT * FROM lecturers WHERE email='$email' LIMIT 1");
            if ($res) $user = mysqli_fetch_assoc($res);
        } elseif ($role === 'admin') {
            $res = mysqli_query($conn, "SELECT * FROM admins WHERE email='$email' LIMIT 1");
            if ($res) $user = mysqli_fetch_assoc($res);
        } else {
            $error = 'Invalid role selected.';
        }

        if (!$error) {
            if (!$user) {
                $error = 'No account found with that email address.';
            } elseif (!password_verify($password, $user['password'])) {
                // Check if password matches plain text (for backward compatibility)
                if ($password === $user['password']) {
                    // Plain text match - update to hashed password
                    $new_hash = password_hash($password, PASSWORD_DEFAULT);
                    $table = ($role === 'student') ? 'students' : 'lecturers';
                    mysqli_query($conn, "UPDATE $table SET password='$new_hash' WHERE id=" . $user['id']);
                } else {
                    $error = 'Incorrect password. Please try again.';
                }
            } elseif (isset($user['status'])) {
                if ($user['status'] === 'pending') {
                    $error = 'Your account is pending admin approval. Please wait for confirmation.';
                } elseif ($user['status'] === 'rejected') {
                    $error = 'Your account has been rejected. Please contact the school administration.';
                }
            }

            if (!$error) {
                // Login successful – set session
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_role'] = $role;
                $_SESSION['user_email']= $user['email'];

                setFlash('success', 'Welcome back, ' . $user['full_name'] . '!');

                if ($role === 'admin')    redirect('/school/admin/dashboard.php');
                elseif ($role === 'student')  redirect('/school/dashboard.php');
                elseif ($role === 'lecturer') redirect('/school/lecturer/dashboard.php');
            }
        }
    }
}

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Al-Ashraq M.V</title>
    <link rel="stylesheet" href="/school/style.css?v=4">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Nunito:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php if ($flash): ?>
<div class="flash-message flash-<?php echo $flash['type']; ?>" id="flashMsg">
    <div class="container">
        <i class="fas <?php echo $flash['type']==='success'?'fa-check-circle':'fa-exclamation-circle'; ?>"></i>
        <?php echo htmlspecialchars($flash['message']); ?>
        <button onclick="this.parentElement.parentElement.remove()" class="flash-close">&times;</button>
    </div>
</div>
<?php endif; ?>

<div class="auth-page">
    <div class="auth-card">
        <div class="auth-logo">
            <i class="fas fa-graduation-cap"></i>
            <h2>Al-Ashraq M.V</h2>
            <p>School Portal Login</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">

            <!-- Role Selector -->
            <div class="form-group">
                <label>Login As <span class="required">*</span></label>
                <div class="role-selector">
                    <div class="role-option">
                        <input type="radio" name="role" id="role_student" value="student" <?php echo (($_POST['role'] ?? 'student')==='student')?'checked':''; ?>>
                        <label for="role_student">
                            <i class="fas fa-user-graduate"></i>
                            Student
                        </label>
                    </div>
                    <div class="role-option">
                        <input type="radio" name="role" id="role_lecturer" value="lecturer" <?php echo (($_POST['role'] ?? '')==='lecturer')?'checked':''; ?>>
                        <label for="role_lecturer">
                            <i class="fas fa-chalkboard-teacher"></i>
                            Lecturer
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Email Address <span class="required">*</span></label>
                <input type="email" name="email" required placeholder="your@email.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label>Password <span class="required">*</span></label>
                <div style="position:relative;">
                    <input type="password" name="password" id="password" required placeholder="Enter your password">
                    <button type="button" class="toggle-password" data-target="password" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--gray);"><i class="fas fa-eye"></i></button>
                </div>
            </div>

            <button type="submit" class="btn btn-green w-100" style="margin-top:6px;"><i class="fas fa-sign-in-alt"></i> Login</button>
        </form>

        <div class="divider"></div>
        <div class="auth-links">
            New student? <a href="/school/student_register.php">Register here</a><br>
            Are you a lecturer? <a href="/school/lecturer_register.php">Register here</a><br>
            <a href="/school/index.php" style="color:var(--gray);margin-top:8px;display:inline-block;"><i class="fas fa-arrow-left"></i> Back to Home</a>
        </div>
    </div>
</div>

<script src="/school/script.js"></script>
</body>
</html>
