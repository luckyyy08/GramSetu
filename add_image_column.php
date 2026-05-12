<?php
require_once 'config/init.php';

try {
    $pdo->exec("ALTER TABLE complaints ADD COLUMN IF NOT EXISTS image VARCHAR(255) NULL");
    echo "✅ तक्रारींसाठी फोटो कॉलम यशस्वीरित्या जोडला गेला आहे!";
} catch (PDOException $e) {
    echo "❌ त्रुटी: " . $e->getMessage();
}
?>
