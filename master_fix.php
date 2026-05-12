<?php
require_once 'config/init.php';

echo "<h2>🛠️ GramSetu Master Fix System</h2>";

try {
    // 1. Complaints Image Column
    $pdo->exec("ALTER TABLE complaints ADD COLUMN IF NOT EXISTS image VARCHAR(255) NULL");
    echo "✅ Complaints Image Column: OK<br>";

    // 2. Notifications Columns
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS last_notif_seen TIMESTAMP NULL");
    $pdo->exec("ALTER TABLE admins ADD COLUMN IF NOT EXISTS last_notif_seen TIMESTAMP NULL");
    echo "✅ Notification Tracking: OK<br>";

    // 3. Certificates Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS certificates (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        certificate_type VARCHAR(100) NOT NULL,
        reason TEXT,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        admin_remark TEXT,
        applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    echo "✅ Certificates Table: OK<br>";

    // 4. Polls Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS polls (
        id INT AUTO_INCREMENT PRIMARY KEY,
        question TEXT NOT NULL,
        option_1 VARCHAR(255) NOT NULL,
        option_2 VARCHAR(255) NOT NULL,
        status ENUM('active', 'closed') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✅ Polls Table: OK<br>";

    // 5. Poll Votes Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS poll_votes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        poll_id INT NOT NULL,
        user_id INT NOT NULL,
        vote_option INT NOT NULL,
        voted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_vote (poll_id, user_id),
        FOREIGN KEY (poll_id) REFERENCES polls(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    echo "✅ Poll Votes Table: OK<br>";

    echo "<br><h3 style='color: green;'>बधाई! सर्व तांत्रिक अडचणी दूर झाल्या आहेत. आता ॲडमिन पॅनेल व्यवस्थित चालेल.</h3>";
    echo "<a href='admin/dashboard.php'>डॅशबोर्डवर जा</a>";

} catch (PDOException $e) {
    echo "<h3 style='color: red;'>❌ त्रुटी: " . $e->getMessage() . "</h3>";
}
?>
