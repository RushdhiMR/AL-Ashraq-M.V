<?php
// ============================================================
// admin/school_info.php  –  Edit School Information
// ============================================================
$page_title  = 'School Information';
$active_nav  = 'school_info';
$breadcrumbs = [['label'=>'School Info']];
require_once '../_layout.php';

$error = '';

$res  = mysqli_query($conn,"SELECT * FROM school_info LIMIT 1");
$info = $res ? (mysqli_fetch_assoc($res) ?? []) : [];
$v    = fn($key,$default='') => htmlspecialchars($info[$key] ?? $default);

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['save_info'])) {
    $f = [
        'school_name'       => sanitize($conn, $_POST['school_name']       ?? ''),
        'tagline'           => sanitize($conn, $_POST['tagline']           ?? ''),
        'address'           => sanitize($conn, $_POST['address']           ?? ''),
        'phone'             => sanitize($conn, $_POST['phone']             ?? ''),
        'email'             => sanitize($conn, $_POST['email']             ?? ''),
        'established_year'  => sanitize($conn, $_POST['established_year']  ?? ''),
        'principal_name'    => sanitize($conn, $_POST['principal_name']    ?? ''),
        'principal_message' => sanitize($conn, $_POST['principal_message'] ?? ''),
        'vision'            => sanitize($conn, $_POST['vision']            ?? ''),
        'mission'           => sanitize($conn, $_POST['mission']           ?? ''),
    ];

    // Ensure there is a column available for the principal photo.
    $column_check = mysqli_query($conn, "SHOW COLUMNS FROM school_info LIKE 'principal_image'");
    if ($column_check && mysqli_num_rows($column_check) === 0) {
        mysqli_query($conn, "ALTER TABLE school_info ADD COLUMN principal_image VARCHAR(255) DEFAULT NULL");
    }

    $principal_image = $info['principal_image'] ?? '';
    if (!empty($_FILES['principal_image']['name'])) {
        $dir = $_SERVER['DOCUMENT_ROOT'].'/school/uploads/principal/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $ext = strtolower(pathinfo($_FILES['principal_image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];
        if (!in_array($ext, $allowed)) {
            $error = 'Invalid image format. Use JPG, PNG, GIF or WEBP.';
        } elseif ($_FILES['principal_image']['size'] > 5 * 1024 * 1024) {
            $error = 'File too large. Maximum 5 MB.';
        } else {
            $fn = 'principal_'.time().'_'.rand(1000,9999).'.'.$ext;
            if (move_uploaded_file($_FILES['principal_image']['tmp_name'], $dir.$fn)) {
                if ($principal_image && strpos($principal_image, 'placeholder') === false) {
                    $old = $_SERVER['DOCUMENT_ROOT'].'/school/'.$principal_image;
                    if (file_exists($old)) @unlink($old);
                }
                $principal_image = 'uploads/principal/'.$fn;
            } else {
                $error = 'Upload failed. Check uploads/principal/ permissions.';
            }
        }
    }

    if (!$f['school_name']) { $error = 'School name cannot be empty.'; }
    else {
        $exists = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM school_info"))['c'];
        if ($exists) {
            $sql = "UPDATE school_info SET
                school_name='{$f['school_name']}', tagline='{$f['tagline']}', address='{$f['address']}',
                phone='{$f['phone']}', email='{$f['email']}', established_year='{$f['established_year']}',
                principal_name='{$f['principal_name']}', principal_message='{$f['principal_message']}',
                principal_image='{$principal_image}', vision='{$f['vision']}', mission='{$f['mission']}' LIMIT 1";
        } else {
            $f['principal_image'] = $principal_image;
            $keys = implode(',', array_keys($f));
            $vals = implode("','", array_values($f));
            $sql = "INSERT INTO school_info ($keys) VALUES ('$vals')";
        }
        if (!$error) {
            if (mysqli_query($conn,$sql)) { setFlash('success','School information saved successfully!'); redirect('school_info.php'); }
            else { $error = 'Database error: '.mysqli_error($conn); }
        }
    }
}

admin_head(); admin_sidebar(); admin_topbar();
?>

<?php if ($error): ?><div class="adm-flash adm-flash-error"><i class="fas fa-exclamation-circle"></i><?php echo htmlspecialchars($error); ?><button class="close-btn" onclick="this.parentElement.remove()">&times;</button></div><?php endif; ?>

<div class="adm-flash adm-flash-info" style="margin-bottom:20px;">
    <i class="fas fa-info-circle"></i>
    Changes saved here reflect immediately across the <strong>entire public website</strong> — including the home page, about page, footer, and contact page.
</div>

<form method="POST" enctype="multipart/form-data">

    <!-- Section 1: Basic Info -->
    <div class="panel" style="margin-bottom:20px;">
        <div class="panel-header">
            <div class="panel-title"><i class="fas fa-school"></i> Basic School Information</div>
        </div>
        <div class="panel-body">
            <div class="adm-form-row">
                <div class="adm-form-group">
                    <label class="adm-label">School Name <span class="req">*</span></label>
                    <input class="adm-input" type="text" name="school_name" required value="<?php echo $v('school_name','Al-Ashraq M.V'); ?>">
                </div>
                <div class="adm-form-group">
                    <label class="adm-label">Tagline / Motto</label>
                    <input class="adm-input" type="text" name="tagline" placeholder="e.g. Nurturing Minds, Shaping Futures" value="<?php echo $v('tagline'); ?>">
                </div>
            </div>
            <div class="adm-form-row">
                <div class="adm-form-group">
                    <label class="adm-label">Phone Number</label>
                    <input class="adm-input" type="text" name="phone" placeholder="+94 XX XXX XXXX" value="<?php echo $v('phone'); ?>">
                </div>
                <div class="adm-form-group">
                    <label class="adm-label">Email Address</label>
                    <input class="adm-input" type="email" name="email" placeholder="info@alashraq.edu" value="<?php echo $v('email'); ?>">
                </div>
            </div>
            <div class="adm-form-row">
                <div class="adm-form-group">
                    <label class="adm-label">Year Established</label>
                    <input class="adm-input" type="number" name="established_year" placeholder="e.g. 1980" min="1800" max="<?php echo date('Y'); ?>" value="<?php echo $v('established_year'); ?>">
                    <div class="adm-hint">Used to calculate years of service on the homepage.</div>
                </div>
                <div class="adm-form-group">
                    <label class="adm-label">Full Address</label>
                    <input class="adm-input" type="text" name="address" placeholder="Street, City, Province" value="<?php echo $v('address'); ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2: Principal -->
    <div class="panel" style="margin-bottom:20px;">
        <div class="panel-header">
            <div class="panel-title"><i class="fas fa-user-tie"></i> Principal's Details</div>
        </div>
        <div class="panel-body">
            <div class="adm-form-group">
                <label class="adm-label">Principal's Photo</label>
                <input class="adm-input" type="file" name="principal_image" accept="image/*">
                <div class="adm-hint">Upload a principal portrait (JPG/PNG/GIF/WEBP, max 5 MB). Leave blank to keep the current image.</div>
                <?php if (!empty($info['principal_image'])): ?>
                <div class="img-preview-box" style="margin-top:12px; max-width:220px;">
                    <img src="/school/<?php echo htmlspecialchars($info['principal_image']); ?>" alt="Principal photo">
                </div>
                <?php endif; ?>
            </div>
            <div class="adm-form-group">
                <label class="adm-label">Principal's Full Name</label>
                <input class="adm-input" type="text" name="principal_name" placeholder="Full name as it should appear on the site" value="<?php echo $v('principal_name'); ?>">
            </div>
            <div class="adm-form-group">
                <label class="adm-label">Principal's Welcome Message</label>
                <textarea class="adm-textarea" name="principal_message" rows="9"
                    placeholder="Write the principal's message here. This appears on the Home page and the About page…"><?php echo $v('principal_message'); ?></textarea>
                <div class="adm-hint">Supports plain text. Press Enter for new paragraphs. This text will appear in quotes.</div>
            </div>
        </div>
    </div>

    <!-- Section 3: Vision & Mission -->
    <div class="panel" style="margin-bottom:24px;">
        <div class="panel-header">
            <div class="panel-title"><i class="fas fa-eye"></i> Vision &amp; Mission</div>
        </div>
        <div class="panel-body">
            <div class="adm-form-group">
                <label class="adm-label">School Vision</label>
                <textarea class="adm-textarea" name="vision" rows="4"
                    placeholder="Describe the school's long-term vision…"><?php echo $v('vision'); ?></textarea>
            </div>
            <div class="adm-form-group">
                <label class="adm-label">School Mission</label>
                <textarea class="adm-textarea" name="mission" rows="4"
                    placeholder="Describe the school's mission and day-to-day purpose…"><?php echo $v('mission'); ?></textarea>
            </div>
        </div>
    </div>

    <button type="submit" name="save_info" class="adm-btn adm-btn-primary" style="padding:12px 32px;font-size:.9rem;">
        <i class="fas fa-save"></i> Save All Changes
    </button>
    <span class="fs-sm text-muted" style="margin-left:14px;">Changes go live on the website immediately.</span>
</form>

<?php admin_end(); ?>