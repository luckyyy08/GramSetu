<?php 
require_once '../config/init.php';

if (!isLoggedIn()) {
    redirect('/login.php');
}

$category = isset($_GET['category']) ? sanitize($_GET['category']) : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

$query = "SELECT * FROM businesses WHERE 1=1";
$params = [];

if ($category) {
    $query .= " AND category = ?";
    $params[] = $category;
}

if ($search) {
    $query .= " AND (business_name LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " ORDER BY is_verified DESC, created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$businesses = $stmt->fetchAll();

include_once '../includes/header.php'; 
?>

<div class="container-fluid py-4 px-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h3 class="fw-bold mb-0 text-primary">गावची व्यापारी डिरेक्टरी</h3>
            <p class="text-muted small">आपल्या गावातील सर्व दुकाने आणि सेवांची अधिकृत यादी.</p>
        </div>
        <div class="col-md-6 text-md-end">
            <button class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#addBusinessModal">+ माझा व्यवसाय जोडा</button>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="card p-3 border-0 shadow-sm rounded-4 mb-4">
        <form action="" method="GET" class="row g-2">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control bg-light border-0 py-2" placeholder="दुकानाचे किंवा सेवेचे नाव..." value="<?php echo $search; ?>">
            </div>
            <div class="col-md-4">
                <select name="category" class="form-select bg-light border-0 py-2" onchange="this.form.submit()">
                    <option value="">सर्व प्रकार</option>
                    <option value="दुकान" <?php echo $category == 'दुकान' ? 'selected' : ''; ?>>दुकाने (Shops)</option>
                    <option value="सेवा (Services)" <?php echo $category == 'सेवा (Services)' ? 'selected' : ''; ?>>सेवा (Services)</option>
                    <option value="दवाखाना" <?php echo $category == 'दवाखाना' ? 'selected' : ''; ?>>दवाखाना (Clinic)</option>
                    <option value="हॉटेल" <?php echo $category == 'हॉटेल' ? 'selected' : ''; ?>>हॉटेल (Hotel)</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100 py-2">शोधा</button>
            </div>
        </form>
    </div>

    <!-- Business List -->
    <div class="row">
        <?php if (empty($businesses)): ?>
            <div class="col-12 text-center py-5">
                <i class="fas fa-search fa-4x text-light mb-3"></i>
                <h5 class="text-muted">कोणताही व्यवसाय सापडला नाही.</h5>
            </div>
        <?php else: ?>
            <?php foreach($businesses as $b): ?>
                <div class="col-md-4 mb-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="badge bg-primary bg-opacity-10 text-primary small"><?php echo $b['category']; ?></span>
                            <?php if($b['is_verified']): ?>
                                <span class="badge bg-success small"><i class="fas fa-check-circle me-1"></i>अधिकृत</span>
                            <?php endif; ?>
                        </div>
                        <h5 class="fw-bold mb-1 text-dark"><?php echo $b['business_name']; ?></h5>
                        <p class="text-muted small mb-3"><?php echo $b['description']; ?></p>
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-light p-2 rounded-3 me-3">
                                <i class="fas fa-map-marker-alt text-danger"></i>
                            </div>
                            <small class="text-muted"><?php echo $b['address']; ?></small>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <a href="tel:<?php echo $b['contact_number']; ?>" class="btn btn-primary w-100 py-2 rounded-pill fw-bold">
                                    <i class="fas fa-phone-alt me-1"></i> कॉल करा
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="https://wa.me/91<?php echo $b['contact_number']; ?>" target="_blank" class="btn btn-outline-success w-100 py-2 rounded-pill fw-bold">
                                    <i class="fab fa-whatsapp me-1"></i> मेसेज
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Add Business Modal -->
<div class="modal fade" id="addBusinessModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="add_business_action.php" method="POST">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold">तुमचा व्यवसाय नोंदवा</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">व्यवसायाचे/दुकानाचे नाव</label>
                        <input type="text" name="business_name" class="form-control bg-light border-0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">कॅटेगरी</label>
                        <select name="category" class="form-select bg-light border-0" required>
                            <option value="दुकान">दुकान</option>
                            <option value="सेवा (Services)">सेवा (Services)</option>
                            <option value="दवाखाना">दवाखाना</option>
                            <option value="हॉटेल">हॉटेल</option>
                            <option value="शिक्षण">शिक्षण</option>
                            <option value="इतर">इतर</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">संपर्क नंबर</label>
                        <input type="text" name="contact_number" class="form-control bg-light border-0" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold small">पत्ता व थोडक्यात माहिती</label>
                        <textarea name="description" class="form-control bg-light border-0" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">नोंदणी करा</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>
