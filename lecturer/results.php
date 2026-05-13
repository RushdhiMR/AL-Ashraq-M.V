<?php
// ============================================================
// lecturer/results.php — Manage Student Exam Results
// ============================================================
$portal_role = 'lecturer';
$page_title  = 'Manage Results';
$active_nav  = 'results';
require_once '../_layout.php';

$lecturer = $_portal_user;

// Create table if not exists
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS exam_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    student_name VARCHAR(255) NOT NULL,
    student_email VARCHAR(255) NOT NULL,
    class_grade VARCHAR(50) NOT NULL,
    exam_name VARCHAR(255) NOT NULL,
    exam_date DATE,
    subject VARCHAR(255) NOT NULL,
    marks_obtained DECIMAL(6,2) NOT NULL,
    marks_total DECIMAL(6,2) NOT NULL DEFAULT 100,
    grade VARCHAR(10),
    remarks TEXT,
    added_by VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_student (student_id),
    INDEX idx_exam (exam_name),
    INDEX idx_grade (class_grade),
    INDEX idx_subject (subject)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// ── Handle Actions ───────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'add_result') {
        $student_id = (int)$_POST['student_id'];
        $exam_name = sanitize($conn, $_POST['exam_name']);
        $exam_date = sanitize($conn, $_POST['exam_date']);
        $subject = sanitize($conn, $_POST['subject']);
        $marks_obtained = (float)$_POST['marks_obtained'];
        $marks_total = (float)$_POST['marks_total'];
        $grade = sanitize($conn, $_POST['grade']);
        $remarks = sanitize($conn, $_POST['remarks']);
        
        // Get student info
        $student = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM students WHERE id=$student_id LIMIT 1"));
        
        if ($student) {
            $student_name = sanitize($conn, $student['full_name']);
            $student_email = sanitize($conn, $student['email']);
            $class_grade = sanitize($conn, $student['class_grade']);
            $added_by = sanitize($conn, $lecturer['full_name']);
            
            $sql = "INSERT INTO exam_results (student_id, student_name, student_email, class_grade, exam_name, exam_date, subject, marks_obtained, marks_total, grade, remarks, added_by) 
                    VALUES ($student_id, '$student_name', '$student_email', '$class_grade', '$exam_name', '$exam_date', '$subject', $marks_obtained, $marks_total, '$grade', '$remarks', '$added_by')";
            
            if (mysqli_query($conn, $sql)) {
                setFlash('success', 'Exam result added successfully!');
            } else {
                setFlash('error', 'Error adding result: ' . mysqli_error($conn));
            }
        } else {
            setFlash('error', 'Student not found!');
        }
        redirect('results.php');
    }
    
    if ($action === 'delete_result' && isset($_POST['result_id'])) {
        $result_id = (int)$_POST['result_id'];
        mysqli_query($conn, "DELETE FROM exam_results WHERE id=$result_id");
        setFlash('success', 'Result deleted successfully!');
        redirect('results.php');
    }
    
    if ($action === 'bulk_add') {
        $exam_name = sanitize($conn, $_POST['exam_name']);
        $exam_date = sanitize($conn, $_POST['exam_date']);
        $subject = sanitize($conn, $_POST['subject']);
        $marks_total = (float)$_POST['marks_total'];
        $class_grade = sanitize($conn, $_POST['class_grade']);
        $added_by = sanitize($conn, $lecturer['full_name']);
        
        $students = mysqli_fetch_all(mysqli_query($conn, "SELECT * FROM students WHERE class_grade='$class_grade' AND status='approved' ORDER BY full_name"), MYSQLI_ASSOC);
        
        $added_count = 0;
        foreach ($students as $student) {
            $student_id = $student['id'];
            $marks_key = 'marks_' . $student_id;
            $grade_key = 'grade_' . $student_id;
            
            if (isset($_POST[$marks_key]) && $_POST[$marks_key] !== '') {
                $marks_obtained = (float)$_POST[$marks_key];
                $grade = sanitize($conn, $_POST[$grade_key]);
                $student_name = sanitize($conn, $student['full_name']);
                $student_email = sanitize($conn, $student['email']);
                
                $sql = "INSERT INTO exam_results (student_id, student_name, student_email, class_grade, exam_name, exam_date, subject, marks_obtained, marks_total, grade, added_by) 
                        VALUES ($student_id, '$student_name', '$student_email', '$class_grade', '$exam_name', '$exam_date', '$subject', $marks_obtained, $marks_total, '$grade', '$added_by')";
                
                if (mysqli_query($conn, $sql)) {
                    $added_count++;
                }
            }
        }
        
        setFlash('success', "Successfully added results for $added_count student(s)!");
        redirect('results.php');
    }
}

// ── Filters ─────────────────────────────────────────
$filter_exam = isset($_GET['exam']) ? sanitize($conn, $_GET['exam']) : '';
$filter_grade = isset($_GET['grade']) ? sanitize($conn, $_GET['grade']) : '';
$filter_subject = isset($_GET['subject']) ? sanitize($conn, $_GET['subject']) : '';

$where = "WHERE 1=1";
if ($filter_exam) $where .= " AND exam_name='$filter_exam'";
if ($filter_grade) $where .= " AND class_grade='$filter_grade'";
if ($filter_subject) $where .= " AND subject='$filter_subject'";

$results = mysqli_fetch_all(mysqli_query($conn, "SELECT * FROM exam_results $where ORDER BY exam_date DESC, class_grade, student_name"), MYSQLI_ASSOC);

// Get unique values for filters
$exams = mysqli_fetch_all(mysqli_query($conn, "SELECT DISTINCT exam_name FROM exam_results ORDER BY exam_name DESC"), MYSQLI_ASSOC);
$grades = mysqli_fetch_all(mysqli_query($conn, "SELECT DISTINCT class_grade FROM students WHERE status='approved' ORDER BY class_grade"), MYSQLI_ASSOC);
$subjects = mysqli_fetch_all(mysqli_query($conn, "SELECT DISTINCT subject FROM exam_results ORDER BY subject"), MYSQLI_ASSOC);

$show_add_form = isset($_GET['action']) && $_GET['action'] === 'add';
$show_bulk_form = isset($_GET['action']) && $_GET['action'] === 'bulk';

portal_head();
portal_sidebar($lecturer);
portal_topbar();
?>

<!-- KPI Cards -->
<div class="kpi-row" style="margin-bottom:20px;">
    <div class="kpi-box green">
        <div class="kpi-icon-box green"><i class="fas fa-clipboard-list"></i></div>
        <div>
            <div class="kpi-val"><?php echo count($results); ?></div>
            <div class="kpi-lbl">Total Results</div>
        </div>
    </div>
    <div class="kpi-box blue">
        <div class="kpi-icon-box blue"><i class="fas fa-graduation-cap"></i></div>
        <div>
            <div class="kpi-val"><?php echo count(array_unique(array_column($results, 'student_id'))); ?></div>
            <div class="kpi-lbl">Students</div>
        </div>
    </div>
    <div class="kpi-box gold">
        <div class="kpi-icon-box gold"><i class="fas fa-book"></i></div>
        <div>
            <div class="kpi-val"><?php echo count($subjects); ?></div>
            <div class="kpi-lbl">Subjects</div>
        </div>
    </div>
    <div class="kpi-box teal">
        <div class="kpi-icon-box teal"><i class="fas fa-file-alt"></i></div>
        <div>
            <div class="kpi-val"><?php echo count($exams); ?></div>
            <div class="kpi-lbl">Exams</div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="btn-row" style="margin-bottom:20px;">
    <a href="results.php?action=add" class="prt-btn prt-btn-primary"><i class="fas fa-plus-circle"></i> Add Single Result</a>
    <a href="results.php?action=bulk" class="prt-btn prt-btn-accent"><i class="fas fa-users"></i> Bulk Add Results</a>
    <?php if ($filter_exam || $filter_grade || $filter_subject): ?>
    <a href="results.php" class="prt-btn prt-btn-ghost"><i class="fas fa-times"></i> Clear Filters</a>
    <?php endif; ?>
</div>

<?php if ($show_add_form): ?>
<!-- Add Single Result Form -->
<div class="prt-panel" style="margin-bottom:20px;">
    <div class="prt-panel-head">
        <div class="prt-panel-title"><i class="fas fa-plus-circle"></i> Add Exam Result</div>
        <a href="results.php" class="prt-btn prt-btn-ghost prt-btn-sm"><i class="fas fa-times"></i> Cancel</a>
    </div>
    <div class="prt-panel-body">
        <form method="POST">
            <input type="hidden" name="action" value="add_result">
            
            <div class="prt-form-row">
                <div class="prt-form-group">
                    <label class="prt-label">Student <span class="req">*</span></label>
                    <select name="student_id" class="prt-select" required>
                        <option value="">-- Select Student --</option>
                        <?php
                        $students = mysqli_fetch_all(mysqli_query($conn, "SELECT * FROM students WHERE status='approved' ORDER BY class_grade, full_name"), MYSQLI_ASSOC);
                        foreach ($students as $s):
                        ?>
                        <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['full_name']); ?> (<?php echo htmlspecialchars($s['class_grade']); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="prt-form-group">
                    <label class="prt-label">Exam Name <span class="req">*</span></label>
                    <input type="text" name="exam_name" class="prt-input" placeholder="e.g., Term 1 - 2024" required>
                </div>
            </div>
            
            <div class="prt-form-row">
                <div class="prt-form-group">
                    <label class="prt-label">Exam Date <span class="req">*</span></label>
                    <input type="date" name="exam_date" class="prt-input" required>
                </div>
                
                <div class="prt-form-group">
                    <label class="prt-label">Subject <span class="req">*</span></label>
                    <input type="text" name="subject" class="prt-input" placeholder="e.g., Mathematics" required>
                </div>
            </div>
            
            <div class="prt-form-row">
                <div class="prt-form-group">
                    <label class="prt-label">Marks Obtained <span class="req">*</span></label>
                    <input type="number" name="marks_obtained" class="prt-input" step="0.01" min="0" placeholder="e.g., 85" required>
                </div>
                
                <div class="prt-form-group">
                    <label class="prt-label">Total Marks <span class="req">*</span></label>
                    <input type="number" name="marks_total" class="prt-input" step="0.01" min="0" value="100" required>
                </div>
            </div>
            
            <div class="prt-form-row">
                <div class="prt-form-group">
                    <label class="prt-label">Grade <span class="req">*</span></label>
                    <select name="grade" class="prt-select" required>
                        <option value="">-- Select Grade --</option>
                        <option value="A+">A+</option>
                        <option value="A">A</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B">B</option>
                        <option value="B-">B-</option>
                        <option value="C+">C+</option>
                        <option value="C">C</option>
                        <option value="C-">C-</option>
                        <option value="D">D</option>
                        <option value="F">F</option>
                    </select>
                </div>
                
                <div class="prt-form-group">
                    <label class="prt-label">Remarks (Optional)</label>
                    <input type="text" name="remarks" class="prt-input" placeholder="e.g., Excellent performance">
                </div>
            </div>
            
            <div class="btn-row" style="margin-top:20px;">
                <button type="submit" class="prt-btn prt-btn-primary"><i class="fas fa-save"></i> Save Result</button>
                <a href="results.php" class="prt-btn prt-btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php if ($show_bulk_form): ?>
<!-- Bulk Add Results Form -->
<div class="prt-panel" style="margin-bottom:20px;">
    <div class="prt-panel-head">
        <div class="prt-panel-title"><i class="fas fa-users"></i> Bulk Add Results for Class</div>
        <a href="results.php" class="prt-btn prt-btn-ghost prt-btn-sm"><i class="fas fa-times"></i> Cancel</a>
    </div>
    <div class="prt-panel-body">
        <form method="POST" id="bulkForm">
            <input type="hidden" name="action" value="bulk_add">
            
            <div class="prt-form-row">
                <div class="prt-form-group">
                    <label class="prt-label">Class / Grade <span class="req">*</span></label>
                    <select name="class_grade" class="prt-select" required onchange="loadStudents(this.value)">
                        <option value="">-- Select Grade --</option>
                        <?php foreach ($grades as $g): ?>
                        <option value="<?php echo htmlspecialchars($g['class_grade']); ?>"><?php echo htmlspecialchars($g['class_grade']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="prt-form-group">
                    <label class="prt-label">Exam Name <span class="req">*</span></label>
                    <input type="text" name="exam_name" class="prt-input" placeholder="e.g., Term 1 - 2024" required>
                </div>
            </div>
            
            <div class="prt-form-row">
                <div class="prt-form-group">
                    <label class="prt-label">Exam Date <span class="req">*</span></label>
                    <input type="date" name="exam_date" class="prt-input" required>
                </div>
                
                <div class="prt-form-group">
                    <label class="prt-label">Subject <span class="req">*</span></label>
                    <input type="text" name="subject" class="prt-input" placeholder="e.g., Mathematics" required>
                </div>
            </div>
            
            <div class="prt-form-group">
                <label class="prt-label">Total Marks <span class="req">*</span></label>
                <input type="number" name="marks_total" class="prt-input" step="0.01" min="0" value="100" required style="max-width:200px;">
            </div>
            
            <div class="divider"></div>
            
            <div id="studentsList">
                <p class="text-muted" style="text-align:center;padding:20px;">
                    <i class="fas fa-arrow-up"></i> Select a grade to load students
                </p>
            </div>
            
            <div class="btn-row" style="margin-top:20px;" id="submitBtn" style="display:none;">
                <button type="submit" class="prt-btn prt-btn-primary"><i class="fas fa-save"></i> Save All Results</button>
                <a href="results.php" class="prt-btn prt-btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
function loadStudents(grade) {
    if (!grade) {
        document.getElementById('studentsList').innerHTML = '<p class="text-muted" style="text-align:center;padding:20px;"><i class="fas fa-arrow-up"></i> Select a grade to load students</p>';
        document.getElementById('submitBtn').style.display = 'none';
        return;
    }
    
    fetch('get_students.php?grade=' + encodeURIComponent(grade))
        .then(r => r.json())
        .then(students => {
            if (students.length === 0) {
                document.getElementById('studentsList').innerHTML = '<p class="text-muted" style="text-align:center;padding:20px;"><i class="fas fa-user-slash"></i> No approved students found in this grade</p>';
                document.getElementById('submitBtn').style.display = 'none';
                return;
            }
            
            let html = '<h4 style="margin-bottom:16px;color:var(--role-color);">Enter Marks for Each Student:</h4>';
            html += '<div style="max-height:400px;overflow-y:auto;border:1px solid var(--border);border-radius:8px;padding:16px;">';
            
            students.forEach((s, i) => {
                html += `
                <div style="display:grid;grid-template-columns:2fr 1fr 1fr;gap:12px;align-items:center;padding:12px;border-bottom:1px solid var(--border);${i%2===0?'background:var(--off-white);':''}">
                    <div>
                        <div style="font-weight:700;font-size:.88rem;">${s.full_name}</div>
                        <div style="font-size:.75rem;color:var(--gray);">${s.email}</div>
                    </div>
                    <div>
                        <input type="number" name="marks_${s.id}" class="prt-input" placeholder="Marks" step="0.01" min="0" style="font-size:.85rem;">
                    </div>
                    <div>
                        <select name="grade_${s.id}" class="prt-select" style="font-size:.85rem;">
                            <option value="">Grade</option>
                            <option value="A+">A+</option>
                            <option value="A">A</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B">B</option>
                            <option value="B-">B-</option>
                            <option value="C+">C+</option>
                            <option value="C">C</option>
                            <option value="C-">C-</option>
                            <option value="D">D</option>
                            <option value="F">F</option>
                        </select>
                    </div>
                </div>`;
            });
            
            html += '</div>';
            document.getElementById('studentsList').innerHTML = html;
            document.getElementById('submitBtn').style.display = 'flex';
        });
}
</script>
<?php endif; ?>

<!-- Filters -->
<div class="prt-panel" style="margin-bottom:20px;">
    <div class="prt-panel-head">
        <div class="prt-panel-title"><i class="fas fa-filter"></i> Filter Results</div>
    </div>
    <div class="prt-panel-body">
        <form method="GET" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;align-items:end;">
            <div class="prt-form-group" style="margin-bottom:0;">
                <label class="prt-label">Exam</label>
                <select name="exam" class="prt-select">
                    <option value="">All Exams</option>
                    <?php foreach ($exams as $e): ?>
                    <option value="<?php echo htmlspecialchars($e['exam_name']); ?>" <?php echo $filter_exam===$e['exam_name']?'selected':''; ?>><?php echo htmlspecialchars($e['exam_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="prt-form-group" style="margin-bottom:0;">
                <label class="prt-label">Grade</label>
                <select name="grade" class="prt-select">
                    <option value="">All Grades</option>
                    <?php foreach ($grades as $g): ?>
                    <option value="<?php echo htmlspecialchars($g['class_grade']); ?>" <?php echo $filter_grade===$g['class_grade']?'selected':''; ?>><?php echo htmlspecialchars($g['class_grade']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="prt-form-group" style="margin-bottom:0;">
                <label class="prt-label">Subject</label>
                <select name="subject" class="prt-select">
                    <option value="">All Subjects</option>
                    <?php foreach ($subjects as $sub): ?>
                    <option value="<?php echo htmlspecialchars($sub['subject']); ?>" <?php echo $filter_subject===$sub['subject']?'selected':''; ?>><?php echo htmlspecialchars($sub['subject']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit" class="prt-btn prt-btn-primary"><i class="fas fa-search"></i> Filter</button>
        </form>
    </div>
</div>

<!-- Results Table -->
<div class="prt-panel">
    <div class="prt-panel-head">
        <div class="prt-panel-title"><i class="fas fa-table"></i> Exam Results</div>
        <span class="prt-badge prt-badge-info"><?php echo count($results); ?> result(s)</span>
    </div>
    
    <?php if ($results): ?>
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:.82rem;">
            <thead>
                <tr style="background:var(--off-white);">
                    <th style="padding:11px 14px;border-bottom:2px solid var(--border);text-align:left;font-size:.7rem;text-transform:uppercase;letter-spacing:.4px;color:var(--gray);">Student</th>
                    <th style="padding:11px 14px;border-bottom:2px solid var(--border);text-align:left;font-size:.7rem;text-transform:uppercase;letter-spacing:.4px;color:var(--gray);">Grade</th>
                    <th style="padding:11px 14px;border-bottom:2px solid var(--border);text-align:left;font-size:.7rem;text-transform:uppercase;letter-spacing:.4px;color:var(--gray);">Exam</th>
                    <th style="padding:11px 14px;border-bottom:2px solid var(--border);text-align:left;font-size:.7rem;text-transform:uppercase;letter-spacing:.4px;color:var(--gray);">Subject</th>
                    <th style="padding:11px 14px;border-bottom:2px solid var(--border);text-align:center;font-size:.7rem;text-transform:uppercase;letter-spacing:.4px;color:var(--gray);">Marks</th>
                    <th style="padding:11px 14px;border-bottom:2px solid var(--border);text-align:center;font-size:.7rem;text-transform:uppercase;letter-spacing:.4px;color:var(--gray);">Grade</th>
                    <th style="padding:11px 14px;border-bottom:2px solid var(--border);text-align:center;font-size:.7rem;text-transform:uppercase;letter-spacing:.4px;color:var(--gray);">Date</th>
                    <th style="padding:11px 14px;border-bottom:2px solid var(--border);text-align:center;font-size:.7rem;text-transform:uppercase;letter-spacing:.4px;color:var(--gray);">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($results as $r): ?>
            <tr style="border-bottom:1px solid var(--border);">
                <td style="padding:12px 14px;">
                    <div style="font-weight:700;font-size:.85rem;"><?php echo htmlspecialchars($r['student_name']); ?></div>
                    <div style="font-size:.72rem;color:var(--gray);"><?php echo htmlspecialchars($r['student_email']); ?></div>
                </td>
                <td style="padding:12px 14px;font-weight:600;"><?php echo htmlspecialchars($r['class_grade']); ?></td>
                <td style="padding:12px 14px;"><?php echo htmlspecialchars($r['exam_name']); ?></td>
                <td style="padding:12px 14px;font-weight:600;"><?php echo htmlspecialchars($r['subject']); ?></td>
                <td style="padding:12px 14px;text-align:center;font-weight:700;"><?php echo $r['marks_obtained']; ?> / <?php echo $r['marks_total']; ?></td>
                <td style="padding:12px 14px;text-align:center;">
                    <span style="display:inline-block;background:var(--role-color)18;color:var(--role-color);border:1px solid var(--role-color)40;padding:3px 10px;border-radius:20px;font-weight:800;font-size:.72rem;"><?php echo htmlspecialchars($r['grade']); ?></span>
                </td>
                <td style="padding:12px 14px;text-align:center;font-size:.78rem;color:var(--gray);"><?php echo date('d M Y', strtotime($r['exam_date'])); ?></td>
                <td style="padding:12px 14px;text-align:center;">
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this result?');">
                        <input type="hidden" name="action" value="delete_result">
                        <input type="hidden" name="result_id" value="<?php echo $r['id']; ?>">
                        <button type="submit" class="prt-btn prt-btn-danger prt-btn-sm prt-btn-icon" title="Delete"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <i class="fas fa-clipboard-list"></i>
        <h3>No Results Found</h3>
        <p>Start by adding exam results for your students.</p>
    </div>
    <?php endif; ?>
</div>

<?php portal_end(); ?>
