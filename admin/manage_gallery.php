<?php
// ============================================================
// admin/manage_gallery.php  –  Gallery CRUD + File Upload
// ============================================================
$page_title  = 'Manage Gallery';
$active_nav  = 'gallery';
$breadcrumbs = [['label'=>'Gallery']];
require_once '../_layout.php';

$error     = '';
$edit_item = null;
$show_form = isset($_GET['action']) && in_array($_GET['action'],['add','edit']);

// Delete
if (isset($_GET['action']) && $_GET['action']==='delete' && isset($_GET['id'])) {
    $gid = (int)$_GET['id'];
    $row = mysqli_fetch_assoc(mysqli_query($conn,"SELECT image_path FROM gallery WHERE id=$gid"));
    if ($row && strpos($row['image_path'],'placeholder')===false) {
        $fp = $_SERVER['DOCUMENT_ROOT'].'/school/'.$row['image_path'];
        if (file_exists($fp)) @unlink($fp);
    }
    mysqli_query($conn,"DELETE FROM gallery WHERE id=$gid");
    setFlash('success','Gallery item deleted.');
    redirect('manage_gallery.php');
}

// Load for edit
if (isset($_GET['action']) && $_GET['action']==='edit' && isset($_GET['id'])) {
    $edit_item = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM gallery WHERE id=".(int)$_GET['id']." LIMIT 1"));
    if (!$edit_item) redirect('manage_gallery.php');
}

// Save
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['save_gallery'])) {
    $title       = sanitize($conn, $_POST['title']       ?? '');
    $description = sanitize($conn, $_POST['description'] ?? '');
    $gal_id      = (int)($_POST['gal_id'] ?? 0);
    $image_path  = $edit_item['image_path'] ?? '';

    if (!$title) { $error = 'Title is required.'; $show_form = true; }
    else {
        // Handle upload
        if (!empty($_FILES['image']['name'])) {
            $dir = $_SERVER['DOCUMENT_ROOT'].'/school/uploads/gallery/';
            if (!is_dir($dir)) mkdir($dir,0755,true);
            $ext = strtolower(pathinfo($_FILES['image']['name'],PATHINFO_EXTENSION));
            if (!in_array($ext,['jpg','jpeg','png','gif','webp'])) {
                $error = 'Invalid image format. Use JPG, PNG, GIF or WEBP.'; $show_form = true;
            } elseif ($_FILES['image']['size'] > 5*1024*1024) {
                $error = 'File too large. Maximum 5 MB.'; $show_form = true;
            } else {
                $fn = 'gallery_'.time().'_'.rand(1000,9999).'.'.$ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'],$dir.$fn)) {
                    if ($gal_id>0 && $image_path && strpos($image_path,'placeholder')===false) {
                        $old = $_SERVER['DOCUMENT_ROOT'].'/school/'.$image_path;
                        if (file_exists($old)) @unlink($old);
                    }
                    $image_path = 'uploads/gallery/'.$fn;
                } else { $error = 'Upload failed. Check uploads/gallery/ permissions.'; $show_form = true; }
            }
        } elseif ($gal_id === 0) { $error = 'Please select an image to upload.'; $show_form = true; }

        if (!$error) {
            if ($gal_id > 0) {
                mysqli_query($conn,"UPDATE gallery SET title='$title',description='$description',image_path='$image_path' WHERE id=$gal_id");
                setFlash('success','Gallery item updated.');
            } else {
                mysqli_query($conn,"INSERT INTO gallery (title,description,image_path) VALUES ('$title','$description','$image_path')");
                setFlash('success','Image added to gallery.');
            }
            redirect('manage_gallery.php');
        }
    }
}

$gallery = mysqli_fetch_all(mysqli_query($conn,"SELECT * FROM gallery ORDER BY created_at DESC"), MYSQLI_ASSOC);

admin_head(); admin_sidebar(); admin_topbar();
?>

<!-- Add/Edit Form -->
<?php if ($show_form): ?>
<div class="panel" style="max-width:680px;margin-bottom:22px;">
    <div class="panel-header">
        <div class="panel-title"><i class="fas <?php echo $edit_item?'fa-edit':'fa-plus-circle'; ?>"></i> <?php echo $edit_item?'Edit Gallery Item':'Add New Gallery Image'; ?></div>
        <a href="manage_gallery.php" class="adm-btn adm-btn-ghost adm-btn-sm"><i class="fas fa-times"></i> Cancel</a>
    </div>
    <div class="panel-body">
        <?php if ($error): ?><div class="adm-flash adm-flash-error"><i class="fas fa-exclamation-circle"></i><?php echo htmlspecialchars($error); ?><button class="close-btn" onclick="this.parentElement.remove()">&times;</button></div><?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="gal_id" value="<?php echo $edit_item ? $edit_item['id'] : 0; ?>">

            <div class="adm-form-group">
                <label class="adm-label">Image Title <span class="req">*</span></label>
                <input class="adm-input" type="text" name="title" required placeholder="e.g. Annual Sports Day 2024"
                    value="<?php echo htmlspecialchars($edit_item ? $edit_item['title'] : ($_POST['title']??'')); ?>">
            </div>
            <div class="adm-form-group">
                <label class="adm-label">Caption / Description</label>
                <input class="adm-input" type="text" name="description" placeholder="Brief description (optional)"
                    value="<?php echo htmlspecialchars($edit_item ? $edit_item['description'] : ($_POST['description']??'')); ?>">
            </div>
            <div class="adm-form-group">
                <label class="adm-label"><?php echo $edit_item ? 'Replace Image (leave blank to keep current)' : 'Upload Image <span class="req">*</span>'; ?></label>
                <div class="upload-zone" id="uploadZone" onclick="document.getElementById('imgFile').click()">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>Click to choose an image, or drag &amp; drop here</p>
                    <p class="allowed">JPG, PNG, GIF, WEBP &nbsp;·&nbsp; Max 5 MB</p>
                </div>
                <input type="file" name="image" id="imgFile" accept="image/*" style="display:none;" onchange="previewImg(this)">
                <?php if ($edit_item && $edit_item['image_path']): ?>
                <p class="adm-hint" style="margin-top:8px;">Current:</p>
                <div class="img-preview-box" id="previewBox">
                    <img src="/school/<?php echo htmlspecialchars($edit_item['image_path']); ?>"
                         onerror="this.src='https://placehold.co/600x200/D16820/fff?text=Image'" alt="Current image">
                </div>
                <?php else: ?>
                <div class="img-preview-box" id="previewBox" style="display:none;"><img id="previewImg" src="" alt="Preview"></div>
                <?php endif; ?>
            </div>

            <div class="btn-group" style="margin-top:6px;">
                <button type="submit" name="save_gallery" class="adm-btn adm-btn-primary">
                    <i class="fas fa-save"></i> <?php echo $edit_item ? 'Update Image' : 'Add to Gallery'; ?>
                </button>
                <a href="manage_gallery.php" class="adm-btn adm-btn-ghost"><i class="fas fa-times"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Gallery Grid -->
<div class="panel">
    <div class="panel-header">
        <div class="panel-title"><i class="fas fa-th-large"></i> Gallery Images <span class="badge badge-info"><?php echo count($gallery); ?></span></div>
        <a href="manage_gallery.php?action=add" class="adm-btn adm-btn-primary adm-btn-sm"><i class="fas fa-plus"></i> Add Image</a>
    </div>
    <div class="panel-body">
        <?php if ($gallery): ?>
        <div class="adm-gallery-grid">
            <?php foreach ($gallery as $g): ?>
            <div class="adm-gallery-item">
                <img src="/school/<?php echo htmlspecialchars($g['image_path']); ?>"
                     onerror="this.src='https://placehold.co/400x150/D16820/fff?text=No+Image'"
                     alt="<?php echo htmlspecialchars($g['title']); ?>" loading="lazy">
                <div class="adm-gallery-item-body">
                    <div class="adm-gallery-item-title"><?php echo htmlspecialchars($g['title']); ?></div>
                    <div class="adm-gallery-item-desc"><?php echo htmlspecialchars($g['description'] ?: 'No description'); ?></div>
                    <div class="btn-group">
                        <a href="manage_gallery.php?action=edit&id=<?php echo $g['id']; ?>" class="adm-btn adm-btn-warning adm-btn-sm adm-btn-icon" title="Edit"><i class="fas fa-edit"></i></a>
                        <a href="manage_gallery.php?action=delete&id=<?php echo $g['id']; ?>" class="adm-btn adm-btn-danger adm-btn-sm adm-btn-icon" title="Delete" data-confirm="Delete '<?php echo htmlspecialchars(addslashes($g['title'])); ?>'?"><i class="fas fa-trash"></i></a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state"><i class="fas fa-images"></i><h3>No Gallery Images Yet</h3><p>Click <strong>Add Image</strong> to upload the first photo.</p></div>
        <?php endif; ?>
    </div>
</div>

<script>
function previewImg(input) {
    const box = document.getElementById('previewBox');
    const img = box.querySelector('img') || document.getElementById('previewImg');
    if (input.files && input.files[0]) {
        const r = new FileReader();
        r.onload = e => { img.src = e.target.result; box.style.display = 'block'; };
        r.readAsDataURL(input.files[0]);
    }
}
const zone = document.getElementById('uploadZone');
if (zone) {
    zone.addEventListener('dragover',  e => { e.preventDefault(); zone.classList.add('drag-over'); });
    zone.addEventListener('dragleave', ()  => zone.classList.remove('drag-over'));
    zone.addEventListener('drop', e => {
        e.preventDefault(); zone.classList.remove('drag-over');
        const f = e.dataTransfer.files[0];
        if (f) {
            const dt = new DataTransfer(); dt.items.add(f);
            const inp = document.getElementById('imgFile');
            inp.files = dt.files;
            previewImg(inp);
        }
    });
}
</script>
<?php admin_end(); ?>