<?php
// ============================================================
// admin/manage_lecturers.php  –  Lecturers Management
// ============================================================
$page_title  = 'Manage Lecturers';
$active_nav  = 'lecturers';
$breadcrumbs = [['label'=>'Lecturers']];
require_once '../_layout.php';

if (isset($_GET['action'], $_GET['id'])) {
    $lid = (int)$_GET['id'];
    switch ($_GET['action']) {
        case 'approve': mysqli_query($conn,"UPDATE lecturers SET status='approved' WHERE id=$lid"); setFlash('success','Lecturer approved.'); break;
        case 'reject':  mysqli_query($conn,"UPDATE lecturers SET status='rejected' WHERE id=$lid"); setFlash('success','Lecturer rejected.');  break;
        case 'pending': mysqli_query($conn,"UPDATE lecturers SET status='pending'  WHERE id=$lid"); setFlash('info','Reset to pending.');       break;
        case 'delete':  mysqli_query($conn,"DELETE FROM lecturers WHERE id=$lid");                  setFlash('success','Lecturer deleted.');    break;
    }
    redirect('manage_lecturers.php'.(isset($_GET['filter'])?"?filter={$_GET['filter']}":'')); 
}

$filter = isset($_GET['filter']) ? sanitize($conn,$_GET['filter']) : 'all';
$search = isset($_GET['search']) ? sanitize($conn,$_GET['search']) : '';
$where  = "WHERE 1=1";
if ($filter==='pending')  $where .= " AND status='pending'";
if ($filter==='approved') $where .= " AND status='approved'";
if ($filter==='rejected') $where .= " AND status='rejected'";
if ($search) $where .= " AND (full_name LIKE '%$search%' OR email LIKE '%$search%' OR subject_specialization LIKE '%$search%')";

$lecturers    = mysqli_fetch_all(mysqli_query($conn,"SELECT * FROM lecturers $where ORDER BY created_at DESC"), MYSQLI_ASSOC);
$cnt_all      = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM lecturers"))['c'];
$cnt_pending  = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM lecturers WHERE status='pending'"))['c'];
$cnt_approved = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM lecturers WHERE status='approved'"))['c'];
$cnt_rejected = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM lecturers WHERE status='rejected'"))['c'];

admin_head(); admin_sidebar(); admin_topbar();
?>
<div class="kpi-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:20px;">
    <div class="kpi-card green"><div class="kpi-icon green"><i class="fas fa-users"></i></div><div><div class="kpi-value"><?php echo $cnt_all; ?></div><div class="kpi-label">Total</div></div></div>
    <div class="kpi-card gold"><div class="kpi-icon gold"><i class="fas fa-hourglass-half"></i></div><div><div class="kpi-value"><?php echo $cnt_pending; ?></div><div class="kpi-label">Pending</div></div></div>
    <div class="kpi-card blue"><div class="kpi-icon blue"><i class="fas fa-check-circle"></i></div><div><div class="kpi-value"><?php echo $cnt_approved; ?></div><div class="kpi-label">Approved</div></div></div>
    <div class="kpi-card red"><div class="kpi-icon red"><i class="fas fa-times-circle"></i></div><div><div class="kpi-value"><?php echo $cnt_rejected; ?></div><div class="kpi-label">Rejected</div></div></div>
</div>

<div class="panel">
    <div class="panel-header">
        <div class="panel-title"><i class="fas fa-chalkboard-teacher"></i> Lecturer Accounts</div>
        <div class="filter-tabs">
            <a href="manage_lecturers.php?filter=all"      class="filter-tab <?php echo $filter==='all'?'active':''; ?>">All <span class="tab-count"><?php echo $cnt_all; ?></span></a>
            <a href="manage_lecturers.php?filter=pending"  class="filter-tab <?php echo $filter==='pending'?'active':''; ?>">Pending <span class="tab-count"><?php echo $cnt_pending; ?></span></a>
            <a href="manage_lecturers.php?filter=approved" class="filter-tab <?php echo $filter==='approved'?'active':''; ?>">Approved <span class="tab-count"><?php echo $cnt_approved; ?></span></a>
            <a href="manage_lecturers.php?filter=rejected" class="filter-tab <?php echo $filter==='rejected'?'active':''; ?>">Rejected <span class="tab-count"><?php echo $cnt_rejected; ?></span></a>
        </div>
    </div>
    <div class="panel-body" style="padding-bottom:0;">
        <form method="GET" class="search-bar" style="margin-bottom:16px;">
            <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
            <i class="fas fa-search" style="color:var(--gray);"></i>
            <input class="adm-input" type="text" name="search" placeholder="Search name, email, subject…" value="<?php echo htmlspecialchars($search); ?>" style="max-width:320px;">
            <button type="submit" class="adm-btn adm-btn-primary adm-btn-sm">Search</button>
            <?php if($search): ?><a href="manage_lecturers.php?filter=<?php echo $filter; ?>" class="adm-btn adm-btn-ghost adm-btn-sm"><i class="fas fa-times"></i> Clear</a><?php endif; ?>
        </form>
        <p class="fs-sm text-muted" style="margin-bottom:12px;">Showing <strong><?php echo count($lecturers); ?></strong> lecturer<?php echo count($lecturers)!==1?'s':''; ?></p>
    </div>

    <?php if ($lecturers): ?>
    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead><tr><th>#</th><th>Lecturer</th><th>Phone</th><th>Subject</th><th>Qualification</th><th>Registered</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($lecturers as $i => $l): ?>
            <tr>
                <td class="text-muted"><?php echo $i+1; ?></td>
                <td><div class="cell-name"><?php echo htmlspecialchars($l['full_name']); ?></div><div class="cell-sub"><?php echo htmlspecialchars($l['email']); ?></div></td>
                <td class="cell-nowrap"><?php echo htmlspecialchars($l['phone'] ?: '—'); ?></td>
                <td><?php echo htmlspecialchars($l['subject_specialization'] ?: '—'); ?></td>
                <td><?php echo htmlspecialchars($l['qualification'] ?: '—'); ?></td>
                <td class="cell-nowrap"><?php echo date('d M Y', strtotime($l['created_at'])); ?></td>
                <td><span class="badge badge-<?php echo $l['status']; ?>"><?php echo ucfirst($l['status']); ?></span></td>
                <td>
                    <div class="btn-group">
                        <?php if ($l['status']!=='approved'): ?><a href="manage_lecturers.php?action=approve&id=<?php echo $l['id']; ?>&filter=<?php echo $filter; ?>" class="adm-btn adm-btn-success adm-btn-sm adm-btn-icon" title="Approve" data-confirm="Approve?"><i class="fas fa-check"></i></a><?php endif; ?>
                        <?php if ($l['status']!=='rejected'): ?><a href="manage_lecturers.php?action=reject&id=<?php echo $l['id']; ?>&filter=<?php echo $filter; ?>"  class="adm-btn adm-btn-warning adm-btn-sm adm-btn-icon" title="Reject"  data-confirm="Reject?"><i class="fas fa-ban"></i></a><?php endif; ?>
                        <?php if ($l['status']!=='pending'):  ?><a href="manage_lecturers.php?action=pending&id=<?php echo $l['id']; ?>&filter=<?php echo $filter; ?>" class="adm-btn adm-btn-info    adm-btn-sm adm-btn-icon" title="Reset to Pending"><i class="fas fa-undo"></i></a><?php endif; ?>
                        <a href="manage_lecturers.php?action=delete&id=<?php echo $l['id']; ?>&filter=<?php echo $filter; ?>" class="adm-btn adm-btn-danger adm-btn-sm adm-btn-icon" title="Delete" data-confirm="Delete <?php echo htmlspecialchars(addslashes($l['full_name'])); ?> permanently?"><i class="fas fa-trash"></i></a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state"><i class="fas fa-user-slash"></i><h3>No Lecturers Found</h3><p><?php echo $search?'Try a different term.':'No match for selected filter.'; ?></p></div>
    <?php endif; ?>
</div>
<?php admin_end(); ?>