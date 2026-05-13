<?php
// ============================================================
// profile.php — View & Edit Portal Profile
// ============================================================
$portal_role = $_SESSION['user_role'] ?? 'student';
$page_title  = 'My Profile';
$active_nav  = 'profile';
require_once '_layout.php';

$user = $_portal_user;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_profile'])) {
    $full_name = sanitize($conn, $_POST['full_name'] ?? '');
    $phone     = sanitize($conn, $_POST['phone'] ?? '');
    $address   = sanitize($conn, $_POST['address'] ?? '');
    $new_pass  = $_POST['new_password'] ?? '';
    $confirm   = $_POST['confirm_password'] ?? '';

    if (!$full_name) {
        $error = 'Full name is required.';
    } elseif ($new_pass && strlen($new_pass) < 6) {
        $error = 'New password must be at least 6 characters.';
    } elseif ($new_pass && $new_pass !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $pass_sql = $new_pass ? ", password='" . password_hash($new_pass, PASSWORD_DEFAULT) . "'" : '';

        if ($portal_role === 'student') {
            $dob         = sanitize($conn, $_POST['dob'] ?? '');
            $gender      = sanitize($conn, $_POST['gender'] ?? '');
            $class_grade = sanitize($conn, $_POST['class_grade'] ?? '');
            $sql = "UPDATE students SET full_name='$full_name', phone='$phone', dob='$dob', gender='$gender', class_grade='$class_grade', address='$address'$pass_sql WHERE id=" . (int)$_SESSION['user_id'];
        } else {
            $subject      = sanitize($conn, $_POST['subject_specialization'] ?? '');
            $qualification = sanitize($conn, $_POST['qualification'] ?? '');
            $sql = "UPDATE lecturers SET full_name='$full_name', phone='$phone', subject_specialization='$subject', qualification='$qualification', address='$address'$pass_sql WHERE id=" . (int)$_SESSION['user_id'];
        }

        if (mysqli_query($conn, $sql)) {
            $_SESSION['user_name'] = $full_name;
            setFlash('success', 'Profile updated successfully!');
            redirect('profile.php');
        } else {
            $error = 'Update failed: ' . mysqli_error($conn);
        }
    }
}

portal_head();
portal_sidebar($user);
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
        <div class="prt-panel">
            <div class="prt-panel-body">
                <div class="profile-big-wrap">
                    <div class="profile-big-avatar"><?php echo strtoupper(substr($user['full_name'], 0, 1)); ?></div>
                    <div class="profile-big-name"><?php echo htmlspecialchars($user['full_name']); ?></div>
                    <div class="profile-big-email"><?php echo htmlspecialchars($user['email']); ?></div>
                    <div class="profile-big-meta">
                        <span class="prt-badge prt-badge-<?php echo htmlspecialchars($user['status'] ?? 'active'); ?>"><?php echo ucfirst(htmlspecialchars($user['status'] ?? 'active')); ?></span>
                        <?php if ($portal_role === 'student'): ?>
                        <span class="prt-badge prt-badge-info"><?php echo htmlspecialchars($user['class_grade']); ?></span>
                        <?php else: ?>
                        <span class="prt-badge prt-badge-info"><?php echo htmlspecialchars($user['subject_specialization'] ?? 'Lecturer'); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="divider"></div>
                <div class="info-grid">
                    <div class="info-row"><div class="lbl"><i class="fas fa-phone"></i> Phone</div><div class="val"><?php echo htmlspecialchars($user['phone'] ?: '—'); ?></div></div>
                    <?php if ($portal_role === 'student'): ?>
                    <div class="info-row"><div class="lbl"><i class="fas fa-venus-mars"></i> Gender</div><div class="val"><?php echo htmlspecialchars($user['gender']); ?></div></div>
                    <div class="info-row"><div class="lbl"><i class="fas fa-calendar"></i> DOB</div><div class="val"><?php echo $user['dob'] ? date('d M Y', strtotime($user['dob'])) : '—'; ?></div></div>
                    <?php else: ?>
                    <div class="info-row"><div class="lbl"><i class="fas fa-book-open"></i> Subject</div><div class="val"><?php echo htmlspecialchars($user['subject_specialization'] ?? '—'); ?></div></div>
                    <div class="info-row"><div class="lbl"><i class="fas fa-award"></i> Qualification</div><div class="val"><?php echo htmlspecialchars($user['qualification'] ?? '—'); ?></div></div>
                    <?php endif; ?>
                    <div class="info-row"><div class="lbl"><i class="fas fa-calendar-plus"></i> Joined</div><div class="val"><?php echo date('d M Y', strtotime($user['created_at'])); ?></div></div>
                </div>
            </div>
            <div class="prt-panel-foot">
                <a href="dashboard.php" class="prt-btn prt-btn-ghost prt-btn-sm"><i class="fas fa-arrow-left"></i> Dashboard</a>
            </div>
        </div>
    </div>

    <div>
        <form method="POST">
            <div class="prt-panel">
                <div class="prt-panel-head">
                    <div class="prt-panel-title"><i class="fas fa-user-edit"></i> Personal Information</div>
                </div>
                <div class="prt-panel-body">
                    <div class="prt-form-row">
                        <div class="prt-form-group">
                            <label class="prt-label">Full Name <span class="req">*</span></label>
                            <input class="prt-input" type="text" name="full_name" required value="<?php echo htmlspecialchars($user['full_name']); ?>">
                        </div>
                        <div class="prt-form-group">
                            <label class="prt-label">Email Address</label>
                            <input class="prt-input" type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                            <div class="prt-hint">Email cannot be changed. Contact admin if needed.</div>
                        </div>
                    </div>
                    <div class="prt-form-row">
                        <div class="prt-form-group">
                            <label class="prt-label">Phone Number</label>
                            <input class="prt-input" type="tel" name="phone" placeholder="+94 XX XXX XXXX" value="<?php echo htmlspecialchars($user['phone']); ?>">
                        </div>
                        <?php if ($portal_role === 'student'): ?>
                        <div class="prt-form-group">
                            <label class="prt-label">Gender</label>
                            <select class="prt-select" name="gender">
                                <?php foreach (['Male','Female','Other'] as $g): ?>
                                <option value="<?php echo $g; ?>" <?php echo $user['gender']===$g ? 'selected' : ''; ?>><?php echo $g; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php else: ?>
                        <div class="prt-form-group">
                            <label class="prt-label">Subject Specialization</label>
                            <input class="prt-input" type="text" name="subject_specialization" value="<?php echo htmlspecialchars($user['subject_specialization'] ?? ''); ?>">
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="prt-form-row">
                        <?php if ($portal_role === 'student'): ?>
                        <div class="prt-form-group">
                            <label class="prt-label">Date of Birth</label>
                            <input class="prt-input" type="date" name="dob" value="<?php echo htmlspecialchars($user['dob']); ?>" max="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="prt-form-group">
                            <label class="prt-label">Class / Grade</label>
                            <select class="prt-select" name="class_grade">
                                <?php for ($g=1; $g<=11; $g++): ?>
                                <option value="Grade <?php echo $g; ?>" <?php echo $user['class_grade']==="Grade $g" ? 'selected' : ''; ?>>Grade <?php echo $g; ?></option>
                                <?php endfor; ?>
                                <option value="1st Year A/L" <?php echo $user['class_grade']==="1st Year A/L" ? 'selected' : ''; ?>>1st Year A/L</option>
                                <option value="2nd Year A/L" <?php echo $user['class_grade']==="2nd Year A/L" ? 'selected' : ''; ?>>2nd Year A/L</option>
                            </select>
                        </div>
                        <?php else: ?>
                        <div class="prt-form-group">
                            <label class="prt-label">Qualification</label>
                            <input class="prt-input" type="text" name="qualification" value="<?php echo htmlspecialchars($user['qualification'] ?? ''); ?>">
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="prt-form-group">
                        <label class="prt-label"><?php echo $portal_role === 'student' ? 'Home Address' : 'Office Address'; ?></label>
                        <textarea class="prt-textarea" name="address" placeholder="Your address"><?php echo htmlspecialchars($user['address']); ?></textarea>
                    </div>
                </div>
            </div>

            <div class="prt-panel">
                <div class="prt-panel-head">
                    <div class="prt-panel-title"><i class="fas fa-lock"></i> Change Password</div>
                    <span class="fs-sm text-muted">Leave blank to keep current password</span>
                </div>
                <div class="prt-panel-body">
                    <div class="prt-form-row">
                        <div class="prt-form-group">
                            <label class="prt-label">New Password</label>
                            <div class="pass-wrap">
                                <input class="prt-input" type="password" name="new_password" id="newPassword" placeholder="Min. 6 characters">
                                <button type="button" class="pass-toggle"><i class="fas fa-eye"></i></button>
                            </div>
                            <div class="pass-strength" id="strengthBar"></div>
                            <div class="prt-hint">Use at least 6 characters with a mix of letters and numbers.</div>
                        </div>
                        <div class="prt-form-group">
                            <label class="prt-label">Confirm New Password</label>
                            <div class="pass-wrap">
                                <input class="prt-input" type="password" name="confirm_password" id="confirmPassword" placeholder="Re-enter new password">
                                <button type="button" class="pass-toggle"><i class="fas fa-eye"></i></button>
                            </div>
                            <div class="pass-match" id="matchMsg"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="btn-row">
                <button type="submit" name="save_profile" class="prt-btn prt-btn-primary">
                    <i class="fas fa-save"></i> Save All Changes
                </button>
                <a href="dashboard.php" class="prt-btn prt-btn-ghost"><i class="fas fa-times"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php portal_end(); ?>