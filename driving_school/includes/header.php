<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Driving School'; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
    header {
        background: linear-gradient(90deg, #1f1c2c, rgb(53, 18, 230));
        color: #fff;
        padding: 1rem 0;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    header .container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
    }

    .logo-and-welcome {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .img-responsive {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid white;
        box-shadow: 0 0 5px rgba(255,255,255,0.3);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    #logo-click:hover img {
        transform: scale(1.05) rotate(-2deg);
        box-shadow: 0 0 12px rgba(255, 255, 255, 0.6);
    }

    .logo {
        font-size: 1.6rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .welcome-message {
        font-size: 1.1rem;
        color: rgb(255, 255, 255);
        font-weight: 400;
    }

    nav ul {
        display: flex;
        gap: 1.5rem;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    nav ul li a {
        color: rgb(255, 255, 255);
        text-decoration: none;
        font-weight: 500;
        font-size: 1rem;
        position: relative;
        transition: color 0.3s;
    }

    nav ul li a::after {
        content: '';
        display: block;
        height: 2px;
        background: #ffffff;
        transition: width 0.3s;
        width: 0;
        position: absolute;
        bottom: -4px;
        left: 0;
    }

    nav ul li a:hover {
        color: rgb(50, 228, 34);
    }

    nav ul li a:hover::after {
        width: 100%;
    }

    .dropdown {
        position: relative;
    }

    .dropdown-toggle {
        cursor: pointer;
    }

    .dropdown-menu {
        display: none;
        position: absolute;
        background-color: white;
        padding: 10px;
        border: 1px solid #ccc;
        list-style: none;
        z-index: 1000;
        top: 100%;
        left: 0;
        min-width: 160px;
    }

    .dropdown:hover .dropdown-menu {
        display: block;
    }

    .dropdown-menu li {
        margin: 0;
    }

    .dropdown-menu li a {
        display: block;
        padding: 5px 10px;
        color: #333;
    }

    .dropdown-menu li a:hover {
        background-color: #f0f0f0;
    }

    /* ✅ Mobile Responsive Tweaks */
    @media (max-width: 480px) {
        header .container {
            flex-direction: column;
            align-items: center;
            padding: 0 1rem;
            width: 100%;
        }

        .logo-and-welcome {
            flex-direction: column;
            gap: 0.5rem;
            text-align: center;
        }

        .logo {
            font-size: 1.4rem;
        }

        .welcome-message {
            font-size: 1rem;
        }

        nav ul {
            max-height: 60vh; /* Prevent overlap */
            overflow-y: auto; /* Scroll if too long */
        }


        nav ul li a {
            font-size: 1.1rem;
        }

        .dropdown-menu {
            position: static;
            background-color: transparent;
            border: none;
            padding: 0;
        }

        .dropdown-menu li a {
            color: #fff;
            background-color: rgba(0, 0, 0, 0.2);
            padding: 8px;
            border-radius: 5px;
        }

        .dropdown-menu li a:hover {
            background-color: rgba(0, 0, 0, 0.4);
        }
    }

    @media (max-width: 480px) {
        .img-responsive {
            width: 40px;
            height: 40px;
        }
        .logo {
            font-size: 1.2rem;
        }
        nav ul li a {
            font-size: 1rem;
        }
    }
</style>

</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo-and-welcome">
                    <div id="logo-click">
                        <img src="../assets/images/IROKS.jpg" alt="Website Logo" class="img-responsive">
                    </div>
                    <?php if (isLoggedIn() && isset($_SESSION['username'])): ?>
                        <div class="welcome-message">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</div>
                    <?php else: ?>
                        <div class="logo">Driving School</div>
                    <?php endif; ?>
                </div>
                <nav>
                    <ul>
                        <?php if (isLoggedIn()): ?>
                            <li>
                                <a href="
                                    <?= isStudent() ? '../student/dashboard.php' : 
                                        (isInstructor() ? '../instructor/dashboard.php' : 
                                        (isBarber() ? '../barber/dashboard.php' : 
                                        (isAdmin() ? '../admin/dashboard.php' : '#'))) ?>">Dashboard</a>
                            </li>

                            <?php if (isStudent()): ?>
                                <!-- Appointments Dropdown for Students -->
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle">Appointments ▼</a>
                                    <ul class="dropdown-menu">
                                        <li><a href="../student/instructors.php?type=instructor">Book Instructor</a></li>
                                        <li><a href="../student/barbers.php?type=barber">Book Barber</a></li>
                                    </ul>
                                </li>

                                <!-- Ratings Dropdown for Students -->
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle">Ratings ▼</a>
                                    <ul class="dropdown-menu">
                                        <li><a href="../student/ratings.php?type=instructor">Rate Instructor</a></li>
                                        <li><a href="../student/rate_barber.php?type=barber">Rate Barber</a></li>
                                    </ul>
                                </li>
                            <?php elseif (!isAdmin()): ?>
                                <!-- Ratings Link for Instructor or Barber -->
                                <li><a href="<?= isInstructor() ? '../instructor/ratings.php' : '../barber/ratings.php' ?>">Ratings</a></li>
                            <?php endif; ?>

                            <li><a href="../auth/logout.php">Logout</a></li>
                        <?php else: ?>
                            <li><a href="index.php">Home</a></li>
                            <li><a href="../auth/login.php">Login</a></li>
                            <li><a href="../auth/register.php">Register</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>

            </div>
        </div>
    </header>
    <main>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
    const logo = document.getElementById('logo-click');

    if (logo) {
        logo.style.cursor = 'pointer';
        logo.addEventListener('click', function () {
        Swal.fire({
            title: "Do you like our website? 💬",
            showDenyButton: true,
            confirmButtonText: 'Yes😁!',
            denyButtonText: 'No 🙄',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didRender: () => {
            const denyButton = document.querySelector('.swal2-deny');

            if (denyButton) {
                denyButton.style.position = 'relative';

                // Make "No" button run from cursor
                denyButton.addEventListener('mouseenter', () => {
                const maxX = 200;
                const maxY = 100;
                const randomX = Math.floor(Math.random() * maxX - maxX / 2);
                const randomY = Math.floor(Math.random() * maxY - maxY / 2);
                denyButton.style.transform = `translate(${randomX}px, ${randomY}px)`;
                });

                // Optional: prevent clicking "No"
                denyButton.addEventListener('click', (e) => {
                e.preventDefault();
                denyButton.dispatchEvent(new Event('mouseenter'));
                });
            }
            }
        }).then((result) => {
            if (result.isConfirmed) {
            Swal.fire('Thanks! 😊', 'We’re glad you like it!', 'success');
            }
        });
        });
    }
    });
    </script>

</body>
</html>