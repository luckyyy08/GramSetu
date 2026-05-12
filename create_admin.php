<?php
require_once 'config/init.php';

try {
    $full_name = 'Admin';
    $phone = '0000000000';
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $role = 'admin';

    // Check if exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE phone = ?");
    $stmt->execute([$phone]);
    
    if ($stmt->fetch()) {
        echo "<h3 style='color: orange;'>अॅडमिन आधीच अस्तित्वात आहे!</h3>";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (full_name, phone, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$full_name, $phone, $password, $role]);
        echo "<h3 style='color: green;'>अॅडमिन यशस्वीरित्या तयार झाला आहे!</h3>";
    }
    
    echo "<p>आता तुम्ही <a href='login.php'>येथे क्लिक करून</a> लॉगिन करू शकता.</p>";
    echo "<p><b>User:</b> 0000000000<br><b>Pass:</b> admin123</p>";

} catch (PDOException $e) {
    echo "<h3 style='color: red;'>त्रुटी: " . $e->getMessage() . "</h3>";
}
?>
