<?php
// ============================================================
// includes/header.php - Site Header & Navigation
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db.php';

// Fetch school name for nav
$school_name = 'Al-Ashraq M.V';
$res = mysqli_query($conn, "SELECT school_name FROM school_info LIMIT 1");
if ($res && $row = mysqli_fetch_assoc($res)) {
    $school_name = $row['school_name'];
}

// Determine current page for active nav
$current_page = basename($_SERVER['PHP_SELF']);

// Build base path dynamically (works from subdirectories)
$depth = substr_count(str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_FILENAME']), '/') - 1;
$base = str_repeat('../', $depth - 1);
// Simplify: use absolute paths from root
$root = '/school/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' | ' . $school_name : $school_name; ?></title>
    <link rel="stylesheet" href="<?php echo $root; ?>style.css?v=4">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Nunito:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<!-- Top Bar -->
<div class="top-bar">
    <div class="container">
        <div class="top-bar-left">
            <span><i class="fas fa-phone"></i> +94 76 333 0090</span>
            <span><i class="fas fa-envelope"></i> info@alashraq.edu</span>
        </div>
        <div class="top-bar-right">
            <?php if (isset($_SESSION['user_id'])): ?>
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <?php
                    $dash_link = $root . 'login.php';
                    if ($_SESSION['user_role'] === 'admin') $dash_link = $root . 'admin/dashboard.php';
                    elseif ($_SESSION['user_role'] === 'student') $dash_link = $root . 'dashboard.php';
                    elseif ($_SESSION['user_role'] === 'lecturer') $dash_link = $root . 'lecturer/dashboard.php';
                ?>
                <a href="<?php echo $dash_link; ?>">Dashboard</a>
                <a href="<?php echo $root; ?>logout.php">Logout</a>
            <?php else: ?>
                <a href="<?php echo $root; ?>login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Main Navigation -->
<header class="site-header">
    <div class="container">
        <div class="logo">
            <a href="<?php echo $root; ?>index.php">
                <div class="logo-icon"><i class="fas fa-graduation-cap"></i></div>
                <div class="logo-text">
                    <span class="logo-name"><?php echo htmlspecialchars($school_name); ?></span>
                    <span class="logo-tagline">Nurturing Minds, Shaping Futures</span>
                </div>
            </a>
        </div>

        <button class="menu-toggle" id="menuToggle" aria-label="Toggle navigation">
            <span></span><span></span><span></span>
        </button>

        <nav class="main-nav" id="mainNav">
            <ul>
                <li><a href="<?php echo $root; ?>index.php" class="<?php echo $current_page==='index.php'?'active':''; ?>">Home</a></li>
                <li><a href="<?php echo $root; ?>about.php" class="<?php echo $current_page==='about.php'?'active':''; ?>">About</a></li>
                <li><a href="<?php echo $root; ?>academics.php" class="<?php echo $current_page==='academics.php'?'active':''; ?>">Academics</a></li>
                <li><a href="<?php echo $root; ?>admissions.php" class="<?php echo $current_page==='admissions.php'?'active':''; ?>">Admissions</a></li>
                <li><a href="<?php echo $root; ?>announcements.php" class="<?php echo $current_page==='announcements.php'?'active':''; ?>">Announcements</a></li>
                <li><a href="<?php echo $root; ?>gallery.php" class="<?php echo $current_page==='gallery.php'?'active':''; ?>">Gallery</a></li>
                <li><a href="<?php echo $root; ?>contact.php" class="<?php echo $current_page==='contact.php'?'active':''; ?>">Contact</a></li>
            </ul>
        </nav>
    </div>
</header>

<?php
// Display flash messages
$flash = getFlash();
if ($flash): ?>
<div class="flash-message flash-<?php echo $flash['type']; ?>" id="flashMsg">
    <div class="container">
        <i class="fas <?php echo $flash['type']==='success'?'fa-check-circle':'fa-exclamation-circle'; ?>"></i>
        <?php echo htmlspecialchars($flash['message']); ?>
        <button onclick="this.parentElement.parentElement.remove()" class="flash-close">&times;</button>
    </div>
</div>
<?php endif; ?>