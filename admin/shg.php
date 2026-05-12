<?php 
require_once '../config/init.php';

if (!isAdmin()) {
    redirect('/login.php');
}

$success = '';
$error = '';

// Handle Group Creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_group'])) {
    $name = sanitize($_POST['group_name']);
    $leader = $_POST['leader_id'];
    $amount = $_POST['monthly_amount'];

    $stmt = $pdo->prepare("INSERT INTO shg_groups (group_name, leader_id, monthly_amount) VALUES (?, ?, ?)");
    if ($stmt->execute([$name, $leader, $amount])) {
        $success = "नवीन बचत गट यशस्वीरित्या तयार झाला आहे.";
    }
}

// Handle Savings Entry
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_saving'])) {
    $group_id = $_POST['group_id'];
    $user_id = $_POST['user_id'];
    $amount = $_POST['amount'];
    $month = $_POST['month_year'];

    $stmt = $pdo->prepare("INSERT INTO shg_savings (group_id, user_id, amount, month_year) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$group_id, $user_id, $amount, $month])) {
        $success = "बचत नोंदवली गेली आहे.";
    }
}

// Handle Loan Update
if (isset($_GET['approve_loan'])) {
    $pdo->prepare("UPDATE shg_loans SET status = 'approved' WHERE id = ?")->execute([$_GET['approve_loan']]);
    redirect('/admin/shg.php');
}

$groups = $pdo->query("SELECT g.*, u.full_name as leader_name FROM shg_groups g JOIN users u ON g.leader_id = u.id")->fetchAll();
$all_users = $pdo->query("SELECT id, full_name FROM users WHERE role = 'user'")->fetchAll();

include_once '../includes/header.php'; 
?>

<div class="container-fluid py-4 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary">महिला बचत गट व्यवस्थापन</h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createGroupModal">+ नवीन गट तयार करा</button>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- Groups List -->
        <?php foreach($groups as $g): ?>
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white py-3 border-0">
                        <h5 class="fw-bold mb-0 text-primary"><?php echo $g['group_name']; ?></h5>
                        <small class="text-muted">प्रमुख: <?php echo $g['leader_name']; ?> | मासिक बचत: ₹<?php echo $g['monthly_amount']; ?></small>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light extra-small">
                                    <tr>
                                        <th class="ps-4">सदस्य</th>
                                        <th>एकूण बचत</th>
                                        <th class="text-end pe-4">कृती</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $members = $pdo->prepare("SELECT m.*, u.full_name FROM shg_members m JOIN users u ON m.user_id = u.id WHERE m.group_id = ?");
                                    $members->execute([$g['id']]);
                                    foreach($members->fetchAll() as $m): 
                                        $s_total = $pdo->prepare("SELECT SUM(amount) FROM shg_savings WHERE user_id = ? AND group_id = ?");
                                        $s_total->execute([$m['user_id'], $g['id']]);
                                        $total = $s_total->fetchColumn() ?: 0;
                                    ?>
                                        <tr>
                                            <td class="ps-4 fw-bold small"><?php echo $m['full_name']; ?></td>
                                            <td>₹<?php echo $total; ?></td>
                                            <td class="text-end pe-4">
                                                <button class="btn btn-sm btn-outline-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#savingModal<?php echo $m['id']; ?>">बचत नोंदवा</button>
                                            </td>
                                        </tr>

                                        <!-- Savings Modal -->
                                        <div class="modal fade" id="savingModal<?php echo $m['id']; ?>" tabindex="-1">
                                            <div class="modal-dialog modal-sm">
                                                <div class="modal-content border-0 shadow rounded-4">
                                                    <form action="" method="POST">
                                                        <div class="modal-header bg-light">
                                                            <h5 class="modal-title fw-bold small">बचत नोंदवा</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body p-4">
                                                            <input type="hidden" name="group_id" value="<?php echo $g['id']; ?>">
                                                            <input type="hidden" name="user_id" value="<?php echo $m['user_id']; ?>">
                                                            <div class="mb-3">
                                                                <label class="form-label small fw-bold">महिना व वर्ष</label>
                                                                <input type="text" name="month_year" class="form-control bg-light border-0" value="<?php echo date('M Y'); ?>" required>
                                                            </div>
                                                            <div class="mb-0">
                                                                <label class="form-label small fw-bold">रक्कम (₹)</label>
                                                                <input type="number" name="amount" class="form-control bg-light border-0" value="<?php echo $g['monthly_amount']; ?>" required>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-0 p-4 pt-0">
                                                            <button type="submit" name="add_saving" class="btn btn-primary w-100 py-2 fw-bold">जतन करा</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Create Group Modal -->
<div class="modal fade" id="createGroupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="" method="POST">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold">नवीन बचत गट तयार करा</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">गटाचे नाव</label>
                        <input type="text" name="group_name" class="form-control bg-light border-0" placeholder="उदा. सावित्रीबाई फुले बचत गट" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">गट प्रमुख निवडा</label>
                        <select name="leader_id" class="form-select bg-light border-0" required>
                            <?php foreach($all_users as $u): ?>
                                <option value="<?php echo $u['id']; ?>"><?php echo $u['full_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold">मासिक बचत रक्कम (₹)</label>
                        <input type="number" name="monthly_amount" class="form-control bg-light border-0" value="100" required>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" name="create_group" class="btn btn-primary w-100 py-2 fw-bold">गट तयार करा</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>
