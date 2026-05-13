<?php
// ============================================================
// student/results.php — My Exam Results
// ============================================================
$portal_role = 'student';
$page_title  = 'My Results';
$active_nav  = 'results';
require_once '_layout.php';

$student = $_portal_user;
$student_id = $student['id'];

// Fetch results from database
$results_query = mysqli_query($conn, "SELECT * FROM exam_results WHERE student_id=$student_id ORDER BY exam_date DESC, subject");
$all_results = mysqli_fetch_all($results_query, MYSQLI_ASSOC);

// Group results by exam
$terms = [];
foreach ($all_results as $result) {
    $exam_name = $result['exam_name'];
    if (!isset($terms[$exam_name])) {
        $terms[$exam_name] = [];
    }
    $terms[$exam_name][] = [
        'subject' => $result['subject'],
        'marks' => $result['marks_obtained'],
        'out_of' => $result['marks_total'],
        'grade' => $result['grade'],
        'remarks' => $result['remarks'],
        'date' => $result['exam_date']
    ];
}

// If no results, show sample message
$has_results = count($terms) > 0;

function grade_color($grade) {
    if (str_starts_with($grade,'A')) return '#276749';
    if (str_starts_with($grade,'B')) return '#2b6cb0';
    if (str_starts_with($grade,'C')) return '#d69e2e';
    return '#e53e3e';
}

function avg($results) {
    if (!$results) return 0;
    $total = 0;
    $count = 0;
    foreach ($results as $r) {
        $total += ($r['marks'] / $r['out_of']) * 100;
        $count++;
    }
    return $count > 0 ? round($total / $count, 1) : 0;
}

$selected_term = $_GET['term'] ?? array_key_first($terms);
$current = $terms[$selected_term] ?? reset($terms);

portal_head();
portal_sidebar($student);
portal_topbar();
?>

<?php if (!$has_results): ?>
<!-- No Results Message -->
<div class="prt-panel">
    <div class="prt-panel-body" style="text-align:center;padding:60px 20px;">
        <i class="fas fa-clipboard-list" style="font-size:4rem;color:var(--border);margin-bottom:20px;"></i>
        <h3 style="color:var(--text);margin-bottom:10px;">No Exam Results Yet</h3>
        <p style="color:var(--gray);font-size:.9rem;">Your exam results will appear here once your teachers enter them into the system.</p>
    </div>
</div>
<?php else: ?>

<!-- Term selector + average KPI -->
<div style="display:flex;gap:14px;flex-wrap:wrap;align-items:flex-start;margin-bottom:20px;">
    <div style="flex:1;min-width:200px;">
        <div class="prt-panel" style="margin-bottom:0;">
            <div class="prt-panel-head">
                <div class="prt-panel-title"><i class="fas fa-poll"></i> Select Term</div>
            </div>
            <div class="prt-panel-body" style="display:flex;gap:8px;flex-wrap:wrap;">
                <?php foreach (array_keys($terms) as $term): ?>
                <a href="results.php?term=<?php echo urlencode($term); ?>"
                   class="prt-btn prt-btn-sm <?php echo $term===$selected_term?'prt-btn-primary':'prt-btn-ghost'; ?>">
                    <?php echo htmlspecialchars($term); ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Average score card -->
    <div>
        <div class="kpi-box green" style="min-width:160px;">
            <div class="kpi-icon-box green"><i class="fas fa-chart-line"></i></div>
            <div>
                <div class="kpi-val"><?php echo avg($current); ?>%</div>
                <div class="kpi-lbl">Average Score</div>
            </div>
        </div>
    </div>
    <div>
        <div class="kpi-box blue" style="min-width:160px;">
            <div class="kpi-icon-box blue"><i class="fas fa-book"></i></div>
            <div>
                <div class="kpi-val"><?php echo count($current); ?></div>
                <div class="kpi-lbl">Subjects</div>
            </div>
        </div>
    </div>
</div>

<!-- Results table -->
<div class="prt-panel">
    <div class="prt-panel-head">
        <div class="prt-panel-title"><i class="fas fa-table"></i> Results — <?php echo htmlspecialchars($selected_term); ?></div>
        <span class="prt-badge prt-badge-info"><?php echo htmlspecialchars($student['class_grade']); ?></span>
    </div>
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:.84rem;">
            <thead>
                <tr style="background:var(--off-white);">
                    <th style="padding:11px 16px;border-bottom:2px solid var(--border);text-align:left;font-size:.72rem;text-transform:uppercase;letter-spacing:.4px;color:var(--gray);">#</th>
                    <th style="padding:11px 16px;border-bottom:2px solid var(--border);text-align:left;font-size:.72rem;text-transform:uppercase;letter-spacing:.4px;color:var(--gray);">Subject</th>
                    <th style="padding:11px 16px;border-bottom:2px solid var(--border);text-align:center;font-size:.72rem;text-transform:uppercase;letter-spacing:.4px;color:var(--gray);">Marks</th>
                    <th style="padding:11px 16px;border-bottom:2px solid var(--border);text-align:center;font-size:.72rem;text-transform:uppercase;letter-spacing:.4px;color:var(--gray);">Grade</th>
                    <th style="padding:11px 16px;border-bottom:2px solid var(--border);text-align:left;font-size:.72rem;text-transform:uppercase;letter-spacing:.4px;color:var(--gray);">Performance Bar</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($current as $i => $r): ?>
            <tr style="border-bottom:1px solid var(--border);transition:background .15s;" onmouseover="this.style.background='var(--off-white)';" onmouseout="this.style.background='';">
                <td style="padding:12px 16px;color:var(--gray);"><?php echo $i+1; ?></td>
                <td style="padding:12px 16px;font-weight:700;"><?php echo htmlspecialchars($r['subject']); ?></td>
                <td style="padding:12px 16px;text-align:center;font-weight:700;"><?php echo $r['marks']; ?> / <?php echo $r['out_of']; ?></td>
                <td style="padding:12px 16px;text-align:center;">
                    <span style="display:inline-block;background:<?php echo grade_color($r['grade']); ?>18;color:<?php echo grade_color($r['grade']); ?>;border:1px solid <?php echo grade_color($r['grade']); ?>40;padding:3px 12px;border-radius:20px;font-weight:800;font-size:.75rem;"><?php echo $r['grade']; ?></span>
                </td>
                <td style="padding:12px 16px;">
                    <div style="background:var(--border);border-radius:10px;height:7px;overflow:hidden;">
                        <div style="background:<?php echo grade_color($r['grade']); ?>;height:100%;width:<?php echo $r['marks']; ?>%;border-radius:10px;transition:width 1s;"></div>
                    </div>
                    <div style="font-size:.7rem;color:var(--gray);margin-top:3px;"><?php echo round($r['marks']/$r['out_of']*100); ?>%</div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="background:var(--off-white);">
                    <td colspan="2" style="padding:12px 16px;font-weight:800;font-size:.82rem;">Overall Average</td>
                    <td style="padding:12px 16px;text-align:center;font-weight:800;font-family:var(--font-heading);font-size:1rem;color:var(--role-color);"><?php echo avg($current); ?>%</td>
                    <td colspan="2" style="padding:12px 16px;color:var(--gray);font-size:.78rem;">
                        <?php
                        $a = avg($current);
                        echo $a >= 80 ? '🏆 Excellent performance!' : ($a >= 65 ? '👍 Good performance. Keep it up!' : '📚 Work hard to improve your grades.');
                        ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="prt-panel-foot" style="font-size:.75rem;color:var(--gray);">
        <i class="fas fa-info-circle" style="color:var(--role-color);"></i>
        Results are entered by your teachers. Contact your class teacher if you have any questions.
    </div>
</div>

<?php endif; ?>

<?php portal_end(); ?>