<?php
// ============================================================
// admin/login.php - Admin Login Page
// ============================================================
$page_title = 'Admin Login';
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../includes/db.php';

// Redirect if already logged in as admin
if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'admin') {
    header('Location: dashboard.php'); exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = sanitize($conn, $_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $error = 'Please fill in all fields.';
    } else {
        $res = mysqli_query($conn, "SELECT * FROM admins WHERE email='$email' LIMIT 1");
        if ($res) $user = mysqli_fetch_assoc($res);

        if (!$user) {
            $error = 'No admin account found with that email address.';
        } elseif (!password_verify($password, $user['password'])) {
            // Check if password matches plain text (for backward compatibility)
            if ($password === $user['password']) {
                // Plain text match - update to hashed password
                $new_hash = password_hash($password, PASSWORD_DEFAULT);
                mysqli_query($conn, "UPDATE admins SET password='$new_hash' WHERE id=" . $user['id']);
            } else {
                $error = 'Incorrect password. Please try again.';
            }
        }

        if (!$error) {
            // Login successful – set session
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_role'] = 'admin';
            header('Location: dashboard.php'); exit();
        }
    }
}

require_once '../_layout.php';
admin_head();
?>

<style>
    body {
        background: radial-gradient(circle at top left, rgba(255,255,255,.12), transparent 22%),
                    linear-gradient(135deg, #1f2937, #374151 55%, #4b5563 100%);
        min-height: 100vh;
    }
    .auth-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        position: relative;
        z-index: 1;
    }
    .auth-page::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at top right, rgba(255,255,255,.08), transparent 30%),
                    radial-gradient(circle at bottom left, rgba(122,27,28,.1), transparent 24%);
        pointer-events: none;
        filter: blur(1px);
    }
    .auth-card {
        position: relative;
        z-index: 2;
        width: 100%;
        max-width: 500px;
        padding: 46px 42px;
        background: rgba(255,255,255,.96);
        border-radius: 28px;
        box-shadow: 0 32px 70px rgba(15,23,42,.2);
        border: 1px solid rgba(15,23,42,.08);
        overflow: hidden;
        backdrop-filter: blur(8px);
    }
    .auth-card::after {
        content: '';
        position: absolute;
        top: -40px;
        right: -50px;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: rgba(122,27,28,.12);
    }
    .auth-card .auth-logo {
        text-align: center;
        margin-bottom: 32px;
    }
    .auth-card .auth-logo i {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 68px;
        height: 68px;
        border-radius: 18px;
        background: rgba(122,27,28,.12);
        color: var(--primary);
        font-size: 32px;
        margin-bottom: 18px;
    }
    .auth-card .auth-logo h2 {
        font-size: 2rem;
        margin: 0;
        letter-spacing: -.02em;
    }
    .auth-card .auth-logo p {
        margin-top: 8px;
        color: var(--gray);
        font-size: 15px;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-group label {
        display: block;
        font-size: .95rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 10px;
    }
    .form-group input {
        width: 100%;
        padding: 14px 16px;
        padding-right: 48px;
        border: 1px solid rgba(15,23,42,.14);
        border-radius: 16px;
        font-family: var(--font-body);
        font-size: .98rem;
        color: #111827;
        background: #fff;
        transition: all .22s ease;
    }
    .form-group input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 5px rgba(122,27,28,.08);
    }
    .form-group > div {
        position: relative;
    }
    .toggle-password {
        position: absolute;
        top: 50%;
        right: 12px;
        transform: translateY(-50%);
        width: 34px;
        height: 34px;
        border-radius: 12px;
        background: rgba(15,23,42,.04);
        border: none;
        color: var(--gray);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: background .2s ease, color .2s ease;
    }
    .toggle-password:hover {
        background: rgba(122,27,28,.16);
        color: var(--primary);
    }
    .btn {
        width: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 14px 18px;
        border-radius: 16px;
        font-family: var(--font-body);
        font-size: 1rem;
        font-weight: 700;
        border: none;
        background: linear-gradient(135deg, var(--primary), var(--accent));
        color: #fff;
        cursor: pointer;
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 18px 36px rgba(15,23,42,.2);
    }
    .auth-links {
        text-align: center;
        margin-top: 26px;
        font-size: 14px;
        color: #475569;
    }
    .auth-links a {
        color: var(--primary);
        font-weight: 700;
    }
    .auth-links a:hover {
        color: var(--accent-dark);
    }
    .auth-links i {
        margin-right: 6px;
    }
    .alert {
        padding: 16px 20px;
        border-radius: 18px;
        margin-bottom: 22px;
        font-size: 14px;
        border: 1px solid transparent;
    }
    .alert-danger {
        background: #fff1f2;
        color: #86181d;
        border-color: #fecdd3;
    }
    .alert-info {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #bfdbfe;
    }
    .alert-success {
        background: #ecfdf5;
        color: #166534;
        border-color: #bbf7d0;
    }
    .required {
        color: var(--danger);
    }
    @media (max-width: 640px) {
        .auth-card {
            padding: 30px 22px;
        }
        .auth-card .auth-logo i {
            width: 58px;
            height: 58px;
            font-size: 28px;
        }
        .auth-card .auth-logo h2 {
            font-size: 1.6rem;
        }
    }
</style>

<div class="auth-page">
    <div class="auth-card">
        <div class="auth-logo">
            <i class="fas fa-user-shield"></i>
            <h2>Al-Ashraq M.V</h2>
            <p>Administrator Login</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label>Email Address <span class="required">*</span></label>
                <input type="email" name="email" required placeholder="admin@alashraq.edu" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label>Password <span class="required">*</span></label>
                <div style="position:relative;">
                    <input type="password" name="password" id="password" required placeholder="Enter your password">
                    <button type="button" class="toggle-password" data-target="password" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--gray);"><i class="fas fa-eye"></i></button>
                </div>
            </div>

            <button type="submit" class="btn btn-green w-100" style="margin-top:6px;"><i class="fas fa-sign-in-alt"></i> Login as Admin</button>
        </form>

        <div class="divider"></div>
        <div class="auth-links">
            <a href="../login.php" style="color:var(--gray);margin-top:8px;display:inline-block;"><i class="fas fa-arrow-left"></i> Back to Main Login</a>
        </div>
    </div>
</div>

<?php admin_end(); ?>