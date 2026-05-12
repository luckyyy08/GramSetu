<?php 
require_once '../config/init.php';

if (!isLoggedIn()) {
    redirect('/login.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title']);
    $category = sanitize($_POST['category']);
    $description = sanitize($_POST['description']);
    $user_id = $_SESSION['user_id'];
    
    $image_name = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = 'comp_' . uniqid() . '.' . $ext;
            $destination = '../uploads/complaints/' . $new_filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $image_name = $new_filename;
            }
        }
    }

    if (empty($title) || empty($description) || empty($category)) {
        $error = "कृपया सर्व आवश्यक माहिती भरा.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO complaints (user_id, title, category, description, image, status) VALUES (?, ?, ?, ?, ?, 'pending')");
        if ($stmt->execute([$user_id, $title, $category, $description, $image_name])) {
            setFlash('success', 'तक्रार यशस्वीरित्या नोंदवली गेली आहे.');
            redirect('/user/complaints.php');
        } else {
            $error = "तक्रार नोंदवताना त्रुटी आली.";
        }
    }
}

include_once '../includes/header.php'; 
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4">
                <div class="d-flex align-items-center mb-4">
                    <a href="dashboard.php" class="btn btn-outline-secondary me-3"><i class="fas fa-arrow-left"></i></a>
                    <h3 class="fw-bold mb-0">नवीन तक्रार नोंदवा</h3>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form action="new_complaint.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">तक्रारीचा विषय *</label>
                        <input type="text" name="title" class="form-control" placeholder="थोडक्यात विषय सांगा" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">तक्रारीचा प्रकार *</label>
                        <select name="category" class="form-select" required>
                            <option value="">निवडा...</option>
                            <option value="water">पाणी पुरवठा</option>
                            <option value="roads">रस्ते</option>
                            <option value="lights">दिवाबत्ती (वीज)</option>
                            <option value="cleanliness">स्वच्छता</option>
                            <option value="other">इतर</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">तक्रारीचे सविस्तर वर्णन *</label>
                        <textarea name="description" class="form-control" rows="5" placeholder="तक्रारीबद्दल सविस्तर माहिती लिहा..." required></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">फोटो अपलोड करा (पर्यायी)</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <div class="form-text">परिस्थिती स्पष्ट करण्यासाठी फोटो फायदेशीर ठरेल.</div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100">तक्रार दाखल करा</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>
