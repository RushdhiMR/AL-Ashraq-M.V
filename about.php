<?php
// ============================================================
// about.php - About Us Page
// ============================================================
$page_title = 'About Us';
require_once __DIR__ . '/includes/header.php';

$res  = mysqli_query($conn, "SELECT * FROM school_info LIMIT 1");
$info = $res ? mysqli_fetch_assoc($res) : [];
?>

<!-- Page Hero -->
<div class="page-hero">
    <h1>About Us</h1>
    <p>Learn about our history, vision, and the values that drive us</p>
    <div class="breadcrumb"><a href="index.php">Home</a><span>/</span><span>About Us</span></div>
</div>

<!-- History -->
<section class="section">
    <div class="container">
        <div class="grid-2" style="align-items:center;gap:50px;">
            <div>
                <p style="color:var(--accent);font-weight:700;text-transform:uppercase;letter-spacing:1px;margin-bottom:8px;font-size:13px;">Our Story</p>
                <h2 style="margin-bottom:18px;">A Rich History of Educational Excellence</h2>
                <p style="color:var(--gray);line-height:1.9;margin-bottom:16px;">
                    Al-Ashraq Maha Vidyalaya was established in <?php echo htmlspecialchars($info['established_year'] ?? '1980'); ?> with a commitment to providing quality education to the community. Over the decades, our school has grown from humble beginnings into a respected institution known for academic achievement and character development.
                </p>
                <p style="color:var(--gray);line-height:1.9;margin-bottom:16px;">
                    Through the years, countless students have passed through our doors and gone on to make meaningful contributions to society — as professionals, leaders, and responsible citizens. This legacy inspires everything we do today.
                </p>
                <p style="color:var(--gray);line-height:1.9;">
                    Located in the heart of our community, Al-Ashraq M.V continues to evolve with modern teaching methods while staying true to its founding values of integrity, discipline, and excellence.
                </p>
            </div>
            <div style="background:linear-gradient(135deg,var(--primary),var(--primary-light));border-radius:20px;padding:40px;color:white;text-align:center;">
                <i class="fas fa-university" style="font-size:80px;opacity:0.25;display:block;margin-bottom:20px;"></i>
                <h3 style="color:white;font-size:1.8rem;">Est. <?php echo htmlspecialchars($info['established_year'] ?? '1980'); ?></h3>
                <p style="opacity:0.85;margin-top:10px;">Serving our community with dedication for over <?php echo date('Y') - (int)($info['established_year'] ?? 1980); ?> years</p>
            </div>
        </div>
    </div>
</section>

<!-- Vision & Mission -->
<section class="section section-alt">
    <div class="container">
        <div class="section-title">
            <h2>Our Vision & Mission</h2>
            <div class="title-line"></div>
        </div>
        <div class="grid-2">
            <div class="card">
                <div class="card-body">
                    <div class="card-icon"><i class="fas fa-eye"></i></div>
                    <h3>Our Vision</h3>
                    <p style="color:var(--gray);line-height:1.9;">
                        <?php echo htmlspecialchars($info['vision'] ?? 'To be a centre of educational excellence that develops well-rounded, responsible, and globally competent citizens who contribute positively to society.'); ?>
                    </p>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="card-icon"><i class="fas fa-rocket"></i></div>
                    <h3>Our Mission</h3>
                    <p style="color:var(--gray);line-height:1.9;">
                        <?php echo htmlspecialchars($info['mission'] ?? 'Our mission is to provide a safe, inclusive, and stimulating learning environment where every student is inspired to excel academically, develop strong character, and become compassionate leaders for tomorrow.'); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- School Values -->
<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>Our Core Values</h2>
            <p>The principles that guide everything we do at Al-Ashraq M.V</p>
            <div class="title-line"></div>
        </div>
        <div class="value-cards">
            <div class="card value-card">
                <div class="card-body">
                    <div class="value-icon"><i class="fas fa-balance-scale"></i></div>
                    <h3>Integrity</h3>
                    <p style="color:var(--gray);">We uphold honesty and ethical conduct in all that we do, teaching our students to be people of strong character.</p>
                </div>
            </div>
            <div class="card value-card">
                <div class="card-body">
                    <div class="value-icon"><i class="fas fa-star"></i></div>
                    <h3>Excellence</h3>
                    <p style="color:var(--gray);">We strive for the highest standards in academics, sports, and all co-curricular activities.</p>
                </div>
            </div>
            <div class="card value-card">
                <div class="card-body">
                    <div class="value-icon"><i class="fas fa-hands-helping"></i></div>
                    <h3>Compassion</h3>
                    <p style="color:var(--gray);">We cultivate empathy, kindness, and a sense of responsibility toward others and our community.</p>
                </div>
            </div>
            <div class="card value-card">
                <div class="card-body">
                    <div class="value-icon"><i class="fas fa-lightbulb"></i></div>
                    <h3>Innovation</h3>
                    <p style="color:var(--gray);">We embrace creative thinking and modern approaches to learning while honouring proven traditions.</p>
                </div>
            </div>
            <div class="card value-card">
                <div class="card-body">
                    <div class="value-icon"><i class="fas fa-users"></i></div>
                    <h3>Community</h3>
                    <p style="color:var(--gray);">We believe in the power of togetherness — students, teachers, parents, and the wider community working as one.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Principal's Message -->
<section class="section section-alt" id="principal">
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
                <p class="principal-quote"><?php echo nl2br(htmlspecialchars($info['principal_message'] ?? 'Welcome to Al-Ashraq M.V. Our school is dedicated to providing quality education that empowers every student to achieve their full potential. We believe in nurturing intellectual curiosity, moral values, and a lifelong love of learning.')); ?></p>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
