<?php 
require_once 'config/init.php';

if (isLoggedIn()) {
    redirect('/index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize($_POST['full_name']);
    $phone = sanitize($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($full_name) || empty($phone) || empty($password)) {
        $error = "सर्व फील्ड भरणे आवश्यक आहे.";
    } elseif ($password !== $confirm_password) {
        $error = "पासवर्ड जुळत नाहीत.";
    } else {
        // Check if phone exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE phone = ?");
        $stmt->execute([$phone]);
        if ($stmt->fetch()) {
            $error = "हा मोबाईल नंबर आधीच नोंदणीकृत आहे.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (full_name, phone, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$full_name, $phone, $hashed_password])) {
                setFlash('success', 'नोंदणी यशस्वी! कृपया लॉगिन करा.');
                redirect('/login.php');
            } else {
                $error = "काहीतरी चूक झाली. कृपया पुन्हा प्रयत्न करा.";
            }
        }
    }
}

include_once 'includes/header.php'; 
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="bg-primary p-4 text-center position-relative">
                    <img src="<?php echo APP_URL; ?>/assets/img/sqaure logo.png" alt="Logo" width="100" class="bg-white p-3 rounded-circle mb-3 shadow-sm">
                    <h4 class="text-white fw-bold mb-0">ग्रामसेतू नोंदणी</h4>
                </div>
                <div class="card-body p-4 p-md-5">
                    <?php if ($error): ?>
                        <div class="alert alert-danger border-0 small"><?php echo $error; ?></div>
                    <?php endif; ?>

                <form action="register.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">पूर्ण नाव</label>
                        <input type="text" name="full_name" class="form-control" placeholder="उदा. राजेश पाटील" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">मोबाईल नंबर</label>
                        <input type="tel" name="phone" class="form-control" placeholder="१० अंकी मोबाईल नंबर" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">पासवर्ड</label>
                        <input type="password" name="password" class="form-control" placeholder="किमान ६ अक्षरे" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">पासवर्डची पुष्टी करा</label>
                        <input type="password" name="confirm_password" class="form-control" placeholder="पासवर्ड पुन्हा टाका" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 mb-3">नोंदणी पूर्ण करा</button>
                    <div class="text-center">
                        <span>आधीच खाते आहे? <a href="login.php">लॉगिन करा</a></span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
