<?php
require_once 'config/init.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS businesses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        owner_id INT NOT NULL,
        business_name VARCHAR(255) NOT NULL,
        category ENUM('दुकान', 'सेवा (Services)', 'दवाखाना', 'हॉटेल', 'शिक्षण', 'इतर') NOT NULL,
        contact_number VARCHAR(20) NOT NULL,
        address TEXT,
        description TEXT,
        image VARCHAR(255),
        is_verified TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    
    $pdo->exec($sql);
    echo "✅ बिझनेस डिरेक्टरी (Business Directory) टेबल यशस्वीरित्या तयार झाला आहे!";
} catch (PDOException $e) {
    echo "❌ त्रुटी: " . $e->getMessage();
}
?>
