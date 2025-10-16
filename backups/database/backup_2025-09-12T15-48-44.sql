-- Database backup generated on 2025-09-12 17:48:44
-- Generator: Labsys Database Backup System
-- Database: manajemen_laboratorium

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET FOREIGN_KEY_CHECKS = 0;

-- Table structure for table `activity_log`
DROP TABLE IF EXISTS `activity_log`;
CREATE TABLE `activity_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `activity` varchar(255) NOT NULL,
  `table_affected` varchar(100) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=388 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `activity_log`
LOCK TABLES `activity_log` WRITE;
INSERT INTO `activity_log` (`log_id`, `user_id`, `activity`, `table_affected`, `record_id`, `ip_address`, `created_at`) VALUES
('142', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 11:40:24'),
('143', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 11:40:29'),
('144', '1', 'Hasil pemeriksaan dicetak: LAB20250009', 'pemeriksaan_lab', '9', '::1', '2025-09-12 11:40:32'),
('145', '1', 'Pengguna dinonaktifkan: admin front', 'users', '2', '::1', '2025-09-12 11:40:45'),
('146', '1', 'Pengguna diaktifkan: admin front', 'users', '2', '::1', '2025-09-12 11:40:47'),
('147', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 11:40:51'),
('148', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 11:41:09'),
('149', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 11:41:10'),
('150', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 11:44:26'),
('151', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 11:44:28'),
('152', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 11:44:30'),
('153', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 11:44:40'),
('154', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 11:44:42'),
('155', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 11:49:25'),
('156', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 11:49:27'),
('157', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 11:49:28'),
('158', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 11:49:29'),
('159', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 11:49:30'),
('160', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 11:49:31'),
('161', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 11:49:32'),
('162', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 11:49:34'),
('163', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 11:49:35'),
('164', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 11:49:35'),
('165', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 11:49:36'),
('166', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 11:49:38'),
('167', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 11:49:39'),
('168', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 11:49:39'),
('169', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 11:49:40'),
('170', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 11:49:41'),
('171', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-12 11:52:35'),
('172', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 11:52:39'),
('173', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 11:52:40'),
('174', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 11:52:41'),
('175', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 11:52:42'),
('176', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 11:52:51'),
('177', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 11:52:51'),
('178', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 11:52:55'),
('179', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 12:21:49'),
('180', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 12:21:50'),
('181', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 12:25:17'),
('182', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 12:25:29'),
('183', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 12:26:21'),
('184', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 12:26:26'),
('185', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 12:26:31'),
('186', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 12:27:56'),
('187', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 12:27:59'),
('188', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 12:28:00'),
('189', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 12:28:14'),
('190', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 12:28:18'),
('191', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 12:28:20'),
('192', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 12:35:13'),
('193', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 12:43:57'),
('194', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 12:47:34'),
('195', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 12:47:36'),
('196', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 12:49:23'),
('197', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 12:54:39'),
('198', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 13:01:56'),
('199', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 13:02:02'),
('200', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 13:04:35'),
('201', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 13:04:39'),
('202', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 13:05:43'),
('203', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 13:06:29'),
('204', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 13:09:31'),
('205', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 13:09:33'),
('206', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 13:09:36'),
('207', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 13:10:55'),
('208', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 13:10:57'),
('209', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 13:13:59'),
('210', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 13:14:01'),
('211', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 13:14:19'),
('212', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 13:15:26'),
('213', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 13:15:49'),
('214', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 13:15:50'),
('215', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 13:15:50'),
('216', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 13:15:50'),
('217', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 13:16:04'),
('218', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 13:16:05'),
('219', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 13:16:05'),
('220', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 13:16:05'),
('221', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 13:16:13'),
('222', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 13:16:50'),
('223', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 13:17:39'),
('224', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 13:18:08'),
('225', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 13:18:53'),
('226', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 13:21:20'),
('227', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 13:23:15'),
('228', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 13:47:17'),
('229', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 13:47:26'),
('230', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 13:47:33'),
('231', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 13:48:18'),
('232', '1', 'Pengguna dinonaktifkan: admin_front', 'users', '2', '::1', '2025-09-12 13:48:24'),
('233', '1', 'Pengguna diaktifkan: admin_front', 'users', '2', '::1', '2025-09-12 13:48:26'),
('234', '1', 'Data pengguna diperbarui: admin_front', 'users', '2', '::1', '2025-09-12 13:48:41'),
('235', '1', 'Data pengguna diperbarui: administrasi', 'users', '2', '::1', '2025-09-12 13:49:20'),
('236', '1', 'Data pengguna diperbarui: admini_front', 'users', '2', '::1', '2025-09-12 13:49:20'),
('237', '2', 'User logged in', 'users', NULL, '::1', '2025-09-12 13:50:21'),
('238', '1', 'Pengguna dinonaktifkan: admini_front', 'users', '2', '::1', '2025-09-12 13:50:39'),
('240', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 13:50:42'),
('241', '1', 'Log aktivitas dihapus', 'activity_log', '239', '::1', '2025-09-12 13:50:56'),
('242', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 13:50:56'),
('243', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 13:51:20'),
('244', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 13:51:26'),
('245', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 13:51:47'),
('246', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 13:52:04'),
('247', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 13:52:05'),
('248', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 13:52:06'),
('249', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 13:52:06'),
('250', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 13:52:07'),
('251', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 13:52:08'),
('252', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 13:52:09'),
('253', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 13:53:36'),
('254', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 14:02:50'),
('255', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 14:02:55'),
('256', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 14:09:54'),
('257', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 14:09:56'),
('258', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 14:20:28'),
('259', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 14:21:51'),
('260', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 14:25:45'),
('261', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 14:26:08'),
('262', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 14:26:08'),
('263', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 14:26:12'),
('264', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 14:26:13'),
('265', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 14:26:13'),
('266', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 14:28:53'),
('267', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 14:29:38'),
('268', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 14:30:11'),
('269', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 14:32:58'),
('270', '1', 'Invoice dicetak: INV20250001', 'invoice', '1', '::1', '2025-09-12 14:33:07'),
('271', '1', 'Invoice dicetak: INV20250001', 'invoice', '1', '::1', '2025-09-12 14:37:35'),
('272', '1', 'Invoice dicetak: INV20250001', 'invoice', '1', '::1', '2025-09-12 14:39:36'),
('273', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-12 14:40:15'),
('274', '1', 'Invoice dicetak: INV20250001', 'invoice', '1', '::1', '2025-09-12 14:43:09'),
('275', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 14:43:12'),
('276', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 14:43:13'),
('277', '1', 'Invoice dicetak: INV20250001', 'invoice', '1', '::1', '2025-09-12 14:43:22'),
('278', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 14:46:05'),
('279', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 14:46:07'),
('280', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 14:46:09'),
('281', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 14:46:11'),
('282', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 14:46:13'),
('283', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 14:46:16'),
('284', '1', 'Invoice dicetak: INV20250001', 'invoice', '1', '::1', '2025-09-12 14:46:22'),
('285', '1', 'Invoice dicetak: INV20250001', 'invoice', '1', '::1', '2025-09-12 14:46:29'),
('286', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 14:51:26'),
('287', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 14:51:29'),
('288', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 14:51:31'),
('289', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 14:51:31'),
('290', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 14:51:32'),
('291', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 14:51:36'),
('292', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 14:51:37'),
('293', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 14:51:38'),
('294', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 14:51:39'),
('295', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 14:51:45'),
('296', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 14:58:05'),
('297', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 14:58:07'),
('298', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 14:58:08'),
('299', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 14:58:09'),
('300', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 14:58:10'),
('301', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 14:58:14'),
('302', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 14:58:16'),
('303', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 15:00:08'),
('304', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 15:01:37'),
('305', '1', 'Pengguna dinonaktifkan: admini_front', 'users', '2', '::1', '2025-09-12 15:01:58'),
('306', '1', 'Pengguna diaktifkan: admini_front', 'users', '2', '::1', '2025-09-12 15:02:00'),
('307', '1', 'Data pengguna diperbarui: admini_front', 'users', '2', '::1', '2025-09-12 15:02:05'),
('308', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 15:03:14'),
('309', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 15:03:19'),
('310', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 15:03:21'),
('311', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 15:03:22'),
('312', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 15:03:25'),
('313', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-12 15:05:12'),
('314', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 15:07:11'),
('315', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 15:07:24'),
('316', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 15:07:25'),
('317', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 15:07:34'),
('318', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 15:07:35'),
('319', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 15:07:43'),
('320', '4', 'User logged in', 'users', NULL, '::1', '2025-09-12 15:09:03'),
('321', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 15:13:35'),
('322', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 15:13:38'),
('323', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 15:14:06'),
('324', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 15:14:22'),
('325', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 15:14:23'),
('326', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 15:14:54'),
('327', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 15:15:00'),
('328', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 15:15:02'),
('329', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 15:17:19'),
('330', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 15:18:59'),
('331', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 15:19:10'),
('332', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 15:19:14'),
('333', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 15:19:17'),
('334', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 15:19:23'),
('335', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 15:19:26'),
('336', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 15:19:27'),
('337', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 15:20:58'),
('338', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 15:21:03'),
('339', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 15:21:20'),
('340', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 15:21:51'),
('341', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 15:21:59'),
('342', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 15:22:06'),
('343', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 15:25:43'),
('344', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 15:26:17'),
('345', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 15:26:31'),
('346', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 15:26:34'),
('347', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 15:26:37'),
('348', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 15:26:43'),
('349', '4', 'Lab request accepted', 'pemeriksaan_lab', '3', '::1', '2025-09-12 15:28:41'),
('350', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 15:28:45'),
('351', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 15:29:09'),
('352', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 15:31:26'),
('353', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 15:31:29'),
('354', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 15:31:41'),
('355', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 15:32:14'),
('356', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 15:32:54'),
('357', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 15:35:17'),
('358', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 15:35:43'),
('359', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 15:36:55'),
('360', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 15:37:12'),
('361', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 15:37:34'),
('362', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 15:38:06'),
('363', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 15:39:34'),
('364', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 15:39:37'),
('365', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 15:39:42'),
('366', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 15:43:14'),
('367', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 15:43:16'),
('368', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 15:45:21'),
('369', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 15:45:40'),
('370', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 15:47:23'),
('371', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 15:47:24'),
('372', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 15:47:26'),
('373', '1', 'Invoice dicetak: INV20250001', 'invoice', '1', '::1', '2025-09-12 17:38:02'),
('374', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 17:38:42'),
('375', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 17:38:45'),
('376', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 17:38:49'),
('377', '1', 'Mengakses halaman backup database', 'system', NULL, '::1', '2025-09-12 17:39:25'),
('378', '1', 'Database backup dibuat: backup_2025-09-12T15-39-34', 'system', NULL, '::1', '2025-09-12 17:39:34'),
('379', '1', 'Database backup dibuat: backup_2025-09-12T15-39-39', 'system', NULL, '::1', '2025-09-12 17:39:39'),
('380', '1', 'Database backup dibuat: backup_2025-09-12T15-39-41', 'system', NULL, '::1', '2025-09-12 17:39:41'),
('381', '1', 'Database backup dibuat: Database', 'system', NULL, '::1', '2025-09-12 17:39:50'),
('382', '1', 'Database backup dibuat: Database', 'system', NULL, '::1', '2025-09-12 17:40:08'),
('383', '1', 'Database backup dibuat: Database', 'system', NULL, '::1', '2025-09-12 17:40:15'),
('384', '1', 'Mengakses halaman data master', 'system', NULL, '::1', '2025-09-12 17:42:09'),
('385', '1', 'Mengakses halaman backup database', 'system', NULL, '::1', '2025-09-12 17:46:23'),
('386', '1', 'Mengakses halaman backup database', 'system', NULL, '::1', '2025-09-12 17:48:37'),
('387', '1', 'Database backup dibuat: backup_2025-09-12T15-48-41', 'system', NULL, '::1', '2025-09-12 17:48:42');
UNLOCK TABLES;

-- Table structure for table `administrasi`
DROP TABLE IF EXISTS `administrasi`;
CREATE TABLE `administrasi` (
  `administrasi_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `nama_admin` varchar(100) NOT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`administrasi_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `administrasi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `administrasi`
LOCK TABLES `administrasi` WRITE;
INSERT INTO `administrasi` (`administrasi_id`, `user_id`, `nama_admin`, `telepon`, `created_at`) VALUES
('1', '2', 'Admin Front Office', '0852-8218-2747', '2025-08-28 17:41:13');
UNLOCK TABLES;

-- Table structure for table `administrator`
DROP TABLE IF EXISTS `administrator`;
CREATE TABLE `administrator` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `nama_admin` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`admin_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `administrator_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `administrator`
LOCK TABLES `administrator` WRITE;
INSERT INTO `administrator` (`admin_id`, `user_id`, `nama_admin`, `created_at`) VALUES
('1', '1', 'Super Administrator', '2025-08-28 17:41:13');
UNLOCK TABLES;

-- Table structure for table `alat_laboratorium`
DROP TABLE IF EXISTS `alat_laboratorium`;
CREATE TABLE `alat_laboratorium` (
  `alat_id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_alat` varchar(100) NOT NULL,
  `kode_unik` varchar(50) DEFAULT NULL,
  `merek_model` varchar(100) DEFAULT NULL,
  `lokasi` varchar(100) DEFAULT NULL,
  `status_alat` enum('Normal','Perlu Kalibrasi','Rusak','Sedang Kalibrasi') DEFAULT 'Normal',
  `jadwal_kalibrasi` date DEFAULT NULL,
  `tanggal_kalibrasi_terakhir` date DEFAULT NULL,
  `riwayat_perbaikan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`alat_id`),
  UNIQUE KEY `kode_unik` (`kode_unik`),
  KEY `idx_alat_status` (`status_alat`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `alat_laboratorium`
LOCK TABLES `alat_laboratorium` WRITE;
INSERT INTO `alat_laboratorium` (`alat_id`, `nama_alat`, `kode_unik`, `merek_model`, `lokasi`, `status_alat`, `jadwal_kalibrasi`, `tanggal_kalibrasi_terakhir`, `riwayat_perbaikan`, `created_at`, `updated_at`) VALUES
('1', 'Chemistry Analyzer', 'ALT001', 'Abbott Architect c4000', 'Lab Kimia', 'Normal', '2025-09-01', NULL, NULL, '2025-08-28 17:41:13', '2025-08-28 17:41:13'),
('2', 'Hematology Analyzer', 'ALT002', 'Sysmex XS-1000i', 'Lab Hematologi', 'Normal', '2025-09-15', NULL, NULL, '2025-08-28 17:41:13', '2025-08-28 17:41:13'),
('3', 'Mikroskop', 'ALT003', 'Olympus CX23', 'Lab Urinologi', 'Normal', '2025-10-01', NULL, NULL, '2025-08-28 17:41:13', '2025-08-28 17:41:13');
UNLOCK TABLES;

-- Table structure for table `hematologi`
DROP TABLE IF EXISTS `hematologi`;
CREATE TABLE `hematologi` (
  `hematologi_id` int(11) NOT NULL AUTO_INCREMENT,
  `pemeriksaan_id` int(11) NOT NULL,
  `hemoglobin` decimal(5,2) DEFAULT NULL,
  `hematokrit` decimal(5,2) DEFAULT NULL,
  `laju_endap_darah` decimal(5,2) DEFAULT NULL,
  `clotting_time` time DEFAULT NULL,
  `bleeding_time` time DEFAULT NULL,
  `golongan_darah` enum('A','B','AB','O') DEFAULT NULL,
  `rhesus` enum('+','-') DEFAULT NULL,
  `malaria` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`hematologi_id`),
  KEY `pemeriksaan_id` (`pemeriksaan_id`),
  CONSTRAINT `hematologi_ibfk_1` FOREIGN KEY (`pemeriksaan_id`) REFERENCES `pemeriksaan_lab` (`pemeriksaan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `hematologi`
LOCK TABLES `hematologi` WRITE;
INSERT INTO `hematologi` (`hematologi_id`, `pemeriksaan_id`, `hemoglobin`, `hematokrit`, `laju_endap_darah`, `clotting_time`, `bleeding_time`, `golongan_darah`, `rhesus`, `malaria`, `created_at`) VALUES
('1', '10', '16.00', '45.00', '11.00', '20:47:00', '20:46:00', 'A', '+', 'maalaria', '2025-09-07 14:47:30');
UNLOCK TABLES;

-- Table structure for table `ims`
DROP TABLE IF EXISTS `ims`;
CREATE TABLE `ims` (
  `ims_id` int(11) NOT NULL AUTO_INCREMENT,
  `pemeriksaan_id` int(11) NOT NULL,
  `sifilis` enum('Reaktif','Non-Reaktif') DEFAULT NULL,
  `duh_tubuh` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ims_id`),
  KEY `pemeriksaan_id` (`pemeriksaan_id`),
  CONSTRAINT `ims_ibfk_1` FOREIGN KEY (`pemeriksaan_id`) REFERENCES `pemeriksaan_lab` (`pemeriksaan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `ims`
-- No data found for table `ims`

-- Table structure for table `invoice`
DROP TABLE IF EXISTS `invoice`;
CREATE TABLE `invoice` (
  `invoice_id` int(11) NOT NULL AUTO_INCREMENT,
  `pemeriksaan_id` int(11) NOT NULL,
  `nomor_invoice` varchar(50) NOT NULL,
  `tanggal_invoice` date NOT NULL,
  `jenis_pembayaran` enum('umum','bpjs') NOT NULL,
  `total_biaya` decimal(10,2) NOT NULL,
  `status_pembayaran` enum('belum_bayar','lunas','cicilan') DEFAULT 'belum_bayar',
  `metode_pembayaran` varchar(50) DEFAULT NULL,
  `nomor_kartu_bpjs` varchar(50) DEFAULT NULL,
  `nomor_sep` varchar(50) DEFAULT NULL,
  `tanggal_pembayaran` date DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`invoice_id`),
  UNIQUE KEY `nomor_invoice` (`nomor_invoice`),
  KEY `pemeriksaan_id` (`pemeriksaan_id`),
  KEY `idx_invoice_status` (`status_pembayaran`),
  KEY `idx_invoice_jenis` (`jenis_pembayaran`),
  CONSTRAINT `invoice_ibfk_1` FOREIGN KEY (`pemeriksaan_id`) REFERENCES `pemeriksaan_lab` (`pemeriksaan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `invoice`
LOCK TABLES `invoice` WRITE;
INSERT INTO `invoice` (`invoice_id`, `pemeriksaan_id`, `nomor_invoice`, `tanggal_invoice`, `jenis_pembayaran`, `total_biaya`, `status_pembayaran`, `metode_pembayaran`, `nomor_kartu_bpjs`, `nomor_sep`, `tanggal_pembayaran`, `keterangan`, `created_at`) VALUES
('1', '10', 'INV20250001', '2025-09-12', 'umum', '120000.00', 'belum_bayar', 'Tunai', NULL, NULL, NULL, 'Invoice untuk pemeriksaan hematologi', '2025-09-12 16:33:34');
UNLOCK TABLES;

-- Table structure for table `kimia_darah`
DROP TABLE IF EXISTS `kimia_darah`;
CREATE TABLE `kimia_darah` (
  `kimia_id` int(11) NOT NULL AUTO_INCREMENT,
  `pemeriksaan_id` int(11) NOT NULL,
  `gula_darah_sewaktu` decimal(5,2) DEFAULT NULL,
  `gula_darah_puasa` decimal(5,2) DEFAULT NULL,
  `gula_darah_2jam_pp` decimal(5,2) DEFAULT NULL,
  `cholesterol_total` decimal(5,2) DEFAULT NULL,
  `cholesterol_hdl` decimal(5,2) DEFAULT NULL,
  `cholesterol_ldl` decimal(5,2) DEFAULT NULL,
  `trigliserida` decimal(5,2) DEFAULT NULL,
  `asam_urat` decimal(5,2) DEFAULT NULL,
  `ureum` decimal(5,2) DEFAULT NULL,
  `creatinin` decimal(5,2) DEFAULT NULL,
  `sgpt` decimal(5,2) DEFAULT NULL,
  `sgot` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`kimia_id`),
  KEY `pemeriksaan_id` (`pemeriksaan_id`),
  CONSTRAINT `kimia_darah_ibfk_1` FOREIGN KEY (`pemeriksaan_id`) REFERENCES `pemeriksaan_lab` (`pemeriksaan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `kimia_darah`
LOCK TABLES `kimia_darah` WRITE;
INSERT INTO `kimia_darah` (`kimia_id`, `pemeriksaan_id`, `gula_darah_sewaktu`, `gula_darah_puasa`, `gula_darah_2jam_pp`, `cholesterol_total`, `cholesterol_hdl`, `cholesterol_ldl`, `trigliserida`, `asam_urat`, `ureum`, `creatinin`, `sgpt`, `sgot`, `created_at`) VALUES
('1', '9', '70.00', '60.00', '120.00', '788.00', '50.00', '130.00', '34.00', '78.00', '70.00', '4.00', '78.00', '76.00', '2025-09-06 16:48:18');
UNLOCK TABLES;

-- Table structure for table `lab`
DROP TABLE IF EXISTS `lab`;
CREATE TABLE `lab` (
  `lab_id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`lab_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `lab`
LOCK TABLES `lab` WRITE;
INSERT INTO `lab` (`lab_id`, `nama`, `alamat`, `telephone`, `email`, `created_at`) VALUES
('1', 'Laboratorium Labsy', 'Jl. Kesehatan No. 123, Jakarta', '021-12345678', 'info@labprima.com', '2025-08-28 17:41:13');
UNLOCK TABLES;

-- Table structure for table `laporan`
DROP TABLE IF EXISTS `laporan`;
CREATE TABLE `laporan` (
  `laporan_id` int(11) NOT NULL AUTO_INCREMENT,
  `pemeriksaan_id` int(11) NOT NULL,
  `jenis_laporan` varchar(100) DEFAULT NULL,
  `isi_laporan` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `dibuat_oleh` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`laporan_id`),
  KEY `pemeriksaan_id` (`pemeriksaan_id`),
  KEY `dibuat_oleh` (`dibuat_oleh`),
  CONSTRAINT `laporan_ibfk_1` FOREIGN KEY (`pemeriksaan_id`) REFERENCES `pemeriksaan_lab` (`pemeriksaan_id`),
  CONSTRAINT `laporan_ibfk_2` FOREIGN KEY (`dibuat_oleh`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `laporan`
-- No data found for table `laporan`

-- Table structure for table `mls`
DROP TABLE IF EXISTS `mls`;
CREATE TABLE `mls` (
  `mls_id` int(11) NOT NULL AUTO_INCREMENT,
  `pemeriksaan_id` int(11) NOT NULL,
  `jenis_tes` varchar(100) DEFAULT NULL,
  `hasil` varchar(500) DEFAULT NULL,
  `nilai_rujukan` varchar(100) DEFAULT NULL,
  `satuan` varchar(50) DEFAULT NULL,
  `metode` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`mls_id`),
  KEY `pemeriksaan_id` (`pemeriksaan_id`),
  CONSTRAINT `mls_ibfk_1` FOREIGN KEY (`pemeriksaan_id`) REFERENCES `pemeriksaan_lab` (`pemeriksaan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `mls`
-- No data found for table `mls`

-- Table structure for table `pasien`
DROP TABLE IF EXISTS `pasien`;
CREATE TABLE `pasien` (
  `pasien_id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `nik` varchar(20) DEFAULT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `tempat_lahir` varchar(50) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `umur` int(11) DEFAULT NULL,
  `alamat_domisili` text DEFAULT NULL,
  `pekerjaan` varchar(100) DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `kontak_darurat` varchar(100) DEFAULT NULL,
  `riwayat_pasien` text DEFAULT NULL,
  `permintaan_pemeriksaan` text DEFAULT NULL,
  `dokter_perujuk` varchar(100) DEFAULT NULL COMMENT 'Nama dokter yang memberikan rujukan',
  `asal_rujukan` varchar(100) DEFAULT NULL COMMENT 'Asal rumah sakit/klinik rujukan',
  `nomor_rujukan` varchar(50) DEFAULT NULL COMMENT 'Nomor surat rujukan',
  `tanggal_rujukan` date DEFAULT NULL COMMENT 'Tanggal surat rujukan',
  `diagnosis_awal` text DEFAULT NULL COMMENT 'Diagnosis awal dari dokter perujuk',
  `rekomendasi_pemeriksaan` text DEFAULT NULL COMMENT 'Rekomendasi pemeriksaan dari dokter perujuk',
  `nomor_registrasi` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`pasien_id`),
  UNIQUE KEY `nik` (`nik`),
  UNIQUE KEY `nomor_registrasi` (`nomor_registrasi`),
  KEY `idx_pasien_nik` (`nik`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `pasien`
LOCK TABLES `pasien` WRITE;
INSERT INTO `pasien` (`pasien_id`, `nama`, `nik`, `jenis_kelamin`, `tempat_lahir`, `tanggal_lahir`, `umur`, `alamat_domisili`, `pekerjaan`, `telepon`, `kontak_darurat`, `riwayat_pasien`, `permintaan_pemeriksaan`, `dokter_perujuk`, `asal_rujukan`, `nomor_rujukan`, `tanggal_rujukan`, `diagnosis_awal`, `rekomendasi_pemeriksaan`, `nomor_registrasi`, `created_at`) VALUES
('1', 'John Doe', '1234567890123456', 'L', 'Jakarta', '1990-01-15', '34', 'Jl. Contoh No. 456', 'Karyawan Swasta', '08123456789', 'Jane Doe - 08123456788', 'Tidak ada riwayat penyakit serius', 'Kimia Darah, Hematologi', 'Dr. Ahmad Wijaya, Sp.PD', 'RS Umum Daerah Jakarta', 'RUJ/2025/001234', '2025-08-27', 'Suspek Diabetes Mellitus', 'Gula darah puasa, Gula darah 2 jam PP, HbA1c', 'REG20250001', '2025-08-28 17:41:13'),
('2', 'Budi Santoso', '3175081234567890', 'L', 'Jakarta', '1985-03-15', '39', 'Jl. Mawar No. 123, Jakarta Selatan', 'Karyawan Swasta', '08123456789', 'Siti Santoso - 08123456788', 'Hipertensi ringan', 'Kimia Darah, Hematologi', 'Dr. Ahmad Rahman, Sp.PD', 'RS Umum Jakarta', 'RUJ/2025/001', '2025-09-06', 'Suspek Diabetes', 'Gula darah puasa, HbA1c', 'REG20250002', '2025-09-06 02:48:18'),
('3', 'Siti Rahayu', '3175082345678901', 'P', 'Bandung', '1990-07-22', '34', 'Jl. Melati No. 456, Bandung', 'Guru', '08234567890', 'Ahmad Rahayu - 08234567889', 'Diabetes tipe 2', 'Gula Darah, Urinologi', 'Dr. Siti Nurhaliza, Sp.PD', 'Klinik Sehat Bandung', 'RUJ/2025/002', '2025-09-06', 'Kontrol Diabetes', 'Gula darah, Urin lengkap', 'REG20250003', '2025-09-06 02:48:18'),
('4', 'Ahmad Wijaya', '3175083456789012', 'L', 'Surabaya', '1978-12-10', '46', 'Jl. Kenanga No. 789, Surabaya', 'Wiraswasta', '08345678901', 'Rina Wijaya - 08345678900', 'Kolesterol tinggi', 'Kimia Darah', 'Dr. Bambang Sutopo, Sp.JP', 'RS Jantung Surabaya', 'RUJ/2025/003', '2025-09-06', 'Dislipidemia', 'Profil lipid lengkap', 'REG20250004', '2025-09-06 02:48:18'),
('5', 'Rina Sari', '3175084567890123', 'P', 'Yogyakarta', '1995-05-18', '29', 'Jl. Anggrek No. 321, Yogyakarta', 'Dokter', '08456789012', 'Doni Sari - 08456789011', 'Sehat', 'Medical Check Up', 'Dr. Retno Wulan, Sp.OG', 'RS Ibu dan Anak Yogya', 'RUJ/2025/004', '2025-09-07', 'MCU Pranikah', 'Lab lengkap, TORCH', 'REG20250005', '2025-09-06 02:48:18'),
('6', 'Doni Pratama', '3175085678901234', 'L', 'Medan', '1987-09-25', '37', 'Jl. Cempaka No. 654, Medan', 'Insinyur', '08567890123', 'Maya Pratama - 08567890122', 'Asam urat tinggi', 'Kimia Darah, Hematologi', 'Dr. Indra Gunawan, Sp.PD', 'Klinik Pratama Medan', 'RUJ/2025/005', '2025-09-07', 'Hiperurisemia', 'Asam urat, fungsi ginjal', 'REG20250006', '2025-09-06 02:48:18'),
('7', 'Maya Indah', '3175086789012345', 'P', 'Makassar', '1992-02-14', '32', 'Jl. Sakura No. 987, Makassar', 'Perawat', '08678901234', 'Andi Indah - 08678901233', 'Anemia ringan', 'Hematologi, Urinologi', 'Dr. Andi Mappaware, Sp.PD', 'RS Wahidin Makassar', 'RUJ/2025/006', '2025-09-07', 'Anemia Defisiensi Besi', 'Hemoglobin, Ferritin', 'REG20250007', '2025-09-06 02:48:18'),
('8', 'Andi Susanto', '3175087890123456', 'L', 'Palembang', '1983-11-30', '41', 'Jl. Dahlia No. 147, Palembang', 'Manager', '08789012345', 'Linda Susanto - 08789012344', 'Hipertensi', 'Kimia Darah', 'Dr. Hasan Basri, Sp.PD', 'RS Mohammad Hoesin', 'RUJ/2025/007', '2025-09-07', 'Hipertensi Grade 2', 'Fungsi ginjal, elektrolit', 'REG20250008', '2025-09-06 02:48:18'),
('9', 'Linda Kartika', '3175088901234567', 'P', 'Semarang', '1988-08-07', '36', 'Jl. Tulip No. 258, Semarang', 'Akuntan', '08890123456', 'Rudi Kartika - 08890123455', 'Sehat', 'Serologi, TBC', 'Dr. Wahyu Indarto, Sp.P', 'RS Kariadi Semarang', 'RUJ/2025/008', '2025-09-07', 'Suspek TB Paru', 'Dahak BTA, TCM', 'REG20250009', '2025-09-06 02:48:18'),
('10', 'Rudi Hermawan', '3175089012345678', 'L', 'Denpasar', '1991-04-12', '33', 'Jl. Kamboja No. 369, Denpasar', 'Pilot', '08901234567', 'Dewi Hermawan - 08901234566', 'Sehat', 'Medical Check Up', 'Dr. Made Wirawan, Sp.KO', 'RS Sanglah Denpasar', 'RUJ/2025/009', '2025-09-07', 'MCU Profesi', 'Lab lengkap, EKG', 'REG20250010', '2025-09-06 02:48:18'),
('11', 'Dewi Lestari', '3175090123456789', 'P', 'Balikpapan', '1986-06-28', '38', 'Jl. Marigold No. 741, Balikpapan', 'Farmasis', '09012345678', 'Agus Lestari - 09012345677', 'Kolesterol tinggi', 'Kimia Darah, Hematologi', 'Dr. Yusuf Rahman, Sp.PD', 'RS Pertamina Balikpapan', 'RUJ/2025/010', '2025-09-07', 'Dislipidemia', 'Profil lipid, HbA1c', 'REG20250011', '2025-09-06 02:48:18');
UNLOCK TABLES;

-- Table structure for table `pemeriksaan_lab`
DROP TABLE IF EXISTS `pemeriksaan_lab`;
CREATE TABLE `pemeriksaan_lab` (
  `pemeriksaan_id` int(11) NOT NULL AUTO_INCREMENT,
  `pasien_id` int(11) NOT NULL,
  `petugas_id` int(11) DEFAULT NULL,
  `lab_id` int(11) DEFAULT NULL,
  `nomor_pemeriksaan` varchar(50) NOT NULL,
  `tanggal_pemeriksaan` date NOT NULL,
  `jenis_pemeriksaan` varchar(100) DEFAULT NULL,
  `status_pemeriksaan` enum('pending','progress','selesai','cancelled') DEFAULT 'pending',
  `keterangan` text DEFAULT NULL,
  `biaya` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`pemeriksaan_id`),
  UNIQUE KEY `nomor_pemeriksaan` (`nomor_pemeriksaan`),
  KEY `petugas_id` (`petugas_id`),
  KEY `lab_id` (`lab_id`),
  KEY `idx_pemeriksaan_pasien` (`pasien_id`),
  KEY `idx_pemeriksaan_tanggal` (`tanggal_pemeriksaan`),
  KEY `idx_pemeriksaan_status` (`status_pemeriksaan`),
  KEY `idx_pemeriksaan_updated` (`updated_at`),
  KEY `idx_pemeriksaan_completed` (`completed_at`),
  KEY `idx_pemeriksaan_started` (`started_at`),
  CONSTRAINT `pemeriksaan_lab_ibfk_1` FOREIGN KEY (`pasien_id`) REFERENCES `pasien` (`pasien_id`),
  CONSTRAINT `pemeriksaan_lab_ibfk_3` FOREIGN KEY (`petugas_id`) REFERENCES `petugas_lab` (`petugas_id`),
  CONSTRAINT `pemeriksaan_lab_ibfk_4` FOREIGN KEY (`lab_id`) REFERENCES `lab` (`lab_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `pemeriksaan_lab`
LOCK TABLES `pemeriksaan_lab` WRITE;
INSERT INTO `pemeriksaan_lab` (`pemeriksaan_id`, `pasien_id`, `petugas_id`, `lab_id`, `nomor_pemeriksaan`, `tanggal_pemeriksaan`, `jenis_pemeriksaan`, `status_pemeriksaan`, `keterangan`, `biaya`, `created_at`, `updated_at`, `completed_at`, `started_at`) VALUES
('1', '2', '1', '1', 'LAB20250001', '2025-09-07', 'Kimia Darah', 'progress', 'Permintaan cek gula darah dan HbA1c untuk kontrol diabetes', '200000.00', '2025-09-06 15:23:46', '2025-09-07 19:27:01', NULL, '2025-09-07 19:27:01'),
('2', '3', '1', '1', 'LAB20250002', '2025-09-07', 'Urinologi', 'progress', 'Pemeriksaan urin lengkap untuk evaluasi diabetes', '85000.00', '2025-09-06 15:23:46', '2025-09-06 20:45:39', NULL, '2025-09-06 20:45:39'),
('3', '4', '1', '1', 'LAB20250003', '2025-09-07', 'Kimia Darah', 'progress', 'Profil lipid lengkap untuk evaluasi dislipidemia', '150000.00', '2025-09-06 15:23:46', '2025-09-12 20:28:41', NULL, '2025-09-12 20:28:41'),
('4', '5', '1', '1', 'LAB20250004', '2025-09-07', 'Serologi', 'progress', 'Pemeriksaan TORCH untuk persiapan pranikah', '300000.00', '2025-09-06 15:23:46', '2025-09-07 19:27:25', NULL, '2025-09-07 19:27:25'),
('5', '6', NULL, '1', 'LAB20250005', '2025-09-07', 'Kimia Darah', 'pending', 'Pemeriksaan asam urat dan fungsi ginjal', '180000.00', '2025-09-06 15:23:46', NULL, NULL, NULL),
('6', '7', NULL, '1', 'LAB20250006', '2025-09-07', 'Hematologi', 'pending', 'Pemeriksaan hemoglobin dan ferritin untuk anemia', '120000.00', '2025-09-06 15:23:46', NULL, NULL, NULL),
('7', '8', NULL, '1', 'LAB20250007', '2025-09-07', 'Kimia Darah', 'pending', 'Fungsi ginjal dan elektrolit untuk hipertensi', '160000.00', '2025-09-06 15:23:46', NULL, NULL, NULL),
('8', '9', '1', '1', 'LAB20250008', '2025-09-07', 'TBC', 'cancelled', 'Karna ada ketidak sesuaian', '150000.00', '2025-09-06 15:23:46', '2025-09-06 15:30:16', NULL, '2025-09-06 15:25:15'),
('9', '10', '1', '1', 'LAB20250009', '2025-09-06', 'Kimia Darah', 'selesai', 'Sedang dianalisis - proses kimia darah', '200000.00', '2025-09-06 15:23:46', '2025-09-06 21:49:12', '2025-09-06 21:49:12', NULL),
('10', '11', '1', '1', 'LAB20250010', '2025-09-06', 'Hematologi', 'selesai', 'Sampel sedang diproses - hematologi rutin', '120000.00', '2025-09-06 15:23:46', '2025-09-07 19:47:55', '2025-09-07 19:47:55', '2025-09-06 08:00:00'),
('11', '1', '1', '1', 'LAB20250011', '2025-09-05', 'Urinologi', 'selesai', 'Pemeriksaan urin telah selesai', '85000.00', '2025-09-06 15:23:46', '2025-09-06 15:23:46', NULL, '2025-09-06 08:30:00');
UNLOCK TABLES;

-- Table structure for table `petugas_lab`
DROP TABLE IF EXISTS `petugas_lab`;
CREATE TABLE `petugas_lab` (
  `petugas_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `nama_petugas` varchar(100) NOT NULL,
  `jenis_keahlian` varchar(100) DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`petugas_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `petugas_lab_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `petugas_lab`
LOCK TABLES `petugas_lab` WRITE;
INSERT INTO `petugas_lab` (`petugas_id`, `user_id`, `nama_petugas`, `jenis_keahlian`, `telepon`, `alamat`, `created_at`) VALUES
('1', '4', 'Sari Wulandari', 'Analis Laboratorium', NULL, NULL, '2025-08-28 17:41:13');
UNLOCK TABLES;

-- Table structure for table `reagen`
DROP TABLE IF EXISTS `reagen`;
CREATE TABLE `reagen` (
  `reagen_id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_reagen` varchar(100) NOT NULL,
  `kode_unik` varchar(50) DEFAULT NULL,
  `jumlah_stok` int(11) NOT NULL DEFAULT 0,
  `satuan` varchar(20) DEFAULT NULL,
  `lokasi_penyimpanan` varchar(100) DEFAULT NULL,
  `tanggal_dipakai` date DEFAULT NULL,
  `expired_date` date DEFAULT NULL,
  `stok_minimal` int(11) DEFAULT 10,
  `status` enum('Tersedia','Hampir Habis','Dipesan','Kadaluarsa') DEFAULT 'Tersedia',
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`reagen_id`),
  UNIQUE KEY `kode_unik` (`kode_unik`),
  KEY `idx_reagen_status` (`status`),
  KEY `idx_reagen_expired` (`expired_date`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `reagen`
LOCK TABLES `reagen` WRITE;
INSERT INTO `reagen` (`reagen_id`, `nama_reagen`, `kode_unik`, `jumlah_stok`, `satuan`, `lokasi_penyimpanan`, `tanggal_dipakai`, `expired_date`, `stok_minimal`, `status`, `catatan`, `created_at`, `updated_at`) VALUES
('1', 'Glucose Reagent Kit', 'REA001', '100', 'test', 'Lemari A1', NULL, '2025-12-31', '20', 'Tersedia', NULL, '2025-08-28 17:41:13', '2025-08-28 17:41:13'),
('2', 'Cholesterol Test Kit', 'REA002', '50', 'test', 'Lemari A2', NULL, '2025-10-31', '15', 'Tersedia', NULL, '2025-08-28 17:41:13', '2025-08-28 17:41:13'),
('3', 'Hemoglobin Reagent', 'REA003', '75', 'ml', 'Lemari B1', NULL, '2025-11-30', '25', 'Tersedia', NULL, '2025-08-28 17:41:13', '2025-08-28 17:41:13');
UNLOCK TABLES;

-- Table structure for table `serologi_imunologi`
DROP TABLE IF EXISTS `serologi_imunologi`;
CREATE TABLE `serologi_imunologi` (
  `serologi_id` int(11) NOT NULL AUTO_INCREMENT,
  `pemeriksaan_id` int(11) NOT NULL,
  `rdt_antigen` enum('Positif','Negatif') DEFAULT NULL,
  `widal` text DEFAULT NULL,
  `hbsag` enum('Reaktif','Non-Reaktif') DEFAULT NULL,
  `ns1` enum('Positif','Negatif') DEFAULT NULL,
  `hiv` enum('Reaktif','Non-Reaktif') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`serologi_id`),
  KEY `pemeriksaan_id` (`pemeriksaan_id`),
  CONSTRAINT `serologi_imunologi_ibfk_1` FOREIGN KEY (`pemeriksaan_id`) REFERENCES `pemeriksaan_lab` (`pemeriksaan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `serologi_imunologi`
-- No data found for table `serologi_imunologi`

-- Table structure for table `tbc`
DROP TABLE IF EXISTS `tbc`;
CREATE TABLE `tbc` (
  `tbc_id` int(11) NOT NULL AUTO_INCREMENT,
  `pemeriksaan_id` int(11) NOT NULL,
  `dahak` enum('Negatif','Scanty','+1','+2','+3') DEFAULT NULL,
  `tcm` enum('Detected','Not Detected') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`tbc_id`),
  KEY `pemeriksaan_id` (`pemeriksaan_id`),
  CONSTRAINT `tbc_ibfk_1` FOREIGN KEY (`pemeriksaan_id`) REFERENCES `pemeriksaan_lab` (`pemeriksaan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `tbc`
-- No data found for table `tbc`

-- Table structure for table `timeline_progres`
DROP TABLE IF EXISTS `timeline_progres`;
CREATE TABLE `timeline_progres` (
  `timeline_id` int(11) NOT NULL AUTO_INCREMENT,
  `pemeriksaan_id` int(11) NOT NULL,
  `status` varchar(100) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `petugas_id` int(11) DEFAULT NULL,
  `tanggal_update` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`timeline_id`),
  KEY `pemeriksaan_id` (`pemeriksaan_id`),
  KEY `petugas_id` (`petugas_id`),
  CONSTRAINT `timeline_progres_ibfk_1` FOREIGN KEY (`pemeriksaan_id`) REFERENCES `pemeriksaan_lab` (`pemeriksaan_id`),
  CONSTRAINT `timeline_progres_ibfk_2` FOREIGN KEY (`petugas_id`) REFERENCES `petugas_lab` (`petugas_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `timeline_progres`
LOCK TABLES `timeline_progres` WRITE;
INSERT INTO `timeline_progres` (`timeline_id`, `pemeriksaan_id`, `status`, `keterangan`, `petugas_id`, `tanggal_update`) VALUES
('12', '8', 'Pemeriksaan Dibatalkan', 'Karna ada ketidak sesuaian', '1', '2025-09-06 10:30:16'),
('13', '9', 'Hasil Diinput', 'Hasil pemeriksaan telah diinput dan siap untuk divalidasi', '1', '2025-09-06 16:48:18'),
('14', '9', 'Hasil Divalidasi', 'Hasil pemeriksaan telah divalidasi dan siap diserahkan', '1', '2025-09-06 16:49:12'),
('15', '10', 'Hasil Diinput', 'Hasil pemeriksaan telah diinput dan siap untuk divalidasi', '1', '2025-09-07 14:47:30'),
('16', '10', 'Hasil Divalidasi', 'Hasil pemeriksaan telah divalidasi dan siap diserahkan', '1', '2025-09-07 14:47:55');
UNLOCK TABLES;

-- Table structure for table `urinologi`
DROP TABLE IF EXISTS `urinologi`;
CREATE TABLE `urinologi` (
  `urinologi_id` int(11) NOT NULL AUTO_INCREMENT,
  `pemeriksaan_id` int(11) NOT NULL,
  `makroskopis` text DEFAULT NULL,
  `mikroskopis` text DEFAULT NULL,
  `kimia_ph` decimal(3,1) DEFAULT NULL,
  `protein` text DEFAULT NULL,
  `tes_kehamilan` enum('Positif','Negatif') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`urinologi_id`),
  KEY `pemeriksaan_id` (`pemeriksaan_id`),
  CONSTRAINT `urinologi_ibfk_1` FOREIGN KEY (`pemeriksaan_id`) REFERENCES `pemeriksaan_lab` (`pemeriksaan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `urinologi`
-- No data found for table `urinologi`

-- Table structure for table `users`
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','administrasi','dokter','petugas_lab') NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  KEY `idx_users_username` (`username`),
  KEY `idx_users_role` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `users`
LOCK TABLES `users` WRITE;
INSERT INTO `users` (`user_id`, `username`, `password`, `role`, `is_active`, `created_at`, `updated_at`) VALUES
('1', 'superadmin', '0192023a7bbd73250516f069df18b500', 'admin', '1', '2025-08-28 17:41:13', '2025-08-28 17:41:13'),
('2', 'admin_front', '15ff3c0a0310a2e3de3e95c8aeb328d0', 'administrasi', '1', '2025-08-28 17:41:13', '2025-09-12 15:02:05'),
('3', 'dr_budi', 'cab2d8232139ee4f469a920732578f71', 'dokter', '0', '2025-08-28 17:41:13', '2025-08-29 14:05:04'),
('4', 'lab_sari', '081c49b8c66a69aad79f4bca8334e0ef', 'petugas_lab', '1', '2025-08-28 17:41:13', '2025-09-12 01:37:10');
UNLOCK TABLES;

-- Table structure for table `v_examination_stats`
DROP TABLE IF EXISTS `v_examination_stats`;
;

-- Dumping data for table `v_examination_stats`
LOCK TABLES `v_examination_stats` WRITE;
INSERT INTO `v_examination_stats` (`exam_date`, `status_pemeriksaan`, `count`, `avg_processing_hours`) VALUES
('2025-09-07', 'pending', '3', NULL),
('2025-09-07', 'progress', '4', NULL),
('2025-09-07', 'cancelled', '1', NULL),
('2025-09-06', 'selesai', '2', '35.0000'),
('2025-09-05', 'selesai', '1', NULL);
UNLOCK TABLES;

-- Table structure for table `v_inventory_status`
DROP TABLE IF EXISTS `v_inventory_status`;
;

-- Dumping data for table `v_inventory_status`
LOCK TABLES `v_inventory_status` WRITE;
INSERT INTO `v_inventory_status` (`tipe_inventory`, `item_id`, `nama_item`, `kode_unik`, `jumlah_stok`, `stok_minimal`, `status`, `expired_date`, `alert_level`) VALUES
('reagen', '1', 'Glucose Reagent Kit', 'REA001', '100', '20', 'Tersedia', '2025-12-31', 'OK'),
('reagen', '2', 'Cholesterol Test Kit', 'REA002', '50', '15', 'Tersedia', '2025-10-31', 'OK'),
('reagen', '3', 'Hemoglobin Reagent', 'REA003', '75', '25', 'Tersedia', '2025-11-30', 'OK'),
('alat', '1', 'Chemistry Analyzer', 'ALT001', NULL, NULL, 'Normal', '2025-09-01', 'Calibration Due'),
('alat', '2', 'Hematology Analyzer', 'ALT002', NULL, NULL, 'Normal', '2025-09-15', 'OK'),
('alat', '3', 'Mikroskop', 'ALT003', NULL, NULL, 'Normal', '2025-10-01', 'OK');
UNLOCK TABLES;

-- Table structure for table `v_invoice_cetak`
DROP TABLE IF EXISTS `v_invoice_cetak`;
;

-- Dumping data for table `v_invoice_cetak`
LOCK TABLES `v_invoice_cetak` WRITE;
INSERT INTO `v_invoice_cetak` (`nomor_invoice`, `tanggal_invoice`, `nama_pasien`, `nik`, `umur`, `alamat_domisili`, `telepon`, `nomor_pemeriksaan`, `jenis_pemeriksaan`, `tanggal_pemeriksaan`, `total_biaya`, `metode_pembayaran`, `status_pembayaran`, `tanggal_pembayaran`, `keterangan`) VALUES
('INV20250001', '2025-09-12', 'Dewi Lestari', '3175090123456789', '38', 'Jl. Marigold No. 741, Balikpapan', '09012345678', 'LAB20250010', 'Hematologi', '2025-09-06', '120000.00', 'Tunai', 'belum_bayar', NULL, 'Invoice untuk pemeriksaan hematologi');
UNLOCK TABLES;

-- Table structure for table `v_invoice_detail`
DROP TABLE IF EXISTS `v_invoice_detail`;
;

-- Dumping data for table `v_invoice_detail`
LOCK TABLES `v_invoice_detail` WRITE;
INSERT INTO `v_invoice_detail` (`invoice_id`, `nomor_invoice`, `tanggal_invoice`, `jenis_pembayaran`, `total_biaya`, `status_pembayaran`, `metode_pembayaran`, `nomor_kartu_bpjs`, `nomor_sep`, `nama_pasien`, `nik`, `nomor_pemeriksaan`, `jenis_pemeriksaan`) VALUES
('1', 'INV20250001', '2025-09-12', 'umum', '120000.00', 'belum_bayar', 'Tunai', NULL, NULL, 'Dewi Lestari', '3175090123456789', 'LAB20250010', 'Hematologi');
UNLOCK TABLES;

-- Table structure for table `v_pemeriksaan_detail`
DROP TABLE IF EXISTS `v_pemeriksaan_detail`;
;

-- Dumping data for table `v_pemeriksaan_detail`
LOCK TABLES `v_pemeriksaan_detail` WRITE;
INSERT INTO `v_pemeriksaan_detail` (`pemeriksaan_id`, `nomor_pemeriksaan`, `tanggal_pemeriksaan`, `jenis_pemeriksaan`, `status_pemeriksaan`, `biaya`, `nama_pasien`, `nik`, `riwayat_pasien`, `dokter_perujuk`, `asal_rujukan`, `nomor_rujukan`, `tanggal_rujukan`, `diagnosis_awal`, `rekomendasi_pemeriksaan`, `nama_petugas`, `nama_lab`) VALUES
('1', 'LAB20250001', '2025-09-07', 'Kimia Darah', 'progress', '200000.00', 'Budi Santoso', '3175081234567890', 'Hipertensi ringan', 'Dr. Ahmad Rahman, Sp.PD', 'RS Umum Jakarta', 'RUJ/2025/001', '2025-09-06', 'Suspek Diabetes', 'Gula darah puasa, HbA1c', 'Sari Wulandari', 'Laboratorium Labsy'),
('2', 'LAB20250002', '2025-09-07', 'Urinologi', 'progress', '85000.00', 'Siti Rahayu', '3175082345678901', 'Diabetes tipe 2', 'Dr. Siti Nurhaliza, Sp.PD', 'Klinik Sehat Bandung', 'RUJ/2025/002', '2025-09-06', 'Kontrol Diabetes', 'Gula darah, Urin lengkap', 'Sari Wulandari', 'Laboratorium Labsy'),
('3', 'LAB20250003', '2025-09-07', 'Kimia Darah', 'progress', '150000.00', 'Ahmad Wijaya', '3175083456789012', 'Kolesterol tinggi', 'Dr. Bambang Sutopo, Sp.JP', 'RS Jantung Surabaya', 'RUJ/2025/003', '2025-09-06', 'Dislipidemia', 'Profil lipid lengkap', 'Sari Wulandari', 'Laboratorium Labsy'),
('4', 'LAB20250004', '2025-09-07', 'Serologi', 'progress', '300000.00', 'Rina Sari', '3175084567890123', 'Sehat', 'Dr. Retno Wulan, Sp.OG', 'RS Ibu dan Anak Yogya', 'RUJ/2025/004', '2025-09-07', 'MCU Pranikah', 'Lab lengkap, TORCH', 'Sari Wulandari', 'Laboratorium Labsy'),
('5', 'LAB20250005', '2025-09-07', 'Kimia Darah', 'pending', '180000.00', 'Doni Pratama', '3175085678901234', 'Asam urat tinggi', 'Dr. Indra Gunawan, Sp.PD', 'Klinik Pratama Medan', 'RUJ/2025/005', '2025-09-07', 'Hiperurisemia', 'Asam urat, fungsi ginjal', NULL, 'Laboratorium Labsy'),
('6', 'LAB20250006', '2025-09-07', 'Hematologi', 'pending', '120000.00', 'Maya Indah', '3175086789012345', 'Anemia ringan', 'Dr. Andi Mappaware, Sp.PD', 'RS Wahidin Makassar', 'RUJ/2025/006', '2025-09-07', 'Anemia Defisiensi Besi', 'Hemoglobin, Ferritin', NULL, 'Laboratorium Labsy'),
('7', 'LAB20250007', '2025-09-07', 'Kimia Darah', 'pending', '160000.00', 'Andi Susanto', '3175087890123456', 'Hipertensi', 'Dr. Hasan Basri, Sp.PD', 'RS Mohammad Hoesin', 'RUJ/2025/007', '2025-09-07', 'Hipertensi Grade 2', 'Fungsi ginjal, elektrolit', NULL, 'Laboratorium Labsy'),
('8', 'LAB20250008', '2025-09-07', 'TBC', 'cancelled', '150000.00', 'Linda Kartika', '3175088901234567', 'Sehat', 'Dr. Wahyu Indarto, Sp.P', 'RS Kariadi Semarang', 'RUJ/2025/008', '2025-09-07', 'Suspek TB Paru', 'Dahak BTA, TCM', 'Sari Wulandari', 'Laboratorium Labsy'),
('9', 'LAB20250009', '2025-09-06', 'Kimia Darah', 'selesai', '200000.00', 'Rudi Hermawan', '3175089012345678', 'Sehat', 'Dr. Made Wirawan, Sp.KO', 'RS Sanglah Denpasar', 'RUJ/2025/009', '2025-09-07', 'MCU Profesi', 'Lab lengkap, EKG', 'Sari Wulandari', 'Laboratorium Labsy'),
('10', 'LAB20250010', '2025-09-06', 'Hematologi', 'selesai', '120000.00', 'Dewi Lestari', '3175090123456789', 'Kolesterol tinggi', 'Dr. Yusuf Rahman, Sp.PD', 'RS Pertamina Balikpapan', 'RUJ/2025/010', '2025-09-07', 'Dislipidemia', 'Profil lipid, HbA1c', 'Sari Wulandari', 'Laboratorium Labsy'),
('11', 'LAB20250011', '2025-09-05', 'Urinologi', 'selesai', '85000.00', 'John Doe', '1234567890123456', 'Tidak ada riwayat penyakit serius', 'Dr. Ahmad Wijaya, Sp.PD', 'RS Umum Daerah Jakarta', 'RUJ/2025/001234', '2025-08-27', 'Suspek Diabetes Mellitus', 'Gula darah puasa, Gula darah 2 jam PP, HbA1c', 'Sari Wulandari', 'Laboratorium Labsy');
UNLOCK TABLES;

-- Table structure for table `v_user_details`
DROP TABLE IF EXISTS `v_user_details`;
;

-- Dumping data for table `v_user_details`
LOCK TABLES `v_user_details` WRITE;
INSERT INTO `v_user_details` (`user_id`, `username`, `role`, `is_active`, `created_at`, `nama_lengkap`) VALUES
('4', 'lab_sari', 'petugas_lab', '1', '2025-08-28 17:41:13', 'Sari Wulandari'),
('2', 'admin_front', 'administrasi', '1', '2025-08-28 17:41:13', 'Admin Front Office'),
('1', 'superadmin', 'admin', '1', '2025-08-28 17:41:13', 'Super Administrator'),
('3', 'dr_budi', 'dokter', '0', '2025-08-28 17:41:13', NULL);
UNLOCK TABLES;

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;
