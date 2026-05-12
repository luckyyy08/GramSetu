<?php
require_once 'config/init.php';

try {
    // 1. SHG Groups Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS shg_groups (
        id INT AUTO_INCREMENT PRIMARY KEY,
        group_name VARCHAR(255) NOT NULL,
        leader_id INT NOT NULL,
        monthly_amount DECIMAL(10,2) DEFAULT 100.00,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (leader_id) REFERENCES users(id)
    )");

    // 2. SHG Members Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS shg_members (
        id INT AUTO_INCREMENT PRIMARY KEY,
        group_id INT NOT NULL,
        user_id INT NOT NULL,
        joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_membership (group_id, user_id),
        FOREIGN KEY (group_id) REFERENCES shg_groups(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // 3. SHG Savings Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS shg_savings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        group_id INT NOT NULL,
        user_id INT NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        month_year VARCHAR(20) NOT NULL, -- e.g., 'May 2024'
        paid_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (group_id) REFERENCES shg_groups(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // 4. SHG Loans Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS shg_loans (
        id INT AUTO_INCREMENT PRIMARY KEY,
        group_id INT NOT NULL,
        user_id INT NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        interest_rate DECIMAL(5,2) DEFAULT 2.00,
        purpose TEXT,
        status ENUM('pending', 'approved', 'rejected', 'repaid') DEFAULT 'pending',
        applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (group_id) REFERENCES shg_groups(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    echo "✅ बचत गट (Bachat Gat) सिस्टिमचे सर्व टेबल्स यशस्वीरित्या तयार झाले आहेत!";
} catch (PDOException $e) {
    echo "❌ त्रुटी: " . $e->getMessage();
}
?>
