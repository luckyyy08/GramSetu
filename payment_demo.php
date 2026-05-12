<?php 
require_once 'config/init.php';

if (!isLoggedIn()) {
    redirect('/login.php');
}

$amount = isset($_GET['amount']) ? $_GET['amount'] : 100;
$purpose = isset($_GET['purpose']) ? $_GET['purpose'] : 'वर्गणी';
?>

<!DOCTYPE html>
<html lang="mr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Razorpay Payment Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: rgba(0,0,0,0.05); }
        .checkout-card { max-width: 400px; margin: 50px auto; border: none; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .checkout-header { background: #3395ff; color: white; padding: 30px; text-align: center; }
        .razorpay-logo { width: 120px; filter: brightness(0) invert(1); }
        .payment-method { border: 1px solid #eee; border-radius: 10px; padding: 15px; margin-bottom: 10px; cursor: pointer; transition: 0.3s; }
        .payment-method:hover { border-color: #3395ff; background: #f0f7ff; }
    </style>
</head>
<body>

<div class="card checkout-card">
    <div class="checkout-header">
        <img src="https://razorpay.com/assets/razorpay-glyph.svg" class="razorpay-logo mb-3" alt="Razorpay">
        <h5 class="mb-0">ग्रामसेतू डिजिटल पेमेंट</h5>
        <div class="mt-2 small opacity-75">सुरक्षित आणि जलद पेमेंट</div>
    </div>
    <div class="card-body p-4">
        <div class="d-flex justify-content-between mb-4">
            <div class="text-muted small">भरण्यासाठी रक्कम:</div>
            <div class="h4 fw-bold text-dark mb-0">₹<?php echo $amount; ?></div>
        </div>
        
        <h6 class="fw-bold mb-3 small text-muted">पेमेंट पद्धत निवडा</h6>
        
        <div class="payment-method d-flex align-items-center" onclick="processPayment()">
            <i class="fab fa-google-pay fa-2x text-primary me-3"></i>
            <div>
                <div class="fw-bold small">Google Pay / PhonePe</div>
                <div class="extra-small text-muted">UPI द्वारे त्वरित पेमेंट</div>
            </div>
        </div>

        <div class="payment-method d-flex align-items-center" onclick="processPayment()">
            <i class="fas fa-credit-card fa-lg text-secondary me-3"></i>
            <div>
                <div class="fw-bold small">Card / NetBanking</div>
                <div class="extra-small text-muted">सर्व बँकांचे कार्ड चालतील</div>
            </div>
        </div>

        <div class="mt-4">
            <button onclick="processPayment()" class="btn btn-primary w-100 py-3 fw-bold rounded-pill shadow-none" style="background: #3395ff;">
                PAY ₹<?php echo $amount; ?>
            </button>
        </div>
        
        <div class="text-center mt-4">
            <img src="https://razorpay.com/assets/trusted-badge.png" width="150" alt="Trusted">
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function processPayment() {
        Swal.fire({
            title: 'पेमेंट प्रक्रिया सुरू आहे...',
            html: 'कृपया स्क्रीन बंद करू नका',
            timer: 2000,
            timerProgressBar: true,
            didOpen: () => { Swal.showLoading() }
        }).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'पेमेंट यशस्वी झाले!',
                text: 'तुमची वर्गणी जमा करण्यात आली आहे.',
                confirmButtonText: 'डॅशबोर्डवर जा'
            }).then(() => {
                window.location.href = 'user/donations.php?status=success';
            });
        });
    }
</script>

</body>
</html>
