<?php 
require_once 'config/init.php';

if (!isLoggedIn()) {
    redirect('/login.php');
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $full_name = sanitize($_POST['full_name']);
        $email = sanitize($_POST['email']);
        $address = sanitize($_POST['address']);
        
        // Handle Profile Pic
        $profile_pic = $user['profile_pic'];
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
            $allowed = ['jpg', 'jpeg', 'png'];
            $ext = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $new_name = 'user_' . $user_id . '_' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], 'uploads/' . $new_name)) {
                    $profile_pic = $new_name;
                }
            }
        }

        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, address = ?, profile_pic = ? WHERE id = ?");
        if ($stmt->execute([$full_name, $email, $address, $profile_pic, $user_id])) {
            $_SESSION['full_name'] = $full_name;
            setFlash('success', 'प्रोफाईल यशस्वीरित्या अपडेट झाली आहे.');
            redirect('/profile.php');
        }
    }

    if (isset($_POST['change_password'])) {
        $old_pass = $_POST['old_password'];
        $new_pass = $_POST['new_password'];
        $confirm_pass = $_POST['confirm_password'];

        if (password_verify($old_pass, $user['password'])) {
            if ($new_pass === $confirm_pass) {
                $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed, $user_id]);
                setFlash('success', 'पासवर्ड यशस्वीरित्या बदलला आहे.');
                redirect('/profile.php');
            } else {
                $error = "नवीन पासवर्ड जुळत नाहीत.";
            }
        } else {
            $error = "जुना पासवर्ड चुकीचा आहे.";
        }
    }
}

include_once 'includes/header.php'; 
?>

<div class="container py-5">
    <div class="row">
        <!-- Profile Overview Card -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="bg-primary py-5 text-center position-relative">
                    <div class="position-absolute w-100 h-100 top-0 start-0 opacity-10" style="background-image: url('https://www.transparenttextures.com/patterns/cubes.png');"></div>
                </div>
                <div class="card-body text-center mt-n5 px-4 pb-4">
                    <div class="position-relative d-inline-block mt-n5 mb-3">
                        <img src="uploads/<?php echo $user['profile_pic']; ?>" class="rounded-circle border border-4 border-white shadow" width="130" height="130" style="object-fit: cover; background: white;">
                    </div>
                    <h4 class="fw-bold mb-1"><?php echo $user['full_name']; ?></h4>
                    <p class="text-muted small mb-3"><i class="fas fa-phone-alt me-1"></i> <?php echo $user['phone']; ?></p>
                    <div class="badge bg-success bg-opacity-10 text-success rounded-pill px-4 py-2 small fw-bold">
                        <i class="fas fa-check-circle me-1"></i> सक्रिय नागरिक
                    </div>
                </div>
            </div>

            <!-- Password Change Card -->
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h5 class="fw-bold mb-4 d-flex align-items-center">
                    <i class="fas fa-lock me-2 text-primary"></i> सुरक्षा सेटिंग्ज
                </h5>
                <?php if ($error): ?>
                    <div class="alert alert-danger small border-0 py-2"><?php echo $error; ?></div>
                <?php endif; ?>
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">जुना पासवर्ड</label>
                        <input type="password" name="old_password" class="form-control bg-light border-0 py-2" placeholder="••••••••" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">नवीन पासवर्ड</label>
                        <input type="password" name="new_password" class="form-control bg-light border-0 py-2" placeholder="किमान ६ अक्षरे" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">पासवर्डची पुष्टी करा</label>
                        <input type="password" name="confirm_password" class="form-control bg-light border-0 py-2" placeholder="पुन्हा टाका" required>
                    </div>
                    <button type="submit" name="change_password" class="btn btn-dark w-100 py-2 rounded-3 fw-bold">
                        पासवर्ड अपडेट करा
                    </button>
                </form>
            </div>
        </div>

        <!-- Profile Edit Card -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <h3 class="fw-bold mb-0">प्रोफाईल माहिती</h3>
                    <a href="user/dashboard.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="fas fa-arrow-left me-1"></i> डॅशबोर्ड
                    </a>
                </div>

                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small"><i class="fas fa-user me-2 text-primary"></i>पूर्ण नाव</label>
                            <input type="text" name="full_name" class="form-control py-3 px-4 bg-light border-0 rounded-3" value="<?php echo $user['full_name']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small"><i class="fas fa-envelope me-2 text-primary"></i>ईमेल पत्ता</label>
                            <input type="email" name="email" class="form-control py-3 px-4 bg-light border-0 rounded-3" value="<?php echo $user['email']; ?>" placeholder="उदा. lokesh@gmail.com">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-muted small"><i class="fas fa-map-marker-alt me-2 text-primary"></i>पत्ता (गाव/वॉर्ड)</label>
                            <textarea name="address" class="form-control py-3 px-4 bg-light border-0 rounded-3" rows="4" placeholder="तुमचा पूर्ण पत्ता लिहा..."><?php echo $user['address']; ?></textarea>
                        </div>
                        <div class="col-md-12">
                            <div class="p-4 bg-light rounded-4 border-2 border-dashed text-center">
                                <label class="form-label fw-bold d-block mb-3">प्रोफाईल फोटो बदला</label>
                                <div class="d-flex align-items-center justify-content-center">
                                    <input type="file" name="profile_pic" class="form-control w-auto d-inline-block" accept="image/*">
                                </div>
                                <small class="text-muted d-block mt-2">फक्त JPG, PNG फाईल्स (कमाल २ MB)</small>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 text-end">
                        <button type="submit" name="update_profile" class="btn btn-primary btn-lg px-5 py-3 rounded-pill fw-bold shadow-sm">
                            <i class="fas fa-save me-2"></i> माहिती जतन करा
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.mt-n5 { margin-top: -3rem !important; }
.extra-small { font-size: 0.75rem; }
.border-dashed { border-style: dashed !important; border-color: #dee2e6 !important; }
</style>

<?php include_once 'includes/footer.php'; ?>
