<?php
session_start();
include '../includes/auth.php';
redirectIfNotInstructor();
include '../includes/config.php';

// Fetch ratings for the logged-in instructor
$stmt = $pdo->prepare("SELECT r.*, u.full_name as student_name FROM ratings r JOIN users u ON r.student_id = u.id WHERE r.rated_user_id = ? AND r.role = 'instructor' ORDER BY r.created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$ratings = $stmt->fetchAll();

// Calculate rating stats
$stmt = $pdo->prepare("SELECT 
    COUNT(*) as total_ratings,
    AVG(CASE rating_text
        WHEN 'excellent' THEN 5
        WHEN 'good' THEN 4
        WHEN 'neutral' THEN 3
        WHEN 'poor' THEN 2
        WHEN 'very poor' THEN 1
        ELSE NULL
    END) as avg_rating,
    COUNT(CASE WHEN rating_text = 'excellent' THEN 1 END) as excellent,
    COUNT(CASE WHEN rating_text = 'good' THEN 1 END) as good,
    COUNT(CASE WHEN rating_text = 'neutral' THEN 1 END) as neutral,
    COUNT(CASE WHEN rating_text = 'poor' THEN 1 END) as poor,
    COUNT(CASE WHEN rating_text = 'very poor' THEN 1 END) as very_poor
FROM ratings WHERE rated_user_id = ? AND role = 'instructor'");
$stmt->execute([$_SESSION['user_id']]);
$rating_stats = $stmt->fetch();

// Handle reply
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_reply'])) {
    $rating_id = $_POST['rating_id'];
    $reply = trim($_POST['reply']);

    try {
        $stmt = $pdo->prepare("UPDATE ratings SET reply = ? WHERE id = ?");
        $stmt->execute([$reply, $rating_id]);

        $_SESSION['success'] = "Reply submitted successfully!";
        header("Location: ratings.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error submitting reply: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Your Ratings</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container">
    <h2>Your Ratings</h2>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"> <?= $_SESSION['error'] ?> </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"> <?= $_SESSION['success'] ?> </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="chart-container">
        <h3 class="chart-title">Emoji Feedback Distribution</h3>
        <canvas id="emojiChart"></canvas>
    </div>

    <div class="rating-summary">
        <div class="average-rating">
            <span class="rating-number"> <?= $rating_stats['avg_rating'] ? number_format($rating_stats['avg_rating'], 1) : 'N/A' ?> </span>
            <p class="rating-count">Based on <?= $rating_stats['total_ratings'] ?> reviews</p>
        </div>
    </div>

    <div class="ratings-list">
        <?php if (empty($ratings)): ?>
            <p class="no-ratings">You have not received any ratings yet.</p>
        <?php else: ?>
            <?php foreach ($ratings as $rating): ?>
                <div class="rating-item">
                    <div class="rating-header">
                        <h4><?= htmlspecialchars($rating['student_name']) ?></h4>
                        <div class="rating-emoji"> <?= htmlspecialchars($rating['emoji']) ?> </div>
                    </div>
                    <p class="comment"> <?= htmlspecialchars($rating['comment']) ?> </p>
                    <?php if (empty($rating['reply'])): ?>
                        <form method="post" class="reply-form">
                            <input type="hidden" name="rating_id" value="<?= $rating['id'] ?>">
                            <textarea name="reply" placeholder="Reply to this review" required></textarea>
                            <button type="submit" name="submit_reply" class="btn btn-primary">Submit Reply</button>
                        </form>
                    <?php else: ?>
                        <div class="your-reply">
                            <strong>Your Reply:</strong>
                            <p> <?= htmlspecialchars($rating['reply']) ?> </p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    const ctx = document.getElementById('emojiChart').getContext('2d');
    const emojiData = [
        <?= (int)$rating_stats['very_poor'] ?>,
        <?= (int)$rating_stats['poor'] ?>,
        <?= (int)$rating_stats['neutral'] ?>,
        <?= (int)$rating_stats['good'] ?>,
        <?= (int)$rating_stats['excellent'] ?>
    ];

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['😠', '😞', '😐', '😊', '😍'],
            datasets: [{
                label: 'Number of Ratings',
                data: emojiData,
                backgroundColor: ['#DC143C', '#FF6347', '#ccc', '#FFD700', '#FF69B4']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    stepSize: 1
                }
            }
        }
    });
</script>

<?php include '../includes/footer.php'; ?>
</body>
</html>
