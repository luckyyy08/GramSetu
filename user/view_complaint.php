<?php 
require_once '../config/init.php';

if (!isLoggedIn()) {
    redirect('/login.php');
}

if (!isset($_GET['id'])) {
    redirect('/user/complaints.php');
}

$id = $_GET['id'];
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM complaints WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $user_id]);
$complaint = $stmt->fetch();

if (!$complaint) {
    redirect('/user/complaints.php');
}

include_once '../includes/header.php'; 
?>

<div class="container py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="complaints.php" class="btn btn-outline-secondary me-3"><i class="fas fa-arrow-left"></i></a>
        <h3 class="fw-bold mb-0">तक्रार तपशील</h3>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="badge bg-primary px-3 py-2"><?php echo ucfirst($complaint['category']); ?></span>
                    <span class="text-muted small"><i class="far fa-calendar-alt me-1"></i> नोंदणी दिनांक: <?php echo formatDate($complaint['created_at']); ?></span>
                </div>
                
                <h4 class="fw-bold mb-3"><?php echo $complaint['title']; ?></h4>
                <p class="mb-4 text-muted" style="white-space: pre-line;"><?php echo $complaint['description']; ?></p>
                
                <?php if($complaint['image_path']): ?>
                    <h6 class="fw-bold mb-3">अपलोड केलेला फोटो:</h6>
                    <img src="../uploads/complaints/<?php echo $complaint['image_path']; ?>" class="img-fluid rounded border mb-4" style="max-height: 500px;">
                <?php endif; ?>
            </div>

            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold mb-4">तक्रार ट्रॅकिंग (Timeline)</h5>
                <div class="timeline">
                    <!-- Step 1 -->
                    <div class="d-flex mb-4">
                        <div class="me-3">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                <i class="fas fa-check small"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">तक्रार प्राप्त झाली</h6>
                            <p class="text-muted small mb-0"><?php echo formatDate($complaint['created_at']); ?> - तुमची तक्रार आमच्या सिस्टममध्ये यशस्वीरित्या नोंदवली गेली आहे.</p>
                        </div>
                    </div>

                    <!-- Step 2: In Progress -->
                    <?php if($complaint['status'] != 'pending'): ?>
                        <div class="d-flex mb-4">
                            <div class="me-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                    <i class="fas fa-spinner fa-spin small"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">कार्यवाही सुरू आहे</h6>
                                <p class="text-muted small mb-0">अधिकारी तुमच्या तक्रारीवर काम करत आहेत.</p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Step 3: Resolved/Rejected -->
                    <?php if($complaint['status'] == 'resolved' || $complaint['status'] == 'rejected'): ?>
                        <div class="d-flex">
                            <div class="me-3">
                                <div class="<?php echo $complaint['status'] == 'resolved' ? 'bg-success' : 'bg-danger'; ?> text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                    <i class="fas <?php echo $complaint['status'] == 'resolved' ? 'fa-check-double' : 'fa-times'; ?> small"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1"><?php echo $complaint['status'] == 'resolved' ? 'निवारण झाले' : 'नाकारली गेली'; ?></h6>
                                <p class="text-muted small mb-0"><?php echo $complaint['admin_remark'] ? $complaint['admin_remark'] : 'तक्रारीवर आवश्यक ती कार्यवाही पूर्ण करण्यात आली आहे.'; ?></p>
                            </div>
                        </div>
                    <?php else: ?>
                         <div class="d-flex opacity-50">
                            <div class="me-3">
                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                    <i class="fas fa-flag small"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">अंतिम निर्णय</h6>
                                <p class="text-muted small mb-0">पुढील अपडेटची प्रतीक्षा करा.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 sticky-top" style="top: 100px;">
                <h5 class="fw-bold mb-4">सद्यस्थिती</h5>
                <div class="mb-4">
                    <?php 
                    $status_info = [
                        'pending' => ['class' => 'bg-warning', 'text' => 'प्रलंबित (Pending)', 'icon' => 'fa-clock'],
                        'in-progress' => ['class' => 'bg-info', 'text' => 'प्रक्रियेत (In Progress)', 'icon' => 'fa-spinner fa-spin'],
                        'resolved' => ['class' => 'bg-success', 'text' => 'निकाली (Resolved)', 'icon' => 'fa-check-circle'],
                        'rejected' => ['class' => 'bg-danger', 'text' => 'नाकारली (Rejected)', 'icon' => 'fa-times-circle']
                    ][$complaint['status']];
                    ?>
                    <div class="badge <?php echo $status_info['class']; ?> w-100 py-3 mb-3 h5">
                        <i class="fas <?php echo $status_info['icon']; ?> me-2"></i> <?php echo $status_info['text']; ?>
                    </div>
                </div>
                
                <div class="alert alert-info border-0 mb-0">
                    <small><i class="fas fa-info-circle me-1"></i> तुमच्या तक्रारीवर अपडेट मिळाल्यास तुम्हाला येथे कळवले जाईल.</small>
                </div>

                <hr>
                <button onclick="window.print()" class="btn btn-outline-secondary w-100 mb-2">
                    <i class="fas fa-print me-2"></i> पावती डाऊनलोड करा
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 0;
}
.timeline::before {
    content: '';
    position: absolute;
    left: 17px;
    top: 5px;
    height: calc(100% - 30px);
    width: 2px;
    background: #e9ecef;
}
</style>

<?php include_once '../includes/footer.php'; ?>
