<?php
// ============================================================
// _layout.php — Shared Portal Layout for Student & Lecturer
// Place this file in both student/ and lecturer/ directories
// OR reference from the correct relative path.
//
// Usage:
//   $portal_role  = 'student';          // or 'lecturer'
//   $page_title   = 'Dashboard';
//   $active_nav   = 'dashboard';
//   require_once '_layout.php';
//   portal_head();
//   portal_sidebar($user);
//   portal_topbar();
//   // ... page content ...
//   portal_end();
// ============================================================

if (session_status() === PHP_SESSION_NONE) session_start();

$is_admin_area   = basename(dirname($_SERVER['SCRIPT_FILENAME'])) === 'admin';
$current_script = basename($_SERVER['SCRIPT_FILENAME']);

require_once __DIR__ . '/includes/db.php';

if ($is_admin_area) {
    if ($current_script !== 'login.php' && (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin')) {
        header('Location: /school/admin/login.php'); exit();
    }

    function admin_get_user($conn, $id) {
        $id = (int)$id;
        $res = mysqli_query($conn, "SELECT * FROM admins WHERE id=$id LIMIT 1");
        return $res ? mysqli_fetch_assoc($res) : [];
    }

    $_admin_user = null;
    if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'admin') {
        $_admin_user = admin_get_user($conn, $_SESSION['user_id']);
    }
    if ($_admin_user) {
        $_SESSION['user_name'] = $_admin_user['full_name'];
    }
} else {
    // Auth guard — role set by the including file
    $_required_role = $portal_role ?? 'student';
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== $_required_role) {
        header('Location: /school/login.php'); exit();
    }

    // Fetch user row fresh every load
    function portal_get_user($conn, $role, $id) {
        $id   = (int)$id;
        $tbl  = ($role === 'student') ? 'students' : 'lecturers';
        $res  = mysqli_query($conn, "SELECT * FROM $tbl WHERE id=$id LIMIT 1");
        return $res ? mysqli_fetch_assoc($res) : [];
    }

    $_portal_user = portal_get_user($conn, $_required_role, $_SESSION['user_id']);
    if ($_portal_user) $_SESSION['user_name'] = $_portal_user['full_name'];
}

// ─── Layout functions ───────────────────────────────────────

function admin_head() {
    global $page_title, $current_script;
    $title = isset($page_title) ? "$page_title | Admin Dashboard – Al-Ashraq M.V" : "Admin Dashboard – Al-Ashraq M.V";
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Nunito:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/school/admin.css?v=4">
</head>
<body>
<?php if ($current_script !== 'login.php'): ?>
<div class="admin-shell">
<?php endif; ?>
    <?php
}

function admin_sidebar() {
    global $active_nav;
    $act  = $active_nav ?? 'dashboard';
    $name = $_SESSION['user_name'] ?? 'Admin';
    $init = strtoupper(substr($name, 0, 1));
    ?>
    <aside class="adm-sidebar">
        <div class="adm-logo">
            <div class="adm-logo-icon"><i class="fas fa-graduation-cap"></i></div>
            <div class="adm-logo-text">
                <span class="adm-logo-name">Al-Ashraq M.V</span>
                <span class="adm-logo-sub">Admin Panel</span>
            </div>
        </div>
        <div class="adm-profile">
            <div class="adm-avatar"><?php echo htmlspecialchars($init); ?></div>
            <div class="adm-profile-info">
                <span class="adm-profile-name"><?php echo htmlspecialchars($name); ?></span>
                <span class="adm-profile-role">Administrator</span>
            </div>
        </div>
        <nav class="adm-nav">
            <div class="adm-nav-group">Main</div>
            <a href="dashboard.php" class="<?php echo $act==='dashboard'?'active':''; ?>"><i class="fas fa-home nav-icon"></i> Dashboard</a>
            <a href="manage_students.php" class="<?php echo $act==='students'?'active':''; ?>"><i class="fas fa-users nav-icon"></i> Students</a>
            <a href="manage_lecturers.php" class="<?php echo $act==='lecturers'?'active':''; ?>"><i class="fas fa-chalkboard-teacher nav-icon"></i> Lecturers</a>
            <a href="manage_announcements.php" class="<?php echo $act==='announcements'?'active':''; ?>"><i class="fas fa-bullhorn nav-icon"></i> Announcements</a>
            <a href="timetable.php" class="<?php echo $act==='timetable'?'active':''; ?>"><i class="fas fa-calendar-alt nav-icon"></i> Timetable</a>
            <a href="manage_gallery.php" class="<?php echo $act==='gallery'?'active':''; ?>"><i class="fas fa-images nav-icon"></i> Gallery</a>
            <a href="view_contacts.php" class="<?php echo $act==='contacts'?'active':''; ?>"><i class="fas fa-envelope nav-icon"></i> Contacts</a>
            <a href="view_admissions.php" class="<?php echo $act==='admissions'?'active':''; ?>"><i class="fas fa-file-alt nav-icon"></i> Admissions</a>
            <a href="school_info.php" class="<?php echo $act==='school'?'active':''; ?>"><i class="fas fa-school nav-icon"></i> School Info</a>
            <a href="admin_profile.php" class="<?php echo $act==='profile'?'active':''; ?>"><i class="fas fa-user-cog nav-icon"></i> Profile</a>
        </nav>
        <div class="adm-sidebar-footer">
            <a href="/school/index.php" target="_blank"><i class="fas fa-external-link-alt"></i> Visit Site</a>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </aside>
    <?php
}

function admin_topbar() {
    global $page_title, $breadcrumbs;
    ?>
    <div class="adm-main">
        <div class="adm-topbar">
            <div class="adm-topbar-title">
                <h1><?php echo htmlspecialchars($page_title ?? 'Dashboard'); ?></h1>
                <?php if (!empty($breadcrumbs) && is_array($breadcrumbs)): ?>
                    <div class="breadcrumb">
                        <?php foreach ($breadcrumbs as $i => $crumb): ?>
                            <?php if ($i > 0): ?><span>&raquo;</span><?php endif; ?>
                            <?php if (!empty($crumb['url'])): ?>
                                <a href="<?php echo htmlspecialchars($crumb['url']); ?>"><?php echo htmlspecialchars($crumb['label']); ?></a>
                            <?php else: ?>
                                <span><?php echo htmlspecialchars($crumb['label']); ?></span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="adm-topbar-actions">
                <a href="dashboard.php" class="notif-btn" title="Dashboard"><i class="fas fa-home"></i></a>
                <a href="../index.php" target="_blank" class="notif-btn" title="View Website"><i class="fas fa-external-link-alt"></i></a>
                <a href="../logout.php" class="notif-btn" title="Logout"><i class="fas fa-sign-out-alt"></i></a>
            </div>
        </div>
        <div class="adm-content">
    <?php
}

function admin_end() {
    global $current_script;
    ?>
    <?php if ($current_script !== 'login.php'): ?>
        </div><!-- adm-content -->
    </div><!-- adm-main -->
</div><!-- admin-shell -->
    <?php endif; ?>
</body>
</html>
    <?php
}

function portal_head() {
    global $page_title, $portal_role;
    $role  = ucfirst($portal_role ?? 'Student');
    $title = isset($page_title) ? "$page_title | $role Portal – Al-Ashraq M.V" : "Al-Ashraq M.V $role Portal";
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Nunito:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/school/includes/portal.css?v=4">
</head>
<body class="<?php echo htmlspecialchars($portal_role ?? 'student'); ?>">
<div class="portal-shell">
    <?php
}

function portal_sidebar($user) {
    global $portal_role, $active_nav;
    $role   = $portal_role ?? 'student';
    $act    = $active_nav  ?? 'dashboard';
    $name   = $user['full_name'] ?? 'User';
    $init   = strtoupper(substr($name, 0, 1));
    $page_prefix = ($role === 'lecturer') ? '/school/lecturer' : '/school';
    $root_prefix = '/school';
    $icon   = ($role === 'student') ? 'fa-user-graduate'  : 'fa-chalkboard-teacher';
    $label  = ($role === 'student') ? 'Student Portal'    : 'Lecturer Portal';
    ?>
    <aside class="prt-sidebar" id="prtSidebar">
        <!-- Logo -->
        <div class="prt-logo">
            <div class="prt-logo-icon"><i class="fas fa-graduation-cap"></i></div>
            <div>
                <span class="prt-logo-name">Al-Ashraq M.V</span>
                <span class="prt-logo-sub"><?php echo $label; ?></span>
            </div>
        </div>

        <!-- Profile strip -->
        <div class="prt-profile">
            <div class="prt-avatar"><?php echo $init; ?></div>
            <div>
                <span class="prt-profile-name"><?php echo htmlspecialchars($name); ?></span>
                <span class="prt-profile-role"><?php echo ucfirst($role); ?></span>
            </div>
        </div>

        <!-- Nav -->
        <nav class="prt-nav">
            <div class="prt-nav-group">Main</div>
            <a href="<?php echo $page_prefix; ?>/dashboard.php" class="<?php echo $act==='dashboard'?'active':''; ?>">
                <i class="fas fa-home ni"></i> Dashboard
            </a>
            <a href="<?php echo $page_prefix; ?>/profile.php" class="<?php echo $act==='profile'?'active':''; ?>">
                <i class="fas fa-user ni"></i> My Profile
            </a>

            <?php if ($role === 'student'): ?>
            <div class="prt-nav-group">Academic</div>
            <a href="<?php echo $page_prefix; ?>/student_announcements.php" class="<?php echo $act==='announcements'?'active':''; ?>">
                <i class="fas fa-bullhorn ni"></i> Announcements
            </a>
            <a href="<?php echo $page_prefix; ?>/timetable.php" class="<?php echo $act==='timetable'?'active':''; ?>">
                <i class="fas fa-calendar-alt ni"></i> Timetable
            </a>
            <a href="<?php echo $page_prefix; ?>/results.php" class="<?php echo $act==='results'?'active':''; ?>">
                <i class="fas fa-poll ni"></i> My Results
            </a>

            <?php else: // lecturer ?>
            <div class="prt-nav-group">Academic</div>
            <a href="<?php echo $page_prefix; ?>/announcements.php" class="<?php echo $act==='announcements'?'active':''; ?>">
                <i class="fas fa-bullhorn ni"></i> Announcements
            </a>
            <a href="<?php echo $page_prefix; ?>/students.php" class="<?php echo $act==='students'?'active':''; ?>">
                <i class="fas fa-users ni"></i> My Students
            </a>
            <a href="<?php echo $page_prefix; ?>/timetable.php" class="<?php echo $act==='timetable'?'active':''; ?>">
                <i class="fas fa-calendar-alt ni"></i> My Timetable
            </a>
            <a href="<?php echo $page_prefix; ?>/results.php" class="<?php echo $act==='results'?'active':''; ?>">
                <i class="fas fa-clipboard-list ni"></i> Manage Results
            </a>
            <?php endif; ?>

            <div class="prt-nav-group">Account</div>
            <a href="<?php echo $page_prefix; ?>/settings.php" class="<?php echo $act==='settings'?'active':''; ?>">
                <i class="fas fa-cog ni"></i> Account Settings
            </a>
        </nav>

        <!-- Footer links -->
        <div class="prt-sidebar-foot">
            <a href="/school/index.php" target="_blank">
                <i class="fas fa-external-link-alt"></i> School Website
            </a>
            <a href="/school/logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </aside>
    <?php
}

function portal_topbar() {
    global $page_title, $portal_role;
    $role = $portal_role ?? 'student';
    ?>
    <div class="prt-main">
        <div class="prt-topbar">
            <button class="prt-toggle" id="prtToggle" onclick="document.getElementById('prtSidebar').classList.toggle('open')">
                <i class="fas fa-bars"></i>
            </button>
            <div class="prt-topbar-title">
                <h1><?php echo htmlspecialchars($page_title ?? 'Dashboard'); ?></h1>
                <div class="prt-date"><?php echo date('l, F j, Y'); ?></div>
            </div>
            <div class="prt-topbar-actions">
                <?php if ($role === 'student'): ?>
                <a href="/school/profile.php" class="prt-btn prt-btn-ghost prt-btn-sm"><i class="fas fa-user"></i> Profile</a>
                <?php else: ?>
                <a href="/school/profile.php" class="prt-btn prt-btn-ghost prt-btn-sm"><i class="fas fa-user"></i> Profile</a>
                <?php endif; ?>
                <a href="/school/logout.php" class="prt-btn prt-btn-danger prt-btn-sm"><i class="fas fa-sign-out-alt"></i></a>
            </div>
        </div>

        <!-- Flash messages -->
        <?php
        $flash = getFlash();
        if ($flash): ?>
        <div class="prt-flash prt-flash-<?php echo $flash['type']; ?>" id="prtFlash">
            <i class="fas <?php echo $flash['type']==='success'?'fa-check-circle':'fa-exclamation-circle'; ?>"></i>
            <?php echo htmlspecialchars($flash['message']); ?>
            <button class="fclose" onclick="this.parentElement.remove()">&times;</button>
        </div>
        <?php endif; ?>

        <div class="prt-content">
    <?php
}

function portal_end() {
    ?>
        </div><!-- /prt-content -->
    </div><!-- /prt-main -->
</div><!-- /portal-shell -->

<!-- Mobile sidebar overlay -->
<div id="prtOverlay" onclick="document.getElementById('prtSidebar').classList.remove('open');this.style.display='none';"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:150;"></div>

<script>
// Sidebar mobile toggle
const prtToggle  = document.getElementById('prtToggle');
const prtSidebar = document.getElementById('prtSidebar');
const prtOverlay = document.getElementById('prtOverlay');
if (prtToggle) {
    prtToggle.addEventListener('click', () => {
        const open = prtSidebar.classList.contains('open');
        prtSidebar.classList.toggle('open');
        prtOverlay.style.display = open ? 'none' : 'block';
    });
}

// Auto-dismiss flash
const fl = document.getElementById('prtFlash');
if (fl) setTimeout(() => { fl.style.opacity='0'; fl.style.transition='opacity .5s'; setTimeout(()=>fl.remove(),500); }, 4000);

// Confirm dialogs
document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', e => { if (!confirm(el.dataset.confirm)) e.preventDefault(); });
});

// Password visibility toggle
document.querySelectorAll('.pass-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
        const inp = btn.previousElementSibling;
        if (!inp) return;
        inp.type = inp.type === 'password' ? 'text' : 'password';
        btn.querySelector('i').className = inp.type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
    });
});

// Password strength meter
const newPassInp = document.getElementById('newPassword');
const strengthBar = document.getElementById('strengthBar');
if (newPassInp && strengthBar) {
    newPassInp.addEventListener('input', () => {
        const v = newPassInp.value;
        let score = 0;
        if (v.length >= 6)  score++;
        if (v.length >= 10) score++;
        if (/[A-Z]/.test(v) && /[0-9]/.test(v)) score++;
        strengthBar.className = 'pass-strength ' + ['','weak','medium','strong'][score] || '';
    });
}

// Password match check
const confirmPassInp = document.getElementById('confirmPassword');
const matchMsg       = document.getElementById('matchMsg');
if (newPassInp && confirmPassInp && matchMsg) {
    function checkMatch() {
        if (!confirmPassInp.value) { matchMsg.textContent=''; confirmPassInp.setCustomValidity(''); return; }
        if (newPassInp.value === confirmPassInp.value) {
            matchMsg.textContent='✓ Passwords match'; matchMsg.className='pass-match ok';
            confirmPassInp.setCustomValidity('');
        } else {
            matchMsg.textContent='✗ Passwords do not match'; matchMsg.className='pass-match bad';
            confirmPassInp.setCustomValidity('no match');
        }
    }
    newPassInp.addEventListener('input', checkMatch);
    confirmPassInp.addEventListener('input', checkMatch);
}
</script>
</body>
</html>
    <?php
}
?>