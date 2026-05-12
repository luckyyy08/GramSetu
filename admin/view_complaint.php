<?php 
require_once '../config/init.php';

if (!isAdmin()) {
    redirect('/login.php');
}

if (!isset($_GET['id'])) {
    redirect('/admin/dashboard.php');
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT c.*, u.full_name, u.phone FROM complaints c JOIN users u ON c.user_id = u.id WHERE c.id = ?");
$stmt->execute([$id]);
$complaint = $stmt->fetch();

if (!$complaint) {
    redirect('/admin/dashboard.php');
}

// Handle Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $status = $_POST['status'];
    $remark = sanitize($_POST['admin_remark']);

    $stmt = $pdo->prepare("UPDATE complaints SET status = ?, admin_remark = ? WHERE id = ?");
    if ($stmt->execute([$status, $remark, $id])) {
        setFlash('success', 'तक्रारीची स्थिती अपडेट झाली आहे.');
        redirect("/admin/view_complaint.php?id=$id");
    }
}

include_once '../includes/header.php'; 
?>

<div class="container py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="dashboard.php" class="btn btn-outline-secondary me-3"><i class="fas fa-arrow-left"></i></a>
        <h3 class="fw-bold mb-0">तक्रार तपशील</h3>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card p-4 border-0 shadow-sm mb-4">
                <div class="d-flex justify-content-between mb-3">
                    <span class="badge bg-primary"><?php echo $complaint['category']; ?></span>
                    <span class="text-muted small"><?php echo formatDate($complaint['created_at']); ?></span>
                </div>
                <h4 class="fw-bold mb-3"><?php echo $complaint['title']; ?></h4>
                <p class="mb-4"><?php echo nl2br($complaint['description']); ?></p>
                
                <?php if($complaint['image']): ?>
                    <h6 class="fw-bold mb-3">संलग्न फोटो:</h6>
                    <img src="../uploads/complaints/<?php echo $complaint['image']; ?>" class="img-fluid rounded mb-4 border shadow-sm" style="max-height: 500px; width: 100%; object-fit: contain; background: #f8f9fa;">
                <?php endif; ?>

                <hr>
                <div class="row mb-4">
                    <div class="col-12">
                        <?php 
                        require_once '../config/ai_analysis.php';
                        $ai = analyzeComplaint($complaint['description']);
                        ?>
                        <div class="card bg-primary bg-opacity-10 border-0 rounded-4 p-3 border-start border-5 border-primary">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle p-2 me-3">
                                    <i class="fas fa-robot"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-primary">Smart AI विश्लेषण (AI Insights)</h6>
                                    <p class="small mb-0 text-dark">
                                        ही तक्रार <strong><?php echo $ai['category']; ?></strong> शी संबंधित असून तिची गंभीरता <strong><?php echo $ai['urgency']; ?></strong> वाटत आहे. (AI अचूकता: <?php echo $ai['ai_score']; ?>)
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted small mb-1">तक्रारदार:</h6>
                        <p class="fw-bold"><?php echo $complaint['full_name']; ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted small mb-1">मोबाईल नंबर:</h6>
                        <p class="fw-bold"><?php echo $complaint['phone']; ?></p>
                    </div>
                </div>
                <div class="mt-4">
                    <h6 class="fw-bold small text-muted mb-3">कार्यवाही (Actions)</h6>
                    <div class="d-grid gap-2">
                        <?php 
                        $wa_msg = "नमस्कार " . $complaint['full_name'] . ", तुमच्या '" . $complaint['title'] . "' या तक्रारीवर ग्रामपंचायतीने कार्यवाही केली आहे. सध्याची स्थिती: " . $complaint['status'] . ". अधिक माहितीसाठी ग्रामसेतू ॲप पहा.";
                        $wa_link = "https://wa.me/91" . $complaint['phone'] . "?text=" . urlencode($wa_msg);
                        ?>
                        <a href="<?php echo $wa_link; ?>" target="_blank" class="btn btn-success py-2 fw-bold">
                            <i class="fab fa-whatsapp me-2"></i> WhatsApp वर अपडेट पाठवा
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-4 border-0 shadow-sm">
                <h5 class="fw-bold mb-4">स्थिती अपडेट करा</h5>
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label">वर्तमान स्थिती:</label>
                        <?php 
                        $status_class = [
                            'pending' => 'bg-warning',
                            'in-progress' => 'bg-info',
                            'resolved' => 'bg-success',
                            'rejected' => 'bg-danger'
                        ][$complaint['status']];
                        ?>
                        <span class="badge <?php echo $status_class; ?> d-block py-2"><?php echo $complaint['status']; ?></span>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">नवीन स्थिती निवडा</label>
                        <select name="status" class="form-select">
                            <option value="pending" <?php echo $complaint['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="in-progress" <?php echo $complaint['status'] == 'in-progress' ? 'selected' : ''; ?>>In Progress</option>
                            <option value="resolved" <?php echo $complaint['status'] == 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                            <option value="rejected" <?php echo $complaint['status'] == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">प्रशासकीय टिप्पणी (Remark)</label>
                        <textarea name="admin_remark" class="form-control" rows="3"><?php echo $complaint['admin_remark']; ?></textarea>
                    </div>

                    <button type="submit" name="update_status" class="btn btn-primary w-100">अपडेट करा</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>
