-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 19, 2025 at 12:03 PM
-- Server version: 8.0.30
-- PHP Version: 8.2.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `finance_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `initial_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(10) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'IDR',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `user_id`, `name`, `initial_balance`, `currency`, `created_at`) VALUES
(5, 2, 'Kas Harian', 1000000.00, 'IDR', '2025-06-01 07:56:01'),
(7, 2, 'Rek Mandiri', 5000000.00, 'IDR', '2025-06-01 07:56:01'),
(9, 3, 'Uang Makan', 10000.00, 'IDR', '2025-06-01 08:47:19'),
(11, 3, 'Duit Mingguan', 10000.00, 'IDR', '2025-06-01 08:52:21'),
(13, 7, 'Kas Harian', 1000000.00, 'IDR', '2025-06-02 04:26:15'),
(14, 3, 'Analisis Uang Jajan Bulanan', 100000.00, 'IDR', '2025-06-19 11:18:15');

-- --------------------------------------------------------

--
-- Table structure for table `budgets`
--

CREATE TABLE `budgets` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `category_id` int NOT NULL,
  `month` char(7) COLLATE utf8mb4_general_ci NOT NULL,
  `amount_limit` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `budgets`
--

INSERT INTO `budgets` (`id`, `user_id`, `category_id`, `month`, `amount_limit`, `created_at`) VALUES
(1, 2, 2, '2025-05', 1000000.00, '2025-06-01 07:56:48'),
(3, 2, 3, '2025-05', 500000.00, '2025-06-01 07:56:48'),
(4, 3, 9, '2025-06', 100000.00, '2025-06-19 11:19:54');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `type` enum('income','expense') COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `user_id`, `name`, `type`, `created_at`) VALUES
(1, 2, 'Gaji', 'income', '2025-06-01 07:56:16'),
(3, 2, 'Makanan', 'expense', '2025-06-01 07:56:16'),
(5, 2, 'Transport', 'expense', '2025-06-01 07:56:16'),
(7, 2, 'Freelance', 'income', '2025-06-01 07:56:16'),
(9, 3, 'Uang Makan', 'expense', '2025-06-01 08:46:41'),
(11, 3, 'Duit Mingguan', 'income', '2025-06-01 08:51:55'),
(13, 7, 'Uang Makan', 'expense', '2025-06-02 04:26:32'),
(15, 7, 'Transportasi', 'expense', '2025-06-02 04:26:48'),
(17, 9, 'Duit Mingguan', 'income', '2025-06-16 10:35:15'),
(19, 3, 'Uang Makan', 'income', '2025-06-16 10:35:43'),
(20, 3, 'Uang Jajan Bulanan', 'income', '2025-06-19 11:17:40');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `account_id` int NOT NULL,
  `category_id` int NOT NULL,
  `type` enum('income','expense') COLLATE utf8mb4_general_ci NOT NULL,
  `transaction_date` date NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `account_id`, `category_id`, `type`, `transaction_date`, `amount`, `description`, `created_at`) VALUES
(1, 2, 2, 1, 'income', '2025-05-25', 5000000.00, 'Gaji Bulanan', '2025-06-01 07:56:35'),
(3, 2, 1, 2, 'expense', '2025-05-24', 50000.00, 'Makan Siang', '2025-06-01 07:56:35'),
(5, 2, 2, 3, 'expense', '2025-05-23', 600000.00, 'Listrik', '2025-06-01 07:56:35'),
(7, 2, 1, 4, 'income', '2025-05-22', 1200000.00, 'Project Freelance', '2025-06-01 07:56:35'),
(9, 3, 9, 9, 'expense', '2025-06-01', 5000.00, '', '2025-06-01 08:47:47'),
(11, 3, 11, 11, 'income', '2025-06-01', 10000.00, '', '2025-06-01 08:52:34'),
(13, 7, 13, 15, 'expense', '2025-06-02', 250000.00, '', '2025-06-02 04:27:42'),
(14, 3, 14, 20, 'income', '2025-06-19', 10000.00, '', '2025-06-19 11:19:25'),
(15, 3, 9, 9, 'expense', '2025-06-19', 120000.00, '', '2025-06-19 11:20:38');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','user') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `full_name`, `email`, `role`, `created_at`) VALUES
(1, 'admin01', '$2y$10$GDKqL1P2lsbbBYYN6iTdhOykSsB1JkyqjOgB9OAVT8peW6e.Byyse', 'Administrator', 'admin@example.com', 'admin', '2025-06-01 07:55:27'),
(3, 'user01', '$2y$10$DGF1PpeKMNj.G4rh3fsmmufp9OIXuaKXZhO/erQOIwEfiSiJrM9Ru', 'User Biasa', 'user@example.com', 'user', '2025-06-01 07:55:27'),
(7, 'Adheet', '$2y$10$1GmKuG6gawwjD4z3zAvkh.L/TYS4i29cgqBTKkM7Oa01dLDthrkFy', 'Adit', 'adheet@gmail.com', 'user', '2025-06-02 04:24:19'),
(9, 'riski2', '$2y$10$lNNEB5oe4GSid2CUFMRRnOsS0DFEVvyR7ku9ttfMd6zoU8VoL0nVO', 'riskip', 'Riski2@gmail.com', 'admin', '2025-06-16 10:33:25'),
(10, 'Riski3', '$2y$10$uO1.u3edx6uZo4K6jCrrn.QvjH/fPdi1UIfVndr7yF6QkhpfWRuu6', 'Riski3', 'Riski3@gmail.com', 'user', '2025-06-19 11:15:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `budgets`
--
ALTER TABLE `budgets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `account_id` (`account_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `budgets`
--
ALTER TABLE `budgets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `accounts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `budgets`
--
ALTER TABLE `budgets`
  ADD CONSTRAINT `budgets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `budgets_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_3` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
