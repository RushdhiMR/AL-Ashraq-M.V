<?php
// ============================================================
// lecturer/students.php — Lecturer Student List
// ============================================================
$portal_role = 'lecturer';
$page_title  = 'My Students';
$active_nav  = 'students';
require_once '../_layout.php';

$lecturer = $_portal_user;
$search   = isset($_GET['search']) ? sanitize($conn, $_GET['search']) : '';
$where    = "WHERE status='approved'";
if ($search) {
    $search_safe = sanitize($conn, $search);
    $where .= " AND (full_name LIKE '%$search_safe%' OR email LIKE '%$search_safe%' OR class_grade LIKE '%$search_safe%')";
}
$students = mysqli_fetch_all(mysqli_query($conn, "SELECT * FROM students $where ORDER BY class_grade, full_name"), MYSQLI_ASSOC);
$total    = count($students);

portal_head();
portal_sidebar($lecturer);
portal_topbar();
?>

<div class="prt-panel">
    <div class="prt-panel-head">
        <div class="prt-panel-title"><i class="fas fa-users"></i> My Students <span class="prt-badge prt-badge-info"><?php echo $total; ?></span></div>
        <form method="GET" style="display:flex;gap:8px;align-items:center;">
            <input class="prt-input prt-input-sm" type="text" name="search" placeholder="Search students…" value="<?php echo htmlspecialchars($search); ?>" style="min-width:220px;padding:7px 12px;">
            <button type="submit" class="prt-btn prt-btn-primary prt-btn-sm"><i class="fas fa-search"></i></button>
            <?php if ($search): ?><a href="students.php" class="prt-btn prt-btn-ghost prt-btn-sm"><i class="fas fa-times"></i></a><?php endif; ?>
        </form>
    </div>
    <div class="prt-panel-body">
        <?php if ($students): ?>
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr><th>#</th><th>Name</th><th>Grade</th><th>Email</th><th>Status</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $i => $s): ?>
                    <tr>
                        <td class="text-muted"><?php echo $i + 1; ?></td>
                        <td><div class="cell-name"><?php echo htmlspecialchars($s['full_name']); ?></div><div class="cell-sub"><?php echo htmlspecialchars($s['phone'] ?: $s['email']); ?></div></td>
                        <td><?php echo htmlspecialchars($s['class_grade']); ?></td>
                        <td class="cell-wrap"><?php echo htmlspecialchars($s['email']); ?></td>
                        <td><span class="badge badge-<?php echo htmlspecialchars($s['status']); ?>"><?php echo ucfirst(htmlspecialchars($s['status'])); ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-user-graduate"></i>
            <h3>No students found</h3>
            <p><?php echo $search ? 'Try a different search term.' : 'No approved students are available yet.'; ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php portal_end(); ?>
