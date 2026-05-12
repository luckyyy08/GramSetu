<?php 
require_once '../config/init.php';

if (!isLoggedIn()) {
    redirect('/login.php');
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Handle New Job Post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_job'])) {
    $title = sanitize($_POST['title']);
    $type = $_POST['job_type'];
    $desc = sanitize($_POST['description']);
    $wage = sanitize($_POST['wage']);
    $contact = sanitize($_POST['contact_info']);

    $stmt = $pdo->prepare("INSERT INTO jobs (posted_by, title, job_type, description, wage, contact_info) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$user_id, $title, $type, $desc, $wage, $contact])) {
        $success = "कामाची जाहिरात यशस्वीरित्या प्रसिद्ध झाली आहे.";
    }
}

$active_jobs = $pdo->query("SELECT j.*, u.full_name FROM jobs j JOIN users u ON j.posted_by = u.id WHERE j.status = 'active' ORDER BY j.created_at DESC")->fetchAll();

include_once '../includes/header.php'; 
?>

<div class="container-fluid py-4 px-4">
    <div class="row">
        <!-- Post Job Form -->
        <div class="col-md-4 mb-4">
            <div class="card p-4 border-0 shadow-sm rounded-4">
                <h4 class="fw-bold mb-4">कामाची जाहिरात द्या</h4>
                
                <?php if ($success): ?>
                    <div class="alert alert-success small"><?php echo $success; ?></div>
                <?php endif; ?>

                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">कामाचे शीर्षक</label>
                        <input type="text" name="title" class="form-control bg-light border-0" placeholder="उदा. कापूस वेचण्यासाठी मजूर हवेत" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">कामाचा प्रकार</label>
                        <select name="job_type" class="form-select bg-light border-0" required>
                            <option value="शेतीकाम">शेतीकाम</option>
                            <option value="दुकान">दुकान</option>
                            <option value="मजुरी">मजुरी</option>
                            <option value="शिकाऊ काम">शिकाऊ काम (Internship)</option>
                            <option value="इतर">इतर</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">रोजंदारी / पगार (₹)</label>
                        <input type="text" name="wage" class="form-control bg-light border-0" placeholder="उदा. 400 प्रति दिवस" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">कामाची माहिती</label>
                        <textarea name="description" class="form-control bg-light border-0" rows="3" placeholder="वेळ आणि इतर अटी सांगा..." required></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold">संपर्क नंबर</label>
                        <input type="text" name="contact_info" class="form-control bg-light border-0" value="<?php echo $_SESSION['phone'] ?? ''; ?>" required>
                    </div>
                    <button type="submit" name="post_job" class="btn btn-primary w-100 py-2 fw-bold">जाहिरात प्रसिद्ध करा</button>
                </form>
            </div>
        </div>

        <!-- Jobs List -->
        <div class="col-md-8">
            <div class="card p-4 border-0 shadow-sm rounded-4">
                <h4 class="fw-bold mb-4">उपलब्ध कामाच्या संधी</h4>
                <div class="row">
                    <?php if (empty($active_jobs)): ?>
                        <div class="col-12 text-center py-5">
                            <i class="fas fa-briefcase fa-4x text-light mb-3"></i>
                            <h5 class="text-muted">सध्या कोणतीही कामाची संधी उपलब्ध नाही.</h5>
                        </div>
                    <?php else: ?>
                        <?php foreach($active_jobs as $j): ?>
                            <div class="col-md-12 mb-3">
                                <div class="card bg-light border-0 p-3 rounded-4">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <span class="badge bg-primary bg-opacity-10 text-primary small mb-2"><?php echo $j['job_type']; ?></span>
                                            <h5 class="fw-bold mb-1"><?php echo $j['title']; ?></h5>
                                            <p class="text-muted small mb-2"><?php echo $j['description']; ?></p>
                                            <div class="small fw-bold text-success mb-2">₹ पगार: <?php echo $j['wage']; ?></div>
                                            <small class="text-muted">दिनांक: <?php echo formatDate($j['created_at']); ?> | पोस्ट केली: <?php echo $j['full_name']; ?></small>
                                        </div>
                                        <a href="https://wa.me/91<?php echo $j['contact_info']; ?>?text=नमस्ते, मला तुमच्या '<?php echo $j['title']; ?>' या कामाबद्दल विचारायचे आहे." target="_blank" class="btn btn-success btn-sm rounded-pill px-3">
                                            <i class="fab fa-whatsapp me-1"></i> संपर्क करा
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>
