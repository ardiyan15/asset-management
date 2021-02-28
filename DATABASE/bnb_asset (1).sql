-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 28, 2021 at 01:49 PM
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
  `origin_location` varchar(11) NOT NULL,
  `asset_location` varchar(11) NOT NULL,
  `status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `asset`
--

INSERT INTO `asset` (`id_asset`, `asset_name`, `merk`, `qty`, `serial_number`, `origin_location`, `asset_location`, `status`) VALUES
(16, 'Monitor', 'Acer', 1, '3321102SD', 'IT', 'S011', 0),
(31, 'UPS', 'Eaton', 1, '009181', 'IT', 'IT', 0),
(34, 'Monitor', 'Acer', 1, 'ddda12', 'IT', 'S011', 0),
(35, 'Monitor', 'Lenovo', 1, '8ML1253E42N4215', 'S022', 'S022', 0),
(36, 'CPU', 'Lenovo', 1, 'R3003WSL', 'S022', 'S022', 0),
(37, 'Keyboard', 'Lenovo', 1, '40142265', 'S022', 'S022', 0),
(38, 'Mouse', 'Acer', 1, '84105946', 'S022', 'S022', 0),
(39, 'UPS', 'Eaton', 1, '53GC00973', 'S022', 'S022', 0),
(40, 'Printer', 'Epson', 1, 'NJUF283381', 'S022', 'S022', 0),
(41, 'Poledisplay', 'VFD', 1, 'AIDLUF233129', 'S022', 'S022', 0),
(42, 'Fingerprint', 'Digital Pesona', 1, 'F500E007189', 'S022', 'S022', 0),
(43, 'Cashdrawer', 'Securebox', 1, '109384703E', 'S022', 'S022', 0),
(44, 'Scanner', 'Datalogic', 1, 'G13F37345', 'S022', 'IT', 0),
(45, 'Monitor', 'Lenovo', 1, 'VNC1ZR1', 'S022', 'S022', 0),
(46, 'Monitor', 'Lenovo', 1, '0928KBS110', 'S010', 'IT', 0),
(47, 'Monitor', 'Lenovo', 1, '22981J28A', 'S010', 'IT', 0),
(48, 'CPU', 'Lenovo', 1, '00918246BLJA', 'S010', 'IT', 0),
(49, 'Keyboard', 'Lenovo', 1, '230609891', 'S010', 'S010', 0),
(50, 'UPS', 'Eaton', 1, '10928AQSH', 'S010', 'S010', 0),
(51, 'Printer', 'LX-300 + II', 1, 'ESD102947', 'S010', 'S010', 0),
(52, 'Printer', 'Epson', 1, 'LKS17S8271', 'S010', 'S010', 0),
(53, 'Poledisplay', 'Enibit', 1, 'VNDA1029S', 'S010', 'S010', 0),
(54, 'Scanner', 'Datalogic', 1, 'S09LAK2S', 'S010', 'S010', 0),
(55, 'Fingerprint', 'Digital Pesona', 1, 'A09182KDN', 'S010', 'IT', 0),
(56, 'Cashdrawer', 'Securebox', 1, '10KL28S71', 'S010', 'S010', 0),
(57, 'Monitor', 'Lenovo', 1, '8ML1253E42A9AI15', 'S011', 'S011', 0),
(58, 'CPU', 'Lenovo', 1, 'B0F3003WSL', 'S011', 'S011', 0),
(59, 'Mouse', 'Acer', 1, '0928SJ21', 'S011', 'S011', 0),
(60, 'Keyboard', 'Lenovo', 1, '02942265', 'S011', 'S011', 0),
(61, 'UPS', 'Eaton', 1, '10A28AASH', 'S011', 'S011', 0),
(62, 'Printer', 'LX-300', 1, 'AOK1S92H', 'S011', 'S011', 0),
(63, 'Printer', 'Epson', 1, 'JLMSN12S9', 'S011', 'S011', 0),
(64, 'Poledisplay', 'Enibit', 1, '11SDLZ01', 'S011', 'S011', 0),
(65, 'Fingerprint', 'Digital Pesona', 1, 'D800FAJS19', 'S011', 'S011', 0),
(66, 'USB Port', 'Digigear', 1, 'USB001', 'S011', 'S011', 0),
(67, 'Scanner', 'Datalogic', 1, 'ALS19238', 'S011', 'S011', 0),
(68, 'Monitor', 'Acer', 1, 'AS091LAK', 'S011', 'S011', 0),
(69, 'Monitor', 'Samsung', 1, 'F019SJLSAH1', 'S011', 'S011', 0),
(70, 'Monitor', 'Lenovo', 1, '0019S9SBZX', 'S011', 'S011', 0),
(71, 'Mouse', 'Lenovo', 1, '312S5710', 'S015', 'S015', 0),
(72, 'Keyboard', 'Lenovo', 1, '40609839', 'S015', 'S015', 0),
(73, 'UPS', 'Eaton', 1, '10A28BXNQSH', 'S015', 'S015', 0),
(74, 'Printer', 'LX-300', 1, 'Q7FY277647', 'S015', 'S015', 0),
(75, 'Printer', 'Epson', 1, 'NJUF283357', 'S015', 'S015', 0),
(76, 'Poledisplay', 'WARANTY', 1, 'ZMLOS10S', 'S015', 'S015', 0),
(77, 'Fingerprint', 'Digital Pesona', 1, 'AZS1KFAJS19', 'S015', 'S015', 0),
(78, 'USB Port', 'Digigear', 1, 'USB002', 'S015', 'S015', 0),
(79, 'Scanner', 'Datalogic', 1, 'QW2100', 'S015', 'S015', 0),
(80, 'Monitor', 'Lenovo', 1, '019SL019', 'S015', 'S015', 0),
(81, 'Monitor', 'Lenovo', 1, '0191920S', 'S015', 'S015', 0),
(82, 'Monitor', 'Lenovo', 1, '0019S9SAK', 'S015', 'S015', 0),
(83, 'Cashdrawer', 'SecureBox', 1, '102918ZXA', 'S015', 'S015', 0),
(84, 'Cashdrawer', 'Securebox', 1, '101029171', 'S011', 'S011', 0),
(85, 'Mouse', 'Lenovo', 1, '01S9D', 'S017', 'S017', 0),
(86, 'Keyboard', 'Lenovo', 1, '40609839', 'S017', 'S017', 0),
(87, 'UPS', 'Eaton', 1, '091ANMZA12', 'S017', 'S017', 0),
(88, 'Printer', 'LX-300', 1, 'Q7FY091L8UXV', 'S017', 'S017', 0),
(89, 'Printer', 'Epson', 1, '091A981JNV', 'S017', 'S017', 0),
(90, 'Poledisplay', 'VFD', 1, '18091781', 'S017', 'S017', 0),
(91, 'Fingerprint', 'Digital Pesona', 1, '109SLH17', 'S017', 'S017', 0),
(92, 'USB Port', 'Digigear', 1, 'USB003', 'S017', 'S017', 0),
(93, 'Scanner', 'Datalogic', 1, 'QW3200', 'S015', 'S017', 0),
(94, 'Monitor', 'Lenovo', 1, '0918AKM', 'S017', 'S017', 0),
(95, 'Monitor', 'Lenovo', 1, '018ISKA', 'S017', 'S017', 0),
(96, 'Monitor', 'Lenovo', 1, '0009AKA1X', 'S017', 'S017', 0),
(97, 'Cashdrawer', 'SecureBox', 1, '10212DKLXA', 'S017', 'S017', 0),
(98, 'CPU', 'Lenovo', 1, 'ES901MXB', 'S015', 'S015', 0),
(99, 'CPU', 'Lenovo', 1, 'QXCNAL8190', 'S017', 'S017', 0),
(100, 'CPU', 'Lenovo', 1, 'CCL18710S', 'S018', 'S018', 0),
(101, 'Mouse', 'Logitech', 1, '10920918', 'S018', 'S018', 0),
(102, 'Keyboard', 'Lenovo', 1, '40192156', 'S018', 'S018', 0),
(103, 'UPS', 'Eaton', 1, '091ANMBLKO', 'S018', 'S018', 0),
(104, 'Printer', 'LX-300', 1, 'Q7FY0KL019NM', 'S018', 'S018', 0),
(105, 'Printer', 'Epson', 1, 'Q7UIAN091', 'S018', 'S018', 0),
(106, 'Poledisplay', 'VFD', 1, '109KLA1XB', 'S018', 'S018', 0),
(107, 'Fingerprint', 'Digital Pesona', 1, '10910928S', 'S018', 'S018', 0),
(108, 'USB Port', 'Digigear', 1, 'USB004', 'S018', 'S018', 0),
(109, 'Scanner', 'Datalogic', 1, 'QW3109S', 'S018', 'S018', 0),
(110, 'Monitor', 'Lenovo', 1, '109S819281', 'S018', 'S018', 0),
(111, 'Monitor', 'Lenovo', 1, '101S098514', 'S018', 'S018', 0),
(112, 'Monitor', 'Lenovo', 1, '10091876529', 'S018', 'S010', 0),
(113, 'Cashdrawer', 'SecureBox', 1, '41OPSLQ1', 'S018', 'S010', 0),
(115, 'Scanner', 'Datalogic', 1, 'G12N52121', 'IT', 'S022', 0),
(116, 'Cashdrawer', 'Securebox', 1, '420150200732', 'W001', 'IT', 0),
(117, 'Router', 'Linksys', 1, 'MDG30H303503', 'IT', 'IT', 0),
(118, 'CPU', 'Lenovo', 1, '10OPQMNJ192I', 'IT', 'W001', 0),
(119, 'CPU', 'Lenovo', 1, '198SKL10YT', 'S063', 'IT', 0),
(120, 'Router', 'Linksys', 1, 'CL7B1K522693', 'IT', 'S010', 0),
(124, 'Router', 'Linksys', 1, '10A30C61514987', 'S010', 'IT', 0),
(125, 'Monitor', 'LG', 1, '607NTABCN944', 'W001', 'IT', 0),
(126, 'Monitor', 'LG', 1, '607NTLECN960', 'S010', 'IT', 0),
(127, 'Monitor', 'Lenovo', 1, 'VNC20YP', 'S010', 'IT', 0),
(128, 'Router', 'Linksys', 1, '10A30C65528369', 'IT', 'IT', 0),
(129, 'Router', 'TP-Link', 1, '215B327002903', 'IT', 'IT', 0),
(130, 'Access Point', 'Linksys', 1, 'MDG30H303510', 'IT', 'IT', 0),
(131, 'Router', 'TP Link', 1, '12996807950', 'IT', 'IT', 0),
(132, 'CPU', 'Lenovo', 1, 'R301YZNA', 'S010', 'IT', 0),
(133, 'Monitor', 'Lenovo', 1, 'U38ABG07', 'S010', 'IT', 0),
(134, 'Fingerprint', 'Digital Pesona', 1, 'G500E00172', 'S063', 'W001', 0),
(135, 'UPS', 'Eaton', 1, '109BNMA8917LQ', 'S063', 'W001', 0),
(136, 'Cashdrawer', 'Securebox', 1, '10938410LB', 'IT', 'W001', 0),
(137, 'Printer', 'Epson', 1, 'NJF109ALK10', 'S038', 'W001', 0),
(138, 'UPS', 'Eaton', 1, 'SK109826AMNAJ', 'S038', 'W001', 0),
(139, 'Poledisplay', 'VFD', 1, '1098261019261', 'S038', 'W001', 0),
(140, 'Cashdrawer', 'Securebox', 1, '981150200732', 'S038', 'W001', 0),
(141, 'Monitor', 'Cocca', 1, 'VNC1109S', 'S038', 'W001', 0),
(142, 'USB Port', 'Digigear', 1, 'USB005', 'S038', 'W001', 0),
(143, 'Monitor', 'Lenovo', 1, '0928TNL11', 'S038', 'W001', 0),
(144, 'Monitor', 'Acer', 1, '00128KQS110', 'S038', 'W001', 0),
(145, 'CPU', 'Lenovo', 1, 'B0F300301OA', 'S038', 'W001', 0),
(146, 'Keyboard', 'Lenovo', 1, '10840142265', 'S038', 'W001', 0),
(147, 'Printer', 'LX-300', 1, 'Q7FY091L918J', 'S038', 'W001', 0),
(148, 'UPS', 'Eaton', 1, 'SK109MLZXMNAJ', 'S070', 'W001', 0),
(149, 'Monitor', 'Lenovo', 1, '0928AJ19NL11', 'S070', 'W001', 0),
(150, 'Monitor', 'Thinkvision', 1, '0928QLA9NL11', 'S070', 'W001', 0),
(151, 'Fingerprint', 'Digital Pesona', 1, 'F500L197189', 'S070', 'W001', 0),
(152, 'Printer', 'LX-300', 1, 'ESD1LZMQ2947', 'S070', 'W001', 0),
(153, 'Cashdrawer', 'Securebox', 1, '109AAL703E', 'S070', 'W001', 0),
(154, 'Printer', 'Epson', 1, 'NJUL172381', 'S070', 'W001', 0),
(155, 'CPU', 'Lenovo', 1, 'R301AZQWSL', 'S070', 'In Shipping', 0),
(156, 'Monitor', 'Lenovo', 1, '111aaaopamzzaafqqwer', 'W001', 'S010', 0),
(157, 'Dedicated Server', 'IBM', 1, '10.100.1.66', 'IT', 'IT', 0),
(158, 'Dedicated Server', 'IBM 999', 1, '10.100.1.22', 'IT', 'In Shipping', 0);

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
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `user_code` varchar(5) NOT NULL,
  `fullname` varchar(50) NOT NULL,
  `email` varchar(40) NOT NULL,
  `image` varchar(15) NOT NULL,
  `password` varchar(65) NOT NULL,
  `role_id` int(1) NOT NULL,
  `is_active` int(1) NOT NULL,
  `date_created` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `user_code`, `fullname`, `email`, `image`, `password`, `role_id`, `is_active`, `date_created`) VALUES
(1, 'S010', 'UPH Books & Beyond', 'UPH@booksbeyond.co.id', 'default.jpg', '$2y$10$Itqfhvt5RY18N7JaFk215.Hzwz6NbQAdW/xYtkgFlaJ7rOKZEaGE2', 2, 1, 1572445121),
(5, 'IT', 'Ardiyan Agus', 'ardiyan.agus@booksbeyond.co.id', 'bnb241.jpg', '$2y$10$MoeyOgbExoGGkXbl5yk0He9lD6T7coSkmNSnbAjnwEE75S7Xj/Cd2', 1, 1, 1572444578),
(29, 'S045', 'LCG Lippo Cikarang', 'LCG@booksbeyond.co.id', 'default.jpg', '$2y$10$yV16ppY9sNsARLyMHXuvQ.Yfkw48P0KdnMVnDkIbcp9f4juUBFhA2', 2, 1, 1573156346),
(30, 'S024', 'Siloam Karawaci', 'SL2@booksbeyond.co.id', 'default.jpg', '$2y$10$xBECJ.szFNpw3p6cpFr4.OwxzzlT15.117sBBiqnyYlYwnp183Y/y', 2, 1, 1574497781),
(32, 'S011', 'Kemang Books&Beyond', 'KEM@booksbeyond.co.id', 'bnb28.jpg', '$2y$10$W5N2IojwwQ2U0mEB3AzMqORylyS8lERrbFsw6WJ7TkXVwiwHQxEeq', 2, 1, 1578585770),
(33, 'S060', 'Books&amp;Beyond Lippo Mall Puri', 'lmp@booksbeyond.co.id', 'default.jpg', '$2y$10$u002n.rFEjdohJdHXJuRQOgSmm95fCddxgbJ/a4uJG0..DpTAryGW', 2, 1, 1579721538),
(34, 'S022', 'Istana Plaza Bandung', 'isp@booksbeyond.co.id', 'default.jpg', '$2y$10$AiI8VHE7.v0HjfdUEgzR8uPqvHtXS2b.bqmc9F1xVzNJwQtmvyozu', 2, 1, 1581387930),
(35, 'W001', 'Warehouse', 'war@booksbeyond.co.id', 'default.jpg', '$2y$10$4YYlfF/yoh3xkR6U.xAXful4R7bPhvxa8RRBSXEBcT9RpSQM2/iP2', 2, 1, 1581524690);

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
(11, 1, 'Data Ruangan', 'admin/store_location', 'fas fa-fw fa-coins'),
(12, 1, 'Report Download', 'report', 'fas fa-fw fa-coins');

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
-- Indexes for table `detail_process`
--
ALTER TABLE `detail_process`
  ADD PRIMARY KEY (`id_detail_process`);

--
-- Indexes for table `store_location`
--
ALTER TABLE `store_location`
  ADD PRIMARY KEY (`id_location`);

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
  MODIFY `id_asset` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=160;

--
-- AUTO_INCREMENT for table `detail_process`
--
ALTER TABLE `detail_process`
  MODIFY `id_detail_process` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `store_location`
--
ALTER TABLE `store_location`
  MODIFY `id_location` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

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
  MODIFY `id_sub_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `user_token`
--
ALTER TABLE `user_token`
  MODIFY `id_token` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
