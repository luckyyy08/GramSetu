<?php 
require_once '../config/init.php';

if (!isLoggedIn()) {
    redirect('/login.php');
}

$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$category = isset($_GET['category']) ? sanitize($_GET['category']) : '';

$query = "SELECT * FROM schemes WHERE 1=1";
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

$query .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$schemes = $stmt->fetchAll();

include_once '../includes/header.php'; 
?>

<div class="container-fluid py-4 px-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h3 class="fw-bold mb-0 text-primary">सरकारी योजना</h3>
            <p class="text-muted small mb-0">गावासाठी उपलब्ध असलेल्या सर्व कल्याणकारी योजना.</p>
        </div>
        <div class="col-md-6 mt-3 mt-md-0">
            <form action="" method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control shadow-sm border-0 px-3" placeholder="योजना शोधा..." value="<?php echo $search; ?>">
                <select name="category" class="form-select shadow-sm border-0" onchange="this.form.submit()">
                    <option value="">सर्व विभाग</option>
                    <option value="कृषी" <?php echo $category == 'कृषी' ? 'selected' : ''; ?>>कृषी</option>
                    <option value="शिक्षण" <?php echo $category == 'शिक्षण' ? 'selected' : ''; ?>>शिक्षण</option>
                    <option value="आरोग्य" <?php echo $category == 'आरोग्य' ? 'selected' : ''; ?>>आरोग्य</option>
                    <option value="समाजकल्याण" <?php echo $category == 'समाजकल्याण' ? 'selected' : ''; ?>>समाजकल्याण</option>
                </select>
                <button type="submit" class="btn btn-primary shadow-sm"><i class="fas fa-search"></i></button>
            </form>
        </div>
    </div>

    <div class="row">
        <?php if (empty($schemes)): ?>
            <div class="col-12 text-center py-5">
                <div class="mb-3"><i class="fas fa-folder-open fa-4x text-muted"></i></div>
                <h5>सध्या कोणतीही योजना उपलब्ध नाही.</h5>
            </div>
        <?php else: ?>
            <?php foreach($schemes as $scheme): ?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="badge bg-success bg-opacity-10 text-success">सक्रिय</span>
                                <small class="text-danger fw-bold"><i class="fas fa-clock me-1"></i> अंतिम तारीख: <?php echo $scheme['deadline'] ? formatDate($scheme['deadline']) : 'अद्याप ठरली नाही'; ?></small>
                            </div>
                            <h5 class="fw-bold mb-3"><?php echo $scheme['title']; ?></h5>
                            <p class="text-muted small text-truncate-3"><?php echo $scheme['description']; ?></p>
                            
                            <hr>
                            <div class="mb-3">
                                <h6 class="fw-bold small mb-1">पात्रता:</h6>
                                <p class="text-muted small mb-0"><?php echo $scheme['eligibility'] ? $scheme['eligibility'] : 'अधिक माहितीसाठी तपशील पहा'; ?></p>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <a href="view_scheme.php?id=<?php echo $scheme['id']; ?>" class="btn btn-outline-primary btn-sm px-4">तपशील पहा</a>
                                <?php if($scheme['link']): ?>
                                    <a href="<?php echo $scheme['link']; ?>" target="_blank" class="btn btn-primary btn-sm px-4">अर्ज करा <i class="fas fa-external-link-alt ms-1 small"></i></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>
