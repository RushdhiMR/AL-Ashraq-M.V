<?php
// ============================================================
// admin/view_admissions.php  –  Admission Inquiries
// ============================================================
$page_title  = 'Admission Inquiries';
$active_nav  = 'admissions';
$breadcrumbs = [['label'=>'Admission Inquiries']];
require_once '../_layout.php';

if (isset($_GET['action'], $_GET['id'])) {
    $aid = (int)$_GET['id'];
    if ($_GET['action']==='read')     { mysqli_query($conn,"UPDATE admission_inquiries SET is_read=1 WHERE id=$aid"); redirect('view_admissions.php'); }
    if ($_GET['action']==='read_all') { mysqli_query($conn,"UPDATE admission_inquiries SET is_read=1"); setFlash('success','All inquiries marked as reviewed.'); redirect('view_admissions.php'); }
    if ($_GET['action']==='delete')   { mysqli_query($conn,"DELETE FROM admission_inquiries WHERE id=$aid"); setFlash('success','Inquiry deleted.'); redirect('view_admissions.php'); }
}

$view_inq = null;
if (isset($_GET['action']) && $_GET['action']==='view' && isset($_GET['id'])) {
    $aid = (int)$_GET['id'];
    $view_inq = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM admission_inquiries WHERE id=$aid LIMIT 1"));
    if ($view_inq && !$view_inq['is_read']) { mysqli_query($conn,"UPDATE admission_inquiries SET is_read=1 WHERE id=$aid"); $view_inq['is_read']=1; }
}

$filter    = $_GET['filter'] ?? 'all';
$where     = "WHERE 1=1";
if ($filter==='new')      $where .= " AND is_read=0";
if ($filter==='reviewed') $where .= " AND is_read=1";

$inquiries  = mysqli_fetch_all(mysqli_query($conn,"SELECT * FROM admission_inquiries $where ORDER BY created_at DESC"), MYSQLI_ASSOC);
$cnt_total  = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM admission_inquiries"))['c'];
$cnt_new    = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM admission_inquiries WHERE is_read=0"))['c'];
$cnt_done   = $cnt_total - $cnt_new;

admin_head(); admin_sidebar(); admin_topbar();
?>

<div class="kpi-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:20px;">
    <div class="kpi-card green"><div class="kpi-icon green"><i class="fas fa-file-alt"></i></div><div><div class="kpi-value"><?php echo $cnt_total; ?></div><div class="kpi-label">Total Inquiries</div></div></div>
    <div class="kpi-card gold"><div class="kpi-icon gold"><i class="fas fa-bell"></i></div><div><div class="kpi-value"><?php echo $cnt_new; ?></div><div class="kpi-label">New / Unreviewed</div></div></div>
    <div class="kpi-card blue"><div class="kpi-icon blue"><i class="fas fa-check-circle"></i></div><div><div class="kpi-value"><?php echo $cnt_done; ?></div><div class="kpi-label">Reviewed</div></div></div>
</div>

<?php if ($view_inq): ?>
<!-- ── Detail View ── -->
<div class="panel">
    <div class="panel-header">
        <div class="panel-title"><i class="fas fa-file-alt"></i> Inquiry Detail</div>
        <div class="btn-group">
            <a href="view_admissions.php?action=delete&id=<?php echo $view_inq['id']; ?>" class="adm-btn adm-btn-danger adm-btn-sm" data-confirm="Delete this inquiry permanently?"><i class="fas fa-trash"></i> Delete</a>
            <a href="view_admissions.php" class="adm-btn adm-btn-ghost adm-btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
    </div>
    <div class="panel-body">
        <dl class="info-dl">
            <dt>Student Name</dt>  <dd><strong><?php echo htmlspecialchars($view_inq['student_name']); ?></strong></dd>
            <dt>Parent / Guardian</dt><dd><?php echo htmlspecialchars($view_inq['parent_name']); ?></dd>
            <dt>Email</dt>        <dd><a href="mailto:<?php echo htmlspecialchars($view_inq['email']); ?>" style="color:var(--primary);"><?php echo htmlspecialchars($view_inq['email']); ?></a></dd>
            <dt>Phone</dt>        <dd><a href="tel:<?php echo htmlspecialchars($view_inq['phone']); ?>" style="color:var(--primary);"><?php echo htmlspecialchars($view_inq['phone']); ?></a></dd>
            <dt>Applying for</dt> <dd><span class="badge badge-info"><?php echo htmlspecialchars($view_inq['applying_grade']); ?></span></dd>
            <dt>Submitted on</dt> <dd><?php echo date('F j, Y \a\t g:i A', strtotime($view_inq['created_at'])); ?></dd>
            <dt>Status</dt>       <dd><?php echo $view_inq['is_read'] ? '<span class="badge badge-approved">Reviewed</span>' : '<span class="badge badge-new">New</span>'; ?></dd>
            <dt>Message</dt>      <dd style="white-space:pre-wrap;"><?php echo htmlspecialchars($view_inq['message'] ?: '(No additional message provided)'); ?></dd>
        </dl>
    </div>
    <div class="msg-detail-actions">
        <a href="mailto:<?php echo htmlspecialchars($view_inq['email']); ?>?subject=Re: Admission Inquiry - <?php echo rawurlencode($view_inq['student_name']); ?>" class="adm-btn adm-btn-primary"><i class="fas fa-reply"></i> Reply via Email</a>
        <a href="tel:<?php echo htmlspecialchars($view_inq['phone']); ?>" class="adm-btn adm-btn-info"><i class="fas fa-phone"></i> Call Parent</a>
        <a href="view_admissions.php" class="adm-btn adm-btn-ghost"><i class="fas fa-arrow-left"></i> Back to List</a>
    </div>
</div>

<?php else: ?>
<!-- ── Inquiries Table ── -->
<div class="panel">
    <div class="panel-header">
        <div class="panel-title"><i class="fas fa-file-alt"></i> All Inquiries <?php if($cnt_new>0): ?><span class="badge badge-new"><?php echo $cnt_new; ?> new</span><?php endif; ?></div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
            <div class="filter-tabs">
                <a href="view_admissions.php?filter=all"      class="filter-tab <?php echo $filter==='all'?'active':''; ?>">All <span class="tab-count"><?php echo $cnt_total; ?></span></a>
                <a href="view_admissions.php?filter=new"      class="filter-tab <?php echo $filter==='new'?'active':''; ?>">New <span class="tab-count"><?php echo $cnt_new; ?></span></a>
                <a href="view_admissions.php?filter=reviewed" class="filter-tab <?php echo $filter==='reviewed'?'active':''; ?>">Reviewed <span class="tab-count"><?php echo $cnt_done; ?></span></a>
            </div>
            <?php if ($cnt_new>0): ?><a href="view_admissions.php?action=read_all&id=0" class="adm-btn adm-btn-success adm-btn-sm"><i class="fas fa-check-double"></i> Mark All Reviewed</a><?php endif; ?>
        </div>
    </div>

    <?php if ($inquiries): ?>
    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr><th>#</th><th>Student</th><th>Parent / Guardian</th><th>Contact</th><th>Grade</th><th>Date</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php foreach ($inquiries as $i => $inq): ?>
            <tr style="<?php echo !$inq['is_read']?'background:#f4e6e7;font-weight:600;':''; ?>">
                <td class="text-muted"><?php echo $i+1; ?></td>
                <td><div class="cell-name"><?php echo htmlspecialchars($inq['student_name']); ?></div></td>
                <td><?php echo htmlspecialchars($inq['parent_name']); ?></td>
                <td>
                    <a href="mailto:<?php echo htmlspecialchars($inq['email']); ?>" style="color:var(--primary);display:block;" class="cell-nowrap"><?php echo htmlspecialchars($inq['email']); ?></a>
                    <a href="tel:<?php echo htmlspecialchars($inq['phone']); ?>" style="color:var(--gray);font-size:.78rem;" class="cell-nowrap"><?php echo htmlspecialchars($inq['phone']); ?></a>
                </td>
                <td><span class="badge badge-info"><?php echo htmlspecialchars($inq['applying_grade']); ?></span></td>
                <td class="cell-nowrap"><?php echo date('d M Y', strtotime($inq['created_at'])); ?></td>
                <td><?php echo $inq['is_read'] ? '<span class="badge badge-approved">Reviewed</span>' : '<span class="badge badge-new">New</span>'; ?></td>
                <td>
                    <div class="btn-group">
                        <a href="view_admissions.php?action=view&id=<?php echo $inq['id']; ?>&filter=<?php echo $filter; ?>" class="adm-btn adm-btn-primary adm-btn-sm adm-btn-icon" title="View"><i class="fas fa-eye"></i></a>
                        <?php if (!$inq['is_read']): ?><a href="view_admissions.php?action=read&id=<?php echo $inq['id']; ?>" class="adm-btn adm-btn-success adm-btn-sm adm-btn-icon" title="Mark Reviewed"><i class="fas fa-check"></i></a><?php endif; ?>
                        <a href="mailto:<?php echo htmlspecialchars($inq['email']); ?>" class="adm-btn adm-btn-info adm-btn-sm adm-btn-icon" title="Reply"><i class="fas fa-reply"></i></a>
                        <a href="view_admissions.php?action=delete&id=<?php echo $inq['id']; ?>&filter=<?php echo $filter; ?>" class="adm-btn adm-btn-danger adm-btn-sm adm-btn-icon" title="Delete" data-confirm="Delete this inquiry?"><i class="fas fa-trash"></i></a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state"><i class="fas fa-folder-open"></i><h3>No Inquiries Found</h3><p><?php echo $filter==='new'?'All inquiries have been reviewed.':'No admission inquiries submitted yet.'; ?></p></div>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php admin_end(); ?>