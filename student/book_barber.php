<?php
session_start();
include '../includes/auth.php';
redirectIfNotStudent();

include '../includes/config.php';



if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $instructor_id = $_POST['barber_id'] ?? $_POST['instructor_id'] ?? null;
    $availability_id = $_POST['time_slot'];
    $role = $_POST['role'] ?? 'barber';
    $student_id = $_SESSION['user_id'];

    // Check if the slot is still available and fetch its time
    $stmt = $pdo->prepare("SELECT day, start_time, status FROM availability WHERE id = ?");
    $stmt->execute([$availability_id]);
    $slot = $stmt->fetch();

    if ($slot && $slot['status'] === 'available') {

        // Combine day and start_time to create a DateTime object
        $slotDateTime = new DateTime($slot['day'] . ' ' . $slot['start_time']);
        $currentDateTime = new DateTime();

        // Check if the slot is more than 24 hours from now
        $interval = $currentDateTime->diff($slotDateTime);
        $hoursDifference = ($interval->days * 24) + $interval->h;

        if ($hoursDifference < 24) {
            error_log("Setting session error...");
            $_SESSION['error'] = "Booking must be made at least 24 hours in advance.";
            header("Location: ../student/barbers.php");
            exit();
        }

        try {
            // Start transaction
            $pdo->beginTransaction();

            // Update availability status
            $stmt = $pdo->prepare("UPDATE availability SET status = 'booked' WHERE id = ?");
            $stmt->execute([$availability_id]);

            // Create booking
            $stmt = $pdo->prepare("INSERT INTO bookings (student_id, instructor_id, availability_id, service_type) VALUES (?, ?, ?, 'barber')");
            $stmt->execute([$student_id, $instructor_id, $availability_id]);

            // Get instructor phone for WhatsApp
            $stmt = $pdo->prepare("SELECT phone FROM users WHERE id = ?");
            $stmt->execute([$instructor_id]);
            $instructor = $stmt->fetch();

            // Get booking details
            $stmt = $pdo->prepare("SELECT a.day, a.start_time, a.end_time FROM availability a WHERE a.id = ?");
            $stmt->execute([$availability_id]);
            $booking = $stmt->fetch();

            $pdo->commit();

            // Prepare WhatsApp message
            $message = urlencode(
                "Hello! I've booked a driving lesson with you on " .
                date('l, F j', strtotime($booking['day'])) .
                " from " . date('g:i A', strtotime($booking['start_time'])) .
                " to " . date('g:i A', strtotime($booking['end_time'])) .
                ". Please confirm if this works for you."
            );

            $whatsapp_url = "https://wa.me/{$instructor['phone']}?text=$message";

            // Redirect to WhatsApp
            header("Location: $whatsapp_url");
            exit();

        } catch (PDOException $e) {
            $pdo->rollBack();
            $_SESSION['error'] = "Booking failed: " . $e->getMessage();
            header("Location: barbers.php");
            exit();
        }

    } else {
        $_SESSION['error'] = "The selected time slot is no longer available.";
        header("Location: barbers.php");
        exit();
    }



} else {
    header("Location: barbers.php");
    exit();
}
?>