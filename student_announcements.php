<?php
// ============================================================
// student_announcements.php — Student Portal Announcements
// ============================================================
$portal_role = 'student';
$page_title  = 'Announcements';
$active_nav  = 'announcements';
require_once '_layout.php';

$user   = $_portal_user;
$search = isset($_GET['search']) ? sanitize($conn, $_GET['search']) : '';
$where  = $search ? "WHERE title LIKE '%$search%' OR content LIKE '%$search%'" : '';
$anns   = mysqli_fetch_all(mysqli_query($conn,"SELECT * FROM announcements $where ORDER BY created_at DESC"), MYSQLI_ASSOC);
$total  = count($anns);

portal_head();
portal_sidebar($user);
portal_topbar();
?>

<div class="prt-panel">
    <div class="prt-panel-head">
        <div class="prt-panel-title"><i class="fas fa-bullhorn"></i> All Announcements <span class="prt-badge prt-badge-info"><?php echo $total; ?></span></div>
        <form method="GET" style="display:flex;gap:8px;align-items:center;">
            <input class="prt-input" type="text" name="search" placeholder="Search announcements…" value="<?php echo htmlspecialchars($search); ?>" style="min-width:220px;">
            <button type="submit" class="prt-btn prt-btn-primary prt-btn-sm"><i class="fas fa-search"></i></button>
            <?php if ($search): ?><a href="student_announcements.php" class="prt-btn prt-btn-ghost prt-btn-sm"><i class="fas fa-times"></i></a><?php endif; ?>
        </form>
    </div>
    <div class="prt-panel-body">
        <?php if ($anns): ?>
        <div class="ann-list">
            <?php foreach ($anns as $a): ?>
            <div class="ann-item">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px;flex-wrap:wrap;">
                    <div class="ann-item-title"><?php echo htmlspecialchars($a['title']); ?></div>
                    <span class="prt-badge prt-badge-info" style="white-space:nowrap;"><?php echo date('d M Y', strtotime($a['created_at'])); ?></span>
                </div>
                <div class="ann-item-body" style="margin-top:8px;"><?php echo nl2br(htmlspecialchars($a['content'])); ?></div>
                <div class="ann-item-meta">
                    <span><i class="fas fa-user"></i><?php echo htmlspecialchars($a['posted_by']); ?></span>
                    <span><i class="fas fa-clock"></i><?php echo date('g:i A, d M Y', strtotime($a['created_at'])); ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-bullhorn"></i>
            <h3><?php echo $search ? 'No Results Found' : 'No Announcements Yet'; ?></h3>
            <p><?php echo $search ? "No announcements matching \"$search\"." : 'Check back soon for school updates.'; ?></p>
            <?php if ($search): ?><a href="student_announcements.php" class="prt-btn prt-btn-ghost prt-btn-sm" style="margin-top:10px;"><i class="fas fa-times"></i> Clear Search</a><?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php portal_end(); ?>
