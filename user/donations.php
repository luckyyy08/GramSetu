<?php 
require_once '../config/init.php';

if (!isLoggedIn()) {
    redirect('/login.php');
}

// Get event-wise totals
$event_totals = $pdo->query("SELECT event_name, SUM(amount) as total FROM donations GROUP BY event_name ORDER BY total DESC")->fetchAll();

// Get recent donations
$donations = $pdo->query("SELECT * FROM donations ORDER BY created_at DESC LIMIT 50")->fetchAll();

include_once '../includes/header.php'; 
?>

<div class="container-fluid py-4 px-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-12">
            <h3 class="fw-bold mb-0 text-primary">उत्सव निधी व पारदर्शक हिशोब</h3>
            <p class="text-muted small">गावातील सार्वजनिक कार्यांसाठी जमा झालेल्या वर्गणीचा तपशील.</p>
        </div>
    </div>

    <div class="row">
        <!-- Summary Cards -->
        <div class="col-md-12 mb-4">
            <div class="row g-3">
                <?php foreach($event_totals as $et): ?>
                    <div class="col-md-3">
                        <div class="card p-4 border-0 shadow-sm rounded-4 bg-white border-start border-5 border-success">
                            <h6 class="text-muted small fw-bold mb-1"><?php echo $et['event_name']; ?></h6>
                            <h3 class="fw-bold mb-0">₹<?php echo number_format($et['total'], 2); ?></h3>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if(empty($event_totals)): ?>
                    <div class="col-12 text-center py-4 card border-0 shadow-sm rounded-4">
                        <p class="text-muted mb-0">सध्या कोणताही निधी जमा झालेला नाही.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Donations Table -->
        <div class="col-md-12">
            <div class="card p-4 border-0 shadow-sm rounded-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">देणगीदार व वर्गणी यादी</h5>
                    <div class="badge bg-primary rounded-pill px-3 py-2">एकूण नोंदी: <?php echo count($donations); ?></div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>देणगीदाराचे नाव</th>
                                <th>उत्सव/प्रसंग</th>
                                <th>रक्कम (₹)</th>
                                <th>पद्धत</th>
                                <th>तारीख</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($donations)): ?>
                                <tr><td colspan="5" class="text-center py-5 text-muted">अद्याप कोणतीही देणगी नोंदवलेली नाही.</td></tr>
                            <?php else: ?>
                                <?php foreach($donations as $d): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark"><?php echo $d['donor_name']; ?></div>
                                            <small class="text-muted"><?php echo $d['description'] ?: '---'; ?></small>
                                        </td>
                                        <td><span class="badge bg-light text-dark"><?php echo $d['event_name']; ?></span></td>
                                        <td class="fw-bold">₹<?php echo number_format($d['amount'], 2); ?></td>
                                        <td>
                                            <span class="small">
                                                <i class="fas <?php echo $d['payment_mode'] == 'Online' ? 'fa-mobile-alt text-primary' : 'fa-money-bill-wave text-success'; ?> me-1"></i>
                                                <?php echo $d['payment_mode']; ?>
                                            </span>
                                        </td>
                                        <td><small class="text-muted"><?php echo formatDate($d['created_at']); ?></small></td>
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
