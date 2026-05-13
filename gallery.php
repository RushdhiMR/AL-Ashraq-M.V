<?php
// ============================================================
// gallery.php - Gallery Page
// ============================================================
$page_title = 'Gallery';
require_once __DIR__ . '/includes/header.php';

$gal_res = mysqli_query($conn, "SELECT * FROM gallery ORDER BY created_at DESC");
$gallery = mysqli_fetch_all($gal_res, MYSQLI_ASSOC);
?>

<div class="page-hero">
    <h1>Gallery</h1>
    <p>Glimpses of life at Al-Ashraq M.V – our campus, events, and achievements</p>
    <div class="breadcrumb"><a href="index.php">Home</a><span>/</span><span>Gallery</span></div>
</div>

<section class="section">
    <div class="container">

        <?php if ($gallery): ?>
        <div class="gallery-grid">
            <?php foreach ($gallery as $item): ?>
            <div class="gallery-item">
                <?php
                    $img_path = '/school/' . $item['image_path'];
                    // If image doesn't exist, show a styled placeholder
                    $display_img = file_exists($_SERVER['DOCUMENT_ROOT'] . $img_path)
                        ? $img_path
                        : 'https://placehold.co/600x400/D16820/ffffff?text=' . urlencode($item['title']);
                ?>
                <img src="<?php echo htmlspecialchars($display_img); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" loading="lazy">
                <div class="gallery-overlay">
                    <strong><?php echo htmlspecialchars($item['title']); ?></strong>
                    <?php if ($item['description']): ?>
                    <p style="font-size:13px;margin-top:4px;opacity:0.85;"><?php echo htmlspecialchars($item['description']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
            <div class="text-center" style="padding:60px 20px;">
                <i class="fas fa-images" style="font-size:60px;color:var(--light-gray);margin-bottom:20px;display:block;"></i>
                <h3 style="color:var(--gray);">No Gallery Images Yet</h3>
                <p style="color:var(--gray);">Images will appear here once added by the admin.</p>
            </div>
        <?php endif; ?>

    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
