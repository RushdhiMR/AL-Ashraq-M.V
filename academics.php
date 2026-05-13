<?php
// ============================================================
// academics.php - Academics Page
// ============================================================
$page_title = 'Academics';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-hero">
    <h1>Academics</h1>
    <p>Explore our classes, curriculum, teaching methods, and examination details</p>
    <div class="breadcrumb"><a href="index.php">Home</a><span>/</span><span>Academics</span></div>
</div>

<!-- Grades / Classes -->
<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>Classes & Grades</h2>
            <p>Al-Ashraq M.V offers education from Grade 1 through to Advanced Level</p>
            <div class="title-line"></div>
        </div>

        <div class="tabs-wrapper">
            <div class="tabs">
                <button class="tab-btn active" data-tab="primary">Primary (1–5)</button>
                <button class="tab-btn" data-tab="junior">Junior Secondary (6–9)</button>
                <button class="tab-btn" data-tab="ol">O/L (10–11)</button>
                <button class="tab-btn" data-tab="al">A/L (1st & 2nd Year)</button>
            </div>

            <div class="tab-panel active" id="primary">
                <div class="dash-card">
                    <h2>Primary Section – Grades 1 to 5</h2>
                    <p style="color:var(--gray);margin-bottom:16px;">The primary section focuses on building a strong foundation in literacy, numeracy, and creative thinking. Our child-centred approach ensures every young learner feels safe, supported, and excited about learning.</p>
                    <ul style="color:var(--gray);line-height:2.2;padding-left:20px;list-style:disc;">
                        <li>Sinhala / Tamil Language</li>
                        <li>English Language</li>
                        <li>Mathematics</li>
                        <li>Environmental Science</li>
                        <li>Religion (Islam / Buddhism)</li>
                        <li>Arts & Crafts</li>
                        <li>Physical Education</li>
                    </ul>
                </div>
            </div>

            <div class="tab-panel" id="junior">
                <div class="dash-card">
                    <h2>Junior Secondary – Grades 6 to 9</h2>
                    <p style="color:var(--gray);margin-bottom:16px;">Students in the junior secondary section are introduced to a broader curriculum that prepares them for the national O/L examinations. Critical thinking and independent study skills are emphasized.</p>
                    <ul style="color:var(--gray);line-height:2.2;padding-left:20px;list-style:disc;">
                        <li>Sinhala / Tamil</li>
                        <li>English</li>
                        <li>Mathematics</li>
                        <li>Science</li>
                        <li>History</li>
                        <li>Geography</li>
                        <li>Civics</li>
                        <li>Health & Physical Education</li>
                        <li>Religion & Moral Education</li>
                        <li>ICT (Grade 7 onwards)</li>
                    </ul>
                </div>
            </div>

            <div class="tab-panel" id="ol">
                <div class="dash-card">
                    <h2>Ordinary Level – Grades 10 & 11</h2>
                    <p style="color:var(--gray);margin-bottom:16px;">Preparing students for the G.C.E. Ordinary Level national examination conducted by the Department of Examinations, Sri Lanka. Students choose from Science, Arts, or Commerce streams.</p>
                    <div class="grid-2" style="margin-top:20px;">
                        <div>
                            <h4 style="color:var(--primary);margin-bottom:10px;">Science Stream</h4>
                            <ul style="color:var(--gray);line-height:2.2;padding-left:20px;list-style:disc;">
                                <li>Mathematics</li><li>Science</li><li>English</li><li>Sinhala/Tamil</li><li>History</li><li>Elective subjects</li>
                            </ul>
                        </div>
                        <div>
                            <h4 style="color:var(--primary);margin-bottom:10px;">Arts / Commerce Stream</h4>
                            <ul style="color:var(--gray);line-height:2.2;padding-left:20px;list-style:disc;">
                                <li>Mathematics</li><li>English</li><li>Sinhala/Tamil</li><li>History</li><li>Commerce / Buddhism</li><li>Elective subjects</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-panel" id="al">
                <div class="dash-card">
                    <h2>Advanced Level – 1st Year & 2nd Year</h2>
                    <p style="color:var(--gray);margin-bottom:16px;">G.C.E. Advanced Level prepares students for university entrance. We offer three main streams with dedicated experienced lecturers for each subject.</p>
                    <div class="grid-3" style="margin-top:20px;">
                        <div>
                            <h4 style="color:var(--primary);margin-bottom:10px;">Physical Science</h4>
                            <ul style="color:var(--gray);line-height:2.2;padding-left:20px;list-style:disc;">
                                <li>Physics</li><li>Chemistry</li><li>Combined Mathematics</li>
                            </ul>
                        </div>
                        <div>
                            <h4 style="color:var(--primary);margin-bottom:10px;">Biological Science</h4>
                            <ul style="color:var(--gray);line-height:2.2;padding-left:20px;list-style:disc;">
                                <li>Biology</li><li>Chemistry</li><li>Physics / Agriculture</li>
                            </ul>
                        </div>
                        <div>
                            <h4 style="color:var(--primary);margin-bottom:10px;">Commerce</h4>
                            <ul style="color:var(--gray);line-height:2.2;padding-left:20px;list-style:disc;">
                                <li>Accounting</li><li>Business Studies</li><li>Economics</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Teaching Methods -->
<section class="section section-alt">
    <div class="container">
        <div class="section-title">
            <h2>Our Teaching Methods</h2>
            <p>We combine time-tested pedagogy with modern educational approaches</p>
            <div class="title-line"></div>
        </div>
        <div class="grid-3">
            <div class="card"><div class="card-body">
                <div class="card-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                <h3>Interactive Classes</h3>
                <p style="color:var(--gray);">Our teachers encourage active participation through discussions, Q&A sessions, and collaborative group activities that deepen understanding.</p>
            </div></div>
            <div class="card"><div class="card-body">
                <div class="card-icon"><i class="fas fa-flask"></i></div>
                <h3>Practical Learning</h3>
                <p style="color:var(--gray);">Science students benefit from hands-on laboratory sessions, while all students engage in project-based assignments that apply theory to real life.</p>
            </div></div>
            <div class="card"><div class="card-body">
                <div class="card-icon"><i class="fas fa-laptop-code"></i></div>
                <h3>ICT-Integrated Learning</h3>
                <p style="color:var(--gray);">We integrate digital tools, educational software, and multimedia resources to make learning engaging and relevant to the modern world.</p>
            </div></div>
            <div class="card"><div class="card-body">
                <div class="card-icon"><i class="fas fa-book-reader"></i></div>
                <h3>Guided Self-Study</h3>
                <p style="color:var(--gray);">Students are coached on effective study strategies, note-taking, and time management to foster independent lifelong learners.</p>
            </div></div>
            <div class="card"><div class="card-body">
                <div class="card-icon"><i class="fas fa-users"></i></div>
                <h3>Peer Learning</h3>
                <p style="color:var(--gray);">Study groups and peer tutoring programmes allow students to learn from and support each other, building teamwork and communication skills.</p>
            </div></div>
            <div class="card"><div class="card-body">
                <div class="card-icon"><i class="fas fa-trophy"></i></div>
                <h3>Co-Curricular Activities</h3>
                <p style="color:var(--gray);">Sports, arts, debate, and clubs complement academic learning to develop well-rounded individuals with diverse skills and interests.</p>
            </div></div>
        </div>
    </div>
</section>

<!-- Examination Details -->
<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>Examination Details</h2>
            <p>Understanding our assessment and examination structure</p>
            <div class="title-line"></div>
        </div>

        <div class="accordion">
            <div class="accordion-header">
                <span><i class="fas fa-pencil-alt" style="color:var(--primary);margin-right:10px;"></i> Term Tests (Internal)</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="accordion-body">
                <p style="color:var(--gray);">Internal term tests are conducted three times per year (Term 1, Term 2, Term 3). These assessments cover all subjects and contribute to the student's ongoing academic evaluation. Results are communicated to parents/guardians via report cards at the end of each term.</p>
            </div>
        </div>

        <div class="accordion">
            <div class="accordion-header">
                <span><i class="fas fa-file-alt" style="color:var(--primary);margin-right:10px;"></i> G.C.E. Ordinary Level (O/L)</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="accordion-body">
                <p style="color:var(--gray);">The G.C.E. Ordinary Level examination is a national examination conducted by the Department of Examinations, Sri Lanka, for Grade 11 students. It covers 9 subjects including compulsory subjects and electives. Results determine eligibility for G.C.E. Advanced Level study.</p>
            </div>
        </div>

        <div class="accordion">
            <div class="accordion-header">
                <span><i class="fas fa-scroll" style="color:var(--primary);margin-right:10px;"></i> G.C.E. Advanced Level (A/L)</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="accordion-body">
                <p style="color:var(--gray);">The G.C.E. Advanced Level examination determines university entrance eligibility. Students sit for 3 main subjects plus a General English paper and a General Common Test. The Z-Score calculated from results determines university placement across Sri Lanka.</p>
            </div>
        </div>

        <div class="accordion">
            <div class="accordion-header">
                <span><i class="fas fa-star" style="color:var(--primary);margin-right:10px;"></i> School-Based Assessment (SBA)</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="accordion-body">
                <p style="color:var(--gray);">School-Based Assessments include assignments, projects, practical work, and oral presentations. These ongoing assessments provide a holistic view of student performance beyond written examinations and are recorded in the student's cumulative record.</p>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
