-- Database backup generated on 2025-09-16 13:39:34
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
) ENGINE=InnoDB AUTO_INCREMENT=981 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
('387', '1', 'Database backup dibuat: backup_2025-09-12T15-48-41', 'system', NULL, '::1', '2025-09-12 17:48:42'),
('388', '1', 'Database backup dibuat: backup_2025-09-12T15-48-44', 'system', NULL, '::1', '2025-09-12 17:48:44'),
('389', '1', 'Database backup dibuat: backup_2025-09-12T15-48-45', 'system', NULL, '::1', '2025-09-12 17:48:45'),
('390', '1', 'File backup didownload: Database.zip', 'system', NULL, '::1', '2025-09-12 17:49:06'),
('391', '1', 'File backup didownload: backup_2025-09-12T15-39-41.zip', 'system', NULL, '::1', '2025-09-12 17:49:15'),
('392', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-12 17:51:37'),
('393', '1', 'Mengakses halaman backup database', 'system', NULL, '::1', '2025-09-12 17:51:40'),
('394', '1', 'Mengakses halaman backup database', 'system', NULL, '::1', '2025-09-12 17:52:10'),
('395', '1', 'Mengakses halaman backup database', 'system', NULL, '::1', '2025-09-12 17:52:15'),
('396', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 17:52:16'),
('397', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 17:52:17'),
('398', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 17:52:24'),
('399', '1', 'Mengakses halaman backup database', 'system', NULL, '::1', '2025-09-12 17:52:32'),
('400', '1', 'Mengakses halaman data master', 'system', NULL, '::1', '2025-09-12 17:52:33'),
('401', '1', 'Mengakses halaman data master', 'system', NULL, '::1', '2025-09-12 17:52:42'),
('402', '4', 'User logged in', 'users', NULL, '::1', '2025-09-12 17:57:59'),
('403', '1', 'Mengakses halaman data operasional', 'system', NULL, '::1', '2025-09-12 18:03:06'),
('404', '1', 'Mengakses halaman data operasional', 'system', NULL, '::1', '2025-09-12 18:03:31'),
('405', '1', 'Mengakses halaman data operasional', 'system', NULL, '::1', '2025-09-12 18:03:32'),
('406', '1', 'Mengakses halaman data operasional', 'system', NULL, '::1', '2025-09-12 18:03:42'),
('407', '1', 'Mengakses halaman data operasional', 'system', NULL, '::1', '2025-09-12 18:03:43'),
('408', '1', 'Mengakses halaman data operasional', 'system', NULL, '::1', '2025-09-12 18:03:43'),
('409', '1', 'Mengakses halaman data operasional', 'system', NULL, '::1', '2025-09-12 18:03:43'),
('410', '1', 'Mengakses halaman data operasional', 'system', NULL, '::1', '2025-09-12 18:03:54'),
('411', '1', 'Mengakses halaman data master', 'system', NULL, '::1', '2025-09-12 18:04:20'),
('412', '1', 'Mengakses halaman data operasional', 'system', NULL, '::1', '2025-09-12 18:04:24'),
('413', '1', 'Mengakses halaman data master', 'system', NULL, '::1', '2025-09-12 18:06:00'),
('414', '1', 'Mengakses halaman data master', 'system', NULL, '::1', '2025-09-12 18:06:05'),
('415', '1', 'Mengakses halaman data master', 'system', NULL, '::1', '2025-09-12 18:16:48'),
('416', '1', 'Mengakses halaman data master', 'system', NULL, '::1', '2025-09-12 18:27:01'),
('417', '1', 'Mengakses halaman data master', 'system', NULL, '::1', '2025-09-12 18:28:46'),
('418', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 18:31:35'),
('419', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 18:31:36'),
('420', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 18:31:37'),
('421', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 18:31:39'),
('422', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 18:31:43'),
('423', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 18:31:44'),
('424', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 18:31:46'),
('425', '1', 'Mengakses halaman data master', 'system', NULL, '::1', '2025-09-12 18:37:04'),
('426', '1', 'Mengakses halaman backup database', 'system', NULL, '::1', '2025-09-12 18:37:06'),
('427', '1', 'Mengakses halaman backup database', 'system', NULL, '::1', '2025-09-12 18:37:08'),
('428', '1', 'Mengakses halaman data master', 'system', NULL, '::1', '2025-09-12 18:37:10'),
('429', '1', 'Data master ditambahkan - lab_info', 'lab', '2', '::1', '2025-09-12 18:38:25'),
('430', '1', 'Mengakses halaman data master', 'system', NULL, '::1', '2025-09-12 18:39:04'),
('431', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 18:47:02'),
('432', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 18:47:08'),
('433', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 18:47:09'),
('434', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 18:47:11'),
('435', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 18:47:13'),
('436', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 18:47:33'),
('437', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 18:48:00'),
('438', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 18:48:03'),
('439', '1', 'Invoice dicetak: INV20250001', 'invoice', '1', '::1', '2025-09-12 18:48:06'),
('440', '1', 'Mengakses halaman data master', 'system', NULL, '::1', '2025-09-12 18:48:57'),
('441', '1', 'Mengakses halaman data master', 'system', NULL, '::1', '2025-09-12 18:49:03'),
('442', '1', 'Mengakses halaman data master', 'system', NULL, '::1', '2025-09-12 18:49:17'),
('443', '1', 'Mengakses halaman backup database', 'system', NULL, '::1', '2025-09-12 18:49:19'),
('444', '1', 'Mengakses halaman data master', 'system', NULL, '::1', '2025-09-12 18:49:22'),
('445', '1', 'Mengakses halaman data master', 'system', NULL, '::1', '2025-09-12 18:50:45'),
('446', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 18:50:51'),
('447', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 18:50:52'),
('448', '1', 'Mengakses halaman backup database', 'system', NULL, '::1', '2025-09-12 18:51:05'),
('449', '1', 'Mengakses halaman data master', 'system', NULL, '::1', '2025-09-12 18:51:07'),
('450', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 18:51:33'),
('451', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 18:51:35'),
('452', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 18:51:35'),
('453', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 18:51:38'),
('454', '1', 'Mengakses halaman data master', 'system', NULL, '::1', '2025-09-12 18:51:42'),
('455', '1', 'Mengakses halaman data master', 'system', NULL, '::1', '2025-09-12 18:52:25'),
('456', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 19:00:32'),
('457', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 19:00:34'),
('458', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 19:00:35'),
('459', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 19:00:36'),
('460', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 19:00:36'),
('461', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 19:00:38'),
('462', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 19:00:42'),
('463', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 19:00:43'),
('464', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 19:00:44'),
('465', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 19:00:44'),
('466', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 19:00:45'),
('467', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 19:00:46'),
('468', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 19:00:46'),
('469', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 19:00:46'),
('470', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 19:00:47'),
('471', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 19:00:48'),
('472', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 19:00:57'),
('473', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 19:01:31'),
('474', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 19:01:34'),
('475', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 19:01:36'),
('476', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 19:01:37'),
('477', '1', 'Invoice dicetak: INV20250001', 'invoice', '1', '::1', '2025-09-12 19:01:39'),
('478', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 19:01:43'),
('479', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 19:01:49'),
('480', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 19:16:09'),
('481', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-12 19:16:13'),
('482', '1', 'Hasil pemeriksaan dicetak: LAB20250009', 'pemeriksaan_lab', '9', '::1', '2025-09-12 19:16:48'),
('483', '1', 'Invoice dicetak: INV20250001', 'invoice', '1', '::1', '2025-09-12 19:17:32'),
('484', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 19:18:26'),
('485', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 19:18:37'),
('486', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 19:18:39'),
('487', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 19:59:32'),
('488', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-12 19:59:34'),
('489', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 19:59:38'),
('490', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 19:59:40'),
('491', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 20:00:09'),
('492', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 20:00:11'),
('493', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 20:00:14'),
('494', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 20:00:23'),
('495', '1', 'Pengguna dinonaktifkan: lab_sari', 'users', '4', '::1', '2025-09-12 20:00:27'),
('496', '1', 'Pengguna diaktifkan: lab_sari', 'users', '4', '::1', '2025-09-12 20:00:30'),
('497', '1', 'Data pengguna diperbarui: lab_sari', 'users', '4', '::1', '2025-09-12 20:00:40'),
('498', '1', 'Pengguna baru ditambahkan: Firdaus', 'users', '7', '::1', '2025-09-12 20:01:18'),
('499', '7', 'User logged in', 'users', NULL, '::1', '2025-09-12 20:01:41'),
('500', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 20:01:49'),
('501', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 20:02:06'),
('502', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 20:02:13'),
('503', '1', 'Invoice dicetak: INV20250001', 'invoice', '1', '::1', '2025-09-12 20:02:15'),
('504', '1', 'Mengakses halaman backup database', 'system', NULL, '::1', '2025-09-12 20:02:29'),
('505', '1', 'Mengakses halaman data master', 'system', NULL, '::1', '2025-09-12 20:02:31'),
('506', '1', 'Mengakses halaman backup database', 'system', NULL, '::1', '2025-09-12 20:02:34'),
('507', '1', 'Database backup dibuat: backup_2025-09-12T18-03-23', 'system', NULL, '::1', '2025-09-12 20:03:23'),
('508', '1', 'Database backup dibuat: database', 'system', NULL, '::1', '2025-09-12 20:03:49'),
('509', '1', 'Mengakses halaman data master', 'system', NULL, '::1', '2025-09-12 20:04:11'),
('510', '1', 'Mengakses halaman backup database', 'system', NULL, '::1', '2025-09-12 20:04:45'),
('511', '1', 'Mengakses halaman backup database', 'system', NULL, '::1', '2025-09-12 20:04:46'),
('512', '1', 'Mengakses halaman backup database', 'system', NULL, '::1', '2025-09-12 20:05:13'),
('513', '1', 'Mengakses halaman backup database', 'system', NULL, '::1', '2025-09-12 20:06:55'),
('514', '1', 'Mengakses halaman backup database', 'system', NULL, '::1', '2025-09-12 20:07:00'),
('515', '1', 'Database backup dibuat: Database', 'system', NULL, '::1', '2025-09-12 20:07:15'),
('516', '1', 'File backup didownload: backup_2025-09-12T15-48-45.sql', 'system', NULL, '::1', '2025-09-12 20:07:25'),
('517', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 20:07:39'),
('518', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 20:07:41'),
('519', '1', 'Mengakses halaman backup database', 'system', NULL, '::1', '2025-09-12 20:13:57'),
('520', '1', 'Mengakses halaman backup database', 'system', NULL, '::1', '2025-09-12 20:17:46'),
('521', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 20:17:48'),
('522', '1', 'Invoice dicetak: INV-2025-0002', 'invoice', '2', '::1', '2025-09-12 20:17:51'),
('523', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 20:17:54'),
('524', '1', 'Mengakses halaman backup database', 'system', NULL, '::1', '2025-09-12 20:18:05'),
('525', '1', 'Database backup dibuat: backup_2025-09-12T18-18-10', 'system', NULL, '::1', '2025-09-12 20:18:10'),
('526', '1', 'Mengakses halaman backup database', 'system', NULL, '::1', '2025-09-12 20:18:15'),
('527', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 20:32:58'),
('528', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 20:33:00'),
('529', '1', 'Invoice dicetak: INV20250001', 'invoice', '1', '::1', '2025-09-12 20:33:03'),
('530', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 20:33:44'),
('531', '1', 'Pengguna dinonaktifkan: Firdaus', 'users', '7', '::1', '2025-09-12 20:33:52'),
('532', '1', 'Data pengguna diperbarui: Firdaus', 'users', '7', '::1', '2025-09-12 20:33:57'),
('533', '1', 'Pengguna diaktifkan: Firdaus7', 'users', '7', '::1', '2025-09-12 20:34:02'),
('534', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 20:34:09'),
('535', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 20:34:12'),
('538', '1', 'Log aktivitas dihapus', 'activity_log', '536', '::1', '2025-09-12 20:34:29'),
('540', '1', 'Log aktivitas dihapus', 'activity_log', '537', '::1', '2025-09-12 20:34:49'),
('541', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 20:34:49'),
('542', '1', 'Log aktivitas dihapus', 'activity_log', '539', '::1', '2025-09-12 20:35:07'),
('543', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 20:35:07'),
('544', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 20:35:17'),
('545', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 20:35:39'),
('546', '1', 'Mengakses halaman backup database', 'system', NULL, '::1', '2025-09-12 20:35:41'),
('547', '1', 'Database backup dibuat: backup_2025-09-12T18-35-45', 'system', NULL, '::1', '2025-09-12 20:35:45'),
('548', '1', 'Mengakses halaman backup database', 'system', NULL, '::1', '2025-09-12 20:35:52'),
('549', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 20:36:26'),
('550', '1', 'Hasil pemeriksaan dicetak: LAB20250011', 'pemeriksaan_lab', '11', '::1', '2025-09-12 20:36:30'),
('551', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 20:41:45'),
('552', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 20:41:48'),
('553', '1', 'Mengakses halaman backup database', 'system', NULL, '::1', '2025-09-12 20:41:56'),
('554', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 20:42:00'),
('555', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 20:42:02'),
('556', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 20:42:04'),
('557', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 20:42:21'),
('558', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 20:45:47'),
('559', '1', 'Invoice dicetak: INV-2025-0002', 'invoice', '2', '::1', '2025-09-12 20:45:58'),
('560', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 20:46:48'),
('561', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 20:46:52'),
('562', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 20:47:02'),
('563', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 20:47:15'),
('564', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 20:47:19'),
('565', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 20:59:06'),
('566', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 20:59:08'),
('567', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 21:05:53'),
('568', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 21:06:32'),
('569', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 21:16:36'),
('570', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 21:17:17'),
('571', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 21:20:40'),
('572', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 21:24:17'),
('573', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 21:24:19'),
('574', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 21:25:11'),
('575', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 21:30:58'),
('576', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 21:31:02'),
('577', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 21:31:04'),
('578', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 21:31:06'),
('579', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 21:31:08'),
('580', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 21:31:12'),
('581', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 21:31:16'),
('582', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 21:31:20'),
('583', '1', 'Pengguna dinonaktifkan: Firdaus7', 'users', '7', '::1', '2025-09-12 21:36:15'),
('584', '1', 'Data pasien diperbarui: Ahmad Wijaya', 'pasien', '4', '::1', '2025-09-12 21:49:55'),
('585', '1', 'Data pasien diperbarui: Ahmad Wijaya yanto', 'pasien', '4', '::1', '2025-09-12 21:50:15'),
('586', '1', 'Data pasien diperbarui: Ahmad Wijaya yanto', 'pasien', '4', '::1', '2025-09-12 21:50:15'),
('587', '1', 'Data pasien diperbarui: Ahmad Wijaya yanto', 'pasien', '4', '::1', '2025-09-12 21:50:39'),
('588', '1', 'Data pasien diperbarui: Ahmad Wijaya ', 'pasien', '4', '::1', '2025-09-12 21:50:39'),
('589', '1', 'Data pasien diperbarui: Ahmad Wijaya ', 'pasien', '4', '::1', '2025-09-12 21:50:39'),
('590', '1', 'Data pasien diperbarui: Ahmad Wijaya ', 'pasien', '4', '::1', '2025-09-12 21:50:50'),
('591', '1', 'Data pasien diperbarui: Ahmad Wijaya ', 'pasien', '4', '::1', '2025-09-12 21:50:50'),
('592', '1', 'Data pasien diperbarui: Ahmad Wijaya ', 'pasien', '4', '::1', '2025-09-12 21:50:50'),
('593', '1', 'Data pasien diperbarui: Ahmad Wijaya ', 'pasien', '4', '::1', '2025-09-12 21:50:50'),
('594', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-12 21:53:54'),
('595', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-12 21:53:55'),
('596', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-12 21:53:59'),
('597', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-12 21:54:01'),
('598', '1', 'Mengakses halaman backup database', 'system', NULL, '::1', '2025-09-12 21:54:03'),
('599', '1', 'Pengguna diaktifkan: Firdaus7', 'users', '7', '::1', '2025-09-12 21:54:11'),
('600', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-13 19:52:19'),
('601', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-14 13:15:16'),
('602', '1', 'User logged out', 'users', NULL, '::1', '2025-09-14 13:26:24'),
('603', '1', 'User logged in', 'users', NULL, '::1', '2025-09-14 13:27:17'),
('604', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-14 13:27:17'),
('605', '1', 'Data pengguna diperbarui: Firdaus7', 'users', '7', '::1', '2025-09-14 13:28:21'),
('606', '1', 'Data pengguna diperbarui: Firdaus', 'users', '7', '::1', '2025-09-14 13:28:21'),
('607', '7', 'User logged in', 'users', NULL, '::1', '2025-09-14 13:29:19'),
('608', '1', 'User logged in', 'users', NULL, '::1', '2025-09-14 16:14:26'),
('609', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-14 16:14:27'),
('610', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-14 16:14:41'),
('611', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-14 16:14:54'),
('612', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-14 16:14:57'),
('613', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-14 16:15:04'),
('614', '1', 'Mengakses halaman backup database', 'system', NULL, '::1', '2025-09-14 16:15:21'),
('615', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-14 16:15:30'),
('616', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-14 16:15:34'),
('617', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-14 16:18:44'),
('618', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-14 16:18:51'),
('619', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-14 16:18:53'),
('620', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-14 16:18:56'),
('621', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-14 16:21:29'),
('622', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-14 16:25:34'),
('623', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-14 16:25:38'),
('624', '1', 'Mengakses halaman backup database', 'system', NULL, '::1', '2025-09-14 16:25:39'),
('625', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-14 16:28:48'),
('626', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-14 16:28:51'),
('627', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-14 16:33:52'),
('628', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-14 16:33:55'),
('629', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-14 16:39:29'),
('630', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-14 16:41:19'),
('631', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-14 16:45:45'),
('632', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-14 16:46:50'),
('633', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-14 16:46:53'),
('634', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-14 16:55:58'),
('635', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-14 16:59:22'),
('636', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-14 17:01:08'),
('637', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-14 17:01:12'),
('638', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-14 17:05:14'),
('639', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-14 17:05:36'),
('640', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-14 17:05:53'),
('641', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-14 17:08:51'),
('642', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-14 17:09:10'),
('643', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-14 17:09:24'),
('644', '1', 'Mengakses halaman backup database', 'system', NULL, '::1', '2025-09-14 17:09:54'),
('645', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-14 17:10:07'),
('646', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-14 17:10:49'),
('647', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-14 17:11:13'),
('648', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-14 17:11:44'),
('649', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-14 17:12:01'),
('650', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-14 17:12:03'),
('651', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-14 17:13:13'),
('652', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-14 17:13:49'),
('653', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-14 17:14:12'),
('654', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-14 17:14:20'),
('655', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-14 17:14:27'),
('656', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-14 17:14:32'),
('657', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-14 17:17:52'),
('658', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-14 17:18:03'),
('659', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-14 17:20:15'),
('660', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-14 17:20:25'),
('661', '1', 'Pengguna dinonaktifkan: Firdaus', 'users', '7', '::1', '2025-09-14 17:27:55'),
('662', '1', 'Pengguna dinonaktifkan: lab_sari', 'users', '4', '::1', '2025-09-14 17:28:04'),
('663', '1', 'Pengguna diaktifkan: lab_sari', 'users', '4', '::1', '2025-09-14 17:28:09'),
('664', '1', 'Pengguna diaktifkan: Firdaus', 'users', '7', '::1', '2025-09-14 17:28:11'),
('665', '1', 'Mengakses halaman backup database', 'system', NULL, '::1', '2025-09-14 17:44:49'),
('666', '1', 'Mengakses halaman backup database', 'system', NULL, '::1', '2025-09-14 17:44:53'),
('667', '1', 'Data pasien diperbarui: Rina Sari', 'pasien', '5', '::1', '2025-09-14 17:47:55'),
('668', '1', 'Data pasien diperbarui: Rina Saris', 'pasien', '5', '::1', '2025-09-14 17:48:36'),
('669', '1', 'Data pasien diperbarui: Rina Sari', 'pasien', '5', '::1', '2025-09-14 17:48:36'),
('670', '1', 'Data pasien diperbarui: Rina Sari', 'pasien', '5', '::1', '2025-09-14 17:48:36'),
('671', '1', 'Data pasien diperbarui: Siti Rahayu', 'pasien', '3', '::1', '2025-09-14 20:04:40'),
('672', '1', 'Data pasien diperbarui: Siti Rahayup', 'pasien', '3', '::1', '2025-09-14 20:04:40'),
('673', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-14 20:05:06'),
('674', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-14 20:05:17'),
('675', '1', 'Pengguna baru ditambahkan: Aulia', 'users', '8', '::1', '2025-09-14 20:06:44'),
('676', '1', 'Data pengguna diperbarui: Aulia', 'users', '8', '::1', '2025-09-14 20:06:54'),
('678', '1', 'Pengguna dinonaktifkan: Aulia', 'users', '8', '::1', '2025-09-14 20:11:43'),
('679', '1', 'Pengguna diaktifkan: Aulia', 'users', '8', '::1', '2025-09-14 20:11:46'),
('680', '1', 'Data pasien diekspor ke Excel: Data_Pasien_2025-09-14_20-16-23.xlsx', 'pasien', NULL, '::1', '2025-09-14 20:16:23'),
('681', '1', 'Data pasien diekspor ke Excel: Data_Pasien_2025-09-14_20-18-40.xlsx', 'pasien', NULL, '::1', '2025-09-14 20:18:40'),
('682', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-14 20:33:44'),
('683', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-14 20:34:36'),
('684', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-14 20:34:37'),
('685', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-14 20:35:53'),
('686', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-14 20:36:47'),
('687', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-14 20:36:49'),
('688', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-14 20:36:50'),
('689', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-14 20:43:22'),
('690', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-14 20:46:31'),
('691', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-14 20:47:32'),
('692', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-14 20:49:05'),
('693', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-14 20:49:32'),
('694', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-14 20:49:48'),
('695', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-14 20:53:05'),
('696', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-14 20:53:24'),
('697', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-14 20:55:05'),
('698', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-14 20:56:31'),
('699', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-14 20:57:02'),
('700', '1', 'Item inventory dihapus: Hemoglobin Reagent', 'reagen', '3', '::1', '2025-09-14 20:57:57'),
('701', '7', 'User logged in', 'users', NULL, '::1', '2025-09-14 21:00:54'),
('702', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-14 21:09:52'),
('703', '1', 'Item inventory baru ditambahkan: Taktau', 'alat_laboratorium', '9', '::1', '2025-09-14 21:10:44'),
('704', '1', 'Item inventory diperbarui: Taktau', 'alat_laboratorium', '9', '::1', '2025-09-14 21:11:11'),
('705', '1', 'Item inventory dihapus: Taktau', 'alat_laboratorium', '9', '::1', '2025-09-14 21:11:26'),
('706', '7', 'User logged out', 'users', NULL, '::1', '2025-09-14 21:12:16'),
('707', '4', 'User logged in', 'users', NULL, '::1', '2025-09-14 21:12:24'),
('708', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-14 21:14:04'),
('709', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-14 21:17:25'),
('710', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-14 21:19:22'),
('711', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-14 21:30:31'),
('712', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-14 21:31:49'),
('713', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-14 21:38:20'),
('714', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-14 21:39:49'),
('715', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-14 21:40:04'),
('716', '1', 'User logged in', 'users', NULL, '::1', '2025-09-15 05:56:38'),
('717', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-15 05:56:38'),
('718', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-15 05:56:50'),
('719', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-15 05:57:17'),
('720', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-15 05:57:20'),
('721', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-15 06:02:34'),
('722', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-15 06:02:57'),
('723', '1', 'User logged out', 'users', NULL, '::1', '2025-09-15 06:07:05'),
('724', '4', 'User logged in', 'users', NULL, '::1', '2025-09-15 06:07:17'),
('725', '4', 'User logged out', 'users', NULL, '::1', '2025-09-15 06:08:57'),
('726', '1', 'User logged in', 'users', NULL, '::1', '2025-09-15 06:09:12'),
('727', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-15 06:09:12'),
('728', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-15 06:16:58'),
('729', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-15 06:22:29'),
('730', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-15 06:22:37'),
('731', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-15 06:26:03'),
('732', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-15 06:31:34'),
('733', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-15 06:31:50'),
('734', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-15 06:48:35'),
('735', '1', 'Pengguna dinonaktifkan: Aulia', 'users', '8', '::1', '2025-09-15 06:49:05'),
('736', '1', 'Pengguna diaktifkan: Aulia', 'users', '8', '::1', '2025-09-15 06:49:08'),
('737', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-15 06:49:11'),
('738', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-15 06:53:56'),
('739', '1', 'Pengguna dinonaktifkan: Aulia', 'users', '8', '::1', '2025-09-15 06:54:06'),
('740', '1', 'Pengguna diaktifkan: Aulia', 'users', '8', '::1', '2025-09-15 06:54:08'),
('741', '1', 'Pengguna dinonaktifkan: Firdaus', 'users', '7', '::1', '2025-09-15 06:54:10'),
('742', '1', 'Pengguna diaktifkan: Firdaus', 'users', '7', '::1', '2025-09-15 06:54:12'),
('743', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-15 06:54:13'),
('744', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-15 06:54:17'),
('745', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-15 06:54:22'),
('746', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-15 06:54:26'),
('747', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-15 06:54:29'),
('748', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-15 06:54:37'),
('749', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-15 07:01:38'),
('750', '1', 'Pengguna dinonaktifkan: Aulia', 'users', '8', '::1', '2025-09-15 07:01:45'),
('751', '1', 'Pengguna diaktifkan: Aulia', 'users', '8', '::1', '2025-09-15 07:01:48'),
('752', '1', 'Pengguna dihapus: Aulia', 'users', '8', '::1', '2025-09-15 07:02:45'),
('753', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-15 07:05:17'),
('754', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-15 07:05:24'),
('755', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-15 07:05:41'),
('756', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-15 07:05:51'),
('757', '1', 'Pengguna dinonaktifkan: Firdaus', 'users', '7', '::1', '2025-09-15 07:05:57'),
('758', '1', 'Pengguna diaktifkan: Firdaus', 'users', '7', '::1', '2025-09-15 07:05:59'),
('759', '1', 'Data pengguna diperbarui: Firdaus', 'users', '7', '::1', '2025-09-15 07:06:10'),
('760', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-15 07:06:15'),
('761', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-15 07:17:18'),
('762', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-15 07:18:23'),
('763', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-15 07:18:30'),
('764', '1', 'Data pengguna diperbarui: Firdaus', 'users', '7', '::1', '2025-09-15 07:28:19'),
('765', '1', 'User logged out', 'users', NULL, '::1', '2025-09-15 07:28:22'),
('766', '7', 'User logged in', 'users', NULL, '::1', '2025-09-15 07:28:33'),
('767', '7', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-15 07:28:37'),
('768', '7', 'Hasil pemeriksaan dicetak: LAB20250009', 'pemeriksaan_lab', '9', '::1', '2025-09-15 07:28:49'),
('769', '7', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-15 07:28:58'),
('770', '7', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-15 07:29:40'),
('771', '7', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-15 07:30:02'),
('772', '7', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-15 07:30:29'),
('773', '7', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-15 07:30:37'),
('774', '7', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-15 07:32:25'),
('775', '7', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-15 07:32:29'),
('776', '7', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-15 07:33:37'),
('777', '7', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-15 07:33:40'),
('778', '7', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-15 07:34:21'),
('779', '7', 'Invoice dicetak: INV-2025-0003', 'invoice', '3', '::1', '2025-09-15 07:34:29'),
('780', '7', 'Status pembayaran invoice diperbarui: lunas', 'invoice', '1', '::1', '2025-09-15 07:34:56'),
('781', '7', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-15 07:35:04'),
('782', '7', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-15 07:35:07'),
('783', '7', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-15 07:35:24'),
('784', '7', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-15 07:35:29'),
('785', '7', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-15 07:35:42'),
('786', '7', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-15 07:36:34'),
('787', '7', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-15 07:37:30'),
('788', '7', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-15 07:37:33'),
('789', '7', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-15 07:38:21'),
('790', '7', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-15 07:38:38'),
('791', '7', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-15 07:38:47'),
('792', '7', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-15 07:38:48'),
('793', '7', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-15 07:38:49'),
('794', '7', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-15 07:42:07'),
('795', '7', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-15 07:42:09'),
('796', '7', 'Hasil pemeriksaan dicetak: LAB20250009', 'pemeriksaan_lab', '9', '::1', '2025-09-15 07:42:12'),
('797', '4', 'User logged in', 'users', NULL, '::1', '2025-09-15 13:00:40'),
('798', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-15 13:15:12'),
('799', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-15 13:15:14'),
('800', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-15 13:15:44'),
('801', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-15 13:20:48'),
('802', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-15 13:36:38'),
('803', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-15 13:37:20'),
('804', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-15 13:37:30'),
('805', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-15 13:37:31'),
('806', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-15 13:37:36'),
('807', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-15 13:37:42'),
('808', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-15 13:38:36'),
('809', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-15 13:58:04'),
('810', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-15 14:02:04'),
('811', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-15 14:02:06'),
('812', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-15 14:02:07'),
('813', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-15 14:02:07'),
('814', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-15 14:02:08'),
('815', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-15 14:02:08'),
('816', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-15 14:02:08'),
('817', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-15 14:02:08'),
('818', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-15 14:02:08'),
('819', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-15 14:02:09'),
('820', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-15 14:02:09'),
('821', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-15 14:02:09'),
('822', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-15 14:02:09'),
('823', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-15 14:02:09'),
('824', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-15 14:02:09'),
('825', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-15 14:02:27'),
('826', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-15 14:02:35'),
('827', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-15 16:05:46'),
('828', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-15 17:02:16'),
('829', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-15 17:06:05'),
('830', '4', 'User logged in', 'users', NULL, '::1', '2025-09-15 20:29:02'),
('831', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-15 20:29:05'),
('832', '4', 'User logged in', 'users', NULL, '::1', '2025-09-16 02:05:22'),
('833', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-16 02:05:25'),
('834', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-16 02:07:40'),
('835', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-16 02:08:10'),
('836', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-16 02:08:36'),
('837', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-16 02:08:52'),
('838', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-16 02:09:09'),
('839', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-16 02:09:28'),
('840', '4', 'User logged in', 'users', NULL, '::1', '2025-09-16 04:06:11'),
('841', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-16 04:06:13'),
('842', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-16 04:06:24'),
('843', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-16 04:06:36'),
('844', '4', 'User logged in', 'users', NULL, '::1', '2025-09-16 06:11:06'),
('845', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-16 06:11:09'),
('846', '4', 'User logged in', 'users', NULL, '::1', '2025-09-16 06:23:29'),
('847', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-16 06:23:32'),
('848', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-16 06:26:27'),
('849', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-16 06:36:29'),
('850', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-16 07:01:27'),
('851', '4', 'Mengakses dashboard inventory petugas', 'system', NULL, '::1', '2025-09-16 07:30:28'),
('852', '4', 'Mengakses dashboard inventory petugas', 'system', NULL, '::1', '2025-09-16 07:30:42'),
('853', '4', 'Mengakses monitoring inventory', 'system', NULL, '::1', '2025-09-16 07:30:47'),
('854', '4', 'Mengakses dashboard inventory petugas', 'system', NULL, '::1', '2025-09-16 07:31:25'),
('855', '4', 'Mengakses dashboard inventory petugas', 'system', NULL, '::1', '2025-09-16 07:31:35'),
('856', '4', 'Mengakses dashboard inventory petugas', 'system', NULL, '::1', '2025-09-16 07:33:43'),
('857', '4', 'Mengakses dashboard inventory petugas', 'system', NULL, '::1', '2025-09-16 07:34:03'),
('858', '4', 'Mengakses dashboard inventory petugas', 'system', NULL, '::1', '2025-09-16 07:43:25'),
('859', '4', 'Mengakses dashboard inventory petugas', 'system', NULL, '::1', '2025-09-16 07:43:49'),
('860', '4', 'Mengakses dashboard inventory petugas', 'system', NULL, '::1', '2025-09-16 07:59:42'),
('861', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-16 08:00:03'),
('862', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-16 08:00:04'),
('863', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-16 08:00:05'),
('864', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-16 08:15:50'),
('865', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-16 08:16:00'),
('866', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-16 08:16:21'),
('867', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-16 08:17:11'),
('868', '1', 'User logged in', 'users', NULL, '::1', '2025-09-16 10:54:15'),
('869', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-16 10:54:15'),
('870', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-16 10:54:44'),
('871', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-16 10:54:51'),
('872', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-16 10:54:54'),
('873', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-16 10:54:57'),
('874', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-16 10:55:48'),
('875', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-16 10:55:54'),
('876', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-16 10:55:56'),
('877', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-16 10:55:57'),
('878', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-16 10:56:08'),
('879', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-16 10:56:09'),
('880', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-16 10:58:45'),
('881', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-16 10:59:26'),
('882', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-16 10:59:28'),
('883', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-16 10:59:31'),
('884', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-16 10:59:46'),
('885', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-16 10:59:57'),
('886', '1', 'Data pengguna diperbarui: Firdaus', 'users', '7', '::1', '2025-09-16 11:00:08'),
('887', '7', 'User logged in', 'users', NULL, '::1', '2025-09-16 11:00:19'),
('888', '7', 'Examination request created', 'pemeriksaan_lab', '12', '::1', '2025-09-16 11:01:09'),
('889', '1', 'User logged out', 'users', NULL, '::1', '2025-09-16 11:01:27'),
('890', '4', 'User logged in', 'users', NULL, '::1', '2025-09-16 11:01:36'),
('891', '4', 'Lab request accepted', 'pemeriksaan_lab', '12', '::1', '2025-09-16 11:02:07'),
('892', '7', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-16 11:15:46'),
('893', '7', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-16 11:16:07'),
('894', '7', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-16 11:16:16'),
('895', '7', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-16 11:16:17'),
('896', '7', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-16 11:16:17'),
('897', '7', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-16 11:17:06'),
('898', '7', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-16 11:17:30'),
('899', '7', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-16 11:17:31'),
('900', '7', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-16 11:17:32'),
('901', '7', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-16 11:17:39'),
('902', '7', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-16 11:18:02'),
('903', '4', 'Sample status updated to selesai', 'pemeriksaan_lab', '12', '::1', '2025-09-16 11:27:35'),
('904', '4', 'Sample status updated to cancelled', 'pemeriksaan_lab', '1', '::1', '2025-09-16 11:28:01'),
('905', '7', 'User logged out', 'users', NULL, '::1', '2025-09-16 11:28:16'),
('906', '1', 'User logged in', 'users', NULL, '::1', '2025-09-16 11:28:24'),
('907', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-16 11:28:25'),
('908', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-16 11:28:30'),
('909', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-16 11:28:40'),
('910', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-16 11:28:46'),
('911', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-16 11:28:54'),
('912', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-16 11:28:58'),
('913', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-16 11:29:01'),
('914', '4', 'Lab results saved: urinologi', 'pemeriksaan_lab', '2', '::1', '2025-09-16 11:30:35'),
('915', '4', 'Examination result validated', 'pemeriksaan_lab', '2', '::1', '2025-09-16 11:30:56'),
('916', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-16 11:31:10'),
('917', '1', 'Hasil pemeriksaan dicetak: LAB20250002', 'pemeriksaan_lab', '2', '::1', '2025-09-16 11:31:18'),
('918', '1', 'Hasil pemeriksaan dicetak: LAB20250012', 'pemeriksaan_lab', '12', '::1', '2025-09-16 11:31:25'),
('919', '1', 'Hasil pemeriksaan dicetak: LAB20250002', 'pemeriksaan_lab', '2', '::1', '2025-09-16 11:31:29'),
('920', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-16 11:31:44'),
('921', '1', 'Hasil pemeriksaan dicetak: LAB20250002', 'pemeriksaan_lab', '2', '::1', '2025-09-16 11:31:51'),
('922', '4', 'Item inventory baru ditambahkan: kjkjk', 'alat_laboratorium', '10', '::1', '2025-09-16 11:41:22'),
('923', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-16 11:42:01'),
('924', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-16 11:42:04'),
('925', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-16 12:02:50'),
('926', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-16 12:02:51'),
('927', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-16 12:02:52'),
('928', '1', 'Hasil pemeriksaan dicetak: LAB20250012', 'pemeriksaan_lab', '12', '::1', '2025-09-16 12:02:55'),
('929', '1', 'Hasil pemeriksaan dicetak: LAB20250002', 'pemeriksaan_lab', '2', '::1', '2025-09-16 12:03:00'),
('930', '1', 'Hasil pemeriksaan dicetak: LAB20250009', 'pemeriksaan_lab', '9', '::1', '2025-09-16 12:03:10'),
('931', '1', 'Hasil pemeriksaan dicetak: LAB20250010', 'pemeriksaan_lab', '10', '::1', '2025-09-16 12:03:13'),
('932', '1', 'Hasil pemeriksaan dicetak: LAB20250011', 'pemeriksaan_lab', '11', '::1', '2025-09-16 12:03:16'),
('933', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-16 12:17:07'),
('934', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-16 12:17:14'),
('935', '4', 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-09-16 13:07:51'),
('936', '4', 'User logged out', 'users', NULL, '::1', '2025-09-16 13:08:20'),
('937', '7', 'User logged in', 'users', NULL, '::1', '2025-09-16 13:09:49'),
('938', '7', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-16 13:09:56'),
('939', '7', 'User logged out', 'users', NULL, '::1', '2025-09-16 13:16:16'),
('940', '1', 'User logged in', 'users', NULL, '::1', '2025-09-16 13:16:35'),
('941', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-16 13:16:35'),
('942', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-16 13:17:10'),
('943', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-16 13:17:24'),
('944', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-16 13:17:33'),
('945', '1', 'Mengakses halaman backup database', 'system', NULL, '::1', '2025-09-16 13:17:40'),
('946', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-16 13:18:21'),
('947', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-16 13:19:05'),
('948', '1', 'Mengakses halaman backup database', 'system', NULL, '::1', '2025-09-16 13:20:29'),
('949', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-16 13:20:32'),
('950', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-16 13:21:22'),
('951', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-16 13:21:43'),
('952', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-16 13:23:26'),
('953', '1', 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-09-16 13:24:48'),
('954', '1', 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-09-16 13:24:57'),
('955', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-16 13:25:04'),
('956', '1', 'Mengakses laporan keuangan', 'system', NULL, '::1', '2025-09-16 13:25:53'),
('957', '1', 'Mengakses halaman backup database', 'system', NULL, '::1', '2025-09-16 13:26:11'),
('958', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-16 13:26:31'),
('959', '1', 'User logged out', 'users', NULL, '::1', '2025-09-16 13:27:00'),
('960', '7', 'User logged in', 'users', NULL, '::1', '2025-09-16 13:27:44'),
('961', '7', 'User logged out', 'users', NULL, '::1', '2025-09-16 13:27:57'),
('962', '1', 'User logged in', 'users', NULL, '::1', '2025-09-16 13:30:15'),
('963', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-16 13:30:16'),
('964', '1', 'User logged out', 'users', NULL, '::1', '2025-09-16 13:30:25'),
('965', '7', 'User logged in', 'users', NULL, '::1', '2025-09-16 13:30:40'),
('966', '7', 'User logged out', 'users', NULL, '::1', '2025-09-16 13:30:53'),
('967', '4', 'User logged in', 'users', NULL, '::1', '2025-09-16 13:31:05'),
('968', '4', 'User logged out', 'users', NULL, '::1', '2025-09-16 13:31:22'),
('969', '1', 'User logged in', 'users', NULL, '::1', '2025-09-16 13:31:37'),
('970', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-16 13:31:38'),
('971', '1', 'Pengguna baru ditambahkan: ocha', 'users', '9', '::1', '2025-09-16 13:34:40'),
('972', '1', 'User logged out', 'users', NULL, '::1', '2025-09-16 13:36:33'),
('973', '1', 'User logged in', 'users', NULL, '::1', '2025-09-16 13:36:52'),
('974', '1', 'Mengakses dashboard', 'system', NULL, '::1', '2025-09-16 13:36:52'),
('975', '1', 'Pengguna dinonaktifkan: ocha', 'users', '9', '::1', '2025-09-16 13:37:02'),
('976', '1', 'Pengguna diaktifkan: ocha', 'users', '9', '::1', '2025-09-16 13:37:45'),
('977', '1', 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-09-16 13:37:57'),
('978', '1', 'User logged out', 'users', NULL, '::1', '2025-09-16 13:38:02'),
('979', '9', 'User logged in', 'users', NULL, '::1', '2025-09-16 13:38:33'),
('980', '1', 'Mengakses halaman backup database', 'system', NULL, '::1', '2025-09-16 13:39:11');
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `administrasi`
LOCK TABLES `administrasi` WRITE;
INSERT INTO `administrasi` (`administrasi_id`, `user_id`, `nama_admin`, `telepon`, `created_at`) VALUES
('1', '2', 'Admin Front Office', '0852-8218-2747', '2025-08-28 17:41:13'),
('2', '7', 'Firdaus', '0852-8218-2747', '2025-09-13 01:01:18');
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
  KEY `idx_alat_status` (`status_alat`),
  KEY `idx_status_jadwal` (`status_alat`,`jadwal_kalibrasi`),
  KEY `idx_lokasi` (`lokasi`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `alat_laboratorium`
LOCK TABLES `alat_laboratorium` WRITE;
INSERT INTO `alat_laboratorium` (`alat_id`, `nama_alat`, `kode_unik`, `merek_model`, `lokasi`, `status_alat`, `jadwal_kalibrasi`, `tanggal_kalibrasi_terakhir`, `riwayat_perbaikan`, `created_at`, `updated_at`) VALUES
('2', 'Hematology Analyzer', 'ALT002', 'Sysmex XS-1000i', 'Lab Hematologi', 'Normal', '2025-09-15', NULL, NULL, '2025-08-28 17:41:13', '2025-08-28 17:41:13'),
('3', 'Mikroskop', 'ALT003', 'Olympus CX23', 'Lab Urinologi', 'Normal', '2025-10-01', NULL, NULL, '2025-08-28 17:41:13', '2025-08-28 17:41:13'),
('4', 'Centrifuge', 'ALT004', 'Eppendorf 5424R', 'Lab Umum', 'Normal', '2025-12-01', NULL, NULL, '2025-09-15 01:35:28', '2025-09-15 01:35:28'),
('5', 'pH Meter', 'ALT005', 'Hanna HI-2020', 'Lab Kimia', 'Normal', '2025-11-15', NULL, NULL, '2025-09-15 01:35:28', '2025-09-15 01:35:28'),
('6', 'Analytical Balance', 'ALT006', 'Sartorius Entris 224i', 'Lab Kimia', 'Perlu Kalibrasi', '2025-10-01', NULL, NULL, '2025-09-15 01:35:28', '2025-09-15 01:35:28'),
('7', 'Autoclave', 'ALT007', 'Tuttnauer 3870EA', 'Lab Sterilisasi', 'Normal', '2025-12-31', NULL, NULL, '2025-09-15 01:35:28', '2025-09-15 01:35:28'),
('8', 'Incubator', 'ALT008', 'Memmert IN110', 'Lab Mikrobiologi', 'Normal', '2026-01-15', NULL, NULL, '2025-09-15 01:35:28', '2025-09-15 01:35:28'),
('10', 'kjkjk', '123', 'asdas', 'asdasd', 'Normal', '2025-09-02', NULL, 'asdasd', '2025-09-16 16:41:22', '2025-09-16 16:41:22');
UNLOCK TABLES;

-- Table structure for table `calibration_history`
DROP TABLE IF EXISTS `calibration_history`;
CREATE TABLE `calibration_history` (
  `calibration_id` int(11) NOT NULL AUTO_INCREMENT,
  `alat_id` int(11) NOT NULL,
  `tanggal_kalibrasi` date NOT NULL,
  `hasil_kalibrasi` text DEFAULT NULL,
  `teknisi` varchar(100) DEFAULT NULL,
  `sertifikat_no` varchar(100) DEFAULT NULL,
  `next_calibration_date` date DEFAULT NULL,
  `status` enum('Passed','Failed','Conditional') DEFAULT 'Passed',
  `catatan` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`calibration_id`),
  KEY `idx_alat_id` (`alat_id`),
  KEY `idx_tanggal_kalibrasi` (`tanggal_kalibrasi`),
  KEY `idx_user_id` (`user_id`),
  CONSTRAINT `fk_calibration_alat` FOREIGN KEY (`alat_id`) REFERENCES `alat_laboratorium` (`alat_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_calibration_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `calibration_history`
-- No data found for table `calibration_history`

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

-- Table structure for table `inventory_maintenance_logs`
DROP TABLE IF EXISTS `inventory_maintenance_logs`;
CREATE TABLE `inventory_maintenance_logs` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `item_type` enum('alat','reagen') NOT NULL,
  `action` varchar(100) NOT NULL,
  `status` varchar(50) NOT NULL,
  `notes` text DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`),
  KEY `item_id` (`item_id`),
  KEY `user_id` (`user_id`),
  KEY `item_type` (`item_type`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `inventory_maintenance_logs`
-- No data found for table `inventory_maintenance_logs`

-- Table structure for table `inventory_notifications`
DROP TABLE IF EXISTS `inventory_notifications`;
CREATE TABLE `inventory_notifications` (
  `notification_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('low_stock','expiry','calibration','maintenance') NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `item_type` enum('alat','reagen') DEFAULT NULL,
  `priority` enum('low','medium','high','critical') DEFAULT 'medium',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`notification_id`),
  KEY `type` (`type`),
  KEY `priority` (`priority`),
  KEY `is_read` (`is_read`),
  KEY `created_at` (`created_at`),
  KEY `item_reference` (`item_id`,`item_type`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `inventory_notifications`
LOCK TABLES `inventory_notifications` WRITE;
INSERT INTO `inventory_notifications` (`notification_id`, `type`, `title`, `message`, `item_id`, `item_type`, `priority`, `is_read`, `created_at`, `expires_at`) VALUES
('1', 'calibration', 'Kalibrasi Segera: Hematology Analyzer', 'Kalibrasi Hematology Analyzer dalam 0 hari', '2', 'alat', 'medium', '0', '2025-09-15 17:02:17', '2025-09-22 17:02:17'),
('2', 'expiry', 'Akan Expired: Mikroskop', 'Reagen Mikroskop akan expired dalam 16 hari', '3', 'reagen', 'medium', '0', '2025-09-15 17:02:17', '2025-09-22 17:02:17'),
('3', 'expiry', 'Akan Expired: Analytical Balance', 'Reagen Analytical Balance akan expired dalam 16 hari', '6', 'reagen', 'medium', '0', '2025-09-15 17:02:17', '2025-09-22 17:02:17'),
('4', 'calibration', 'Kalibrasi Segera: Hematology Analyzer', 'Kalibrasi Hematology Analyzer dalam 0 hari', '2', 'alat', 'medium', '0', '2025-09-15 17:06:06', '2025-09-22 17:06:06'),
('5', 'expiry', 'Akan Expired: Mikroskop', 'Reagen Mikroskop akan expired dalam 16 hari', '3', 'reagen', 'medium', '0', '2025-09-15 17:06:06', '2025-09-22 17:06:06'),
('6', 'expiry', 'Akan Expired: Analytical Balance', 'Reagen Analytical Balance akan expired dalam 16 hari', '6', 'reagen', 'medium', '0', '2025-09-15 17:06:06', '2025-09-22 17:06:06'),
('7', 'calibration', 'Kalibrasi Segera: Hematology Analyzer', 'Kalibrasi Hematology Analyzer dalam 0 hari', '2', 'alat', 'medium', '0', '2025-09-15 17:11:07', '2025-09-22 17:11:07'),
('8', 'expiry', 'Akan Expired: Mikroskop', 'Reagen Mikroskop akan expired dalam 16 hari', '3', 'reagen', 'medium', '0', '2025-09-15 17:11:07', '2025-09-22 17:11:07'),
('9', 'expiry', 'Akan Expired: Analytical Balance', 'Reagen Analytical Balance akan expired dalam 16 hari', '6', 'reagen', 'medium', '0', '2025-09-15 17:11:07', '2025-09-22 17:11:07');
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `invoice`
LOCK TABLES `invoice` WRITE;
INSERT INTO `invoice` (`invoice_id`, `pemeriksaan_id`, `nomor_invoice`, `tanggal_invoice`, `jenis_pembayaran`, `total_biaya`, `status_pembayaran`, `metode_pembayaran`, `nomor_kartu_bpjs`, `nomor_sep`, `tanggal_pembayaran`, `keterangan`, `created_at`) VALUES
('1', '10', 'INV20250001', '2025-09-12', 'umum', '120000.00', 'lunas', 'transfer', NULL, NULL, '2025-09-15', '', '2025-09-12 16:33:34'),
('2', '9', 'INV-2025-0002', '2025-09-06', 'umum', '200000.00', 'belum_bayar', NULL, NULL, NULL, NULL, NULL, '2025-09-12 19:16:48'),
('3', '11', 'INV-2025-0003', '2025-09-05', 'umum', '85000.00', 'belum_bayar', NULL, NULL, NULL, NULL, NULL, '2025-09-12 20:36:30'),
('4', '2', 'INV-2025-0004', '2025-09-07', 'umum', '85000.00', 'belum_bayar', NULL, NULL, NULL, NULL, NULL, '2025-09-16 11:31:18'),
('5', '12', 'INV-2025-0005', '2025-09-17', 'umum', '423000.00', 'belum_bayar', NULL, NULL, NULL, NULL, NULL, '2025-09-16 11:31:25');
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `lab`
LOCK TABLES `lab` WRITE;
INSERT INTO `lab` (`lab_id`, `nama`, `alamat`, `telephone`, `email`, `created_at`) VALUES
('1', 'Laboratorium Labsy', 'Jl. Tata Bumi No.3, Area Sawah, Banyuraden, Kec. Gamping, Kabupaten Sleman, Daerah Istimewa Yogyakarta 55293', '(0274) 617601', 'info@labsy.com', '2025-08-28 17:41:13');
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
('2', 'Budi Santoso2', '3175081234567890', 'P', 'Jogja', '1986-06-05', '39', 'Jl. Kesehatan No. 123, Jogja Selatan', 'Karyawan Negri', '08123456780', 'Siti Santoso - 08123456788', 'jantung', 'TBC', 'Dr. Ahmad Rahman, Sp.PD', 'RS Umum Jakarta', 'RUJ/2025/001', '2025-09-06', 'Suspek Diabetes', 'Gula darah puasa, HbA1c', 'REG20250002', '2025-09-06 02:48:18'),
('3', 'Siti Rahayup', '3175082345678901', 'P', 'Bandung', '1990-07-10', '34', 'Jl. Melati No. 456, Bandung', 'Guru', '08234567890', 'Ahmad Rahayu - 08234567889', 'Diabetes tipe 2', 'Gula Darah, Urinologi', 'Dr. Siti Nurhaliza, Sp.PD', 'Klinik Sehat Bandung', 'RUJ/2025/002', '2025-09-06', 'Kontrol Diabetes', 'Gula darah, Urin lengkap', 'REG20250003', '2025-09-06 02:48:18'),
('4', 'Ahmad Wijaya ', '3175083456789012', 'L', 'Yogyakarta', '1978-12-10', '46', 'Jl. Kenanga No. 789, Surabaya', 'Wiraswasta', '08345678901', 'Rina Wijaya - 08345678900', 'Kolesterol tinggi', 'Kimia Darah', 'Dr. Bambang Sutopo, Sp.JP', 'RS Jantung Surabaya', 'RUJ/2025/003', '2025-09-06', 'Dislipidemia', 'Profil lipid lengkap', 'REG20250004', '2025-09-06 02:48:18'),
('5', 'Rina Sari', '3175084567890121', 'P', 'Yogyakarta', '1995-05-18', '29', 'Jl. Anggrek No. 321, Yogyakarta', 'Dokter', '08456789012', 'Doni Sari - 08456789011', 'Sehat', 'Medical Check Up', 'Dr. Retno Wulan, Sp.OG', 'RS Ibu dan Anak Yogya', 'RUJ/2025/004', '2025-09-07', 'MCU Pranikah', 'Lab lengkap, TORCH', 'REG20250005', '2025-09-06 02:48:18'),
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `pemeriksaan_lab`
LOCK TABLES `pemeriksaan_lab` WRITE;
INSERT INTO `pemeriksaan_lab` (`pemeriksaan_id`, `pasien_id`, `petugas_id`, `lab_id`, `nomor_pemeriksaan`, `tanggal_pemeriksaan`, `jenis_pemeriksaan`, `status_pemeriksaan`, `keterangan`, `biaya`, `created_at`, `updated_at`, `completed_at`, `started_at`) VALUES
('1', '2', '1', '1', 'LAB20250001', '2025-09-07', 'Kimia Darah', 'cancelled', 'Tidak bersedia', '200000.00', '2025-09-06 15:23:46', '2025-09-16 16:28:01', NULL, '2025-09-07 19:27:01'),
('2', '3', '1', '1', 'LAB20250002', '2025-09-07', 'Urinologi', 'selesai', 'Pemeriksaan urin lengkap untuk evaluasi diabetes', '85000.00', '2025-09-06 15:23:46', '2025-09-16 16:30:56', '2025-09-16 16:30:56', '2025-09-06 20:45:39'),
('3', '4', '1', '1', 'LAB20250003', '2025-09-07', 'Kimia Darah', 'progress', 'Profil lipid lengkap untuk evaluasi dislipidemia', '150000.00', '2025-09-06 15:23:46', '2025-09-12 20:28:41', NULL, '2025-09-12 20:28:41'),
('4', '5', '1', '1', 'LAB20250004', '2025-09-07', 'Serologi', 'progress', 'Pemeriksaan TORCH untuk persiapan pranikah', '300000.00', '2025-09-06 15:23:46', '2025-09-07 19:27:25', NULL, '2025-09-07 19:27:25'),
('5', '6', NULL, '1', 'LAB20250005', '2025-09-07', 'Kimia Darah', 'pending', 'Pemeriksaan asam urat dan fungsi ginjal', '180000.00', '2025-09-06 15:23:46', NULL, NULL, NULL),
('6', '7', NULL, '1', 'LAB20250006', '2025-09-07', 'Hematologi', 'pending', 'Pemeriksaan hemoglobin dan ferritin untuk anemia', '120000.00', '2025-09-06 15:23:46', NULL, NULL, NULL),
('7', '8', NULL, '1', 'LAB20250007', '2025-09-07', 'Kimia Darah', 'pending', 'Fungsi ginjal dan elektrolit untuk hipertensi', '160000.00', '2025-09-06 15:23:46', NULL, NULL, NULL),
('8', '9', '1', '1', 'LAB20250008', '2025-09-07', 'TBC', 'cancelled', 'Karna ada ketidak sesuaian', '150000.00', '2025-09-06 15:23:46', '2025-09-06 15:30:16', NULL, '2025-09-06 15:25:15'),
('9', '10', '1', '1', 'LAB20250009', '2025-09-06', 'Kimia Darah', 'selesai', 'Sedang dianalisis - proses kimia darah', '200000.00', '2025-09-06 15:23:46', '2025-09-06 21:49:12', '2025-09-06 21:49:12', NULL),
('10', '11', '1', '1', 'LAB20250010', '2025-09-06', 'Hematologi', 'selesai', 'Sampel sedang diproses - hematologi rutin', '120000.00', '2025-09-06 15:23:46', '2025-09-07 19:47:55', '2025-09-07 19:47:55', '2025-09-06 08:00:00'),
('11', '1', '1', '1', 'LAB20250011', '2025-09-05', 'Urinologi', 'selesai', 'Pemeriksaan urin telah selesai', '85000.00', '2025-09-06 15:23:46', '2025-09-06 15:23:46', NULL, '2025-09-06 08:30:00'),
('12', '5', '1', NULL, 'LAB20250012', '2025-09-17', 'Kimia Darah', 'selesai', 'Sukses', '423000.00', '2025-09-16 11:01:09', '2025-09-16 16:27:35', '2025-09-16 16:27:35', '2025-09-16 16:02:07');
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `petugas_lab`
LOCK TABLES `petugas_lab` WRITE;
INSERT INTO `petugas_lab` (`petugas_id`, `user_id`, `nama_petugas`, `jenis_keahlian`, `telepon`, `alamat`, `created_at`) VALUES
('1', '4', 'Sari Oktavia', 'Analis Laboratorium', '', '', '2025-08-28 17:41:13'),
('5', '9', 'ochaaaaa', 'anlisis bakteri', '989897867554', 'JL. POLKESYO', '2025-09-16 18:34:40');
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
  KEY `idx_reagen_expired` (`expired_date`),
  KEY `idx_status_stock` (`status`,`jumlah_stok`),
  KEY `idx_expired_status` (`expired_date`,`status`),
  KEY `idx_lokasi_penyimpanan` (`lokasi_penyimpanan`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `reagen`
LOCK TABLES `reagen` WRITE;
INSERT INTO `reagen` (`reagen_id`, `nama_reagen`, `kode_unik`, `jumlah_stok`, `satuan`, `lokasi_penyimpanan`, `tanggal_dipakai`, `expired_date`, `stok_minimal`, `status`, `catatan`, `created_at`, `updated_at`) VALUES
('1', 'Glucose Reagent Kit', 'REA001', '100', 'test', 'Lemari A1', NULL, '2025-12-31', '20', 'Tersedia', NULL, '2025-08-28 17:41:13', '2025-08-28 17:41:13'),
('2', 'Cholesterol Test Kit', 'REA002', '50', 'test', 'Lemari A2', NULL, '2025-10-31', '15', 'Tersedia', NULL, '2025-08-28 17:41:13', '2025-08-28 17:41:13'),
('4', 'Buffer Solution pH 7.0', 'REA004', '25', 'botol', 'Lemari B2', NULL, '2026-03-31', '5', 'Tersedia', NULL, '2025-09-15 01:35:29', '2025-09-15 01:35:29'),
('5', 'Ethanol 96%', 'REA005', '12', 'liter', 'Lemari C1', NULL, '2025-12-31', '3', 'Tersedia', NULL, '2025-09-15 01:35:29', '2025-09-15 01:35:29'),
('6', 'Methylene Blue', 'REA006', '8', 'botol', 'Lemari A3', NULL, '2025-11-30', '2', 'Tersedia', NULL, '2025-09-15 01:35:29', '2025-09-15 01:35:29'),
('7', 'NaCl 0.9%', 'REA007', '15', 'botol', 'Lemari B1', NULL, '2026-06-30', '5', 'Tersedia', NULL, '2025-09-15 01:35:29', '2025-09-15 01:35:29'),
('8', 'Distilled Water', 'REA008', '50', 'liter', 'Lemari D1', NULL, '2026-12-31', '10', 'Tersedia', NULL, '2025-09-15 01:35:29', '2025-09-15 01:35:29');
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

-- Table structure for table `stock_movements`
DROP TABLE IF EXISTS `stock_movements`;
CREATE TABLE `stock_movements` (
  `movement_id` int(11) NOT NULL AUTO_INCREMENT,
  `reagen_id` int(11) NOT NULL,
  `movement_type` enum('add','subtract','adjust','use','expired','damaged') NOT NULL,
  `quantity_changed` int(11) NOT NULL,
  `stock_before` int(11) NOT NULL,
  `stock_after` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `movement_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`movement_id`),
  KEY `idx_reagen_id` (`reagen_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_movement_date` (`movement_date`),
  CONSTRAINT `fk_stock_movements_reagen` FOREIGN KEY (`reagen_id`) REFERENCES `reagen` (`reagen_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_stock_movements_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `stock_movements`
-- No data found for table `stock_movements`

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
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `timeline_progres`
LOCK TABLES `timeline_progres` WRITE;
INSERT INTO `timeline_progres` (`timeline_id`, `pemeriksaan_id`, `status`, `keterangan`, `petugas_id`, `tanggal_update`) VALUES
('12', '8', 'Pemeriksaan Dibatalkan', 'Karna ada ketidak sesuaian', '1', '2025-09-06 10:30:16'),
('13', '9', 'Hasil Diinput', 'Hasil pemeriksaan telah diinput dan siap untuk divalidasi', '1', '2025-09-06 16:48:18'),
('14', '9', 'Hasil Divalidasi', 'Hasil pemeriksaan telah divalidasi dan siap diserahkan', '1', '2025-09-06 16:49:12'),
('15', '10', 'Hasil Diinput', 'Hasil pemeriksaan telah diinput dan siap untuk divalidasi', '1', '2025-09-07 14:47:30'),
('16', '10', 'Hasil Divalidasi', 'Hasil pemeriksaan telah divalidasi dan siap diserahkan', '1', '2025-09-07 14:47:55'),
('17', '12', 'Pemeriksaan Selesai', 'Sukses', '1', '2025-09-16 11:27:35'),
('18', '1', 'Pemeriksaan Dibatalkan', 'Tidak bersedia', '1', '2025-09-16 11:28:01'),
('19', '2', 'Hasil Diinput', 'Hasil pemeriksaan telah diinput dan siap untuk divalidasi', '1', '2025-09-16 11:30:35'),
('20', '2', 'Hasil Divalidasi', 'Hasil pemeriksaan telah divalidasi dan siap diserahkan', '1', '2025-09-16 11:30:56');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `urinologi`
LOCK TABLES `urinologi` WRITE;
INSERT INTO `urinologi` (`urinologi_id`, `pemeriksaan_id`, `makroskopis`, `mikroskopis`, `kimia_ph`, `protein`, `tes_kehamilan`, `created_at`) VALUES
('1', '2', 'bau', 'leukosit', '4.7', 'protein c', 'Positif', '2025-09-16 11:30:35');
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `users`
LOCK TABLES `users` WRITE;
INSERT INTO `users` (`user_id`, `username`, `password`, `role`, `is_active`, `created_at`, `updated_at`) VALUES
('1', 'superadmin', '0192023a7bbd73250516f069df18b500', 'admin', '1', '2025-08-28 17:41:13', '2025-08-28 17:41:13'),
('2', 'admin_front', '15ff3c0a0310a2e3de3e95c8aeb328d0', 'administrasi', '1', '2025-08-28 17:41:13', '2025-09-12 15:02:05'),
('3', 'dr_budi', 'cab2d8232139ee4f469a920732578f71', 'dokter', '0', '2025-08-28 17:41:13', '2025-08-29 14:05:04'),
('4', 'lab_sari', '081c49b8c66a69aad79f4bca8334e0ef', 'petugas_lab', '1', '2025-08-28 17:41:13', '2025-09-14 22:28:09'),
('7', 'Firdaus', 'de28f8f7998f23ab4194b51a6029416f', 'administrasi', '1', '2025-09-12 20:01:18', '2025-09-16 11:00:08'),
('9', 'ocha', '04217c4d7e246e38b0d7014ee109755b', 'petugas_lab', '1', '2025-09-16 13:34:40', '2025-09-16 18:37:45');
UNLOCK TABLES;

-- Table structure for table `v_examination_stats`
DROP TABLE IF EXISTS `v_examination_stats`;
;

-- Dumping data for table `v_examination_stats`
LOCK TABLES `v_examination_stats` WRITE;
INSERT INTO `v_examination_stats` (`exam_date`, `status_pemeriksaan`, `count`, `avg_processing_hours`) VALUES
('2025-09-17', 'selesai', '1', '0.0000'),
('2025-09-07', 'pending', '3', NULL),
('2025-09-07', 'progress', '2', NULL),
('2025-09-07', 'selesai', '1', '235.0000'),
('2025-09-07', 'cancelled', '2', NULL),
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
('reagen', '4', 'Buffer Solution pH 7.0', 'REA004', '25', '5', 'Tersedia', '2026-03-31', 'OK'),
('reagen', '5', 'Ethanol 96%', 'REA005', '12', '3', 'Tersedia', '2025-12-31', 'OK'),
('reagen', '6', 'Methylene Blue', 'REA006', '8', '2', 'Tersedia', '2025-11-30', 'OK'),
('reagen', '7', 'NaCl 0.9%', 'REA007', '15', '5', 'Tersedia', '2026-06-30', 'OK'),
('reagen', '8', 'Distilled Water', 'REA008', '50', '10', 'Tersedia', '2026-12-31', 'OK'),
('alat', '2', 'Hematology Analyzer', 'ALT002', NULL, NULL, 'Normal', '2025-09-15', 'Calibration Due'),
('alat', '3', 'Mikroskop', 'ALT003', NULL, NULL, 'Normal', '2025-10-01', 'OK'),
('alat', '4', 'Centrifuge', 'ALT004', NULL, NULL, 'Normal', '2025-12-01', 'OK'),
('alat', '5', 'pH Meter', 'ALT005', NULL, NULL, 'Normal', '2025-11-15', 'OK'),
('alat', '6', 'Analytical Balance', 'ALT006', NULL, NULL, 'Perlu Kalibrasi', '2025-10-01', 'Warning'),
('alat', '7', 'Autoclave', 'ALT007', NULL, NULL, 'Normal', '2025-12-31', 'OK'),
('alat', '8', 'Incubator', 'ALT008', NULL, NULL, 'Normal', '2026-01-15', 'OK'),
('alat', '10', 'kjkjk', '123', NULL, NULL, 'Normal', '2025-09-02', 'Calibration Due');
UNLOCK TABLES;

-- Table structure for table `v_invoice_cetak`
DROP TABLE IF EXISTS `v_invoice_cetak`;
;

-- Dumping data for table `v_invoice_cetak`
LOCK TABLES `v_invoice_cetak` WRITE;
INSERT INTO `v_invoice_cetak` (`nomor_invoice`, `tanggal_invoice`, `nama_pasien`, `nik`, `umur`, `alamat_domisili`, `telepon`, `nomor_pemeriksaan`, `jenis_pemeriksaan`, `tanggal_pemeriksaan`, `total_biaya`, `metode_pembayaran`, `status_pembayaran`, `tanggal_pembayaran`, `keterangan`) VALUES
('INV20250001', '2025-09-12', 'Dewi Lestari', '3175090123456789', '38', 'Jl. Marigold No. 741, Balikpapan', '09012345678', 'LAB20250010', 'Hematologi', '2025-09-06', '120000.00', 'transfer', 'lunas', '2025-09-15', ''),
('INV-2025-0002', '2025-09-06', 'Rudi Hermawan', '3175089012345678', '33', 'Jl. Kamboja No. 369, Denpasar', '08901234567', 'LAB20250009', 'Kimia Darah', '2025-09-06', '200000.00', NULL, 'belum_bayar', NULL, NULL),
('INV-2025-0003', '2025-09-05', 'John Doe', '1234567890123456', '34', 'Jl. Contoh No. 456', '08123456789', 'LAB20250011', 'Urinologi', '2025-09-05', '85000.00', NULL, 'belum_bayar', NULL, NULL),
('INV-2025-0004', '2025-09-07', 'Siti Rahayup', '3175082345678901', '34', 'Jl. Melati No. 456, Bandung', '08234567890', 'LAB20250002', 'Urinologi', '2025-09-07', '85000.00', NULL, 'belum_bayar', NULL, NULL),
('INV-2025-0005', '2025-09-17', 'Rina Sari', '3175084567890121', '29', 'Jl. Anggrek No. 321, Yogyakarta', '08456789012', 'LAB20250012', 'Kimia Darah', '2025-09-17', '423000.00', NULL, 'belum_bayar', NULL, NULL);
UNLOCK TABLES;

-- Table structure for table `v_invoice_detail`
DROP TABLE IF EXISTS `v_invoice_detail`;
;

-- Dumping data for table `v_invoice_detail`
LOCK TABLES `v_invoice_detail` WRITE;
INSERT INTO `v_invoice_detail` (`invoice_id`, `nomor_invoice`, `tanggal_invoice`, `jenis_pembayaran`, `total_biaya`, `status_pembayaran`, `metode_pembayaran`, `nomor_kartu_bpjs`, `nomor_sep`, `nama_pasien`, `nik`, `nomor_pemeriksaan`, `jenis_pemeriksaan`) VALUES
('1', 'INV20250001', '2025-09-12', 'umum', '120000.00', 'lunas', 'transfer', NULL, NULL, 'Dewi Lestari', '3175090123456789', 'LAB20250010', 'Hematologi'),
('2', 'INV-2025-0002', '2025-09-06', 'umum', '200000.00', 'belum_bayar', NULL, NULL, NULL, 'Rudi Hermawan', '3175089012345678', 'LAB20250009', 'Kimia Darah'),
('3', 'INV-2025-0003', '2025-09-05', 'umum', '85000.00', 'belum_bayar', NULL, NULL, NULL, 'John Doe', '1234567890123456', 'LAB20250011', 'Urinologi'),
('4', 'INV-2025-0004', '2025-09-07', 'umum', '85000.00', 'belum_bayar', NULL, NULL, NULL, 'Siti Rahayup', '3175082345678901', 'LAB20250002', 'Urinologi'),
('5', 'INV-2025-0005', '2025-09-17', 'umum', '423000.00', 'belum_bayar', NULL, NULL, NULL, 'Rina Sari', '3175084567890121', 'LAB20250012', 'Kimia Darah');
UNLOCK TABLES;

-- Table structure for table `v_pemeriksaan_detail`
DROP TABLE IF EXISTS `v_pemeriksaan_detail`;
;

-- Dumping data for table `v_pemeriksaan_detail`
LOCK TABLES `v_pemeriksaan_detail` WRITE;
INSERT INTO `v_pemeriksaan_detail` (`pemeriksaan_id`, `nomor_pemeriksaan`, `tanggal_pemeriksaan`, `jenis_pemeriksaan`, `status_pemeriksaan`, `biaya`, `nama_pasien`, `nik`, `riwayat_pasien`, `dokter_perujuk`, `asal_rujukan`, `nomor_rujukan`, `tanggal_rujukan`, `diagnosis_awal`, `rekomendasi_pemeriksaan`, `nama_petugas`, `nama_lab`) VALUES
('1', 'LAB20250001', '2025-09-07', 'Kimia Darah', 'cancelled', '200000.00', 'Budi Santoso2', '3175081234567890', 'jantung', 'Dr. Ahmad Rahman, Sp.PD', 'RS Umum Jakarta', 'RUJ/2025/001', '2025-09-06', 'Suspek Diabetes', 'Gula darah puasa, HbA1c', 'Sari Oktavia', 'Laboratorium Labsy'),
('2', 'LAB20250002', '2025-09-07', 'Urinologi', 'selesai', '85000.00', 'Siti Rahayup', '3175082345678901', 'Diabetes tipe 2', 'Dr. Siti Nurhaliza, Sp.PD', 'Klinik Sehat Bandung', 'RUJ/2025/002', '2025-09-06', 'Kontrol Diabetes', 'Gula darah, Urin lengkap', 'Sari Oktavia', 'Laboratorium Labsy'),
('3', 'LAB20250003', '2025-09-07', 'Kimia Darah', 'progress', '150000.00', 'Ahmad Wijaya ', '3175083456789012', 'Kolesterol tinggi', 'Dr. Bambang Sutopo, Sp.JP', 'RS Jantung Surabaya', 'RUJ/2025/003', '2025-09-06', 'Dislipidemia', 'Profil lipid lengkap', 'Sari Oktavia', 'Laboratorium Labsy'),
('4', 'LAB20250004', '2025-09-07', 'Serologi', 'progress', '300000.00', 'Rina Sari', '3175084567890121', 'Sehat', 'Dr. Retno Wulan, Sp.OG', 'RS Ibu dan Anak Yogya', 'RUJ/2025/004', '2025-09-07', 'MCU Pranikah', 'Lab lengkap, TORCH', 'Sari Oktavia', 'Laboratorium Labsy'),
('5', 'LAB20250005', '2025-09-07', 'Kimia Darah', 'pending', '180000.00', 'Doni Pratama', '3175085678901234', 'Asam urat tinggi', 'Dr. Indra Gunawan, Sp.PD', 'Klinik Pratama Medan', 'RUJ/2025/005', '2025-09-07', 'Hiperurisemia', 'Asam urat, fungsi ginjal', NULL, 'Laboratorium Labsy'),
('6', 'LAB20250006', '2025-09-07', 'Hematologi', 'pending', '120000.00', 'Maya Indah', '3175086789012345', 'Anemia ringan', 'Dr. Andi Mappaware, Sp.PD', 'RS Wahidin Makassar', 'RUJ/2025/006', '2025-09-07', 'Anemia Defisiensi Besi', 'Hemoglobin, Ferritin', NULL, 'Laboratorium Labsy'),
('7', 'LAB20250007', '2025-09-07', 'Kimia Darah', 'pending', '160000.00', 'Andi Susanto', '3175087890123456', 'Hipertensi', 'Dr. Hasan Basri, Sp.PD', 'RS Mohammad Hoesin', 'RUJ/2025/007', '2025-09-07', 'Hipertensi Grade 2', 'Fungsi ginjal, elektrolit', NULL, 'Laboratorium Labsy'),
('8', 'LAB20250008', '2025-09-07', 'TBC', 'cancelled', '150000.00', 'Linda Kartika', '3175088901234567', 'Sehat', 'Dr. Wahyu Indarto, Sp.P', 'RS Kariadi Semarang', 'RUJ/2025/008', '2025-09-07', 'Suspek TB Paru', 'Dahak BTA, TCM', 'Sari Oktavia', 'Laboratorium Labsy'),
('9', 'LAB20250009', '2025-09-06', 'Kimia Darah', 'selesai', '200000.00', 'Rudi Hermawan', '3175089012345678', 'Sehat', 'Dr. Made Wirawan, Sp.KO', 'RS Sanglah Denpasar', 'RUJ/2025/009', '2025-09-07', 'MCU Profesi', 'Lab lengkap, EKG', 'Sari Oktavia', 'Laboratorium Labsy'),
('10', 'LAB20250010', '2025-09-06', 'Hematologi', 'selesai', '120000.00', 'Dewi Lestari', '3175090123456789', 'Kolesterol tinggi', 'Dr. Yusuf Rahman, Sp.PD', 'RS Pertamina Balikpapan', 'RUJ/2025/010', '2025-09-07', 'Dislipidemia', 'Profil lipid, HbA1c', 'Sari Oktavia', 'Laboratorium Labsy'),
('11', 'LAB20250011', '2025-09-05', 'Urinologi', 'selesai', '85000.00', 'John Doe', '1234567890123456', 'Tidak ada riwayat penyakit serius', 'Dr. Ahmad Wijaya, Sp.PD', 'RS Umum Daerah Jakarta', 'RUJ/2025/001234', '2025-08-27', 'Suspek Diabetes Mellitus', 'Gula darah puasa, Gula darah 2 jam PP, HbA1c', 'Sari Oktavia', 'Laboratorium Labsy'),
('12', 'LAB20250012', '2025-09-17', 'Kimia Darah', 'selesai', '423000.00', 'Rina Sari', '3175084567890121', 'Sehat', 'Dr. Retno Wulan, Sp.OG', 'RS Ibu dan Anak Yogya', 'RUJ/2025/004', '2025-09-07', 'MCU Pranikah', 'Lab lengkap, TORCH', 'Sari Oktavia', NULL);
UNLOCK TABLES;

-- Table structure for table `v_user_details`
DROP TABLE IF EXISTS `v_user_details`;
;

-- Dumping data for table `v_user_details`
LOCK TABLES `v_user_details` WRITE;
INSERT INTO `v_user_details` (`user_id`, `username`, `role`, `is_active`, `created_at`, `nama_lengkap`) VALUES
('4', 'lab_sari', 'petugas_lab', '1', '2025-08-28 17:41:13', 'Sari Oktavia'),
('9', 'ocha', 'petugas_lab', '1', '2025-09-16 13:34:40', 'ochaaaaa'),
('2', 'admin_front', 'administrasi', '1', '2025-08-28 17:41:13', 'Admin Front Office'),
('7', 'Firdaus', 'administrasi', '1', '2025-09-12 20:01:18', 'Firdaus'),
('1', 'superadmin', 'admin', '1', '2025-08-28 17:41:13', 'Super Administrator'),
('3', 'dr_budi', 'dokter', '0', '2025-08-28 17:41:13', NULL);
UNLOCK TABLES;

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;
