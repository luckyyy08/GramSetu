<?php 
require_once '../config/init.php';

if (!isAdmin()) {
    redirect('/login.php');
}

$success = '';
$error = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM jobs WHERE id = ?");
    if ($stmt->execute([$id])) {
        $success = "जाहिरात यशस्वीरित्या हटवली गेली आहे.";
    }
}

// Get all jobs
$jobs = $pdo->query("SELECT j.*, u.full_name as posted_by_name FROM jobs j JOIN users u ON j.posted_by = u.id ORDER BY j.created_at DESC")->fetchAll();

include_once '../includes/header.php'; 
?>

<div class="container-fluid py-4 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary">जॉब बोर्ड व्यवस्थापन</h3>
        <div class="badge bg-primary px-3 py-2">एकूण जाहिराती: <?php echo count($jobs); ?></div>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">कामाचे शीर्षक</th>
                        <th>प्रकार</th>
                        <th>पगार/मजुरी</th>
                        <th>कोणी पोस्ट केली</th>
                        <th>तारीख</th>
                        <th>स्थिती</th>
                        <th class="text-end pe-4">कृती</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($jobs)): ?>
                        <tr><td colspan="7" class="text-center py-5">अद्याप कोणतीही कामाची जाहिरात नाही.</td></tr>
                    <?php else: ?>
                        <?php foreach($jobs as $j): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold"><?php echo $j['title']; ?></div>
                                    <small class="text-muted"><?php echo substr($j['description'], 0, 50) . '...'; ?></small>
                                </td>
                                <td><span class="badge bg-light text-dark"><?php echo $j['job_type']; ?></span></td>
                                <td><?php echo $j['wage']; ?></td>
                                <td><?php echo $j['posted_by_name']; ?></td>
                                <td><?php echo formatDate($j['created_at']); ?></td>
                                <td>
                                    <span class="badge <?php echo $j['status'] == 'active' ? 'bg-success' : 'bg-secondary'; ?>">
                                        <?php echo $j['status'] == 'active' ? 'सुरू' : 'बंद'; ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="?delete=<?php echo $j['id']; ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('ही जाहिरात हटवायची का?')">
                                        <i class="fas fa-trash me-1"></i> हटवा
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>
