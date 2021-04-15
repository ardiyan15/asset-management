-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 15, 2021 at 06:53 AM
-- Server version: 10.4.8-MariaDB
-- PHP Version: 7.3.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bnb_asset`
--

-- --------------------------------------------------------

--
-- Table structure for table `asset`
--

CREATE TABLE `asset` (
  `id_asset` int(11) NOT NULL,
  `asset_name` varchar(20) NOT NULL,
  `merk` varchar(20) NOT NULL,
  `qty` int(1) NOT NULL,
  `serial_number` varchar(20) NOT NULL,
  `room_id` int(9) NOT NULL,
  `status` int(1) NOT NULL,
  `placement_status` int(1) NOT NULL,
  `created` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `asset`
--

INSERT INTO `asset` (`id_asset`, `asset_name`, `merk`, `qty`, `serial_number`, `room_id`, `status`, `placement_status`, `created`) VALUES
(200, 'ahsdjk', 'ashdjk', 1, 'L101ELC210300001', 9, 0, 1, NULL),
(201, 'asajaj', 'aajaj', 1, 'L101SAN210300002', 9, 0, 1, NULL),
(202, 'asd', 'asd', 1, 'M101SAN210300003', 9, 0, 1, NULL),
(203, ' aaaaa sss', 'alalal', 1, 'L101SAN210300004', 9, 0, 1, '2021-03-28');

-- --------------------------------------------------------

--
-- Table structure for table `buildings`
--

CREATE TABLE `buildings` (
  `id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `status` int(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `buildings`
--

INSERT INTO `buildings` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(12, 'Gedung M', 1, '2021-03-27 16:32:06', NULL),
(13, 'Gedung L', 1, '2021-03-27 16:32:17', NULL),
(14, 'Test ', 1, '2021-04-12 19:15:05', '2021-04-12 20:32:59'),
(15, 'Kantin', 1, '2021-04-15 10:48:32', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `code` varchar(10) NOT NULL,
  `name` varchar(20) NOT NULL,
  `status` int(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `code`, `name`, `status`, `created_at`, `updated_at`) VALUES
(3, 'ELC', 'Elektronik', 1, '2021-03-11 19:45:56', NULL),
(4, 'FUR', 'Furnitur', 1, '2021-03-11 19:46:14', '2021-04-15 10:45:24'),
(5, 'SAN', 'Sanitary', 1, '2021-03-12 21:35:58', '2021-04-15 10:45:16'),
(6, 'test', 'test', 0, '2021-04-08 21:44:11', '2021-04-08 22:08:50'),
(7, 'asda', 'asdk', 0, '2021-04-08 21:45:48', '2021-04-08 22:08:45'),
(8, 'test', 'test', 0, '2021-04-13 15:15:01', '2021-04-13 15:15:06');

-- --------------------------------------------------------

--
-- Table structure for table `detail_process`
--

CREATE TABLE `detail_process` (
  `id_detail_process` int(11) NOT NULL,
  `asset_id` int(11) NOT NULL,
  `source` varchar(5) NOT NULL,
  `destination` varchar(5) NOT NULL,
  `createtime` date NOT NULL,
  `acctime` date NOT NULL,
  `deleted` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `detail_process`
--

INSERT INTO `detail_process` (`id_detail_process`, `asset_id`, `source`, `destination`, `createtime`, `acctime`, `deleted`) VALUES
(30, 16, 'S010', 'IT', '2020-01-19', '2020-02-17', 1),
(31, 16, 'IT', 'S010', '2020-01-19', '2020-02-17', 1),
(32, 16, 'S010', 'IT', '2020-01-22', '2020-02-17', 1),
(33, 16, 'S010', 'IT', '2020-01-22', '2020-02-17', 1),
(34, 16, 'S010', 'IT', '2020-01-22', '2020-02-17', 1),
(35, 16, 'S010', 'IT', '2020-01-22', '2020-02-17', 1),
(36, 16, 'S010', 'IT', '2020-01-22', '2020-02-17', 1),
(37, 16, 'S010', 'IT', '2020-01-22', '2020-02-17', 1),
(38, 16, 'S010', 'IT', '2020-01-22', '2020-02-17', 1),
(39, 16, 'S010', 'IT', '2020-01-22', '2020-02-17', 1),
(40, 16, 'S010', 'IT', '2020-01-22', '2020-02-17', 1),
(41, 31, 'IT', 'S010', '2020-02-09', '2020-10-07', 1),
(42, 47, 'S010', 'IT', '2020-02-09', '2020-02-17', 1),
(43, 112, 'S018', 'S010', '2020-02-10', '2020-02-17', 1),
(45, 29, 'IT', 'S010', '2020-02-10', '0000-00-00', 0),
(46, 113, 'IT', 'S010', '2020-02-10', '2020-02-11', 1),
(47, 32, 'IT', 'S010', '2020-02-10', '0000-00-00', 0),
(49, 114, 'IT', 'S010', '2020-02-10', '0000-00-00', 0),
(50, 115, 'IT', 'S022', '2020-02-11', '2020-02-11', 1),
(51, 44, 'S022', 'IT', '2020-02-11', '2020-02-11', 1),
(52, 55, 'S010', 'IT', '2020-02-11', '2020-02-17', 1),
(53, 120, 'IT', 'S010', '2020-02-12', '2020-02-17', 1),
(54, 16, 'IT', 'S011', '2020-02-12', '2020-02-17', 1),
(55, 34, 'IT', 'S010', '2020-02-17', '2020-07-07', 1),
(56, 46, 'S010', 'IT', '2020-02-17', '2020-02-17', 1),
(57, 155, 'W001', 'S010', '2020-02-19', '2020-07-07', 1),
(58, 155, 'In Sh', 'S010', '2020-06-16', '2020-07-07', 1),
(59, 159, 'IT', 'S010', '2020-07-07', '2020-07-07', 1),
(60, 48, 'S010', 'IT', '2020-07-07', '2020-07-07', 1),
(61, 34, 'S010', 'S011', '2020-07-07', '2020-07-07', 1),
(62, 31, 'S010', 'IT', '2020-10-07', '2020-10-07', 1),
(63, 158, 'IT', 'S040', '2020-11-20', '0000-00-00', 0),
(64, 155, 'S010', 'S010', '2021-02-20', '0000-00-00', 0),
(65, 156, 'IT', 'S010', '2021-02-20', '2021-02-20', 1);

-- --------------------------------------------------------

--
-- Table structure for table `floors`
--

CREATE TABLE `floors` (
  `id` int(11) NOT NULL,
  `name` varchar(15) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `building_id` int(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `floors`
--

INSERT INTO `floors` (`id`, `name`, `status`, `created_at`, `updated_at`, `building_id`) VALUES
(8, 'Lantai 1', 1, '2021-03-27 16:32:28', NULL, 12),
(9, 'Lantai 1', 1, '2021-03-27 16:32:45', '2021-04-13 13:02:12', 13);

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `name` varchar(10) NOT NULL,
  `status` int(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `floor_id` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `status`, `created_at`, `updated_at`, `floor_id`) VALUES
(9, 'M-101', 1, '2021-03-27 16:33:30', NULL, 8),
(10, 'L-101', 1, '2021-03-27 16:33:48', '2021-04-13 14:40:44', 9);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `asset_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `source_id` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  `sent` date NOT NULL,
  `received` date DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `asset_id`, `room_id`, `source_id`, `status`, `sent`, `received`, `created_at`, `updated_at`) VALUES
(24, 202, 10, 9, 1, '2021-03-27', '2021-04-14', '2021-03-27 00:00:00', '2021-04-14 15:34:02'),
(25, 200, 9, 10, 1, '2021-03-28', '2021-04-15', '2021-03-28 00:00:00', '2021-04-15 11:48:28'),
(26, 200, 10, 9, 1, '2021-03-28', '2021-04-15', '2021-03-28 00:00:00', '2021-04-15 11:48:28'),
(40, 203, 9, 10, 1, '2021-04-07', '2021-04-15', '2021-04-07 00:00:00', '2021-04-15 11:48:28'),
(41, 202, 9, 10, 1, '2021-04-07', '2021-04-14', '2021-04-07 00:00:00', '2021-04-14 15:34:02'),
(42, 203, 10, 9, 1, '2021-04-07', '2021-04-15', '2021-04-07 00:00:00', '2021-04-15 11:48:28'),
(43, 202, 10, 9, 1, '2021-04-07', '2021-04-14', '2021-04-07 00:00:00', '2021-04-14 15:34:02'),
(44, 203, 9, 10, 1, '2021-04-14', '2021-04-15', '2021-04-14 00:00:00', '2021-04-15 11:48:28'),
(45, 202, 9, 10, 1, '2021-04-14', '2021-04-14', '2021-04-14 00:00:00', '2021-04-14 15:34:02'),
(46, 201, 9, 10, 1, '2021-04-14', NULL, '2021-04-14 00:00:00', NULL),
(47, 203, 10, 9, 1, '2021-04-14', '2021-04-15', '2021-04-14 00:00:00', '2021-04-15 11:48:28'),
(48, 202, 10, 9, 1, '2021-04-14', '2021-04-14', '2021-04-14 00:00:00', '2021-04-14 15:34:02'),
(49, 203, 9, 10, 1, '2021-04-14', '2021-04-15', '2021-04-14 00:00:00', '2021-04-15 11:48:28'),
(50, 202, 9, 10, 1, '2021-04-14', '2021-04-14', '2021-04-14 00:00:00', '2021-04-14 15:34:02'),
(51, 203, 10, 9, 1, '2021-04-15', '2021-04-15', '2021-04-15 00:00:00', '2021-04-15 11:48:28'),
(52, 203, 9, 10, 1, '2021-04-15', '2021-04-15', '2021-04-15 00:00:00', '2021-04-15 11:48:28'),
(53, 200, 9, 10, 1, '2021-04-15', '2021-04-15', '2021-04-15 00:00:00', '2021-04-15 11:48:28');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `username` varchar(40) NOT NULL,
  `image` varchar(15) NOT NULL,
  `password` varchar(65) NOT NULL,
  `role_id` int(1) NOT NULL,
  `is_active` int(1) NOT NULL,
  `created` date NOT NULL,
  `building_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `username`, `image`, `password`, `role_id`, `is_active`, `created`, `building_id`) VALUES
(5, 'admin', 'WA.png', '$2y$10$rG8laZTf834cETwyuxcEfOX2YIgBdwiRjK0867f3Y5lY8602VLid2', 1, 1, '2021-04-05', 0),
(36, 'building_m', 'FB.png', '$2y$10$TUGv2zTpufw3CImAjogIN.//rpRdiFhBMQfpFlTSGx29moNzVfG/q', 2, 1, '0000-00-00', 12),
(37, 'building_l', 'test.jpg', '$2y$10$JD3kxVk8NuyGL7Ia/MBpwOSiU5eGj/FGddKDp38G7LthtmWcRdvwq', 2, 1, '0000-00-00', 13),
(43, 'kantin', '', '$2y$10$9gMKfNqpLldlAyAIlvOrh.exSr3/XntEsKEB1.C0JrW5/kw90H/KO', 2, 1, '0000-00-00', 3),
(45, 'pekarangan', '', '$2y$10$k0oeLCupJyoXenwLECco0OY211lPJwdxmfPqfwguQjeQ78p6Bb3JO', 2, 1, '0000-00-00', 8),
(46, 'ardiyan', '', '$2y$10$HVqmIee8wm/YB8mDipmNT.QotmKaNUrgFmC4K7ikPQdR.K21lxF0u', 2, 1, '0000-00-00', 14),
(47, 'ardi', 'IG.png', '$2y$10$47GKxt/ifVCEZ9vqGIE5KuKbe.FuNLAgQbYRd9mK4IBrWBi2lZdAS', 2, 1, '0000-00-00', 15);

-- --------------------------------------------------------

--
-- Table structure for table `user_role`
--

CREATE TABLE `user_role` (
  `id_role` int(11) NOT NULL,
  `role` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_role`
--

INSERT INTO `user_role` (`id_role`, `role`) VALUES
(1, 'Administrator'),
(2, 'User');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `asset`
--
ALTER TABLE `asset`
  ADD PRIMARY KEY (`id_asset`);

--
-- Indexes for table `buildings`
--
ALTER TABLE `buildings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `detail_process`
--
ALTER TABLE `detail_process`
  ADD PRIMARY KEY (`id_detail_process`);

--
-- Indexes for table `floors`
--
ALTER TABLE `floors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- Indexes for table `user_role`
--
ALTER TABLE `user_role`
  ADD PRIMARY KEY (`id_role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `asset`
--
ALTER TABLE `asset`
  MODIFY `id_asset` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=206;

--
-- AUTO_INCREMENT for table `buildings`
--
ALTER TABLE `buildings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `detail_process`
--
ALTER TABLE `detail_process`
  MODIFY `id_detail_process` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `floors`
--
ALTER TABLE `floors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `user_role`
--
ALTER TABLE `user_role`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
