<?php
require_once 'config/init.php';

try {
    // 1. Finance Groups
    $pdo->exec("CREATE TABLE IF NOT EXISTS finance_groups (
        id INT AUTO_INCREMENT PRIMARY KEY,
        group_name VARCHAR(255) NOT NULL,
        monthly_contribution DECIMAL(10,2) DEFAULT 500.00,
        total_fund DECIMAL(15,2) DEFAULT 0.00,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 2. Finance Loans
    $pdo->exec("CREATE TABLE IF NOT EXISTS finance_loans (
        id INT AUTO_INCREMENT PRIMARY KEY,
        group_id INT NOT NULL,
        user_id INT NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        interest_rate DECIMAL(5,2) DEFAULT 1.50, -- Monthly interest
        status ENUM('pending', 'active', 'repaid') DEFAULT 'pending',
        issued_at DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (group_id) REFERENCES finance_groups(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // 3. Finance Contributions
    $pdo->exec("CREATE TABLE IF NOT EXISTS finance_contributions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        group_id INT NOT NULL,
        user_id INT NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        month_year VARCHAR(20) NOT NULL,
        paid_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (group_id) REFERENCES finance_groups(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    echo "✅ फायनान्स व व्याज प्रणाली (Finance System) टेबल्स यशस्वीरित्या तयार झाले आहेत!";
} catch (PDOException $e) {
    echo "❌ त्रुटी: " . $e->getMessage();
}
?>
