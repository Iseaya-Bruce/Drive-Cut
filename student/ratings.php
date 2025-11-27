<?php
session_start();
include '../includes/auth.php';
redirectIfNotStudent();
include '../includes/config.php';

// Get ALL instructors and barbers
$stmt = $pdo->prepare("SELECT id, full_name FROM users WHERE role = 'instructor' ORDER BY full_name");
$stmt->execute();
$instructors = $stmt->fetchAll();

// Handle rating submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_rating'])) {
    $instructor_id = $_POST['instructor_id'];
    $emoji = $_POST['emoji'] ?? '';
    $rating_text = $_POST['rating_text'] ?? '';
    $comment = $_POST['comment'] ?? '';

    try {
        $stmt = $pdo->prepare("INSERT INTO ratings (rated_user_id, student_id, emoji, rating_text, comment, role) VALUES (?, ?, ?, ?, ?, 'instructor')");
        $stmt->execute([$instructor_id, $_SESSION['user_id'], $emoji, $rating_text, $comment]);

        $_SESSION['success'] = "Rating submitted successfully!";
        header("Location: ratings.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error submitting rating: " . $e->getMessage();
    }
}

// Get student's past instructor ratings
$stmt = $pdo->prepare("
    SELECT r.*, u.full_name AS instructor_name 
    FROM ratings r 
    JOIN users u ON r.rated_user_id = u.id 
    WHERE r.student_id = ? AND r.role = 'instructor' 
    ORDER BY r.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$ratings = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Rate Instructors</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background: linear-gradient(to bottom, #f0f8ff, #e0f7fa);
            font-family: 'Segoe UI', sans-serif;
        }

        .emoji-container {
            padding: 15px;
            background: linear-gradient(90deg, #1f1c2c, #3512e6);
            border-radius: 50px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-around;
        }

        .emoji-btn {
            font-size: 2.2rem;
            cursor: pointer;
            background: none;
            border: none;
            transition: transform 0.3s ease;
        }

        .emoji-btn:hover {
            transform: scale(1.3);
            animation: pulse 0.4s ease;
        }

        .emoji-btn.selected {
            transform: scale(1.4);
            filter: grayscale(0%) drop-shadow(0 0 10px red);
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.4); }
            100% { transform: scale(1.2); }
        }

        .rating-card {
            animation: fadeIn 0.5s ease forwards;
            border-bottom: 1px solid #ccc;
            padding: 10px 0;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .sparkle {
            position: fixed;
            top: 80%;
            font-size: 1.4rem;
            animation: floatSparkle 1.5s ease-out forwards;
            pointer-events: none;
            z-index: 999;
        }

        @keyframes floatSparkle {
            0% { transform: translateY(0); opacity: 1; }
            100% { transform: translateY(-100px); opacity: 0; }
        }
    </style>
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container">
    <h2 class="mt-4">Rate Instructors</h2>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"> <?= $_SESSION['error'] ?> </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"> <?= $_SESSION['success'] ?> </div>
        <script>launchRatingConfetti();</script>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <form method="post" action="ratings.php">
        <div class="mb-3">
            <label for="instructor_id" class="form-label">Select Instructor</label>
            <select id="instructor_id" name="instructor_id" class="form-control" required>
                <option value="">Search Instructor...</option>
                <?php foreach ($instructors as $instructor): ?>
                    <option value="<?= $instructor['id'] ?>" data-role="instructor"><?= htmlspecialchars($instructor['full_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="emoji-container">
            <button type="button" class="emoji-btn" data-emoji="😠" data-text="very poor" onclick="selectEmoji(this)">😠</button>
            <button type="button" class="emoji-btn" data-emoji="😞" data-text="poor" onclick="selectEmoji(this)">😞</button>
            <button type="button" class="emoji-btn" data-emoji="😐" data-text="neutral" onclick="selectEmoji(this)">😐</button>
            <button type="button" class="emoji-btn" data-emoji="😊" data-text="good" onclick="selectEmoji(this)">😊</button>
            <button type="button" class="emoji-btn" data-emoji="😍" data-text="excellent" onclick="selectEmoji(this)">😍</button>
        </div>

        <input type="hidden" name="emoji" id="selected-emoji" required>
        <input type="hidden" name="rating_text" id="selected-text" required>

        <div class="mb-3">
            <textarea class="form-control" name="comment" rows="3" placeholder="Share your experience..."></textarea>
        </div>

        <button type="submit" name="submit_rating" class="btn btn-primary w-100">Submit Rating</button>
    </form>

    <div id="chart-section" style="display:none;">
        <h4 class="mt-4">Instructor's Emoji Feedback</h4>
        <canvas id="emojiChart" width="400" height="200"></canvas>
    </div>

    <h3 class="section-title mt-4">Your Ratings and Replies</h3>

    <?php if (empty($ratings)): ?>
        <div class="no-ratings">You have not submitted any ratings yet.</div>
    <?php else: ?>
        <div class="ratings-container">
            <?php foreach ($ratings as $rating): ?>
                <div class="rating-card">
                    <div class="rating-header">
                        <div class="instructor-name">👨‍🏫 <?= htmlspecialchars($rating['instructor_name']) ?></div>
                        <div class="emoji"><?= $rating['emoji'] ?></div>
                    </div>
                    <p class="rating-comment">"<?= htmlspecialchars($rating['comment']) ?>"</p>

                    <?php if (!empty($rating['reply'])): ?>
                        <div class="instructor-reply">
                            <strong>Instructor Reply:</strong>
                            <p><?= htmlspecialchars($rating['reply']) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    function selectEmoji(btn) {
        document.querySelectorAll('.emoji-btn').forEach(b => b.classList.remove('selected'));
        btn.classList.add('selected');
        document.getElementById('selected-emoji').value = btn.dataset.emoji;
        document.getElementById('selected-text').value = btn.dataset.text;
    }

    function launchRatingConfetti() {
        for (let i = 0; i < 8; i++) {
            const el = document.createElement('div');
            el.className = 'sparkle';
            el.textContent = ['✨','🌟','💫'][Math.floor(Math.random() * 3)];
            el.style.left = `${Math.random() * 100}%`;
            el.style.animationDelay = `${Math.random()}s`;
            document.body.appendChild(el);
            setTimeout(() => el.remove(), 1500);
        }
    }

    $(document).ready(function () {
        $('#instructor_id').select2({
            placeholder: "Search and select an instructor",
            allowClear: true,
            width: '100%',
            minimumResultsForSearch: 0
        });

        const ctx = document.getElementById('emojiChart').getContext('2d');
        let emojiChart = null;

        $('#instructor_id').on('change', function () {
            const selectedOption = $(this).find(':selected');
            const userId = selectedOption.val();
            const role = selectedOption.data('role');

            if (!userId || !role) return;

            fetch(`../includes/get_instructor_chart_data.php?user_id=${userId}&role=${role}`)
                .then(response => response.json())
                .then(data => {
                    const emojiData = [
                        data.very_poor || 0,
                        data.poor || 0,
                        data.neutral || 0,
                        data.good || 0,
                        data.excellent || 0
                    ];

                    if (emojiChart) emojiChart.destroy();
                    emojiChart = new Chart(ctx, {
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
                            plugins: { legend: { display: false } },
                            scales: { y: { beginAtZero: true, stepSize: 1 } }
                        }
                    });

                    document.getElementById('chart-section').style.display = 'block';
                });
        });
    });
</script>
<?php include '../includes/footer.php'; ?>
</body>
</html>
