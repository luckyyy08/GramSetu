<?php 
require_once '../config/init.php';

if (!isAdmin()) {
    redirect('/login.php');
}

// Handle Add Notice
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_notice'])) {
    $title = sanitize($_POST['title']);
    $content = sanitize($_POST['content']);
    $category = sanitize($_POST['category']);
    $is_important = isset($_POST['is_important']) ? 1 : 0;

    $stmt = $pdo->prepare("INSERT INTO notices (title, content, category, is_important) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$title, $content, $category, $is_important])) {
        setFlash('success', 'सूचना प्रकाशित झाली आहे.');
    } else {
        setFlash('danger', 'त्रुटी आली.');
    }
    redirect('/admin/notices.php');
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM notices WHERE id = ?");
    $stmt->execute([$id]);
    setFlash('success', 'सूचना हटवली गेली.');
    redirect('/admin/notices.php');
}

$notices = $pdo->query("SELECT * FROM notices ORDER BY created_at DESC")->fetchAll();

include_once '../includes/header.php'; 
?>

<div class="container-fluid py-4 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">सूचना व्यवस्थापन</h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNoticeModal">+ नवीन सूचना</button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>शीर्षक</th>
                        <th>वर्ग</th>
                        <th>महत्वाची?</th>
                        <th>दिनांक</th>
                        <th>कृती</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($notices as $notice): ?>
                        <tr>
                            <td><div class="fw-bold"><?php echo $notice['title']; ?></div></td>
                            <td><span class="badge bg-light text-dark"><?php echo $notice['category']; ?></span></td>
                            <td>
                                <?php if($notice['is_important']): ?>
                                    <span class="badge bg-danger">हो</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">नाही</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo formatDate($notice['created_at']); ?></td>
                            <td>
                                <a href="?delete=<?php echo $notice['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('खात्री आहे?')">हटवा</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Notice Modal -->
<div class="modal fade" id="addNoticeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="notices.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">नवीन सूचना जोडा</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">शीर्षक</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">वर्ग</label>
                        <select name="category" class="form-select">
                            <option value="General">सामान्य</option>
                            <option value="Health">आरोग्य</option>
                            <option value="Agriculture">शेती</option>
                            <option value="Education">शिक्षण</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">मजकूर</label>
                        <textarea name="content" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_important" class="form-check-input" id="is_imp">
                        <label class="form-check-label" for="is_imp">महत्वाची सूचना म्हणून चिन्हांकित करा</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">बंद करा</button>
                    <button type="submit" name="add_notice" class="btn btn-primary">प्रकाशित करा</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>
