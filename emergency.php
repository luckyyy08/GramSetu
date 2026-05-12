<?php 
require_once 'config/init.php';
include_once 'includes/header.php'; 

// Predefined contacts if DB is empty
$contacts = [
    ['name' => 'ग्रामपंचायत कार्यालय', 'category' => 'प्रशासन', 'phone' => '0231-252525'],
    ['name' => 'रुग्णवाहिका (सरकारी)', 'category' => 'आरोग्य', 'phone' => '108'],
    ['name' => 'पोलीस स्टेशन', 'category' => 'सुरक्षा', 'phone' => '100'],
    ['name' => 'अग्निशमन दल', 'category' => 'आणीबाणी', 'phone' => '101'],
    ['name' => 'प्राथमिक आरोग्य केंद्र', 'category' => 'आरोग्य', 'phone' => '0231-262626'],
];
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold">आणीबाणीचे संपर्क</h2>
        <p class="text-muted">गरजेच्या वेळी त्वरित मदतीसाठी संपर्क साधा</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <?php foreach($contacts as $contact): ?>
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center p-4">
                        <div>
                            <span class="badge bg-danger bg-opacity-10 text-danger mb-2"><?php echo $contact['category']; ?></span>
                            <h5 class="fw-bold mb-0"><?php echo $contact['name']; ?></h5>
                        </div>
                        <div class="text-end">
                            <a href="tel:<?php echo $contact['phone']; ?>" class="btn btn-primary btn-lg rounded-pill px-4">
                                <i class="fas fa-phone-alt me-2"></i> <?php echo $contact['phone']; ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
