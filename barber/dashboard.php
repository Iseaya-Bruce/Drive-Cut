<?php
// Using absolute paths from document root
require_once '../includes/paths.php';
require_once '../includes/auth.php';
require_once '../includes/config.php';


redirectIfNotBarber();



// ... dashboard content ...

// Get upcoming bookings
$stmt = $pdo->prepare("
    SELECT b.*, a.day, a.start_time, a.end_time, 
           u.full_name as student_name, u.phone as student_phone, u.id_card_image
    FROM bookings b
    JOIN availability a ON b.availability_id = a.id
    JOIN users u ON b.student_id = u.id
    WHERE b.instructor_id = ? AND b.status = 'confirmed' AND a.day >= CURDATE()
    ORDER BY a.day, a.start_time
");
$stmt->execute([$_SESSION['user_id']]);
$bookings = $stmt->fetchAll();

// Get average rating based on emoji
$stmt = $pdo->prepare("
    SELECT 
        AVG(CASE 
            WHEN emoji = '😊' THEN 5 
            WHEN emoji = '🙂' THEN 4 
            WHEN emoji = '😐' THEN 3 
            WHEN emoji = '🙁' THEN 2 
            WHEN emoji = '😢' THEN 1 
            ELSE 0 
        END) as avg_rating,
        COUNT(*) as total_ratings 
    FROM ratings 
    WHERE rated_user_id = ? AND role = 'barber'
");
$stmt->execute([$_SESSION['user_id']]);
$rating = $stmt->fetch();


// Fetch the instructor profile image

$stmt->execute([$_SESSION['user_id']]);
$barber = $stmt->fetch();

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT id_card_image FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Barber Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
   <!-- jQuery (required for Lightbox) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Lightbox2 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>

        <style>
        .student-dashboard {
            background: linear-gradient(to bottom, #f0f8ff, #e0f7fa);
            padding: 30px 20px;
        }

        .student-dashboard .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 30px 20px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .student-dashboard h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-size: 2rem;
        }

        .student-dashboard .alert-success {
            background-color: #d4edda;
            padding: 12px;
            border-left: 5px solid #28a745;
            margin-bottom: 20px;
            border-radius: 6px;
        }

        .student-dashboard .upload-container {
            background: #f9f9f9;
            border: 2px dashed #007bff;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 40px;

            /* FIX BELOW */
            position: relative;   /* Instead of absolute */
            margin: 0 auto;       /* Center horizontally */
            width: 100%;          /* Or set a specific width like 80% */
            max-width: 500px;     /* Prevent it from being too wide */
        }

        .student-dashboard .upload-container form {
            text-align: center;
        }

        .student-dashboard .upload-container .btn {
            margin-top: 15px;
        }

        .student-dashboard .current-image-wrapper img {
            max-width: 180px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            cursor: pointer;
            text-align: center;
            margin-bottom: 20px; text-align: center;
            margin-bottom: 20px;
        }

        .student-dashboard .file-input-wrapper {
            margin: 20px 0;
        }

        .student-dashboard .file-button {
            background: #007bff;
            color: white;
            padding: 10px 15px;
            border-radius: 20px;
            cursor: pointer;
        }

        .student-dashboard .file-name {
            margin-left: 10px;
            font-size: 0.9rem;
            color: #333;
        }

        .student-dashboard .upload-button {
            margin-top: 10px;
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            cursor: pointer;
            transition: 0.3s ease;
        }

        .student-dashboard .upload-button:hover {
            background: #0056b3;
        }

        .student-dashboard .upload-requirements {
            font-size: 0.85rem;
            color: #777;
            margin-top: 10px;
        }

        .student-dashboard table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        .student-dashboard th, .student-dashboard td {
            padding: 12px 10px;
            text-align: left;
        }

        .student-dashboard thead {
            background-color: #007bff;
            color: white;
        }

        .student-dashboard tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .student-dashboard .btn-danger {
            background-color: #dc3545;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.9rem;
            text-decoration: none;
            transition:  0.3s ease;
        }

        .student-dashboard .btn-danger:hover {
            background-color: #c82333;
        }

        .student-dashboard .btn-sm {
            font-size: 0.85rem;
            padding: 6px 12px;
            border-radius: 20px;
            margin: 0 5px;
        }

        .student-dashboard .btn-primary {
            background-color: #007bff;
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            text-decoration: none;
        }

        .student-dashboard .btn-primary:hover {
            background-color: #0056b3;
        }

        .student-dashboard p a {
            color: #007bff;
        }

        .student-dashboard .action-buttons {
            margin-top: 20px;
        }

        .table-responsive {
            width: 100%;
            max-width: 290px;
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
    
<div class="student-dashboard">
    <div class="container">
        <h2>📅 Your Dashboard</h2>


        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>


            <div class="upload-container">
                <form action="../student/upload_idcards.php" method="post" enctype="multipart/form-data" class="profile-upload-form">
                    <?php if (!empty($user['id_card_image'])): ?>
                        <div class="current-image-wrapper">
                            <a href="../uploads/id_cards/<?= htmlspecialchars($user['id_card_image']) ?>" 
                            data-lightbox="id-card" 
                            data-title="Your ID Card">
                                <img src="../uploads/id_cards/<?= htmlspecialchars($user['id_card_image']) ?>" 
                                    alt="Your ID Card" 
                                    class="clickable-image">
                            </a>
                            <div class="image-actions">
                                <label for="id_card_image" class="btn btn-secondary">Change ID Card</label>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="file-input-wrapper" <?= !empty($user['id_card_image']) ? 'style="display:none"' : '' ?>>
                        <label for="id_card_image" class="file-label">
                            <span class="file-button">Choose ID Card Image</span>
                            <span class="file-name" id="idCardFileName">No file chosen</span>
                        </label>
                        <input type="file" name="id_card_image" id="id_card_image" accept="image/*" required class="file-input">
                    </div>

                    <button type="submit" class="btn btn-primary upload-button">
                        <?= !empty($user['id_card_image']) ? 'Update' : 'Upload' ?> ID Card
                    </button>

                    <div class="upload-requirements">
                        <small>Accepted formats: JPG, PNG (Max 2MB). Ensure all details are clearly visible.</small>
                    </div>
                </form>
            </div>
    </div>
        
    <div class="dashboard-stats">
        <div class="stat-card">
            <h3>Upcoming Lessons</h3>
            <p><?= count($bookings) ?></p>
        </div>
        <div class="stat-card">
            <h3>Your Rating</h3>
            <p>
                <?= $rating['avg_rating'] ? number_format($rating['avg_rating'], 1) : 'N/A' ?>
                <small>(<?= $rating['total_ratings'] ?> reviews)</small>
            </p>
        </div>
    </div>
        
    <div class="dashboard-sections">
        <div class="section">
            <h3>Upcoming Lessons</h3>
            <?php if (empty($bookings)): ?>
                <p>You have no upcoming lessons.</p>
            <?php else: ?>
            <div class="table-responsive">
                <table class="instructor-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Student</th>
                            <th>ID Card</th>
                            <th>Phone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?= date('D, M j, Y', strtotime($booking['day'])) ?></td>
                                <td><?= date('g:i A', strtotime($booking['start_time'])) ?> - <?= date('g:i A', strtotime($booking['end_time'])) ?></td>
                                <td><?= htmlspecialchars($booking['student_name']) ?></td>
                                <td>
                                    <?php if (!empty($booking['id_card_image'])): ?>
                                        <a href="../uploads/id_cards/<?= htmlspecialchars($booking['id_card_image']) ?>" class="zoomable-image" data-lightbox="id-card-<?= $booking['id'] ?>" data-title="ID Card of <?= htmlspecialchars($booking['student_name']) ?>">
                                            <img src="../uploads/id_cards/<?= htmlspecialchars($booking['id_card_image']) ?>" alt="Student ID Card" class="thumbnail-id-card">
                                        </a>
                                    <?php else: ?>
                                        <span class="no-id">No ID uploaded</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($booking['student_phone']) ?></td>
                                <td>
                                    <a href="whatsapp://send?phone=<?= urlencode($booking['student_phone']) ?>" class="btn btn-primary">Message</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
            
            <div class="section">
                <h3>Quick Actions</h3>
                <div class="action-buttons">
                    <a href="schedule.php" class="btn btn-primary">Manage Availability</a>
                    <a href="ratings.php" class="btn btn-primary">View Ratings</a>
                </div>
            </div>
    </div>
</div>
<script>
document.getElementById('profile_image').addEventListener('change', function(e) {
    document.getElementById('profileFileName').textContent = 
        this.files[0] ? this.files[0].name : 'No file chosen';
});
</script>

<style>
    .thumbnail-id-card {
        width: 50px;
        height: auto;
        border: 1px solid #ddd;
        border-radius: 4px;
        cursor: pointer;
        transition: transform 0.2s;
    }
    
    .thumbnail-id-card:hover {
        transform: scale(1.1);
    }
    
    .no-id {
        color: #999;
        font-style: italic;
    }
    
    /* Customize the lightbox appearance */
    .lightbox .lb-image {
        max-width: 80vw;
        max-height: 80vh;
    }
</style>

<script>
    // Initialize lightbox with custom options
    lightbox.option({
        'resizeDuration': 200,
        'wrapAround': true,
        'showImageNumberLabel': false,
        'disableScrolling': true
    });
</script>
    
    
    <?php include INCLUDES_PATH . '/footer.php'; ?>
</body>
</html>