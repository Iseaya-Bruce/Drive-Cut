<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid booking ID";
    header("Location: instructors.php"); // fallback page
    exit();
}

$booking_id = $_GET['id'];
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

try {
    // Get the booking and verify ownership
    $stmt = $pdo->prepare("
        SELECT b.id, b.availability_id, b.student_id, b.instructor_id
        FROM bookings b
        WHERE b.id = ?
    ");
    $stmt->execute([$booking_id]);
    $booking = $stmt->fetch();

    if (!$booking) {
        $_SESSION['error'] = "Booking not found.";
        header("Location: instructors.php");
        exit();
    }

    // Student cancel check
    if ($role === 'student' && $booking['student_id'] != $user_id) {
        $_SESSION['error'] = "Unauthorized cancellation.";
        header("Location: instructors.php");
        exit();
    }

    // Instructor cancel check (for their own slots)
    if ($role === 'instructor' && $booking['instructor_id'] != $user_id) {
        $_SESSION['error'] = "Unauthorized cancellation.";
        header("Location: schedule.php");
        exit();
    }

    // Begin transaction
    $pdo->beginTransaction();

    // Delete the booking
    $delete = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
    $delete->execute([$booking_id]);

    // Set availability slot back to 'available'
    $update = $pdo->prepare("UPDATE availability SET status = 'available' WHERE id = ?");
    $update->execute([$booking['availability_id']]);

    $pdo->commit();

    $_SESSION['success'] = "Booking cancelled successfully.";
    $redirect = ($role === 'student') ? 'dashboard.php' : 'schedule.php';
    header("Location: $redirect");
    exit();

} catch (PDOException $e) {
    $pdo->rollBack();
    $_SESSION['error'] = "Error cancelling booking: " . $e->getMessage();
    header("Location: instructors.php");
    exit();
}
?>
