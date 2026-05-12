<?php 
require_once '../config/init.php';

if (!isLoggedIn()) {
    redirect('/login.php');
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Handle Vote Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vote'])) {
    $poll_id = $_POST['poll_id'];
    $option = $_POST['vote_option'];

    try {
        $stmt = $pdo->prepare("INSERT INTO poll_votes (poll_id, user_id, vote_option) VALUES (?, ?, ?)");
        if ($stmt->execute([$poll_id, $user_id, $option])) {
            $success = "तुमचे मत यशस्वीरित्या नोंदवले गेले आहे!";
        }
    } catch (PDOException $e) {
        $error = "तुम्ही याआधीच या विषयावर मतदान केले आहे.";
    }
}

$active_polls = $pdo->query("SELECT * FROM polls WHERE status = 'active' ORDER BY created_at DESC")->fetchAll();

include_once '../includes/header.php'; 
?>

<div class="container py-4">
    <h3 class="fw-bold mb-4">गावाचे मत (Polls)</h3>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-warning"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="row">
        <?php if (empty($active_polls)): ?>
            <div class="col-12 text-center py-5">
                <i class="fas fa-vote-yea fa-4x text-light mb-3"></i>
                <h5 class="text-muted">सध्या कोणतेही सक्रिय मतदान सुरू नाही.</h5>
            </div>
        <?php else: ?>
            <?php foreach($active_polls as $p): 
                // Check if user already voted
                $check = $pdo->prepare("SELECT id FROM poll_votes WHERE poll_id = ? AND user_id = ?");
                $check->execute([$p['id'], $user_id]);
                $voted = $check->fetch();
                ?>
                <div class="col-md-6 mb-4">
                    <div class="card p-4 border-0 shadow-sm rounded-4 h-100">
                        <h5 class="fw-bold mb-3"><?php echo $p['question']; ?></h5>
                        
                        <?php if($voted): ?>
                            <div class="alert alert-light border-0 small mb-0 text-success fw-bold">
                                <i class="fas fa-check-circle me-2"></i> तुम्ही तुमचे मत नोंदवले आहे.
                            </div>
                        <?php else: ?>
                            <form action="" method="POST">
                                <input type="hidden" name="poll_id" value="<?php echo $p['id']; ?>">
                                <div class="d-grid gap-2">
                                    <button type="submit" name="vote" value="1" class="btn btn-outline-primary py-2 fw-bold rounded-pill">
                                        <input type="hidden" name="vote_option" value="1">
                                        <?php echo $p['option_1']; ?>
                                    </button>
                                    <button type="submit" name="vote" value="2" class="btn btn-outline-primary py-2 fw-bold rounded-pill">
                                        <input type="hidden" name="vote_option" value="2">
                                        <?php echo $p['option_2']; ?>
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>
