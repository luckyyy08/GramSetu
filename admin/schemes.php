<?php 
require_once '../config/init.php';

if (!isAdmin()) {
    redirect('/login.php');
}

// Handle Add Scheme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_scheme'])) {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $eligibility = sanitize($_POST['eligibility']);
    $deadline = $_POST['deadline'];
    $link = sanitize($_POST['link']);

    $stmt = $pdo->prepare("INSERT INTO schemes (title, description, eligibility, deadline, link) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$title, $description, $eligibility, $deadline, $link])) {
        setFlash('success', 'नवीन योजना यशस्वीरित्या जोडली गेली आहे.');
    } else {
        setFlash('danger', 'त्रुटी आली.');
    }
    redirect('/admin/schemes.php');
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM schemes WHERE id = ?");
    $stmt->execute([$id]);
    setFlash('success', 'योजना हटवली गेली.');
    redirect('/admin/schemes.php');
}

$schemes = $pdo->query("SELECT * FROM schemes ORDER BY created_at DESC")->fetchAll();

include_once '../includes/header.php'; 
?>

<div class="container-fluid py-4 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">योजना व्यवस्थापन</h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSchemeModal">+ नवीन योजना जोडा</button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>योजनेचे नाव</th>
                        <th>अंतिम तारीख</th>
                        <th>कृती</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($schemes)): ?>
                        <tr><td colspan="3" class="text-center py-4">अद्याप कोणतीही योजना जोडली नाही.</td></tr>
                    <?php else: ?>
                        <?php foreach($schemes as $scheme): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold"><?php echo $scheme['title']; ?></div>
                                    <small class="text-muted text-truncate d-block" style="max-width: 300px;"><?php echo $scheme['description']; ?></small>
                                </td>
                                <td><span class="badge bg-light text-dark"><?php echo $scheme['deadline'] ? formatDate($scheme['deadline']) : '---'; ?></span></td>
                                <td>
                                    <a href="?delete=<?php echo $scheme['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('ही योजना हटवायची आहे का?')">हटवा</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Scheme Modal -->
<div class="modal fade" id="addSchemeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="schemes.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">नवीन सरकारी योजना जोडा</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">योजनेचे नाव</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">योजनेची माहिती</label>
                        <textarea name="description" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">पात्रता (Eligibility)</label>
                        <textarea name="eligibility" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">अंतिम तारीख</label>
                            <input type="date" name="deadline" class="form-control">
                        </div>
                        <div class="col">
                            <label class="form-label">अधिकृत लिंक (URL)</label>
                            <input type="url" name="link" class="form-control" placeholder="https://...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">बंद करा</button>
                    <button type="submit" name="add_scheme" class="btn btn-primary">योजना प्रकाशित करा</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>
