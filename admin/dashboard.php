<?php
// ============================================================
// admin/dashboard.php  –  Admin Overview Dashboard
// ============================================================
$page_title  = 'Overview';
$active_nav  = 'dashboard';
$breadcrumbs = [];
require_once '../_layout.php';

$total_students    = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM students"))['c'];
$pending_students  = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM students  WHERE status='pending'"))['c'];
$approved_students = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM students  WHERE status='approved'"))['c'];
$total_lecturers   = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM lecturers"))['c'];
$pending_lecturers = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM lecturers WHERE status='pending'"))['c'];
$total_announce    = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM announcements"))['c'];
$total_gallery     = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM gallery"))['c'];
$unread_contacts   = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM contact_messages   WHERE is_read=0"))['c'];
$unread_admissions = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM admission_inquiries WHERE is_read=0"))['c'];

$recent_students  = mysqli_fetch_all(mysqli_query($conn,"SELECT * FROM students  ORDER BY created_at DESC LIMIT 6"), MYSQLI_ASSOC);
$recent_lecturers = mysqli_fetch_all(mysqli_query($conn,"SELECT * FROM lecturers ORDER BY created_at DESC LIMIT 6"), MYSQLI_ASSOC);
$recent_contacts  = mysqli_fetch_all(mysqli_query($conn,"SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5"), MYSQLI_ASSOC);
$recent_announce  = mysqli_fetch_all(mysqli_query($conn,"SELECT * FROM announcements   ORDER BY created_at DESC LIMIT 4"), MYSQLI_ASSOC);

admin_head(); admin_sidebar(); admin_topbar();
?>

<!-- Welcome Banner -->
<div class="welcome-banner">
    <div>
        <h2>Good <?php echo (date('H')<12)?'Morning':((date('H')<17)?'Afternoon':'Evening'); ?>, <?php echo htmlspecialchars(explode(' ',$_SESSION['user_name'])[0]); ?>! 👋</h2>
        <p>Here's what's happening at Al-Ashraq M.V today.</p>
        <div class="time-chip"><i class="fas fa-clock"></i> <?php echo date('l, F j, Y – g:i A'); ?></div>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <?php if ($pending_students+$pending_lecturers > 0): ?>
        <a href="manage_students.php?filter=pending" class="adm-btn adm-btn-accent">
            <i class="fas fa-user-check"></i> <?php echo $pending_students+$pending_lecturers; ?> Pending Approval<?php echo ($pending_students+$pending_lecturers)!==1?'s':''; ?>
        </a>
        <?php endif; ?>
        <a href="/school/index.php" target="_blank" class="adm-btn adm-btn-ghost" style="border-color:rgba(255,255,255,.35);color:#fff;">
            <i class="fas fa-external-link-alt"></i> View Site
        </a>
    </div>
</div>

<!-- KPI Cards -->
<div class="kpi-grid">
    <div class="kpi-card green">
        <div class="kpi-icon green"><i class="fas fa-user-graduate"></i></div>
        <div>
            <div class="kpi-value"><?php echo $total_students; ?></div>
            <div class="kpi-label">Total Students</div>
            <div class="kpi-change up"><i class="fas fa-check-circle"></i> <?php echo $approved_students; ?> approved</div>
        </div>
    </div>
    <div class="kpi-card gold">
        <div class="kpi-icon gold"><i class="fas fa-hourglass-half"></i></div>
        <div>
            <div class="kpi-value"><?php echo $pending_students + $pending_lecturers; ?></div>
            <div class="kpi-label">Pending Approvals</div>
            <div class="kpi-change warn"><i class="fas fa-exclamation-circle"></i> Needs attention</div>
        </div>
    </div>
    <div class="kpi-card blue">
        <div class="kpi-icon blue"><i class="fas fa-chalkboard-teacher"></i></div>
        <div>
            <div class="kpi-value"><?php echo $total_lecturers; ?></div>
            <div class="kpi-label">Total Lecturers</div>
            <div class="kpi-change up"><i class="fas fa-check-circle"></i> <?php echo $total_lecturers - $pending_lecturers; ?> approved</div>
        </div>
    </div>
    <div class="kpi-card purple">
        <div class="kpi-icon purple"><i class="fas fa-bullhorn"></i></div>
        <div>
            <div class="kpi-value"><?php echo $total_announce; ?></div>
            <div class="kpi-label">Announcements</div>
        </div>
    </div>
    <div class="kpi-card teal">
        <div class="kpi-icon teal"><i class="fas fa-images"></i></div>
        <div>
            <div class="kpi-value"><?php echo $total_gallery; ?></div>
            <div class="kpi-label">Gallery Images</div>
        </div>
    </div>
    <div class="kpi-card red">
        <div class="kpi-icon red"><i class="fas fa-envelope"></i></div>
        <div>
            <div class="kpi-value"><?php echo $unread_contacts + $unread_admissions; ?></div>
            <div class="kpi-label">Unread Messages</div>
            <div class="kpi-change warn"><i class="fas fa-bell"></i> Awaiting reply</div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="panel">
    <div class="panel-header">
        <div class="panel-title"><i class="fas fa-bolt"></i> Quick Actions</div>
    </div>
    <div class="panel-body">
        <div class="quick-actions">
            <a href="manage_students.php?filter=pending" class="qa-card">
                <div class="qa-icon"><i class="fas fa-user-check"></i></div>
                <div class="qa-label">Approve Students<?php if($pending_students>0): ?><br><span style="color:var(--danger);font-size:.7rem;"><?php echo $pending_students; ?> waiting</span><?php endif; ?></div>
            </a>
            <a href="manage_lecturers.php?filter=pending" class="qa-card">
                <div class="qa-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                <div class="qa-label">Approve Lecturers<?php if($pending_lecturers>0): ?><br><span style="color:var(--danger);font-size:.7rem;"><?php echo $pending_lecturers; ?> waiting</span><?php endif; ?></div>
            </a>
            <a href="manage_students.php" class="qa-card">
                <div class="qa-icon"><i class="fas fa-users"></i></div>
                <div class="qa-label">All Students</div>
            </a>
            <a href="manage_lecturers.php" class="qa-card">
                <div class="qa-icon"><i class="fas fa-user-tie"></i></div>
                <div class="qa-label">All Lecturers</div>
            </a>
            <a href="manage_announcements.php?action=add" class="qa-card">
                <div class="qa-icon"><i class="fas fa-plus-circle"></i></div>
                <div class="qa-label">New Announcement</div>
            </a>
            <a href="timetable.php" class="qa-card">
                <div class="qa-icon"><i class="fas fa-calendar-alt"></i></div>
                <div class="qa-label">Edit Timetable</div>
            </a>
            <a href="manage_gallery.php?action=add" class="qa-card">
                <div class="qa-icon"><i class="fas fa-camera"></i></div>
                <div class="qa-label">Upload Gallery Image</div>
            </a>
            <a href="school_info.php" class="qa-card">
                <div class="qa-icon"><i class="fas fa-school"></i></div>
                <div class="qa-label">Edit School Info</div>
            </a>
            <a href="view_contacts.php" class="qa-card">
                <div class="qa-icon"><i class="fas fa-envelope-open-text"></i></div>
                <div class="qa-label">Contact Messages<?php if($unread_contacts>0): ?><br><span style="color:var(--danger);font-size:.7rem;"><?php echo $unread_contacts; ?> unread</span><?php endif; ?></div>
            </a>
            <a href="view_admissions.php" class="qa-card">
                <div class="qa-icon"><i class="fas fa-file-alt"></i></div>
                <div class="qa-label">Admissions<?php if($unread_admissions>0): ?><br><span style="color:var(--danger);font-size:.7rem;"><?php echo $unread_admissions; ?> new</span><?php endif; ?></div>
            </a>
            <a href="admin_profile.php" class="qa-card">
                <div class="qa-icon"><i class="fas fa-user-cog"></i></div>
                <div class="qa-label">My Account</div>
            </a>
        </div>
    </div>
</div>

<!-- Recent Students + Lecturers -->
<div class="col-2" style="margin-bottom:22px;">
    <div class="panel" style="margin-bottom:0;">
        <div class="panel-header">
            <div class="panel-title"><i class="fas fa-user-graduate"></i> Recent Student Registrations</div>
            <a href="manage_students.php" class="adm-btn adm-btn-ghost adm-btn-sm">View All</a>
        </div>
        <?php if ($recent_students): ?>
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead><tr><th>Student</th><th>Grade</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                <?php foreach ($recent_students as $s): ?>
                <tr>
                    <td><div class="cell-name"><?php echo htmlspecialchars($s['full_name']); ?></div><div class="cell-sub"><?php echo htmlspecialchars($s['email']); ?></div></td>
                    <td><?php echo htmlspecialchars($s['class_grade']); ?></td>
                    <td><span class="badge badge-<?php echo $s['status']; ?>"><?php echo ucfirst($s['status']); ?></span></td>
                    <td>
                        <?php if ($s['status']==='pending'): ?>
                        <div class="btn-group">
                            <a href="manage_students.php?action=approve&id=<?php echo $s['id']; ?>" class="adm-btn adm-btn-success adm-btn-sm adm-btn-icon" title="Approve" data-confirm="Approve this student?"><i class="fas fa-check"></i></a>
                            <a href="manage_students.php?action=reject&id=<?php echo $s['id']; ?>"  class="adm-btn adm-btn-danger  adm-btn-sm adm-btn-icon" title="Reject"  data-confirm="Reject?"><i class="fas fa-times"></i></a>
                        </div>
                        <?php else: ?>
                        <a href="manage_students.php" class="adm-btn adm-btn-ghost adm-btn-sm adm-btn-icon"><i class="fas fa-eye"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?><div class="empty-state"><i class="fas fa-user-graduate"></i><p>No students yet.</p></div><?php endif; ?>
    </div>

    <div class="panel" style="margin-bottom:0;">
        <div class="panel-header">
            <div class="panel-title"><i class="fas fa-chalkboard-teacher"></i> Recent Lecturer Registrations</div>
            <a href="manage_lecturers.php" class="adm-btn adm-btn-ghost adm-btn-sm">View All</a>
        </div>
        <?php if ($recent_lecturers): ?>
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead><tr><th>Lecturer</th><th>Subject</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                <?php foreach ($recent_lecturers as $l): ?>
                <tr>
                    <td><div class="cell-name"><?php echo htmlspecialchars($l['full_name']); ?></div><div class="cell-sub"><?php echo htmlspecialchars($l['email']); ?></div></td>
                    <td><?php echo htmlspecialchars($l['subject_specialization'] ?? '—'); ?></td>
                    <td><span class="badge badge-<?php echo $l['status']; ?>"><?php echo ucfirst($l['status']); ?></span></td>
                    <td>
                        <?php if ($l['status']==='pending'): ?>
                        <div class="btn-group">
                            <a href="manage_lecturers.php?action=approve&id=<?php echo $l['id']; ?>" class="adm-btn adm-btn-success adm-btn-sm adm-btn-icon" title="Approve" data-confirm="Approve?"><i class="fas fa-check"></i></a>
                            <a href="manage_lecturers.php?action=reject&id=<?php echo $l['id']; ?>"  class="adm-btn adm-btn-danger  adm-btn-sm adm-btn-icon" title="Reject"  data-confirm="Reject?"><i class="fas fa-times"></i></a>
                        </div>
                        <?php else: ?>
                        <a href="manage_lecturers.php" class="adm-btn adm-btn-ghost adm-btn-sm adm-btn-icon"><i class="fas fa-eye"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?><div class="empty-state"><i class="fas fa-chalkboard-teacher"></i><p>No lecturers yet.</p></div><?php endif; ?>
    </div>
</div>

<!-- Recent Messages + Announcements -->
<div class="col-2">
    <div class="panel" style="margin-bottom:0;">
        <div class="panel-header">
            <div class="panel-title"><i class="fas fa-envelope"></i> Recent Messages <?php if($unread_contacts>0): ?><span class="badge badge-new"><?php echo $unread_contacts; ?></span><?php endif; ?></div>
            <a href="view_contacts.php" class="adm-btn adm-btn-ghost adm-btn-sm">View All</a>
        </div>
        <?php if ($recent_contacts): ?>
        <div class="msg-list" style="padding:10px 16px;">
            <?php foreach ($recent_contacts as $m): ?>
            <a href="view_contacts.php?action=view&id=<?php echo $m['id']; ?>" class="msg-item <?php echo !$m['is_read']?'unread':''; ?>">
                <div class="msg-item-avatar"><?php echo strtoupper(substr($m['full_name'],0,1)); ?></div>
                <div class="msg-item-content">
                    <div class="msg-item-subject"><?php echo htmlspecialchars($m['subject'] ?: 'No Subject'); ?></div>
                    <div class="msg-item-preview"><?php echo htmlspecialchars($m['full_name']); ?> — <?php echo htmlspecialchars(substr($m['message'],0,45)); ?>…</div>
                    <div class="msg-item-meta"><?php echo date('d M Y, g:i A', strtotime($m['created_at'])); ?></div>
                </div>
                <?php if (!$m['is_read']): ?><span class="badge badge-new" style="flex-shrink:0;">New</span><?php endif; ?>
            </a>
            <?php endforeach; ?>
        </div>
        <?php else: ?><div class="empty-state"><i class="fas fa-inbox"></i><p>No messages yet.</p></div><?php endif; ?>
    </div>

    <div class="panel" style="margin-bottom:0;">
        <div class="panel-header">
            <div class="panel-title"><i class="fas fa-bullhorn"></i> Latest Announcements</div>
            <a href="manage_announcements.php?action=add" class="adm-btn adm-btn-primary adm-btn-sm"><i class="fas fa-plus"></i> Add New</a>
        </div>
        <?php if ($recent_announce): ?>
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead><tr><th>Title</th><th>Date</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($recent_announce as $a): ?>
                <tr>
                    <td><div class="cell-name"><?php echo htmlspecialchars($a['title']); ?></div><div class="cell-sub"><?php echo htmlspecialchars(substr($a['content'],0,55)); ?>…</div></td>
                    <td class="cell-nowrap"><?php echo date('d M Y', strtotime($a['created_at'])); ?></td>
                    <td>
                        <div class="btn-group">
                            <a href="manage_announcements.php?action=edit&id=<?php echo $a['id']; ?>" class="adm-btn adm-btn-warning adm-btn-sm adm-btn-icon"><i class="fas fa-edit"></i></a>
                            <a href="manage_announcements.php?action=delete&id=<?php echo $a['id']; ?>" class="adm-btn adm-btn-danger adm-btn-sm adm-btn-icon" data-confirm="Delete?"><i class="fas fa-trash"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="panel-footer"><a href="manage_announcements.php" class="adm-btn adm-btn-ghost adm-btn-sm"><i class="fas fa-list"></i> Manage All</a></div>
        <?php else: ?><div class="empty-state"><i class="fas fa-bullhorn"></i><p>No announcements yet.</p></div><?php endif; ?>
    </div>
</div>

<?php admin_end(); ?>