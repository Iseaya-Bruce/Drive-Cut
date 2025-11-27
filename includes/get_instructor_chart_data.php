<?php
session_start();
include '../includes/auth.php';
include '../includes/config.php';

header('Content-Type: application/json');

if (!isset($_GET['user_id']) || !isset($_GET['role'])) {
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}

$user_id = (int)$_GET['user_id'];
$role = $_GET['role'];

// Validate role
$allowedRoles = ['instructor', 'barber'];
if (!in_array($role, $allowedRoles)) {
    echo json_encode(['error' => 'Invalid role']);
    exit;
}

$stmt = $pdo->prepare("SELECT
    COUNT(CASE WHEN rating_text = 'excellent' THEN 1 END) as excellent,
    COUNT(CASE WHEN rating_text = 'good' THEN 1 END) as good,
    COUNT(CASE WHEN rating_text = 'neutral' THEN 1 END) as neutral,
    COUNT(CASE WHEN rating_text = 'poor' THEN 1 END) as poor,
    COUNT(CASE WHEN rating_text = 'very poor' THEN 1 END) as very_poor
FROM ratings
    WHERE rated_user_id = ? AND role = ?
");

$stmt->execute([$user_id, $role]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($data);
