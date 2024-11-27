-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 27, 2024 at 06:38 PM
-- Server version: 8.3.0
-- PHP Version: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `construction`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

DROP TABLE IF EXISTS `activities`;
CREATE TABLE IF NOT EXISTS `activities` (
  `id` int NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_activities_user` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

DROP TABLE IF EXISTS `inventory`;
CREATE TABLE IF NOT EXISTS `inventory` (
  `id` int NOT NULL AUTO_INCREMENT,
  `item_name` varchar(255) NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_inventory_user` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `item_name`, `quantity`, `price`, `created_at`, `updated_at`, `user_id`) VALUES
(1, 'Cement', 9, 110000.00, '2024-11-27 13:23:54', '2024-11-27 13:23:54', NULL),
(2, 'Cement', 9, 110000.00, '2024-11-27 13:32:55', '2024-11-27 13:32:55', NULL),
(3, 'Cement', 9, 110000.00, '2024-11-27 13:36:07', '2024-11-27 13:36:07', NULL),
(4, 'Cement', 9, 110000.00, '2024-11-27 13:40:44', '2024-11-27 13:40:44', NULL),
(5, 'Cement', 9, 110000.00, '2024-11-27 14:10:15', '2024-11-27 14:10:15', NULL),
(6, 'Cement', 9, 110000.00, '2024-11-27 14:12:55', '2024-11-27 14:12:55', NULL),
(7, 'Machines', 1, 100000.00, '2024-11-27 15:01:47', '2024-11-27 15:01:47', NULL),
(8, 'Machines', 1, 100000.00, '2024-11-27 15:02:00', '2024-11-27 15:02:00', NULL),
(9, 'dgfhgjh', 1, 455.00, '2024-11-27 16:14:04', '2024-11-27 16:14:04', NULL),
(10, 'dgfhgjh', 1, 455.00, '2024-11-27 16:14:20', '2024-11-27 16:14:20', NULL),
(11, 'dgfhgjh', 1, 455.00, '2024-11-27 16:15:23', '2024-11-27 16:15:23', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
CREATE TABLE IF NOT EXISTS `projects` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `status` enum('In Progress','Completed','Pending') NOT NULL,
  `deadline` date NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `worker_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_user` (`worker_id`)
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `name`, `status`, `deadline`, `created_at`, `worker_id`) VALUES
(1, 'karuma construction', 'Pending', '2024-11-23', '2024-11-22 18:43:10', NULL),
(2, 'gulu construction', 'Completed', '2024-11-29', '2024-11-22 18:44:11', NULL),
(3, 'gulu construction', 'Completed', '2024-11-29', '2024-11-22 18:46:54', NULL),
(4, 'gulu construction', 'Completed', '2024-11-29', '2024-11-22 18:47:57', NULL),
(5, 'gulu construction', 'Completed', '2024-11-29', '2024-11-22 18:49:43', NULL),
(6, 'gulu construction', 'Completed', '2024-11-29', '2024-11-22 18:51:02', NULL),
(7, 'gulu construction', 'Completed', '2024-11-29', '2024-11-22 18:51:24', NULL),
(8, 'gulu construction', 'Completed', '2024-11-29', '2024-11-22 18:51:41', NULL),
(9, 'gulu construction', 'Completed', '2024-11-29', '2024-11-22 18:51:55', NULL),
(10, 'gulu construction', 'Completed', '2024-11-29', '2024-11-22 18:52:15', NULL),
(11, 'gulu construction', 'Completed', '2024-11-29', '2024-11-22 18:55:31', NULL),
(12, 'Fundi Bots Constructions', 'In Progress', '2024-11-21', '2024-11-23 05:10:56', NULL),
(13, 'Fundi Bots Constructions', 'In Progress', '2024-11-21', '2024-11-23 05:11:26', NULL),
(14, 'Fundi Bots Constructions', 'In Progress', '2024-11-21', '2024-11-23 05:14:24', NULL),
(15, 'Fundi Bots Constructions', 'In Progress', '2024-11-21', '2024-11-23 05:15:07', NULL),
(16, 'Fundi Bots Constructions', 'In Progress', '2024-11-21', '2024-11-23 05:17:44', NULL),
(17, 'Fundi Bots Constructions', 'In Progress', '2024-11-21', '2024-11-23 05:38:54', NULL),
(18, 'Project PP', 'In Progress', '2020-02-01', '2024-11-23 06:42:47', NULL),
(19, 'Project yy', 'In Progress', '2024-11-20', '2024-11-23 08:36:16', NULL),
(20, 'Project yy', 'In Progress', '2024-11-20', '2024-11-23 08:37:25', NULL),
(21, 'Project yy', 'In Progress', '2024-11-20', '2024-11-23 08:37:56', NULL),
(22, 'Project yy', 'In Progress', '2024-11-20', '2024-11-23 08:38:26', NULL),
(23, 'Project yy', 'In Progress', '2024-11-20', '2024-11-23 08:38:55', NULL),
(24, 'Fundi Bots Constructions', 'Pending', '2024-11-27', '2024-11-23 09:06:36', NULL),
(25, 'Fundi Bots Constructions', 'Pending', '2024-11-27', '2024-11-23 09:12:43', NULL),
(26, 'Fundi Bots Constructions', 'Pending', '2024-11-27', '2024-11-23 09:14:24', NULL),
(27, 'KQM construction', 'Completed', '2024-11-05', '2024-11-23 15:40:33', NULL),
(28, 'ghjkl;', 'Completed', '2024-11-14', '2024-11-23 20:06:17', NULL),
(29, 'fghjk', 'Completed', '2024-11-19', '2024-11-24 12:56:28', NULL),
(30, 'kongo', 'Pending', '2024-11-27', '2024-11-27 18:08:52', 16),
(31, 'kongo', 'Pending', '2024-11-27', '2024-11-27 18:10:17', 16),
(32, 'kongo', 'Pending', '2024-11-27', '2024-11-27 18:12:53', 16),
(33, 'kongo', 'Pending', '2024-11-27', '2024-11-27 18:14:18', 16),
(34, 'kongo', 'Pending', '2024-11-27', '2024-11-27 18:14:59', 16),
(35, 'kongo', 'Pending', '2024-11-27', '2024-11-27 18:18:13', 16),
(36, 'kongo', 'Pending', '2024-11-27', '2024-11-27 18:18:29', 16),
(37, 'kongo', 'Completed', '2024-11-28', '2024-11-27 18:18:52', 12),
(38, 'kongo', 'Completed', '2024-11-28', '2024-11-27 18:20:31', 12),
(39, 'ugBas', 'Pending', '2024-11-27', '2024-11-27 18:21:09', 14),
(40, 'ugBas', 'Pending', '2024-11-27', '2024-11-27 18:27:06', 13),
(41, 'ugBas', 'Pending', '2024-11-27', '2024-11-27 18:29:07', 13),
(42, 'halo', 'Completed', '2024-11-27', '2024-11-27 18:29:39', 14),
(43, 'halo', 'Completed', '2024-11-27', '2024-11-27 18:31:42', 14),
(44, 'halo', 'Completed', '2024-11-27', '2024-11-27 18:31:50', 14),
(45, 'halo', 'Completed', '2024-11-27', '2024-11-27 18:32:48', 14);

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

DROP TABLE IF EXISTS `registrations`;
CREATE TABLE IF NOT EXISTS `registrations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `status` enum('Pending','Completed') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','manager','worker') NOT NULL,
  `profile_picture` varchar(255) DEFAULT 'default-avatar.png',
  `status` enum('pending','active') DEFAULT 'pending',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `profile_picture`, `status`) VALUES
(16, 'people', 'people@gmail.com', '$2y$10$wNlhrJ/tNhSFEddo7V09QuGI258BBNmINwxItCK4KDYzTiZ/EljWO', '', 'default-avatar.png', 'pending'),
(2, 'Honoriscausa', 'admin@admin.com', '$2y$10$Ft3.KG.dKPmd2t0lwvG7DeMfbIs5s6dYbgt00qdhMMaJIHKP1rR/6', 'admin', 'default-avatar.png', 'pending'),
(3, 'manager', 'manager@gmail.com', '$2y$10$VZEvPAedKziu9tbw2pnpceI8Ol4e1hFIxFWw4Zmsuy6KI04lfo6LW', 'manager', 'default-avatar.png', 'pending'),
(4, 'laborer', 'loborer@email.com', '$2y$10$6TDHaWLIcWAzCO7s8sVyLuVIfM/.PNc.QN7a6L5ZJrSjqbFhzNs0S', '', 'default-avatar.png', 'pending'),
(5, 'josh', 'josh@email.com', '$2y$10$c6ya2sY8Ykl5UIR3JjOkk.vU9vg0Ndkab3Q6AELit0/XOsne4Kw.q', '', 'default-avatar.png', 'pending'),
(6, 'elviswatmon', 'elviswatmon242@gmail.com', '$2y$10$AUMn2.eUPe0zgpQFX2W2uOw.VBZeLoPmrs8lR9ViJKIJbTg7GTXsi', 'admin', 'default-avatar.png', 'pending'),
(7, 'olanya', 'olanya@email.com', '', '', 'default-avatar.png', 'pending'),
(9, 'jejd', 'ee@admin.com', '', '', 'default-avatar.png', 'pending'),
(11, 'charles', 'charlse@gmail.com', '', '', 'default-avatar.png', 'pending'),
(12, 'solo', 'solo@gmail.com', '', '', 'default-avatar.png', 'pending'),
(13, 'okotia', 'okotia@gmail.com', '', '', 'default-avatar.png', 'pending'),
(14, 'desire', 'desire@gmail.com', '', '', 'default-avatar.png', 'pending'),
(15, 'collins', 'collins@gmail.com', '', '', 'default-avatar.png', 'pending'),
(17, 'sfdgfhjkh', 'fdgfhgjh@dgfgjhjk.com', '', '', 'default-avatar.png', 'pending');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
