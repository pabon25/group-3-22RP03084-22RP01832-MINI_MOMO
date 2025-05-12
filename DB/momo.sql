-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 12, 2025 at 11:52 AM
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
-- Database: `momo`
--

-- --------------------------------------------------------

--
-- Table structure for table `agents`
--

CREATE TABLE `agents` (
  `id` int(11) NOT NULL,
  `agent_code` varchar(20) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `pin_hash` varchar(255) DEFAULT NULL,
  `approved` tinyint(1) DEFAULT 0,
  `balance` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `agents`
--

INSERT INTO `agents` (`id`, `agent_code`, `phone_number`, `full_name`, `pin_hash`, `approved`, `balance`, `created_at`) VALUES
(1, 'Cris', '+250793341420', 'Kwihangana Lullaby', '$2y$10$dimxHDPJl4R3kI6XRB/cUuaphYLODBizNV2RJnuZ.VEi9rrEnkRoG', 1, 50.00, '2025-04-30 21:56:54'),
(2, 'THEO', '0788836616', 'max', '$2y$10$SfsDGM1SjPWFsDLarFH7WOTw0CCeQE6dJgqugKxfGR9Fi5EdvL5Eq', 1, 0.00, '2025-05-12 08:43:49');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `session_id` varchar(50) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `menu_state` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`session_id`, `phone_number`, `menu_state`, `created_at`) VALUES
('1', '0790222440', '', '2025-04-30 21:29:42'),
('1d', '+250790222440', '1', '2025-05-03 19:46:22'),
('A1', '0790222440', '', '2025-04-30 21:29:26'),
('ATUid_03849b09b6468e63fa0ff0c2747737f2', '+250790222449', '', '2025-04-30 21:36:43'),
('ATUid_05d2a833c8090ddd0df08ed4a11e9e87', '+250790222441', '3*1234', '2025-05-03 19:51:17'),
('ATUid_09e02963210e3c7e7d0da91766caa157', '+250792453617', '1', '2025-05-12 09:39:22'),
('ATUid_09f14011f52465df129707ef84a81b0b', '+250790222441', '1*+250792359800*200*1234*1', '2025-05-03 19:51:33'),
('ATUid_09f7adc64086b7a8ca67444d2793ea6e', '+250790222440', '', '2025-04-30 21:35:59'),
('ATUid_12d559dd4e83f5eca26bdc6eb631f7e6', '+250793341420', '2*1234', '2025-04-30 22:03:51'),
('ATUid_253df0e5393a73f3d8b5417ca255eefd', '+250792453617', '1*+250790222440*200*1234', '2025-05-12 09:34:37'),
('ATUid_2a3a87a66df0ab6d34a7576015903100', '+250793341420', '2*1234', '2025-04-30 22:06:16'),
('ATUid_341ab620270ee7fc25126e64c7f21b58', '+250792453617', '3*1234', '2025-05-12 09:39:36'),
('ATUid_377fd8989b5798d38763eb838e7e189d', '+250793341420', '2*1234', '2025-04-30 22:03:36'),
('ATUid_45dca39e710feaf6729535b9db8d0c5f', '+250790222441', '1*+250790222440', '2025-05-03 19:48:33'),
('ATUid_5b9a82fe95dd54f8352437ba84b4088c', '+250792453617', '3*1234', '2025-05-12 09:39:49'),
('ATUid_5d4ddadc720a5e7a03b351ab9c86cfed', '+250793341420', '', '2025-04-30 22:00:14'),
('ATUid_81bd78c5c9f3d380902f278e157f8048', '+250793341420', '', '2025-04-30 21:59:22'),
('ATUid_977b8b9e8693f26bdd5d30ddd5533f7b', '+250793341420', '4', '2025-04-30 22:30:28'),
('ATUid_9b018e84adcbd815586f1437c0e45dfa', '+250793341420', '1', '2025-04-30 22:06:05'),
('ATUid_9c71f576cb5bf86817eae31879df72b0', '+250793341420', '1*98*2*1234', '2025-04-30 22:29:40'),
('ATUid_9fcad255e330c65f1202661fc79c9cb7', '+250792453617', '1*+250790222440*200*1234*1', '2025-05-12 09:35:43'),
('ATUid_a368aa846bbcd4d347f092de38ea393b', '+250792453617', '3*1234', '2025-05-12 09:39:04'),
('ATUid_bfdc5ed38527df7c68820d53bafc35bc', '+250792453617', '1*pax*1234*1234', '2025-05-12 09:33:23'),
('ATUid_c02ab45cf1ddff35995b8ec5613207de', '+250792453617', '3*1234', '2025-05-12 09:33:52'),
('ATUid_e929de2e111637dcc56b6a10dd875578', '+250793341420', '1*TX-68129cecdb4d0*1', '2025-04-30 22:01:08'),
('ATUid_f21b702c6078bec7e6f6ccb42781b6af', '+250793341420', '3', '2025-04-30 22:03:27');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `reference` varchar(50) DEFAULT NULL,
  `user_phone` varchar(20) DEFAULT NULL,
  `agent_code` varchar(20) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `type` enum('send','withdraw') DEFAULT NULL,
  `status` enum('pending','completed','failed') DEFAULT NULL,
  `fee` decimal(10,2) DEFAULT 100.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `reference`, `user_phone`, `agent_code`, `amount`, `type`, `status`, `fee`, `created_at`) VALUES
(1, 'TX-681298dac28fd', '+250792359800', NULL, 200.00, 'send', 'completed', 100.00, '2025-04-30 21:40:42'),
(2, 'TX-68129cecdb4d0', '+250790222440', 'Cris', 200.00, 'withdraw', 'completed', 100.00, '2025-04-30 21:58:04'),
(3, 'TX-681673ebe44a7', '+250790222441', NULL, 200.00, 'send', 'completed', 100.00, '2025-05-03 19:52:11'),
(4, 'TX-6821c108958c1', '+250792453617', NULL, 200.00, 'send', 'completed', 100.00, '2025-05-12 09:36:08');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `pin_hash` varchar(255) DEFAULT NULL,
  `balance` decimal(10,2) DEFAULT 400.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `phone_number`, `full_name`, `pin_hash`, `balance`, `created_at`) VALUES
(1, '+250790222440', 'Paccy', '$2y$10$.5G3LCr/wWEv4s64Zo83.O7QLcDimaBHgLquuuKHkuUUwRnZkXFTe', 500.00, '2025-04-30 21:33:55'),
(2, '+250792359800', 'Pax', '$2y$10$kAsn/tYeigT4hzjU80KuW.8LrsBR6Gy/vOCj.Y2fYFRLaIbxyL.Tq', 400.00, '2025-04-30 21:39:22'),
(3, '+250790222441', 'MASENGESHO Pacifique', '$2y$10$h.AUUr1wWwKC37OsRogXBO5vHonvdbG.DlMJOtniG5i62vPxkfs.G', 100.00, '2025-05-03 19:47:05'),
(4, '+250792453617', 'pax', '$2y$10$HtA7agkXTK5rhDkWaFGjsOwwYUFonDQJmdTENUCnzv4z0SWq6rgAe', 100.00, '2025-05-12 09:33:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `agents`
--
ALTER TABLE `agents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `agent_code` (`agent_code`),
  ADD UNIQUE KEY `phone_number` (`phone_number`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`session_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference` (`reference`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone_number` (`phone_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `agents`
--
ALTER TABLE `agents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
