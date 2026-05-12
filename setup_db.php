<?php
// Setup Database and Tables
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'gramsetu';

try {
    // 1. Connect to MySQL
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Create Database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    $pdo->exec("USE $dbname");
    echo "✅ डेटाबेस 'gramsetu' तयार आहे.<br>";

    // 3. Create Tables
    $sql = "
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE,
        phone VARCHAR(15) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        address TEXT,
        profile_pic VARCHAR(255) DEFAULT 'default.png',
        role ENUM('user', 'admin') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS notices (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        content TEXT NOT NULL,
        category VARCHAR(50),
        is_important BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS complaints (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        title VARCHAR(255) NOT NULL,
        category ENUM('water', 'roads', 'lights', 'cleanliness', 'other') NOT NULL,
        description TEXT NOT NULL,
        image_path VARCHAR(255),
        status ENUM('pending', 'in-progress', 'resolved', 'rejected') DEFAULT 'pending',
        admin_remark TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        event_date DATE NOT NULL,
        event_time TIME,
        location VARCHAR(255),
        category ENUM('gram_sabha', 'festival', 'meeting', 'other') DEFAULT 'other',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    ";

    $pdo->exec($sql);
    echo "✅ सर्व टेबल्स तयार झाली आहेत.<br>";

    // 4. Insert Admin
    $phone = '0000000000';
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    
    $check = $pdo->prepare("SELECT id FROM users WHERE phone = ?");
    $check->execute([$phone]);
    
    if (!$check->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO users (full_name, phone, password, role) VALUES ('Admin', ?, ?, 'admin')");
        $stmt->execute([$phone, $password]);
        echo "✅ अॅडमिन यशस्वीरित्या तयार झाला आहे!<br>";
    } else {
        echo "ℹ️ अॅडमिन आधीच अस्तित्वात आहे.<br>";
    }

    echo "<br>🚀 आता तुम्ही लॉगिन करू शकता: <a href='login.php'>येथे क्लिक करा</a>";
    echo "<br><b>User:</b> 0000000000 | <b>Pass:</b> admin123";

} catch (PDOException $e) {
    echo "❌ त्रुटी: " . $e->getMessage();
}
?>
