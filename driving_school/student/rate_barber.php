<?php
session_start();
include '../includes/auth.php';
redirectIfNotStudent();
include '../includes/config.php';

// Get ALL barbers
$stmt = $pdo->prepare("SELECT id, full_name FROM users WHERE role = 'barber' ORDER BY full_name");
$stmt->execute();
$barbers = $stmt->fetchAll();

// Handle rating submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_rating'])) {
    $barber_id = $_POST['barber_id'];
    $emoji = $_POST['emoji'] ?? '';
    $rating_text = $_POST['rating_text'] ?? '';
    $comment = $_POST['comment'] ?? '';

    try {
        $stmt = $pdo->prepare("INSERT INTO ratings (rated_user_id, student_id, emoji, rating_text, comment, role) VALUES (?, ?, ?, ?, ?, 'barber')");
        $stmt->execute([$barber_id, $_SESSION['user_id'], $emoji, $rating_text, $comment]);

        $_SESSION['success'] = "Rating submitted successfully!";
        header("Location: rate_barber.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error submitting rating: " . $e->getMessage();
    }
}

// Get student's past barber ratings
$stmt = $pdo->prepare("
    SELECT r.*, u.full_name AS barber_name 
    FROM ratings r 
    JOIN users u ON r.rated_user_id = u.id 
    WHERE r.student_id = ? AND r.role = 'barber' 
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
    <title>Rate Barbers</title>
    <link rel="stylesheet" href="/driving_school/assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .emoji-container {
            padding: 15px;
            background: linear-gradient(90deg, #1f1c2c,rgb(53, 18, 230));
            border-radius: 50px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
        }
        .emoji-btn {
            font-size: 2rem;
            cursor: pointer;
            background: none;
            border: none;
            transition: all 0.3s ease;
        }
        .emoji-btn.selected {
            transform: scale(1.4);
            filter: grayscale(0%) drop-shadow(0 0 10px red);
        }
        .rating-item {
            border-bottom: 1px solid #ccc;
            padding: 10px 0;
        }
        .reply {
            font-style: italic;
            color: #555;
        }
        .select2-container .select2-selection--single {
            height: 38px;
            padding: 5px 10px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px;
        }

    </style>
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container">
    <h2>Rate Barber</h2>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"> <?= $_SESSION['error'] ?> </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"> <?= $_SESSION['success'] ?> </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <form method="post" action="rate_barber.php">
        <div class="mb-3">
            <label for="barber_id" class="form-label">Select Barber</label>
            <select id="barber_id" name="barber_id" class="form-control" required>
                <option value="">Search Barber...</option>
                <?php foreach ($barbers as $barber): ?>
                    <option value="<?= $barber['id'] ?>" data-role="barber"><?= htmlspecialchars($barber['full_name']) ?></option>
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
        <h4 class="mt-4">Barber's Emoji Feedback</h4>
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
                        <div class="instructor-name">👨‍🏫 <?= htmlspecialchars($rating['barber_name']) ?></div>
                        <div class="emoji"><?= $rating['emoji'] ?></div>
                    </div>
                    <p class="rating-comment">"<?= htmlspecialchars($rating['comment']) ?>"</p>

                    <?php if (!empty($rating['reply'])): ?>
                        <div class="instructor-reply">
                            <strong>Barber Reply:</strong>
                            <p><?= htmlspecialchars($rating['reply']) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    function selectEmoji(btn) {
        document.querySelectorAll('.emoji-btn').forEach(b => b.classList.remove('selected'));
        btn.classList.add('selected');
        document.getElementById('selected-emoji').value = btn.dataset.emoji;
        document.getElementById('selected-text').value = btn.dataset.text;
    }

    $(document).ready(function () {
        // Initialize Select2
        $('#barber_id').select2({
            placeholder: "Search and select an barber",
            allowClear: true,
            width: '100%',
            minimumResultsForSearch: 0
        });

        const ctx = document.getElementById('emojiChart').getContext('2d');
        let emojiChart = null;

        $('#barber_id').on('change', function () {
            const userId = $(this).val();
            const role = 'barber'; // Hardcode if this page is for Barber only

            if (!userId) return;

            fetch(`../includes/get_instructor_chart_data.php?user_id=${userId}&role=barber`)
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
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    }
                                }
                            }
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
