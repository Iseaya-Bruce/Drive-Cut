<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid slot ID.";
    header("Location: schedule.php");
    exit();
}

$slot_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

try {
    // Verify the slot belongs to this user
    $stmt = $pdo->prepare("SELECT * FROM availability WHERE id = ? AND instructor_id = ?");
    $stmt->execute([$slot_id, $user_id]);
    $slot = $stmt->fetch();

    if (!$slot) {
        $_SESSION['error'] = "Slot not found or unauthorized.";
        header("Location: schedule.php");
        exit();
    }

    // Only allow removal if slot is not booked
    if ($slot['status'] === 'booked') {
        $_SESSION['error'] = "Cannot remove a booked slot.";
        header("Location: schedule.php");
        exit();
    }

    // Delete the slot
    $delete = $pdo->prepare("DELETE FROM availability WHERE id = ?");
    $delete->execute([$slot_id]);

    $_SESSION['success'] = "Slot removed successfully.";
    header("Location: schedule.php");
    exit();

} catch (PDOException $e) {
    $_SESSION['error'] = "Error removing slot: " . $e->getMessage();
    header("Location: schedule.php");
    exit();
}
?>
