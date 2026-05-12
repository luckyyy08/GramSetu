<?php 
require_once '../config/init.php';

if (!isLoggedIn()) {
    redirect('/login.php');
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM complaints WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$complaints = $stmt->fetchAll();

include_once '../includes/header.php'; 
?>

<div class="container-fluid py-4 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">माझ्या तक्रारी</h3>
        <a href="new_complaint.php" class="btn btn-primary">+ नवीन तक्रार</a>
    </div>

    <div class="row">
        <?php if (empty($complaints)): ?>
            <div class="col-12 text-center py-5">
                <div class="mb-3"><i class="fas fa-clipboard-list fa-4x text-muted"></i></div>
                <h5>तुम्ही अद्याप कोणतीही तक्रार नोंदवली नाही.</h5>
            </div>
        <?php else: ?>
            <?php foreach($complaints as $complaint): ?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="badge bg-light text-dark"><?php echo ucfirst($complaint['category']); ?></span>
                                <small class="text-muted"><?php echo formatDate($complaint['created_at']); ?></small>
                            </div>
                            <h5 class="fw-bold"><?php echo $complaint['title']; ?></h5>
                            <p class="text-muted small text-truncate-2"><?php echo $complaint['description']; ?></p>
                            
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    <?php 
                                    $status_class = [
                                        'pending' => 'bg-warning',
                                        'in-progress' => 'bg-info',
                                        'resolved' => 'bg-success',
                                        'rejected' => 'bg-danger'
                                    ][$complaint['status']];
                                    ?>
                                    स्थिती: <span class="badge <?php echo $status_class; ?>"><?php echo $complaint['status']; ?></span>
                                </div>
                                <a href="view_complaint.php?id=<?php echo $complaint['id']; ?>" class="btn btn-sm btn-outline-primary">तपशील पहा</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>
