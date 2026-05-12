<?php
require_once 'config/init.php';

try {
    // Add column to users table
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS last_notif_seen TIMESTAMP NULL");
    // Add column to admins table
    $pdo->exec("ALTER TABLE admins ADD COLUMN IF NOT EXISTS last_notif_seen TIMESTAMP NULL");
    
    echo "✅ नोटिफिकेशन पाहण्याची वेळ ट्रॅक करण्यासाठी कॉलम्स जोडले गेले आहेत!";
} catch (PDOException $e) {
    echo "❌ त्रुटी: " . $e->getMessage();
}
?>
