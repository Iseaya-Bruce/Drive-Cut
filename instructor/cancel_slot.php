<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid ID.";
    header("Location: schedule.php");
    exit();
}

$record_id = $_GET['id'];
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

try {
    $pdo->beginTransaction();

    // 1️⃣ Check if it's a booking
    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ?");
    $stmt->execute([$record_id]);
    $booking = $stmt->fetch();

    if ($booking) {
        // Cancel booking & free slot
        if ($role === 'student' && $booking['student_id'] != $user_id) {
            $_SESSION['error'] = "Unauthorized cancellation.";
            header("Location: instructors.php");
            exit();
        }

        if (($role === 'instructor' || $role === 'barber') && $booking['instructor_id'] != $user_id) {
            $_SESSION['error'] = "Unauthorized cancellation.";
            header("Location: schedule.php");
            exit();
        }

        // Delete booking
        $delete = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
        $delete->execute([$record_id]);

        // Free up the slot
        $update = $pdo->prepare("UPDATE availability SET status = 'available' WHERE id = ?");
        $update->execute([$booking['availability_id']]);

        $_SESSION['success'] = "Booking cancelled and slot freed.";
    } else {
        // 2️⃣ Else: Check if it's an availability slot
        $stmt = $pdo->prepare("SELECT * FROM availability WHERE id = ?");
        $stmt->execute([$record_id]);
        $slot = $stmt->fetch();

        if (!$slot) {
            $_SESSION['error'] = "Slot not found.";
            header("Location: schedule.php");
            exit();
        }

        if ($slot['instructor_id'] != $user_id) {
            $_SESSION['error'] = "Unauthorized action.";
            header("Location: schedule.php");
            exit();
        }

        if ($slot['status'] === 'booked') {
            $_SESSION['error'] = "Cannot remove a booked slot.";
            header("Location: schedule.php");
            exit();
        }

        // Delete availability slot
        $delete = $pdo->prepare("DELETE FROM availability WHERE id = ?");
        $delete->execute([$record_id]);

        $_SESSION['success'] = "Slot removed successfully.";
    }

    $pdo->commit();

    // Redirect
    if ($role === 'student') {
        header("Location: instructors.php");
    } else {
        header("Location: schedule.php");
    }
    exit();

} catch (PDOException $e) {
    $pdo->rollBack();
    $_SESSION['error'] = "Error: " . $e->getMessage();
    header("Location: schedule.php");
    exit();
}
?>
