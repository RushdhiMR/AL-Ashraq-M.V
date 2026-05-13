<?php
// ============================================================
// logout.php - Logout Handler
// ============================================================
if (session_status() === PHP_SESSION_NONE) session_start();

// Clear all session data
session_unset();

// Destroy the session
session_destroy();

// Clear session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect to login
header('Location: /school/login.php');
exit();
?>