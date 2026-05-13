<?php
// ============================================================
// dashboard.php — Student and Lecturer Dashboard
// ============================================================
$portal_role = $_SESSION['user_role'] ?? 'student';
$page_title  = ($portal_role === 'lecturer') ? 'Lecturer Dashboard' : 'Dashboard';
$active_nav  = 'dashboard';
require_once '_layout.php';

$user = $_portal_user;
$is_student  = $portal_role === 'student';
$is_lecturer = $portal_role === 'lecturer';

if ($is_student) {
    $student = $user;
    $id      = (int)$_SESSION['user_id'];

    // Latest 5 announcements
    $anns = mysqli_fetch_all(mysqli_query($conn,"SELECT * FROM announcements ORDER BY created_at DESC LIMIT 5"), MYSQLI_ASSOC);
    $ann_total = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM announcements"))['c'];

    // School info for academic details
    $school = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM school_info LIMIT 1")) ?? [];

    // Age from DOB
    $age = '';
    if (!empty($student['dob'])) {
        $age = (new DateTime($student['dob']))->diff(new DateTime())->y . ' yrs';
    }
} else {
    $lecturer = $user;
    $total_students   = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM students WHERE status='approved'"))['c'];
    $pending_students = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM students WHERE status='pending'"))['c'];
    $total_announcements = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM announcements"))['c'];
    $total_classes    = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(DISTINCT class_grade) c FROM students"))['c'];
    $recent_announcements = mysqli_fetch_all(mysqli_query($conn,"SELECT * FROM announcements ORDER BY created_at DESC LIMIT 5"), MYSQLI_ASSOC);
}

portal_head();
portal_sidebar($user);
portal_topbar();
?>

<?php if ($is_student): ?>

<!-- Welcome Hero -->
<div class="welcome-hero">
    <div>
        <h2>Welcome back, <?php echo htmlspecialchars(explode(' ',$student['full_name'])[0]); ?>! 👋</h2>
        <p><?php echo htmlspecialchars($student['class_grade']); ?> &nbsp;·&nbsp; Al-Ashraq M.V Student Portal</p>
        <div class="wh-chip"><i class="fas fa-clock"></i> <?php echo date('l, F j, Y – g:i A'); ?></div>
    </div>
    <div class="wh-actions">
        <a href="profile.php" class="prt-btn prt-btn-accent prt-btn-sm"><i class="fas fa-user-edit"></i> Edit Profile</a>
        <a href="student_announcements.php" class="prt-btn prt-btn-ghost prt-btn-sm" style="border-color:rgba(255,255,255,.35);color:#fff;"><i class="fas fa-bullhorn"></i> Announcements</a>
    </div>
</div>

<!-- KPI Row -->
<div class="kpi-row">
    <div class="kpi-box green">
        <div class="kpi-icon-box green"><i class="fas fa-layer-group"></i></div>
        <div>
            <div class="kpi-val"><?php echo htmlspecialchars($student['class_grade']); ?></div>
            <div class="kpi-lbl">My Class</div>
        </div>
    </div>
    <div class="kpi-box blue">
        <div class="kpi-icon-box blue"><i class="fas fa-shield-alt"></i></div>
        <div>
            <div class="kpi-val" style="text-transform:capitalize;font-size:1.1rem;"><?php echo htmlspecialchars($student['status']); ?></div>
            <div class="kpi-lbl">Account Status</div>
        </div>
    </div>
    <div class="kpi-box gold">
        <div class="kpi-icon-box gold"><i class="fas fa-birthday-cake"></i></div>
        <div>
            <div class="kpi-val" style="font-size:1.1rem;"><?php echo $age ?: '—'; ?></div>
            <div class="kpi-lbl">Age</div>
        </div>
    </div>
    <div class="kpi-box teal">
        <div class="kpi-icon-box teal"><i class="fas fa-bullhorn"></i></div>
        <div>
            <div class="kpi-val"><?php echo $ann_total; ?></div>
            <div class="kpi-lbl">Announcements</div>
        </div>
    </div>
</div>

<div class="col-2">

    <!-- Left: Profile summary -->
    <div>
        <div class="prt-panel">
            <div class="prt-panel-head">
                <div class="prt-panel-title"><i class="fas fa-id-card"></i> My Profile</div>
                <a href="profile.php" class="prt-btn prt-btn-ghost prt-btn-sm"><i class="fas fa-edit"></i> Edit</a>
            </div>
            <div class="prt-panel-body">
                <div class="profile-big-wrap">
                    <div class="profile-big-avatar"><?php echo strtoupper(substr($student['full_name'],0,1)); ?></div>
                    <div class="profile-big-name"><?php echo htmlspecialchars($student['full_name']); ?></div>
                    <div class="profile-big-email"><?php echo htmlspecialchars($student['email']); ?></div>
                    <div class="profile-big-meta">
                        <span class="prt-badge prt-badge-<?php echo $student['status']; ?>"><?php echo ucfirst($student['status']); ?></span>
                        <span class="prt-badge prt-badge-info"><?php echo htmlspecialchars($student['class_grade']); ?></span>
                    </div>
                </div>

                <div class="divider"></div>

                <div class="info-grid">
                    <div class="info-row">
                        <div class="lbl"><i class="fas fa-user"></i> Full Name</div>
                        <div class="val"><?php echo htmlspecialchars($student['full_name']); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="lbl"><i class="fas fa-envelope"></i> Email</div>
                        <div class="val" style="word-break:break-all;"><?php echo htmlspecialchars($student['email']); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="lbl"><i class="fas fa-phone"></i> Phone</div>
                        <div class="val"><?php echo htmlspecialchars($student['phone'] ?: '—'); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="lbl"><i class="fas fa-venus-mars"></i> Gender</div>
                        <div class="val"><?php echo htmlspecialchars($student['gender']); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="lbl"><i class="fas fa-calendar"></i> Date of Birth</div>
                        <div class="val"><?php echo $student['dob'] ? date('F j, Y', strtotime($student['dob'])) : '—'; ?></div>
                    </div>
                    <div class="info-row">
                        <div class="lbl"><i class="fas fa-layer-group"></i> Grade</div>
                        <div class="val"><?php echo htmlspecialchars($student['class_grade']); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="lbl"><i class="fas fa-map-marker-alt"></i> Address</div>
                        <div class="val"><?php echo htmlspecialchars($student['address'] ?: '—'); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="lbl"><i class="fas fa-calendar-plus"></i> Joined</div>
                        <div class="val"><?php echo date('F j, Y', strtotime($student['created_at'])); ?></div>
                    </div>
                </div>
            </div>
            <div class="prt-panel-foot">
                <a href="profile.php" class="prt-btn prt-btn-primary prt-btn-sm"><i class="fas fa-user-edit"></i> Update Profile</a>
            </div>
        </div>

        <!-- Quick links -->
        <div class="prt-panel">
            <div class="prt-panel-head">
                <div class="prt-panel-title"><i class="fas fa-bolt"></i> Quick Links</div>
            </div>
            <div class="prt-panel-body" style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <?php
                $links = [
                    ['student_announcements.php','fa-bullhorn','Announcements'],
                    ['timetable.php','fa-calendar-alt','Timetable'],
                    ['results.php','fa-poll','My Results'],
                    ['settings.php','fa-cog','Settings'],
                    ['/school/admissions.php','fa-file-alt','Admissions Info'],
                    ['/school/contact.php','fa-envelope','Contact School'],
                ];
                foreach ($links as [$url, $icon, $label]):
                    $href = strpos($url,'/')===0 ? $url : $url;
                ?>
                <a href="<?php echo $href; ?>" style="display:flex;align-items:center;gap:9px;padding:10px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:.8rem;font-weight:700;color:var(--text);transition:all .2s;" onmouseover="this.style.borderColor='var(--role-color)';this.style.color='var(--role-color)';" onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text)';">
                    <i class="fas <?php echo $icon; ?>" style="width:16px;text-align:center;color:var(--role-color);"></i> <?php echo $label; ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Right: Announcements -->
    <div>
        <div class="prt-panel">
            <div class="prt-panel-head">
                <div class="prt-panel-title"><i class="fas fa-bullhorn"></i> Latest Announcements</div>
                <a href="student_announcements.php" class="prt-btn prt-btn-ghost prt-btn-sm">View All</a>
            </div>
            <div class="prt-panel-body">
                <?php if ($anns): ?>
                <div class="ann-list">
                    <?php foreach ($anns as $a): ?>
                    <div class="ann-item">
                        <div class="ann-item-title"><?php echo htmlspecialchars($a['title']); ?></div>
                        <div class="ann-item-body"><?php echo htmlspecialchars(substr($a['content'],0,160)).(strlen($a['content'])>160?'…':''); ?></div>
                        <div class="ann-item-meta">
                            <span><i class="fas fa-user"></i><?php echo htmlspecialchars($a['posted_by']); ?></span>
                            <span><i class="fas fa-calendar-alt"></i><?php echo date('d M Y', strtotime($a['created_at'])); ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="empty-state"><i class="fas fa-bullhorn"></i><h3>No Announcements</h3><p>Check back soon for school updates.</p></div>
                <?php endif; ?>
            </div>
            <?php if ($ann_total > 5): ?>
            <div class="prt-panel-foot">
                <a href="student_announcements.php" class="prt-btn prt-btn-primary prt-btn-sm"><i class="fas fa-list"></i> View All <?php echo $ann_total; ?> Announcements</a>
            </div>
            <?php endif; ?>
        </div>

        <!-- School info snapshot -->
        <div class="prt-panel">
            <div class="prt-panel-head">
                <div class="prt-panel-title"><i class="fas fa-school"></i> School Information</div>
            </div>
            <div class="prt-panel-body">
                <div class="info-grid">
                    <div class="info-row">
                        <div class="lbl"><i class="fas fa-school"></i> School</div>
                        <div class="val"><?php echo htmlspecialchars($school['school_name'] ?? 'Al-Ashraq M.V'); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="lbl"><i class="fas fa-user-tie"></i> Principal</div>
                        <div class="val"><?php echo htmlspecialchars($school['principal_name'] ?? '—'); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="lbl"><i class="fas fa-phone"></i> Contact</div>
                        <div class="val"><?php echo htmlspecialchars($school['phone'] ?? '—'); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="lbl"><i class="fas fa-envelope"></i> Email</div>
                        <div class="val"><?php echo htmlspecialchars($school['email'] ?? '—'); ?></div>
                    </div>
                </div>
            </div>
            <div class="prt-panel-foot">
                <a href="/school/contact.php" class="prt-btn prt-btn-ghost prt-btn-sm"><i class="fas fa-envelope"></i> Contact School</a>
            </div>
        </div>
    </div>

</div>

<?php else: ?>

<!-- Lecturer Dashboard -->
<div class="welcome-hero">
    <div>
        <h2>Welcome back, <?php echo htmlspecialchars(explode(' ',$lecturer['full_name'])[0]); ?>! 👋</h2>
        <p>Al-Ashraq M.V Lecturer Portal</p>
        <div class="wh-chip"><i class="fas fa-clock"></i> <?php echo date('l, F j, Y – g:i A'); ?></div>
    </div>
    <div class="wh-actions">
        <a href="profile.php" class="prt-btn prt-btn-accent prt-btn-sm"><i class="fas fa-user-edit"></i> Edit Profile</a>
        <a href="students.php" class="prt-btn prt-btn-ghost prt-btn-sm"><i class="fas fa-users"></i> My Students</a>
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

<div class="col-2">
    <div class="prt-panel">
        <div class="prt-panel-head">
            <div class="prt-panel-title"><i class="fas fa-users"></i> Lecturer Overview</div>
        </div>
        <div class="prt-panel-body">
            <div class="info-grid">
                <div class="info-row"><div class="lbl"><i class="fas fa-user"></i> Name</div><div class="val"><?php echo htmlspecialchars($lecturer['full_name']); ?></div></div>
                <div class="info-row"><div class="lbl"><i class="fas fa-envelope"></i> Email</div><div class="val"><?php echo htmlspecialchars($lecturer['email']); ?></div></div>
                <div class="info-row"><div class="lbl"><i class="fas fa-phone"></i> Phone</div><div class="val"><?php echo htmlspecialchars($lecturer['phone'] ?: '—'); ?></div></div>
                <div class="info-row"><div class="lbl"><i class="fas fa-book-open"></i> Subject</div><div class="val"><?php echo htmlspecialchars($lecturer['subject_specialization'] ?? '—'); ?></div></div>
                <div class="info-row"><div class="lbl"><i class="fas fa-award"></i> Qualification</div><div class="val"><?php echo htmlspecialchars($lecturer['qualification'] ?? '—'); ?></div></div>
                <div class="info-row"><div class="lbl"><i class="fas fa-map-marker-alt"></i> Address</div><div class="val"><?php echo htmlspecialchars($lecturer['address'] ?: '—'); ?></div></div>
                <div class="info-row"><div class="lbl"><i class="fas fa-calendar-plus"></i> Joined</div><div class="val"><?php echo date('F j, Y', strtotime($lecturer['created_at'])); ?></div></div>
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
                    <div class="ann-item-body"><?php echo htmlspecialchars(substr($a['content'],0,140)).(strlen($a['content'])>140?'…':''); ?></div>
                    <div class="ann-item-meta">
                        <span><i class="fas fa-user"></i><?php echo htmlspecialchars($a['posted_by']); ?></span>
                        <span><i class="fas fa-calendar-alt"></i><?php echo date('d M Y', strtotime($a['created_at'])); ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state"><i class="fas fa-info-circle"></i><h3>No Announcements</h3><p>There are no school announcements yet.</p></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php endif; ?>

<?php portal_end(); ?>