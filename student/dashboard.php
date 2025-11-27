<?php
include '../includes/auth.php';
redirectIfNotStudent();
include '../includes/config.php';

// Fetch student bookings
// Fetch student bookings (for instructors and barbers)
$stmt = $pdo->prepare("
    SELECT b.*, 
           u.full_name as provider_name, 
           u.phone as provider_phone, 
           u.role as role,
           a.day, a.start_time, a.end_time 
    FROM bookings b
    JOIN availability a ON b.availability_id = a.id
    JOIN users u ON b.instructor_id = u.id
    WHERE b.student_id = ? 
      AND b.status = 'confirmed' 
      AND a.day >= CURDATE()
    ORDER BY a.day, a.start_time
");
$stmt->execute([$_SESSION['user_id']]);
$bookings = $stmt->fetchAll();


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
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <!-- jQuery (required for Lightbox) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Lightbox2 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
<style>
    
</style>
</head>
<body>

<?php include '../includes/header.php'; ?>

<div class="student-dashboard">
    <div class="container">
        <h2>📅 Your Dashboard</h2>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <div class="upload-container">
            <form action="upload_idcards.php" method="post" enctype="multipart/form-data" class="profile-upload-form">
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

        <h2>Your Upcoming Lessons</h2>

        <?php if (empty($bookings)): ?>
            <p>You have no upcoming lessons. <a href="instructors.php">Book one now!</a></p>
            <p>You have no upcoming barber appointments. <a href="barbers.php">Book one now!</a></p>
        <?php else: ?>
            <div class="action-buttons">
                <p>Book another session: 
                    <a href="instructors.php" class="btn btn-sm btn-primary">Driving Instructor</a>
                    <a href="barbers.php" class="btn btn-sm btn-primary">Barber</a>
                </p>
            </div>

            <div class="table-responsive">
                <table class="student-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Provider</th>
                            <th>Phone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?= date('D, M j, Y', strtotime($booking['day'])) ?></td>
                                <td><?= date('g:i A', strtotime($booking['start_time'])) ?> - <?= date('g:i A', strtotime($booking['end_time'])) ?></td>
                                <td><?= htmlspecialchars($booking['provider_name']) ?> (<?= ucfirst($booking['role']) ?>)</td>
                                <td><?= htmlspecialchars($booking['provider_phone']) ?></td>
                                <td>
                                    <a href="cancel.php?id=<?= $booking['id'] ?>" class="btn btn-danger"
                                    onclick="return confirm('Are you sure you want to cancel this booking?');">Cancel</a>
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

<script>
    document.getElementById('id_card_image')?.addEventListener('change', function(e) {
        document.getElementById('idCardFileName').textContent = 
            this.files[0] ? this.files[0].name : 'No file chosen';
    });
</script>

</body>
</html>
