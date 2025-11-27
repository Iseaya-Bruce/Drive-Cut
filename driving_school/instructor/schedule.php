<?php
include '../includes/auth.php';
require_once '../includes/functions.php';
redirectIfNotInstructor();

include '../includes/config.php';

// Handle form submission to add availability
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_availability'])) {
    $day = $_POST['day'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    $role = $_SESSION['role']; // 'instructor' or 'barber'
    $service_type = ($role === 'instructor') ? 'lesson' : 'lesson';

    try {
        $stmt = $pdo->prepare("INSERT INTO availability 
            (instructor_id, day, start_time, end_time, role, service_type) 
            VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $day, $start_time, $end_time, $role, $service_type]);

        $_SESSION['success'] = "Availability added successfully";
        header("Location: schedule.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error adding availability: " . $e->getMessage();
    }
}

// Get instructor's availability
$stmt = $pdo->prepare("
    SELECT * FROM availability 
    WHERE instructor_id = ? AND day >= CURDATE()
    ORDER BY day, start_time
");
$stmt->execute([$_SESSION['user_id']]);
$availability = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Manage Schedule</title>
    <link rel="stylesheet" href="/driving_school/assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css">
    <style>
        .fc-event.animated-slot {
            animation: pulse 1.5s infinite;
            border-radius: 5px;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); background-color: #3ad83a; }
            100% { transform: scale(1); }
        }

        .table-responsive {
            width: 100%;
            min-width: 290px;
            overflow-x: auto;  /* Only table scrolls */
            -webkit-overflow-scrolling: touch; /* Smooth scroll for iOS */
            margin-bottom: 20px;
        }

        .instructor-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;  /* Ensures table scrolls instead of shrinking */
        }

        .instructor-table th,
        .instructor-table td {
            text-align: left;
            padding: 8px 6px;
            font-size: 0.85rem;
            white-space: nowrap; /* Prevents text from breaking awkwardly */
        }

        .thumbnail-id-card {
            max-width: 60px;
            border-radius: 6px;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <h2>Manage Your Schedule</h2>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <div class="schedule-container">
            <div class="add-availability">
                <h3>Add Available Time</h3>
                <form method="post">
                    <div class="form-group">
                        <label for="day"></label>
                        <input type="date" id="day" name="day" min="<?= date('Y-m-d') ?>" placeholder="Date" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="start_time">Start Time:</label>
                        <input type="time" id="start_time" name="start_time" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="end_time">End Time:</label>
                        <input type="time" id="end_time" name="end_time" required>
                    </div>
                    
                    <button type="submit" name="add_availability" class="btn btn-primary">Add Availability</button>
                </form>
            </div>
            
            <div class="availability-calendar">
                <h3>Your Availability Calendar</h3>
                <div id="calendar"></div>
            </div>
        </div>
        
        <div class="availability-list">
            <h3>Upcoming Availability</h3>
            <?php if (empty($availability)): ?>
                <p>You have no upcoming availability slots.</p>
            <?php else: ?>
            <div class="table-responsive">
                <table class="instructor-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($availability as $slot): ?>
                            <tr>
                                <td><?= date('D, M j, Y', strtotime($slot['day'])) ?></td>
                                <td><?= date('g:i A', strtotime($slot['start_time'])) ?> - <?= date('g:i A', strtotime($slot['end_time'])) ?></td>
                                <td>
                                    <span class="status-badge <?= $slot['status'] === 'available' ? 'available' : 'booked' ?>">
                                        <?= ucfirst($slot['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($slot['status'] === 'available'): ?>
                                        <a href="cancel_slot.php?id=<?= $slot['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to remove this slot?');">Remove</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: {
            url: '../includes/get_availability.php',
            method: 'POST',
            extraParams: {
                instructor_id: <?= $_SESSION['user_id'] ?>,
                role: '<?= $_SESSION['role'] ?>', 
                service_type: '<?= ($_SESSION['role'] === 'instructor') ? 'lesson' : 'lesson' ?>'
            },
            failure: function() {
                console.error('Failed to load availability');
                document.getElementById('calendar').innerHTML = '<p style="color:red;">Error loading availability</p>';
            }
        },
        eventDidMount: function(info) {
            // Make available slots glow
            if (info.event.extendedProps && info.event.extendedProps.status === 'available') {
                info.el.classList.add('animated-slot');
            }
        },
        dayCellDidMount: function(info) {
            // Highlight today's date
            const today = new Date();
            const cellDate = info.date;

            if (cellDate.getDate() === today.getDate() && 
                cellDate.getMonth() === today.getMonth() && 
                cellDate.getFullYear() === today.getFullYear()) {
                info.el.style.border = '2px solid #ff0000ff';
            }
        }
    });
    
    calendar.render();
});
    </script>
</body>
</html>