<?php 
require_once '../config/init.php';

if (!isLoggedIn()) {
    redirect('/login.php');
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Handle Application Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply'])) {
    $type = sanitize($_POST['certificate_type']);
    $reason = sanitize($_POST['reason']);

    if (empty($type) || empty($reason)) {
        $error = "कृपया सर्व माहिती भरा.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO certificates (user_id, certificate_type, reason) VALUES (?, ?, ?)");
        if ($stmt->execute([$user_id, $type, $reason])) {
            $success = "तुमचा अर्ज यशस्वीरित्या सादर झाला आहे.";
        } else {
            $error = "अर्ज करताना त्रुटी आली.";
        }
    }
}

$my_requests = $pdo->prepare("SELECT * FROM certificates WHERE user_id = ? ORDER BY applied_at DESC");
$my_requests->execute([$user_id]);
$requests = $my_requests->fetchAll();

include_once '../includes/header.php'; 
?>

<div class="container py-4">
    <div class="row">
        <!-- Application Form -->
        <div class="col-md-5 mb-4">
            <div class="card p-4 border-0 shadow-sm rounded-4">
                <h4 class="fw-bold mb-4">दाखल्यासाठी अर्ज करा</h4>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">दाखल्याचा प्रकार निवडा</label>
                        <select name="certificate_type" class="form-select bg-light border-0 py-2" required>
                            <option value="">निवडा...</option>
                            <option value="रहिवासी दाखला">रहिवासी दाखला (Resident Certificate)</option>
                            <option value="उत्पन्न दाखला">उत्पन्न दाखला (Income Certificate)</option>
                            <option value="येणे बाकी नसलेला दाखला">येणे बाकी नसलेला दाखला (No Dues)</option>
                            <option value="जन्म नोंद दाखला">जन्म नोंद दाखला (Birth Certificate Copy)</option>
                            <option value="मृत्यू नोंद दाखला">मृत्यू नोंद दाखला (Death Certificate Copy)</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">दाखला कशासाठी हवा आहे? (कारण)</label>
                        <textarea name="reason" class="form-control bg-light border-0" rows="4" placeholder="उदा. शाळेत प्रवेशासाठी, बँक कामासाठी..." required></textarea>
                    </div>
                    <button type="submit" name="apply" class="btn btn-primary w-100 py-2 fw-bold rounded-3">अर्ज सादर करा</button>
                </form>
            </div>
        </div>

        <!-- Request History -->
        <div class="col-md-7">
            <div class="card p-4 border-0 shadow-sm rounded-4">
                <h4 class="fw-bold mb-4">माझे अर्ज</h4>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>दाखला</th>
                                <th>तारीख</th>
                                <th>स्थिती</th>
                                <th>शेरा (Remark)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($requests)): ?>
                                <tr><td colspan="4" class="text-center py-4 text-muted">अद्याप कोणताही अर्ज केलेला नाही.</td></tr>
                            <?php else: ?>
                                <?php foreach($requests as $r): ?>
                                    <tr>
                                        <td><div class="fw-bold"><?php echo $r['certificate_type']; ?></div></td>
                                        <td><small><?php echo formatDate($r['applied_at']); ?></small></td>
                                        <td>
                                            <?php 
                                            $s_class = ['pending' => 'bg-warning', 'approved' => 'bg-success', 'rejected' => 'bg-danger'][$r['status']];
                                            ?>
                                            <span class="badge <?php echo $s_class; ?>"><?php echo ucfirst($r['status']); ?></span>
                                        </td>
                                        <td><small class="text-muted"><?php echo $r['admin_remark'] ? $r['admin_remark'] : '---'; ?></small></td>
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
