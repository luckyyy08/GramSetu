<?php
require_once 'config/init.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS donations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_name VARCHAR(255) NOT NULL,
        donor_name VARCHAR(255) NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        description TEXT,
        payment_mode ENUM('Cash', 'Online') DEFAULT 'Cash',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    echo "✅ वर्गणी व उत्सव व्यवस्थापन (Donations) टेबल यशस्वीरित्या तयार झाला आहे!";
} catch (PDOException $e) {
    echo "❌ त्रुटी: " . $e->getMessage();
}
?>
