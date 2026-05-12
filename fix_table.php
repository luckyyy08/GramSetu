<?php
require_once 'config/init.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS admin_tasks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        task_text VARCHAR(255) NOT NULL,
        is_completed BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    echo "✅ admin_tasks टेबल यशस्वीरित्या तयार झाला आहे!";
} catch (PDOException $e) {
    echo "❌ त्रुटी: " . $e->getMessage();
}
?>
