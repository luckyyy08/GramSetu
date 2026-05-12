<?php 
require_once '../config/init.php';

if (!isAdmin()) {
    redirect('/login.php');
}

// Handle Task Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_task'])) {
        $task_text = sanitize($_POST['task_text']);
        $pdo->prepare("INSERT INTO admin_tasks (task_text) VALUES (?)")->execute([$task_text]);
        redirect('dashboard.php');
    }
    if (isset($_POST['toggle_task'])) {
        $task_id = $_POST['task_id'];
        $pdo->prepare("UPDATE admin_tasks SET is_completed = NOT is_completed WHERE id = ?")->execute([$task_id]);
        redirect('dashboard.php');
    }
    if (isset($_POST['delete_task'])) {
        $task_id = $_POST['task_id'];
        $pdo->prepare("DELETE FROM admin_tasks WHERE id = ?")->execute([$task_id]);
        redirect('dashboard.php');
    }
}

// Get admin stats
$total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
$total_complaints = $pdo->query("SELECT COUNT(*) FROM complaints")->fetchColumn();
$pending_complaints = $pdo->query("SELECT COUNT(*) FROM complaints WHERE status = 'pending'")->fetchColumn();
$resolved_complaints = $pdo->query("SELECT COUNT(*) FROM complaints WHERE status = 'resolved'")->fetchColumn();
$total_notices = $pdo->query("SELECT COUNT(*) FROM notices")->fetchColumn();
$total_events = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();

$tasks = $pdo->query("SELECT * FROM admin_tasks ORDER BY created_at DESC LIMIT 10")->fetchAll();

include_once '../includes/header.php'; 
?>

<style>
    .admin-sidebar .list-group-item {
        border-radius: 10px !important;
        margin-bottom: 5px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .admin-sidebar .list-group-item.active {
        background: var(--bs-primary) !important;
        border: none;
    }
    .stat-card {
        border-radius: 15px;
        border: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
</style>

<div class="container-fluid py-4 px-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 mb-4">
            <div class="card p-3 admin-sidebar border-0 shadow-sm rounded-4">
                <div class="text-center mb-4 pt-3">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 70px; height: 70px;">
                        <i class="fas fa-user-shield fa-2x"></i>
                    </div>
                    <h5 class="fw-bold mb-0 text-primary">प्रशासक</h5>
                    <small class="text-muted">सरपंच / ग्रामसेवक</small>
                </div>
                <hr>
                <div class="list-group list-group-flush">
                    <a href="dashboard.php" class="list-group-item list-group-item-action active border-0">
                        डॅशबोर्ड
                    </a>
                    <a href="complaints.php" class="list-group-item list-group-item-action border-0">
                        तक्रार निवारण
                    </a>
                    <a href="notices.php" class="list-group-item list-group-item-action border-0">
                        सूचना व्यवस्थापन
                    </a>
                    <a href="events.php" class="list-group-item list-group-item-action border-0">
                        कार्यक्रम नियोजन
                    </a>
                    <a href="certificates.php" class="list-group-item list-group-item-action border-0">
                        दाखले अर्ज
                    </a>
                    <a href="polls.php" class="list-group-item list-group-item-action border-0">
                        मतदान / कौल
                    </a>
                    <a href="shg.php" class="list-group-item list-group-item-action border-0">
                        महिला बचत गट व्यवस्थापन
                    </a>
                    <a href="marketplace.php" class="list-group-item list-group-item-action border-0">
                        बाजारपेठ व्यवस्थापन
                    </a>
                    <a href="jobs.php" class="list-group-item list-group-item-action border-0">
                        जॉब बोर्ड व्यवस्थापन
                    </a>
                    <a href="donations.php" class="list-group-item list-group-item-action border-0">
                        वर्गणी व निधी व्यवस्थापन
                    </a>
                    <a href="reports.php" class="list-group-item list-group-item-action border-0">
                        गावचा प्रगती अहवाल
                    </a>
                    <a href="schemes.php" class="list-group-item list-group-item-action border-0">
                        सरकारी योजना
                    </a>
                    <a href="users.php" class="list-group-item list-group-item-action border-0">
                        ग्रामस्थ यादी
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h2 class="fw-bold mb-0 text-primary">प्रशासकीय डॅशबोर्ड</h2>
                </div>
                <div class="text-end">
                    <div class="fw-bold text-dark"><?php echo date('d M Y'); ?></div>
                </div>
            </div>

            
            <!-- Stats Cards -->
            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="card p-4 stat-card bg-white border-start border-5 border-primary">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted small fw-bold">नोंदणीकृत नागरिक</h6>
                                <h2 class="fw-bold mb-0"><?php echo $total_users; ?></h2>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded-4 text-primary">
                                <i class="fas fa-users fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-4 stat-card bg-white border-start border-5 border-warning">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted small fw-bold">प्रलंबित तक्रारी</h6>
                                <h2 class="fw-bold mb-0"><?php echo $pending_complaints; ?></h2>
                            </div>
                            <div class="bg-warning bg-opacity-10 p-3 rounded-4 text-warning">
                                <i class="fas fa-clock fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-4 stat-card bg-white border-start border-5 border-success">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted small fw-bold">निकाली तक्रारी</h6>
                                <h2 class="fw-bold mb-0"><?php echo $resolved_complaints; ?></h2>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded-4 text-success">
                                <i class="fas fa-check-circle fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-4 stat-card bg-white border-start border-5 border-info">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted small fw-bold">गावातील सूचना</h6>
                                <h2 class="fw-bold mb-0"><?php echo $total_notices; ?></h2>
                            </div>
                            <div class="bg-info bg-opacity-10 p-3 rounded-4 text-info">
                                <i class="fas fa-bullhorn fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Analytics Section -->
                <div class="col-md-12 mb-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-chart-line me-2 text-primary"></i>गावचा विकास आलेख (Smart Analytics)</h5>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <canvas id="complaintChart" height="250"></canvas>
                            </div>
                            <div class="col-md-6">
                                <canvas id="statusChart" height="250"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Task List Section -->
                <div class="col-md-5 mb-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-calendar-check me-2 text-dark"></i>या महिन्यातील कामे</h5>
                        </div>
                        
                        <form action="" method="POST" class="mb-4">
                            <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                <input type="text" name="task_text" class="form-control border-0 bg-light py-3 px-3" placeholder="नवीन काम जोडा..." required>
                                <button type="submit" name="add_task" class="btn btn-primary px-4 shadow-none">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </form>

                        <div class="task-list overflow-auto pe-2" style="max-height: 400px;">
                            <?php if (empty($tasks)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-clipboard-list fa-3x text-light mb-3"></i>
                                    <p class="text-muted small">सध्या कोणतीही प्रलंबित कामे नाहीत.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach($tasks as $task): ?>
                                    <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-4 mb-3 border-0">
                                        <div class="d-flex align-items-center overflow-hidden">
                                            <form action="" method="POST" class="m-0">
                                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                                <button type="submit" name="toggle_task" class="btn btn-link p-0 me-3 text-decoration-none shadow-none">
                                                    <i class="far <?php echo $task['is_completed'] ? 'fa-check-circle text-success' : 'fa-circle text-muted'; ?> fs-4"></i>
                                                </button>
                                            </form>
                                            <span class="<?php echo $task['is_completed'] ? 'text-decoration-line-through text-muted' : 'fw-bold text-dark'; ?> small text-truncate">
                                                <?php echo $task['task_text']; ?>
                                            </span>
                                        </div>
                                        <form action="" method="POST" class="m-0 ms-2">
                                            <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                            <button type="submit" name="delete_task" class="btn btn-link p-0 text-danger text-decoration-none shadow-none" onclick="return confirm('हटवायचे?')">
                                                <i class="fas fa-times-circle fs-5"></i>
                                            </button>
                                        </form>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Recent Complaints Section -->
                <div class="col-md-7 mb-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold mb-0 text-dark">तक्रारींवर कार्यवाही</h5>
                            <a href="complaints.php" class="btn btn-primary btn-sm rounded-pill px-3 py-2">सर्व तक्रारी</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="extra-small text-uppercase text-muted letter-spacing-1">
                                    <tr>
                                        <th class="border-0 px-0">नागरिक</th>
                                        <th class="border-0">विषय</th>
                                        <th class="border-0 text-end">स्थिती</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $recent_complaints = $pdo->query("SELECT complaints.*, users.full_name FROM complaints JOIN users ON complaints.user_id = users.id ORDER BY created_at DESC LIMIT 5")->fetchAll();
                                    foreach($recent_complaints as $rc): ?>
                                        <tr>
                                            <td class="border-0 px-0">
                                                <div class="fw-bold small text-dark"><?php echo $rc['full_name']; ?></div>
                                            </td>
                                            <td class="border-0">
                                                <div class="text-muted small text-truncate" style="max-width: 200px;"><?php echo $rc['title']; ?></div>
                                            </td>
                                            <td class="border-0 text-end">
                                                <?php 
                                                $rc_class = ['pending' => 'bg-warning', 'in-progress' => 'bg-info', 'resolved' => 'bg-success', 'rejected' => 'bg-danger'][$rc['status']];
                                                $rc_status_mr = [
                                                    'pending' => 'प्रलंबित',
                                                    'in-progress' => 'प्रक्रियेत',
                                                    'resolved' => 'निकाली',
                                                    'rejected' => 'नाकारली'
                                                ][$rc['status']];
                                                ?>
                                                <span class="badge <?php echo $rc_class; ?> rounded-pill px-3" style="font-size: 0.65rem;"><?php echo $rc_status_mr; ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

<?php include_once '../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Complaint Category Chart
    const ctx1 = document.getElementById('complaintChart').getContext('2d');
    new Chart(ctx1, {
        type: 'doughnut',
        data: {
            labels: ['पाणी', 'रस्ते', 'वीज', 'स्वच्छता', 'इतर'],
            datasets: [{
                label: 'तक्रारींचे प्रकार',
                data: [12, 19, 3, 5, 2],
                backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6c757d'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' },
                title: { display: true, text: 'तक्रारींचे प्रकार (Category-wise)' }
            }
        }
    });

    // Status Chart
    const ctx2 = document.getElementById('statusChart').getContext('2d');
    new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: ['प्रलंबित', 'प्रक्रियेत', 'निकाली'],
            datasets: [{
                label: 'तक्रारींची स्थिती',
                data: [<?php echo $pending_complaints; ?>, 5, <?php echo $resolved_complaints; ?>],
                backgroundColor: ['#ffc107', '#0dcaf0', '#198754'],
                borderRadius: 10
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } },
            plugins: {
                title: { display: true, text: 'निवारण प्रगती (Resolution Progress)' }
            }
        }
    });
</script>
