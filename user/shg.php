<?php 
require_once '../config/init.php';

if (!isLoggedIn()) {
    redirect('/login.php');
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Get user's SHG Membership
$stmt = $pdo->prepare("SELECT m.*, g.group_name, g.monthly_amount, g.description 
                       FROM shg_members m 
                       JOIN shg_groups g ON m.group_id = g.id 
                       WHERE m.user_id = ?");
$stmt->execute([$user_id]);
$my_group = $stmt->fetch();

// Handle Loan Application
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_loan'])) {
    $amount = $_POST['amount'];
    $purpose = sanitize($_POST['purpose']);
    $group_id = $my_group['group_id'];

    if ($amount > 0 && !empty($purpose)) {
        $stmt = $pdo->prepare("INSERT INTO shg_loans (group_id, user_id, amount, purpose) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$group_id, $user_id, $amount, $purpose])) {
            $success = "कर्जाचा अर्ज यशस्वीरित्या सादर झाला आहे.";
        }
    }
}

// Get Savings History
$savings = [];
if ($my_group) {
    $stmt = $pdo->prepare("SELECT * FROM shg_savings WHERE user_id = ? ORDER BY paid_at DESC");
    $stmt->execute([$user_id]);
    $savings = $stmt->fetchAll();
    
    // Total Savings
    $stmt = $pdo->prepare("SELECT SUM(amount) FROM shg_savings WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $total_savings = $stmt->fetchColumn() ?: 0;
}

include_once '../includes/header.php'; 
?>

<div class="container-fluid py-4 px-4">
    <?php if (!$my_group): ?>
        <!-- Not in any group -->
        <div class="text-center py-5">
            <div class="mb-4"><i class="fas fa-users fa-4x text-muted opacity-50"></i></div>
            <h3 class="fw-bold">तुम्ही अद्याप कोणत्याही बचत गटात सहभागी नाही.</h3>
            <p class="text-muted">बचत गटात सहभागी होण्यासाठी कृपया आपल्या ग्रामपंचायतीशी किंवा बचत गट प्रमुखाशी संपर्क साधा.</p>
        </div>
    <?php else: ?>
        <div class="row">
            <!-- Group Info & Stats -->
            <div class="col-md-4 mb-4">
                <div class="card p-4 border-0 shadow-sm rounded-4 mb-4 bg-primary text-white">
                    <h6 class="text-white-50 small fw-bold">माझा बचत गट</h6>
                    <h3 class="fw-bold mb-3"><?php echo $my_group['group_name']; ?></h3>
                    <div class="d-flex justify-content-between mb-2">
                        <span>मासिक बचत:</span>
                        <span class="fw-bold">₹<?php echo $my_group['monthly_amount']; ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>एकूण बचत:</span>
                        <h4 class="fw-bold mb-0">₹<?php echo $total_savings; ?></h4>
                    </div>
                </div>

                <div class="card p-4 border-0 shadow-sm rounded-4">
                    <h5 class="fw-bold mb-4">कर्जासाठी अर्ज करा</h5>
                    <?php if ($success): ?>
                        <div class="alert alert-success small"><?php echo $success; ?></div>
                    <?php endif; ?>
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">रक्कम (₹)</label>
                            <input type="number" name="amount" class="form-control bg-light border-0" placeholder="उदा. 5000" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">कारण</label>
                            <textarea name="purpose" class="form-control bg-light border-0" rows="3" placeholder="उदा. नवीन व्यवसाय, शेती काम..." required></textarea>
                        </div>
                        <button type="submit" name="apply_loan" class="btn btn-primary w-100 py-2 fw-bold">अर्ज करा</button>
                    </form>
                </div>
            </div>

            <!-- Savings History -->
            <div class="col-md-8">
                <div class="card p-4 border-0 shadow-sm rounded-4">
                    <h5 class="fw-bold mb-4">बचत इतिहास (Passbook)</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>महिना</th>
                                    <th>रक्कम</th>
                                    <th>तारीख</th>
                                    <th>स्थिती</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($savings)): ?>
                                    <tr><td colspan="4" class="text-center py-4 text-muted">अद्याप कोणतीही बचत जमा नाही.</td></tr>
                                <?php else: ?>
                                    <?php foreach($savings as $s): ?>
                                        <tr>
                                            <td><span class="fw-bold text-dark"><?php echo $s['month_year']; ?></span></td>
                                            <td>₹<?php echo $s['amount']; ?></td>
                                            <td><small class="text-muted"><?php echo formatDate($s['paid_at']); ?></small></td>
                                            <td><span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">जमा</span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- My Loans -->
                <div class="card p-4 border-0 shadow-sm rounded-4 mt-4">
                    <h5 class="fw-bold mb-4">माझे कर्ज रेकॉर्ड्स</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>रक्कम</th>
                                    <th>उद्देश</th>
                                    <th>तारीख</th>
                                    <th>स्थिती</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $stmt = $pdo->prepare("SELECT * FROM shg_loans WHERE user_id = ? ORDER BY applied_at DESC");
                                $stmt->execute([$user_id]);
                                $loans = $stmt->fetchAll();
                                
                                if (empty($loans)): ?>
                                    <tr><td colspan="4" class="text-center py-4 text-muted">अद्याप कोणताही कर्जाचा अर्ज नाही.</td></tr>
                                <?php else: ?>
                                    <?php foreach($loans as $l): ?>
                                        <tr>
                                            <td class="fw-bold">₹<?php echo $l['amount']; ?></td>
                                            <td><small class="text-muted"><?php echo $l['purpose']; ?></small></td>
                                            <td><small class="text-muted"><?php echo formatDate($l['applied_at']); ?></small></td>
                                            <td>
                                                <?php 
                                                $l_mr = ['pending' => 'प्रलंबित', 'approved' => 'मंजूर', 'rejected' => 'नाकारला', 'repaid' => 'फेडले'];
                                                $l_class = ['pending' => 'bg-warning', 'approved' => 'bg-primary', 'rejected' => 'bg-danger', 'repaid' => 'bg-success'];
                                                ?>
                                                <span class="badge <?php echo $l_class[$l['status']]; ?> rounded-pill px-3"><?php echo $l_mr[$l['status']]; ?></span>
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
    <?php endif; ?>
</div>

<?php include_once '../includes/footer.php'; ?>
