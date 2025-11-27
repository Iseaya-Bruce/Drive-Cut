<?php
session_start();

if (isset($_SESSION['error'])) {
    echo "<div class='alert alert-danger'>" . $_SESSION['error'] . "</div>";
    unset($_SESSION['error']);
}

include '../includes/auth.php';
redirectIfNotStudent();

include '../includes/config.php';

// Get all instructors with their profile images (only active ones)
$stmt = $pdo->prepare("SELECT id, full_name, barbershop_name, phone, address, id_card_image FROM users WHERE role = 'barber' AND status = 'active'");
$stmt->execute();
$barbers = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Available Barbers</title>
    <!-- Main Stylesheet (adjust path if needed) -->
    <link rel="stylesheet" href="../assets/css/style.css">

    <!-- FullCalendar v5.10.1 (stable with older syntax) -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>

    <!-- jQuery (required for Lightbox) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Lightbox2 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Select2 JS -->  
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        body {
            position: relative;
            overflow-x: hidden;
        }

        .floating-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
            font-size: 1.8rem;
            animation: floatExam 3s ease-in-out infinite;
        }

        .car-emoji {
            position: absolute;
            left: -100px;
            top: 140px;
            font-size: 2.2rem;
            animation: driveCar 10s linear infinite;
            z-index: 0;
            pointer-events: none;
        }

        @keyframes floatExam {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-8px);
            }
        }

        @keyframes driveCar {
            0% { left: -100px; }
            100% { left: 110%; }
        }

        .car-emoji:hover {
            animation-play-state: paused;
            transform: scale(1.3) rotate(-3deg);
            transition: all 0.3s ease;
            filter: drop-shadow(0 0 10px #000);
            z-index: -1;
        }

        .no-slots-label {
            font-size: 0.7rem;
            color: #aaa;
            position: absolute;
            bottom: 3px;
            right: 3px;
        }

        .fc-event.animated-slot {
            animation: pulse 1.5s infinite;
            border-radius: 5px;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); background-color: #3ad83a; }
            100% { transform: scale(1); }
        }

        .modal {
            position: fixed;
            z-index: 9999;
            left: 0; top: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex; align-items: center; justify-content: center;
        }

        .modal-content {
            background: white;
            padding: 20px 30px;
            border-radius: 10px;
            width: 500px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            position: relative;
        }

        .close-btn {
            position: absolute;
            right: 15px; top: 10px;
            font-size: 1.5rem;
            cursor: pointer;
        }

        #availabilityList table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        #availabilityList th, #availabilityList td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

         /* ✅ Mobile Responsive Tweaks */
        @media (max-width: 768px) {
        .instructor-card {
            flex-direction: column;
            text-align: center;
            padding: 15px;
            margin-bottom: 20px;
        }

        .instructor-profile img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .instructor-info h3 {
            font-size: 1.3rem;
        }

        .instructor-info p {
            font-size: 0.95rem;
            margin: 5px 0;
        }

        .btn.btn-primary {
            width: 100%;
            font-size: 1.1rem;
            padding: 12px;
        }

        .instructor-calendar {
            margin-top: 15px;
        }

        /* FullCalendar mobile tweaks */
        .fc {
            font-size: 1rem; /* Slightly bigger for touch */
        }

        .fc-toolbar-title {
            font-size: 1.2rem;
        }

        .fc .fc-button {
            font-size: 0.9rem;
            padding: 6px 10px;
        }

        .show-all-btn {
            width: 100%;
            padding: 12px;
            font-size: 1rem;
        }


    }

    @media (max-width: 480px) {
        .instructor-profile img {
            width: 100px;
            height: 100px;
        }

        .btn.btn-primary {
            font-size: 1rem;
            padding: 10px;
        }
    }

    .mb-3 {
        width: 100%;
        max-width: 1400px;     /* keeps it centered on large screens */
        box-sizing: border-box;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        background-color: white;
    }

    /* Unique container for instructors */
    .instructor-container {
        display: flex;
        flex-direction: column;
        align-items: center;   /* center everything horizontally */
        justify-content: center;
        margin: 0 auto;
        padding: 20px;
        width: 100%;
        max-width: 1400px;     /* keeps it centered on large screens */
        box-sizing: border-box;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        background-color: white;
    }

    /* Instructor list layout */
    .instructors-list {
        display: flex;
        flex-wrap: wrap;
        justify-content: center; /* center cards */
        gap: 20px;
        width: 100%;
        background-color: white;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    /* Individual instructor cards */
    .instructor-info {
        flex: 1 1 300px;      /* grow/shrink but stay min 300px */
        max-width: 600px;
        margin: 10px;
        
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    /* Mobile adjustments */
    /* FORCE instructors-list to center on small screens */
    @media screen and (max-width: 768px) {
        .instructors-list {
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            align-items: center !important;
            width: 100% !important;
            margin: 0 auto !important;
            padding: 0 !important;
            text-align: center !important;
            box-sizing: border-box;
            overflow-x: hidden !important;
        }

        .instructor-info {
            width: 90% !important;   /* prevent overflow */
            margin: 10px auto !important;
        }

        .instructor-details,
        .id-card-image,
        .instructor-calendar {
            max-width: 100% !important;
        }
    }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="floating-header" id="floatingHeader">
        <span id="instructorEmoji"></span>
        <span>✂️ Available Barbers 💈</span>
    </div>
    <div class="car-emoji">✂💇🏻‍♂</div>
    <div class="instructor-container">

            <div class="mb-3">
                <label for="barber_id" class="form-label">Search Barber</label>
                <select id="barber_id" name="barber_id" class="form-control">
                    <option value="">-- Search Barber --</option>
                    <?php foreach ($barbers as $barber): ?>
                        <option value="<?= $barber['id'] ?>">
                            <?= htmlspecialchars($barber['full_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

        <div class="instructors-list">
            <?php foreach ($barbers as $barber): ?>
                <div class="instructor-card" data-id="<?= $barber['id'] ?>">
                    <div class="instructor-info">
                        <div class="id-card-image">
                            <a href="/driving_school/uploads/id_cards/<?= htmlspecialchars($barber['id_card_image']) ?>"
                            class="zoomable-image"
                            data-lightbox="barber-<?= $barber['id'] ?>"
                            data-title="<?= htmlspecialchars($barber['full_name']) ?>">
                                <img src="/driving_school/uploads/id_cards/<?= htmlspecialchars($barber['id_card_image']) ?>"
                                    alt="<?= htmlspecialchars($barber['full_name']) ?>"
                                    class="instructor-image">
                            </a>
                        </div>
                        <div class="instructor-details">
                            <h3><?= htmlspecialchars($barber['full_name']) ?></h3>
                            <p><strong>Barbershop Name:</strong> <?= htmlspecialchars($barber['barbershop_name']) ?></p>
                            <p><strong>Phone:</strong> <?= htmlspecialchars($barber['phone']) ?></p>
                            <p><strong>Address:</strong> <?= htmlspecialchars($barber['address']) ?></p>
                            <div id="calendar-<?= $barber['id'] ?>" class="instructor-calendar"></div>
                            <button onclick="showBookingForm(<?= $barber['id'] ?>, 'barber')" class="btn btn-primary">Show all available schedules</button>
                        </div>
                    </div>
                </div>    
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Availability Calendar Modal -->
        <div id="availabilityModal" class="modal" style="display: none;">
            <div class="modal-content">
                <span class="close-btn" onclick="closeModal()">&times;</span>
                <h3>📋 Available Schedules</h3>
                <div id="availabilityList">
                    <p>Loading availability...</p>
                </div>
            </div>
        </div>

    <!-- Booking Modal -->
    <div id="bookingModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Book a barber</h3>
            <form id="bookingForm" method="post" action="book_barber.php">
                <input type="hidden" id="modal_barber_id" name="barber_id">
                <input type="hidden" id="selected_date" name="selected_date">
                <div class="form-group">
                    <label for="time_slot"></label>
                    <select id="time_slot" name="time_slot" required></select>
                </div>
                <button type="submit" class="btn btn-primary">Confirm Booking</button>
            </form>
        </div>
    </div>


    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
    <script>
        $(document).ready(function() {
                // Initialize Select2
                $('#barber_id').select2({
                    placeholder: "Search and select barber...",
                    allowClear: true
                });

                // Filter instructor cards on select
                $('#barber_id').on('change', function() {
                    const selectedId = $(this).val();

                    if (selectedId) {
                        // Hide all instructor cards
                        $('.instructor-card').hide();

                        // Show only the selected one
                        $(`.instructor-card[data-id='${selectedId}']`).fadeIn();
                    } else {
                        // If cleared, show all instructors again
                        $('.instructor-card').fadeIn();
                    }
                });
            });
        <?php foreach ($barbers as $barber): ?>
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar-<?= $barber['id'] ?>');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: {
                    url: '../includes/get_availability.php',
                    method: 'POST',
                    extraParams: {
                        barber_id: <?= $barber['id'] ?>,
                        role: 'barber',
                        service_type: 'barber'
                    },
                    failure: function () {
                        alert('Error loading availability');
                    }
                },
                eventClick: function(info) {
                    const selectedDate = info.event.start;
                    const now = new Date();
                    const diffMs = selectedDate - now;
                    const diffHours = diffMs / (1000 * 60 * 60);

                    if (diffHours < 24) {
                        alert("❌ Booking must be made at least 24 hours in advance.");
                        return;
                    }

                    if (info.event.title !== 'Available') return;

                    const dateStr = selectedDate.toISOString().split('T')[0];
                    showAvailableTimes(<?= $barber['id'] ?>, dateStr);
                },

                eventDidMount: function (info) {
                    if (info.event.extendedProps && info.event.extendedProps.status === 'available') {
                        info.el.classList.add('animated-slot');
                    }
                },
                dayCellDidMount: function (info) {
                    const today = new Date();
                    const cellDate = info.date;
                    if (cellDate.getDate() === today.getDate() &&
                        cellDate.getMonth() === today.getMonth() &&
                        cellDate.getFullYear() === today.getFullYear()) {
                        info.el.style.border = '2px solid #ff0000ff';
                    }
                    // Add "No Slots" label to empty days
                    if (!info.el.querySelector('.fc-event')) {
                        const label = document.createElement('div');
                        label.className = 'no-slots-label';
                        label.textContent = 'No Slots';
                        info.el.appendChild(label);
                    }
                }
            });
            calendar.render();
        });
        <?php endforeach; ?>

        document.addEventListener('DOMContentLoaded', function () {
            const bookingForm = document.getElementById('bookingForm');

            if (bookingForm) {
                bookingForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const timeSelect = document.getElementById('time_slot');
                    const selectedOption = timeSelect.options[timeSelect.selectedIndex];
                    const selectedText = selectedOption ? selectedOption.text : 'this slot';

                    Swal.fire({
                        title: 'Confirm Booking?',
                        text: `Do you want to book: ${selectedText}?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, book it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            bookingForm.submit();
                        }
                    });
                });
            }
        });

        function showAvailableTimes(barberId, dateStr) {
            fetch('../includes/get_timeslots.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `barber_id=${barberId}&date=${dateStr}&role=barber`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        document.getElementById('modal_barber_id').value = barberId;
                        document.getElementById('selected_date').value = dateStr;
                        const timeSlotSelect = document.getElementById('time_slot');
                        timeSlotSelect.innerHTML = '';
                        data.forEach(slot => {
                            const option = document.createElement('option');
                            option.value = slot.id;
                            option.textContent = `${slot.start_time} - ${slot.end_time}`;
                            timeSlotSelect.appendChild(option);
                        });
                        document.getElementById('bookingModal').style.display = 'block';
                    } else {
                        alert('No available time slots for this date');
                    }
                });
        }

        document.querySelector('.close').addEventListener('click', function () {
            document.getElementById('bookingModal').style.display = 'none';
        });
        window.addEventListener('click', function (event) {
            if (event.target === document.getElementById('bookingModal')) {
                document.getElementById('bookingModal').style.display = 'none';
            }
        });
        document.querySelector('.car-emoji').addEventListener('mouseenter', function () {
            const honk = new Audio("https://www.soundjay.com/transportation/car-horn-1.mp3");
            honk.volume = 0.3;
            honk.play();
        });

       function showBookingForm(userId, role) {
            const modal = document.getElementById('availabilityModal');
            const listContainer = document.getElementById('availabilityList');

            modal.style.display = 'flex';
            listContainer.innerHTML = "<p>Loading availability...</p>";

            fetch(`/driving_school/includes/fetch_availability_list.php?user_id=${userId}&role=${role}`)
                .then(response => response.json())
                .then(data => {
                    if (!Array.isArray(data) || data.length === 0) {
                        listContainer.innerHTML = "<p>No available times found for this " + role + ".</p>";
                        return;
                    }

                    const bookingAction = role === 'barber'
                        ? '/driving_school/student/book_barber.php'
                        : '/driving_school/student/book.php';

                    let table = `<table>
                        <thead><tr><th>Date</th><th>Start</th><th>End</th><th></th></tr></thead>
                        <tbody>`;

                    data.forEach(slot => {
                        table += `<tr>
                            <td>${slot.day}</td>
                            <td>${slot.start_time}</td>
                            <td>${slot.end_time}</td>
                            <td>
                                <form method="post" action="${bookingAction}" onsubmit="return confirmBooking(this, '${slot.day}', '${slot.start_time}', '${slot.end_time}')">
                                    <input type="hidden" name="time_slot" value="${slot.id}">
                                    <input type="hidden" name="instructor_id" value="${userId}">
                                    <button type="submit" class="btn btn-sm btn-success">Book</button>
                                </form>
                            </td>
                        </tr>`;
                    });

                    table += `</tbody></table>`;
                    listContainer.innerHTML = table;
                })
                .catch(() => {
                    listContainer.innerHTML = "<p>Error loading availability.</p>";
                });
        }

        function closeModal() {
            document.getElementById('availabilityModal').style.display = 'none';
        }
        
        function confirmBooking(form, day, start, end) {
            const message = `📅 Confirm booking for:\n\nDate: ${day}\nTime: ${start} - ${end}\n\nProceed?`;
            return confirm(message);
        }
    </script>
</body>
</html>

