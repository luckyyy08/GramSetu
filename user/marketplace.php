<?php 
require_once '../config/init.php';

if (!isLoggedIn()) {
    redirect('/login.php');
}

$category = isset($_GET['category']) ? sanitize($_GET['category']) : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

$query = "SELECT p.*, u.full_name as seller_name, u.phone as seller_phone 
          FROM products p 
          JOIN users u ON p.seller_id = u.id 
          WHERE p.status = 'available'";
$params = [];

if ($category) {
    $query .= " AND p.category = ?";
    $params[] = $category;
}

if ($search) {
    $query .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " ORDER BY p.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

include_once '../includes/header.php'; 
?>

<div class="container-fluid py-4 px-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h3 class="fw-bold mb-0 text-primary">गावची बाजारपेठ</h3>
            <p class="text-muted small mb-0">आमच्या गावातील महिलांनी बनवलेल्या शुद्ध आणि घरगुती वस्तू.</p>
        </div>
        <div class="col-md-6 mt-3 mt-md-0 text-md-end">
            <a href="my_products.php" class="btn btn-outline-primary px-4 fw-bold">+ माझी वस्तू विका</a>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card p-3 border-0 shadow-sm rounded-4">
                <form action="" method="GET" class="row g-2">
                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control bg-light border-0 py-2" placeholder="वस्तूचे नाव शोधा..." value="<?php echo $search; ?>">
                    </div>
                    <div class="col-md-4">
                        <select name="category" class="form-select bg-light border-0 py-2" onchange="this.form.submit()">
                            <option value="">सर्व प्रकार</option>
                            <option value="खाद्यपदार्थ" <?php echo $category == 'खाद्यपदार्थ' ? 'selected' : ''; ?>>खाद्यपदार्थ (Food)</option>
                            <option value="शिलाई काम" <?php echo $category == 'शिलाई काम' ? 'selected' : ''; ?>>शिलाई काम (Tailoring)</option>
                            <option value="हस्तकला" <?php echo $category == 'हस्तकला' ? 'selected' : ''; ?>>हस्तकला (Handicrafts)</option>
                            <option value="दुग्धजन्य" <?php echo $category == 'दुग्धजन्य' ? 'selected' : ''; ?>>दुग्धजन्य (Dairy)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">शोधा</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="row">
        <?php if (empty($products)): ?>
            <div class="col-12 text-center py-5">
                <i class="fas fa-shopping-basket fa-4x text-light mb-3"></i>
                <h5 class="text-muted">सध्या बाजारपेठेत कोणतीही वस्तू उपलब्ध नाही.</h5>
            </div>
        <?php else: ?>
            <?php foreach($products as $p): ?>
                <div class="col-md-3 mb-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                        <?php if($p['image']): ?>
                            <img src="../uploads/products/<?php echo $p['image']; ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="fas fa-image fa-3x text-muted opacity-20"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-body p-4">
                            <span class="badge bg-primary bg-opacity-10 text-primary small mb-2"><?php echo $p['category']; ?></span>
                            <h5 class="fw-bold mb-1"><?php echo $p['name']; ?></h5>
                            <p class="text-muted small text-truncate-2 mb-3"><?php echo $p['description']; ?></p>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="fw-bold text-dark mb-0">₹<?php echo $p['price']; ?></h4>
                                <small class="text-muted">विक्रेती: <?php echo $p['seller_name']; ?></small>
                            </div>

                            <?php 
                            $wa_msg = "नमस्कार, मला ग्रामसेतू बाजारपेठेतून तुमची '" . $p['name'] . "' ही वस्तू खरेदी करायची आहे. कृपया अधिक माहिती सांगा.";
                            $wa_link = "https://wa.me/91" . $p['seller_phone'] . "?text=" . urlencode($wa_msg);
                            ?>
                            <a href="<?php echo $wa_link; ?>" target="_blank" class="btn btn-success w-100 py-2 fw-bold rounded-pill">
                                <i class="fab fa-whatsapp me-2"></i> ऑर्डर करा
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>
