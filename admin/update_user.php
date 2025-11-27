<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';
redirectIfNotAdmin();

$id = $_POST['id'];
$full_name = $_POST['full_name'];
$username = $_POST['username'];
$role = $_POST['role'];

$stmt = $pdo->prepare("UPDATE users SET full_name = ?, username = ?, role = ? WHERE id = ?");
$stmt->execute([$full_name, $username, $role, $id]);

header("Location: ../admin/dashboard.php");
exit();
