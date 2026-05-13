<?php
// ============================================================
// lecturer/timetable.php — Class Timetable
// ============================================================
$portal_role = 'lecturer';
$page_title  = 'My Timetable';
$active_nav  = 'timetable';
require_once '../_layout.php';

$user = $_portal_user;
$lecturer_name = $user['full_name'] ?? '';
$days    = ['Monday','Tuesday','Wednesday','Thursday','Friday'];
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

$lecturer_safe = sanitize($conn, $lecturer_name);
$result = mysqli_query($conn, "SELECT * FROM timetable_entries WHERE teacher='$lecturer_safe' ORDER BY FIELD(day,'Monday','Tuesday','Wednesday','Thursday','Friday'), period_index");
$entries = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
$grid = [];
foreach ($entries as $entry) {
    $grid[$entry['day']][$entry['period_index']] = $entry;
}
$has_timetable = !empty($entries);

portal_head();
portal_sidebar($user);
portal_topbar();
?>

<div class="prt-panel" style="margin-bottom:16px;">
    <div class="prt-panel-head">
        <div class="prt-panel-title"><i class="fas fa-calendar-alt"></i> My Teaching Schedule</div>
        <span class="prt-badge prt-badge-info">Academic Year 2024/25</span>
    </div>
    <div class="prt-panel-body" style="padding:10px 4px;">
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;min-width:680px;font-size:.78rem;">
                <thead>
                    <tr>
                        <th style="background:var(--off-white);padding:10px 12px;border:1px solid var(--border);text-align:left;font-size:.7rem;text-transform:uppercase;letter-spacing:.4px;color:var(--gray);min-width:110px;">Period / Time</th>
                        <?php foreach ($days as $day): ?>
                        <th style="background:var(--role-color);color:#fff;padding:10px 12px;border:1px solid var(--border);text-align:center;"><?php echo $day; ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($periods as $period_index => $period_label): ?>
                    <tr>
                        <td style="padding:9px 12px;border:1px solid var(--border);font-weight:700;font-size:.72rem;color:var(--gray);background:var(--off-white);white-space:nowrap;">
                            <?php echo $period_label; ?>
                        </td>
                        <?php foreach ($days as $day): ?>
                        <?php $cell = $grid[$day][$period_index] ?? null; ?>
                        <td style="padding:8px 10px;border:1px solid var(--border);text-align:left;vertical-align:top;min-width:140px;">
                            <?php if ($cell): ?>
                            <div style="font-weight:700;color:var(--text);margin-bottom:4px"><?php echo htmlspecialchars($cell['subject']); ?></div>
                            <div style="font-size:.82rem;color:var(--muted);">Grade: <?php echo htmlspecialchars($cell['grade']); ?></div>
                            <div style="font-size:.82rem;color:var(--muted);">Room: <?php echo htmlspecialchars($cell['room'] ?: 'TBA'); ?></div>
                            <?php else: ?>
                            <span style="color:var(--gray);font-size:.83rem;">No class</span>
                            <?php endif; ?>
                        </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if (!$has_timetable): ?>
    <div class="prt-panel-foot" style="font-size:.75rem;color:var(--gray);">
        <i class="fas fa-info-circle" style="color:var(--role-color);"></i>
        No classes have been assigned to you yet. Please contact an administrator to update your teaching schedule.
    </div>
    <?php endif; ?>
</div>

<?php portal_end(); ?>
