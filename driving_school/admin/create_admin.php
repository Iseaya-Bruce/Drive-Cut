<?php
require_once '../includes/config.php';

$username = 'admin';
$password = password_hash('admin', PASSWORD_DEFAULT);
$email = 'admin@example.com';
$role = 'admin';
$full_name = 'Administrator';
$phone = '0000000000';
$status = 'active';

// Check if admin already exists
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);

if ($stmt->rowCount() > 0) {
    echo "❗ Admin user already exists.";
} else {
    try {
        $insert = $pdo->prepare("INSERT INTO users (username, password, email, role, full_name, phone, status)
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insert->execute([$username, $password, $email, $role, $full_name, $phone, $status]);

        echo "✅ Admin user created successfully!";
    } catch (PDOException $e) {
        echo "❌ Error creating admin user: " . $e->getMessage();
    }
}
?>
