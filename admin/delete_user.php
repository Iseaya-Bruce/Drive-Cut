<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

redirectIfNotAdmin();

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Soft delete: mark user as inactive
    $stmt = $pdo->prepare("UPDATE users SET status = 'inactive' WHERE id = ?");
    if ($stmt->execute([$id])) {
        $_SESSION['success'] = "User marked as inactive.";
    } else {
        $_SESSION['error'] = "Failed to mark user inactive.";
    }
}

header("Location: ../admin/admin.php");
exit();
