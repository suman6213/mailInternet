-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 30, 2025 at 10:49 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `simple_banking`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(2, 'suman', '$2y$10$lF6RAxJeaT3aPitEEnWcJuNjly2obvyQ94oQcKd9EMMCw0Q2thEiK');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `type` enum('success','error','info') NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `type`, `is_read`, `created_at`) VALUES
(1, 5, 'Money transferred successfully to user1.', 'success', 1, '2025-08-11 17:45:59'),
(2, 4, 'You received a transfer of $100.00 from user2.', 'success', 1, '2025-08-11 17:45:59'),
(3, 5, 'Money transferred successfully to user1.', 'success', 1, '2025-08-11 18:18:12'),
(4, 4, 'You received a transfer of $100.00 from user2.', 'success', 1, '2025-08-11 18:18:12'),
(5, 5, 'Your profile update request has been accepted.', 'success', 1, '2025-08-13 17:58:41'),
(6, 4, 'Your profile update request has been rejected.', 'success', 1, '2025-08-15 15:49:42'),
(7, 4, 'You sent $600 to mango', 'success', 1, '2025-08-30 04:34:07'),
(8, 5, 'You received $600 from user1', 'success', 1, '2025-08-30 04:34:07'),
(9, 5, 'You sent $500 to user1', 'success', 1, '2025-08-30 05:21:02'),
(10, 4, 'You received $500 from mango', 'success', 1, '2025-08-30 05:21:02'),
(11, 4, 'You sent $100 to user2', 'success', 1, '2025-08-30 07:04:26'),
(12, 5, 'You received $100 from user1', 'success', 0, '2025-08-30 07:04:26'),
(13, 4, 'Your profile update request has been accepted. New username: user3', 'success', 1, '2025-08-30 07:05:37'),
(14, 4, 'Your profile update request has been accepted. New username: user1', 'success', 1, '2025-08-30 07:11:49'),
(15, 4, 'Your profile update request has been accepted. New username: user3', 'success', 1, '2025-08-30 07:16:40');

-- --------------------------------------------------------

--
-- Table structure for table `profile_update_requests`
--

CREATE TABLE `profile_update_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `new_username` varchar(50) DEFAULT NULL,
  `new_password` varchar(255) DEFAULT NULL,
  `status` enum('pending','accepted','rejected') NOT NULL DEFAULT 'pending',
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `processed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `profile_update_requests`
--

INSERT INTO `profile_update_requests` (`id`, `user_id`, `new_username`, `new_password`, `status`, `requested_at`, `processed_at`) VALUES
(1, 4, 'apple', '$2y$10$2uUZm/KOCEOLlduDJsVFH.MO3GMeP2JUNLA0UY693yOdefQlQdXrq', 'rejected', '2025-08-11 17:06:50', NULL),
(2, 4, 'mango', '$2y$10$Ymg2WbbhYF25xPKyGkh7m.0Hli064NG4xR7t8caF11kLjgzt9uw4S', 'rejected', '2025-08-13 16:57:32', NULL),
(3, 4, 'mango', '$2y$10$iNC/kmjSrdcyJcfQpFmi.OJnrSB8RW6t1L6Y9.3N2lxjiYJlWDiP.', 'rejected', '2025-08-13 17:30:26', NULL),
(4, 5, 'mango', '$2y$10$8EI1dnFJVoCa97HJRhmje.ZHcEvajyMSEGaxm.bxivLd6ytRKzIdm', 'accepted', '2025-08-13 17:36:21', '2025-08-13 23:24:52'),
(5, 5, 'user2', '$2y$10$QUaegHrffs4q13mfkYLAjuqg2ySGvm8dc3gFO0fnvZYIA9DvKYUM6', 'accepted', '2025-08-13 17:45:49', '2025-08-13 23:31:08'),
(6, 5, 'mango', '$2y$10$zqSA8SINyKcxWrGx8hOFmeZKrzHdo//5IaOLV7p1Ua.ZB1ktB.9iC', 'accepted', '2025-08-13 17:58:19', '2025-08-13 23:43:41'),
(7, 4, 'apple', '$2y$10$e3IMtCsAkX2CNUvKD1OghueSq5jDTsbMYiIPBGK7CEJc7YPjEi0Pi', 'rejected', '2025-08-15 15:48:52', '2025-08-15 21:34:42'),
(8, 5, 'user2', NULL, 'accepted', '2025-08-30 05:21:59', NULL),
(9, 4, 'user3', NULL, 'accepted', '2025-08-30 07:05:04', NULL),
(10, 4, 'user1', NULL, 'accepted', '2025-08-30 07:11:32', NULL),
(11, 4, 'user3', '$2y$10$OR55t2em5UpgPvoZI9daIums1NKp4EDDuRg89wc32E.ZBZoxsak1y', 'accepted', '2025-08-30 07:16:12', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `sender_id`, `receiver_id`, `amount`, `transaction_date`) VALUES
(1, 4, 5, 100.00, '2025-08-11 17:35:59'),
(2, 5, 4, 50.00, '2025-08-11 17:38:31'),
(3, 5, 4, 50.00, '2025-08-11 17:39:37'),
(4, 4, 5, 500.00, '2025-08-11 17:43:32'),
(5, 5, 4, 100.00, '2025-08-11 17:45:59'),
(6, 5, 4, 100.00, '2025-08-11 18:18:12'),
(7, 5, 4, 100.00, '2025-08-13 17:59:40'),
(8, 4, 4, 100.00, '2025-08-15 15:48:21'),
(9, 4, 5, 200.00, '2025-08-30 04:29:11'),
(10, 4, 5, 600.00, '2025-08-30 04:34:07'),
(11, 5, 4, 500.00, '2025-08-30 05:21:02'),
(12, 4, 5, 100.00, '2025-08-30 07:04:26');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `balance` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `balance`) VALUES
(4, 'user3', '$2y$10$OR55t2em5UpgPvoZI9daIums1NKp4EDDuRg89wc32E.ZBZoxsak1y', 9400.00),
(5, 'user2', '$2y$10$zqSA8SINyKcxWrGx8hOFmeZKrzHdo//5IaOLV7p1Ua.ZB1ktB.9iC', 600.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `profile_update_requests`
--
ALTER TABLE `profile_update_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

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
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `profile_update_requests`
--
ALTER TABLE `profile_update_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `profile_update_requests`
--
ALTER TABLE `profile_update_requests`
  ADD CONSTRAINT `profile_update_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
