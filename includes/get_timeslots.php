<?php
session_start();
require_once 'config.php';
// ⚠️ Do not include auth.php if it echoes/redirects. Only use role from session.
header('Content-Type: application/json; charset=utf-8');

// Collect inputs safely
$instructor_id = $_POST['instructor_id'] ?? $_POST['barber_id'] ?? null;
$date          = $_POST['date'] ?? null;
$role          = $_POST['role'] ?? ($_SESSION['role'] ?? null);
$service_type  = $_POST['service_type'] ?? (($role === 'barber') ? 'barber' : 'lesson');

// Debug logging (only server-side, not output)
file_put_contents(__DIR__ . '/debug_timeslots.log', print_r($_POST, true), FILE_APPEND);

if (!$instructor_id || !$date || !$role) {
    echo json_encode(['error' => 'Missing ID, date, or role']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT id, start_time, end_time
        FROM availability
        WHERE instructor_id = ? 
          AND role = ? 
          AND service_type = ?
          AND day = ? 
          AND status = 'available'
        ORDER BY start_time
    ");
    $stmt->execute([$instructor_id, $role, $service_type, $date]);
    $slots = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Always return an array, never "false"
    echo json_encode($slots ?: []);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
