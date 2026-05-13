<?php
// ============================================================
// admin/manage_announcements.php  –  Announcements CRUD
// ============================================================
$page_title  = 'Manage Announcements';
$active_nav  = 'announcements';
$breadcrumbs = [['label'=>'Announcements']];
require_once '../_layout.php';

$error     = '';
$edit_item = null;
$show_form = isset($_GET['action']) && in_array($_GET['action'],['add','edit']);

// Delete
if (isset($_GET['action']) && $_GET['action']==='delete' && isset($_GET['id'])) {
    mysqli_query($conn,"DELETE FROM announcements WHERE id=".(int)$_GET['id']);
    setFlash('success','Announcement deleted.');
    redirect('manage_announcements.php');
}

// Load for edit
if (isset($_GET['action']) && $_GET['action']==='edit' && isset($_GET['id'])) {
    $edit_item = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM announcements WHERE id=".(int)$_GET['id']." LIMIT 1"));
    if (!$edit_item) redirect('manage_announcements.php');
}

// Save
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['save_ann'])) {
    $title   = sanitize($conn, $_POST['title']   ?? '');
    $content = sanitize($conn, $_POST['content'] ?? '');
    $ann_id  = (int)($_POST['ann_id'] ?? 0);
    if (!$title || !$content) {
        $error = 'Title and content are both required.';
        $show_form = true;
        if ($ann_id) $edit_item = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM announcements WHERE id=$ann_id LIMIT 1"));
    } else {
        if ($ann_id > 0) {
            mysqli_query($conn,"UPDATE announcements SET title='$title',content='$content' WHERE id=$ann_id");
            setFlash('success','Announcement updated successfully.');
        } else {
            $posted = sanitize($conn,$_SESSION['user_name']);
            mysqli_query($conn,"INSERT INTO announcements (title,content,posted_by) VALUES ('$title','$content','$posted')");
            setFlash('success','Announcement published successfully.');
        }
        redirect('manage_announcements.php');
    }
}

$announcements = mysqli_fetch_all(mysqli_query($conn,"SELECT * FROM announcements ORDER BY created_at DESC"), MYSQLI_ASSOC);

admin_head(); admin_sidebar(); admin_topbar();
?>

<!-- Add / Edit Form -->
<?php if ($show_form): ?>
<div class="panel" style="margin-bottom:22px;">
    <div class="panel-header">
        <div class="panel-title">
            <i class="fas <?php echo $edit_item ? 'fa-edit' : 'fa-plus-circle'; ?>"></i>
            <?php echo $edit_item ? 'Edit Announcement' : 'Publish New Announcement'; ?>
        </div>
        <a href="manage_announcements.php" class="adm-btn adm-btn-ghost adm-btn-sm"><i class="fas fa-times"></i> Cancel</a>
    </div>
    <div class="panel-body">
        <?php if ($error): ?>
        <div class="adm-flash adm-flash-error"><i class="fas fa-exclamation-circle"></i><?php echo htmlspecialchars($error); ?><button class="close-btn" onclick="this.parentElement.remove()">&times;</button></div>
        <?php endif; ?>
        <form method="POST">
            <input type="hidden" name="ann_id" value="<?php echo $edit_item ? $edit_item['id'] : 0; ?>">
            <div class="adm-form-group">
                <label class="adm-label">Announcement Title <span class="req">*</span></label>
                <input class="adm-input" type="text" name="title" required placeholder="Enter a clear, concise title…"
                    value="<?php echo htmlspecialchars($edit_item ? $edit_item['title'] : ($_POST['title']??'')); ?>">
            </div>
            <div class="adm-form-group">
                <label class="adm-label">Announcement Content <span class="req">*</span></label>
                <textarea class="adm-textarea" name="content" required rows="8" placeholder="Write the full announcement here…"><?php echo htmlspecialchars($edit_item ? $edit_item['content'] : ($_POST['content']??'')); ?></textarea>
            </div>
            <div class="btn-group">
                <button type="submit" name="save_ann" class="adm-btn adm-btn-primary">
                    <i class="fas <?php echo $edit_item ? 'fa-save' : 'fa-paper-plane'; ?>"></i>
                    <?php echo $edit_item ? 'Update Announcement' : 'Publish Announcement'; ?>
                </button>
                <a href="manage_announcements.php" class="adm-btn adm-btn-ghost"><i class="fas fa-times"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- List Panel -->
<div class="panel">
    <div class="panel-header">
        <div class="panel-title"><i class="fas fa-list"></i> All Announcements <span class="badge badge-info"><?php echo count($announcements); ?></span></div>
        <a href="manage_announcements.php?action=add" class="adm-btn adm-btn-primary adm-btn-sm"><i class="fas fa-plus"></i> New Announcement</a>
    </div>

    <?php if ($announcements): ?>
    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr><th>#</th><th>Title</th><th>Content Preview</th><th>Posted By</th><th>Date</th><th>Last Updated</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php foreach ($announcements as $i => $a): ?>
            <tr>
                <td class="text-muted"><?php echo $i+1; ?></td>
                <td><div class="cell-name"><?php echo htmlspecialchars($a['title']); ?></div></td>
                <td class="cell-wrap text-muted"><?php echo htmlspecialchars(substr($a['content'],0,120)); ?>…</td>
                <td><?php echo htmlspecialchars($a['posted_by']); ?></td>
                <td class="cell-nowrap"><?php echo date('d M Y', strtotime($a['created_at'])); ?></td>
                <td class="cell-nowrap"><?php echo date('d M Y', strtotime($a['updated_at'])); ?></td>
                <td>
                    <div class="btn-group">
                        <a href="manage_announcements.php?action=edit&id=<?php echo $a['id']; ?>" class="adm-btn adm-btn-warning adm-btn-sm adm-btn-icon" title="Edit"><i class="fas fa-edit"></i></a>
                        <a href="manage_announcements.php?action=delete&id=<?php echo $a['id']; ?>" class="adm-btn adm-btn-danger adm-btn-sm adm-btn-icon" title="Delete" data-confirm="Delete '<?php echo htmlspecialchars(addslashes($a['title'])); ?>'?"><i class="fas fa-trash"></i></a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <i class="fas fa-bullhorn"></i>
        <h3>No Announcements Yet</h3>
        <p>Click <strong>New Announcement</strong> to publish your first one.</p>
    </div>
    <?php endif; ?>
</div>
<?php admin_end(); ?>