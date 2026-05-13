<?php
// ============================================================
// admin/admin_profile.php  –  Admin Account Settings
// ============================================================
$page_title  = 'My Account';
$active_nav  = 'profile';
$breadcrumbs = [['label'=>'My Account']];
require_once '../_layout.php';

$id    = (int)$_SESSION['user_id'];
$error = '';

// Fetch admin
$admin = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM admins WHERE id=$id LIMIT 1"));

// Save profile
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['save_profile'])) {
    $full_name    = sanitize($conn, $_POST['full_name'] ?? '');
    $email        = sanitize($conn, $_POST['email']     ?? '');
    $current_pass = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password']  ?? '';
    $confirm      = $_POST['confirm_password'] ?? '';

    if (!$full_name || !$email) { $error = 'Name and email are required.'; }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $error = 'Enter a valid email address.'; }
    elseif ($new_password && !$current_pass) { $error = 'Current password is required to change password.'; }
    elseif ($new_password && !password_verify($current_pass, $admin['password'])) { $error = 'Current password is incorrect.'; }
    elseif ($new_password && strlen($new_password) < 6) { $error = 'New password must be at least 6 characters.'; }
    elseif ($new_password && $new_password !== $confirm) { $error = 'Passwords do not match.'; }
    else {
        // Check email uniqueness (excluding self)
        $chk = mysqli_fetch_assoc(mysqli_query($conn,"SELECT id FROM admins WHERE email='$email' AND id!=$id"));
        if ($chk) { $error = 'That email is already used by another admin.'; }
        else {
            $pass_sql = $new_password ? ", password='".password_hash($new_password, PASSWORD_DEFAULT)."'" : '';
            $sql = "UPDATE admins SET full_name='$full_name', email='$email'$pass_sql WHERE id=$id";
            if (mysqli_query($conn,$sql)) {
                $_SESSION['user_name']  = $full_name;
                $_SESSION['user_email'] = $email;
                setFlash('success','Account updated successfully!');
                redirect('admin_profile.php');
            } else { $error = 'Update failed: '.mysqli_error($conn); }
        }
    }
}

// Re-fetch after possible update
$admin = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM admins WHERE id=$id LIMIT 1"));

admin_head(); admin_sidebar(); admin_topbar();
?>

<?php if ($error): ?><div class="adm-flash adm-flash-error"><i class="fas fa-exclamation-circle"></i><?php echo htmlspecialchars($error); ?><button class="close-btn" onclick="this.parentElement.remove()">&times;</button></div><?php endif; ?>

<div style="display:grid;grid-template-columns:280px 1fr;gap:22px;align-items:start;">

    <!-- Left: Profile card -->
    <div class="panel" style="margin-bottom:0;">
        <div class="panel-body" style="text-align:center;padding:30px 20px;">
            <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--primary-light));display:flex;align-items:center;justify-content:center;font-size:32px;font-weight:800;color:#fff;margin:0 auto 16px;box-shadow:0 4px 16px rgba(122,27,28,.3);">
                <?php echo strtoupper(substr($admin['full_name'],0,1)); ?>
            </div>
            <div style="font-family:var(--font-heading);font-size:1.05rem;font-weight:700;margin-bottom:4px;"><?php echo htmlspecialchars($admin['full_name']); ?></div>
            <div style="font-size:.78rem;color:var(--gray);margin-bottom:14px;"><?php echo htmlspecialchars($admin['email']); ?></div>
            <span class="badge badge-info" style="font-size:.75rem;">Super Administrator</span>
            <div class="divider"></div>
            <dl style="text-align:left;font-size:.8rem;">
                <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid var(--border);">
                    <span class="text-muted fw-bold">Account ID</span><span>#<?php echo $admin['id']; ?></span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid var(--border);">
                    <span class="text-muted fw-bold">Role</span><span>Admin</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:6px 0;">
                    <span class="text-muted fw-bold">Since</span><span><?php echo date('M Y', strtotime($admin['created_at'])); ?></span>
                </div>
            </dl>
        </div>
    </div>

    <!-- Right: Edit form -->
    <div>
        <div class="panel" style="margin-bottom:20px;">
            <div class="panel-header">
                <div class="panel-title"><i class="fas fa-user-edit"></i> Edit Profile Information</div>
            </div>
            <div class="panel-body">
                <form method="POST">
                    <div class="adm-form-row">
                        <div class="adm-form-group">
                            <label class="adm-label">Full Name <span class="req">*</span></label>
                            <input class="adm-input" type="text" name="full_name" required value="<?php echo htmlspecialchars($admin['full_name']); ?>">
                        </div>
                        <div class="adm-form-group">
                            <label class="adm-label">Email Address <span class="req">*</span></label>
                            <input class="adm-input" type="email" name="email" required value="<?php echo htmlspecialchars($admin['email']); ?>">
                        </div>
                    </div>

                    <div class="divider"></div>
                    <p style="font-size:.82rem;color:var(--gray);font-weight:700;margin-bottom:14px;"><i class="fas fa-lock" style="color:var(--primary);margin-right:6px;"></i> Change Password <em style="font-weight:400;">(leave blank to keep current password)</em></p>

                    <div class="adm-form-group" style="margin-bottom:16px;">
                        <label class="adm-label">Current Password <span class="req">*</span> <em style="font-weight:400;color:var(--gray);">(required to change password)</em></label>
                        <input class="adm-input" type="password" name="current_password" placeholder="Enter your current password">
                    </div>

                    <div class="adm-form-row">
                        <div class="adm-form-group">
                            <label class="adm-label">New Password</label>
                            <input class="adm-input" type="password" name="new_password" id="newPass" placeholder="Min. 6 characters">
                        </div>
                        <div class="adm-form-group">
                            <label class="adm-label">Confirm New Password</label>
                            <input class="adm-input" type="password" name="confirm_password" id="confirmPass" placeholder="Re-enter new password">
                            <div class="adm-hint" id="passHint"></div>
                        </div>
                    </div>

                    <button type="submit" name="save_profile" class="adm-btn adm-btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title" style="color:var(--danger);"><i class="fas fa-exclamation-triangle"></i> Danger Zone</div>
            </div>
            <div class="panel-body">
                <p style="font-size:.85rem;color:var(--gray);margin-bottom:16px;">Performing a logout will end your current session immediately. All unsaved changes will be lost.</p>
                <a href="/school/logout.php" class="adm-btn adm-btn-danger" data-confirm="Log out of the admin panel?">
                    <i class="fas fa-sign-out-alt"></i> Logout Now
                </a>
            </div>
        </div>
    </div>
</div>

<script>
const np = document.getElementById('newPass');
const cp = document.getElementById('confirmPass');
const hint = document.getElementById('passHint');
function checkPw() {
    if (!cp.value) { hint.textContent=''; cp.style.borderColor=''; return; }
    if (np.value !== cp.value) {
        hint.textContent = '⚠ Passwords do not match';
        hint.style.color = 'var(--danger)';
        cp.style.borderColor = 'var(--danger)';
        cp.setCustomValidity('Passwords do not match');
    } else {
        hint.textContent = '✓ Passwords match';
        hint.style.color = 'var(--success)';
        cp.style.borderColor = 'var(--success)';
        cp.setCustomValidity('');
    }
}
if (np && cp) { np.addEventListener('input', checkPw); cp.addEventListener('input', checkPw); }
</script>

<?php admin_end(); ?>