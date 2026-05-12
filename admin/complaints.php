<?php 
require_once '../config/init.php';

if (!isAdmin()) {
    redirect('/login.php');
}

// Mark as seen
$a_id = $_SESSION['user_id'];
$pdo->prepare("UPDATE admins SET last_notif_seen = NOW() WHERE id = ?")->execute([$a_id]);

// Handle Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $complaint_id = $_POST['complaint_id'];
    $status = $_POST['status'];
    $admin_remark = sanitize($_POST['admin_remark']);

    $stmt = $pdo->prepare("UPDATE complaints SET status = ?, admin_remark = ? WHERE id = ?");
    if ($stmt->execute([$status, $admin_remark, $complaint_id])) {
        setFlash('success', 'तक्रारीची स्थिती अपडेट करण्यात आली आहे.');
    } else {
        setFlash('danger', 'अपडेट करताना त्रुटी आली.');
    }
    redirect('/admin/complaints.php');
}

$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$query = "SELECT complaints.*, users.full_name, users.phone 
          FROM complaints 
          JOIN users ON complaints.user_id = users.id";
$params = [];

if ($status_filter) {
    $query .= " WHERE status = ?";
    $params[] = $status_filter;
}

$query .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$complaints = $stmt->fetchAll();

include_once '../includes/header.php'; 
?>

<div class="container-fluid py-4 px-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h3 class="fw-bold mb-0">तक्रार निवारण केंद्र</h3>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <div class="btn-group shadow-sm">
                <a href="complaints.php" class="btn btn-<?php echo !$status_filter ? 'primary' : 'outline-primary'; ?>">सर्व</a>
                <a href="?status=pending" class="btn btn-<?php echo $status_filter == 'pending' ? 'primary' : 'outline-primary'; ?>">प्रलंबित</a>
                <a href="?status=resolved" class="btn btn-<?php echo $status_filter == 'resolved' ? 'primary' : 'outline-primary'; ?>">निकाली</a>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light py-3">
                    <tr>
                        <th class="ps-4">तक्रारदार</th>
                        <th>विषय व प्रवर्ग</th>
                        <th>तारीख</th>
                        <th>स्थिती</th>
                        <th class="text-end pe-4">कृती</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($complaints)): ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">कोणतीही तक्रार आढळली नाही.</td></tr>
                    <?php else: ?>
                        <?php foreach($complaints as $c): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold"><?php echo $c['full_name']; ?></div>
                                    <small class="text-muted"><?php echo $c['phone']; ?></small>
                                </td>
                                <td>
                                    <div class="fw-bold"><?php echo $c['title']; ?></div>
                                    <span class="badge bg-light text-dark small"><?php echo ucfirst($c['category']); ?></span>
                                </td>
                                <td><?php echo formatDate($c['created_at']); ?></td>
                                <td>
                                    <?php 
                                    $s_class = [
                                        'pending' => 'bg-warning',
                                        'in-progress' => 'bg-info',
                                        'resolved' => 'bg-success',
                                        'rejected' => 'bg-danger'
                                    ][$c['status']];
                                    ?>
                                    <span class="badge <?php echo $s_class; ?>"><?php echo ucfirst($c['status']); ?></span>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#updateModal<?php echo $c['id']; ?>">स्थिती बदला</button>
                                    <a href="view_complaint.php?id=<?php echo $c['id']; ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3">पहा</a>
                                </td>
                            </tr>

                            <!-- Status Update Modal -->
                            <div class="modal fade" id="updateModal<?php echo $c['id']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content border-0 shadow rounded-4">
                                        <form action="" method="POST">
                                            <div class="modal-header bg-light">
                                                <h5 class="modal-title fw-bold">स्थिती अपडेट करा</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <input type="hidden" name="complaint_id" value="<?php echo $c['id']; ?>">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold small">नवीन स्थिती निवडा</label>
                                                    <select name="status" class="form-select bg-light border-0 py-2">
                                                        <option value="pending" <?php echo $c['status'] == 'pending' ? 'selected' : ''; ?>>प्रलंबित (Pending)</option>
                                                        <option value="in-progress" <?php echo $c['status'] == 'in-progress' ? 'selected' : ''; ?>>प्रक्रियेत (In Progress)</option>
                                                        <option value="resolved" <?php echo $c['status'] == 'resolved' ? 'selected' : ''; ?>>निकाली (Resolved)</option>
                                                        <option value="rejected" <?php echo $c['status'] == 'rejected' ? 'selected' : ''; ?>>नाकारली (Rejected)</option>
                                                    </select>
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label fw-bold small">प्रशासकीय टिप्पणी (Admin Remark)</label>
                                                    <textarea name="admin_remark" class="form-control bg-light border-0" rows="3" placeholder="तक्रारीवर काय कार्यवाही झाली?"><?php echo $c['admin_remark']; ?></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 p-4 pt-0">
                                                <button type="submit" name="update_status" class="btn btn-primary w-100 py-2 rounded-3 fw-bold">अपडेट जतन करा</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>
