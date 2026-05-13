<?php
// ============================================================
// lecturer/get_students.php — Get students by grade (AJAX)
// ============================================================

require_once '../db.php';
session_start();

// Check if lecturer is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'lecturer') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$grade = isset($_GET['grade']) ? sanitize($conn, $_GET['grade']) : '';

if (!$grade) {
    echo json_encode([]);
    exit;
}

$students = mysqli_fetch_all(
    mysqli_query($conn, "SELECT id, full_name, email FROM students WHERE class_grade='$grade' AND status='approved' ORDER BY full_name"),
    MYSQLI_ASSOC
);

header('Content-Type: application/json');
echo json_encode($students);
