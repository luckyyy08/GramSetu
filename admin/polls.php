<?php 
require_once '../config/init.php';

if (!isAdmin()) {
    redirect('/login.php');
}

// Handle New Poll
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_poll'])) {
    $question = sanitize($_POST['question']);
    $opt1 = sanitize($_POST['option_1']);
    $opt2 = sanitize($_POST['option_2']);

    if ($question && $opt1 && $opt2) {
        $stmt = $pdo->prepare("INSERT INTO polls (question, option_1, option_2) VALUES (?, ?, ?)");
        $stmt->execute([$question, $opt1, $opt2]);
        setFlash('success', 'नवीन मतदान (Poll) सुरू झाले आहे.');
    }
}

// Handle Close Poll
if (isset($_GET['close'])) {
    $id = (int)$_GET['close'];
    $stmt = $pdo->prepare("UPDATE polls SET status = 'closed' WHERE id = ?");
    if ($stmt->execute([$id])) {
        setFlash('success', 'मतदान यशस्वीरित्या बंद करण्यात आले आहे.');
    }
    redirect('/admin/polls.php');
}

$polls = $pdo->query("SELECT * FROM polls ORDER BY created_at DESC")->fetchAll();

include_once '../includes/header.php'; 
?>

<div class="container-fluid py-4 px-4">
    <div class="row">
        <!-- Create Poll -->
        <div class="col-md-4 mb-4">
            <div class="card p-4 border-0 shadow-sm rounded-4">
                <h4 class="fw-bold mb-4">नवीन मतदान सुरू करा</h4>
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">तुमचा प्रश्न</label>
                        <input type="text" name="question" class="form-control bg-light border-0 py-2" placeholder="उदा. गावात वाचनालय हवे का?" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">पर्याय १</label>
                        <input type="text" name="option_1" class="form-control bg-light border-0" placeholder="उदा. हो" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">पर्याय २</label>
                        <input type="text" name="option_2" class="form-control bg-light border-0" placeholder="उदा. नाही" required>
                    </div>
                    <button type="submit" name="create_poll" class="btn btn-primary w-100 py-2 fw-bold">सुरू करा</button>
                </form>
            </div>
        </div>

        <!-- Polls List -->
        <div class="col-md-8">
            <div class="card p-4 border-0 shadow-sm rounded-4">
                <h4 class="fw-bold mb-4">मतदान इतिहास व निकाल</h4>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>प्रश्न</th>
                                <th>निकालाची स्थिती (Votes)</th>
                                <th>स्थिती</th>
                                <th>कृती</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($polls as $p): 
                                // Get Vote Counts
                                $v1 = $pdo->query("SELECT COUNT(*) FROM poll_votes WHERE poll_id = {$p['id']} AND vote_option = 1")->fetchColumn();
                                $v2 = $pdo->query("SELECT COUNT(*) FROM poll_votes WHERE poll_id = {$p['id']} AND vote_option = 2")->fetchColumn();
                                ?>
                                <tr>
                                    <td><div class="fw-bold small"><?php echo $p['question']; ?></div></td>
                                    <td>
                                        <div class="small">
                                            <?php echo $p['option_1']; ?>: <strong><?php echo $v1; ?></strong> | 
                                            <?php echo $p['option_2']; ?>: <strong><?php echo $v2; ?></strong>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $p['status'] == 'active' ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo $p['status'] == 'active' ? 'सुरू' : 'बंद'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if($p['status'] == 'active'): ?>
                                            <a href="?close=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-danger rounded-pill">बंद करा</a>
                                        <?php endif; ?>
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

<?php include_once '../includes/footer.php'; ?>
