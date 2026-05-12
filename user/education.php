<?php 
require_once '../config/init.php';

if (!isLoggedIn()) {
    redirect('/login.php');
}

// Filter notices for 'Education' category
$stmt = $pdo->prepare("SELECT * FROM notices WHERE category = 'शिक्षण' OR category = 'Education' ORDER BY created_at DESC");
$stmt->execute();
$edu_notices = $stmt->fetchAll();

include_once '../includes/header.php'; 
?>

<div class="container-fluid py-4 px-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h3 class="fw-bold mb-0 text-primary"><i class="fas fa-user-graduate me-2"></i>शिक्षण व शिष्यवृत्ती केंद्र</h3>
            <p class="text-muted small">गावच्या विद्यार्थ्यांसाठी शाळा, परीक्षा आणि सरकारी मदतीची माहिती.</p>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar/Info -->
        <div class="col-md-4 mb-4">
            <div class="card p-4 border-0 shadow-sm rounded-4 bg-info text-white mb-4">
                <h5 class="fw-bold mb-3">विद्यार्थी मदत कक्ष</h5>
                <p class="small opacity-80">शिष्यवृत्ती अर्ज किंवा शैक्षणिक मदतीसाठी ग्रामपंचायत कार्यालयात संपर्क साधावा.</p>
                <hr class="opacity-20">
                <div class="small fw-bold">शाळा वेळ: सकाळी 10 ते दुपारी 5</div>
            </div>
            
            <div class="card p-4 border-0 shadow-sm rounded-4">
                <h6 class="fw-bold mb-3">महत्वाचे दुवे</h6>
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item px-0 border-light"><a href="#" class="text-decoration-none text-dark">महाडीबीटी पोर्टल (Scholarship)</a></li>
                    <li class="list-group-item px-0 border-light"><a href="#" class="text-decoration-none text-dark">शालेय निकाल (Result)</a></li>
                    <li class="list-group-item px-0 border-light"><a href="#" class="text-decoration-none text-dark">प्रवेश प्रक्रिया (Admission)</a></li>
                </ul>
            </div>
        </div>

        <!-- Education Notices -->
        <div class="col-md-8">
            <div class="card p-4 border-0 shadow-sm rounded-4">
                <h5 class="fw-bold mb-4">शैक्षणिक सूचना व घोषणा</h5>
                <?php if (empty($edu_notices)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-book-open fa-3x text-light mb-3"></i>
                        <p class="text-muted">सध्या कोणतीही शैक्षणिक सूचना उपलब्ध नाही.</p>
                    </div>
                <?php else: ?>
                    <?php foreach($edu_notices as $n): ?>
                        <div class="p-3 bg-light rounded-4 mb-3 border-start border-4 border-info">
                            <div class="d-flex justify-content-between mb-1">
                                <h6 class="fw-bold mb-0 text-dark"><?php echo $n['title']; ?></h6>
                                <small class="text-muted"><?php echo formatDate($n['created_at']); ?></small>
                            </div>
                            <p class="small text-muted mb-0"><?php echo $n['description']; ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>
