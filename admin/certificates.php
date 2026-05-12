<?php 
require_once '../config/init.php';

if (!isAdmin()) {
    redirect('/login.php');
}

// Handle Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id = $_POST['request_id'];
    $status = $_POST['status'];
    $remark = sanitize($_POST['admin_remark']);

    $stmt = $pdo->prepare("UPDATE certificates SET status = ?, admin_remark = ? WHERE id = ?");
    if ($stmt->execute([$status, $remark, $id])) {
        setFlash('success', 'दाखल्याचा अर्ज यशस्वीरित्या अपडेट झाला आहे.');
    } else {
        setFlash('danger', 'अपडेट करताना त्रुटी आली.');
    }
    redirect('/admin/certificates.php');
}

$requests = $pdo->query("SELECT c.*, u.full_name, u.phone FROM certificates c JOIN users u ON c.user_id = u.id ORDER BY applied_at DESC")->fetchAll();

include_once '../includes/header.php'; 
?>

<div class="container-fluid py-4 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">दाखला अर्ज व्यवस्थापन</h3>
        <div class="badge bg-primary px-3 py-2">एकूण अर्ज: <?php echo count($requests); ?></div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light py-3">
                    <tr>
                        <th class="ps-4">नागरिक</th>
                        <th>दाखल्याचा प्रकार</th>
                        <th>कारण</th>
                        <th>तारीख</th>
                        <th>स्थिती</th>
                        <th class="text-end pe-4">कृती</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($requests)): ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted">कोणताही अर्ज उपलब्ध नाही.</td></tr>
                    <?php else: ?>
                        <?php foreach($requests as $r): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold"><?php echo $r['full_name']; ?></div>
                                    <small class="text-muted"><?php echo $r['phone']; ?></small>
                                </td>
                                <td><span class="badge bg-light text-dark"><?php echo $r['certificate_type']; ?></span></td>
                                <td><small class="text-muted"><?php echo $r['reason']; ?></small></td>
                                <td><?php echo formatDate($r['applied_at']); ?></td>
                                <td>
                                    <?php 
                                    $s_class = ['pending' => 'bg-warning', 'approved' => 'bg-success', 'rejected' => 'bg-danger'][$r['status']];
                                    $s_mr = ['pending' => 'प्रलंबित', 'approved' => 'मंजूर', 'rejected' => 'नाकारला'][$r['status']];
                                    ?>
                                    <span class="badge <?php echo $s_class; ?>"><?php echo $s_mr; ?></span>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#certModal<?php echo $r['id']; ?>">स्थिती बदला</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modals Section outside table for better performance -->
    <?php foreach($requests as $r): ?>
        <div class="modal fade" id="certModal<?php echo $r['id']; ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content border-0 shadow rounded-4">
                    <form action="" method="POST">
                        <div class="modal-header bg-light">
                            <h5 class="modal-title fw-bold">अर्जाची स्थिती बदला</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <input type="hidden" name="request_id" value="<?php echo $r['id']; ?>">
                            <div class="mb-3">
                                <label class="form-label fw-bold small">स्थिती निवडा</label>
                                <select name="status" class="form-select bg-light border-0">
                                    <option value="pending" <?php echo $r['status'] == 'pending' ? 'selected' : ''; ?>>प्रलंबित (Pending)</option>
                                    <option value="approved" <?php echo $r['status'] == 'approved' ? 'selected' : ''; ?>>मंजूर (Approved)</option>
                                    <option value="rejected" <?php echo $r['status'] == 'rejected' ? 'selected' : ''; ?>>नाकारला (Rejected)</option>
                                </select>
                            </div>
                            <div class="mb-0">
                                <label class="form-label fw-bold small">प्रशासकीय शेरा (Admin Remark)</label>
                                <textarea name="admin_remark" class="form-control bg-light border-0" rows="3" placeholder="दाखला तयार आहे का? किंवा का नाकारला?"><?php echo $r['admin_remark']; ?></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-0 p-4 pt-0">
                            <button type="submit" name="update_status" class="btn btn-primary w-100 py-2 fw-bold">अपडेट करा</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php include_once '../includes/footer.php'; ?>
