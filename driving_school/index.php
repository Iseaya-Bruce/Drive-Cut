<?php
session_start();

// Include configuration first
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';


// Redirect only if explicitly requested (e.g. from login)
if (isset($_GET['redirect']) && $_GET['redirect'] === 'true' && isLoggedIn()) {
    if (isStudent()) {
        header("Location: /driving_school/student/dashboard.php");
        exit();
    } elseif (isInstructor()) {
        header("Location: /driving_school/instructor/dashboard.php");
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Drive & Cut</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to bottom, #f0f8ff, #e0f7fa);
      overflow-x: hidden;
    }

    .header {
      text-align: center;
      padding: 40px 20px 10px;
      font-size: 2.5rem;
      color: #333;
    }

    .animation-wrapper {
      position: relative;
      width: 100%;
      height: 200px;
      margin-bottom: 20px;
    }

    .road {
      position: absolute;
      bottom: 0;
      width: 100%;
      height: 50px;
      background: #555;
    }

    .car {
      position: absolute;
      bottom: 55px;
      left: -80px;
      font-size: 2.5rem;
      animation: driveToShop 8s ease-in-out infinite;
      cursor: pointer;
      z-index: 2;
      transform: scaleX(-1); /* <-- This flips the car horizontally */
    }

    .building {
      position: absolute;
      bottom: 50px;
      right: 50px;
      font-size: 2.5rem;
      z-index: 1;
    }

    @keyframes driveToShop {
      0%   { left: -80px; transform: rotate(0deg); }
      50%  { left: 60%; }
      70%  { left: 62%; }
      100% { left: 110%; }
    }

    .sparkle {
      position: absolute;
      font-size: 1.4rem;
      animation: sparkleFloat 1.5s ease-out forwards;
      pointer-events: none;
    }

    @keyframes sparkleFloat {
      0% {
        transform: translateY(0) scale(1);
        opacity: 1;
      }
      100% {
        transform: translateY(-50px) scale(1.2);
        opacity: 0;
      }
    }

    .features {
      text-align: center;
      padding: 20px;
    }

    .features h2 {
      margin-bottom: 10px;
      font-size: 1.8rem;
    }

    .feature-list {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 25px;
      margin-top: 20px;
    }

    .feature {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      padding: 20px;
      width: 260px;
      transition: transform 0.3s ease;
    }

    .feature:hover {
      transform: translateY(-6px);
    }

    .feature h3 {
      margin-bottom: 10px;
      font-size: 1.2rem;
      color: #007bff;
    }

    .feature p {
      font-size: 0.95rem;
      color: #555;
    }

    .feature-typed-text::after {
        content: '|';
        animation: blink 1s step-start infinite;
        margin-left: 5px;
        color: #007bff;
    }

    @keyframes blink {
        50% { opacity: 0; }
    }


    .start-btn {
      margin: 30px auto;
      display: block;
      padding: 12px 24px;
      background: #007bff;
      color: white;
      font-size: 1rem;
      border: none;
      border-radius: 30px;
      cursor: pointer;
      transition: 0.3s ease;
    }

    .start-btn:hover {
      background: #0056b3;
    }

    .feature h3::after, .feature p::after {
        content: '|';
        animation: blink 1s infinite;
        margin-left: 3px;
        color: #007bff;
        }
        @keyframes blink {
        50% { opacity: 0; }
   }
  </style>
</head>
<body>

  <div class="header">
    🚗✂️ Welcome to Drive & Cut 💈
  </div>

  <div class="animation-wrapper">
    <div class="car" id="car">🚗</div>
    <div class="building" id="building">💈🏰</div>
    <div class="road"></div>
  </div>

  <div class="features">
     <h2 id="feature-typed-text"></h2> <!-- This will be animated -->
     <div class="feature-list" id="typed-features" style="min-height: 300px;"></div>
  </div>

    <button class="start-btn" onclick="window.location.href='auth/login.php'">
      🚀 Start Booking
    </button>
  </div>

  <script>
    const car = document.getElementById('car');
    const building = document.getElementById('building');

    // Change building based on role (hardcoded example)
    const userRole = 'barber'; // change to 'instructor' dynamically if needed
    building.textContent = userRole === 'barber' ? '💈' : '🏫';

    // Snip sound
    car.addEventListener('mouseenter', () => {
      const honk = new Audio('https://www.soundjay.com/transportation/car-horn-1.mp3');
      honk.volume = 50;
      honk.play();
    });

    // Sparkle effect when car reaches building area
    function createSparkle(x, y) {
      const spark = document.createElement('div');
      spark.className = 'sparkle';
      spark.style.left = `${x + Math.random() * 40}px`;
      spark.style.top = `${y - 30 + Math.random() * 20}px`;
      spark.textContent = '✨';
      document.body.appendChild(spark);
      setTimeout(() => spark.remove(), 1500);
    }

    setInterval(() => {
      const carPos = car.getBoundingClientRect();
      const buildingPos = building.getBoundingClientRect();
      if (Math.abs(carPos.left - buildingPos.left) < 50) {
        for (let i = 0; i < 5; i++) {
          createSparkle(carPos.left + 20, carPos.top);
        }
      }
    }, 500);

    const textToType = "Why Choose Our Platform?";
    const typedElement = document.getElementById("feature-typed-text");
    let index = 0;

    function typeText() {
        if (index < textToType.length) {
        typedElement.innerHTML += textToType.charAt(index);
        index++;
        setTimeout(typeText, 70); // Typing speed (ms)
        }
    }

    document.addEventListener("DOMContentLoaded", typeText);

    const features = [
  {
    title: "Easy Scheduling",
    description: "Book your driving lessons or hair appointments in just a few clicks."
  },
  {
    title: "Trusted Professionals",
    description: "All instructors and barbers are verified for your confidence and safety."
  },
  {
    title: "Real-Time Availability",
    description: "See open time slots instantly and avoid unnecessary delays."
  },
  {
    title: "Rate & Review",
    description: "Leave feedback to help others choose the best service providers."
  }
];

const container = document.getElementById('typed-features');

function typeElementText(element, text, delay = 20) {
  return new Promise(resolve => {
    let i = 0;
    function typeChar() {
      if (i < text.length) {
        element.innerHTML += text.charAt(i);
        i++;
        setTimeout(typeChar, delay);
      } else {
        resolve();
      }
    }
    typeChar();
  });
}

async function typeFeatures() {
  for (let feature of features) {
    const card = document.createElement('div');
    card.className = 'feature';

    const h3 = document.createElement('h3');
    const p = document.createElement('p');

    card.appendChild(h3);
    card.appendChild(p);
    container.appendChild(card);

    await typeElementText(h3, feature.title, 50);
    await new Promise(r => setTimeout(r, 200));
    await typeElementText(p, feature.description, 25);
    await new Promise(r => setTimeout(r, 300));
  }
}

document.addEventListener('DOMContentLoaded', typeFeatures);
  </script>

</body>
</html>
