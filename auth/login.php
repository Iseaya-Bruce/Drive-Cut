<?php
session_start();
include '../includes/config.php';
include '../includes/auth.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user) {
        if ($user['status'] !== 'active') {
            $error = "Your account is inactive. Please contact the administrator.";
        } elseif (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // ✅ Redirect based on role
            if       ($user['username'] === 'admin') {
                header("Location: ../admin/dashboard.php");
            } elseif ($user['role'] === 'student') {
                header("Location: ../student/dashboard.php");
            } elseif ($user['role'] === 'barber') {
                header("Location: ../barber/dashboard.php");
            } elseif ($user['role'] === 'instructor') {
                header("Location: ../instructor/dashboard.php");
            } else {
                $error = "Unknown role. Please contact support.";
            }
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        #particle-canvas {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }
    </style>
</head>
<body class="auth-page">

 <canvas id="particle-canvas"></canvas>

    <div class="login-container">
        <h2>Login</h2>
        <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
        <form method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>

        <p style="text-align:center;">
            <a href="../index.php" class="btn-secondary">← Home</a><br><br>
            Don't have an account? <a href="register.php">Register</a>
        </p>
    </div>

    <script>
        // Particle Animation Script
        const canvas = document.getElementById('particle-canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        const particles = [];
        const particleCount = 60;
        const colors = ['rgba(0, 255, 64, 0.7)', 'rgba(255, 255, 255, 0.5)', 'rgba(0, 0, 0, 0.3)'];

        for (let i = 0; i < particleCount; i++) {
            particles.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height,
                size: Math.random() * 5 + 1,
                color: colors[Math.floor(Math.random() * colors.length)],
                speedX: Math.random() * 1 - 0.5,
                speedY: Math.random() * 1 - 0.5,
                angle: 0,
                spin: Math.random() * 0.2 - 0.1
            });
        }

        let mouseX = null, mouseY = null;
        window.addEventListener('mousemove', (e) => {
            mouseX = e.clientX;
            mouseY = e.clientY;
        });

        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            particles.forEach(p => {
                p.x += p.speedX;
                p.y += p.speedY;
                p.angle += p.spin;

                if (p.x < 0 || p.x > canvas.width) p.speedX *= -1;
                if (p.y < 0 || p.y > canvas.height) p.speedY *= -1;

                if (mouseX && mouseY) {
                    const dx = mouseX - p.x;
                    const dy = mouseY - p.y;
                    const dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < 100) {
                        p.x += dx * 0.02;
                        p.y += dy * 0.02;
                    }
                }

                ctx.save();
                ctx.translate(p.x, p.y);
                ctx.rotate(p.angle);
                ctx.fillStyle = p.color;
                ctx.fillRect(-p.size / 2, -p.size / 2, p.size, p.size);
                ctx.restore();
            });

            requestAnimationFrame(animate);
        }

        animate();
    </script>

</body>
</html>
