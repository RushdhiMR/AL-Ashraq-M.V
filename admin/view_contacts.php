<?php
// ============================================================
// admin/view_contacts.php  –  Contact Messages Inbox
// ============================================================
$page_title  = 'Contact Messages';
$active_nav  = 'contacts';
$breadcrumbs = [['label'=>'Contact Messages']];
require_once '../_layout.php';

// Actions
if (isset($_GET['action'], $_GET['id'])) {
    $mid = (int)$_GET['id'];
    if ($_GET['action']==='read')     { mysqli_query($conn,"UPDATE contact_messages SET is_read=1 WHERE id=$mid"); redirect('view_contacts.php'); }
    if ($_GET['action']==='unread')   { mysqli_query($conn,"UPDATE contact_messages SET is_read=0 WHERE id=$mid"); redirect('view_contacts.php'); }
    if ($_GET['action']==='read_all') { mysqli_query($conn,"UPDATE contact_messages SET is_read=1"); setFlash('success','All messages marked as read.'); redirect('view_contacts.php'); }
    if ($_GET['action']==='delete')   { mysqli_query($conn,"DELETE FROM contact_messages WHERE id=$mid"); setFlash('success','Message deleted.'); redirect('view_contacts.php'); }
}

// Single view
$view_msg = null;
if (isset($_GET['action']) && $_GET['action']==='view' && isset($_GET['id'])) {
    $mid = (int)$_GET['id'];
    $view_msg = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM contact_messages WHERE id=$mid LIMIT 1"));
    if ($view_msg && !$view_msg['is_read']) { mysqli_query($conn,"UPDATE contact_messages SET is_read=1 WHERE id=$mid"); $view_msg['is_read']=1; }
}

$filter  = $_GET['filter'] ?? 'all';
$where   = "WHERE 1=1";
if ($filter==='unread') $where .= " AND is_read=0";
if ($filter==='read')   $where .= " AND is_read=1";

$messages  = mysqli_fetch_all(mysqli_query($conn,"SELECT * FROM contact_messages $where ORDER BY created_at DESC"), MYSQLI_ASSOC);
$cnt_total = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM contact_messages"))['c'];
$cnt_unread= (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM contact_messages WHERE is_read=0"))['c'];
$cnt_read  = $cnt_total - $cnt_unread;

admin_head(); admin_sidebar(); admin_topbar();
?>

<!-- KPI -->
<div class="kpi-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:20px;">
    <div class="kpi-card green"><div class="kpi-icon green"><i class="fas fa-envelope"></i></div><div><div class="kpi-value"><?php echo $cnt_total; ?></div><div class="kpi-label">Total Messages</div></div></div>
    <div class="kpi-card red"><div class="kpi-icon red"><i class="fas fa-envelope-open"></i></div><div><div class="kpi-value"><?php echo $cnt_unread; ?></div><div class="kpi-label">Unread</div></div></div>
    <div class="kpi-card blue"><div class="kpi-icon blue"><i class="fas fa-check-double"></i></div><div><div class="kpi-value"><?php echo $cnt_read; ?></div><div class="kpi-label">Read</div></div></div>
</div>

<?php if ($view_msg): ?>
<!-- ── Single Message View ── -->
<div class="msg-detail-card">
    <div class="msg-detail-head">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:12px;">
            <h2><?php echo htmlspecialchars($view_msg['subject'] ?: 'No Subject'); ?></h2>
            <div class="btn-group">
                <?php if ($view_msg['is_read']): ?>
                <a href="view_contacts.php?action=unread&id=<?php echo $view_msg['id']; ?>" class="adm-btn adm-btn-ghost adm-btn-sm"><i class="fas fa-envelope"></i> Mark Unread</a>
                <?php endif; ?>
                <a href="view_contacts.php?action=delete&id=<?php echo $view_msg['id']; ?>" class="adm-btn adm-btn-danger adm-btn-sm" data-confirm="Delete this message permanently?"><i class="fas fa-trash"></i> Delete</a>
                <a href="view_contacts.php?filter=<?php echo $filter; ?>" class="adm-btn adm-btn-ghost adm-btn-sm"><i class="fas fa-arrow-left"></i> Back to Inbox</a>
            </div>
        </div>
        <div class="msg-detail-meta">
            <span><i class="fas fa-user"></i> <strong><?php echo htmlspecialchars($view_msg['full_name']); ?></strong></span>
            <span><i class="fas fa-envelope"></i> <a href="mailto:<?php echo htmlspecialchars($view_msg['email']); ?>" style="color:var(--primary);"><?php echo htmlspecialchars($view_msg['email']); ?></a></span>
            <span><i class="fas fa-calendar"></i> <?php echo date('F j, Y \a\t g:i A', strtotime($view_msg['created_at'])); ?></span>
            <span><i class="fas fa-circle" style="color:<?php echo $view_msg['is_read']?'var(--success)':'var(--danger)'; ?>;font-size:.5rem;"></i> <?php echo $view_msg['is_read']?'Read':'Unread'; ?></span>
        </div>
    </div>
    <div class="msg-detail-body"><?php echo nl2br(htmlspecialchars($view_msg['message'])); ?></div>
    <div class="msg-detail-actions">
        <a href="mailto:<?php echo htmlspecialchars($view_msg['email']); ?>?subject=Re: <?php echo rawurlencode($view_msg['subject'] ?: 'Your Message'); ?>" class="adm-btn adm-btn-primary">
            <i class="fas fa-reply"></i> Reply via Email
        </a>
        <a href="view_contacts.php?filter=<?php echo $filter; ?>" class="adm-btn adm-btn-ghost"><i class="fas fa-arrow-left"></i> Back to Inbox</a>
    </div>
</div>

<?php else: ?>
<!-- ── Inbox List ── -->
<div class="panel">
    <div class="panel-header">
        <div class="panel-title">
            <i class="fas fa-inbox"></i> Inbox
            <?php if ($cnt_unread>0): ?><span class="badge badge-new"><?php echo $cnt_unread; ?> unread</span><?php endif; ?>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
            <div class="filter-tabs">
                <a href="view_contacts.php?filter=all"    class="filter-tab <?php echo $filter==='all'?'active':''; ?>">All <span class="tab-count"><?php echo $cnt_total; ?></span></a>
                <a href="view_contacts.php?filter=unread" class="filter-tab <?php echo $filter==='unread'?'active':''; ?>">Unread <span class="tab-count"><?php echo $cnt_unread; ?></span></a>
                <a href="view_contacts.php?filter=read"   class="filter-tab <?php echo $filter==='read'?'active':''; ?>">Read <span class="tab-count"><?php echo $cnt_read; ?></span></a>
            </div>
            <?php if ($cnt_unread>0): ?><a href="view_contacts.php?action=read_all&id=0" class="adm-btn adm-btn-success adm-btn-sm"><i class="fas fa-check-double"></i> Mark All Read</a><?php endif; ?>
        </div>
    </div>
    <div class="panel-body">
        <?php if ($messages): ?>
        <div class="msg-list">
            <?php foreach ($messages as $m): ?>
            <div class="msg-item <?php echo !$m['is_read']?'unread':''; ?>" style="cursor:default;">
                <div class="msg-item-avatar"><?php echo strtoupper(substr($m['full_name'],0,1)); ?></div>
                <div class="msg-item-content">
                    <div class="msg-item-subject"><?php echo htmlspecialchars($m['subject'] ?: 'No Subject'); ?></div>
                    <div class="msg-item-preview">
                        <strong><?php echo htmlspecialchars($m['full_name']); ?></strong> &lt;<?php echo htmlspecialchars($m['email']); ?>&gt;
                        &mdash; <?php echo htmlspecialchars(substr($m['message'],0,60)); ?>…
                    </div>
                    <div class="msg-item-meta"><?php echo date('d M Y, g:i A', strtotime($m['created_at'])); ?></div>
                </div>
                <div class="msg-item-actions">
                    <a href="view_contacts.php?action=view&id=<?php echo $m['id']; ?>&filter=<?php echo $filter; ?>" class="adm-btn adm-btn-primary adm-btn-sm adm-btn-icon" title="Read message"><i class="fas fa-eye"></i></a>
                    <?php if (!$m['is_read']): ?>
                    <a href="view_contacts.php?action=read&id=<?php echo $m['id']; ?>" class="adm-btn adm-btn-success adm-btn-sm adm-btn-icon" title="Mark as read"><i class="fas fa-check"></i></a>
                    <?php endif; ?>
                    <a href="mailto:<?php echo htmlspecialchars($m['email']); ?>" class="adm-btn adm-btn-info adm-btn-sm adm-btn-icon" title="Reply"><i class="fas fa-reply"></i></a>
                    <a href="view_contacts.php?action=delete&id=<?php echo $m['id']; ?>&filter=<?php echo $filter; ?>" class="adm-btn adm-btn-danger adm-btn-sm adm-btn-icon" title="Delete" data-confirm="Delete this message?"><i class="fas fa-trash"></i></a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state"><i class="fas fa-inbox"></i><h3>No Messages Found</h3><p><?php echo $filter==='unread'?'All messages have been read.':'No messages yet.'; ?></p></div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?php admin_end(); ?>