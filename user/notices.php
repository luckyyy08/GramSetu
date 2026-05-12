<?php 
require_once '../config/init.php';

if (!isLoggedIn()) {
    redirect('/login.php');
}

// Mark as seen
$u_id = $_SESSION['user_id'];
$pdo->prepare("UPDATE users SET last_notif_seen = NOW() WHERE id = ?")->execute([$u_id]);

// Search & Filter Logic
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$category = isset($_GET['category']) ? sanitize($_GET['category']) : '';

$query = "SELECT * FROM notices WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND (title LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($category) {
    $query .= " AND category = ?";
    $params[] = $category;
}

$query .= " ORDER BY is_important DESC, created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$notices = $stmt->fetchAll();

include_once '../includes/header.php'; 
?>

<div class="container-fluid py-4 px-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h3 class="fw-bold mb-0">ग्रामपंचायत सूचना फलक</h3>
        </div>
        <div class="col-md-6">
            <form action="" method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control shadow-sm border-0 px-3" placeholder="येथे शोधा..." value="<?php echo $search; ?>">
                <select name="category" class="form-select shadow-sm border-0" onchange="this.form.submit()">
                    <option value="">सर्व कॅटेगरी</option>
                    <option value="सामान्य" <?php echo $category == 'सामान्य' ? 'selected' : ''; ?>>सामान्य</option>
                    <option value="पाणी" <?php echo $category == 'पाणी' ? 'selected' : ''; ?>>पाणी पुरवठा</option>
                    <option value="आरोग्य" <?php echo $category == 'आरोग्य' ? 'selected' : ''; ?>>आरोग्य</option>
                    <option value="शेती" <?php echo $category == 'शेती' ? 'selected' : ''; ?>>शेती</option>
                </select>
                <button type="submit" class="btn btn-primary shadow-sm"><i class="fas fa-search"></i></button>
            </form>
        </div>
    </div>

    <div class="row">
        <?php if (empty($notices)): ?>
            <div class="col-12 text-center py-5">
                <h5>सध्या कोणतीही नवीन सूचना नाही.</h5>
            </div>
        <?php else: ?>
            <?php foreach($notices as $notice): ?>
                <div class="col-md-12 mb-3">
                    <div class="card border-0 shadow-sm <?php echo $notice['is_important'] ? 'border-start border-4 border-danger' : 'border-start border-4 border-primary'; ?>">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <span class="badge bg-light text-dark mb-2"><?php echo $notice['category']; ?></span>
                                <small class="text-muted"><?php echo formatDate($notice['created_at']); ?></small>
                            </div>
                            <h5 class="fw-bold <?php echo $notice['is_important'] ? 'text-danger' : ''; ?>">
                                <?php if($notice['is_important']): ?><i class="fas fa-exclamation-triangle me-2"></i><?php endif; ?>
                                <?php echo $notice['title']; ?>
                            </h5>
                            <p class="text-muted mb-0"><?php echo nl2br($notice['content']); ?></p>
                            
                            <div class="d-flex justify-content-end align-items-center mt-3 pt-3 border-top border-light">
                                <button class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="readNotice('<?php echo addslashes($notice['title'] . '. ' . $notice['content']); ?>')">
                                    <i class="fas fa-volume-up me-1"></i> ऐका
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>
