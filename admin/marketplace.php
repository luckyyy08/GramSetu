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
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    if ($stmt->execute([$id])) {
        $success = "वस्तू यशस्वीरित्या हटवली गेली आहे.";
    }
}

// Get all products
$products = $pdo->query("SELECT p.*, u.full_name as seller_name FROM products p JOIN users u ON p.seller_id = u.id ORDER BY p.created_at DESC")->fetchAll();

include_once '../includes/header.php'; 
?>

<div class="container-fluid py-4 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary">बाजारपेठ व्यवस्थापन</h3>
        <div class="badge bg-primary px-3 py-2">एकूण वस्तू: <?php echo count($products); ?></div>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">फोटो</th>
                        <th>वस्तूचे नाव</th>
                        <th>विक्रेती</th>
                        <th>कॅटेगरी</th>
                        <th>किंमत</th>
                        <th>तारीख</th>
                        <th class="text-end pe-4">कृती</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr><td colspan="7" class="text-center py-5">बाजारपेठेत अद्याप कोणतीही वस्तू नाही.</td></tr>
                    <?php else: ?>
                        <?php foreach($products as $p): ?>
                            <tr>
                                <td class="ps-4">
                                    <?php if($p['image']): ?>
                                        <img src="../uploads/products/<?php echo $p['image']; ?>" class="rounded-3" width="50" height="50" style="object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><div class="fw-bold"><?php echo $p['name']; ?></div></td>
                                <td><?php echo $p['seller_name']; ?></td>
                                <td><span class="badge bg-light text-dark"><?php echo $p['category']; ?></span></td>
                                <td class="fw-bold">₹<?php echo $p['price']; ?></td>
                                <td><small class="text-muted"><?php echo formatDate($p['created_at']); ?></small></td>
                                <td class="text-end pe-4">
                                    <a href="?delete=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('ही वस्तू हटवायची का?')">
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
