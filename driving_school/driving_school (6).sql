-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 27, 2025 at 06:34 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `driving_school`
--

-- --------------------------------------------------------

--
-- Table structure for table `availability`
--

CREATE TABLE `availability` (
  `id` int(11) NOT NULL,
  `instructor_id` int(11) NOT NULL,
  `role` enum('instructor','barber') DEFAULT NULL,
  `day` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `status` enum('available','booked') DEFAULT 'available',
  `service_type` enum('lesson','barber') DEFAULT 'lesson'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `availability`
--

INSERT INTO `availability` (`id`, `instructor_id`, `role`, `day`, `start_time`, `end_time`, `status`, `service_type`) VALUES
(12, 1, NULL, '2025-05-07', '09:20:00', '10:20:00', 'available', 'lesson'),
(13, 1, NULL, '2025-08-05', '00:00:00', '00:00:00', 'booked', 'lesson'),
(14, 1, NULL, '2025-05-06', '15:04:00', '17:04:00', 'booked', 'lesson'),
(16, 1, NULL, '2025-05-10', '09:22:00', '09:25:00', 'booked', 'lesson'),
(17, 1, NULL, '2025-05-16', '10:05:00', '00:05:00', 'booked', 'lesson'),
(27, 1, NULL, '2025-05-23', '14:23:00', '14:23:00', 'available', 'lesson'),
(28, 1, NULL, '2025-05-27', '11:45:00', '11:46:00', 'available', 'lesson'),
(29, 18, NULL, '2025-05-28', '12:55:00', '12:55:00', 'available', 'lesson'),
(30, 18, NULL, '2025-05-30', '12:56:00', '12:56:00', 'available', 'lesson'),
(31, 18, NULL, '2025-05-31', '12:56:00', '12:56:00', 'available', 'lesson'),
(32, 18, NULL, '2025-05-29', '12:58:00', '12:58:00', 'available', 'lesson'),
(33, 18, NULL, '2025-05-31', '15:18:00', '15:18:00', 'available', 'lesson'),
(34, 7, NULL, '2025-06-05', '10:24:00', '10:24:00', 'available', 'lesson'),
(35, 7, NULL, '2025-06-12', '10:24:00', '10:24:00', 'booked', 'lesson'),
(36, 18, NULL, '2025-06-05', '13:47:00', '13:47:00', 'available', 'lesson'),
(37, 18, NULL, '2025-06-12', '09:36:00', '09:36:00', 'available', 'lesson'),
(47, 7, 'instructor', '2025-06-26', '16:02:00', '16:02:00', 'available', 'lesson'),
(48, 19, 'barber', '2025-06-27', '08:34:00', '08:34:00', 'available', 'barber'),
(49, 19, NULL, '2025-06-28', '14:44:00', '14:44:00', 'available', 'barber'),
(50, 19, NULL, '2025-06-27', '14:45:00', '14:45:00', 'available', 'barber'),
(51, 7, NULL, '2025-06-27', '14:55:00', '14:55:00', 'available', 'lesson'),
(52, 7, 'instructor', '2025-06-29', '15:02:00', '15:02:00', 'available', 'lesson'),
(53, 19, 'barber', '2025-06-30', '15:03:00', '15:04:00', 'available', 'barber'),
(54, 7, 'instructor', '2025-06-29', '14:58:00', '14:58:00', 'booked', 'lesson'),
(55, 7, 'instructor', '2025-06-28', '14:58:00', '14:58:00', 'available', 'lesson'),
(56, 19, 'barber', '2025-06-28', '14:59:00', '14:59:00', 'available', 'barber'),
(57, 19, 'barber', '2025-06-29', '14:59:00', '14:59:00', 'available', 'barber'),
(58, 7, 'instructor', '2025-07-03', '10:06:00', '10:06:00', 'available', 'lesson'),
(59, 7, 'instructor', '2025-07-05', '10:06:00', '10:06:00', 'available', 'lesson'),
(60, 7, 'instructor', '2025-07-08', '10:06:00', '10:06:00', 'booked', 'lesson'),
(61, 19, 'barber', '2025-07-03', '10:07:00', '10:07:00', 'available', 'barber'),
(62, 19, 'barber', '2025-07-05', '10:07:00', '10:07:00', 'available', 'barber'),
(63, 19, 'barber', '2025-07-18', '08:30:00', '08:30:00', 'available', 'barber'),
(64, 19, 'barber', '2025-07-22', '08:30:00', '08:30:00', 'available', 'barber'),
(65, 19, 'barber', '2025-07-20', '08:33:00', '08:30:00', 'booked', 'barber'),
(67, 7, 'instructor', '2025-07-25', '11:39:00', '11:39:00', 'available', 'lesson'),
(68, 19, 'barber', '2025-07-26', '08:58:00', '08:58:00', 'available', 'barber'),
(69, 19, 'barber', '2025-07-27', '08:58:00', '08:58:00', 'available', 'barber'),
(70, 7, 'instructor', '2025-07-26', '08:58:00', '08:58:00', 'booked', 'lesson'),
(71, 7, 'instructor', '2025-07-28', '08:59:00', '08:59:00', 'available', 'lesson'),
(72, 7, 'instructor', '2025-07-27', '14:23:00', '15:23:00', 'available', 'lesson'),
(73, 7, 'instructor', '2025-07-29', '13:52:00', '13:52:00', 'available', 'lesson'),
(74, 7, 'instructor', '2025-07-30', '13:52:00', '13:52:00', 'available', 'lesson'),
(75, 18, 'instructor', '2025-08-15', '14:35:00', '14:35:00', 'booked', 'lesson'),
(76, 18, 'instructor', '2025-08-14', '14:35:00', '14:36:00', 'available', 'lesson'),
(77, 18, 'instructor', '2025-08-31', '10:05:00', '10:05:00', 'available', 'lesson'),
(78, 19, 'barber', '2025-09-01', '10:05:00', '10:05:00', 'available', 'barber'),
(80, 18, 'instructor', '2025-09-04', '21:44:00', '21:44:00', 'available', 'lesson'),
(81, 18, 'instructor', '2025-09-04', '22:45:00', '00:45:00', 'available', 'lesson');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `instructor_id` int(11) NOT NULL,
  `availability_id` int(11) NOT NULL,
  `booking_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('confirmed','cancelled','completed') DEFAULT 'confirmed',
  `service_type` enum('lesson','barber') DEFAULT 'lesson',
  `role` varchar(20) DEFAULT 'student'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `student_id`, `instructor_id`, `availability_id`, `booking_time`, `status`, `service_type`, `role`) VALUES
(7, 8, 1, 12, '2025-05-05 12:23:16', 'confirmed', 'lesson', 'student'),
(8, 9, 1, 14, '2025-05-05 16:05:17', 'confirmed', 'lesson', 'student'),
(10, 6, 1, 16, '2025-05-08 12:22:52', 'confirmed', 'lesson', 'student'),
(11, 9, 1, 13, '2025-05-08 18:19:49', 'confirmed', 'lesson', 'student'),
(12, 6, 1, 17, '2025-05-14 13:05:53', 'confirmed', 'lesson', 'student'),
(23, 6, 7, 35, '2025-06-03 12:14:43', 'confirmed', 'lesson', 'student'),
(30, 6, 7, 54, '2025-06-27 18:22:46', 'confirmed', 'lesson', 'student'),
(33, 6, 7, 60, '2025-06-30 18:19:05', 'confirmed', 'lesson', 'student'),
(34, 8, 19, 65, '2025-07-17 17:23:29', 'confirmed', 'barber', 'student'),
(45, 8, 7, 70, '2025-07-24 19:29:10', 'confirmed', 'lesson', 'student'),
(46, 8, 18, 75, '2025-08-13 17:38:38', 'confirmed', 'lesson', 'student');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `emoji` varchar(10) NOT NULL,
  `comment` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `student_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `emoji`, `comment`, `timestamp`, `student_id`) VALUES
(21, '😞', '', '2025-05-14 16:39:27', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` int(11) NOT NULL,
  `rated_user_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `rating_text` varchar(50) DEFAULT NULL,
  `emoji` varchar(10) NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reply` text DEFAULT NULL,
  `role` enum('instructor','barber') NOT NULL DEFAULT 'instructor'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`id`, `rated_user_id`, `student_id`, `rating_text`, `emoji`, `comment`, `created_at`, `reply`, `role`) VALUES
(10, 18, 6, 'good', '😊', 'geeft goed uitleg ', '2025-06-23 13:18:01', 'dank u', 'instructor'),
(11, 19, 6, 'excellent', '😍', 'ey kot wiri wreeeeed', '2025-06-23 17:56:05', 'thanks broski', 'barber'),
(12, 18, 6, 'very poor', '😠', 'ey rij lik wang poe\r\n', '2025-06-27 11:32:02', NULL, 'instructor'),
(13, 18, 6, 'excellent', '😍', 'geduld is 100%', '2025-06-27 11:32:30', NULL, 'instructor'),
(14, 18, 6, 'neutral', '😐', 'amang stil toemsi\r\nnex mi ne leri', '2025-06-27 11:32:54', NULL, 'instructor'),
(15, 18, 6, 'good', '😊', 'een calme aardige man', '2025-06-27 11:33:40', 'danku', 'instructor'),
(16, 18, 8, 'good', '😊', 'Geweldige instructuur 😁🙏', '2025-07-21 12:06:15', 'danku', 'instructor'),
(17, 7, 8, 'good', '😊', 'goed les', '2025-07-24 19:27:36', 'dankje', 'instructor'),
(18, 18, 6, 'poor', '😞', 'blabla', '2025-08-23 16:30:45', NULL, 'instructor'),
(19, 7, 8, 'good', '😊', 'Flexi mang ', '2025-08-23 16:31:07', NULL, 'instructor'),
(20, 7, 8, 'very poor', '😠', '', '2025-08-25 12:26:28', NULL, 'instructor'),
(21, 19, 8, 'very poor', '😠', '', '2025-08-25 12:37:04', NULL, 'barber'),
(22, 19, 8, 'excellent', '😍', 'walo mira pasi', '2025-08-25 19:04:19', NULL, 'barber'),
(23, 19, 8, 'good', '😊', 'Domi', '2025-08-31 01:38:15', NULL, 'barber');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','instructor','barber','admin') NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `driving_school` varchar(100) DEFAULT NULL,
  `barbershop_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `active` tinyint(1) DEFAULT 1,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `district` varchar(100) DEFAULT NULL,
  `id_card_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `full_name`, `phone`, `address`, `driving_school`, `barbershop_name`, `created_at`, `active`, `status`, `district`, `id_card_image`) VALUES
(1, 'dead', '$2y$10$wr0km9t7UZeCH1Ky3BctheYuZ6aMNQwIutZqYZaY5zPOAAZWzNuxm\r\n', 'student', 'blanco', '8858034', 'Holsteinstraat200', 'Sawil', NULL, '2025-04-17 17:12:09', 1, 'active', NULL, NULL),
(6, 'Max', '$2y$10$hsuRiThsnTXuDdHf8d/Bg.QTyZ.1mUeNzjNtKZpFDifM5FE6NvSYq', 'student', 'jhnnj', '8858038', NULL, NULL, NULL, '2025-04-22 12:33:54', 1, 'active', NULL, 'id_card_6_1751120312.jpg'),
(7, 'Martin', '$2y$10$U5NmtyZbfVv90boff2YYq.hgtFsiysQ7PUKpXAnpeEaFm37MIMa5.', 'instructor', 'Martin KapasiAwarie', '8858039', 'Plutostraat#39', 'Martin', NULL, '2025-04-22 13:11:48', 1, 'active', NULL, 'id_card_7_1753721488.jpg'),
(8, 'Nev', '$2y$10$OKunSJAwm3muYcm3W4KA/uF5yRBZe0xgiW/L6tgt1F3YzNwj3QbCu', 'student', 'Nevaeh Doornkamp', '368861', NULL, NULL, NULL, '2025-04-22 13:56:30', 1, 'active', NULL, 'id_card_8_1752772977.jpg'),
(9, 'terencia<3', '$2y$10$TADWmWBWHEzKhM6zzxE/tumDA4FfJ6p/vnIOpkZhw0NzZhtaEEMNq', 'student', 'terencia djasmo', '8456333', NULL, NULL, NULL, '2025-05-05 16:03:02', 1, 'active', NULL, NULL),
(17, 'admin', '$2y$10$VTN0f.pWuFWHDQojvgN1subEjRshM5/8nNXiBEvgPViFXp9lW2vqa', 'admin', 'Administrator', '0000000000', NULL, NULL, NULL, '2025-05-28 15:29:24', 1, 'active', NULL, NULL),
(18, 'iseaya', '$2y$10$SbunO6l7r9LvFxMBtcf/MOqYAhILVTDMS48gQ5JSXPs13991f8WwO', 'instructor', 'Iseaya D\'oliveira', '+5978858038', 'Holsteinstraat201', 'Rijschool Iseaya', NULL, '2025-05-28 15:52:43', 1, 'active', NULL, 'id_card_18_1751306692.jpeg'),
(19, 'mave', '$2y$10$ZBPpwypawHXgq9i4d8fMQOggFqNomhZaft3Wa/xdxd3GjmMXajg3G', 'barber', 'maverick', '8862466', 'welgedacht A', NULL, 'mozzy blendzz', '2025-06-19 14:53:24', 1, 'active', 'Wanica', 'id_card_19_1751306724.jpeg'),
(21, 'veux', '$2y$10$FRGRvq35LBkPoQ5Spui06uGt1ZrJN02qZDzzri5T/No9Kz7YfT9AK', 'barber', 'Jaier', '+597 8153818', 'Curacao straat', NULL, 'The Brother\'s Cuts', '2025-08-28 17:50:09', 1, 'active', 'Wanica', 'idcard_68b096d1d97b82.03789186.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `availability`
--
ALTER TABLE `availability`
  ADD PRIMARY KEY (`id`),
  ADD KEY `instructor_id` (`instructor_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `instructor_id` (`instructor_id`),
  ADD KEY `availability_id` (`availability_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `feedback_ibfk_1` (`student_id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `fk_rated_user` (`rated_user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `availability`
--
ALTER TABLE `availability`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `availability`
--
ALTER TABLE `availability`
  ADD CONSTRAINT `availability_ibfk_1` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`availability_id`) REFERENCES `availability` (`id`);

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `fk_rated_user` FOREIGN KEY (`rated_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
