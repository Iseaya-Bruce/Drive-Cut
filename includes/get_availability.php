<?php
file_put_contents("debug_availability.log", print_r($_POST, true));

header('Content-Type: application/json');
session_start();

require_once '../includes/config.php';
require_once '../includes/auth.php';

// Debugging helper (temporary, remove in production)
file_put_contents("debug_availability.log", print_r($_POST, true));

$instructor_id = $_POST['instructor_id'] ?? $_POST['barber_id'] ?? null;
$role = $_POST['role'] ?? null;

if ($instructor_id && $role) {

    // Default date range
    $start = $_POST['start'] ?? date('Y-m-01');
    $end = $_POST['end'] ?? date('Y-m-t');

    try {
        // Get availability slots
        $service_type = $_POST['service_type'];

        $stmt = $pdo->prepare("
            SELECT 
                a.id,
                a.day AS start,
                a.start_time,
                a.end_time,
                a.status,
                CONCAT(a.day, ' ', a.start_time) AS start_datetime,
                CONCAT(a.day, ' ', a.end_time) AS end_datetime
            FROM 
                availability a
            WHERE 
                a.instructor_id = ?
                AND a.role = ?
                AND a.service_type = ?
                AND a.day BETWEEN ? AND ?
            ORDER BY 
                a.day, a.start_time
        ");
        $stmt->execute([$instructor_id, $role, $service_type, $start, $end]);
        $slots = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get days with availability for background highlighting
        $daysStmt = $pdo->prepare("
            SELECT DISTINCT day 
            FROM availability 
            WHERE instructor_id = ? 
            AND role = ?
            AND service_type = ?
            AND day BETWEEN ? AND ?
            AND status = 'available'
        ");
        $daysStmt->execute([$instructor_id, $role, $service_type, $start, $end]);
        $availableDays = $daysStmt->fetchAll(PDO::FETCH_COLUMN);

        // Convert to FullCalendar format
        $events = [];

        foreach ($slots as $slot) {
            $events[] = [
                'id' => $slot['id'],
                'title' => ($slot['status'] === 'available') ? 'Available' : 'Booked',
                'start' => $slot['start_datetime'],
                'end' => $slot['end_datetime'],
                'color' => ($slot['status'] === 'available') ? '#28a745' : '#dc3545',
                'extendedProps' => [
                    'status' => $slot['status'],
                    'start_time' => $slot['start_time'],
                    'end_time' => $slot['end_time']
                ]
            ];
        }

        foreach ($availableDays as $day) {
            $events[] = [
                'start' => $day,
                'display' => 'background',
                'color' => 'rgba(40, 167, 69, 0.1)' // Light green background
            ];
        }

        echo json_encode($events);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(403);
    echo json_encode(['error' => 'Missing instructor ID or role']);
}
