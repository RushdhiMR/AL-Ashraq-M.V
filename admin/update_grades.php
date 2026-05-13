<?php
// ============================================================
// admin/update_grades.php - Update Grade 12 & 13 to Years
// ⚠️ RUN THIS ONCE, THEN DELETE THIS FILE
// ============================================================

require_once '../db.php';

// Security check - only run if accessed directly
if (basename($_SERVER['PHP_SELF']) !== 'update_grades.php') {
    die('Access denied');
}

$updates = [];
$errors = [];

// Update students table
$result1 = mysqli_query($conn, "UPDATE students SET class_grade = '1st Year A/L' WHERE class_grade = 'Grade 12'");
if ($result1) {
    $count1 = mysqli_affected_rows($conn);
    $updates[] = "✅ Updated $count1 student(s) from Grade 12 to 1st Year A/L";
} else {
    $errors[] = "❌ Error updating students Grade 12: " . mysqli_error($conn);
}

$result2 = mysqli_query($conn, "UPDATE students SET class_grade = '2nd Year A/L' WHERE class_grade = 'Grade 13'");
if ($result2) {
    $count2 = mysqli_affected_rows($conn);
    $updates[] = "✅ Updated $count2 student(s) from Grade 13 to 2nd Year A/L";
} else {
    $errors[] = "❌ Error updating students Grade 13: " . mysqli_error($conn);
}

// Update admission_inquiries table
$result3 = mysqli_query($conn, "UPDATE admission_inquiries SET applying_grade = '1st Year A/L' WHERE applying_grade = 'Grade 12'");
if ($result3) {
    $count3 = mysqli_affected_rows($conn);
    $updates[] = "✅ Updated $count3 admission inquiry(ies) from Grade 12 to 1st Year A/L";
} else {
    $errors[] = "❌ Error updating admissions Grade 12: " . mysqli_error($conn);
}

$result4 = mysqli_query($conn, "UPDATE admission_inquiries SET applying_grade = '2nd Year A/L' WHERE applying_grade = 'Grade 13'");
if ($result4) {
    $count4 = mysqli_affected_rows($conn);
    $updates[] = "✅ Updated $count4 admission inquiry(ies) from Grade 13 to 2nd Year A/L";
} else {
    $errors[] = "❌ Error updating admissions Grade 13: " . mysqli_error($conn);
}

// Update timetable_entries table
$result5 = mysqli_query($conn, "UPDATE timetable_entries SET grade = '1st Year A/L' WHERE grade = 'Grade 12'");
if ($result5) {
    $count5 = mysqli_affected_rows($conn);
    $updates[] = "✅ Updated $count5 timetable entry(ies) from Grade 12 to 1st Year A/L";
} else {
    $errors[] = "❌ Error updating timetable Grade 12: " . mysqli_error($conn);
}

$result6 = mysqli_query($conn, "UPDATE timetable_entries SET grade = '2nd Year A/L' WHERE grade = 'Grade 13'");
if ($result6) {
    $count6 = mysqli_affected_rows($conn);
    $updates[] = "✅ Updated $count6 timetable entry(ies) from Grade 13 to 2nd Year A/L";
} else {
    $errors[] = "❌ Error updating timetable Grade 13: " . mysqli_error($conn);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grade Update Results</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #2D0809, #561113);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 40px rgba(0,0,0,0.2);
            padding: 40px;
            max-width: 700px;
            width: 100%;
        }
        h1 {
            color: #561113;
            margin-bottom: 10px;
            font-size: 1.8rem;
        }
        .subtitle {
            color: #6c757d;
            margin-bottom: 30px;
            font-size: 0.95rem;
        }
        .result-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .result-item {
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
            font-size: 0.95rem;
        }
        .result-item:last-child {
            border-bottom: none;
        }
        .success {
            color: #28a745;
        }
        .error {
            color: #dc3545;
            background: #fff5f5;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            font-weight: 600;
        }
        .btn {
            display: inline-block;
            background: #561113;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 20px;
            transition: all 0.3s ease;
        }
        .btn:hover {
            background: #2D0809;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(86, 17, 19, 0.3);
        }
        .summary {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📚 Grade Update Complete</h1>
        <p class="subtitle">Grade 12 & 13 have been updated to 1st Year A/L & 2nd Year A/L</p>

        <?php if (empty($errors)): ?>
            <div class="summary">
                ✅ All updates completed successfully!
            </div>
        <?php endif; ?>

        <?php if ($errors): ?>
            <h3 style="color: #dc3545; margin-bottom: 15px;">Errors:</h3>
            <?php foreach ($errors as $error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($updates): ?>
            <h3 style="color: #561113; margin-bottom: 15px;">Update Results:</h3>
            <div class="result-box">
                <?php foreach ($updates as $update): ?>
                    <div class="result-item success"><?php echo $update; ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="warning">
            ⚠️ IMPORTANT: Delete this file (admin/update_grades.php) now for security!
        </div>

        <a href="dashboard.php" class="btn">Go to Admin Dashboard</a>
    </div>
</body>
</html>
