<?php
// ============================================================
// index.php - Home Page
// ============================================================

$page_title = 'Home';
require_once __DIR__ . '/includes/header.php';

// Fetch school info
$info = [];
$res = mysqli_query($conn, "SELECT * FROM school_info LIMIT 1");
if ($res) $info = mysqli_fetch_assoc($res);

// Fetch latest 3 announcements
$ann_res = mysqli_query($conn, "SELECT * FROM announcements ORDER BY created_at DESC LIMIT 3");
$announcements = mysqli_fetch_all($ann_res, MYSQLI_ASSOC);

// Fetch counts
$student_count  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM students WHERE status='approved'"))['c'];
$lecturer_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM lecturers WHERE status='approved'"))['c'];
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <div class="hero-badge"><i class="fas fa-star"></i> Welcome to Our School</div>
            <h1>Al-Ashraq M.V</h1>
            <p><?php echo htmlspecialchars($info['tagline'] ?? 'Nurturing Minds, Shaping Futures – Excellence in Education since 1980'); ?></p>
            <div class="hero-buttons">
                <a href="admissions.php" class="btn btn-primary"><i class="fas fa-user-plus"></i> Apply Now</a>
                <a href="about.php" class="btn btn-outline"><i class="fas fa-info-circle"></i> Learn More</a>
            </div>
        </div>
    </div>
</section>

<!-- Stats Strip -->
<div class="stats-strip">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number" data-target="<?php echo $student_count ?: 500; ?>" data-suffix="+">0+</div>
                <div class="stat-label"><i class="fas fa-user-graduate"></i> Enrolled Students</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" data-target="<?php echo $lecturer_count ?: 40; ?>" data-suffix="+">0+</div>
                <div class="stat-label"><i class="fas fa-chalkboard-teacher"></i> Qualified Lecturers</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" data-target="<?php echo (int)($info['established_year'] ?? 1980) > 0 ? date('Y') - (int)($info['established_year'] ?? 1980) : 40; ?>" data-suffix="+">0+</div>
                <div class="stat-label"><i class="fas fa-calendar-alt"></i> Years of Excellence</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" data-target="12" data-suffix="">0</div>
                <div class="stat-label"><i class="fas fa-layer-group"></i> Grades Offered</div>
            </div>
        </div>
    </div>
</div>

<!-- Welcome / Intro Section -->
<section class="section">
    <div class="container">
        <div class="grid-2" style="align-items:center; gap:50px;">
            <div>
                <p style="color:var(--accent);font-weight:700;text-transform:uppercase;letter-spacing:1px;margin-bottom:10px;font-size:13px;"><i class="fas fa-school"></i> About Our School</p>
                <h2 style="font-size:2rem;margin-bottom:18px;">A Legacy of Learning & Excellence</h2>
                <p style="color:var(--gray);line-height:1.9;margin-bottom:16px;">
                    Al-Ashraq Maha Vidyalaya has been a beacon of educational excellence in Sri Lanka for decades. Founded with a vision to empower every child, our school provides a holistic education that combines academic rigour with moral values and community spirit.
                </p>
                <p style="color:var(--gray);line-height:1.9;margin-bottom:28px;">
                    We nurture curious minds and compassionate hearts, preparing students not just for examinations, but for life. Our dedicated faculty and supportive community make Al-Ashraq a place where every student can thrive.
                </p>
                <a href="about.php" class="btn btn-green"><i class="fas fa-arrow-right"></i> Our Full Story</a>
            </div>
            <div>
                <div style="background:linear-gradient(135deg, var(--primary),var(--primary-light));border-radius:20px;padding:40px;color:white;text-align:center;">
                    <i class="fas fa-graduation-cap" style="font-size:70px;opacity:0.3;margin-bottom:20px;display:block;"></i>
                    <h3 style="font-size:1.6rem;color:white;margin-bottom:10px;">Est. <?php echo htmlspecialchars($info['established_year'] ?? '1980'); ?></h3>
                    <p style="opacity:0.85;">Dedicated to providing quality education to the community of Sri Lanka</p>
                    <div style="margin-top:24px;display:flex;justify-content:center;gap:20px;flex-wrap:wrap;">
                        <div><div style="font-size:1.8rem;font-weight:700;color:var(--accent);">O/L</div><div style="font-size:12px;opacity:0.8;">& A/L</div></div>
                        <div><div style="font-size:1.8rem;font-weight:700;color:var(--accent);">100%</div><div style="font-size:12px;opacity:0.8;">Commitment</div></div>
                        <div><div style="font-size:1.8rem;font-weight:700;color:var(--accent);">Full</div><div style="font-size:12px;opacity:0.8;">Facilities</div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Principal's Message Preview -->
<section class="section section-alt">
    <div class="container">
        <div class="section-title">
            <h2>Principal's Message</h2>
            <div class="title-line"></div>
        </div>
        <div class="principal-card">
            <div class="principal-image">
                <?php if (!empty($info['principal_image'])): ?>
                <img class="principal-avatar" src="/school/<?php echo htmlspecialchars($info['principal_image']); ?>" alt="Principal photo" onerror="this.style.display='none'">
                <?php else: ?>
                <div class="principal-avatar"><i class="fas fa-user-tie"></i></div>
                <?php endif; ?>
                <div class="principal-name"><?php echo htmlspecialchars($info['principal_name'] ?? 'The Principal'); ?></div>
                <div class="principal-title">Principal, Al-Ashraq M.V</div>
            </div>
            <div>
                <p class="principal-quote">
                    <?php
                        $msg = $info['principal_message'] ?? '';
                        echo htmlspecialchars(strlen($msg) > 300 ? substr($msg, 0, 300) . '...' : $msg);
                    ?>
                </p>
                <a href="about.php#principal" class="btn btn-green mt-20"><i class="fas fa-book-open"></i> Read Full Message</a>
            </div>
        </div>
    </div>
</section>

<!-- Latest Announcements -->
<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>Latest Announcements</h2>
            <p>Stay up-to-date with the latest news and events from our school</p>
            <div class="title-line"></div>
        </div>

        <?php if ($announcements): ?>
            <?php foreach ($announcements as $ann): ?>
            <div class="announcement-card">
                <h3><?php echo htmlspecialchars($ann['title']); ?></h3>
                <p><?php echo htmlspecialchars(substr($ann['content'], 0, 200)) . (strlen($ann['content']) > 200 ? '...' : ''); ?></p>
                <div class="announcement-meta">
                    <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($ann['posted_by']); ?></span>
                    <span><i class="fas fa-calendar"></i> <?php echo date('F j, Y', strtotime($ann['created_at'])); ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center text-muted">No announcements at this time.</p>
        <?php endif; ?>

        <div class="text-center mt-30">
            <a href="announcements.php" class="btn btn-green"><i class="fas fa-bullhorn"></i> View All Announcements</a>
        </div>
    </div>
</section>

<!-- Quick Links -->
<section class="section section-alt">
    <div class="container">
        <div class="section-title">
            <h2>Quick Links</h2>
            <p>Navigate to the most visited sections of our website</p>
            <div class="title-line"></div>
        </div>
        <div class="quick-links-grid">
            <a href="admissions.php" class="quick-link-item">
                <i class="fas fa-user-graduate"></i>
                <span>Admissions</span>
            </a>
            <a href="academics.php" class="quick-link-item">
                <i class="fas fa-book"></i>
                <span>Academics</span>
            </a>
            <a href="announcements.php" class="quick-link-item">
                <i class="fas fa-bullhorn"></i>
                <span>Announcements</span>
            </a>
            <a href="gallery.php" class="quick-link-item">
                <i class="fas fa-images"></i>
                <span>Gallery</span>
            </a>
            <a href="contact.php" class="quick-link-item">
                <i class="fas fa-envelope"></i>
                <span>Contact Us</span>
            </a>
            <a href="student_register.php" class="quick-link-item">
                <i class="fas fa-user-plus"></i>
                <span>Register</span>
            </a>
            <a href="login.php" class="quick-link-item">
                <i class="fas fa-sign-in-alt"></i>
                <span>Login Portal</span>
            </a>
            <a href="about.php" class="quick-link-item">
                <i class="fas fa-info-circle"></i>
                <span>About Us</span>
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
