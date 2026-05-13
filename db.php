<?php
// ============================================================
// includes/db.php - Database Connection
// ============================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'al_ashraq_school');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die('<div style="font-family:sans-serif;padding:30px;color:#c00;">
        <h2>Database Connection Failed</h2>
        <p>' . mysqli_connect_error() . '</p>
        <p>Please check your XAMPP MySQL server is running and the database exists.</p>
    </div>');
}

mysqli_set_charset($conn, 'utf8mb4');

// Helper: Sanitize input
function sanitize($conn, $data) {
    return mysqli_real_escape_string($conn, trim(strip_tags($data)));
}

// Helper: Redirect
function redirect($url) {
    header("Location: $url");
    exit();
}

// Helper: Flash messages via session
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
?>