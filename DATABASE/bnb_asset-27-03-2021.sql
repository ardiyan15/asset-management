-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 27, 2021 at 02:37 AM
-- Server version: 10.4.14-MariaDB
-- PHP Version: 7.4.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
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
  `placement_status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `asset`
--

INSERT INTO `asset` (`id_asset`, `asset_name`, `merk`, `qty`, `serial_number`, `room_id`, `status`, `placement_status`) VALUES
(189, 'asjkl', 'asjkl', 1, 'M202FUR210300001', 1, 0, 1),
(190, 'jkh', 'kjasjk', 1, 'L202ELC210300002', 1, 0, 1),
(191, 'asdjkl', 'jklsad', 1, 'L201SAN210300003', 4, 0, 1),
(192, 'ASet Kantin', 'Test', 1, 'K101SAN210300004', 6, 0, 1),
(193, 'aset kantin', 'kantin', 1, 'K101SAN210300005', 4, 0, 1),
(194, 'Test', 'Test', 1, 'P102FUR210300006', 6, 0, 1),
(195, 'test', 'test', 1, 'L203ELC210300007', 3, 0, 1),
(196, 'asd', 'asd', 1, 'P102SAN210300008', 8, 0, 1),
(197, 'asd', 'asd', 1, 'K101FUR210300009', 6, 0, 1),
(198, 'asdas', 'asdfsa', 1, 'L202FUR210300010', 3, 0, 1),
(199, 'asdas', 'asdas', 1, 'P101ELC210300011', 3, 0, 0);

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
(1, 'Gedung M', 1, '2021-03-03 19:01:22', '2021-03-03 19:01:22'),
(2, 'Gedung L', 1, '2021-03-03 19:01:43', '2021-03-03 19:01:43'),
(3, 'Kantin', 1, '2021-03-09 20:48:46', NULL),
(5, 'Pos Security', 1, '2021-03-09 20:51:47', NULL),
(6, 'Parkiran', 1, '2021-03-09 20:52:10', '2021-03-09 22:04:16'),
(7, 'Masjid', 1, '2021-03-12 21:51:46', NULL),
(8, 'Pekarangan', 1, '2021-03-12 21:52:01', NULL);

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
(4, 'FUR', 'furnitur', 1, '2021-03-11 19:46:14', NULL),
(5, 'SAN', 'Sanitary', 1, '2021-03-12 21:35:58', NULL);

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
(1, 'Lantai 1', 1, '2021-03-09 22:43:18', '0000-00-00 00:00:00', 1),
(2, 'Lantai 2', 1, '2021-03-15 20:35:26', NULL, 1),
(3, 'Lantai 1', 1, '2021-03-15 22:07:19', NULL, 2),
(4, 'Lantai 1', 1, '2021-03-01 14:36:44', NULL, 3),
(5, 'Lantai 1', 1, '2021-03-21 18:50:26', NULL, 8),
(6, 'Lantai 2', 1, '2021-03-21 18:51:21', NULL, 8),
(7, 'Lantai 3', 1, '2021-03-21 18:51:31', NULL, 8);

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
(1, 'L-201', 1, '2021-03-03 19:04:13', '2021-03-03 19:04:13', 2),
(2, 'L-202', 1, '2021-03-03 19:04:37', '2021-03-03 19:04:37', 2),
(3, 'M-201', 1, '2021-03-03 19:04:51', '2021-03-03 19:04:51', 1),
(4, 'M-202', 1, '2021-03-03 19:05:07', '2021-03-03 19:05:07', 1),
(5, 'L-203', 1, '2021-03-15 22:08:02', '0000-00-00 00:00:00', 3),
(6, 'K-101', 1, '2021-03-01 14:37:44', '2021-03-01 14:37:44', 4),
(7, 'P-101', 1, '2021-03-21 19:17:00', NULL, 7),
(8, 'P-102', 1, '2021-03-21 19:17:25', NULL, 7);

-- --------------------------------------------------------

--
-- Table structure for table `store_location`
--

CREATE TABLE `store_location` (
  `id_location` int(11) NOT NULL,
  `store_code` varchar(5) NOT NULL,
  `store_name` varchar(20) NOT NULL,
  `address` varchar(100) NOT NULL,
  `handphone` int(12) NOT NULL,
  `status_location` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `store_location`
--

INSERT INTO `store_location` (`id_location`, `store_code`, `store_name`, `address`, `handphone`, `status_location`) VALUES
(1, 'S010', 'Book&Beyond UPH', 'Building A UPH Jl. MH Thamrin No.2Lippo Karawaci - Tangerang 15811', 981234567, 1),
(2, 'S011', 'Books&Beyond Kemang', 'LG #1, Jl. Pangeran Antasari Kav. 36Jakarta Selatan 12150', 812984756, 1),
(3, 'S024', 'Siloam Karawaci', 'Lobby Ground Floor, Jl. Siloam No. 6Lippo Karawaci - Tangerang 15811', 2147483647, 1),
(4, 'S045', 'Lippo Cikarang', 'Ground Floor No. 30 - 31, Jl. MH. Thamrin - Lippo Cikarang\r\nBekasi 17550', 1209384756, 1),
(5, 'IT', 'Support', 'Jl. M.H. Thamrin No.2, Gedung A UPH, Lippo Karawaci, Tangerang, Banten 15710', 12345636, 1),
(8, 'S060', 'Lippo Mall Puri', '1st floor No. 75, Lippo Mall Puri Jl Raya Puri Indah, Blok U1 Kembangan Jakarta Barat 11610', 12342, 1),
(9, 'S022', 'Istana Plaza', '1st Floor C 1 & C1 A, Jl. Pasir Kaliki No. 121-123 Bandung 40173', 22, 1),
(10, 'W001', 'Warehouse', 'Pergudangan Bizlink Blok P05 No. 33-35 Sukamulya Cikupa Tangerang 15710', 2122018278, 1),
(11, 'S015', 'Plaza Semanggi', 'Lt LG Kawasan Bisnis Granadha Jl.Jend.Sudirman Kav.50, Jakarta Pusat', 2125536493, 1),
(12, 'S017', 'Pejaten Village', 'Ground Floor, Unit G-69, Jl. Warung Jari Barat No. 39, Pasar Minggu Jakarta Selatan 12430', 217822253, 1),
(13, 'S018', 'Siloam Kebon Jeruk', 'Lobby Ground Floor, Siloam Hospital Kebon Jeruk Jakarta Barat', 2147483647, 1),
(14, 'S019', 'Lippo Pluit Village', 'Ground Floor No. 25 , Jl. Pluit Indah Raya Jakarta Utara 14450', 2147483647, 1),
(15, 'S021', 'Bandung Indah Plaza', '1St Floor (L1-K) Jl. Merdeka 56 Bandung 40116', 224223268, 1),
(16, 'S026', 'Cilandak Town Square', 'Foodmart - Ground Floor, Jl. TB Simatupang Kav.17 Jakarta Selatan 12430', 2147483647, 1),
(17, 'S031', 'Plaza Medan Fair', 'First Floor Main Entrance, Jalan Jend. Gatot Subroto No.30 Medan 20113', 614140020, 1),
(18, 'S034', 'MRC Hospital', 'Main Lobby Siloam MRCCC, Jl.Garnisun Dalam no. 2-3 Jakarta Selatan 12430', 2147483647, 1),
(19, 'S035', 'Siloam Balikpapan', 'Jalan MT. Haryono No. 09 Ring Road Balikpapan 76114', 2147483647, 1),
(20, 'S039', 'Medan Sun Plaza', 'Jl. H. Zaiunul Arifin No. 7 Main Lobby (#3.A-01) Medan 20152', 614501860, 1),
(21, 'S040', 'Siloam Palembang', 'Palembang Square Extension - Lobby Ground Floor Jl. Angkatan 45 POM IX', 2147483647, 1),
(22, 'S041', 'Siloam Makassar', 'Ground Floor Jl. Metro Tanjung Bunga N-3 Desa Penambungan, Kec. Mariso Makassar - Sulawesi Selatan', 2147483647, 1),
(23, 'S042', 'Siloam Bali', 'Jl. Sunset Road Lingkar Abianbase No 818 Kuta Denpasar - Bali 80361', 361768503, 1),
(24, 'S055', 'Lippo Mall Kuta', 'Ground Floor no. 73, Jalan Kartika Plaza - Kuta Denpasar - Bali', 2147483647, 1),
(25, 'S038', 'Siloam Manado', 'Rumah Sakit Siloam, Jl. Sam Ratulangi No.22, North Wenang, Wenang, Manado City, North Sulawesi 95114', 2147483647, 1),
(26, 'S070', 'Pakuwon Surabaya', 'Pakuwon Mall, Jl. Puncak Indah Lontar No.2, Babatan, Kec. Wiyung, Kota SBY, Jawa Timur 60123', 317390265, 1);

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
(22, 199, 2, 0, 0, '2021-03-26', NULL, '2021-03-26 00:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `user_code` varchar(10) NOT NULL,
  `fullname` varchar(50) NOT NULL,
  `email` varchar(40) NOT NULL,
  `image` varchar(15) NOT NULL,
  `password` varchar(65) NOT NULL,
  `role_id` int(1) NOT NULL,
  `is_active` int(1) NOT NULL,
  `date_created` int(11) NOT NULL,
  `building_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `user_code`, `fullname`, `email`, `image`, `password`, `role_id`, `is_active`, `date_created`, `building_id`) VALUES
(5, 'IT', 'Ardiyan Agus', 'admin', 'bnb241.jpg', '$2y$10$rG8laZTf834cETwyuxcEfOX2YIgBdwiRjK0867f3Y5lY8602VLid2', 1, 1, 1572444578, 0),
(36, 'M', 'User Building M\\', 'building_m', 'test.jpg', '$2y$10$39rNEQ2zveSWSjM.u2G5Aetxswkq3a8FlG25eJhgSP0AL7bjAkB7C', 1, 1, 0, 1),
(37, 'L', 'User Building L', 'building_l', 'test.jpg', '$2y$10$JD3kxVk8NuyGL7Ia/MBpwOSiU5eGj/FGddKDp38G7LthtmWcRdvwq', 1, 1, 0, 2),
(43, 'kantin', 'kantin', 'kantin', '', '$2y$10$9gMKfNqpLldlAyAIlvOrh.exSr3/XntEsKEB1.C0JrW5/kw90H/KO', 2, 1, 0, 3),
(45, 'p', 'pekarangan', 'pekarangan', '', '$2y$10$k0oeLCupJyoXenwLECco0OY211lPJwdxmfPqfwguQjeQ78p6Bb3JO', 2, 1, 0, 8);

-- --------------------------------------------------------

--
-- Table structure for table `user_access_menu`
--

CREATE TABLE `user_access_menu` (
  `id_user_access` int(2) NOT NULL,
  `role_id` int(2) NOT NULL,
  `menu_id` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_access_menu`
--

INSERT INTO `user_access_menu` (`id_user_access`, `role_id`, `menu_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 2, 2),
(4, 1, 3),
(5, 2, 3);

-- --------------------------------------------------------

--
-- Table structure for table `user_menu`
--

CREATE TABLE `user_menu` (
  `id_menu` int(11) NOT NULL,
  `menu` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_menu`
--

INSERT INTO `user_menu` (`id_menu`, `menu`) VALUES
(1, 'Data Master'),
(2, 'User'),
(3, 'Asset');

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

-- --------------------------------------------------------

--
-- Table structure for table `user_sub_menu`
--

CREATE TABLE `user_sub_menu` (
  `id_sub_menu` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `title` varchar(20) NOT NULL,
  `url` varchar(20) NOT NULL,
  `icon` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_sub_menu`
--

INSERT INTO `user_sub_menu` (`id_sub_menu`, `menu_id`, `title`, `url`, `icon`) VALUES
(1, 1, 'Dashboard', 'admin', 'fas fa-fw fa-tachometer-alt'),
(2, 2, 'My Profile', 'user', 'fas fa-fw fa-user-circle'),
(3, 2, 'Edit Profile', 'user/editprofile', 'fas fa-fw fa-user-edit'),
(4, 1, 'Data Assets', 'asset', 'fas fa-fw fa-drafting-compass'),
(5, 3, 'Take Out', 'TO', 'fas fa-fw fa-exchange-alt'),
(6, 3, 'Take In', 'TI', 'fas fa-fw fa-exchange-alt'),
(7, 2, 'Change Password', 'user/changepassword', 'fas fa-fw fa-key'),
(8, 3, 'History', 'History', 'fas fa-fw fa-history'),
(10, 1, 'Data Users', 'admin/list_user', 'fas fa-fw fa-tachometer-alt'),
(11, 1, 'Data Ruangan', 'buildings', 'fas fa-fw fa-coins'),
(12, 1, 'Report Download', 'report', 'fas fa-fw fa-coins'),
(14, 3, 'Transaksi Asset', 'transaction', 'fas fa-fw fa-coins');

-- --------------------------------------------------------

--
-- Table structure for table `user_token`
--

CREATE TABLE `user_token` (
  `id_token` int(11) NOT NULL,
  `email` varchar(40) NOT NULL,
  `token` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_token`
--

INSERT INTO `user_token` (`id_token`, `email`, `token`) VALUES
(1, 'ardiyan.agus@booksbeyond.co.id', 'gBKhj6XCbhz/CgPw86h3toQG6bWn1ozqIl+K2pJfUy8='),
(2, 'ardiyan.agus@booksbeyond.co.id', '/mQIurqH21gI0iAsQJCp8/zhbGtipbGe+HbJTkWKud0='),
(3, 'ardiyan.agus@booksbeyond.co.id', 'lUAexG2JdCcfDUgK+SSC2rVb/gb2xKzRc5N8HvapQmQ='),
(4, 'ardiyan.agus@booksbeyond.co.id', '95aPuqFjZFj9pH7FxYl9f+wBzOnQWDP1ySm6OVwHoFg='),
(5, 'ardiyan.agus@booksbeyond.co.id', 'UhvPS25FdXdLaTk67xWwgL6iA1HcXxLi5nKxvKypGdI='),
(6, 'ardiyan.agus@booksbeyond.co.id', '9jlTCzRMu/qngcQ1drzWIE5mCpQHmqOZIiO8Gk4/OUQ='),
(7, 'ardiyan.agus@booksbeyond.co.id', 'BCQ7zgByajVv6ARltlBu+199Z8rDXcLGqFZav6MWvPA='),
(8, 'ardiyan.agus@booksbeyond.co.id', '0nBzSob2DzBAMDyPKjRlnmPaCOj5Pz7tv0fOosJLYAo='),
(9, 'ardiyan.agus@booksbeyond.co.id', 'Nblis1UdDEu/UbiG2REQKq27Mq30YreFcTt7kma6pEg='),
(10, 'ardiyan.agus@booksbeyond.co.id', '4W0D+4rymhGzDIyoRsR6YT8jOTtjnnQKjsUV2TMAKKY='),
(11, 'ardiyan.agus@booksbeyond.co.id', '4yJmCmQXJSsYSFjnwWWusWPtY1Jr5VIixcM2h2yMaug='),
(12, 'ardiyan.agus@booksbeyond.co.id', 'u+witOCBG7ldcbdiSAM4q/usIuepoaK2lZP+axPC0O0='),
(13, 'ardiyan.agus@booksbeyond.co.id', 'SmGlPdJ5Ods2fKHd5Jn/7jBeXX0UFgSR/7YRH7XucNM='),
(14, 'ardiyan.agus@booksbeyond.co.id', 'mx1bUyGTJqNRMVqJBBuCz4sSjWUbbsGwvcOUbjCWY0M=');

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
-- Indexes for table `store_location`
--
ALTER TABLE `store_location`
  ADD PRIMARY KEY (`id_location`);

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
-- Indexes for table `user_access_menu`
--
ALTER TABLE `user_access_menu`
  ADD PRIMARY KEY (`id_user_access`);

--
-- Indexes for table `user_menu`
--
ALTER TABLE `user_menu`
  ADD PRIMARY KEY (`id_menu`);

--
-- Indexes for table `user_role`
--
ALTER TABLE `user_role`
  ADD PRIMARY KEY (`id_role`);

--
-- Indexes for table `user_sub_menu`
--
ALTER TABLE `user_sub_menu`
  ADD PRIMARY KEY (`id_sub_menu`);

--
-- Indexes for table `user_token`
--
ALTER TABLE `user_token`
  ADD PRIMARY KEY (`id_token`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `asset`
--
ALTER TABLE `asset`
  MODIFY `id_asset` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=200;

--
-- AUTO_INCREMENT for table `buildings`
--
ALTER TABLE `buildings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `detail_process`
--
ALTER TABLE `detail_process`
  MODIFY `id_detail_process` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `floors`
--
ALTER TABLE `floors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `store_location`
--
ALTER TABLE `store_location`
  MODIFY `id_location` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `user_access_menu`
--
ALTER TABLE `user_access_menu`
  MODIFY `id_user_access` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_menu`
--
ALTER TABLE `user_menu`
  MODIFY `id_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_role`
--
ALTER TABLE `user_role`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_sub_menu`
--
ALTER TABLE `user_sub_menu`
  MODIFY `id_sub_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `user_token`
--
ALTER TABLE `user_token`
  MODIFY `id_token` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
