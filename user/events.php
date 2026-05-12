<?php 
require_once '../config/init.php';

if (!isLoggedIn()) {
    redirect('/login.php');
}

$events = $pdo->query("SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC")->fetchAll();

include_once '../includes/header.php'; 
?>

<div class="container py-4">
    <div class="text-center mb-5">
        <h3 class="fw-bold">गावातील येणारे कार्यक्रम</h3>
        <p class="text-muted">ग्रामसभा, उत्सव आणि महत्त्वाच्या बैठकांचे वेळापत्रक</p>
    </div>

    <div class="row">
        <?php if (empty($events)): ?>
            <div class="col-12 text-center py-5">
                <div class="mb-3"><i class="far fa-calendar-times fa-4x text-muted"></i></div>
                <h5>सध्या कोणताही कार्यक्रम नियोजित नाही.</h5>
            </div>
        <?php else: ?>
            <?php foreach($events as $event): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="position-absolute p-3" style="right: 0; top: 0;">
                            <?php 
                            $cat_icon = [
                                'gram_sabha' => 'fa-landmark',
                                'festival' => 'fa-om',
                                'meeting' => 'fa-users',
                                'other' => 'fa-calendar-alt'
                            ][$event['category']];
                            ?>
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas <?php echo $cat_icon; ?>"></i>
                            </div>
                        </div>
                        <div class="card-body p-4 pt-5">
                            <div class="text-primary fw-bold mb-2 h2 mb-0"><?php echo date('d', strtotime($event['event_date'])); ?></div>
                            <div class="text-muted fw-bold mb-3 small"><?php echo date('F Y', strtotime($event['event_date'])); ?></div>
                            
                            <h5 class="fw-bold mb-3"><?php echo $event['title']; ?></h5>
                            <p class="text-muted small mb-4"><?php echo $event['description']; ?></p>
                            
                            <div class="bg-light p-3 rounded">
                                <div class="small mb-1"><i class="fas fa-clock me-2 text-primary"></i> <?php echo formatTime($event['event_time']); ?></div>
                                <div class="small"><i class="fas fa-map-marker-alt me-2 text-danger"></i> <?php echo $event['location']; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>
