-- -------------------------------------------------------------
-- TablePlus 6.8.2(656)
--
-- https://tableplus.com/
--
-- Database: kitchen_pos
-- Generation Time: 2026-03-19 01:30:59.2720
-- -------------------------------------------------------------


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(191) DEFAULT NULL,
  `barcode` varchar(191) NOT NULL,
  `price` decimal(14,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `category` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_barcode_unique` (`barcode`)
) ENGINE=InnoDB AUTO_INCREMENT=123 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `products` (`id`, `name`, `description`, `image`, `barcode`, `price`, `quantity`, `status`, `category`, `created_at`, `updated_at`) VALUES
(1, 'Zinger Burger', 'Normal Zinger Burger', 'products/UolvgmIriFiBL2aZEOl8sWVZVXgdE9e35PwXBOSX.jpg', '123', 350.00, 91, 0, '', '2025-06-07 07:04:03', '2026-03-18 19:21:26'),
(2, 'Tikka Pizza Medium', 'Tikka', 'products/d7gRleQe8cd7PUZGaepVn04fH6qh9sT2D0sbB8KM.jpg', '1234', 1000.00, 92, 0, '', '2025-06-07 07:26:29', '2026-03-18 19:20:44'),
(3, 'Tikka Pizza Small', 'small', 'products/Kh3vfrhRQEsBLxVqvOpiZz39rDnGBywGbEyaMB13.jpg', '12345', 560.00, 94, 0, '', '2025-06-07 07:27:00', '2026-03-18 19:21:10'),
(4, 'Tikka Pizza Large', 'large', 'products/iAqnmvT1SYbLqi1ccdBw2xSWOWBUpbKsbiWnuLXa.jpg', '1234567', 1400.00, 0, 0, '', '2025-06-07 07:27:52', '2026-03-18 19:21:10'),
(5, 'Fish Burger', 'asdsa', 'products/n46T7vyYVlUoDlCWx9lRhl9BoMpBSHShTz3VWrQN.jpg', '1', 600.00, 64, 0, '', '2025-06-09 19:05:17', '2026-03-18 19:23:22'),
(6, 'Fish Burger', 'Burger', NULL, 'B001', 650.00, 100, 1, 'Burger', '2026-03-19 00:31:06', '2026-03-19 00:31:06'),
(7, 'Zinger Burger', 'Burger', NULL, 'B002', 300.00, 100, 1, 'Burger', '2026-03-19 00:31:06', '2026-03-19 00:31:06'),
(8, 'Zinger Cheese Burger', 'Burger', NULL, 'B003', 350.00, 100, 1, 'Burger', '2026-03-19 00:31:06', '2026-03-19 00:31:06'),
(9, 'Zinger Tower Burger', 'Burger', NULL, 'B004', 450.00, 100, 1, 'Burger', '2026-03-19 00:31:06', '2026-03-19 00:31:06'),
(10, 'Crispy Patty Burger', 'Burger', NULL, 'B005', 250.00, 100, 1, 'Burger', '2026-03-19 00:31:06', '2026-03-19 00:31:06'),
(11, 'Al Basha Special Mighty Burger', 'Burger', NULL, 'B006', 600.00, 100, 1, 'Burger', '2026-03-19 00:31:06', '2026-03-19 00:31:06'),
(12, 'Chicken Egg Burger', 'Burger', NULL, 'B007', 200.00, 100, 1, 'Burger', '2026-03-19 00:31:06', '2026-03-19 00:31:06'),
(13, 'Zinger Pizza Burger', 'Burger', NULL, 'B008', 450.00, 100, 1, 'Burger', '2026-03-19 00:31:06', '2026-03-19 00:31:06'),
(14, 'Petty Pizza Burger', 'Burger', NULL, 'B009', 400.00, 100, 1, 'Burger', '2026-03-19 00:31:06', '2026-03-19 00:31:06'),
(15, 'Malai Boti Burger', 'Burger', NULL, 'B010', 400.00, 100, 1, 'Burger', '2026-03-19 00:31:06', '2026-03-19 00:31:06'),
(16, 'Shami Egg Burger', 'Burger', NULL, 'B011', 150.00, 100, 1, 'Burger', '2026-03-19 00:31:06', '2026-03-19 00:31:06'),
(17, 'Shami Double Egg Burger', 'Burger', NULL, 'B012', 200.00, 100, 1, 'Burger', '2026-03-19 00:31:06', '2026-03-19 00:31:06'),
(18, 'Chicken Shawarma', 'Shawarma', NULL, 'S001', 200.00, 100, 1, 'Shawarma', '2026-03-19 00:31:18', '2026-03-19 00:31:18'),
(19, 'Chicken Cheese Shawarma', 'Shawarma', NULL, 'S002', 250.00, 100, 1, 'Shawarma', '2026-03-19 00:31:18', '2026-03-19 00:31:18'),
(20, 'Zinger Shawarma', 'Shawarma', NULL, 'S003', 300.00, 100, 1, 'Shawarma', '2026-03-19 00:31:18', '2026-03-19 00:31:18'),
(21, 'Zinger Cheese Shawarma', 'Shawarma', NULL, 'S004', 350.00, 100, 1, 'Shawarma', '2026-03-19 00:31:18', '2026-03-19 00:31:18'),
(22, 'Pizza Shawarma', 'Shawarma', NULL, 'S005', 300.00, 100, 1, 'Shawarma', '2026-03-19 00:31:18', '2026-03-19 00:31:18'),
(23, 'Grill Cheese Shawarma', 'Shawarma', NULL, 'S006', 300.00, 100, 1, 'Shawarma', '2026-03-19 00:31:18', '2026-03-19 00:31:18'),
(24, 'Malai Boti Shawarma', 'Shawarma', NULL, 'S007', 300.00, 100, 1, 'Shawarma', '2026-03-19 00:31:18', '2026-03-19 00:31:18'),
(25, 'Chicken Plater Shawarma', 'Shawarma', NULL, 'S008', 400.00, 100, 1, 'Shawarma', '2026-03-19 00:31:18', '2026-03-19 00:31:18'),
(26, 'Zinger Plater Shawarma', 'Shawarma', NULL, 'S009', 450.00, 100, 1, 'Shawarma', '2026-03-19 00:31:18', '2026-03-19 00:31:18'),
(27, 'Chicken Pratha Roll', 'Pratha Roll', NULL, 'P001', 280.00, 100, 1, 'Pratha Roll', '2026-03-19 00:31:30', '2026-03-19 00:31:30'),
(28, 'Zinger Pratha Roll', 'Pratha Roll', NULL, 'P002', 350.00, 100, 1, 'Pratha Roll', '2026-03-19 00:31:30', '2026-03-19 00:31:30'),
(29, 'Behari Roll', 'Pratha Roll', NULL, 'P003', 400.00, 100, 1, 'Pratha Roll', '2026-03-19 00:31:30', '2026-03-19 00:31:30'),
(30, 'Kabab Roll', 'Pratha Roll', NULL, 'P004', 300.00, 100, 1, 'Pratha Roll', '2026-03-19 00:31:30', '2026-03-19 00:31:30'),
(31, 'Malai Boti Roll', 'Pratha Roll', NULL, 'P005', 350.00, 100, 1, 'Pratha Roll', '2026-03-19 00:31:30', '2026-03-19 00:31:30'),
(32, 'Extra Cheese', 'Addon', NULL, 'P006', 100.00, 100, 1, 'Addon', '2026-03-19 00:31:30', '2026-03-19 00:31:30'),
(33, 'Extra Pratha', 'Addon', NULL, 'P007', 100.00, 100, 1, 'Addon', '2026-03-19 00:31:30', '2026-03-19 00:31:30'),
(34, 'Chicken Tikka Pizza (S)', 'Pizza', NULL, 'R001', 450.00, 100, 1, 'Pizza', '2026-03-19 00:31:42', '2026-03-19 00:31:42'),
(35, 'Fajita Pizza (S)', 'Pizza', NULL, 'R002', 450.00, 100, 1, 'Pizza', '2026-03-19 00:31:42', '2026-03-19 00:31:42'),
(36, 'Chicken Supreme Pizza (S)', 'Pizza', NULL, 'R003', 500.00, 100, 1, 'Pizza', '2026-03-19 00:31:42', '2026-03-19 00:31:42'),
(37, 'Cheese Lover Pizza (S)', 'Pizza', NULL, 'R004', 500.00, 100, 1, 'Pizza', '2026-03-19 00:31:42', '2026-03-19 00:31:42'),
(38, 'Chicken Lover Pizza (S)', 'Pizza', NULL, 'R005', 450.00, 100, 1, 'Pizza', '2026-03-19 00:31:42', '2026-03-19 00:31:42'),
(39, 'Chicken Achari Pizza (S)', 'Pizza', NULL, 'R006', 450.00, 100, 1, 'Pizza', '2026-03-19 00:31:42', '2026-03-19 00:31:42'),
(40, 'Hot & Spice Pizza (S)', 'Pizza', NULL, 'R007', 450.00, 100, 1, 'Pizza', '2026-03-19 00:31:42', '2026-03-19 00:31:42'),
(41, 'Chicken Macaroni Pasta', 'Pasta', NULL, 'PA001', 400.00, 100, 1, 'Pasta', '2026-03-19 00:31:52', '2026-03-19 00:31:52'),
(42, 'Chicken Crunchi Pasta', 'Pasta', NULL, 'PA002', 400.00, 100, 1, 'Pasta', '2026-03-19 00:31:52', '2026-03-19 00:31:52'),
(43, 'Chicken & Cheese Pasta', 'Pasta', NULL, 'PA003', 400.00, 100, 1, 'Pasta', '2026-03-19 00:31:52', '2026-03-19 00:31:52'),
(44, 'Al Basha Special Pasta', 'Pasta', NULL, 'PA004', 450.00, 100, 1, 'Pasta', '2026-03-19 00:31:52', '2026-03-19 00:31:52'),
(45, 'Simple Fries (Small)', 'Fries', NULL, 'F001', 150.00, 100, 1, 'Fries', '2026-03-19 00:32:04', '2026-03-19 00:32:04'),
(46, 'Simple Fries (Large)', 'Fries', NULL, 'F002', 200.00, 100, 1, 'Fries', '2026-03-19 00:32:04', '2026-03-19 00:32:04'),
(47, 'Masala Fries (Small)', 'Fries', NULL, 'F003', 200.00, 100, 1, 'Fries', '2026-03-19 00:32:04', '2026-03-19 00:32:04'),
(48, 'Masala Fries (Large)', 'Fries', NULL, 'F004', 250.00, 100, 1, 'Fries', '2026-03-19 00:32:04', '2026-03-19 00:32:04'),
(49, 'Loaded Fries', 'Fries', NULL, 'F005', 350.00, 100, 1, 'Fries', '2026-03-19 00:32:04', '2026-03-19 00:32:04'),
(50, 'Chicken Crispy Wings (5pcs)', 'Starter', NULL, 'ST001', 350.00, 100, 1, 'Starter', '2026-03-19 00:32:14', '2026-03-19 00:32:14'),
(51, 'Chicken Crispy Wings (10pcs)', 'Starter', NULL, 'ST002', 650.00, 100, 1, 'Starter', '2026-03-19 00:32:14', '2026-03-19 00:32:14'),
(52, 'Oven Baked Wings (5pcs)', 'Starter', NULL, 'ST003', 350.00, 100, 1, 'Starter', '2026-03-19 00:32:14', '2026-03-19 00:32:14'),
(53, 'Oven Baked Wings (10pcs)', 'Starter', NULL, 'ST004', 650.00, 100, 1, 'Starter', '2026-03-19 00:32:14', '2026-03-19 00:32:14'),
(54, 'Chicken Nuggets (5pcs)', 'Starter', NULL, 'ST005', 300.00, 100, 1, 'Starter', '2026-03-19 00:32:14', '2026-03-19 00:32:14'),
(55, 'Chicken Nuggets (10pcs)', 'Starter', NULL, 'ST006', 550.00, 100, 1, 'Starter', '2026-03-19 00:32:14', '2026-03-19 00:32:14'),
(56, 'Chicken Club Sandwich', 'Sandwich', NULL, 'SW001', 300.00, 100, 1, 'Sandwich', '2026-03-19 00:32:24', '2026-03-19 00:32:24'),
(57, 'Chicken Grill Sandwich', 'Sandwich', NULL, 'SW002', 400.00, 100, 1, 'Sandwich', '2026-03-19 00:32:24', '2026-03-19 00:32:24'),
(58, 'Chicken Cheese Sandwich', 'Sandwich', NULL, 'SW003', 350.00, 100, 1, 'Sandwich', '2026-03-19 00:32:24', '2026-03-19 00:32:24'),
(59, 'Smokee Charcoal Wrap', 'Wrap', NULL, 'W001', 500.00, 100, 1, 'Wrap', '2026-03-19 00:32:34', '2026-03-19 00:32:34'),
(60, 'Fiery Mexican Wrap', 'Wrap', NULL, 'W002', 550.00, 100, 1, 'Wrap', '2026-03-19 00:32:34', '2026-03-19 00:32:34'),
(61, 'Stick Pizza', 'This is stick pizza', 'products/yHZPOwqmA3TJm6Dytepyiit687hPKqZT1Jq1Bk4M.jpg', '12432', 900.00, 10, 1, 'Main Course', '2026-03-18 20:03:08', '2026-03-18 20:03:08'),
(62, 'Al Basha Special Pizza (S)', 'Special Pizza', NULL, 'SP001', 750.00, 100, 1, 'Special Pizza', '2026-03-19 01:16:27', '2026-03-19 01:16:27'),
(63, 'Lazania Pizza (S)', 'Special Pizza', NULL, 'SP002', 650.00, 100, 1, 'Special Pizza', '2026-03-19 01:16:27', '2026-03-19 01:16:27'),
(64, 'Malai Boti Pizza (S)', 'Special Pizza', NULL, 'SP003', 600.00, 100, 1, 'Special Pizza', '2026-03-19 01:16:27', '2026-03-19 01:16:27'),
(65, 'Chicken Crunch Pizza (S)', 'Special Pizza', NULL, 'SP004', 600.00, 100, 1, 'Special Pizza', '2026-03-19 01:16:27', '2026-03-19 01:16:27'),
(66, 'Kabab Stuff Pizza (S)', 'Special Pizza', NULL, 'SP005', 1100.00, 100, 1, 'Special Pizza', '2026-03-19 01:16:27', '2026-03-19 01:16:27'),
(67, 'Chicken Behari Kabab Pizza (S)', 'Special Pizza', NULL, 'SP006', 600.00, 100, 1, 'Special Pizza', '2026-03-19 01:16:27', '2026-03-19 01:16:27'),
(68, 'Crown Crust Pizza (S)', 'Special Pizza', NULL, 'SP007', 1100.00, 100, 1, 'Special Pizza', '2026-03-19 01:16:27', '2026-03-19 01:16:27'),
(69, 'Cheese Stuff Pizza (S)', 'Special Pizza', NULL, 'Special Pizza', 1100.00, 100, 1, 'SP008', '2026-03-19 01:16:27', '2026-03-19 01:16:27'),
(70, 'Crown Crust Kabab Pizza (S)', 'Special Pizza', NULL, 'SP009', 1150.00, 100, 1, 'Special Pizza', '2026-03-19 01:16:27', '2026-03-19 01:16:27'),
(71, 'Cheese Stick Pizza (S)', 'Special Pizza', NULL, 'SP010', 600.00, 100, 1, 'Special Pizza', '2026-03-19 01:16:27', '2026-03-19 01:16:27'),
(72, 'Chicken Tikka Pizza (M)', 'Pizza', NULL, 'RM001', 850.00, 100, 1, 'Pizza', '2026-03-19 01:18:10', '2026-03-19 01:18:10'),
(73, 'Fajita Pizza (M)', 'Pizza', NULL, 'RM002', 850.00, 100, 1, 'Pizza', '2026-03-19 01:18:10', '2026-03-19 01:18:10'),
(74, 'Chicken Supreme Pizza (M)', 'Pizza', NULL, 'RM003', 850.00, 100, 1, 'Pizza', '2026-03-19 01:18:10', '2026-03-19 01:18:10'),
(75, 'Cheese Lover Pizza (M)', 'Pizza', NULL, 'RM004', 850.00, 100, 1, 'Pizza', '2026-03-19 01:18:10', '2026-03-19 01:18:10'),
(76, 'Chicken Lover Pizza (M)', 'Pizza', NULL, 'RM005', 850.00, 100, 1, 'Pizza', '2026-03-19 01:18:10', '2026-03-19 01:18:10'),
(77, 'Chicken Achari Pizza (M)', 'Pizza', NULL, 'RM006', 850.00, 100, 1, 'Pizza', '2026-03-19 01:18:10', '2026-03-19 01:18:10'),
(78, 'Hot & Spice Pizza (M)', 'Pizza', NULL, 'RM007', 850.00, 100, 1, 'Pizza', '2026-03-19 01:18:10', '2026-03-19 01:18:10'),
(79, 'Chicken Tikka Pizza (L)', 'Pizza', NULL, 'RL001', 1200.00, 100, 1, 'Pizza', '2026-03-19 01:18:10', '2026-03-19 01:18:10'),
(80, 'Fajita Pizza (L)', 'Pizza', NULL, 'RL002', 1200.00, 100, 1, 'Pizza', '2026-03-19 01:18:10', '2026-03-19 01:18:10'),
(81, 'Chicken Supreme Pizza (L)', 'Pizza', NULL, 'RL003', 1250.00, 100, 1, 'Pizza', '2026-03-19 01:18:10', '2026-03-19 01:18:10'),
(82, 'Cheese Lover Pizza (L)', 'Pizza', NULL, 'RL004', 1250.00, 100, 1, 'Pizza', '2026-03-19 01:18:10', '2026-03-19 01:18:10'),
(83, 'Chicken Lover Pizza (L)', 'Pizza', NULL, 'RL005', 1200.00, 100, 1, 'Pizza', '2026-03-19 01:18:10', '2026-03-19 01:18:10'),
(84, 'Chicken Achari Pizza (L)', 'Pizza', NULL, 'RL006', 1150.00, 100, 1, 'Pizza', '2026-03-19 01:18:10', '2026-03-19 01:18:10'),
(85, 'Hot & Spice Pizza (L)', 'Pizza', NULL, 'RL007', 1200.00, 100, 1, 'Pizza', '2026-03-19 01:18:10', '2026-03-19 01:18:10'),
(86, 'Chicken Tikka Pizza (F)', 'Pizza', NULL, 'RF001', 1600.00, 100, 1, 'Pizza', '2026-03-19 01:18:10', '2026-03-19 01:18:10'),
(87, 'Fajita Pizza (F)', 'Pizza', NULL, 'RF002', 1600.00, 100, 1, 'Pizza', '2026-03-19 01:18:10', '2026-03-19 01:18:10'),
(88, 'Chicken Supreme Pizza (F)', 'Pizza', NULL, 'RF003', 1650.00, 100, 1, 'Pizza', '2026-03-19 01:18:10', '2026-03-19 01:18:10'),
(89, 'Cheese Lover Pizza (F)', 'Pizza', NULL, 'RF004', 1650.00, 100, 1, 'Pizza', '2026-03-19 01:18:10', '2026-03-19 01:18:10'),
(90, 'Chicken Lover Pizza (F)', 'Pizza', NULL, 'RF005', 1600.00, 100, 1, 'Pizza', '2026-03-19 01:18:10', '2026-03-19 01:18:10'),
(91, 'Chicken Achari Pizza (F)', 'Pizza', NULL, 'RF006', 1600.00, 100, 1, 'Pizza', '2026-03-19 01:18:10', '2026-03-19 01:18:10'),
(92, 'Hot & Spice Pizza (F)', 'Pizza', NULL, 'RF007', 1600.00, 100, 1, 'Pizza', '2026-03-19 01:18:10', '2026-03-19 01:18:10'),
(93, 'Al Basha Special Pizza (M)', 'Special Pizza', NULL, 'SPM001', 1100.00, 100, 1, 'Special Pizza', '2026-03-19 01:19:48', '2026-03-19 01:19:48'),
(94, 'Lazania Pizza (M)', 'Special Pizza', NULL, 'SPM002', 1000.00, 100, 1, 'Special Pizza', '2026-03-19 01:19:48', '2026-03-19 01:19:48'),
(95, 'Malai Boti Pizza (M)', 'Special Pizza', NULL, 'SPM003', 1000.00, 100, 1, 'Special Pizza', '2026-03-19 01:19:48', '2026-03-19 01:19:48'),
(96, 'Chicken Crunch Pizza (M)', 'Special Pizza', NULL, 'SPM004', 1000.00, 100, 1, 'Special Pizza', '2026-03-19 01:19:48', '2026-03-19 01:19:48'),
(97, 'Kabab Stuff Pizza (M)', 'Special Pizza', NULL, 'SPM005', 1100.00, 100, 1, 'Special Pizza', '2026-03-19 01:19:48', '2026-03-19 01:19:48'),
(98, 'Chicken Behari Kabab Pizza (M)', 'Special Pizza', NULL, 'SPM006', 1100.00, 100, 1, 'Special Pizza', '2026-03-19 01:19:48', '2026-03-19 01:19:48'),
(99, 'Crown Crust Pizza (M)', 'Special Pizza', NULL, 'SPM007', 1100.00, 100, 1, 'Special Pizza', '2026-03-19 01:19:48', '2026-03-19 01:19:48'),
(100, 'Cheese Stuff Pizza (M)', 'Special Pizza', NULL, 'SPM008', 1100.00, 100, 1, 'Special Pizza', '2026-03-19 01:19:48', '2026-03-19 01:19:48'),
(101, 'Crown Crust Kabab Pizza (M)', 'Special Pizza', NULL, 'SPM009', 1150.00, 100, 1, 'Special Pizza', '2026-03-19 01:19:48', '2026-03-19 01:19:48'),
(102, 'Cheese Stick Pizza (M)', 'Special Pizza', NULL, 'SPM010', 1000.00, 100, 1, 'Special Pizza', '2026-03-19 01:19:48', '2026-03-19 01:19:48'),
(103, 'Al Basha Special Pizza (L)', 'Special Pizza', NULL, 'SPL001', 1700.00, 100, 1, 'Special Pizza', '2026-03-19 01:19:48', '2026-03-19 01:19:48'),
(104, 'Lazania Pizza (L)', 'Special Pizza', NULL, 'SPL002', 1500.00, 100, 1, 'Special Pizza', '2026-03-19 01:19:48', '2026-03-19 01:19:48'),
(105, 'Malai Boti Pizza (L)', 'Special Pizza', NULL, 'SPL003', 1500.00, 100, 1, 'Special Pizza', '2026-03-19 01:19:48', '2026-03-19 01:19:48'),
(106, 'Chicken Crunch Pizza (L)', 'Special Pizza', NULL, 'SPL004', 1500.00, 100, 1, 'Special Pizza', '2026-03-19 01:19:48', '2026-03-19 01:19:48'),
(107, 'Kabab Stuff Pizza (L)', 'Special Pizza', NULL, 'SPL005', 1600.00, 100, 1, 'Special Pizza', '2026-03-19 01:19:48', '2026-03-19 01:19:48'),
(108, 'Chicken Behari Kabab Pizza (L)', 'Special Pizza', NULL, 'SPL006', 1500.00, 100, 1, 'Special Pizza', '2026-03-19 01:19:48', '2026-03-19 01:19:48'),
(109, 'Crown Crust Pizza (L)', 'Special Pizza', NULL, 'SPL007', 1500.00, 100, 1, 'Special Pizza', '2026-03-19 01:19:48', '2026-03-19 01:19:48'),
(110, 'Cheese Stuff Pizza (L)', 'Special Pizza', NULL, 'SPL008', 1500.00, 100, 1, 'Special Pizza', '2026-03-19 01:19:48', '2026-03-19 01:19:48'),
(111, 'Crown Crust Kabab Pizza (L)', 'Special Pizza', NULL, 'SPL009', 1600.00, 100, 1, 'Special Pizza', '2026-03-19 01:19:48', '2026-03-19 01:19:48'),
(112, 'Cheese Stick Pizza (L)', 'Special Pizza', NULL, 'SPL010', 1500.00, 100, 1, 'Special Pizza', '2026-03-19 01:19:48', '2026-03-19 01:19:48'),
(113, 'Al Basha Special Pizza (F)', 'Special Pizza', NULL, 'SPF001', 2100.00, 100, 1, 'Special Pizza', '2026-03-19 01:19:48', '2026-03-19 01:19:48'),
(114, 'Lazania Pizza (F)', 'Special Pizza', NULL, 'SPF002', 1900.00, 100, 1, 'Special Pizza', '2026-03-19 01:19:48', '2026-03-19 01:19:48'),
(115, 'Malai Boti Pizza (F)', 'Special Pizza', NULL, 'SPF003', 1900.00, 100, 1, 'Special Pizza', '2026-03-19 01:19:48', '2026-03-19 01:19:48'),
(116, 'Chicken Crunch Pizza (F)', 'Special Pizza', NULL, 'SPF004', 1900.00, 100, 1, 'Special Pizza', '2026-03-19 01:19:48', '2026-03-19 01:19:48'),
(117, 'Kabab Stuff Pizza (F)', 'Special Pizza', NULL, 'SPF005', 2000.00, 100, 1, 'Special Pizza', '2026-03-19 01:19:48', '2026-03-19 01:19:48'),
(118, 'Chicken Behari Kabab Pizza (F)', 'Special Pizza', NULL, 'SPF006', 2000.00, 100, 1, 'Special Pizza', '2026-03-19 01:19:48', '2026-03-19 01:19:48'),
(119, 'Crown Crust Pizza (F)', 'Special Pizza', NULL, 'SPF007', 2000.00, 100, 1, 'Special Pizza', '2026-03-19 01:19:48', '2026-03-19 01:19:48'),
(120, 'Cheese Stuff Pizza (F)', 'Special Pizza', NULL, 'SPF008', 2000.00, 100, 1, 'Special Pizza', '2026-03-19 01:19:48', '2026-03-19 01:19:48'),
(121, 'Crown Crust Kabab Pizza (F)', 'Special Pizza', NULL, 'SPF009', 2100.00, 100, 1, 'Special Pizza', '2026-03-19 01:19:48', '2026-03-19 01:19:48'),
(122, 'Cheese Stick Pizza (F)', 'Special Pizza', NULL, 'SPF010', 1900.00, 100, 1, 'Special Pizza', '2026-03-19 01:19:48', '2026-03-19 01:19:48');



/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;