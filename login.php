<?php 
require_once 'config/init.php';

if (isLoggedIn()) {
    redirect('/index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = sanitize($_POST['phone']);
    $password = $_POST['password'];

    if (empty($phone) || empty($password)) {
        $error = "सर्व फील्ड भरणे आवश्यक आहे.";
    } else {
        // 1. Check in users table (by phone)
        $stmt = $pdo->prepare("SELECT * FROM users WHERE phone = ?");
        $stmt->execute([$phone]);
        $user = $stmt->fetch();

        // 2. If not found, check in admins table (by username)
        if (!$user) {
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
            $stmt->execute([$phone]); // Using the same input field
            $user = $stmt->fetch();
            if ($user) {
                $user['full_name'] = $user['username'];
                $user['role'] = 'admin';
            }
        }

        if ($user) {
            // Check password (hash or plain text fallback for manual entries)
            if (password_verify($password, $user['password']) || $password === $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] === 'admin') {
                    redirect('/admin/dashboard.php');
                } else {
                    redirect('/user/dashboard.php');
                }
            } else {
                $error = "पासवर्ड चुकीचा आहे.";
            }
        } else {
            $error = "मोबाईल नंबर/युजरनेम चुकीचा आहे.";
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
                    <h4 class="text-white fw-bold mb-0">ग्रामसेतू मध्ये आपले स्वागत आहे</h4>
                </div>
                <div class="card-body p-4 p-md-5">
                    <?php if ($error): ?>
                        <div class="alert alert-danger border-0 small"><?php echo $error; ?></div>
                    <?php endif; ?>

                <form action="login.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">मोबाईल नंबर</label>
                        <input type="tel" name="phone" class="form-control" placeholder="नोंदणीकृत मोबाईल नंबर" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">पासवर्ड</label>
                        <input type="password" name="password" class="form-control" placeholder="आपला पासवर्ड टाका" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember">
                        <label class="form-check-label" for="remember">मला लक्षात ठेवा</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 mb-3">लॉगिन</button>
                    <div class="text-center">
                        <span>खाते नाही? <a href="register.php">आताच नोंदणी करा</a></span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
