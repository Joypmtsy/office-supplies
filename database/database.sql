-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 03, 2026 at 12:24 PM
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
-- Database: `office_supplies`
--
CREATE DATABASE IF NOT EXISTS `office_supplies` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `office_supplies`;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`) VALUES
(1, 'บัญชี'),
(2, 'จัดซื้อ'),
(3, 'ทรัพยากรบุคคล'),
(4, 'ขนส่ง'),
(5, 'การตลาด'),
(6, 'QC'),
(7, 'QA');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(10) UNSIGNED NOT NULL,
  `group_name` varchar(180) NOT NULL COMMENT 'Product category name',
  `name` varchar(255) NOT NULL COMMENT 'Variant / full item name',
  `image` varchar(300) NOT NULL DEFAULT 'default.png',
  `unit` varchar(60) NOT NULL DEFAULT 'ชิ้น',
  `pack_qty` int(10) UNSIGNED DEFAULT NULL COMMENT 'Pieces of unit per pack, e.g. 12 -> 1 แพ็ค / 12 ชิ้น. NULL/empty = not shown.',
  `current_stock` int(11) NOT NULL DEFAULT 0,
  `min_stock` int(11) NOT NULL DEFAULT 5,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `group_name`, `name`, `image`, `unit`, `pack_qty`, `current_stock`, `min_stock`, `created_at`) VALUES
-- 1. เครื่องเขียนและการลบคำผิด
(1, 'เครื่องเขียนและการลบคำผิด', 'ปากกาแลนเซอร์(ไลน์ผลิต) นง.', 'default.png', 'ด้าม', NULL, 0, 5, '2026-09-03 10:52:59'),
(2, 'เครื่องเขียนและการลบคำผิด', 'ปากกาแลนเซอร์(ไลน์ผลิต) แดง', 'default.png', 'ด้าม', NULL, 38, 5, '2026-09-03 10:52:59'),
(3, 'เครื่องเขียนและการลบคำผิด', 'ปากกาตรางช้าง(ออฟฟิค) นง.', 'default.png', 'ด้าม', NULL, 163, 5, '2026-09-03 10:52:59'),
(4, 'เครื่องเขียนและการลบคำผิด', 'ปากกาตรางช้าง(ออฟฟิค) แดง', 'default.png', 'ด้าม', NULL, 10, 5, '2026-09-03 10:52:59'),
(5, 'เครื่องเขียนและการลบคำผิด', 'เทปลบคำผิด', 'default.png', 'อัน', NULL, 5, 5, '2026-09-03 10:52:59'),
(6, 'เครื่องเขียนและการลบคำผิด', 'ปากกาไฮไลท์ เหลือง', 'default.png', 'ด้าม', NULL, 0, 5, '2026-09-03 10:52:59'),
(7, 'เครื่องเขียนและการลบคำผิด', 'ปากกาเคมี เหลือง', 'default.png', 'ด้าม', NULL, 1, 5, '2026-09-03 10:52:59'),
(8, 'เครื่องเขียนและการลบคำผิด', 'ปากกาเคมี แดง', 'default.png', 'ด้าม', NULL, 6, 5, '2026-09-03 10:52:59'),
(9, 'เครื่องเขียนและการลบคำผิด', 'ปากกาเคมี ดำ', 'default.png', 'ด้าม', NULL, 1, 5, '2026-09-03 10:52:59'),
(10, 'เครื่องเขียนและการลบคำผิด', 'ปากกาเคมี นง.', 'default.png', 'ด้าม', NULL, 17, 5, '2026-09-03 10:52:59'),
(11, 'เครื่องเขียนและการลบคำผิด', 'ปากกาไวท์บอร์ด ดำ', 'default.png', 'ด้าม', NULL, 1, 5, '2026-09-03 10:52:59'),
(12, 'เครื่องเขียนและการลบคำผิด', 'ปากกาไวท์บอร์ด แดง', 'default.png', 'ด้าม', NULL, 1, 5, '2026-09-03 10:52:59'),
(13, 'เครื่องเขียนและการลบคำผิด', 'ปากกาไวท์บอร์ด นง.', 'default.png', 'ด้าม', NULL, 3, 5, '2026-09-03 10:52:59'),
(14, 'เครื่องเขียนและการลบคำผิด', 'ปากกาไวท์บอร์ด เขียว', 'default.png', 'ด้าม', NULL, 1, 5, '2026-09-03 10:52:59'),
(15, 'เครื่องเขียนและการลบคำผิด', 'หมึกเติมปากกาเคมี', 'default.png', 'กล่อง', NULL, 8, 5, '2026-09-03 10:52:59'),
(19, 'เครื่องเขียนและการลบคำผิด', 'ดินสอไม้', 'default.png', 'แท่ง', NULL, 4, 5, '2026-09-03 10:52:59'),

-- 2. กาวและเทปกาว
(21, 'กาวและเทปกาว', 'กาวแท่ง', 'default.png', 'แท่ง', NULL, 3, 5, '2026-09-03 10:52:59'),
(29, 'กาวและเทปกาว', 'แท่นตัดเทป', 'default.png', 'อัน', NULL, 0, 5, '2026-09-03 10:52:59'),
(30, 'กาวและเทปกาว', 'เทป 2 หน้าแบบหนา', 'default.png', 'ม้วน', NULL, 2, 5, '2026-09-03 10:52:59'),
(31, 'กาวและเทปกาว', 'เทป 2 หน้าแบบบาง', 'default.png', 'ม้วน', NULL, 0, 5, '2026-09-03 10:52:59'),
(32, 'กาวและเทปกาว', 'เทปใส 1"เล็ก', 'default.png', 'ม้วน', NULL, 33, 5, '2026-09-03 10:52:59'),
(33, 'กาวและเทปกาว', 'เทปใส 2" ใหญ่', 'default.png', 'ม้วน', NULL, 1, 5, '2026-09-03 10:52:59'),

-- 3. อุปกรณ์เย็บและหนีบ
(24, 'อุปกรณ์เย็บและหนีบ', 'แม็ก', 'default.png', 'อัน', NULL, 1, 5, '2026-09-03 10:52:59'),
(25, 'อุปกรณ์เย็บและหนีบ', 'ที่ถอดลวด เย็บกระดาษ', 'default.png', 'อัน', NULL, 0, 5, '2026-09-03 10:52:59'),
(26, 'อุปกรณ์เย็บและหนีบ', 'ลูกแม็ก NO.10-1M', 'default.png', 'กล่อง', NULL, 87, 5, '2026-09-03 10:52:59'),
(27, 'อุปกรณ์เย็บและหนีบ', 'ลูกแม็ก NO.35-1M', 'default.png', 'กล่อง', NULL, 6, 5, '2026-09-03 10:52:59'),
(28, 'อุปกรณ์เย็บและหนีบ', 'ลวดเสียบ', 'default.png', 'กล่อง', NULL, 12, 5, '2026-09-03 10:52:59'),
(38, 'อุปกรณ์เย็บและหนีบ', 'คลิปดำ NO.107 (60 mm)', 'default.png', 'ชิ้น', 6, 18, 5, '2026-09-03 10:52:59'),
(39, 'อุปกรณ์เย็บและหนีบ', 'คลิปดำ NO.108 (22mm)', 'default.png', 'ชิ้น', 12, 39, 5, '2026-09-03 10:52:59'),
(40, 'อุปกรณ์เย็บและหนีบ', 'คลิปดำ NO.109 (17.5 mm)', 'default.png', 'ชิ้น', 12, 78, 5, '2026-09-03 10:52:59'),
(41, 'อุปกรณ์เย็บและหนีบ', 'คลิปดำ NO.110 (12 mm)', 'default.png', 'ชิ้น', 12, 84, 5, '2026-09-03 10:52:59'),
(42, 'อุปกรณ์เย็บและหนีบ', 'คลิปดำ NO.111 (10 mm)', 'default.png', 'ชิ้น', 12, 0, 5, '2026-09-03 10:52:59'),
(43, 'อุปกรณ์เย็บและหนีบ', 'คลิปดำ NO.112 (8 mm)', 'default.png', 'ชิ้น', 12, 60, 5, '2026-09-03 10:52:59'),
(44, 'อุปกรณ์เย็บและหนีบ', 'คลิปดำ NO.113', 'default.png', 'ชิ้น', NULL, 96, 5, '2026-09-03 10:52:59'),

-- 4. อุปกรณ์ตัดและวัด
(20, 'อุปกรณ์ตัดและวัด', 'ไม้บรรทัด', 'default.png', 'อัน', NULL, 0, 5, '2026-09-03 10:52:59'),
(34, 'อุปกรณ์ตัดและวัด', 'ฟุตเหล็ก', 'default.png', 'อัน', NULL, 5, 5, '2026-09-03 10:52:59'),
(35, 'อุปกรณ์ตัดและวัด', 'กรรไกร', 'item_35_1788407994.jpg', 'อัน', NULL, 0, 5, '2026-09-03 10:52:59'),
(36, 'อุปกรณ์ตัดและวัด', 'ใบมีดคัตเตอร์ ใหญ่', 'default.png', 'กล่อง', NULL, 6, 5, '2026-09-03 10:52:59'),
(37, 'อุปกรณ์ตัดและวัด', 'ใบมีดคัตเตอร์ เล็ก', 'default.png', 'กล่อง', NULL, 8, 5, '2026-09-03 10:52:59'),

-- 5. กระดาษและสมุด
(51, 'กระดาษและสมุด', 'ซองจดหมายขาว', 'default.png', 'ซอง', 50, 516, 5, '2026-09-03 10:52:59'),
(52, 'กระดาษและสมุด', 'ซองน้ำตาล A4', 'default.png', 'ซอง', NULL, 0, 5, '2026-09-03 10:52:59'),
(53, 'กระดาษและสมุด', 'ซองน้ำตาล A6', 'default.png', 'ซอง', NULL, 13, 5, '2026-09-03 10:52:59'),
(54, 'กระดาษและสมุด', 'กระดาษ A4', 'default.png', 'รีม', NULL, 22, 5, '2026-09-03 10:52:59'),
(55, 'กระดาษและสมุด', 'กระดาษไฮเจ็ท', 'default.png', 'แผ่น', NULL, 39, 5, '2026-09-03 10:52:59'),
(56, 'กระดาษและสมุด', 'กระดาษดับเบิ้ล 10', 'default.png', 'แผ่น', NULL, 0, 5, '2026-09-03 10:52:59'),
(57, 'กระดาษและสมุด', 'กระดาษ Copy คาร์บอน', 'default.png', 'แผ่น', NULL, 100, 5, '2026-09-03 10:52:59'),
(58, 'กระดาษและสมุด', 'กระดาษสีแดง', 'default.png', 'แผ่น', NULL, 0, 5, '2026-09-03 10:52:59'),
(59, 'กระดาษและสมุด', 'กระดาษสีเหลือง', 'default.png', 'แผ่น', NULL, 900, 5, '2026-09-03 10:52:59'),
(60, 'กระดาษและสมุด', 'กระดาษสีส้ม', 'default.png', 'แผ่น', NULL, 900, 5, '2026-09-03 10:52:59'),
(61, 'กระดาษและสมุด', 'โพตอิท 3*3 (มีกาว)', 'default.png', 'ชิ้น', NULL, 0, 5, '2026-09-03 10:52:59'),
(62, 'กระดาษและสมุด', 'กระดาษโน๊ต (ไม่มีกาว)', 'default.png', 'แพ็ค', NULL, 2, 5, '2026-09-03 10:52:59'),
(67, 'กระดาษและสมุด', 'สมุดปกอ่อน', 'default.png', 'เล่ม', NULL, 41, 5, '2026-09-03 10:52:59'),
(68, 'กระดาษและสมุด', 'สมุดบันทึกใหญ่', 'default.png', 'เล่ม', NULL, 0, 5, '2026-09-03 10:52:59'),

-- 6. แฟ้มและจัดเก็บเอกสาร
(69, 'แฟ้มและจัดเก็บเอกสาร', 'คลิปบอร์ด', 'default.png', 'อัน', NULL, 21, 5, '2026-09-03 10:52:59'),
(70, 'แฟ้มและจัดเก็บเอกสาร', 'แฟ้มซองเปิดข้าง A4', 'default.png', 'ซอง', NULL, 163, 5, '2026-09-03 10:52:59'),
(71, 'แฟ้มและจัดเก็บเอกสาร', 'แฟ้มโชว์เอกสาร A4', 'default.png', 'เล่ม', NULL, 2, 5, '2026-09-03 10:52:59'),
(72, 'แฟ้มและจัดเก็บเอกสาร', 'ลิ้นแฟ้ม', 'default.png', 'ชิ้น', NULL, 0, 5, '2026-09-03 10:52:59'),
(74, 'แฟ้มและจัดเก็บเอกสาร', 'ตะกร้าใส่ซองเอกสาร', 'default.png', 'กล่อง', NULL, 2, 5, '2026-09-03 10:52:59'),
(75, 'แฟ้มและจัดเก็บเอกสาร', 'ซอง 11 รู A4', 'default.png', 'ซอง', NULL, 21, 5, '2026-09-03 10:52:59'),

-- 7. อุปกรณ์เบ็ดเตล็ดและเครื่องใช้
(16, 'อุปกรณ์เบ็ดเตล็ดและเครื่องใช้', 'หมึกเติมแท่นประทับตรายาง', 'default.png', 'กล่อง', NULL, 4, 5, '2026-09-03 10:52:59'),
(17, 'อุปกรณ์เบ็ดเตล็ดและเครื่องใช้', 'แท่นหมึกประทับตรายางพิมพ์', 'default.png', 'กล่อง', NULL, 1, 5, '2026-09-03 10:52:59'),
(18, 'อุปกรณ์เบ็ดเตล็ดและเครื่องใช้', 'ตรายางปั๊ม (คำว่า อนุญาต)', 'default.png', 'อัน', NULL, 1, 5, '2026-09-03 10:52:59'),
(22, 'อุปกรณ์เบ็ดเตล็ดและเครื่องใช้', 'เครื่องคิดเลข', 'default.png', 'เครื่อง', NULL, 1, 5, '2026-09-03 10:52:59'),
(23, 'อุปกรณ์เบ็ดเตล็ดและเครื่องใช้', 'ปฏิทิน', 'default.png', 'อัน', NULL, 0, 5, '2026-09-03 10:52:59'),
(45, 'อุปกรณ์เบ็ดเตล็ดและเครื่องใช้', 'ถ่านเม็ดกระดุม 3 A CR2032', 'default.png', 'ก้อน', NULL, 9, 5, '2026-09-03 10:52:59'),
(46, 'อุปกรณ์เบ็ดเตล็ดและเครื่องใช้', 'ถ่านเม็ดกระดุม 1.5V LR1130', 'default.png', 'ก้อน', NULL, 5, 5, '2026-09-03 10:52:59'),
(47, 'อุปกรณ์เบ็ดเตล็ดและเครื่องใช้', 'ถ่านเขียว', 'default.png', 'ก้อน', NULL, 13, 5, '2026-09-03 10:52:59'),
(48, 'อุปกรณ์เบ็ดเตล็ดและเครื่องใช้', 'ถ่าน 9V', 'default.png', 'ก้อน', NULL, 4, 5, '2026-09-03 10:52:59'),
(49, 'อุปกรณ์เบ็ดเตล็ดและเครื่องใช้', 'ถ่าน AA', 'default.png', 'ก้อน', NULL, 27, 5, '2026-09-03 10:52:59'),
(50, 'อุปกรณ์เบ็ดเตล็ดและเครื่องใช้', 'ถ่าน AAA', 'default.png', 'ก้อน', NULL, 0, 5, '2026-09-03 10:52:59'),
(63, 'อุปกรณ์เบ็ดเตล็ดและเครื่องใช้', 'แผ่นเคลือบ A4', 'default.png', 'แผ่น', NULL, 290, 5, '2026-09-03 10:52:59'),
(64, 'อุปกรณ์เบ็ดเตล็ดและเครื่องใช้', 'เคลือบบัตร', 'default.png', 'อัน', NULL, 120, 5, '2026-09-03 10:52:59'),
(65, 'อุปกรณ์เบ็ดเตล็ดและเครื่องใช้', 'คลิปหนีบติดบัตร', 'default.png', 'ชิ้น', NULL, 70, 5, '2026-09-03 10:52:59'),
(66, 'อุปกรณ์เบ็ดเตล็ดและเครื่องใช้', 'ป้ายสติ๊กเกอร์ราคา', 'default.png', 'แผ่น', NULL, 4, 5, '2026-09-03 10:52:59'),
(73, 'อุปกรณ์เบ็ดเตล็ดและเครื่องใช้', 'ป้ายกุญแจ', 'default.png', 'ชิ้น', NULL, 10, 5, '2026-09-03 10:52:59');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `item_id` int(10) UNSIGNED NOT NULL,
  `type` enum('STOCK_IN','STOCK_OUT') NOT NULL,
  `quantity` int(11) NOT NULL,
  `department_id` int(10) UNSIGNED DEFAULT NULL,
  `requester` varchar(180) NOT NULL DEFAULT '',
  `note` varchar(400) NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Indexes for dumped tables
--

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_group_name` (`group_name`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_tx_item` (`item_id`),
  ADD KEY `fk_tx_dept` (`department_id`);

--
-- AUTO_INCREMENT for dumped tables
--

ALTER TABLE `departments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE `items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

ALTER TABLE `transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

ALTER TABLE `transactions`
  ADD CONSTRAINT `fk_tx_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tx_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;