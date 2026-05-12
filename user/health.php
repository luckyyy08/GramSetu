<?php 
require_once '../config/init.php';

if (!isLoggedIn()) {
    redirect('/login.php');
}

// Filter notices for 'Health' category
$stmt = $pdo->prepare("SELECT * FROM notices WHERE category = 'आरोग्य' OR category = 'Health' ORDER BY created_at DESC");
$stmt->execute();
$health_notices = $stmt->fetchAll();

include_once '../includes/header.php'; 
?>

<div class="container-fluid py-4 px-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h3 class="fw-bold mb-0 text-danger"><i class="fas fa-heartbeat me-2"></i>आरोग्य सेवा व अपडेट्स</h3>
            <p class="text-muted small">गावचे लसीकरण, आरोग्य शिबिरे आणि सुदृढ जीवनासाठी मार्गदर्शन.</p>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar/Info -->
        <div class="col-md-4 mb-4">
            <div class="card p-4 border-0 shadow-sm rounded-4 bg-danger text-white mb-4">
                <h5 class="fw-bold mb-3">आपत्कालीन आरोग्य मदत</h5>
                <p class="small opacity-80">गावातील सरकारी रुग्णवाहिका किंवा दवाखान्यासाठी खालील क्रमांकावर संपर्क साधावा.</p>
                <hr class="opacity-20">
                <div class="h4 fw-bold">कॉल: 108 / 102</div>
            </div>
            
            <div class="card p-4 border-0 shadow-sm rounded-4">
                <h6 class="fw-bold mb-3">महत्वाचे संपर्क</h6>
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item px-0 border-light d-flex justify-content-between">
                        <span>आशा वर्कर</span>
                        <a href="tel:9999999999" class="text-decoration-none">कॉल करा</a>
                    </li>
                    <li class="list-group-item px-0 border-light d-flex justify-content-between">
                        <span>प्राथमिक आरोग्य केंद्र</span>
                        <a href="tel:8888888888" class="text-decoration-none">कॉल करा</a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Health Notices -->
        <div class="col-md-8">
            <div class="card p-4 border-0 shadow-sm rounded-4">
                <h5 class="fw-bold mb-4">आरोग्य घोषणा व लसीकरण</h5>
                <?php if (empty($health_notices)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-hospital-alt fa-3x text-light mb-3"></i>
                        <p class="text-muted">सध्या कोणतीही आरोग्य विषयक सूचना उपलब्ध नाही.</p>
                    </div>
                <?php else: ?>
                    <?php foreach($health_notices as $n): ?>
                        <div class="p-3 bg-light rounded-4 mb-3 border-start border-4 border-danger">
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
