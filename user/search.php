<?php 
require_once '../config/init.php';

if (!isLoggedIn()) {
    redirect('/login.php');
}

$query = isset($_GET['q']) ? sanitize($_GET['q']) : '';
$results = [
    'notices' => [],
    'schemes' => [],
    'jobs' => []
];

if ($query) {
    $q = "%$query%";
    
    // Search Notices
    $stmt = $pdo->prepare("SELECT * FROM notices WHERE title LIKE ? OR content LIKE ?");
    $stmt->execute([$q, $q]);
    $results['notices'] = $stmt->fetchAll();
    
    // Search Schemes
    $stmt = $pdo->prepare("SELECT * FROM schemes WHERE title LIKE ? OR description LIKE ?");
    $stmt->execute([$q, $q]);
    $results['schemes'] = $stmt->fetchAll();

    // Search Jobs
    $stmt = $pdo->prepare("SELECT * FROM jobs WHERE title LIKE ? OR description LIKE ?");
    $stmt->execute([$q, $q]);
    $results['jobs'] = $stmt->fetchAll();
}

include_once '../includes/header.php'; 
?>

<div class="container-fluid py-4 px-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card p-4 border-0 shadow-sm rounded-4 bg-primary text-white">
                <h4 class="fw-bold mb-3">तुम्ही काय शोधत आहात?</h4>
                <form action="" method="GET" class="d-flex">
                    <input type="text" name="q" class="form-control form-control-lg border-0 shadow-none rounded-pill px-4" placeholder="उदा. पाणी, योजना, काम..." value="<?php echo $query; ?>">
                    <button type="submit" class="btn btn-light rounded-pill px-4 ms-2 fw-bold text-primary">शोधा</button>
                </form>
            </div>
        </div>
    </div>

    <?php if ($query): ?>
        <h5 class="fw-bold mb-4 text-muted">'<?php echo $query; ?>' साठी मिळालेले निकाल:</h5>
        
        <div class="row">
            <!-- Notices -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 p-4 border-0 shadow-sm rounded-4">
                    <h6 class="fw-bold text-primary mb-3"><i class="fas fa-bullhorn me-2"></i>सूचना (Notices)</h6>
                    <?php if (empty($results['notices'])): ?>
                        <p class="small text-muted">काहीही सापडले नाही.</p>
                    <?php else: ?>
                        <?php foreach($results['notices'] as $n): ?>
                            <div class="border-bottom pb-2 mb-2">
                                <a href="notices.php" class="text-decoration-none text-dark fw-bold small d-block"><?php echo $n['title']; ?></a>
                                <small class="text-muted extra-small"><?php echo formatDate($n['created_at']); ?></small>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Schemes -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 p-4 border-0 shadow-sm rounded-4">
                    <h6 class="fw-bold text-success mb-3"><i class="fas fa-hand-holding-heart me-2"></i>योजना (Schemes)</h6>
                    <?php if (empty($results['schemes'])): ?>
                        <p class="small text-muted">काहीही सापडले नाही.</p>
                    <?php else: ?>
                        <?php foreach($results['schemes'] as $s): ?>
                            <div class="border-bottom pb-2 mb-2">
                                <a href="schemes.php" class="text-decoration-none text-dark fw-bold small d-block"><?php echo $s['title']; ?></a>
                                <small class="text-muted extra-small"><?php echo $s['category']; ?></small>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Jobs -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 p-4 border-0 shadow-sm rounded-4">
                    <h6 class="fw-bold text-info mb-3"><i class="fas fa-briefcase me-2"></i>नोकरी (Jobs)</h6>
                    <?php if (empty($results['jobs'])): ?>
                        <p class="small text-muted">काहीही सापडले नाही.</p>
                    <?php else: ?>
                        <?php foreach($results['jobs'] as $j): ?>
                            <div class="border-bottom pb-2 mb-2">
                                <a href="jobs.php" class="text-decoration-none text-dark fw-bold small d-block"><?php echo $j['title']; ?></a>
                                <small class="text-muted extra-small">₹ <?php echo $j['wage']; ?></small>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include_once '../includes/footer.php'; ?>
