<?php
require_once 'config/init.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS jobs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        posted_by INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        job_type ENUM('शेतीकाम', 'दुकान', 'शिकाऊ काम', 'मजुरी', 'इतर') NOT NULL,
        description TEXT,
        wage VARCHAR(100),
        contact_info VARCHAR(255),
        status ENUM('active', 'closed') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (posted_by) REFERENCES users(id) ON DELETE CASCADE
    )";
    
    $pdo->exec($sql);
    echo "✅ जॉब बोर्ड (Job Board) टेबल यशस्वीरित्या तयार झाला आहे!";
} catch (PDOException $e) {
    echo "❌ त्रुटी: " . $e->getMessage();
}
?>
