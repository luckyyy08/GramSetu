<?php 
require_once '../config/init.php';

if (!isAdmin()) {
    redirect('/login.php');
}

// Get stats for report
$total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn();
$total_complaints = $pdo->query("SELECT COUNT(*) FROM complaints")->fetchColumn();
$resolved = $pdo->query("SELECT COUNT(*) FROM complaints WHERE status='resolved'")->fetchColumn();
$total_funds = $pdo->query("SELECT SUM(amount) FROM donations")->fetchColumn() ?: 0;
$total_shg = $pdo->query("SELECT COUNT(*) FROM shg_groups")->fetchColumn();

include_once '../includes/header.php'; 
?>

<div class="container-fluid py-4 px-4 no-print">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary">गावचा प्रगती अहवाल (Reports)</h3>
        <button class="btn btn-dark fw-bold px-4" onclick="window.print()">
            <i class="fas fa-print me-2"></i> रिपोर्ट प्रिंट करा
        </button>
    </div>
</div>

<!-- Printable Area -->
<div class="container-fluid py-4 px-5 bg-white shadow-sm rounded-4 printable-report" id="reportArea">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-dark border-bottom pb-3 mb-2">ग्रामसेतू - डिजिटल ग्रामपंचायत अहवाल</h2>
        <h5 class="text-muted">तारीख: <?php echo date('d F Y'); ?></h5>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="p-4 border rounded-4">
                <h6 class="text-muted small fw-bold">नोंदणीकृत नागरिक</h6>
                <h3 class="fw-bold mb-0"><?php echo $total_users; ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-4 border rounded-4">
                <h6 class="text-muted small fw-bold">तक्रार निवारण दर</h6>
                <h3 class="fw-bold mb-0"><?php echo ($total_complaints > 0) ? round(($resolved/$total_complaints)*100, 1) : 0; ?>%</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-4 border rounded-4">
                <h6 class="text-muted small fw-bold">एकूण सार्वजनिक निधी</h6>
                <h3 class="fw-bold mb-0">₹<?php echo number_format($total_funds, 2); ?></h3>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h5 class="fw-bold mb-3">अलीकडील तक्रारींचा सारांश</h5>
            <table class="table table-bordered small">
                <thead>
                    <tr class="bg-light">
                        <th>विषय</th>
                        <th>नागरिक</th>
                        <th>स्थिती</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $recent = $pdo->query("SELECT c.*, u.full_name FROM complaints c JOIN users u ON c.user_id = u.id ORDER BY c.created_at DESC LIMIT 5")->fetchAll();
                    foreach($recent as $r): ?>
                        <tr>
                            <td><?php echo $r['title']; ?></td>
                            <td><?php echo $r['full_name']; ?></td>
                            <td><?php echo $r['status']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="col-md-6">
            <h5 class="fw-bold mb-3">उत्सव निधी हिशोब</h5>
            <table class="table table-bordered small">
                <thead>
                    <tr class="bg-light">
                        <th>उत्सव</th>
                        <th>जमा निधी</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $funds = $pdo->query("SELECT event_name, SUM(amount) as total FROM donations GROUP BY event_name")->fetchAll();
                    foreach($funds as $f): ?>
                        <tr>
                            <td><?php echo $f['event_name']; ?></td>
                            <td>₹<?php echo number_format($f['total'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5 pt-5 text-end">
        <div class="d-inline-block text-center" style="width: 200px; border-top: 2px solid #000;">
            <p class="mb-0 fw-bold">सरपंच / ग्रामसेवक</p>
            <small class="text-muted">स्वाक्षरी व शिक्का</small>
        </div>
    </div>
</div>

<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white !important; }
        .printable-report { border: none !important; shadow: none !important; }
    }
</style>

<?php include_once '../includes/footer.php'; ?>
