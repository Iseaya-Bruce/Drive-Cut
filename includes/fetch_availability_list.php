<?php
header('Content-Type: application/json');
include '../includes/config.php';

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;
$role = isset($_GET['role']) ? $_GET['role'] : null;

if (!$user_id || !$role) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing user_id or role']);
    exit;
}

try {
    if ($role === 'instructor') {
       $stmt = $pdo->prepare("
            SELECT id, day, start_time, end_time 
            FROM availability 
            WHERE instructor_id = :user_id 
            AND service_type = 'lesson' 
            AND status = 'available'
            AND day >= CURDATE()
            ORDER BY day, start_time
        ");
    } elseif ($role === 'barber') {
        $stmt = $pdo->prepare("
            SELECT day, start_time, end_time 
            FROM availability 
            WHERE instructor_id = :user_id 
            AND service_type = 'barber' 
            AND status = 'available'
            AND day >= CURDATE()
            ORDER BY day, start_time
        ");
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid role specified']);
        exit;
    }

    $stmt->execute(['user_id' => $user_id]);
    $availability = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($availability);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
