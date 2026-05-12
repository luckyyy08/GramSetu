<?php 
require_once '../config/init.php';

if (!isLoggedIn()) {
    redirect('/login.php');
}

if (!isset($_GET['id'])) {
    redirect('/user/schemes.php');
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM schemes WHERE id = ?");
$stmt->execute([$id]);
$scheme = $stmt->fetch();

if (!$scheme) {
    redirect('/user/schemes.php');
}

include_once '../includes/header.php'; 
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="bg-primary text-white p-5 text-center">
                    <h2 class="fw-bold mb-0"><?php echo $scheme['title']; ?></h2>
                </div>
                <div class="card-body p-5">
                    <div class="row mb-5">
                        <div class="col-md-6 mb-4 mb-md-0 border-end">
                            <h5 class="fw-bold text-primary mb-3"><i class="fas fa-info-circle me-2"></i>योजनेबद्दल माहिती</h5>
                            <p class="text-muted"><?php echo nl2br($scheme['description']); ?></p>
                        </div>
                        <div class="col-md-6 ps-md-4">
                            <h5 class="fw-bold text-success mb-3"><i class="fas fa-user-check me-2"></i>पात्रता निकष</h5>
                            <p class="text-muted"><?php echo nl2br($scheme['eligibility']); ?></p>
                            
                            <div class="bg-light p-3 rounded mt-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">प्रकाशित तारीख:</span>
                                    <span class="fw-bold"><?php echo formatDate($scheme['created_at']); ?></span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">अंतिम तारीख:</span>
                                    <span class="fw-bold text-danger"><?php echo $scheme['deadline'] ? formatDate($scheme['deadline']) : '---'; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <?php if($scheme['link']): ?>
                            <a href="<?php echo $scheme['link']; ?>" target="_blank" class="btn btn-primary btn-lg px-5 py-3 fw-bold shadow-sm mb-3">
                                अधिकृत वेबसाइटवरून अर्ज करा <i class="fas fa-external-link-alt ms-2"></i>
                            </a>
                        <?php endif; ?>
                        <br>
                        <a href="schemes.php" class="btn btn-link text-muted text-decoration-none mt-2">
                            <i class="fas fa-arrow-left me-2"></i> सर्व योजनांकडे परत जा
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>
