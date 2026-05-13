<?php
// ============================================================
// settings.php — Account Settings
// ============================================================
$portal_role = $_SESSION['user_role'] ?? 'student';
$page_title  = 'Account Settings';
$active_nav  = 'settings';
require_once '_layout.php';

$account = $_portal_user;
$id       = (int)$_SESSION['user_id'];
$error    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current = $_POST['current_password'] ?? '';
    $new     = $_POST['new_password']     ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (!$current || !$new || !$confirm) {
        $error = 'All password fields are required.';
    } elseif (!password_verify($current, $account['password'])) {
        $error = 'Current password is incorrect.';
    } elseif (strlen($new) < 6) {
        $error = 'New password must be at least 6 characters.';
    } elseif ($new !== $confirm) {
        $error = 'New passwords do not match.';
    } else {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $table = ($portal_role === 'student') ? 'students' : 'lecturers';
        mysqli_query($conn,"UPDATE $table SET password='$hash' WHERE id=$id");
        setFlash('success','Password changed successfully!');
        redirect('settings.php');
    }
}

portal_head();
portal_sidebar($account);
portal_topbar();
?>

<?php if ($error): ?>
<div class="prt-flash prt-flash-error" style="border-radius:var(--radius);margin-bottom:16px;">
    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
    <button class="fclose" onclick="this.parentElement.remove()">&times;</button>
</div>
<?php endif; ?>

<div class="col-2" style="align-items:start;">
<div>

    <!-- Account Info -->
    <div class="prt-panel">
        <div class="prt-panel-head">
            <div class="prt-panel-title"><i class="fas fa-user-circle"></i> Account Overview</div>
        </div>
        <div class="prt-panel-body">
            <div class="setting-row">
                <div class="sr-info">
                    <h4>Full Name</h4>
                    <p><?php echo htmlspecialchars($account['full_name']); ?></p>
                </div>
                <a href="profile.php" class="prt-btn prt-btn-ghost prt-btn-sm"><i class="fas fa-edit"></i> Edit</a>
            </div>
            <div class="setting-row">
                <div class="sr-info">
                    <h4>Email Address</h4>
                    <p><?php echo htmlspecialchars($account['email']); ?></p>
                </div>
                <span class="prt-badge prt-badge-info">Read-only</span>
            </div>
            <div class="setting-row">
                <div class="sr-info">
                    <h4>Account Status</h4>
                    <p>Your account is currently <strong><?php echo $account['status']; ?></strong>.</p>
                </div>
                <span class="prt-badge prt-badge-<?php echo $account['status']; ?>"><?php echo ucfirst($account['status']); ?></span>
            </div>
            <div class="setting-row">
                <div class="sr-info">
                    <h4><?php echo $portal_role === 'student' ? 'Class / Grade' : 'Subject'; ?></h4>
                    <p><?php echo htmlspecialchars($portal_role === 'student' ? $account['class_grade'] : ($account['subject_specialization'] ?? 'N/A')); ?></p>
                </div>
                <a href="profile.php" class="prt-btn prt-btn-ghost prt-btn-sm"><i class="fas fa-edit"></i> Change</a>
            </div>
            <div class="setting-row">
                <div class="sr-info">
                    <h4>Member Since</h4>
                    <p><?php echo date('F j, Y', strtotime($account['created_at'])); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Danger zone -->
    <div class="prt-panel">
        <div class="prt-panel-head">
            <div class="prt-panel-title" style="color:var(--danger);"><i class="fas fa-exclamation-triangle"></i> Session</div>
        </div>
        <div class="prt-panel-body">
            <div class="setting-row">
                <div class="sr-info">
                    <h4>Log Out</h4>
                    <p>End your current session and return to the login page.</p>
                </div>
                <a href="/school/logout.php" class="prt-btn prt-btn-danger prt-btn-sm" data-confirm="Log out now?">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </div>

</div>
<div>

    <!-- Change Password -->
    <div class="prt-panel">
        <div class="prt-panel-head">
            <div class="prt-panel-title"><i class="fas fa-lock"></i> Change Password</div>
        </div>
        <div class="prt-panel-body">
            <form method="POST">
                <div class="prt-form-group">
                    <label class="prt-label">Current Password <span class="req">*</span></label>
                    <div class="pass-wrap">
                        <input class="prt-input" type="password" name="current_password" placeholder="Enter your current password">
                        <button type="button" class="pass-toggle"><i class="fas fa-eye"></i></button>
                    </div>
                </div>
                <div class="prt-form-group">
                    <label class="prt-label">New Password <span class="req">*</span></label>
                    <div class="pass-wrap">
                        <input class="prt-input" type="password" name="new_password" id="newPassword" placeholder="Min. 6 characters">
                        <button type="button" class="pass-toggle"><i class="fas fa-eye"></i></button>
                    </div>
                    <div class="pass-strength" id="strengthBar"></div>
                </div>
                <div class="prt-form-group">
                    <label class="prt-label">Confirm New Password <span class="req">*</span></label>
                    <div class="pass-wrap">
                        <input class="prt-input" type="password" name="confirm_password" id="confirmPassword" placeholder="Re-enter new password">
                        <button type="button" class="pass-toggle"><i class="fas fa-eye"></i></button>
                    </div>
                    <div class="pass-match" id="matchMsg"></div>
                </div>
                <button type="submit" name="change_password" class="prt-btn prt-btn-primary">
                    <i class="fas fa-key"></i> Update Password
                </button>
            </form>
        </div>
    </div>

    <!-- Help box -->
    <div class="prt-panel" style="border-left:4px solid var(--role-color);">
        <div class="prt-panel-body">
            <h4 style="margin-bottom:8px;font-size:.9rem;"><i class="fas fa-question-circle" style="color:var(--role-color);margin-right:6px;"></i>Need Help?</h4>
            <p style="font-size:.8rem;color:var(--gray);line-height:1.7;">If you have issues with your account, please contact the school administration office or email us directly.</p>
            <div class="btn-row" style="margin-top:12px;">
                <a href="/school/contact.php" class="prt-btn prt-btn-ghost prt-btn-sm"><i class="fas fa-envelope"></i> Contact School</a>
            </div>
        </div>
    </div>

</div>
</div>

<?php portal_end(); ?>