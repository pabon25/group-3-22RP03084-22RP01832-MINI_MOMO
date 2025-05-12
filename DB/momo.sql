-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 12, 2025 at 02:37 PM
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
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password_hash`, `created_at`, `last_login`) VALUES
(2, 'admin', '$2y$10$.G6I41YabWJfw4ZvFrRIKedM/hyFsbWKzM8saD51RTbhklRhUg1CS', '2025-05-12 11:43:36', '2025-05-12 12:18:43');

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
  `balance` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `agents`
--

INSERT INTO `agents` (`id`, `agent_code`, `phone_number`, `full_name`, `pin_hash`, `approved`, `balance`, `created_at`) VALUES
(10, 'PAX', '+250792359800', 'MASENGESHO Pacifique', '$2y$10$LdR2PoV14gbrRYm4gbSLdOd4ZYKqxKUqbBFet9x6RXFgEkMmiU7dq', 1, 50050.00, '2025-05-12 11:23:16'),
(11, 'THEO', '+250788836616', 'ISHIMWE Theogene', '$2y$10$uu/GVbmxzr6yzn2gQHQh4OLxjcn73oAJwYnJYt5lOg.24S.jBDKwC', 1, 70000.00, '2025-05-12 11:24:03');

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
('ATUid_0123c622c68f72a4ac52666e46c51609', '+250788836616', '', '2025-05-12 12:28:51'),
('ATUid_2e4fd0e24c12ce6c9ec6bf8fd2d48eb1', '+250788790877', '2*50000*PAX*1234', '2025-05-12 12:26:24'),
('ATUid_7924140dccc16c8c43702710ec30b549', '+250788790877', '2', '2025-05-12 12:25:51'),
('ATUid_ac8697a9507d698666c5caf8f918641c', '+250788836616', '1*TX-6821e90e49bdb*1', '2025-05-12 12:27:55'),
('ATUid_b59e9c7f7d7f61be5a2918779168fd2e', '+250783672819', '1*Elias*1234*1234', '2025-05-12 12:21:04'),
('ATUid_b70736198f509f4892158f38fad283b4', '+250788790877', '1*+250783672819*200*1234*1', '2025-05-12 12:24:03'),
('ATUid_bb0f9d3347fd84793da8b492c1ffbb9d', '+250788790877', '1*Mathias*1234*1234', '2025-05-12 12:23:29'),
('ATUid_c02ab45cf1ddff35995b8ec5613207de', '+250792453617', '3*1234', '2025-05-12 09:33:52'),
('ATUid_d74d79a7f8161058f79ee9f9cf374dfa', '+250792359800', '', '2025-05-12 10:19:08'),
('ATUid_e18767c58c65158ce254f8726b86f51e', '+250783672819', '1*Elias Ndiho*1234', '2025-05-12 12:20:24'),
('ATUid_e929de2e111637dcc56b6a10dd875578', '+250793341420', '1*TX-68129cecdb4d0*1', '2025-04-30 22:01:08'),
('ATUid_f1fc8e18dd5facd484e66c79ab346876', '+250792359800', '1*TX-6821e90e49bdb*1', '2025-05-12 12:29:34'),
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
(5, 'TX-6821e88caa6ef', '+250788790877', NULL, 200.00, 'send', 'completed', 100.00, '2025-05-12 12:24:44'),
(6, 'TX-6821e90e49bdb', '+250788790877', 'PAX', 50000.00, 'withdraw', 'completed', 100.00, '2025-05-12 12:26:54');

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
(5, '+250783672819', 'Elias', '$2y$10$8vy3L2e.JJQFURqxlUOL3eSX1jd4gjKR0S0SpveJ.FwRf/i3y/Ob.', 600.00, '2025-05-12 12:21:32'),
(6, '+250788790877', 'Mathias', '$2y$10$SPRexWASBWYqXYbL5.9B9.GKPX7WpMgL9hsiGm9KbIFm0FboLbQIy', 49900.00, '2025-05-12 12:23:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

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
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `agents`
--
ALTER TABLE `agents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
