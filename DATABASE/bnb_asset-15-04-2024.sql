-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.4.21-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             11.2.0.6213
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for bnb_asset
CREATE DATABASE IF NOT EXISTS `bnb_asset` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;
USE `bnb_asset`;

-- Dumping structure for table bnb_asset.asset
CREATE TABLE IF NOT EXISTS `asset` (
  `id_asset` int(11) NOT NULL AUTO_INCREMENT,
  `asset_name` varchar(20) NOT NULL,
  `merk` varchar(20) NOT NULL,
  `qty` int(1) NOT NULL,
  `serial_number` varchar(20) NOT NULL,
  `room_id` int(9) NOT NULL,
  `status` int(1) NOT NULL,
  `placement_status` int(1) NOT NULL,
  `created` date DEFAULT NULL,
  PRIMARY KEY (`id_asset`)
) ENGINE=InnoDB AUTO_INCREMENT=271 DEFAULT CHARSET=latin1;

-- Dumping data for table bnb_asset.asset: ~27 rows (approximately)
/*!40000 ALTER TABLE `asset` DISABLE KEYS */;
INSERT INTO `asset` (`id_asset`, `asset_name`, `merk`, `qty`, `serial_number`, `room_id`, `status`, `placement_status`, `created`) VALUES
	(206, 'AC Kecil', 'Test 1', 1, 'M101ELC21040001', 9, 0, 1, '2021-04-21'),
	(209, 'Jam Dinding', 'Test 1', 1, 'M101ELC21040002', 9, 0, 1, '2021-04-21'),
	(212, 'Meja Staff 1/2 Biro', 'Test 1', 1, 'M101FUR21040003', 9, 0, 1, '2021-04-21'),
	(216, 'Dispenser', 'Test 1', 1, 'M101ELC21040004', 9, 0, 1, '2021-04-21'),
	(217, 'Meja Sudut', 'Test 1', 1, 'M101FUR21040005', 9, 0, 1, '2021-04-21'),
	(218, 'Meja Staff 1/2 Biro', 'Test 1', 1, 'M102FUR21040006', 11, 0, 1, '2021-04-21'),
	(219, 'AC Besar', 'Test 1', 1, 'M102ELC21040007', 11, 0, 1, '2021-04-21'),
	(220, 'Jam Dinding', 'Test 1', 1, 'M102ELC21040008', 11, 0, 1, '2021-04-21'),
	(221, 'Tempat Sampah', 'Test 1', 1, 'M102SAN21040009', 11, 0, 1, '2021-04-21'),
	(222, 'Papan tulis besar', 'Test 1', 1, 'M102FUR21040010', 11, 0, 1, '2021-04-21'),
	(223, 'Thermometer', 'Test 1', 1, 'M102ELC21040011', 11, 0, 1, '2021-04-21'),
	(224, 'Meja Staff Biro', 'Test 1', 1, 'M201FUR21040012', 37, 0, 1, '2021-04-21'),
	(225, 'Meja Komputer', 'Test 1', 1, 'M201FUR21040013', 37, 0, 1, '2021-04-21'),
	(226, 'Dispenser', 'Test 1', 1, 'M201ELC21040014', 37, 0, 1, '2021-04-21'),
	(227, 'Telephone', 'Test 1', 1, 'M201ELC21040015', 37, 0, 1, '2021-04-21'),
	(228, 'Tirai kain hijau', 'Test 1', 1, 'M201FUR21040016', 37, 0, 1, '2021-04-21'),
	(231, 'Lemari kayu', 'Test 1', 1, 'M202FUR21040017', 38, 0, 1, '2021-04-21'),
	(232, 'TV 43', 'SAMSUNG', 1, 'M202ELC21040018', 38, 0, 1, '2021-04-21'),
	(233, 'White Board Roda', 'Test 1', 1, 'M202FUR21040019', 151, 0, 1, '2021-04-21'),
	(234, 'Vertical Blind', 'Test 1', 1, 'L102FUR21040020', 89, 0, 1, '2021-04-21'),
	(235, 'Kursi Manager', 'Test 1', 1, 'L102FUR21040021', 89, 0, 1, '2021-04-21'),
	(236, 'Telephone', 'Test 1', 1, 'L102ELC21040022', 89, 0, 1, '2021-04-21'),
	(237, 'Aquarium', 'Test 1', 1, 'L202FUR21040023', 101, 0, 1, '2021-04-21'),
	(238, 'Tempat Sampah', 'Test 1', 1, 'L202SAN21040024', 101, 0, 1, '2021-04-21'),
	(239, 'Telephone', 'Test 1', 1, 'L202ELC21040025', 101, 0, 1, '2021-04-21'),
	(269, 'as', 'as', 1, 'M204ASAN21040026', 151, 0, 1, '2021-04-28'),
	(270, 'as', 'ds', 1, 'L505SAN21040027', 150, 0, 1, '2021-04-28');
/*!40000 ALTER TABLE `asset` ENABLE KEYS */;

-- Dumping structure for table bnb_asset.buildings
CREATE TABLE IF NOT EXISTS `buildings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `status` int(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table bnb_asset.buildings: ~5 rows (approximately)
/*!40000 ALTER TABLE `buildings` DISABLE KEYS */;
INSERT INTO `buildings` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
	(12, 'Gedung M', 1, '2021-03-27 16:32:06', NULL),
	(13, 'Gedung L', 1, '2021-03-27 16:32:17', NULL),
	(14, 'Masjid', 1, '2021-04-12 19:15:05', '2021-04-20 13:23:03'),
	(15, 'Kantin', 1, '2021-04-15 10:48:32', NULL),
	(16, 'Halaman/Pekarangan', 1, '2021-04-20 13:22:55', '2021-04-20 13:44:14');
/*!40000 ALTER TABLE `buildings` ENABLE KEYS */;

-- Dumping structure for table bnb_asset.categories
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(10) NOT NULL,
  `name` varchar(20) NOT NULL,
  `status` int(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table bnb_asset.categories: ~6 rows (approximately)
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` (`id`, `code`, `name`, `status`, `created_at`, `updated_at`) VALUES
	(3, 'ELC', 'Elektronik', 1, '2021-03-11 19:45:56', NULL),
	(4, 'FUR', 'Furnitur', 1, '2021-03-11 19:46:14', '2021-04-15 10:45:24'),
	(5, 'SAN', 'Sanitary', 1, '2021-03-12 21:35:58', '2021-04-15 10:45:16'),
	(6, 'test', 'test', 0, '2021-04-08 21:44:11', '2021-04-08 22:08:50'),
	(7, 'asda', 'asdk', 0, '2021-04-08 21:45:48', '2021-04-08 22:08:45'),
	(8, 'test', 'test', 0, '2021-04-13 15:15:01', '2021-04-13 15:15:06');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;

-- Dumping structure for table bnb_asset.detail_process
CREATE TABLE IF NOT EXISTS `detail_process` (
  `id_detail_process` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL,
  `source` varchar(5) NOT NULL,
  `destination` varchar(5) NOT NULL,
  `createtime` date NOT NULL,
  `acctime` date NOT NULL,
  `deleted` int(1) NOT NULL,
  PRIMARY KEY (`id_detail_process`)
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=latin1;

-- Dumping data for table bnb_asset.detail_process: ~34 rows (approximately)
/*!40000 ALTER TABLE `detail_process` DISABLE KEYS */;
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
/*!40000 ALTER TABLE `detail_process` ENABLE KEYS */;

-- Dumping structure for table bnb_asset.floors
CREATE TABLE IF NOT EXISTS `floors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `building_id` int(9) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table bnb_asset.floors: ~15 rows (approximately)
/*!40000 ALTER TABLE `floors` DISABLE KEYS */;
INSERT INTO `floors` (`id`, `name`, `status`, `created_at`, `updated_at`, `building_id`) VALUES
	(8, 'Lantai 1', 1, '2021-03-27 16:32:28', NULL, 12),
	(9, 'Lantai 1', 1, '2021-03-27 16:32:45', '2021-04-13 13:02:12', 13),
	(10, 'Lantai 1', 1, '2021-04-20 13:42:35', NULL, 15),
	(11, 'Lantai 1', 1, '2021-04-20 13:44:57', NULL, 16),
	(12, 'Lantai 2', 1, '2021-04-20 13:46:43', NULL, 12),
	(13, 'Lantai 2', 1, '2021-04-20 13:54:33', '2021-04-20 13:54:39', 15),
	(14, 'Lantai 3', 1, '2021-04-20 13:57:52', NULL, 12),
	(15, 'Lantai 4', 1, '2021-04-20 14:05:08', NULL, 12),
	(16, 'Basement/Lantai', 1, '2021-04-20 14:06:26', '2021-04-20 14:23:05', 13),
	(17, 'Lantai 1', 1, '2021-04-20 14:11:41', NULL, 14),
	(18, 'Lantai 2', 1, '2021-04-20 14:22:57', NULL, 13),
	(19, 'Lantai 3', 1, '2021-04-20 14:29:35', NULL, 13),
	(20, 'Lantai 4', 1, '2021-04-20 14:36:42', NULL, 13),
	(21, 'Lantai 5', 1, '2021-04-20 14:46:58', NULL, 13),
	(22, 'Lantai 6', 1, '2021-04-20 15:41:01', NULL, 13);
/*!40000 ALTER TABLE `floors` ENABLE KEYS */;

-- Dumping structure for table bnb_asset.rooms
CREATE TABLE IF NOT EXISTS `rooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(10) NOT NULL,
  `description` varchar(50) NOT NULL,
  `status` int(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `floor_id` int(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=153 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table bnb_asset.rooms: ~143 rows (approximately)
/*!40000 ALTER TABLE `rooms` DISABLE KEYS */;
INSERT INTO `rooms` (`id`, `name`, `description`, `status`, `created_at`, `updated_at`, `floor_id`) VALUES
	(9, 'M-101', 'Ruangan Umum', 1, '2021-03-27 16:33:30', '2021-04-21 10:36:48', 8),
	(10, 'L-101', 'Elektrikal', 1, '2021-03-27 16:33:48', '2021-04-20 14:15:35', 9),
	(11, 'M-102', 'Lab Praktikum (DSL)', 1, '2021-04-20 13:27:38', NULL, 8),
	(12, 'M-103', 'Lab Multimedia', 1, '2021-04-20 13:27:58', NULL, 8),
	(13, 'M-104', 'Teori', 1, '2021-04-20 13:28:15', NULL, 8),
	(14, 'M-105', 'Administrasi Pemasaran', 1, '2021-04-20 13:30:13', NULL, 8),
	(15, 'M-106', 'Lab Praktikum (AIL)', 1, '2021-04-20 13:30:33', NULL, 8),
	(16, 'M-107', 'Lab. Komputer + Bahasa', 1, '2021-04-20 13:30:59', NULL, 8),
	(17, 'M-108', 'Lab I-Learning', 1, '2021-04-20 13:31:38', NULL, 8),
	(18, 'M-109', 'Layanan Informasi Mhs (LIM)', 1, '2021-04-20 13:32:22', '2021-04-20 13:33:24', 8),
	(19, 'M-109-A', 'Layanan Informasi Mhs (LIM) Kanan', 1, '2021-04-20 13:34:00', '2021-04-20 13:34:12', 8),
	(20, 'M-109-B', 'Layanan Informasi Mhs (LIM) Kiri', 1, '2021-04-20 13:34:37', NULL, 8),
	(21, 'M-110-A', 'Operator', 1, '2021-04-20 13:34:56', NULL, 8),
	(22, 'M-110-B', 'Server', 1, '2021-04-20 13:35:11', NULL, 8),
	(23, 'M-111', 'Keuangan (LKM)', 1, '2021-04-20 13:35:33', NULL, 8),
	(24, 'M-112', 'Main Distribution Panel (MDP)', 1, '2021-04-20 13:36:37', NULL, 8),
	(25, 'M-113', 'Gudang 1', 1, '2021-04-20 13:36:56', NULL, 8),
	(26, 'M-114', 'Gudang 2', 1, '2021-04-20 13:37:06', NULL, 8),
	(27, 'M-120', 'Kamar Mandi/WC Wanita', 1, '2021-04-20 13:37:32', NULL, 8),
	(28, 'M-121', 'Kamar Mandi/WC Pria', 1, '2021-04-20 13:38:08', NULL, 8),
	(29, 'Lobby', 'Lobby', 1, '2021-04-20 13:40:39', NULL, 8),
	(30, 'K-U', 'Koridor Utara', 1, '2021-04-20 13:40:48', '2021-04-20 13:41:25', 8),
	(31, 'K-S', 'Koridor Selatan', 1, '2021-04-20 13:40:59', '2021-04-20 13:41:42', 8),
	(32, 'K-101', 'Kantin', 1, '2021-04-20 13:42:59', NULL, 10),
	(33, 'H-B', 'Halaman Barat (Pos Jaga, ATM)', 1, '2021-04-20 13:45:30', NULL, 11),
	(34, 'H-U', 'Halaman Utara', 1, '2021-04-20 13:45:48', NULL, 11),
	(35, 'H-S', 'Halaman Selatan', 1, '2021-04-20 13:46:04', NULL, 11),
	(36, 'H-T', 'Halaman Timur', 1, '2021-04-20 13:46:16', NULL, 11),
	(37, 'M-201', 'Ruang Kepala Program Studi', 1, '2021-04-20 13:47:24', NULL, 12),
	(38, 'M-202', '', 1, '2021-04-20 13:47:39', NULL, 12),
	(39, 'M-203', '', 1, '2021-04-20 13:47:49', NULL, 12),
	(40, 'M-204', '', 1, '2021-04-20 13:48:03', NULL, 12),
	(41, 'M-204-A', '', 1, '2021-04-20 13:48:25', NULL, 12),
	(42, 'M-205', '', 1, '2021-04-20 13:48:41', NULL, 12),
	(43, 'M-206', '', 1, '2021-04-20 13:49:02', NULL, 12),
	(44, 'M-207', '', 1, '2021-04-20 13:49:17', NULL, 12),
	(45, 'M-208', '', 1, '2021-04-20 13:49:31', NULL, 12),
	(46, 'M-210', '', 1, '2021-04-20 13:49:49', NULL, 12),
	(47, 'M-211', '', 1, '2021-04-20 13:50:10', '2021-04-20 13:50:19', 12),
	(48, 'M-212', '', 1, '2021-04-20 13:50:38', NULL, 12),
	(49, 'M-213', '', 1, '2021-04-20 13:51:19', NULL, 12),
	(50, 'M-220', '', 1, '2021-04-20 13:52:26', NULL, 12),
	(51, 'M-221', '', 1, '2021-04-20 13:52:43', NULL, 12),
	(52, 'M-200', '', 1, '2021-04-20 13:53:02', NULL, 12),
	(53, 'M-230', '', 1, '2021-04-20 13:53:22', NULL, 12),
	(54, 'M-231', '', 1, '2021-04-20 13:53:37', NULL, 12),
	(55, 'M-232', '', 1, '2021-04-20 13:54:13', NULL, 12),
	(56, 'K-202', '', 1, '2021-04-20 13:56:34', NULL, 13),
	(57, 'M-301', '', 1, '2021-04-20 13:58:03', NULL, 14),
	(58, 'M-302', '', 1, '2021-04-20 13:58:16', NULL, 14),
	(59, 'M-303', '', 1, '2021-04-20 13:58:27', NULL, 14),
	(60, 'M-304', '', 1, '2021-04-20 13:58:40', NULL, 14),
	(61, 'M-305', '', 1, '2021-04-20 13:58:58', NULL, 14),
	(62, 'M-306', '', 1, '2021-04-20 14:00:28', NULL, 14),
	(63, 'M-307', '', 1, '2021-04-20 14:00:36', NULL, 14),
	(64, 'M-308', '', 1, '2021-04-20 14:00:48', NULL, 14),
	(65, 'M-309', '', 1, '2021-04-20 14:01:06', NULL, 14),
	(66, 'M-311', '', 1, '2021-04-20 14:01:49', NULL, 14),
	(67, 'M-320', '', 1, '2021-04-20 14:02:05', '2021-04-20 14:02:31', 14),
	(68, 'M-321', '', 1, '2021-04-20 14:02:06', '2021-04-20 14:02:21', 14),
	(69, 'M-322', '', 1, '2021-04-20 14:02:48', NULL, 14),
	(70, 'M-300', '', 1, '2021-04-20 14:03:15', NULL, 14),
	(71, 'M-330', '', 1, '2021-04-20 14:03:30', NULL, 14),
	(72, 'M-331', '', 1, '2021-04-20 14:03:41', NULL, 14),
	(73, 'M-332', 'Mekanikal/Elektrikal', 1, '2021-04-20 14:04:01', '2021-04-28 11:19:33', 14),
	(74, 'M-400', '', 1, '2021-04-20 14:05:22', NULL, 15),
	(75, 'L-001', '', 1, '2021-04-20 14:06:45', NULL, 16),
	(76, 'L-002', '', 1, '2021-04-20 14:07:00', NULL, 16),
	(77, 'L-003', '', 1, '2021-04-20 14:07:13', NULL, 16),
	(78, 'L-004', '', 1, '2021-04-20 14:07:30', NULL, 16),
	(79, 'L-005', '', 1, '2021-04-20 14:07:46', NULL, 16),
	(80, 'L-006', '', 1, '2021-04-20 14:08:18', NULL, 16),
	(81, 'L-007', '', 1, '2021-04-20 14:09:17', NULL, 16),
	(82, 'L-010', '', 1, '2021-04-20 14:09:38', NULL, 16),
	(83, 'L-020', '', 1, '2021-04-20 14:09:53', NULL, 16),
	(84, 'L-021', '', 1, '2021-04-20 14:10:07', NULL, 16),
	(85, 'L-000', '', 1, '2021-04-20 14:10:38', NULL, 16),
	(86, 'L-033', '', 1, '2021-04-20 14:11:16', NULL, 16),
	(87, 'L-031', 'Masjid', 1, '2021-04-20 14:11:53', '2021-04-28 11:45:27', 17),
	(88, 'L-032', 'Padepokan', 1, '2021-04-20 14:12:42', '2021-04-28 11:45:04', 17),
	(89, 'L-102', '', 1, '2021-04-20 14:15:47', NULL, 9),
	(90, 'L-103', '', 1, '2021-04-20 14:16:30', NULL, 9),
	(91, 'L-104', '', 1, '2021-04-20 14:17:15', NULL, 9),
	(92, 'L-105', '', 1, '2021-04-20 14:19:08', NULL, 9),
	(93, 'L-107', '', 1, '2021-04-20 14:19:26', NULL, 9),
	(94, 'L-108', '', 1, '2021-04-20 14:19:39', NULL, 9),
	(95, 'L-109', '', 1, '2021-04-20 14:19:52', NULL, 9),
	(96, 'L-110', '', 1, '2021-04-20 14:20:05', NULL, 9),
	(97, 'L-120', '', 1, '2021-04-20 14:21:45', NULL, 9),
	(98, 'L-121', '', 1, '2021-04-20 14:21:58', NULL, 9),
	(99, 'L-131', '', 1, '2021-04-20 14:22:18', NULL, 9),
	(100, 'L-201', '', 1, '2021-04-20 14:23:31', NULL, 18),
	(101, 'L-202', '', 1, '2021-04-20 14:23:46', NULL, 18),
	(102, 'L-203', '', 1, '2021-04-20 14:23:58', NULL, 18),
	(103, 'L-204', '', 1, '2021-04-20 14:24:08', NULL, 18),
	(104, 'L-205 ', '', 1, '2021-04-20 14:24:20', NULL, 18),
	(105, 'L-206', '', 1, '2021-04-20 14:24:31', NULL, 18),
	(106, 'L-207', '', 1, '2021-04-20 14:24:41', NULL, 18),
	(107, 'L-208', '', 1, '2021-04-20 14:24:51', NULL, 18),
	(108, 'L-209', '', 1, '2021-04-20 14:25:06', NULL, 18),
	(109, 'L-210', '', 1, '2021-04-20 14:25:17', NULL, 18),
	(110, 'L-211', '', 1, '2021-04-20 14:25:59', NULL, 18),
	(111, 'L-212', '', 1, '2021-04-20 14:26:08', NULL, 18),
	(112, 'L-213', '', 1, '2021-04-20 14:26:19', NULL, 18),
	(113, 'L-220', '', 1, '2021-04-20 14:28:05', NULL, 18),
	(114, 'L-221', '', 1, '2021-04-20 14:28:17', NULL, 18),
	(115, 'L-231', '', 1, '2021-04-20 14:28:40', NULL, 18),
	(116, 'L-301', '', 1, '2021-04-20 14:30:05', NULL, 19),
	(117, 'L-302', '', 1, '2021-04-20 14:30:16', NULL, 19),
	(118, 'L-303', '', 1, '2021-04-20 14:30:30', NULL, 19),
	(119, 'L-304', '', 1, '2021-04-20 14:30:45', NULL, 19),
	(120, 'L-305', '', 1, '2021-04-20 14:32:17', NULL, 19),
	(121, 'L-306', '', 1, '2021-04-20 14:32:34', NULL, 19),
	(122, 'L-307', '', 1, '2021-04-20 14:32:45', NULL, 19),
	(123, 'L-309', '', 1, '2021-04-20 14:32:55', '2021-04-20 14:33:12', 19),
	(124, 'L-310', '', 1, '2021-04-20 14:33:33', NULL, 19),
	(125, 'L-311', '', 1, '2021-04-20 14:33:42', NULL, 19),
	(126, 'L-312', '', 1, '2021-04-20 14:33:53', NULL, 19),
	(127, 'L-313', '', 1, '2021-04-20 14:34:06', NULL, 19),
	(128, 'L-320', '', 1, '2021-04-20 14:34:56', NULL, 19),
	(129, 'L-321', '', 1, '2021-04-20 14:35:08', NULL, 19),
	(130, 'L-300', '', 1, '2021-04-20 14:35:36', NULL, 19),
	(131, 'L-401', '', 1, '2021-04-20 14:38:11', NULL, 20),
	(132, 'L-402', '', 1, '2021-04-20 14:38:25', NULL, 20),
	(133, 'L-403', '', 1, '2021-04-20 14:38:36', NULL, 20),
	(134, 'L-404', '', 1, '2021-04-20 14:38:58', NULL, 20),
	(135, 'L-405', '', 1, '2021-04-20 14:39:12', NULL, 20),
	(136, 'L-406', '', 1, '2021-04-20 14:39:25', NULL, 20),
	(137, 'L-407', '', 1, '2021-04-20 14:39:43', NULL, 20),
	(138, 'L-409', '', 1, '2021-04-20 14:40:05', NULL, 20),
	(139, 'L-410', '', 1, '2021-04-20 14:40:25', NULL, 20),
	(140, 'L-411', '', 1, '2021-04-20 14:45:07', NULL, 20),
	(141, 'L-412', '', 1, '2021-04-20 14:45:17', NULL, 20),
	(142, 'L-413', '', 1, '2021-04-20 14:45:28', NULL, 20),
	(143, 'L-420', '', 1, '2021-04-20 14:46:11', NULL, 20),
	(144, 'L-421', '', 1, '2021-04-20 14:46:22', NULL, 20),
	(145, 'L-400', '', 1, '2021-04-20 14:46:41', NULL, 20),
	(146, 'L-501', '', 1, '2021-04-20 14:47:38', NULL, 21),
	(147, 'L-502', '', 1, '2021-04-20 14:57:11', NULL, 21),
	(148, 'L-503', '', 1, '2021-04-20 15:39:54', '2021-04-20 15:40:09', 21),
	(149, 'L-504', '', 1, '2021-04-20 15:40:23', NULL, 21),
	(150, 'L-505', 'Ruang 4', 1, '2021-04-20 15:40:41', '2021-04-28 12:06:45', 21),
	(151, 'L-600', 'ATAP', 1, '2021-04-20 15:41:15', '2021-04-28 10:46:15', 22);
/*!40000 ALTER TABLE `rooms` ENABLE KEYS */;

-- Dumping structure for table bnb_asset.transactions
CREATE TABLE IF NOT EXISTS `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `source_id` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  `sent` date NOT NULL,
  `received` date DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table bnb_asset.transactions: ~21 rows (approximately)
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
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
	(53, 200, 9, 10, 1, '2021-04-15', '2021-04-15', '2021-04-15 00:00:00', '2021-04-15 11:48:28'),
	(54, 239, 102, 101, 1, '2021-04-21', '2021-04-21', '2021-04-21 00:00:00', '2021-04-21 11:24:19'),
	(55, 239, 101, 102, 1, '2021-04-21', '2021-04-21', '2021-04-21 00:00:00', '2021-04-21 11:27:18'),
	(56, 269, 151, 38, 1, '2021-04-28', '2021-04-28', '2021-04-28 00:00:00', '2021-04-28 11:32:09'),
	(57, 233, 151, 41, 1, '2021-04-28', '2021-04-28', '2021-04-28 00:00:00', '2021-04-28 11:32:09');
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;

-- Dumping structure for table bnb_asset.user
CREATE TABLE IF NOT EXISTS `user` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(40) NOT NULL,
  `image` varchar(15) NOT NULL,
  `password` varchar(65) NOT NULL,
  `role_id` int(1) NOT NULL,
  `is_active` int(1) NOT NULL,
  `created` date NOT NULL,
  `building_id` int(10) NOT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=latin1;

-- Dumping data for table bnb_asset.user: ~7 rows (approximately)
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`id_user`, `username`, `image`, `password`, `role_id`, `is_active`, `created`, `building_id`) VALUES
	(5, 'admin', 'WA.png', '$2y$10$rG8laZTf834cETwyuxcEfOX2YIgBdwiRjK0867f3Y5lY8602VLid2', 1, 1, '2021-04-05', 0),
	(36, 'building_m', 'FB.png', '$2y$10$TUGv2zTpufw3CImAjogIN.//rpRdiFhBMQfpFlTSGx29moNzVfG/q', 2, 1, '0000-00-00', 12),
	(37, 'building_l', 'random.jpg', '$2y$10$JD3kxVk8NuyGL7Ia/MBpwOSiU5eGj/FGddKDp38G7LthtmWcRdvwq', 2, 1, '0000-00-00', 13),
	(43, 'kantin', '', '$2y$10$9gMKfNqpLldlAyAIlvOrh.exSr3/XntEsKEB1.C0JrW5/kw90H/KO', 2, 1, '0000-00-00', 3),
	(45, 'pekarangan', '', '$2y$10$k0oeLCupJyoXenwLECco0OY211lPJwdxmfPqfwguQjeQ78p6Bb3JO', 2, 1, '0000-00-00', 8),
	(46, 'ardiyan', '', '$2y$10$HVqmIee8wm/YB8mDipmNT.QotmKaNUrgFmC4K7ikPQdR.K21lxF0u', 2, 1, '0000-00-00', 14),
	(47, 'ardi', 'IG.png', '$2y$10$47GKxt/ifVCEZ9vqGIE5KuKbe.FuNLAgQbYRd9mK4IBrWBi2lZdAS', 2, 1, '0000-00-00', 15);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;

-- Dumping structure for table bnb_asset.user_role
CREATE TABLE IF NOT EXISTS `user_role` (
  `id_role` int(11) NOT NULL AUTO_INCREMENT,
  `role` varchar(20) NOT NULL,
  PRIMARY KEY (`id_role`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- Dumping data for table bnb_asset.user_role: ~2 rows (approximately)
/*!40000 ALTER TABLE `user_role` DISABLE KEYS */;
INSERT INTO `user_role` (`id_role`, `role`) VALUES
	(1, 'Administrator'),
	(2, 'User');
/*!40000 ALTER TABLE `user_role` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
