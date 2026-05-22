-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: May 22, 2026 at 04:17 AM
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
-- Database: `service_desk`
--

-- --------------------------------------------------------

--
-- Table structure for table `activation_requests`
--

CREATE TABLE `activation_requests` (
  `request_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `requested_at` datetime DEFAULT current_timestamp(),
  `reviewed_at` datetime DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activation_requests`
--

INSERT INTO `activation_requests` (`request_id`, `user_id`, `reason`, `status`, `requested_at`, `reviewed_at`, `reviewed_by`) VALUES
(1, 2, 'User requested activation', 'Approved', '2026-01-24 23:45:27', '2026-01-24 23:45:57', 14),
(5, 16, 'User requested activation', 'Approved', '2026-01-27 09:10:56', '2026-01-27 09:12:08', 14),
(6, 2, 'User requested activation', 'Approved', '2026-01-27 09:11:57', '2026-01-27 09:12:06', 14),
(7, 2, 'User requested activation', 'Approved', '2026-02-04 16:09:01', '2026-02-04 16:09:19', 14),
(8, 2, 'User requested activation', 'Approved', '2026-02-09 15:43:43', '2026-02-09 15:43:56', 14);

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `role`, `action`, `created_at`) VALUES
(1, 1, NULL, 'Test audit log entry', '2026-01-21 22:37:47'),
(2, 3, NULL, 'User logged out', '2026-01-21 22:54:17'),
(3, 10, NULL, 'User logged in', '2026-01-21 22:55:25'),
(4, 10, NULL, 'User logged in', '2026-01-21 22:55:25'),
(5, 10, NULL, 'Created a new support ticket', '2026-01-21 22:59:41'),
(6, 10, NULL, 'User logged out', '2026-01-21 23:00:12'),
(7, 3, NULL, 'User logged in', '2026-01-21 23:00:25'),
(8, 3, NULL, 'User logged in', '2026-01-21 23:00:25'),
(9, 3, NULL, 'Assigned agent ID 2 to ticket ID 4', '2026-01-21 23:00:47'),
(10, 3, NULL, 'User logged out', '2026-01-21 23:03:23'),
(11, 2, NULL, 'User logged in', '2026-01-21 23:03:37'),
(12, 2, NULL, 'User logged in', '2026-01-21 23:03:37'),
(13, 2, NULL, 'User logged out', '2026-01-21 23:04:05'),
(14, 3, NULL, 'User logged in', '2026-01-21 23:04:17'),
(15, 3, NULL, 'User logged in', '2026-01-21 23:04:17'),
(16, 3, NULL, 'User logged out', '2026-01-21 23:06:33'),
(17, 11, NULL, 'User logged in', '2026-01-22 15:27:24'),
(18, 11, NULL, 'User logged in', '2026-01-22 15:27:24'),
(19, 11, NULL, 'Created a new support ticket', '2026-01-22 15:29:01'),
(20, 11, NULL, 'User logged out', '2026-01-22 15:29:43'),
(21, 2, NULL, 'User logged in', '2026-01-22 15:29:58'),
(22, 2, NULL, 'User logged in', '2026-01-22 15:29:58'),
(23, 2, NULL, 'User logged out', '2026-01-22 15:30:44'),
(24, 3, NULL, 'User logged in', '2026-01-22 15:31:00'),
(25, 3, NULL, 'User logged in', '2026-01-22 15:31:00'),
(26, 3, NULL, 'Assigned agent ID 2 to ticket ID 5', '2026-01-22 15:31:46'),
(27, 3, NULL, 'User logged out', '2026-01-22 15:33:20'),
(28, 3, NULL, 'User logged in', '2026-01-22 18:15:45'),
(29, 3, NULL, 'User logged in', '2026-01-22 18:15:45'),
(30, 3, NULL, 'Exported tickets as CSV', '2026-01-22 18:16:08'),
(31, 3, NULL, 'User logged out', '2026-01-22 19:08:11'),
(32, 2, NULL, 'User logged in', '2026-01-22 19:08:35'),
(33, 2, NULL, 'User logged out', '2026-01-22 19:08:38'),
(34, 3, NULL, 'Admin logged in', '2026-01-22 20:02:39'),
(35, 3, NULL, 'Exported tickets as CSV', '2026-01-22 20:12:21'),
(36, 3, NULL, 'Exported tickets as CSV', '2026-01-22 21:32:34'),
(37, 3, NULL, 'Exported tickets as CSV', '2026-01-22 21:34:55'),
(38, 3, NULL, 'Exported tickets as CSV', '2026-01-22 21:36:41'),
(39, 3, NULL, 'Exported tickets as CSV', '2026-01-22 21:38:05'),
(40, 3, NULL, 'User logged out', '2026-01-22 22:57:09'),
(41, 3, NULL, 'Admin logged in', '2026-01-22 23:02:12'),
(42, 3, NULL, 'Admin logged out', '2026-01-22 23:02:15'),
(43, 3, NULL, 'Admin logged in', '2026-01-22 23:04:11'),
(44, 3, NULL, 'Admin logged out', '2026-01-22 23:04:21'),
(45, 3, NULL, 'Admin logged in', '2026-01-22 23:07:35'),
(46, 3, NULL, 'Admin logged out', '2026-01-22 23:07:40'),
(47, 3, NULL, 'Admin logged in', '2026-01-23 08:19:04'),
(48, 3, NULL, 'Admin logged out', '2026-01-23 08:19:17'),
(49, 12, NULL, 'Admin logged in', '2026-01-23 08:22:37'),
(50, 3, NULL, 'Admin logged in', '2026-01-23 10:08:36'),
(51, 3, NULL, 'Assigned agent ID 7 to ticket ID 5', '2026-01-23 10:08:57'),
(52, 3, NULL, 'Admin logged out', '2026-01-23 10:09:36'),
(53, 12, NULL, 'Admin logged in', '2026-01-23 10:36:55'),
(54, 12, NULL, 'Admin deleted account', '2026-01-23 10:41:42'),
(55, 2, NULL, 'User logged in', '2026-01-23 10:46:42'),
(56, 2, NULL, 'User logged out', '2026-01-23 10:56:50'),
(57, 1, NULL, 'User logged in', '2026-01-23 10:57:12'),
(58, 3, NULL, 'Admin logged in', '2026-01-23 11:04:27'),
(59, 13, NULL, 'User logged in', '2026-01-23 13:55:58'),
(60, 2, NULL, 'User logged in', '2026-01-23 13:56:45'),
(61, 2, NULL, 'User logged out', '2026-01-23 13:57:08'),
(62, 3, NULL, 'Admin logged in', '2026-01-23 13:57:36'),
(63, 3, NULL, 'Exported tickets as CSV', '2026-01-23 16:35:35'),
(64, 3, NULL, 'Admin logged out', '2026-01-23 17:13:23'),
(65, 3, NULL, 'Admin logged in', '2026-01-24 12:12:02'),
(66, 3, NULL, 'Admin logged out', '2026-01-24 12:17:58'),
(67, 2, NULL, 'User logged in', '2026-01-24 19:47:46'),
(68, 2, NULL, 'User logged out', '2026-01-24 19:47:49'),
(69, 3, NULL, 'Admin logged in', '2026-01-24 20:19:43'),
(70, 3, NULL, 'Admin logged out', '2026-01-24 20:32:41'),
(71, 14, NULL, 'Super Admin logged in', '2026-01-24 20:33:10'),
(72, 14, NULL, 'Super Admin assigned agent ID 2 to ticket ID 5', '2026-01-24 21:11:10'),
(73, 14, NULL, 'Super Admin unassigned agent from ticket ID 5', '2026-01-24 21:11:14'),
(74, 14, NULL, 'Super Admin exported tickets as PDF', '2026-01-24 21:11:19'),
(75, 14, NULL, 'Exported Agent Performance PDF', '2026-01-24 21:43:47'),
(76, 14, NULL, 'Exported Agent Performance CSV', '2026-01-24 21:46:08'),
(77, 14, NULL, 'Exported Agent Performance PDF', '2026-01-24 22:06:16'),
(78, 14, NULL, 'Exported Agent Performance CSV', '2026-01-24 22:06:23'),
(79, 14, NULL, 'Deactivated user ID 10', '2026-01-24 22:39:32'),
(80, 14, NULL, 'Activated user ID 10', '2026-01-24 22:39:35'),
(81, 14, NULL, 'SUPER ADMIN: Changed admin status (ID 3 → Active)', '2026-01-24 23:07:24'),
(82, 14, NULL, 'SUPER ADMIN: Deactivated admin ID 15', '2026-01-24 23:09:36'),
(83, 14, NULL, 'SUPER ADMIN: Activated admin ID 15', '2026-01-24 23:09:40'),
(84, 14, NULL, 'Admin logged out', '2026-01-24 23:10:56'),
(85, 15, NULL, 'Admin logged in', '2026-01-24 23:12:32'),
(86, 15, NULL, 'Admin logged out', '2026-01-24 23:29:59'),
(87, 2, NULL, 'User logged in', '2026-01-24 23:36:32'),
(88, 2, NULL, 'User logged out', '2026-01-24 23:39:20'),
(89, 2, NULL, 'User logged in', '2026-01-24 23:39:36'),
(90, 2, NULL, 'User logged out', '2026-01-24 23:41:01'),
(91, 2, NULL, 'User logged in', '2026-01-24 23:41:16'),
(92, 2, NULL, 'Agent temporarily deactivated account', '2026-01-24 23:45:09'),
(93, 2, NULL, 'Requested account activation', '2026-01-24 23:45:27'),
(94, 14, NULL, 'Super Admin logged in', '2026-01-24 23:45:49'),
(95, 2, NULL, 'User logged in', '2026-01-24 23:46:24'),
(96, 2, NULL, 'User logged out', '2026-01-24 23:47:54'),
(97, 6, NULL, 'User logged in', '2026-01-24 23:49:15'),
(98, 6, NULL, 'User logged in', '2026-01-24 23:53:29'),
(99, 6, NULL, 'User logged in', '2026-01-24 23:55:44'),
(100, 6, NULL, 'End User temporarily deactivated account', '2026-01-24 23:56:04'),
(101, 14, NULL, 'Super Admin logged in', '2026-01-25 00:01:53'),
(102, 14, NULL, 'Super Admin logged out', '2026-01-25 00:07:15'),
(103, 16, NULL, 'User logged in', '2026-01-25 08:06:01'),
(104, 16, NULL, 'Created a new support ticket', '2026-01-25 08:07:28'),
(105, 16, NULL, 'User logged out', '2026-01-25 08:07:55'),
(106, 17, NULL, 'User logged in', '2026-01-25 08:09:00'),
(107, 17, NULL, 'User logged out', '2026-01-25 08:09:22'),
(108, 14, NULL, 'Super Admin logged in', '2026-01-25 08:11:28'),
(109, 14, NULL, 'Super Admin assigned agent ID 17 to ticket ID 6', '2026-01-25 08:11:57'),
(110, 14, NULL, 'Super Admin assigned agent ID 17 to ticket ID 5', '2026-01-25 08:12:04'),
(111, 14, NULL, 'Super Admin exported tickets as PDF', '2026-01-25 08:35:05'),
(112, 14, NULL, 'Super Admin exported tickets as CSV', '2026-01-25 08:35:12'),
(113, 14, NULL, 'Exported Agent Performance PDF', '2026-01-25 08:35:29'),
(114, 14, NULL, 'Exported Agent Performance CSV', '2026-01-25 08:35:32'),
(115, 14, NULL, 'Super Admin logged out', '2026-01-25 08:36:52'),
(116, 15, NULL, 'Admin logged in', '2026-01-25 08:37:32'),
(117, 15, NULL, 'Exported Agent Performance CSV', '2026-01-25 08:48:52'),
(118, 15, NULL, 'Admin logged out', '2026-01-25 09:01:26'),
(119, 14, NULL, 'Super Admin logged in', '2026-01-25 09:02:38'),
(120, 14, NULL, 'Super Admin logged out', '2026-01-25 09:10:11'),
(121, 14, NULL, 'Super Admin logged out', '2026-01-25 09:16:20'),
(122, 14, NULL, 'Super Admin logged out', '2026-01-25 09:18:33'),
(123, 14, NULL, 'Super Admin logged out', '2026-01-25 09:28:20'),
(134, 14, NULL, 'Activated user ID 6', '2026-01-25 10:05:57'),
(135, 14, NULL, 'Deleted user ID 17', '2026-01-25 10:06:01'),
(136, 14, NULL, 'Super Admin logged out', '2026-01-25 10:06:25'),
(137, 15, NULL, 'Admin logged in', '2026-01-25 10:06:41'),
(138, 15, NULL, 'Admin logged out', '2026-01-25 10:07:16'),
(139, 14, NULL, 'SUPER ADMIN: Deactivated admin ID 15', '2026-01-27 08:41:15'),
(140, 14, NULL, 'Super Admin logged out', '2026-01-27 08:41:28'),
(141, 14, NULL, 'SUPER ADMIN: Activated admin ID 15', '2026-01-27 08:42:52'),
(142, 14, NULL, 'Deactivated user ID 16', '2026-01-27 09:10:19'),
(143, 16, NULL, 'Requested account activation', '2026-01-27 09:10:56'),
(144, 14, NULL, 'Deactivated user ID 9', '2026-01-27 09:11:22'),
(145, 14, NULL, 'Activated user ID 9', '2026-01-27 09:11:28'),
(146, 14, NULL, 'Deactivated user ID 2', '2026-01-27 09:11:33'),
(147, 2, NULL, 'Requested account activation', '2026-01-27 09:11:57'),
(148, 16, NULL, 'User logged in', '2026-01-27 09:12:28'),
(149, 16, NULL, 'User logged out', '2026-01-27 09:12:31'),
(150, 2, NULL, 'User logged in', '2026-01-27 09:12:43'),
(151, 2, NULL, 'User logged out', '2026-01-27 09:12:52'),
(152, 14, NULL, 'SUPER ADMIN: Deactivated admin ID 15', '2026-01-27 09:13:59'),
(153, 14, NULL, 'SUPER ADMIN: Activated admin ID 15', '2026-01-27 09:14:02'),
(154, 14, NULL, 'Super Admin assigned agent ID 7 to ticket ID 6', '2026-01-27 09:14:56'),
(155, 14, NULL, 'Super Admin assigned agent ID 7 to ticket ID 5', '2026-01-27 09:15:03'),
(156, 7, NULL, 'User logged in', '2026-01-27 09:15:49'),
(157, 14, NULL, 'Super Admin logged out', '2026-01-27 09:34:09'),
(158, 3, NULL, 'Admin logged in', '2026-01-27 09:34:24'),
(159, 3, NULL, 'Admin logged out', '2026-01-27 09:37:58'),
(160, 14, NULL, 'Super Admin logged out', '2026-01-29 12:58:57'),
(161, 3, NULL, 'Admin logged in', '2026-01-29 12:59:35'),
(162, 3, NULL, 'Admin logged out', '2026-01-29 13:00:49'),
(163, 16, NULL, 'User logged in', '2026-01-29 13:01:26'),
(164, 16, NULL, 'User logged out', '2026-01-29 13:02:14'),
(165, 6, NULL, 'User logged in', '2026-02-02 15:13:34'),
(166, 6, NULL, 'Created a new support ticket', '2026-02-02 15:14:33'),
(167, 6, NULL, 'End User temporarily deactivated account', '2026-02-02 15:15:08'),
(168, 14, NULL, 'Activated user ID 6', '2026-02-02 15:22:07'),
(169, 6, NULL, 'User logged in', '2026-02-04 15:52:08'),
(170, 2, NULL, 'User logged in', '2026-02-04 16:00:00'),
(171, 2, NULL, 'User logged out', '2026-02-04 16:00:37'),
(172, 14, NULL, 'Exported Agent Performance PDF', '2026-02-04 16:02:50'),
(173, 14, NULL, 'Super Admin logged out', '2026-02-04 16:05:11'),
(174, 14, NULL, 'SUPER ADMIN: Deactivated admin ID 3', '2026-02-04 16:06:29'),
(175, 14, NULL, 'Deactivated user ID 2', '2026-02-04 16:08:35'),
(176, 2, NULL, 'Requested account activation', '2026-02-04 16:09:01'),
(177, 2, NULL, 'User logged in', '2026-02-04 16:09:45'),
(178, 2, NULL, 'User logged out', '2026-02-04 16:09:59'),
(179, 22, NULL, 'User logged in', '2026-02-05 20:51:24'),
(180, 22, NULL, 'User logged in', '2026-02-06 14:31:55'),
(181, 22, NULL, 'User logged out', '2026-02-06 14:43:29'),
(182, 23, NULL, 'User logged in', '2026-02-06 14:44:27'),
(183, 23, NULL, 'User logged in', '2026-02-09 07:42:21'),
(184, 23, NULL, 'User logged out', '2026-02-09 08:19:24'),
(185, 22, NULL, 'User logged in', '2026-02-09 08:19:37'),
(186, 22, NULL, 'User logged out', '2026-02-09 08:20:56'),
(187, 23, NULL, 'User logged in', '2026-02-09 08:21:09'),
(188, 23, NULL, 'User logged out', '2026-02-09 08:34:16'),
(189, 14, NULL, 'SUPER ADMIN: Activated admin ID 3', '2026-02-09 09:03:59'),
(190, 14, NULL, 'Super Admin logged out', '2026-02-09 09:04:06'),
(191, 3, NULL, 'Admin logged in', '2026-02-09 09:04:23'),
(192, 3, NULL, 'Admin logged in', '2026-02-09 14:35:50'),
(193, 3, NULL, 'Admin logged out', '2026-02-09 14:42:44'),
(194, 3, NULL, 'Admin logged in', '2026-02-09 14:42:55'),
(195, 3, NULL, 'Admin logged out', '2026-02-09 14:44:28'),
(196, 3, NULL, 'Admin logged in', '2026-02-09 14:44:51'),
(197, 3, NULL, 'Admin logged out', '2026-02-09 15:42:15'),
(198, 14, NULL, 'Deactivated user ID 2', '2026-02-09 15:43:10'),
(199, 2, NULL, 'Requested account activation', '2026-02-09 15:43:43'),
(200, 14, NULL, 'Super Admin logged out', '2026-02-09 15:44:22'),
(201, 2, NULL, 'User logged in', '2026-02-27 15:03:23'),
(202, 3, NULL, 'Admin logged in', '2026-03-26 13:06:04'),
(203, 3, NULL, 'Admin logged in', '2026-04-09 16:29:40'),
(204, 3, NULL, 'Admin logged out', '2026-04-09 16:30:52'),
(205, 14, NULL, 'Deactivated user ID 10', '2026-04-09 16:32:09'),
(206, 14, NULL, 'Activated user ID 10', '2026-04-09 16:32:12'),
(207, 14, NULL, 'Deactivated user ID 9', '2026-04-09 16:32:14'),
(208, 3, NULL, 'Admin logged in', '2026-04-09 19:10:20'),
(209, 3, NULL, 'Admin logged in', '2026-04-09 19:12:36'),
(210, 3, NULL, 'Admin logged out', '2026-04-09 19:21:27'),
(211, 3, NULL, 'Admin logged in', '2026-04-12 20:36:24'),
(212, 3, NULL, 'Admin logged out', '2026-04-12 20:39:42');

-- --------------------------------------------------------

--
-- Table structure for table `performance`
--

CREATE TABLE `performance` (
  `performance_id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `agent_id` int(11) NOT NULL,
  `response_time` int(11) NOT NULL,
  `resolution_time` int(11) NOT NULL,
  `sla_status` varchar(20) NOT NULL,
  `recorded_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `report_id` int(11) NOT NULL,
  `generated_date` datetime NOT NULL DEFAULT current_timestamp(),
  `admin_id` int(11) NOT NULL,
  `report_type` varchar(50) NOT NULL,
  `report_summary` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `ticket_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `priority` varchar(20) NOT NULL,
  `status` varchar(20) NOT NULL,
  `resolution_notes` text DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  `agent_id` int(11) DEFAULT NULL,
  `assigned_agent_id` int(11) DEFAULT NULL,
  `resolution_note` text DEFAULT NULL,
  `resolved_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`ticket_id`, `title`, `description`, `priority`, `status`, `resolution_notes`, `created_date`, `user_id`, `agent_id`, `assigned_agent_id`, `resolution_note`, `resolved_by`) VALUES
(1, 'Login issues', 'can\'t enter the email id while login', 'Medium', 'Resolved', NULL, '2026-01-20 13:32:32', 6, NULL, 7, 'resolved completely', NULL),
(2, 'Invalid password', 'login is not working', 'Medium', 'Resolved', '', '2026-01-20 13:36:34', 6, 2, 7, 'The bug has been fixed.', NULL),
(4, 'log in takes time', 'whenever tries to log in it takes a longer time than usual', 'Medium', 'Resolved', NULL, '2026-01-21 22:59:41', 10, NULL, 2, 'bugs fixed', NULL),
(5, 'Login Issue', 'during log in it shows invalid password', 'Medium', 'Resolved', NULL, '2026-01-22 15:29:01', 11, NULL, 7, 'bugs fixed', NULL),
(6, 'Account Creation', 'Can\'t create account ', 'Medium', 'Resolved', NULL, '2026-01-25 08:07:28', 16, NULL, 7, 'bugs fixed', NULL),
(7, 'login issue', 'invalid password ', 'Medium', 'Open', NULL, '2026-02-02 15:14:33', 6, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `is_super_admin` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `role`, `created_at`, `status`, `is_super_admin`) VALUES
(2, 'Agent One', 'agent@test.com', '123', 'Agent', '2026-01-20 09:16:29', 'Active', 0),
(3, 'Admin One', 'admin@test.com', '123', 'Admin', '2026-01-20 09:16:29', 'Active', 0),
(6, 'User1', 'user@gmail.com', '123', 'End User', '2026-01-20 10:07:29', 'Active', 0),
(7, 'Agent Two', 'agent2@test.com', '123', 'Agent', '2026-01-21 08:47:25', 'Active', 0),
(9, 'suryakant sahoo', 'surya@gmail.com', '12345', 'Agent', '2026-01-21 15:25:48', 'Inactive', 0),
(10, 'User 2', 'user2@test.com', '123', 'End User', '2026-01-21 22:55:12', 'Active', 0),
(11, 'User Two', 'user2@gmail.com', '123', 'End User', '2026-01-22 15:25:35', 'Active', 0),
(14, 'Super Admin', 'superadmin@gmail.com', '123', 'Admin', '2026-01-24 20:14:38', 'Active', 1),
(15, 'Admin Two', 'admin2@gmail.com', '123', 'Admin', '2026-01-24 22:59:45', 'Active', 0),
(16, 'Test User', 'user@test.com', '123', 'End User', '2026-01-25 08:05:45', 'Active', 0),
(22, 'User One', 'user1@gmail.com', '123', 'End User', '2026-02-05 20:51:08', 'Active', 0),
(23, 'Agent One ', 'agent1@gmail.com', '123', 'Agent', '2026-02-06 14:44:14', 'Active', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activation_requests`
--
ALTER TABLE `activation_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `performance`
--
ALTER TABLE `performance`
  ADD PRIMARY KEY (`performance_id`),
  ADD KEY `ticket_id` (`ticket_id`),
  ADD KEY `agent_id` (`agent_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`ticket_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `agent_id` (`agent_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activation_requests`
--
ALTER TABLE `activation_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=213;

--
-- AUTO_INCREMENT for table `performance`
--
ALTER TABLE `performance`
  MODIFY `performance_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `ticket_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activation_requests`
--
ALTER TABLE `activation_requests`
  ADD CONSTRAINT `activation_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `performance`
--
ALTER TABLE `performance`
  ADD CONSTRAINT `performance_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`ticket_id`),
  ADD CONSTRAINT `performance_ibfk_2` FOREIGN KEY (`agent_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`agent_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
