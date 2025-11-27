<?php
include '../includes/config.php';
include '../includes/auth.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $district = $_POST['district'];
    $driving_school = ($role === 'instructor') ? $_POST['driving_school'] : null;
    $barbershop_name = ($role === 'barber') ? $_POST['barbershop_name'] : null;

    // Handle ID card upload
    $idCardFileName = null;
    if (isset($_FILES['id_card_image']) && $_FILES['id_card_image']['error'] === 0) {
        $targetDir = "../uploads/id_cards/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $ext = pathinfo($_FILES['id_card_image']['name'], PATHINFO_EXTENSION);
        $idCardFileName = uniqid('idcard_', true) . "." . $ext;
        move_uploaded_file($_FILES['id_card_image']['tmp_name'], $targetDir . $idCardFileName);
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO users 
            (username, password, role, full_name, phone, address, district, driving_school, barbershop_name, id_card_image) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([$username, $password, $role, $full_name, $phone, $address, $district, $driving_school, $barbershop_name, $idCardFileName]);


       

        header("Location: login.php");
        exit();
    } catch (PDOException $e) {
        $error = "Registration failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Register</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .auth-page {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            position: relative;
        }
        .register-container {
            position: relative;
            z-index: 2;
            max-width: 450px;
            width: 100%;
            margin: 2rem;
        }
        canvas#particle-canvas {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1;
        }
    </style>
</head>
<body class="auth-page">
    <canvas id="particle-canvas"></canvas>
    <div class="register-container">
        <h2>Register</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="post" enctype="multipart/form-data">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="text" name="full_name" placeholder="Full Name" required>
            <input type="tel" name="phone" placeholder="Phone Number" required>

            <input type="text" name="address" placeholder="Address" required>

            <select name="district" required>
                <option value="">Select District</option>
                <option value="Paramaribo">Paramaribo</option>
                <option value="Wanica">Wanica</option>
                <option value="Commewijne">Commewijne</option>
                <option value="Nickerie">Nickerie</option>
                <option value="Saramacca">Saramacca</option>
                <option value="Coronie">Coronie</option>
                <option value="Marowijne">Marowijne</option>
                <option value="Brokopondo">Brokopondo</option>
                <option value="Sipaliwini">Sipaliwini</option>
            </select>

            <label for="id_card_image">Upload ID Card</label>
            <input type="file" name="id_card_image" id="id_card_image" accept="image/*" required>

            <select name="role" id="role" required>
                <option value="">Select Role</option>
                <option value="student">Student</option>
                <option value="instructor">Instructor</option>
                <option value="barber">Barber</option>
            </select>


            <div id="instructor-fields" style="display:none;">
                <input type="text" name="driving_school" placeholder="Driving School Name">
            </div>

            <div id="barber-fields" style="display:none;">
                <input type="text" name="barbershop_name" placeholder="Barbershop Name">
            </div>


            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>

    <script>
        document.getElementById('role').addEventListener('change', function () {
            const instructorFields = document.getElementById('instructor-fields');
            const barberFields = document.getElementById('barber-fields');

            instructorFields.style.display = this.value === 'instructor' ? 'block' : 'none';
            barberFields.style.display = this.value === 'barber' ? 'block' : 'none';
        });


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

        let mouseX = null;
        let mouseY = null;
        window.addEventListener('mousemove', (e) => {
            mouseX = e.clientX;
            mouseY = e.clientY;
        });

        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            particles.forEach(particle => {
                particle.x += particle.speedX;
                particle.y += particle.speedY;
                particle.angle += particle.spin;

                if (particle.x < 0 || particle.x > canvas.width) particle.speedX *= -1;
                if (particle.y < 0 || particle.y > canvas.height) particle.speedY *= -1;

                if (mouseX && mouseY) {
                    const dx = mouseX - particle.x;
                    const dy = mouseY - particle.y;
                    const distance = Math.sqrt(dx * dx + dy * dy);
                    if (distance < 100) {
                        particle.x += dx * 0.02;
                        particle.y += dy * 0.02;
                    }
                }

                ctx.save();
                ctx.translate(particle.x, particle.y);
                ctx.rotate(particle.angle);
                ctx.fillStyle = particle.color;
                ctx.fillRect(-particle.size / 2, -particle.size / 2, particle.size, particle.size);
                ctx.restore();
            });

            requestAnimationFrame(animate);
        }

        animate();
    </script>
</body>
</html>
