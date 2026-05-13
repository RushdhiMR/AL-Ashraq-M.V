<?php
// ============================================================
// admin/timetable.php  –  Timetable Management
// ============================================================
$page_title  = 'Manage Timetable';
$active_nav  = 'timetable';
$breadcrumbs = [['label'=>'Timetable']];
require_once '../_layout.php';

$days = ['Monday','Tuesday','Wednesday','Thursday','Friday'];
$periods = [
    1 => '7:30 – 8:10',
    2 => '8:10 – 8:50',
    3 => '8:50 – 9:30',
    4 => '9:30 – 10:10',
    5 => '10:30 – 11:10',
    6 => '11:10 – 11:50',
    7 => '11:50 – 12:30',
    8 => '13:00 – 13:40',
    9 => '13:40 – 14:20',
    10 => '14:20 – 15:00',
];

// Ensure timetable storage exists
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS timetable_entries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    grade VARCHAR(64) NOT NULL,
    day VARCHAR(16) NOT NULL,
    period_index TINYINT NOT NULL,
    period_label VARCHAR(32) NOT NULL,
    subject VARCHAR(128) NOT NULL,
    teacher VARCHAR(128) DEFAULT NULL,
    room VARCHAR(64) DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_entry (grade, day, period_index)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$grades = mysqli_fetch_all(mysqli_query($conn, "SELECT DISTINCT class_grade FROM students ORDER BY class_grade"), MYSQLI_ASSOC);
$grade_options = array_column($grades, 'class_grade');
if (empty($grade_options)) {
    $grade_options = ['Grade 10'];
}
$selected_grade = $_GET['grade'] ?? $grade_options[0];
if (!in_array($selected_grade, $grade_options, true)) {
    $selected_grade = $grade_options[0];
}

$error     = '';
$edit_item = null;
$show_form = false;

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    mysqli_query($conn, "DELETE FROM timetable_entries WHERE id=" . (int)$_GET['id']);
    setFlash('success', 'Timetable entry deleted.');
    redirect('timetable.php?grade=' . urlencode($selected_grade));
}

if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $edit_item = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM timetable_entries WHERE id=" . (int)$_GET['id'] . " LIMIT 1"));
    if (!$edit_item) redirect('timetable.php?grade=' . urlencode($selected_grade));
    $show_form = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_timetable'])) {
    $entry_id     = (int)($_POST['entry_id'] ?? 0);
    $grade        = sanitize($conn, $_POST['grade'] ?? '');
    $day          = sanitize($conn, $_POST['day'] ?? '');
    $period_index = (int)($_POST['period_index'] ?? 0);
    $period_label = sanitize($conn, $_POST['period_label'] ?? '');
    $subject      = sanitize($conn, $_POST['subject'] ?? '');
    $teacher      = sanitize($conn, $_POST['teacher'] ?? '');
    $room         = sanitize($conn, $_POST['room'] ?? '');

    if (!$grade || !$day || !$period_index || !$period_label || !$subject) {
        $error = 'Grade, day, period, and subject are required.';
        $show_form = true;
        if ($entry_id) {
            $edit_item = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM timetable_entries WHERE id=$entry_id LIMIT 1"));
        }
    } else {
        if ($entry_id > 0) {
            mysqli_query($conn, "UPDATE timetable_entries SET grade='$grade', day='$day', period_index=$period_index, period_label='$period_label', subject='$subject', teacher='$teacher', room='$room' WHERE id=$entry_id");
            setFlash('success', 'Timetable entry updated successfully.');
        } else {
            mysqli_query($conn, "INSERT INTO timetable_entries (grade,day,period_index,period_label,subject,teacher,room) VALUES ('$grade','$day',$period_index,'$period_label','$subject','$teacher','$room') ON DUPLICATE KEY UPDATE subject='$subject', teacher='$teacher', room='$room', period_label='$period_label'");
            setFlash('success', 'Timetable entry saved successfully.');
        }
        redirect('timetable.php?grade=' . urlencode($grade));
    }
}

$entries = mysqli_fetch_all(mysqli_query($conn, "SELECT * FROM timetable_entries WHERE grade='" . sanitize($conn, $selected_grade) . "' ORDER BY FIELD(day,'Monday','Tuesday','Wednesday','Thursday','Friday'), period_index"), MYSQLI_ASSOC);
$grid = [];
foreach ($entries as $entry) {
    $grid[$entry['day']][$entry['period_index']] = $entry;
}

admin_head(); admin_sidebar(); admin_topbar();
?>

<div class="panel" style="margin-bottom:20px;">
    <div class="panel-header">
        <div class="panel-title"><i class="fas fa-calendar-alt"></i> Timetable for <?php echo htmlspecialchars($selected_grade); ?></div>
        <div>
            <form method="GET" style="display:inline-flex;gap:10px;align-items:center;">
                <label style="font-weight:600;">Grade</label>
                <select name="grade" class="adm-input" onchange="this.form.submit()" style="min-width:180px;">
                    <?php foreach ($grade_options as $grade): ?>
                    <option value="<?php echo htmlspecialchars($grade); ?>" <?php echo $grade=== $selected_grade ? 'selected':''; ?>><?php echo htmlspecialchars($grade); ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
    </div>
    <div class="panel-body" style="padding:0;">
        <div style="overflow-x:auto;">
            <table class="adm-table" style="min-width:860px;">
                <thead>
                    <tr>
                        <th>Period</th>
                        <?php foreach ($days as $day): ?>
                        <th><?php echo $day; ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($periods as $index => $label): ?>
                    <tr>
                        <td class="cell-nowrap" style="font-weight:700;"><?php echo htmlspecialchars($label); ?></td>
                        <?php foreach ($days as $day): ?>
                        <?php $item = $grid[$day][$index] ?? null; ?>
                        <td style="vertical-align:top;">
                            <?php if ($item): ?>
                            <div style="display:flex;justify-content:space-between;gap:10px;align-items:flex-start;">
                                <div>
                                    <div style="font-weight:700;color:var(--text);"><?php echo htmlspecialchars($item['subject']); ?></div>
                                    <div style="font-size:.82rem;color:var(--muted);"><?php echo htmlspecialchars($item['teacher'] ?: 'Teacher not set'); ?></div>
                                    <div style="font-size:.82rem;color:var(--muted);"><?php echo htmlspecialchars($item['room'] ?: 'Room not set'); ?></div>
                                </div>
                                <div class="btn-group" style="margin-top:4px;">
                                    <a href="timetable.php?action=edit&id=<?php echo $item['id']; ?>&grade=<?php echo urlencode($selected_grade); ?>" class="adm-btn adm-btn-warning adm-btn-sm adm-btn-icon" title="Edit"><i class="fas fa-edit"></i></a>
                                    <a href="timetable.php?action=delete&id=<?php echo $item['id']; ?>&grade=<?php echo urlencode($selected_grade); ?>" class="adm-btn adm-btn-danger adm-btn-sm adm-btn-icon" title="Delete" data-confirm="Delete this timetable entry?"><i class="fas fa-trash"></i></a>
                                </div>
                            </div>
                            <?php else: ?>
                            <a href="timetable.php?action=add&grade=<?php echo urlencode($selected_grade); ?>&day=<?php echo urlencode($day); ?>&period_index=<?php echo $index; ?>" class="adm-btn adm-btn-ghost adm-btn-sm">Add</a>
                            <?php endif; ?>
                        </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="panel-footer">
        <?php if (!$entries): ?>
        <div class="empty-state"><i class="fas fa-info-circle"></i><p>No timetable entries found for this grade yet. Use the Add buttons to define your schedule.</p></div>
        <?php endif; ?>
    </div>
</div>

<?php if ($show_form || isset($_GET['action']) && $_GET['action'] === 'add'): ?>
<div class="panel" style="margin-bottom:22px;">
    <div class="panel-header">
        <div class="panel-title"><i class="fas <?php echo $edit_item ? 'fa-edit' : 'fa-plus-circle'; ?>"></i> <?php echo $edit_item ? 'Edit Timetable Entry' : 'Add Timetable Entry'; ?></div>
        <a href="timetable.php?grade=<?php echo urlencode($selected_grade); ?>" class="adm-btn adm-btn-ghost adm-btn-sm"><i class="fas fa-times"></i> Cancel</a>
    </div>
    <div class="panel-body">
        <?php if ($error): ?>
        <div class="adm-flash adm-flash-error"><i class="fas fa-exclamation-circle"></i><?php echo htmlspecialchars($error); ?><button class="close-btn" onclick="this.parentElement.remove()">&times;</button></div>
        <?php endif; ?>
        <form method="POST">
            <input type="hidden" name="entry_id" value="<?php echo $edit_item ? (int)$edit_item['id'] : 0; ?>">
            <div class="adm-form-grid" style="grid-template-columns:1fr 1fr; gap:20px;">
                <div class="adm-form-group">
                    <label class="adm-label">Grade <span class="req">*</span></label>
                    <select name="grade" class="adm-input" required>
                        <?php foreach ($grade_options as $grade): ?>
                        <option value="<?php echo htmlspecialchars($grade); ?>" <?php echo ($edit_item ? $edit_item['grade'] : $selected_grade) === $grade ? 'selected' : ''; ?>><?php echo htmlspecialchars($grade); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="adm-form-group">
                    <label class="adm-label">Day <span class="req">*</span></label>
                    <select name="day" class="adm-input" required>
                        <?php foreach ($days as $day): ?>
                        <option value="<?php echo htmlspecialchars($day); ?>" <?php echo ($edit_item ? $edit_item['day'] : ($_GET['day'] ?? 'Monday')) === $day ? 'selected' : ''; ?>><?php echo htmlspecialchars($day); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="adm-form-group">
                    <label class="adm-label">Period <span class="req">*</span></label>
                    <select name="period_index" class="adm-input" required>
                        <?php foreach ($periods as $index => $label): ?>
                        <option value="<?php echo $index; ?>" <?php echo ($edit_item ? (int)$edit_item['period_index'] : ((int)($_GET['period_index'] ?? 1))) === $index ? 'selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="adm-form-group">
                    <label class="adm-label">Period Label <span class="req">*</span></label>
                    <input class="adm-input" type="text" name="period_label" required value="<?php echo htmlspecialchars($edit_item ? $edit_item['period_label'] : ($periods[(int)($_GET['period_index'] ?? 1)] ?? '')); ?>">
                </div>
            </div>
            <div class="adm-form-group">
                <label class="adm-label">Subject <span class="req">*</span></label>
                <input class="adm-input" type="text" name="subject" required value="<?php echo htmlspecialchars($edit_item ? $edit_item['subject'] : ($_POST['subject'] ?? '')); ?>">
            </div>
            <div class="adm-form-grid" style="grid-template-columns:1fr 1fr; gap:20px;">
                <div class="adm-form-group">
                    <label class="adm-label">Teacher</label>
                    <input class="adm-input" type="text" name="teacher" value="<?php echo htmlspecialchars($edit_item ? $edit_item['teacher'] : ($_POST['teacher'] ?? '')); ?>">
                </div>
                <div class="adm-form-group">
                    <label class="adm-label">Room</label>
                    <input class="adm-input" type="text" name="room" value="<?php echo htmlspecialchars($edit_item ? $edit_item['room'] : ($_POST['room'] ?? '')); ?>">
                </div>
            </div>
            <div class="btn-group">
                <button type="submit" name="save_timetable" class="adm-btn adm-btn-primary"><i class="fas fa-save"></i> <?php echo $edit_item ? 'Update Entry' : 'Save Entry'; ?></button>
                <a href="timetable.php?grade=<?php echo urlencode($selected_grade); ?>" class="adm-btn adm-btn-ghost"><i class="fas fa-times"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php admin_end(); ?>
