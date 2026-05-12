<?php 
require_once '../config/init.php';

if (!isAdmin()) {
    redirect('/login.php');
}

// Handle Delete User
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    // Don't delete the current admin
    if ($id != $_SESSION['user_id']) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        setFlash('success', 'वापरकर्ता यशस्वीरित्या हटवला गेला.');
    } else {
        setFlash('danger', 'तुम्ही स्वतःचे खाते हटवू शकत नाही.');
    }
    redirect('/admin/users.php');
}

$users = $pdo->query("SELECT * FROM users WHERE role = 'user' ORDER BY created_at DESC")->fetchAll();

include_once '../includes/header.php'; 
?>

<div class="container-fluid py-4 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">गावातील नागरिक (वापरकर्ते)</h3>
        <div class="badge bg-primary px-3 py-2">एकूण नागरिक: <?php echo count($users); ?></div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">फोटो</th>
                        <th>पूर्ण नाव</th>
                        <th>मोबाईल नंबर</th>
                        <th>पत्ता</th>
                        <th>नोंदणी तारीख</th>
                        <th class="text-end pe-4">कृती</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr><td colspan="6" class="text-center py-5">अद्याप कोणत्याही नागरिकाने नोंदणी केली नाही.</td></tr>
                    <?php else: ?>
                        <?php foreach($users as $u): ?>
                            <tr>
                                <td class="ps-4">
                                    <img src="../uploads/<?php echo $u['profile_pic']; ?>" class="rounded-circle border" width="40" height="40" style="object-fit: cover;">
                                </td>
                                <td><div class="fw-bold"><?php echo $u['full_name']; ?></div></td>
                                <td><?php echo $u['phone']; ?></td>
                                <td><small class="text-muted"><?php echo $u['address'] ? $u['address'] : '---'; ?></small></td>
                                <td><?php echo formatDate($u['created_at']); ?></td>
                                <td class="text-end pe-4">
                                    <a href="?delete=<?php echo $u['id']; ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('हे खाते हटवायचे आहे का?')">
                                        <i class="fas fa-trash-alt me-1"></i> हटवा
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
