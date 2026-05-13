<?php
// ============================================================
// admin/manage_students.php  –  Students Management
// ============================================================
$page_title  = 'Manage Students';
$active_nav  = 'students';
$breadcrumbs = [['label'=>'Students']];
require_once '../_layout.php';

// ── Handle Actions ───────────────────────────────────────
if (isset($_GET['action'], $_GET['id'])) {
    $sid = (int)$_GET['id'];
    switch ($_GET['action']) {
        case 'approve': mysqli_query($conn,"UPDATE students SET status='approved' WHERE id=$sid"); setFlash('success','Student account approved successfully.'); break;
        case 'reject':  mysqli_query($conn,"UPDATE students SET status='rejected' WHERE id=$sid"); setFlash('success','Student account rejected.');              break;
        case 'pending': mysqli_query($conn,"UPDATE students SET status='pending'  WHERE id=$sid"); setFlash('info','Student status reset to pending.');          break;
        case 'delete':  mysqli_query($conn,"DELETE FROM students WHERE id=$sid");                  setFlash('success','Student deleted permanently.');           break;
    }
    redirect('manage_students.php' . (isset($_GET['filter'])?"?filter={$_GET['filter']}":'' ));
}

// ── Filters + Search ─────────────────────────────────────
$filter = isset($_GET['filter']) ? sanitize($conn, $_GET['filter']) : 'all';
$search = isset($_GET['search']) ? sanitize($conn, $_GET['search']) : '';

$where = "WHERE 1=1";
if ($filter === 'pending')  $where .= " AND status='pending'";
if ($filter === 'approved') $where .= " AND status='approved'";
if ($filter === 'rejected') $where .= " AND status='rejected'";
if ($search) $where .= " AND (full_name LIKE '%$search%' OR email LIKE '%$search%' OR class_grade LIKE '%$search%' OR phone LIKE '%$search%')";

$students = mysqli_fetch_all(mysqli_query($conn,"SELECT * FROM students $where ORDER BY created_at DESC"), MYSQLI_ASSOC);

$cnt_all      = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM students"))['c'];
$cnt_pending  = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM students WHERE status='pending'"))['c'];
$cnt_approved = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM students WHERE status='approved'"))['c'];
$cnt_rejected = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM students WHERE status='rejected'"))['c'];

admin_head(); admin_sidebar(); admin_topbar();
?>

<!-- KPI strip -->
<div class="kpi-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:20px;">
    <div class="kpi-card green"><div class="kpi-icon green"><i class="fas fa-users"></i></div><div><div class="kpi-value"><?php echo $cnt_all; ?></div><div class="kpi-label">Total Students</div></div></div>
    <div class="kpi-card gold"><div class="kpi-icon gold"><i class="fas fa-hourglass-half"></i></div><div><div class="kpi-value"><?php echo $cnt_pending; ?></div><div class="kpi-label">Pending</div></div></div>
    <div class="kpi-card blue"><div class="kpi-icon blue"><i class="fas fa-check-circle"></i></div><div><div class="kpi-value"><?php echo $cnt_approved; ?></div><div class="kpi-label">Approved</div></div></div>
    <div class="kpi-card red"><div class="kpi-icon red"><i class="fas fa-times-circle"></i></div><div><div class="kpi-value"><?php echo $cnt_rejected; ?></div><div class="kpi-label">Rejected</div></div></div>
</div>

<div class="panel">
    <div class="panel-header">
        <div class="panel-title"><i class="fas fa-user-graduate"></i> Student Accounts</div>
        <!-- Filter tabs -->
        <div class="filter-tabs">
            <a href="manage_students.php?filter=all"      class="filter-tab <?php echo $filter==='all'     ?'active':''; ?>">All <span class="tab-count"><?php echo $cnt_all; ?></span></a>
            <a href="manage_students.php?filter=pending"  class="filter-tab <?php echo $filter==='pending' ?'active':''; ?>">Pending <span class="tab-count"><?php echo $cnt_pending; ?></span></a>
            <a href="manage_students.php?filter=approved" class="filter-tab <?php echo $filter==='approved'?'active':''; ?>">Approved <span class="tab-count"><?php echo $cnt_approved; ?></span></a>
            <a href="manage_students.php?filter=rejected" class="filter-tab <?php echo $filter==='rejected'?'active':''; ?>">Rejected <span class="tab-count"><?php echo $cnt_rejected; ?></span></a>
        </div>
    </div>

    <!-- Search bar -->
    <div class="panel-body" style="padding-bottom:0;">
        <form method="GET" class="search-bar" style="margin-bottom:16px;">
            <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
            <i class="fas fa-search" style="color:var(--gray);"></i>
            <input class="adm-input" type="text" name="search" placeholder="Search by name, email, grade, phone…" value="<?php echo htmlspecialchars($search); ?>" style="max-width:340px;">
            <button type="submit" class="adm-btn adm-btn-primary adm-btn-sm">Search</button>
            <?php if ($search): ?><a href="manage_students.php?filter=<?php echo $filter; ?>" class="adm-btn adm-btn-ghost adm-btn-sm"><i class="fas fa-times"></i> Clear</a><?php endif; ?>
        </form>

        <p class="fs-sm text-muted" style="margin-bottom:12px;">
            Showing <strong><?php echo count($students); ?></strong> student<?php echo count($students)!==1?'s':''; ?>
            <?php if ($search): ?> matching "<strong><?php echo htmlspecialchars($search); ?></strong>"<?php endif; ?>
        </p>
    </div>

    <!-- Table -->
    <?php if ($students): ?>
    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student</th>
                    <th>Phone</th>
                    <th>Gender</th>
                    <th>Grade</th>
                    <th>DOB</th>
                    <th>Registered</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($students as $i => $s): ?>
            <tr>
                <td class="text-muted"><?php echo $i+1; ?></td>
                <td>
                    <div class="cell-name"><?php echo htmlspecialchars($s['full_name']); ?></div>
                    <div class="cell-sub"><?php echo htmlspecialchars($s['email']); ?></div>
                </td>
                <td class="cell-nowrap"><?php echo htmlspecialchars($s['phone'] ?: '—'); ?></td>
                <td><?php echo htmlspecialchars($s['gender']); ?></td>
                <td><?php echo htmlspecialchars($s['class_grade']); ?></td>
                <td class="cell-nowrap"><?php echo $s['dob'] ? date('d M Y', strtotime($s['dob'])) : '—'; ?></td>
                <td class="cell-nowrap"><?php echo date('d M Y', strtotime($s['created_at'])); ?></td>
                <td><span class="badge badge-<?php echo $s['status']; ?>"><?php echo ucfirst($s['status']); ?></span></td>
                <td>
                    <div class="btn-group">
                        <?php if ($s['status'] !== 'approved'): ?>
                        <a href="manage_students.php?action=approve&id=<?php echo $s['id']; ?>&filter=<?php echo $filter; ?>"
                           class="adm-btn adm-btn-success adm-btn-sm adm-btn-icon" title="Approve"
                           data-confirm="Approve this student account?"><i class="fas fa-check"></i></a>
                        <?php endif; ?>
                        <?php if ($s['status'] !== 'rejected'): ?>
                        <a href="manage_students.php?action=reject&id=<?php echo $s['id']; ?>&filter=<?php echo $filter; ?>"
                           class="adm-btn adm-btn-warning adm-btn-sm adm-btn-icon" title="Reject"
                           data-confirm="Reject this student?"><i class="fas fa-ban"></i></a>
                        <?php endif; ?>
                        <?php if ($s['status'] !== 'pending'): ?>
                        <a href="manage_students.php?action=pending&id=<?php echo $s['id']; ?>&filter=<?php echo $filter; ?>"
                           class="adm-btn adm-btn-info adm-btn-sm adm-btn-icon" title="Reset to Pending"><i class="fas fa-undo"></i></a>
                        <?php endif; ?>
                        <a href="manage_students.php?action=delete&id=<?php echo $s['id']; ?>&filter=<?php echo $filter; ?>"
                           class="adm-btn adm-btn-danger adm-btn-sm adm-btn-icon" title="Delete permanently"
                           data-confirm="Permanently delete <?php echo htmlspecialchars(addslashes($s['full_name'])); ?>? This cannot be undone."><i class="fas fa-trash"></i></a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <i class="fas fa-user-slash"></i>
        <h3>No Students Found</h3>
        <p><?php echo $search ? 'Try a different search term.' : 'No students match the selected filter.'; ?></p>
    </div>
    <?php endif; ?>

    <?php if ($cnt_pending > 0 && $filter === 'all'): ?>
    <div class="panel-footer" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
        <span class="fs-sm text-muted"><i class="fas fa-exclamation-circle" style="color:var(--warning);"></i> <?php echo $cnt_pending; ?> student<?php echo $cnt_pending!==1?'s need':'needs'; ?> approval</span>
        <a href="manage_students.php?filter=pending" class="adm-btn adm-btn-warning adm-btn-sm"><i class="fas fa-list"></i> View Pending</a>
    </div>
    <?php endif; ?>
</div>

<?php admin_end(); ?>