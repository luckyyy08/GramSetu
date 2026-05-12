<?php include_once 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <h1 class="display-4 fw-bold mb-4">GramSetu — डिजिटल ग्रामपंचायत</h1>
        <p class="lead mb-5">गाव, ग्रामपंचायत, व्यवसाय आणि विकास यांना जोडणारं डिजिटल ecosystem.</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="register.php" class="btn btn-primary btn-lg px-5 py-3">नोंदणी करा</a>
            <a href="#about" class="btn btn-outline-light btn-lg px-5 py-3">अधिक माहिती</a>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 mb-4">
                <div class="card p-4">
                    <h2 class="fw-bold text-primary">१०००+</h2>
                    <p class="text-muted mb-0">नोंदणीकृत नागरिक</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card p-4">
                    <h2 class="fw-bold text-success">५००+</h2>
                    <p class="text-muted mb-0">सोडवलेल्या तक्रारी</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card p-4">
                    <h2 class="fw-bold text-info">५०+</h2>
                    <p class="text-muted mb-0">सक्रिय योजना</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card p-4">
                    <h2 class="fw-bold text-warning">२०+</h2>
                    <p class="text-muted mb-0">येणारे कार्यक्रम</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5" id="about">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">आमच्या सेवा</h2>
            <div class="mx-auto" style="width: 80px; height: 4px; background: var(--primary-color);"></div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card h-100 p-4 text-center">
                    <div class="feature-icon">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <h3>डिजिटल सूचना फलक</h3>
                    <p class="text-muted">ग्रामपंचायतीच्या सर्व महत्त्वाच्या सूचना आता आपल्या मोबाईलवर पहा.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 p-4 text-center">
                    <div class="feature-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                    <h3>तक्रार निवारण</h3>
                    <p class="text-muted">पाणी, रस्ते, वीज किंवा स्वच्छतेबद्दलच्या तक्रारी ऑनलाईन नोंदवा आणि पाठपुरावा करा.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 p-4 text-center">
                    <div class="feature-icon">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <h3>सरकारी योजना</h3>
                    <p class="text-muted">विविध सरकारी योजनांची माहिती आणि पात्रतेबद्दल जाणून घ्या.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Notice Board Highlights -->
<section class="py-5 bg-light" id="notices">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">नवीनतम सूचना</h2>
            <a href="user/notices.php" class="btn btn-outline-primary">सर्व पहा</a>
        </div>
        <div class="row">
            <?php
            $stmt = $pdo->query("SELECT * FROM notices ORDER BY is_important DESC, created_at DESC LIMIT 2");
            $latest_notices = $stmt->fetchAll();
            
            if (empty($latest_notices)): ?>
                <div class="col-12 text-center text-muted py-4">
                    <h6>सध्या कोणतीही नवीन सूचना उपलब्ध नाही.</h6>
                </div>
            <?php else:
                foreach($latest_notices as $notice): 
                    $border_color = $notice['is_important'] ? 'border-danger' : 'border-primary';
                    $badge_color = $notice['is_important'] ? 'bg-danger' : 'bg-primary';
                ?>
                <div class="col-md-6 mb-3">
                    <div class="card p-3 border-start border-4 <?php echo $border_color; ?>">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge <?php echo $badge_color; ?>"><?php echo $notice['category']; ?></span>
                            <small class="text-muted"><?php echo formatDate($notice['created_at']); ?></small>
                        </div>
                        <h5 class="fw-bold"><?php echo $notice['title']; ?></h5>
                        <p class="text-muted small text-truncate-2"><?php echo $notice['content']; ?></p>
                        <a href="user/notices.php" class="text-primary small text-decoration-none fw-bold">अधिक वाचा <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
                <?php endforeach; 
            endif; ?>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5">
    <div class="container text-center py-5 bg-primary text-white rounded-4 shadow">
        <h2 class="fw-bold mb-4">आपल्या गावाच्या विकासात सहभागी व्हा!</h2>
        <p class="lead mb-4">आजच ग्रामसेतूवर नोंदणी करा आणि डिजिटल ग्रामपंचायतीचा लाभ घ्या.</p>
        <a href="register.php" class="btn btn-light btn-lg px-5 fw-bold text-primary">आताच सामील व्हा</a>
    </div>
</section>

<?php include_once 'includes/footer.php'; ?>
