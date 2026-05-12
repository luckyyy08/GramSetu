<?php 
require_once '../config/init.php';

if (!isLoggedIn()) {
    redirect('/login.php');
}

if (isAdmin()) {
    redirect('/admin/dashboard.php');
}

$user_id = $_SESSION['user_id'];

// Get user stats
$stmt = $pdo->prepare("SELECT COUNT(*) FROM complaints WHERE user_id = ?");
$stmt->execute([$user_id]);
$total_complaints = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM complaints WHERE user_id = ? AND status = 'resolved'");
$stmt->execute([$user_id]);
$resolved_complaints = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM schemes");
$total_schemes = $stmt->fetchColumn();

include_once '../includes/header.php'; 
?>

<div class="container-fluid py-4 px-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 mb-4">
            <div class="card p-3 border-0 shadow-sm rounded-4">
                <div class="text-center mb-4 pt-3">
                    <?php 
                    $stmt = $pdo->prepare("SELECT profile_pic FROM users WHERE id = ?");
                    $stmt->execute([$user_id]);
                    $p_pic = $stmt->fetchColumn();
                    $p_pic_path = ($p_pic && $p_pic != 'default.png') ? '../uploads/'.$p_pic : '../assets/img/default.png';
                    ?>
                    <img src="<?php echo $p_pic_path; ?>" class="rounded-circle border p-1 mb-3 shadow-sm" width="100" height="100" style="object-fit: cover;">
                    <h5 class="fw-bold mb-1"><?php echo $_SESSION['full_name']; ?></h5>
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">नागरिक</span>
                </div>
                <hr class="mx-3 opacity-50">
                <div class="list-group list-group-flush px-2">
                    <a href="dashboard.php" class="list-group-item list-group-item-action active border-0 rounded-3 mb-2 py-3">
                        डॅशबोर्ड
                    </a>
                    <a href="complaints.php" class="list-group-item list-group-item-action border-0 rounded-3 mb-2 py-3">
                        <i class="fas fa-edit me-2"></i> माझ्या तक्रारी
                    </a>
                    <a href="notices.php" class="list-group-item list-group-item-action border-0 rounded-3 mb-2 py-3">
                        <i class="fas fa-bullhorn me-2"></i> सूचना फलक
                    </a>
                    <a href="certificates.php" class="list-group-item list-group-item-action border-0 rounded-3 mb-2 py-3">
                        <i class="fas fa-file-alt me-2"></i> दाखल्यांसाठी अर्ज
                    </a>
                    <a href="polls.php" class="list-group-item list-group-item-action border-0 rounded-3 mb-2 py-3">
                        <i class="fas fa-vote-yea me-2"></i> मतदान / कौल
                    </a>
                    <a href="schemes.php" class="list-group-item list-group-item-action border-0 rounded-3 mb-2 py-3">
                        <i class="fas fa-hand-holding-heart me-2"></i> सरकारी योजना
                    </a>
                    <a href="events.php" class="list-group-item list-group-item-action border-0 rounded-3 mb-2 py-3">
                        <i class="fas fa-calendar-alt me-2"></i> येणारे कार्यक्रम
                    </a>
                    <a href="../emergency.php" class="list-group-item list-group-item-action border-0 rounded-3 mb-2 py-3">
                        <i class="fas fa-phone-alt me-2"></i> आणीबाणी संपर्क
                    </a>
                    <a href="../profile.php" class="list-group-item list-group-item-action border-0 rounded-3 mb-2 py-3">
                        <i class="fas fa-user-cog me-2"></i> प्रोफाइल सेटिंग्ज
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-10">
            <h3 class="fw-bold mb-4">नमस्कार, <?php echo $_SESSION['full_name']; ?>!</h3>
            
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card bg-primary text-white p-3">
                        <h6>एकूण तक्रारी</h6>
                        <h2 class="fw-bold"><?php echo $total_complaints; ?></h2>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card bg-success text-white p-3">
                        <h6>निवारण झाले</h6>
                        <h2 class="fw-bold"><?php echo $resolved_complaints; ?></h2>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card bg-info text-white p-3">
                        <h6>सक्रिय योजना</h6>
                        <h2 class="fw-bold"><?php echo $total_schemes; ?></h2>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-7 mb-4">
                    <div class="card p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold">अलीकडील तक्रारी</h5>
                            <a href="new_complaint.php" class="btn btn-sm btn-primary">+ तक्रार नोंदवा</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>विषय</th>
                                        <th>दिनांक</th>
                                        <th>स्थिती</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $pdo->prepare("SELECT * FROM complaints WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
                                    $stmt->execute([$user_id]);
                                    $complaints = $stmt->fetchAll();
                                    
                                    if (empty($complaints)): ?>
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">कोणतीही तक्रार आढळली नाही.</td>
                                        </tr>
                                    <?php else:
                                        foreach($complaints as $complaint): ?>
                                            <tr>
                                                <td><?php echo $complaint['title']; ?></td>
                                                <td><?php echo formatDate($complaint['created_at']); ?></td>
                                                <td>
                                                    <?php 
                                                    $status_class = [
                                                        'pending' => 'bg-warning',
                                                        'in-progress' => 'bg-info',
                                                        'resolved' => 'bg-success',
                                                        'rejected' => 'bg-danger'
                                                    ][$complaint['status']];
                                                    $status_mr = [
                                                        'pending' => 'प्रलंबित',
                                                        'in-progress' => 'प्रक्रियेत',
                                                        'resolved' => 'निकाली',
                                                        'rejected' => 'नाकारली'
                                                    ][$complaint['status']];
                                                    ?>
                                                    <span class="badge <?php echo $status_class; ?>"><?php echo $status_mr; ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; 
                                    endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 mb-4">
                    <div class="card p-4 h-100 border-0 shadow-sm rounded-4">
                        <h5 class="fw-bold mb-3">महत्वाच्या सूचना</h5>
                        <ul class="list-group list-group-flush">
                            <?php
                            $stmt = $pdo->query("SELECT * FROM notices ORDER BY created_at DESC LIMIT 3");
                            $notices = $stmt->fetchAll();
                            
                            if (empty($notices)): ?>
                                <li class="list-group-item px-0 py-3 border-0">सध्या कोणतीही सूचना नाही.</li>
                            <?php else:
                                foreach($notices as $notice): ?>
                                    <li class="list-group-item px-0 py-3 border-bottom border-light">
                                        <small class="text-primary fw-bold d-block mb-1"><?php echo formatDate($notice['created_at']); ?></small>
                                        <h6 class="fw-bold mb-0"><?php echo $notice['title']; ?></h6>
                                    </li>
                                <?php endforeach; 
                            endif; ?>
                        </ul>
                        <a href="notices.php" class="btn btn-link text-primary p-0 mt-3 text-decoration-none fw-bold">
                            सर्व सूचना पहा <i class="fas fa-arrow-right ms-1 small"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>