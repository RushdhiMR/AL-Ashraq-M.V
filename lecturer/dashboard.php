<?php
// ============================================================
// lecturer/dashboard.php — Lecturer Portal Dashboard
// ============================================================
$portal_role = 'lecturer';
$page_title  = 'Lecturer Dashboard';
$active_nav  = 'dashboard';
require_once '../_layout.php';

$lecturer = $_portal_user;
$total_students   = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM students WHERE status='approved'"))['c'];
$pending_students = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM students WHERE status='pending'"))['c'];
$total_announcements = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM announcements"))['c'];
$total_classes    = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT class_grade) c FROM students"))['c'];
$recent_announcements = mysqli_fetch_all(mysqli_query($conn, "SELECT * FROM announcements ORDER BY created_at DESC LIMIT 5"), MYSQLI_ASSOC);

// Get lecturer's upcoming classes (next 3 days)
$lecturer_name_safe = sanitize($conn, $lecturer['full_name']);
$upcoming_classes = mysqli_fetch_all(mysqli_query($conn, "
    SELECT * FROM timetable_entries 
    WHERE teacher='$lecturer_name_safe' 
    AND day IN ('" . implode("','", array_slice(['Monday','Tuesday','Wednesday','Thursday','Friday'], 0, 3)) . "')
    ORDER BY FIELD(day,'Monday','Tuesday','Wednesday','Thursday','Friday'), period_index 
    LIMIT 5
"), MYSQLI_ASSOC);

portal_head();
portal_sidebar($lecturer);
portal_topbar();
?>

<div class="welcome-hero">
    <div>
        <h2>Welcome back, <?php echo htmlspecialchars(explode(' ', $lecturer['full_name'])[0]); ?>! 👋</h2>
        <p>Al-Ashraq M.V Lecturer Portal</p>
        <div class="wh-chip"><i class="fas fa-clock"></i> <?php echo date('l, F j, Y – g:i A'); ?></div>
    </div>
    <div class="wh-actions">
        <a href="../profile.php" class="prt-btn prt-btn-accent prt-btn-sm"><i class="fas fa-user-edit"></i> Edit Profile</a>
        <a href="../students.php" class="prt-btn prt-btn-ghost prt-btn-sm"><i class="fas fa-users"></i> My Students</a>
    </div>
</div>

<div class="kpi-row">
    <div class="kpi-box green">
        <div class="kpi-icon-box green"><i class="fas fa-user-graduate"></i></div>
        <div>
            <div class="kpi-val"><?php echo $total_students; ?></div>
            <div class="kpi-lbl">Approved Students</div>
        </div>
    </div>
    <div class="kpi-box gold">
        <div class="kpi-icon-box gold"><i class="fas fa-hourglass-half"></i></div>
        <div>
            <div class="kpi-val"><?php echo $pending_students; ?></div>
            <div class="kpi-lbl">Pending Students</div>
        </div>
    </div>
    <div class="kpi-box blue">
        <div class="kpi-icon-box blue"><i class="fas fa-chalkboard-teacher"></i></div>
        <div>
            <div class="kpi-val"><?php echo $total_classes; ?></div>
            <div class="kpi-lbl">Classes Covered</div>
        </div>
    </div>
    <div class="kpi-box teal">
        <div class="kpi-icon-box teal"><i class="fas fa-bullhorn"></i></div>
        <div>
            <div class="kpi-val"><?php echo $total_announcements; ?></div>
            <div class="kpi-lbl">Announcements</div>
        </div>
    </div>
</div>

<div class="prt-panel">
    <div class="prt-panel-head">
        <div class="prt-panel-title"><i class="fas fa-bullhorn"></i> Latest Announcements</div>
        <a href="announcements.php" class="prt-btn prt-btn-ghost prt-btn-sm">View All</a>
    </div>
    <div class="prt-panel-body">
        <?php if ($recent_announcements): ?>
        <div class="ann-list">
            <?php foreach ($recent_announcements as $a): ?>
            <div class="ann-item">
                <div class="ann-item-title"><?php echo htmlspecialchars($a['title']); ?></div>
                <div class="ann-item-body"><?php echo htmlspecialchars(substr($a['content'], 0, 140)) . (strlen($a['content']) > 140 ? '…' : ''); ?></div>
                <div class="ann-item-meta">
                    <span><i class="fas fa-user"></i><?php echo htmlspecialchars($a['posted_by']); ?></span>
                    <span><i class="fas fa-calendar-alt"></i><?php echo date('d M Y', strtotime($a['created_at'])); ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-info-circle"></i>
            <h3>No Announcements Yet</h3>
            <p>There are no announcements available right now.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="prt-panel">
    <div class="prt-panel-head">
        <div class="prt-panel-title"><i class="fas fa-calendar-alt"></i> Upcoming Classes</div>
        <a href="../timetable.php" class="prt-btn prt-btn-ghost prt-btn-sm">View Full Timetable</a>
    </div>
    <div class="prt-panel-body">
        <?php if ($upcoming_classes): ?>
        <div class="ann-list">
            <?php foreach ($upcoming_classes as $class): ?>
            <div class="ann-item">
                <div class="ann-item-title"><?php echo htmlspecialchars($class['subject']); ?> — <?php echo htmlspecialchars($class['grade']); ?></div>
                <div class="ann-item-body"><?php echo htmlspecialchars($class['day']); ?> at <?php echo htmlspecialchars($class['period_label']); ?> in <?php echo htmlspecialchars($class['room'] ?: 'TBA'); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-calendar-times"></i>
            <h3>No Upcoming Classes</h3>
            <p>Your teaching schedule has not been set up yet.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php portal_end(); ?>