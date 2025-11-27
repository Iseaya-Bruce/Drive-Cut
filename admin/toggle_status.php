<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';
redirectIfNotAdmin();

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT status FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

$newStatus = $user['status'] === 'active' ? 'inactive' : 'active';
$update = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
$update->execute([$newStatus, $id]);
header("Location: ../admin/dashboard.php");
exit();
