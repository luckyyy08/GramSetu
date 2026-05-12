<?php 
require_once '../config/init.php';

if (!isAdmin()) {
    redirect('/login.php');
}

// Handle Add Event
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_event'])) {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $location = sanitize($_POST['location']);
    $category = sanitize($_POST['category']);

    $stmt = $pdo->prepare("INSERT INTO events (title, description, event_date, event_time, location, category) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$title, $description, $event_date, $event_time, $location, $category])) {
        setFlash('success', 'कार्यक्रम जोडला गेला.');
    } else {
        setFlash('danger', 'त्रुटी आली.');
    }
    redirect('/admin/events.php');
}

$events = $pdo->query("SELECT * FROM events ORDER BY event_date DESC")->fetchAll();

include_once '../includes/header.php'; 
?>

<div class="container-fluid py-4 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">कार्यक्रम व्यवस्थापन</h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEventModal">+ नवीन कार्यक्रम</button>
    </div>

    <div class="row">
        <?php foreach($events as $event): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="badge bg-primary"><?php echo str_replace('_', ' ', ucfirst($event['category'])); ?></span>
                            <small class="text-muted"><i class="fas fa-calendar-alt me-1"></i> <?php echo formatDate($event['event_date']); ?></small>
                        </div>
                        <h5 class="fw-bold"><?php echo $event['title']; ?></h5>
                        <p class="text-muted small"><?php echo $event['description']; ?></p>
                        <div class="mt-3">
                            <div class="small mb-1"><i class="fas fa-clock me-2 text-primary"></i> <?php echo formatTime($event['event_time']); ?></div>
                            <div class="small"><i class="fas fa-map-marker-alt me-2 text-danger"></i> <?php echo $event['location']; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Add Event Modal -->
<div class="modal fade" id="addEventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="events.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">नवीन कार्यक्रम जोडा</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">कार्यक्रमाचे नाव</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">तारीख</label>
                            <input type="date" name="event_date" class="form-control" required>
                        </div>
                        <div class="col">
                            <label class="form-label">वेळ</label>
                            <input type="time" name="event_time" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ठिकाण</label>
                        <input type="text" name="location" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">वर्ग</label>
                        <select name="category" class="form-select">
                            <option value="gram_sabha">ग्रामसभा</option>
                            <option value="festival">सण/उत्सव</option>
                            <option value="meeting">बैठक</option>
                            <option value="other">इतर</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">मजकूर</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">बंद करा</button>
                    <button type="submit" name="add_event" class="btn btn-primary">जतन करा</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>
