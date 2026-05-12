<?php
require_once 'config/init.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        seller_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        category VARCHAR(100),
        image VARCHAR(255),
        status ENUM('available', 'out_of_stock') DEFAULT 'available',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    
    $pdo->exec($sql);
    echo "✅ महिला बाजारपेठ (Marketplace) टेबल यशस्वीरित्या तयार झाला आहे!";
} catch (PDOException $e) {
    echo "❌ त्रुटी: " . $e->getMessage();
}
?>
