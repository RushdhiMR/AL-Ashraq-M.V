<?php
// ============================================================
// admin/reset_admin_password.php
// UTILITY SCRIPT - Run once to set admin password, then DELETE
// Access: http://localhost/al-ashraq/admin/reset_admin_password.php
// ============================================================

// ⚠️  SECURITY WARNING: Delete this file after use! ⚠️

require_once '../includes/db.php';

$new_password = 'Rushdhi12'; // Change this before running if desired
    
$hash = password_hash($new_password, PASSWORD_DEFAULT);

$result = mysqli_query($conn, "UPDATE admins SET password='$hash' WHERE email='admin@alashraq.edu'");

if ($result && mysqli_affected_rows($conn) > 0) {
    echo '<div style="font-family:Arial;padding:30px;background:#d4edda;color:#155724;border-radius:8px;max-width:500px;margin:50px auto;">';
    echo '<h2>✅ Admin Password Reset Successfully</h2>';
    echo '<p><strong>Email:</strong> admin@alashraq.edu</p>';
    echo '<p><strong>Password:</strong> ' . htmlspecialchars($new_password) . '</p>';
    echo '<p><strong>Hash:</strong> <code style="font-size:12px;word-break:break-all;">' . htmlspecialchars($hash) . '</code></p>';
    echo '<hr><p style="color:#721c24;font-weight:bold;">⚠️ DELETE THIS FILE NOW: admin/reset_admin_password.php</p>';
    echo '<a href="/school/login.php" style="background:#155724;color:#fff;padding:10px 20px;text-decoration:none;border-radius:6px;display:inline-block;margin-top:10px;">Go to Login</a>';
    echo '</div>';
} else {
    // If no admin exists, insert one
    $result2 = mysqli_query($conn, "INSERT INTO admins (full_name, email, password) VALUES ('Super Admin', 'admin@alashraq.edu', '$hash')");
    if ($result2) {
        echo '<div style="font-family:Arial;padding:30px;background:#d4edda;color:#155724;border-radius:8px;max-width:500px;margin:50px auto;">';
        echo '<h2>✅ Admin Account Created</h2>';
        echo '<p><strong>Email:</strong> admin@alashraq.edu</p>';
        echo '<p><strong>Password:</strong> ' . htmlspecialchars($new_password) . '</p>';
        echo '<p style="color:#721c24;font-weight:bold;">⚠️ DELETE THIS FILE: admin/reset_admin_password.php</p>';
        echo '<a href="/school/login.php" style="background:#155724;color:#fff;padding:10px 20px;text-decoration:none;border-radius:6px;display:inline-block;margin-top:10px;">Go to Login</a>';
        echo '</div>';
    } else {
        echo '<p style="color:red;">Error: ' . mysqli_error($conn) . '</p>';
    }
}
?>