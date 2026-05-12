<?php 
require_once '../config/init.php';

if (!isLoggedIn()) {
    redirect('/login.php');
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Handle New Product Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = sanitize($_POST['name']);
    $desc = sanitize($_POST['description']);
    $price = $_POST['price'];
    $category = $_POST['category'];
    
    $image_name = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image_name = "prod_" . time() . "." . $ext;
        $target = "../uploads/products/" . $image_name;
        
        if (!is_dir('../uploads/products/')) {
            mkdir('../uploads/products/', 0777, true);
        }
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
    }

    $stmt = $pdo->prepare("INSERT INTO products (seller_id, name, description, price, category, image) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$user_id, $name, $desc, $price, $category, $image_name])) {
        $success = "तुमची वस्तू बाजारपेठेत विक्रीसाठी उपलब्ध झाली आहे!";
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ? AND seller_id = ?");
    $stmt->execute([$id, $user_id]);
    redirect('my_products.php');
}

$my_products = $pdo->prepare("SELECT * FROM products WHERE seller_id = ? ORDER BY created_at DESC");
$my_products->execute([$user_id]);
$products = $my_products->fetchAll();

include_once '../includes/header.php'; 
?>

<div class="container-fluid py-4 px-4">
    <div class="row">
        <!-- Add Product Form -->
        <div class="col-md-4 mb-4">
            <div class="card p-4 border-0 shadow-sm rounded-4">
                <h4 class="fw-bold mb-4">नवीन वस्तू जोडा</h4>
                
                <?php if ($success): ?>
                    <div class="alert alert-success small"><?php echo $success; ?></div>
                <?php endif; ?>

                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">वस्तूचे नाव</label>
                        <input type="text" name="name" class="form-control bg-light border-0" placeholder="उदा. घरगुती लोणचे" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">कॅटेगरी</label>
                        <select name="category" class="form-select bg-light border-0" required>
                            <option value="खाद्यपदार्थ">खाद्यपदार्थ</option>
                            <option value="शिलाई काम">शिलाई काम</option>
                            <option value="हस्तकला">हस्तकला</option>
                            <option value="दुग्धजन्य">दुग्धजन्य</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">किंमत (₹)</label>
                        <input type="number" name="price" class="form-control bg-light border-0" placeholder="0.00" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">माहिती</label>
                        <textarea name="description" class="form-control bg-light border-0" rows="3" placeholder="वस्तूची वैशिष्ट्ये सांगा..." required></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold">फोटो</label>
                        <input type="file" name="image" class="form-control bg-light border-0" accept="image/*" required>
                    </div>
                    <button type="submit" name="add_product" class="btn btn-primary w-100 py-2 fw-bold">बाजारपेठेत टाका</button>
                </form>
            </div>
        </div>

        <!-- My Products List -->
        <div class="col-md-8">
            <div class="card p-4 border-0 shadow-sm rounded-4">
                <h4 class="fw-bold mb-4">माझी विक्री यादी</h4>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>फोटो</th>
                                <th>वस्तू</th>
                                <th>किंमत</th>
                                <th>स्थिती</th>
                                <th class="text-end pe-4">कृती</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($products)): ?>
                                <tr><td colspan="5" class="text-center py-4 text-muted">तुम्ही अद्याप कोणतीही वस्तू विक्रीसाठी टाकलेली नाही.</td></tr>
                            <?php else: ?>
                                <?php foreach($products as $p): ?>
                                    <tr>
                                        <td>
                                            <?php if($p['image']): ?>
                                                <img src="../uploads/products/<?php echo $p['image']; ?>" class="rounded-3" width="50" height="50" style="object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="fw-bold"><?php echo $p['name']; ?></div>
                                            <small class="text-muted"><?php echo $p['category']; ?></small>
                                        </td>
                                        <td>₹<?php echo $p['price']; ?></td>
                                        <td><span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3"><?php echo $p['status']; ?></span></td>
                                        <td class="text-end pe-4">
                                            <a href="?delete=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('हटवायचे?')"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>
