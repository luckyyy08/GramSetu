<?php 
require_once '../config/init.php';

if (!isAdmin()) {
    redirect('/login.php');
}

$success = '';
$error = '';

// Handle New Donation Entry
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_donation'])) {
    $event = sanitize($_POST['event_name']);
    $donor = sanitize($_POST['donor_name']);
    $amount = $_POST['amount'];
    $desc = sanitize($_POST['description']);
    $mode = $_POST['payment_mode'];

    $stmt = $pdo->prepare("INSERT INTO donations (event_name, donor_name, amount, description, payment_mode) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$event, $donor, $amount, $desc, $mode])) {
        $success = "वर्गणीची नोंद यशस्वीरित्या झाली आहे.";
    }
}

// Get event-wise totals
$event_totals = $pdo->query("SELECT event_name, SUM(amount) as total FROM donations GROUP BY event_name ORDER BY total DESC")->fetchAll();

// Get recent donations
$donations = $pdo->query("SELECT * FROM donations ORDER BY created_at DESC")->fetchAll();

include_once '../includes/header.php'; 
?>

<div class="container-fluid py-4 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary">वर्गणी व उत्सव निधी व्यवस्थापन</h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDonationModal">+ नवीन नोंद करा</button>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- Totals -->
        <div class="col-md-12 mb-4">
            <div class="row g-3">
                <?php foreach($event_totals as $et): ?>
                    <div class="col-md-3">
                        <div class="card p-4 border-0 shadow-sm rounded-4 bg-white border-top border-5 border-primary">
                            <h6 class="text-muted small fw-bold mb-1"><?php echo $et['event_name']; ?></h6>
                            <h3 class="fw-bold mb-0 text-primary">₹<?php echo number_format($et['total'], 2); ?></h3>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Table -->
        <div class="col-md-12">
            <div class="card p-4 border-0 shadow-sm rounded-4">
                <h5 class="fw-bold mb-4">सर्व वर्गणी यादी</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>देणगीदार</th>
                                <th>उत्सव</th>
                                <th>रक्कम</th>
                                <th>पद्धत</th>
                                <th>तारीख</th>
                                <th class="text-end pe-4">कृती</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($donations as $d): ?>
                                <tr>
                                    <td><div class="fw-bold"><?php echo $d['donor_name']; ?></div></td>
                                    <td><?php echo $d['event_name']; ?></td>
                                    <td class="fw-bold">₹<?php echo number_format($d['amount'], 2); ?></td>
                                    <td><?php echo $d['payment_mode']; ?></td>
                                    <td><small class="text-muted"><?php echo formatDate($d['created_at']); ?></small></td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-sm btn-outline-danger border-0"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Donation Modal -->
<div class="modal fade" id="addDonationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="" method="POST">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold">नवीन वर्गणी नोंदवा</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">उत्सव / प्रसंगाचे नाव</label>
                        <input type="text" name="event_name" class="form-control bg-light border-0" placeholder="उदा. गणेशोत्सव 2024" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">देणगीदाराचे नाव</label>
                        <input type="text" name="donor_name" class="form-control bg-light border-0" placeholder="पूर्ण नाव" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">रक्कम (₹)</label>
                            <input type="number" name="amount" class="form-control bg-light border-0" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">पद्धत</label>
                            <select name="payment_mode" class="form-select bg-light border-0">
                                <option value="Cash">रोख (Cash)</option>
                                <option value="Online">ऑनलाइन (Online)</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold">टिप्पणी (पर्यायी)</label>
                        <textarea name="description" class="form-control bg-light border-0" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" name="add_donation" class="btn btn-primary w-100 py-2 fw-bold">नोंद जतन करा</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>
