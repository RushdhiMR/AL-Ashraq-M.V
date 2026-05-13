<?php
// ============================================================
// announcements.php — Public Announcements Page
// ============================================================
$page_title = 'Announcements';
require_once 'header.php';

$search = isset($_GET['search']) ? sanitize($conn, $_GET['search']) : '';
$where  = $search ? "WHERE title LIKE '%$search%' OR content LIKE '%$search%'" : '';
$anns   = mysqli_fetch_all(mysqli_query($conn,"SELECT * FROM announcements $where ORDER BY created_at DESC"), MYSQLI_ASSOC);
$total  = count($anns);
?>

<!-- Page Hero -->
<div class="page-hero">
    <div class="container">
        <h1>School Announcements</h1>
        <p>Stay updated with the latest news and events</p>
        <div class="breadcrumb">
            <a href="index.php">Home</a>
            <span>/</span>
            <span>Announcements</span>
        </div>
    </div>
</div>

<!-- Main Content -->
<section class="section">
    <div class="container">
        
        <!-- Search Bar -->
        <div class="dash-card" style="margin-bottom:30px;">
            <form method="GET" style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
                <input type="text" name="search" placeholder="Search announcements…" value="<?php echo htmlspecialchars($search); ?>" style="flex:1;min-width:250px;padding:12px 16px;border:2px solid var(--light-gray);border-radius:8px;font-size:15px;">
                <button type="submit" class="btn btn-green"><i class="fas fa-search"></i> Search</button>
                <?php if ($search): ?>
                <a href="announcements.php" class="btn btn-outline" style="border-color:var(--gray);color:var(--gray);"><i class="fas fa-times"></i> Clear</a>
                <?php endif; ?>
            </form>
            <?php if ($total > 0): ?>
            <p style="margin-top:12px;color:var(--gray);font-size:14px;">
                <i class="fas fa-info-circle" style="color:var(--primary);"></i>
                Showing <strong><?php echo $total; ?></strong> announcement<?php echo $total !== 1 ? 's' : ''; ?>
                <?php if ($search): ?> matching "<strong><?php echo htmlspecialchars($search); ?></strong>"<?php endif; ?>
            </p>
            <?php endif; ?>
        </div>

        <!-- Announcements List -->
        <?php if ($anns): ?>
        <div style="display:flex;flex-direction:column;gap:20px;">
            <?php foreach ($anns as $a): ?>
            <div class="announcement-card">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;margin-bottom:12px;">
                    <h3 style="margin:0;flex:1;"><?php echo htmlspecialchars($a['title']); ?></h3>
                    <span class="badge badge-info" style="white-space:nowrap;font-size:11px;">
                        <i class="fas fa-calendar-alt"></i> <?php echo date('d M Y', strtotime($a['created_at'])); ?>
                    </span>
                </div>
                <p style="color:var(--text);line-height:1.8;margin-bottom:12px;white-space:pre-wrap;"><?php echo htmlspecialchars($a['content']); ?></p>
                <div class="announcement-meta">
                    <span><i class="fas fa-user"></i> Posted by <?php echo htmlspecialchars($a['posted_by']); ?></span>
                    <span><i class="fas fa-clock"></i> <?php echo date('g:i A, d M Y', strtotime($a['created_at'])); ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <!-- Empty State -->
        <div class="dash-card" style="text-align:center;padding:60px 20px;">
            <i class="fas fa-bullhorn" style="font-size:4rem;color:var(--light-gray);margin-bottom:20px;"></i>
            <h3 style="color:var(--text);margin-bottom:10px;">
                <?php echo $search ? 'No Results Found' : 'No Announcements Yet'; ?>
            </h3>
            <p style="color:var(--gray);font-size:15px;max-width:400px;margin:0 auto;">
                <?php echo $search ? "No announcements matching \"" . htmlspecialchars($search) . "\"." : 'Check back soon for school updates and important notices.'; ?>
            </p>
            <?php if ($search): ?>
            <a href="announcements.php" class="btn btn-green" style="margin-top:20px;"><i class="fas fa-times"></i> Clear Search</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div>
</section>

<?php require_once 'footer.php'; ?>