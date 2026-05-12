<?php 
require_once '../config/init.php';

if (!isLoggedIn()) {
    redirect('/login.php');
}

$user_id = $_SESSION['user_id'];

// Get finance group info (joining users table to see if user is in any group)
// For simplicity, we assume one primary finance group for the village for now
$group = $pdo->query("SELECT * FROM finance_groups LIMIT 1")->fetch();

// Get user's contributions
$contributions = [];
$total_contributed = 0;
if ($group) {
    $stmt = $pdo->prepare("SELECT * FROM finance_contributions WHERE user_id = ? AND group_id = ? ORDER BY paid_at DESC");
    $stmt->execute([$user_id, $group['id']]);
    $contributions = $stmt->fetchAll();
    
    $stmt = $pdo->prepare("SELECT SUM(amount) FROM finance_contributions WHERE user_id = ? AND group_id = ?");
    $stmt->execute([$user_id, $group['id']]);
    $total_contributed = $stmt->fetchColumn() ?: 0;
}

// Get user's active loans
$active_loans = [];
if ($group) {
    $stmt = $pdo->prepare("SELECT * FROM finance_loans WHERE user_id = ? AND status = 'active'");
    $stmt->execute([$user_id]);
    $active_loans = $stmt->fetchAll();
}

include_once '../includes/header.php'; 
?>

<div class="container-fluid py-4 px-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h3 class="fw-bold mb-0 text-primary">गावची पतपेढी व फायनान्स</h3>
            <p class="text-muted small">मासिक बचत आणि अल्प दरातील कर्ज सुविधा.</p>
        </div>
    </div>

    <?php if (!$group): ?>
        <div class="card p-5 text-center border-0 shadow-sm rounded-4">
            <i class="fas fa-university fa-3x text-muted mb-3 opacity-30"></i>
            <h5>सध्या गावात कोणताही सक्रिय फायनान्स ग्रुप नाही.</h5>
        </div>
    <?php else: ?>
        <div class="row">
            <!-- Stats -->
            <div class="col-md-4 mb-4">
                <div class="card p-4 border-0 shadow-sm rounded-4 bg-dark text-white mb-4">
                    <h6 class="text-white-50 small fw-bold">माझी एकूण गुंतवणूक</h6>
                    <h2 class="fw-bold mb-3">₹<?php echo number_format($total_contributed, 2); ?></h2>
                    <hr class="opacity-20">
                    <div class="d-flex justify-content-between small">
                        <span>गटाचा एकूण निधी:</span>
                        <span class="text-success fw-bold">₹<?php echo number_format($group['total_fund'], 2); ?></span>
                    </div>
                </div>

                <div class="card p-4 border-0 shadow-sm rounded-4">
                    <h5 class="fw-bold mb-4">सक्रिय कर्ज (Active Loans)</h5>
                    <?php if (empty($active_loans)): ?>
                        <p class="text-muted small mb-0">तुमच्या नावावर सध्या कोणतेही कर्ज नाही.</p>
                    <?php else: ?>
                        <?php foreach($active_loans as $al): ?>
                            <div class="p-3 bg-light rounded-3 mb-2">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fw-bold small">रक्कम: ₹<?php echo $al['amount']; ?></span>
                                    <span class="badge bg-primary rounded-pill"><?php echo $al['interest_rate']; ?>% व्याज</span>
                                </div>
                                <small class="text-muted">दिनांक: <?php echo $al['issued_at']; ?></small>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Passbook -->
            <div class="col-md-8">
                <div class="card p-4 border-0 shadow-sm rounded-4">
                    <h5 class="fw-bold mb-4">माझी मासिक वर्गणी (Passbook)</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>महिना</th>
                                    <th>भरलेली रक्कम</th>
                                    <th>तारीख</th>
                                    <th>नोंद</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($contributions)): ?>
                                    <tr><td colspan="4" class="text-center py-5 text-muted">अद्याप कोणतीही वर्गणी जमा नाही.</td></tr>
                                <?php else: ?>
                                    <?php foreach($contributions as $c): ?>
                                        <tr>
                                            <td class="fw-bold"><?php echo $c['month_year']; ?></td>
                                            <td>₹<?php echo number_format($c['amount'], 2); ?></td>
                                            <td><small class="text-muted"><?php echo formatDate($c['paid_at']); ?></small></td>
                                            <td><span class="text-success small"><i class="fas fa-check-circle me-1"></i>यशस्वी</span></td>
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
