-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 25, 2025 at 02:41 PM
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
-- Database: `labsy`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetDailyInventoryReport` (IN `report_date` DATE)   BEGIN
    SELECT 
        'Inventory Summary' as category,
        (SELECT COUNT(*) FROM alat_laboratorium) as total_alat,
        (SELECT COUNT(*) FROM reagen) as total_reagen,
        (SELECT COUNT(*) FROM v_inventory_status WHERE alert_level IN ('Warning', 'Urgent', 'Low Stock')) as items_need_attention,
        (SELECT COUNT(*) FROM reagen WHERE expired_date <= DATE_ADD(report_date, INTERVAL 30 DAY) AND expired_date >= report_date) as expiring_soon,
        (SELECT COUNT(*) FROM alat_laboratorium WHERE jadwal_kalibrasi <= DATE_ADD(report_date, INTERVAL 30 DAY) AND jadwal_kalibrasi >= report_date) as calibration_due;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetDailyLabStats` (IN `target_date` DATE)   BEGIN
    SELECT 
        'Today Statistics' as category,
        SUM(CASE WHEN status_pemeriksaan = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status_pemeriksaan = 'progress' THEN 1 ELSE 0 END) as in_progress,
        SUM(CASE WHEN status_pemeriksaan = 'selesai' AND DATE(completed_at) = target_date THEN 1 ELSE 0 END) as completed_today,
        COUNT(*) as total_examinations
    FROM pemeriksaan_lab 
    WHERE DATE(tanggal_pemeriksaan) = target_date;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetItemsNeedingAttention` ()   BEGIN
    -- Items with urgent alerts
    SELECT 
        'urgent' as priority,
        tipe_inventory,
        kode_unik,
        nama_item,
        status,
        alert_level,
        CASE 
            WHEN tipe_inventory = 'reagen' AND expired_date IS NOT NULL 
            THEN CONCAT('Expires in ', DATEDIFF(expired_date, CURDATE()), ' days')
            WHEN tipe_inventory = 'alat' AND jadwal_kalibrasi IS NOT NULL
            THEN CONCAT('Calibration due in ', DATEDIFF(expired_date, CURDATE()), ' days')
            ELSE alert_level
        END as description
    FROM v_inventory_status 
    WHERE alert_level = 'Urgent'
    
    UNION ALL
    
    -- Items with warning alerts
    SELECT 
        'warning' as priority,
        tipe_inventory,
        kode_unik,
        nama_item,
        status,
        alert_level,
        CASE 
            WHEN tipe_inventory = 'reagen' AND expired_date IS NOT NULL 
            THEN CONCAT('Expires in ', DATEDIFF(expired_date, CURDATE()), ' days')
            WHEN tipe_inventory = 'alat' AND jadwal_kalibrasi IS NOT NULL
            THEN CONCAT('Calibration due in ', DATEDIFF(expired_date, CURDATE()), ' days')
            ELSE alert_level
        END as description
    FROM v_inventory_status 
    WHERE alert_level IN ('Warning', 'Low Stock')
    
    ORDER BY 
        CASE priority 
            WHEN 'urgent' THEN 1 
            WHEN 'warning' THEN 2 
            ELSE 3 
        END,
        nama_item;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_calculate_examination_cost` (IN `p_pemeriksaan_id` INT, OUT `p_total_biaya` DECIMAL(10,2))   BEGIN
    DECLARE v_jenis_pemeriksaan VARCHAR(100);
    
    SELECT jenis_pemeriksaan INTO v_jenis_pemeriksaan
    FROM pemeriksaan_lab
    WHERE pemeriksaan_id = p_pemeriksaan_id;
    
    SET p_total_biaya = 0;
    
    CASE LOWER(TRIM(v_jenis_pemeriksaan))
        WHEN 'tbc' THEN
            SELECT IFNULL(total_harga_tbc, 0) INTO p_total_biaya
            FROM tbc WHERE pemeriksaan_id = p_pemeriksaan_id;
        WHEN 'kimia darah' THEN
            SELECT IFNULL(total_harga_kimia, 0) INTO p_total_biaya
            FROM kimia_darah WHERE pemeriksaan_id = p_pemeriksaan_id;
        WHEN 'hematologi' THEN
            SELECT IFNULL(total_harga_hematologi, 0) INTO p_total_biaya
            FROM hematologi WHERE pemeriksaan_id = p_pemeriksaan_id;
        WHEN 'urinologi' THEN
            SELECT IFNULL(total_harga_urinologi, 0) INTO p_total_biaya
            FROM urinologi WHERE pemeriksaan_id = p_pemeriksaan_id;
        WHEN 'urine' THEN
            SELECT IFNULL(total_harga_urinologi, 0) INTO p_total_biaya
            FROM urinologi WHERE pemeriksaan_id = p_pemeriksaan_id;
        WHEN 'serologi' THEN
            SELECT IFNULL(total_harga_serologi, 0) INTO p_total_biaya
            FROM serologi_imunologi WHERE pemeriksaan_id = p_pemeriksaan_id;
        WHEN 'serologi imunologi' THEN
            SELECT IFNULL(total_harga_serologi, 0) INTO p_total_biaya
            FROM serologi_imunologi WHERE pemeriksaan_id = p_pemeriksaan_id;
        WHEN 'ims' THEN
            SELECT IFNULL(total_harga_ims, 0) INTO p_total_biaya
            FROM ims WHERE pemeriksaan_id = p_pemeriksaan_id;
        ELSE
            SET p_total_biaya = 0;
    END CASE;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_check_sampel_expired` ()   BEGIN
    -- Get all samples yang akan/sudah expired
    SELECT 
        ps.sampel_id,
        ps.jenis_sampel,
        pl.nomor_pemeriksaan,
        p.nama AS nama_pasien,
        ps.tanggal_pengambilan,
        DATE_ADD(ps.tanggal_pengambilan, INTERVAL se.masa_berlaku_hari DAY) AS tanggal_kadaluarsa,
        DATEDIFF(DATE_ADD(ps.tanggal_pengambilan, INTERVAL se.masa_berlaku_hari DAY), NOW()) AS hari_tersisa,
        ss.lokasi_penyimpanan
    FROM pemeriksaan_sampel ps
    LEFT JOIN sampel_storage ss ON ps.sampel_id = ss.sampel_id AND ss.status_penyimpanan = 'tersimpan'
    LEFT JOIN sampel_expiry se ON ps.jenis_sampel = se.jenis_sampel
    LEFT JOIN pemeriksaan_lab pl ON ps.pemeriksaan_id = pl.pemeriksaan_id
    LEFT JOIN pasien p ON pl.pasien_id = p.pasien_id
    WHERE ps.tanggal_pengambilan IS NOT NULL
    AND DATE_ADD(ps.tanggal_pengambilan, INTERVAL se.masa_berlaku_hari DAY) <= DATE_ADD(NOW(), INTERVAL 2 DAY)
    AND ss.status_penyimpanan = 'tersimpan'
    ORDER BY tanggal_kadaluarsa ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_qc_summary` (IN `p_start_date` DATE, IN `p_end_date` DATE)   BEGIN
    SELECT 
        COUNT(DISTINCT qc.alat_id) as total_alat_tested,
        COUNT(*) as total_qc_performed,
        SUM(CASE WHEN qc.hasil_qc = 'Passed' THEN 1 ELSE 0 END) as total_passed,
        SUM(CASE WHEN qc.hasil_qc = 'Failed' THEN 1 ELSE 0 END) as total_failed,
        SUM(CASE WHEN qc.hasil_qc = 'Conditional' THEN 1 ELSE 0 END) as total_conditional,
        ROUND(AVG(CASE WHEN qc.hasil_qc = 'Passed' THEN 100 ELSE 0 END), 2) as pass_rate,
        COUNT(DISTINCT qc.teknisi) as total_teknisi
    FROM quality_control qc
    WHERE qc.tanggal_qc BETWEEN p_start_date AND p_end_date
    AND qc.status = 'Active';
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_terima_sampel_dan_storage` (IN `p_sampel_id` INT, IN `p_petugas_id` INT, IN `p_kondisi_sampel` JSON, IN `p_lokasi_storage` VARCHAR(100), IN `p_volume` DECIMAL(10,2), IN `p_catatan_kondisi` TEXT)   BEGIN
    DECLARE v_pemeriksaan_id INT;
    DECLARE v_jenis_sampel VARCHAR(50);
    DECLARE v_masa_berlaku INT;
    DECLARE v_suhu_optimal DECIMAL(5,2);
    
    -- Ambil data sampel
    SELECT pemeriksaan_id, jenis_sampel 
    INTO v_pemeriksaan_id, v_jenis_sampel
    FROM pemeriksaan_sampel
    WHERE sampel_id = p_sampel_id;
    
    -- Ambil parameter penyimpanan
    SELECT masa_berlaku_hari, suhu_optimal_min
    INTO v_masa_berlaku, v_suhu_optimal
    FROM sampel_expiry
    WHERE jenis_sampel = v_jenis_sampel;
    
    -- Update status sampel
    UPDATE pemeriksaan_sampel
    SET status_sampel = 'diterima',
        tanggal_evaluasi = NOW(),
        petugas_evaluasi_id = p_petugas_id,
        kondisi_sampel = p_kondisi_sampel,
        catatan_kondisi = p_catatan_kondisi
    WHERE sampel_id = p_sampel_id;
    
    -- Insert ke storage
    INSERT INTO sampel_storage (
        sampel_id,
        lokasi_penyimpanan,
        suhu_penyimpanan,
        tanggal_masuk,
        status_penyimpanan,
        volume_sampel,
        satuan_volume,
        petugas_id,
        keterangan
    ) VALUES (
        p_sampel_id,
        p_lokasi_storage,
        v_suhu_optimal,
        NOW(),
        'tersimpan',
        p_volume,
        CASE v_jenis_sampel
            WHEN 'whole_blood' THEN 'ml'
            WHEN 'serum' THEN 'ml'
            WHEN 'plasma' THEN 'ml'
            WHEN 'urin' THEN 'ml'
            WHEN 'feses' THEN 'gram'
            WHEN 'sputum' THEN 'ml'
        END,
        p_petugas_id,
        CONCAT('Stored on acceptance. Valid until: ', 
               DATE_ADD(NOW(), INTERVAL v_masa_berlaku DAY))
    );
    
    -- Insert timeline
    INSERT INTO timeline_progres (
        pemeriksaan_id,
        status,
        keterangan,
        petugas_id
    ) VALUES (
        v_pemeriksaan_id,
        'Sampel Disimpan',
        CONCAT('Sampel ', v_jenis_sampel, ' disimpan di ', p_lokasi_storage),
        p_petugas_id
    );
    
    -- Log activity
    INSERT INTO activity_log (
        user_id,
        activity,
        table_affected,
        record_id
    ) VALUES (
        p_petugas_id,
        CONCAT('Sampel diterima dan disimpan: ', v_jenis_sampel),
        'sampel_storage',
        LAST_INSERT_ID()
    );
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_update_invoice_from_examination` (IN `p_pemeriksaan_id` INT)   BEGIN
    DECLARE v_total_biaya DECIMAL(10,2);
    DECLARE v_invoice_id INT;
    DECLARE v_nomor_invoice VARCHAR(50);
    DECLARE v_max_id INT;
    DECLARE v_tanggal_pemeriksaan DATE;
    
    -- Hitung total biaya menggunakan OUT parameter
    CALL sp_calculate_examination_cost(p_pemeriksaan_id, v_total_biaya);
    
    -- Update biaya di pemeriksaan_lab
    UPDATE pemeriksaan_lab 
    SET biaya = v_total_biaya 
    WHERE pemeriksaan_id = p_pemeriksaan_id;
    
    -- Cek apakah invoice sudah ada
    SELECT invoice_id INTO v_invoice_id
    FROM invoice
    WHERE pemeriksaan_id = p_pemeriksaan_id
    LIMIT 1;
    
    IF v_invoice_id IS NOT NULL THEN
        -- Update invoice yang ada
        UPDATE invoice 
        SET total_biaya = v_total_biaya
        WHERE invoice_id = v_invoice_id;
    ELSE
        -- Buat invoice baru
        SELECT MAX(invoice_id) INTO v_max_id FROM invoice;
        SET v_max_id = IFNULL(v_max_id, 0);
        SET v_nomor_invoice = CONCAT('INV-', YEAR(CURDATE()), '-', LPAD(v_max_id + 1, 4, '0'));
        
        SELECT tanggal_pemeriksaan INTO v_tanggal_pemeriksaan
        FROM pemeriksaan_lab
        WHERE pemeriksaan_id = p_pemeriksaan_id;
        
        INSERT INTO invoice (
            pemeriksaan_id, 
            nomor_invoice, 
            tanggal_invoice, 
            jenis_pembayaran, 
            total_biaya, 
            status_pembayaran,
            created_at
        ) VALUES (
            p_pemeriksaan_id,
            v_nomor_invoice,
            v_tanggal_pemeriksaan,
            'umum',
            v_total_biaya,
            'belum_bayar',
            NOW()
        );
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateExpiredReagentStatus` ()   BEGIN
    DECLARE affected_rows INT DEFAULT 0;
    
    -- Update expired reagents
    UPDATE reagen 
    SET status = 'Kadaluarsa', updated_at = NOW()
    WHERE expired_date < CURDATE() 
    AND status != 'Kadaluarsa';
    
    SET affected_rows = ROW_COUNT();
    
    -- Return result
    SELECT 
        affected_rows as updated_items,
        'Reagents marked as expired' as message,
        NOW() as updated_at;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `activity` varchar(255) NOT NULL,
  `table_affected` varchar(100) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`log_id`, `user_id`, `activity`, `table_affected`, `record_id`, `ip_address`, `created_at`) VALUES
(1690, 4, 'User logged out', 'users', NULL, '::1', '2025-12-09 00:22:09'),
(1691, 7, 'User logged in', 'users', NULL, '::1', '2025-12-09 00:22:21'),
(1692, 7, 'Examination request created: LAB20250001', 'pemeriksaan_lab', 24, '::1', '2025-12-09 00:23:08'),
(1693, 7, 'User logged out', 'users', NULL, '::1', '2025-12-09 00:23:13'),
(1694, 1, 'User logged in', 'users', NULL, '::1', '2025-12-09 00:23:47'),
(1695, 1, 'Mengakses dashboard', 'system', NULL, '::1', '2025-12-09 00:23:47'),
(1696, 1, 'Pengguna baru ditambahkan: supervisor', 'users', 11, '::1', '2025-12-09 00:24:11'),
(1697, 1, 'User logged out', 'users', NULL, '::1', '2025-12-09 00:24:20'),
(1698, 4, 'User logged in', 'users', NULL, '::1', '2025-12-09 00:24:30'),
(1699, 4, 'Lab request accepted', 'pemeriksaan_lab', 24, '::1', '2025-12-09 00:24:53'),
(1700, 4, 'Multiple examination results saved (0 types)', 'pemeriksaan_lab', 24, '::1', '2025-12-09 00:41:45'),
(1701, 4, 'Multiple examination results saved (0 types)', 'pemeriksaan_lab', 24, '::1', '2025-12-09 00:43:23'),
(1702, 4, 'Multiple examination results saved (0 types)', 'pemeriksaan_lab', 24, '::1', '2025-12-09 00:46:15'),
(1703, 4, 'Multiple examination results saved (0 types)', 'pemeriksaan_lab', 24, '::1', '2025-12-09 00:46:16'),
(1704, 4, 'User logged out', 'users', NULL, '::1', '2025-12-09 00:52:18'),
(1705, 7, 'User logged in', 'users', NULL, '::1', '2025-12-09 00:52:24'),
(1706, 7, 'Examination request created: LAB20250002', 'pemeriksaan_lab', 25, '::1', '2025-12-09 00:52:48'),
(1707, 7, 'User logged out', 'users', NULL, '::1', '2025-12-09 00:52:51'),
(1708, 4, 'User logged in', 'users', NULL, '::1', '2025-12-09 00:53:11'),
(1709, 4, 'Lab request accepted', 'pemeriksaan_lab', 25, '::1', '2025-12-09 00:53:39'),
(1710, 7, 'User logged in', 'users', NULL, '::1', '2025-12-09 00:54:48'),
(1711, 7, 'Examination request created: LAB20250003', 'pemeriksaan_lab', 26, '::1', '2025-12-09 00:55:20'),
(1712, 4, 'Lab request accepted', 'pemeriksaan_lab', 26, '::1', '2025-12-09 00:55:35'),
(1713, 4, 'Multiple examination results saved (0 types)', 'pemeriksaan_lab', 26, '::1', '2025-12-09 03:06:32'),
(1714, 4, 'User logged in', 'users', NULL, '::1', '2025-12-10 07:27:14'),
(1715, 4, 'User logged in', 'users', NULL, '::1', '2025-12-10 10:37:43'),
(1716, 7, 'User logged in', 'users', NULL, '::1', '2025-12-11 03:10:13'),
(1717, 7, 'User logged out', 'users', NULL, '::1', '2025-12-11 03:10:18'),
(1718, 4, 'User logged in', 'users', NULL, '::1', '2025-12-11 03:10:22'),
(1719, 4, 'User logged in', 'users', NULL, '::1', '2025-12-11 13:28:58'),
(1720, 4, 'User logged in', 'users', NULL, '::1', '2025-12-12 08:35:11'),
(1721, 4, 'Multiple examination results saved (2 types)', 'pemeriksaan_lab', 24, '::1', '2025-12-12 09:04:03'),
(1722, 4, 'User logged out', 'users', NULL, '::1', '2025-12-12 09:04:17'),
(1723, 11, 'User logged in', 'users', NULL, '::1', '2025-12-12 09:04:34'),
(1724, 11, 'User logged out', 'users', NULL, '::1', '2025-12-12 09:12:44'),
(1725, 4, 'User logged in', 'users', NULL, '::1', '2025-12-12 09:12:50'),
(1726, 4, 'User logged in', 'users', NULL, '::1', '2025-12-12 22:40:55'),
(1727, 4, 'User logged out', 'users', NULL, '::1', '2025-12-12 23:47:32'),
(1728, 4, 'User logged in', 'users', NULL, '::1', '2025-12-13 00:08:24'),
(1729, 4, 'User logged in', 'users', NULL, '::1', '2025-12-13 04:02:59'),
(1730, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-13 07:18:53'),
(1731, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-13 09:34:01'),
(1732, 4, 'Sample collected', 'pemeriksaan_sampel', 3, '::1', '2025-12-13 11:22:54'),
(1733, 4, 'Sample accepted with conditions', 'pemeriksaan_sampel', 3, '::1', '2025-12-13 11:27:07'),
(1734, 4, 'Bulk conditions added: 4 conditions', 'kondisi_sampel', 3, '::1', '2025-12-13 11:27:44'),
(1735, 4, 'User logged out', 'users', NULL, '::1', '2025-12-13 11:45:15'),
(1736, 7, 'User logged in', 'users', NULL, '::1', '2025-12-13 11:45:21'),
(1737, 7, 'Examination request created: LAB20250004', 'pemeriksaan_lab', 27, '::1', '2025-12-13 11:45:53'),
(1738, 7, 'Examination request created: LAB20250005', 'pemeriksaan_lab', 28, '::1', '2025-12-13 11:46:28'),
(1739, 7, 'User logged out', 'users', NULL, '::1', '2025-12-13 11:46:31'),
(1740, 4, 'User logged in', 'users', NULL, '::1', '2025-12-13 11:46:39'),
(1741, 4, 'Lab request accepted', 'pemeriksaan_lab', 28, '::1', '2025-12-13 11:46:56'),
(1742, 4, 'Lab request accepted', 'pemeriksaan_lab', 27, '::1', '2025-12-13 11:46:59'),
(1743, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-13 11:48:09'),
(1744, 4, 'User logged out', 'users', NULL, '::1', '2025-12-13 12:29:35'),
(1745, 11, 'User logged in', 'users', NULL, '::1', '2025-12-13 12:29:41'),
(1746, 11, 'User logged in', 'users', NULL, '::1', '2025-12-13 23:47:55'),
(1747, 11, 'Examination validated by supervisor', 'pemeriksaan_lab', 24, '::1', '2025-12-14 00:29:22'),
(1748, 11, 'User logged in', 'users', NULL, '::1', '2025-12-14 04:22:45'),
(1749, 11, 'User logged out', 'users', NULL, '::1', '2025-12-14 04:34:41'),
(1750, 4, 'User logged in', 'users', NULL, '::1', '2025-12-14 04:34:50'),
(1751, 4, 'Lab results saved: tbc', 'pemeriksaan_lab', 28, '::1', '2025-12-14 04:35:41'),
(1752, 4, 'Sample collected', 'pemeriksaan_sampel', 5, '::1', '2025-12-14 04:35:50'),
(1753, 4, 'Sample accepted with conditions', 'pemeriksaan_sampel', 5, '::1', '2025-12-14 04:36:01'),
(1754, 4, 'User logged out', 'users', NULL, '::1', '2025-12-14 04:36:07'),
(1755, 11, 'User logged in', 'users', NULL, '::1', '2025-12-14 04:36:19'),
(1756, 11, 'Validasi hasil pemeriksaan', 'pemeriksaan_lab', 28, '::1', '2025-12-14 04:37:33'),
(1757, 4, 'User logged in', 'users', NULL, '::1', '2025-12-14 04:46:07'),
(1758, 4, 'User logged in', 'users', NULL, '::1', '2025-12-14 08:16:54'),
(1759, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-14 08:34:36'),
(1760, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-14 08:34:49'),
(1761, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-14 08:34:57'),
(1762, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-14 08:36:49'),
(1763, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-14 08:40:31'),
(1764, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-14 08:40:42'),
(1765, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-14 08:40:44'),
(1766, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-14 08:40:48'),
(1767, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-14 08:41:08'),
(1768, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-14 08:48:36'),
(1769, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-14 08:49:30'),
(1770, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-14 09:24:05'),
(1771, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-14 09:24:16'),
(1772, 4, 'User logged in', 'users', NULL, '::1', '2025-12-19 10:59:41'),
(1773, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-19 10:59:44'),
(1774, 4, 'User logged out', 'users', NULL, '::1', '2025-12-19 13:31:01'),
(1775, 11, 'User logged in', 'users', NULL, '::1', '2025-12-19 13:31:08'),
(1776, 11, 'Validasi hasil pemeriksaan', 'pemeriksaan_lab', 26, '::1', '2025-12-19 13:40:24'),
(1777, 4, 'User logged in', 'users', NULL, '::1', '2025-12-19 13:51:20'),
(1778, 11, 'Validasi hasil pemeriksaan', 'pemeriksaan_lab', 27, '::1', '2025-12-19 13:52:49'),
(1779, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-19 13:53:36'),
(1780, 4, 'Lab results saved: urinologi', 'pemeriksaan_lab', 25, '::1', '2025-12-19 14:13:18'),
(1781, 4, 'Sample collected', 'pemeriksaan_sampel', 2, '::1', '2025-12-19 14:13:39'),
(1782, 4, 'Sample accepted with conditions', 'pemeriksaan_sampel', 2, '::1', '2025-12-19 14:13:48'),
(1783, 11, 'Validasi hasil pemeriksaan', 'pemeriksaan_lab', 25, '::1', '2025-12-19 14:15:39'),
(1784, 4, 'User logged out', 'users', NULL, '::1', '2025-12-19 14:30:57'),
(1785, 7, 'User logged in', 'users', NULL, '::1', '2025-12-19 14:31:00'),
(1786, 7, 'Examination request created: LAB20250006', 'pemeriksaan_lab', 29, '::1', '2025-12-19 14:31:53'),
(1787, 4, 'User logged in', 'users', NULL, '::1', '2025-12-19 14:32:39'),
(1788, 4, 'Lab request accepted', 'pemeriksaan_lab', 29, '::1', '2025-12-19 14:32:46'),
(1789, 4, 'Sample collected', 'pemeriksaan_sampel', 6, '::1', '2025-12-19 14:32:58'),
(1790, 4, 'Sample collected', 'pemeriksaan_sampel', 7, '::1', '2025-12-19 14:33:01'),
(1791, 4, 'Sample accepted with conditions', 'pemeriksaan_sampel', 6, '::1', '2025-12-19 14:33:08'),
(1792, 4, 'Sample accepted with conditions', 'pemeriksaan_sampel', 7, '::1', '2025-12-19 14:33:19'),
(1793, 4, 'Lab results saved: kimia_darah', 'pemeriksaan_lab', 29, '::1', '2025-12-19 14:33:56'),
(1794, 11, 'Validasi hasil pemeriksaan', 'pemeriksaan_lab', 29, '::1', '2025-12-19 14:34:19'),
(1795, 7, 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-12-19 14:34:59'),
(1796, 11, 'User logged out', 'users', NULL, '::1', '2025-12-19 14:37:24'),
(1797, 1, 'User logged in', 'users', NULL, '::1', '2025-12-19 14:37:29'),
(1798, 1, 'Mengakses dashboard', 'system', NULL, '::1', '2025-12-19 14:37:29'),
(1799, 1, 'Pengguna dinonaktifkan: lab_sari', 'users', 4, '::1', '2025-12-19 14:37:41'),
(1800, 1, 'Pengguna diaktifkan: lab_sari', 'users', 4, '::1', '2025-12-19 14:37:46'),
(1801, 1, 'Pengguna dinonaktifkan: lab_sari', 'users', 4, '::1', '2025-12-19 14:45:15'),
(1802, 1, 'Pengguna diaktifkan: lab_sari', 'users', 4, '::1', '2025-12-19 14:45:18'),
(1803, 1, 'Pengguna dinonaktifkan: lab_sari', 'users', 4, '::1', '2025-12-19 14:45:20'),
(1804, 1, 'Pengguna diaktifkan: lab_sari', 'users', 4, '::1', '2025-12-19 14:45:25'),
(1805, 1, 'Pengguna dinonaktifkan: lab_sari', 'users', 4, '::1', '2025-12-19 14:45:27'),
(1806, 1, 'Pengguna diaktifkan: lab_sari', 'users', 4, '::1', '2025-12-19 14:45:28'),
(1807, 1, 'Pengguna dinonaktifkan: lab_sari', 'users', 4, '::1', '2025-12-19 14:45:30'),
(1808, 1, 'Pengguna diaktifkan: lab_sari', 'users', 4, '::1', '2025-12-19 14:45:32'),
(1809, 1, 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-12-19 14:45:44'),
(1810, 1, 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-12-19 14:45:46'),
(1811, 1, 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-12-19 14:45:55'),
(1812, 1, 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-12-19 14:45:59'),
(1813, 1, 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-12-19 14:46:02'),
(1814, 1, 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-12-19 14:46:03'),
(1815, 1, 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-12-19 14:46:05'),
(1816, 1, 'Pengguna dinonaktifkan: supervisor', 'users', 11, '::1', '2025-12-19 14:46:21'),
(1817, 1, 'Pengguna diaktifkan: supervisor', 'users', 11, '::1', '2025-12-19 14:46:24'),
(1818, 1, 'Pengguna dinonaktifkan: supervisor', 'users', 11, '::1', '2025-12-19 14:51:35'),
(1819, 1, 'Pengguna diaktifkan: supervisor', 'users', 11, '::1', '2025-12-19 14:51:37'),
(1820, 1, 'Pasien dihapus: John Doe', 'pasien', 1, '::1', '2025-12-19 14:54:35'),
(1821, 1, 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-12-19 14:54:54'),
(1822, 1, 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-12-19 14:55:09'),
(1823, 1, 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-12-19 14:55:12'),
(1824, 1, 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-12-19 15:06:48'),
(1825, 1, 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-12-19 15:10:53'),
(1826, 1, 'Mengakses dashboard', 'system', NULL, '::1', '2025-12-19 15:10:55'),
(1827, 1, 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-12-19 15:11:04'),
(1828, 1, 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-12-19 15:15:52'),
(1829, 1, 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-12-19 15:15:56'),
(1830, 1, 'Mengakses dashboard', 'system', NULL, '::1', '2025-12-19 15:18:34'),
(1831, 1, 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-12-19 15:19:01'),
(1832, 1, 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-12-19 15:19:02'),
(1833, 1, 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-12-19 15:20:22'),
(1834, 1, 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-12-19 15:20:33'),
(1835, 1, 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-12-19 15:22:11'),
(1836, 1, 'Item inventory dihapus: Reagen Asam Urat', 'reagen', 4, '::1', '2025-12-19 15:22:28'),
(1837, 1, 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-12-19 15:23:03'),
(1838, 1, 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-12-19 15:23:17'),
(1839, 1, 'Mengakses halaman backup database', 'system', NULL, '::1', '2025-12-19 15:23:20'),
(1840, 1, 'File backup didownload: backup_2025-09-16T11-39-34.sql', 'system', NULL, '::1', '2025-12-19 15:23:25'),
(1841, 1, 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-12-19 15:23:29'),
(1842, 1, 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-12-19 15:23:54'),
(1843, 1, 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-12-19 15:31:40'),
(1846, 1, 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-12-19 15:33:56'),
(1848, 1, 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-12-19 15:33:59'),
(1849, 1, 'Log aktivitas dihapus', 'activity_log', 1847, '::1', '2025-12-19 15:34:04'),
(1850, 1, 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-12-19 15:34:04'),
(1851, 1, 'Log aktivitas dihapus', 'activity_log', 1844, '::1', '2025-12-19 15:34:08'),
(1853, 1, 'Log aktivitas lama dibersihkan (lebih dari 30 hari)', 'activity_log', NULL, '::1', '2025-12-19 15:34:17'),
(1854, 1, 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-12-19 15:34:17'),
(1855, 1, 'Log aktivitas lama dibersihkan (lebih dari 180 hari)', 'activity_log', NULL, '::1', '2025-12-19 15:34:30'),
(1856, 1, 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-12-19 15:34:30'),
(1857, 1, 'Log aktivitas dihapus', 'activity_log', 1852, '::1', '2025-12-19 15:34:39'),
(1859, 1, 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-12-19 15:37:33'),
(1860, 1, 'Log aktivitas dihapus', 'activity_log', 1858, '::1', '2025-12-19 15:37:37'),
(1861, 1, 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-12-19 15:37:37'),
(1862, 1, 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-12-19 15:37:43'),
(1863, 1, 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-12-19 15:37:49'),
(1864, 1, 'Mengakses halaman backup database', 'system', NULL, '::1', '2025-12-19 15:38:28'),
(1865, 1, 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-12-19 15:38:31'),
(1866, 1, 'Hasil pemeriksaan dicetak: LAB20250002', 'pemeriksaan_lab', 25, '::1', '2025-12-19 15:38:46'),
(1867, 1, 'Hasil pemeriksaan dicetak: LAB20250004', 'pemeriksaan_lab', 27, '::1', '2025-12-19 15:39:08'),
(1868, 1, 'Hasil pemeriksaan dicetak: LAB20250003', 'pemeriksaan_lab', 26, '::1', '2025-12-19 15:39:19'),
(1869, 1, 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-12-19 15:44:54'),
(1870, 1, 'Mengakses halaman backup database', 'system', NULL, '::1', '2025-12-19 15:44:56'),
(1871, 1, 'File backup didownload: backup_2025-09-12T18-35-45.sql', 'system', NULL, '::1', '2025-12-19 15:45:10'),
(1872, 1, 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-12-19 15:48:39'),
(1873, 1, 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-12-19 15:48:42'),
(1874, 1, 'Mengakses dashboard', 'system', NULL, '::1', '2025-12-19 15:48:50'),
(1875, 1, 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-12-19 15:48:55'),
(1876, 1, 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-12-19 15:49:01'),
(1877, 1, 'Mengakses halaman backup database', 'system', NULL, '::1', '2025-12-19 15:49:03'),
(1878, 1, 'File backup dihapus: backup_2025-09-16T11-39-34.sql', 'system', NULL, '::1', '2025-12-19 15:49:12'),
(1879, 1, 'File backup dihapus: backup_2025-09-12T18-35-45.sql', 'system', NULL, '::1', '2025-12-19 15:49:14'),
(1880, 1, 'File backup dihapus: backup_2025-09-12T18-18-10.sql', 'system', NULL, '::1', '2025-12-19 15:49:17'),
(1881, 1, 'File backup didownload: Database.sql', 'system', NULL, '::1', '2025-12-19 15:49:18'),
(1882, 1, 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-12-19 15:49:22'),
(1883, 1, 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-12-19 15:49:49'),
(1884, 1, 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-12-19 15:49:51'),
(1885, 1, 'Hasil pemeriksaan dicetak: LAB20250002', 'pemeriksaan_lab', 25, '::1', '2025-12-19 15:50:01'),
(1886, 1, 'Hasil pemeriksaan dicetak: LAB20250004', 'pemeriksaan_lab', 27, '::1', '2025-12-19 15:51:14'),
(1887, 1, 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-12-19 15:57:16'),
(1888, 1, 'Hasil pemeriksaan dicetak: LAB20250004', 'pemeriksaan_lab', 27, '::1', '2025-12-19 15:57:18'),
(1889, 1, 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-12-19 16:50:56'),
(1890, 1, 'Hasil pemeriksaan dicetak: LAB20250004', 'pemeriksaan_lab', 27, '::1', '2025-12-19 16:51:01'),
(1891, 1, 'Hasil pemeriksaan dicetak: LAB20250004', 'pemeriksaan_lab', 27, '::1', '2025-12-19 16:52:34'),
(1892, 1, 'Hasil pemeriksaan dicetak: LAB20250003', 'pemeriksaan_lab', 26, '::1', '2025-12-19 16:52:49'),
(1893, 1, 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-12-19 16:52:57'),
(1894, 1, 'Mengakses dashboard', 'system', NULL, '::1', '2025-12-19 16:53:26'),
(1895, 1, 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-12-19 16:54:54'),
(1896, 1, 'Mengakses dashboard', 'system', NULL, '::1', '2025-12-19 16:55:00'),
(1897, 1, 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-12-19 16:55:01'),
(1898, 1, 'User logged out', 'users', NULL, '::1', '2025-12-19 16:55:06'),
(1899, 7, 'User logged in', 'users', NULL, '::1', '2025-12-19 16:55:12'),
(1900, 7, 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-12-19 16:56:55'),
(1901, 7, 'Hasil pemeriksaan dicetak: LAB20250002', 'pemeriksaan_lab', 25, '::1', '2025-12-19 16:56:57'),
(1902, 7, 'Hasil pemeriksaan dicetak: LAB20250004', 'pemeriksaan_lab', 27, '::1', '2025-12-19 16:57:00'),
(1903, 7, 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-12-19 17:05:02'),
(1904, 7, 'User logged out', 'users', NULL, '::1', '2025-12-19 17:13:13'),
(1905, 1, 'User logged in', 'users', NULL, '::1', '2025-12-19 17:13:19'),
(1906, 1, 'Mengakses dashboard', 'system', NULL, '::1', '2025-12-19 17:13:19'),
(1907, 1, 'Mengakses dashboard', 'system', NULL, '::1', '2025-12-19 17:20:02'),
(1908, 1, 'User logged out', 'users', NULL, '::1', '2025-12-19 17:20:07'),
(1909, 7, 'User logged in', 'users', NULL, '::1', '2025-12-19 17:20:20'),
(1910, 7, 'Patient created: Vewe', 'pasien', 21, '::1', '2025-12-19 17:52:06'),
(1911, 7, 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-12-19 17:52:36'),
(1912, 7, 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-12-19 17:52:50'),
(1913, 7, 'Hasil pemeriksaan dicetak: LAB20250004', 'pemeriksaan_lab', 27, '::1', '2025-12-19 17:52:53'),
(1914, 7, 'User logged out', 'users', NULL, '::1', '2025-12-19 17:53:04'),
(1915, 4, 'User logged in', 'users', NULL, '::1', '2025-12-19 17:53:12'),
(1916, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-19 17:53:28'),
(1917, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-19 17:53:59'),
(1918, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-19 17:55:22'),
(1919, 4, 'User logged out', 'users', NULL, '::1', '2025-12-19 17:55:25'),
(1920, 7, 'User logged in', 'users', NULL, '::1', '2025-12-19 17:55:32'),
(1921, 7, 'Examination request created: LAB20250007', 'pemeriksaan_lab', 30, '::1', '2025-12-19 17:56:57'),
(1922, 7, 'User logged out', 'users', NULL, '::1', '2025-12-19 17:57:02'),
(1923, 4, 'User logged in', 'users', NULL, '::1', '2025-12-19 17:57:09'),
(1924, 4, 'Lab request accepted', 'pemeriksaan_lab', 30, '::1', '2025-12-19 17:57:15'),
(1925, 4, 'User logged in', 'users', NULL, '127.0.0.1', '2025-12-20 10:05:34'),
(1926, 4, 'Sample collected', 'pemeriksaan_sampel', 8, '::1', '2025-12-20 11:53:57'),
(1927, 4, 'Sample accepted with conditions', 'pemeriksaan_sampel', 8, '::1', '2025-12-20 11:54:05'),
(1928, 4, 'Sample collected', 'pemeriksaan_sampel', 9, '::1', '2025-12-20 11:55:10'),
(1929, 4, 'Sample accepted with conditions', 'pemeriksaan_sampel', 9, '::1', '2025-12-20 11:55:17'),
(1930, 4, 'User logged in', 'users', NULL, '::1', '2025-12-20 17:38:25'),
(1931, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-20 17:38:29'),
(1932, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-20 17:42:59'),
(1933, 4, 'Bulk conditions added: 2 conditions', 'kondisi_sampel', 8, '::1', '2025-12-20 17:48:33'),
(1934, 1, 'Sampel auto-stored: plasma', 'sampel_storage', 1, '::1', '2025-12-21 04:35:28'),
(1935, 4, 'User logged in', 'users', NULL, '::1', '2025-12-21 04:52:32'),
(1936, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 04:52:34'),
(1937, 4, 'User logged out', 'users', NULL, '::1', '2025-12-21 04:55:19'),
(1938, 7, 'User logged in', 'users', NULL, '::1', '2025-12-21 04:55:30'),
(1939, 7, 'Examination request created: LAB20250008', 'pemeriksaan_lab', 31, '::1', '2025-12-21 04:56:29'),
(1940, 7, 'User logged out', 'users', NULL, '::1', '2025-12-21 04:56:36'),
(1941, 4, 'User logged in', 'users', NULL, '::1', '2025-12-21 04:56:46'),
(1942, 4, 'Lab request accepted', 'pemeriksaan_lab', 31, '::1', '2025-12-21 04:56:50'),
(1943, 4, 'Sample collected', 'pemeriksaan_sampel', 10, '::1', '2025-12-21 04:57:00'),
(1944, 1, 'Sampel auto-stored: whole_blood', 'sampel_storage', 2, '::1', '2025-12-21 04:57:12'),
(1945, 4, 'Sample accepted with conditions', 'pemeriksaan_sampel', 10, '::1', '2025-12-21 04:57:12'),
(1946, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 04:57:15'),
(1947, 4, 'Storage status updated to: tersimpan', 'sampel_storage', 1, '::1', '2025-12-21 04:59:32'),
(1948, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 04:59:52'),
(1949, 4, 'Kalibrasi Refrigerator selesai. Jadwal berikutnya: 25/12/2025', 'alat_laboratorium', 9, '::1', '2025-12-21 05:00:07'),
(1950, 4, 'User logged in', 'users', NULL, '::1', '2025-12-21 07:06:29'),
(1951, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 07:06:33'),
(1952, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 07:06:35'),
(1953, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 07:07:01'),
(1954, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 07:14:22'),
(1955, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 07:16:40'),
(1956, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 07:19:16'),
(1957, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 07:19:19'),
(1958, 4, 'Kalibrasi Water Bath selesai. Jadwal berikutnya: 24/12/2025', 'alat_laboratorium', 8, '::1', '2025-12-21 07:19:35'),
(1959, 4, 'QC performed: Water Bath - Passed', 'quality_control', 1, '::1', '2025-12-21 07:21:54'),
(1960, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 07:23:57'),
(1961, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 07:23:59'),
(1962, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 07:30:10'),
(1963, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 07:30:20'),
(1964, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 07:30:36'),
(1965, 4, 'User logged out', 'users', NULL, '::1', '2025-12-21 07:30:40'),
(1966, 11, 'User logged in', 'users', NULL, '::1', '2025-12-21 07:30:56'),
(1967, 11, 'User logged out', 'users', NULL, '::1', '2025-12-21 07:42:52'),
(1968, 4, 'User logged in', 'users', NULL, '::1', '2025-12-21 07:43:06'),
(1969, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 07:43:30'),
(1970, 4, 'Sample status updated to progress', 'pemeriksaan_lab', 31, '::1', '2025-12-21 07:43:53'),
(1971, 4, 'Sample status updated to progress', 'pemeriksaan_lab', 31, '::1', '2025-12-21 07:50:51'),
(1972, 4, 'Sample status updated to progress', 'pemeriksaan_lab', 31, '::1', '2025-12-21 07:54:33'),
(1973, 4, 'Bulk conditions added: 2 conditions', 'kondisi_sampel', 10, '::1', '2025-12-21 07:55:01'),
(1974, 7, 'User logged in', 'users', NULL, '::1', '2025-12-21 08:21:23'),
(1975, 7, 'Examination request created: LAB20250009', 'pemeriksaan_lab', 32, '::1', '2025-12-21 08:21:49'),
(1976, 7, 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-12-21 08:22:01'),
(1977, 4, 'Sample status updated to progress', 'pemeriksaan_lab', 31, '::1', '2025-12-21 08:29:14'),
(1978, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 08:38:10'),
(1979, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 08:38:20'),
(1980, 4, 'Sample status updated to progress', 'pemeriksaan_lab', 31, '::1', '2025-12-21 08:38:34'),
(1981, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 08:39:34'),
(1982, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 08:41:30'),
(1983, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 08:41:32'),
(1984, 4, 'User logged out', 'users', NULL, '::1', '2025-12-21 08:42:06'),
(1985, 7, 'User logged in', 'users', NULL, '::1', '2025-12-21 08:42:14'),
(1986, 7, 'Examination request created: LAB20250010', 'pemeriksaan_lab', 33, '::1', '2025-12-21 08:42:35'),
(1987, 7, 'User logged out', 'users', NULL, '::1', '2025-12-21 08:42:38'),
(1988, 4, 'User logged in', 'users', NULL, '::1', '2025-12-21 08:42:46'),
(1989, 7, 'User logged out', 'users', NULL, '::1', '2025-12-21 08:45:27'),
(1990, 11, 'User logged in', 'users', NULL, '::1', '2025-12-21 08:45:33'),
(1991, 11, 'Validated QC Alat ID: 1', 'quality_control', 1, '::1', '2025-12-21 08:46:30'),
(1992, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 08:51:33'),
(1993, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 09:04:04'),
(1994, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 09:04:09'),
(1995, 4, 'Kalibrasi Autoclave selesai. Jadwal berikutnya: 21/12/2025', 'alat_laboratorium', 13, '::1', '2025-12-21 09:04:26'),
(1996, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 09:04:33'),
(1997, 11, 'User logged out', 'users', NULL, '::1', '2025-12-21 09:13:02'),
(1998, 1, 'User logged in', 'users', NULL, '::1', '2025-12-21 09:13:20'),
(1999, 1, 'Mengakses dashboard', 'system', NULL, '::1', '2025-12-21 09:13:21'),
(2000, 1, 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-12-21 09:13:34'),
(2001, 4, 'Sample status updated to progress', 'pemeriksaan_lab', 31, '::1', '2025-12-21 09:16:49'),
(2002, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 09:20:09'),
(2003, 4, 'Sample status updated to progress', 'pemeriksaan_lab', 31, '::1', '2025-12-21 09:24:40'),
(2004, 4, 'Storage status updated to: tersimpan', 'sampel_storage', 2, '::1', '2025-12-21 09:27:38'),
(2005, 1, 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-12-21 09:29:49'),
(2006, 4, 'Storage status updated to: tersimpan', 'sampel_storage', 2, '::1', '2025-12-21 09:29:56'),
(2007, 4, 'Lab request accepted', 'pemeriksaan_lab', 32, '::1', '2025-12-21 09:30:02'),
(2008, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 09:30:08'),
(2009, 4, 'Storage status updated to: tersimpan', 'sampel_storage', 2, '::1', '2025-12-21 09:30:13'),
(2010, 4, 'Storage status updated to: tersimpan', 'sampel_storage', 2, '::1', '2025-12-21 09:30:17'),
(2011, 4, 'Storage status updated to: tersimpan', 'sampel_storage', 2, '::1', '2025-12-21 09:32:32'),
(2012, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 09:32:37'),
(2013, 4, 'Kalibrasi Autoclave selesai. Jadwal berikutnya: 15/12/2025', 'alat_laboratorium', 13, '::1', '2025-12-21 09:32:52'),
(2014, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 09:33:03'),
(2015, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 09:33:10'),
(2016, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 09:33:26'),
(2017, 4, 'QC performed: Refrigerator - Passed', 'quality_control', 2, '::1', '2025-12-21 09:34:55'),
(2018, 4, 'QC performed: Refrigerator - Passed', 'quality_control', 3, '::1', '2025-12-21 09:36:25'),
(2019, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 11:23:28'),
(2020, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 11:23:41'),
(2021, 4, 'Lab request accepted', 'pemeriksaan_lab', 33, '::1', '2025-12-21 11:23:45'),
(2022, 4, 'Sample collected', 'pemeriksaan_sampel', 11, '::1', '2025-12-21 11:24:32'),
(2023, 1, 'Sampel auto-stored: whole_blood', 'sampel_storage', 3, '::1', '2025-12-21 11:24:41'),
(2024, 4, 'Sample accepted with conditions', 'pemeriksaan_sampel', 11, '::1', '2025-12-21 11:24:41'),
(2025, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 11:24:51'),
(2026, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 11:24:55'),
(2027, 4, 'Kalibrasi Autoclave selesai. Jadwal berikutnya: 21/12/2025', 'alat_laboratorium', 13, '::1', '2025-12-21 11:25:14'),
(2028, 4, 'Kalibrasi Autoclave selesai. Jadwal berikutnya: 31/12/2025', 'alat_laboratorium', 13, '::1', '2025-12-21 11:25:29'),
(2029, 4, 'QC performed: Autoclave - Passed', 'quality_control', 4, '::1', '2025-12-21 11:26:24'),
(2030, 4, 'User logged out', 'users', NULL, '::1', '2025-12-21 11:26:41'),
(2031, 11, 'User logged in', 'users', NULL, '::1', '2025-12-21 11:26:58'),
(2032, 11, 'Validasi hasil pemeriksaan', 'pemeriksaan_lab', 31, '::1', '2025-12-21 11:27:05'),
(2033, 1, 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-12-21 11:28:37'),
(2034, 11, 'Validated QC Alat ID: 4', 'quality_control', 4, '::1', '2025-12-21 11:31:21'),
(2035, 1, 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-12-21 11:31:28'),
(2036, 1, 'User logged out', 'users', NULL, '::1', '2025-12-21 11:31:33'),
(2037, 1, 'User logged in', 'users', NULL, '::1', '2025-12-21 11:31:51'),
(2038, 1, 'Mengakses dashboard', 'system', NULL, '::1', '2025-12-21 11:31:51'),
(2039, 1, 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-12-21 11:31:53'),
(2040, 1, 'User logged out', 'users', NULL, '::1', '2025-12-21 11:32:12'),
(2041, 4, 'User logged in', 'users', NULL, '::1', '2025-12-21 11:32:19'),
(2042, 4, 'Storage status updated to: tersimpan', 'sampel_storage', 3, '::1', '2025-12-21 11:32:34'),
(2043, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 11:32:57'),
(2044, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 11:32:59'),
(2045, 4, 'User logged out', 'users', NULL, '::1', '2025-12-21 11:33:26'),
(2046, 11, 'User logged in', 'users', NULL, '::1', '2025-12-21 11:33:33'),
(2047, 11, 'Validated QC Alat ID: 3', 'quality_control', 3, '::1', '2025-12-21 11:33:38'),
(2048, 11, 'User logged out', 'users', NULL, '::1', '2025-12-21 11:33:53'),
(2049, 4, 'User logged in', 'users', NULL, '::1', '2025-12-21 11:34:02'),
(2050, 4, 'Lab results saved: hematologi', 'pemeriksaan_lab', 33, '::1', '2025-12-21 11:34:37'),
(2051, 11, 'Validasi hasil pemeriksaan', 'pemeriksaan_lab', 33, '::1', '2025-12-21 11:34:48'),
(2052, 4, 'Sample status updated to progress', 'pemeriksaan_lab', 32, '::1', '2025-12-21 11:35:50'),
(2053, 4, 'Bulk conditions added: 2 conditions', 'kondisi_sampel', 11, '::1', '2025-12-21 11:41:20'),
(2054, 4, 'Sample status updated to progress', 'pemeriksaan_lab', 32, '::1', '2025-12-21 11:41:40'),
(2055, 4, 'Sample status updated to progress', 'pemeriksaan_lab', 32, '::1', '2025-12-21 11:54:18'),
(2056, 4, 'Lab results saved: hematologi', 'pemeriksaan_lab', 32, '::1', '2025-12-21 11:55:08'),
(2057, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 11:59:48'),
(2058, 4, 'Lab results saved: hematologi', 'pemeriksaan_lab', 32, '::1', '2025-12-21 11:59:58'),
(2059, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 12:05:04'),
(2060, 4, 'Lab results saved: hematologi', 'pemeriksaan_lab', 32, '::1', '2025-12-21 12:05:16'),
(2061, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 12:05:27'),
(2062, 4, 'Bulk conditions added: 2 conditions', 'kondisi_sampel', 11, '::1', '2025-12-21 12:05:38'),
(2063, 4, 'Bulk conditions added: 2 conditions', 'kondisi_sampel', 11, '::1', '2025-12-21 12:16:01'),
(2064, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 12:16:05'),
(2065, 11, 'Validasi hasil pemeriksaan', 'pemeriksaan_lab', 32, '::1', '2025-12-21 12:16:26'),
(2066, 11, 'User logged out', 'users', NULL, '::1', '2025-12-21 12:19:55'),
(2067, 7, 'User logged in', 'users', NULL, '::1', '2025-12-21 12:20:30'),
(2068, 7, 'User logged out', 'users', NULL, '::1', '2025-12-21 12:20:57'),
(2069, 1, 'User logged in', 'users', NULL, '::1', '2025-12-21 12:21:03'),
(2070, 1, 'Mengakses dashboard', 'system', NULL, '::1', '2025-12-21 12:21:04'),
(2071, 1, 'User logged out', 'users', NULL, '::1', '2025-12-21 12:21:32'),
(2072, 7, 'User logged in', 'users', NULL, '::1', '2025-12-21 12:21:36'),
(2073, 7, 'Examination request created: LAB20250011', 'pemeriksaan_lab', 34, '::1', '2025-12-21 12:22:20'),
(2074, 4, 'Lab request accepted', 'pemeriksaan_lab', 34, '::1', '2025-12-21 12:32:13'),
(2075, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-21 12:32:32'),
(2076, 7, 'Examination request created: LAB20250012', 'pemeriksaan_lab', 35, '::1', '2025-12-21 12:33:30'),
(2077, 7, 'User logged in', 'users', NULL, '::1', '2025-12-25 04:17:12'),
(2078, 7, 'Examination request created: LAB20250013', 'pemeriksaan_lab', 36, '::1', '2025-12-25 04:17:48'),
(2079, 7, 'User logged out', 'users', NULL, '::1', '2025-12-25 04:17:52'),
(2080, 4, 'User logged in', 'users', NULL, '::1', '2025-12-25 04:17:56'),
(2081, 4, 'Lab request accepted', 'pemeriksaan_lab', 36, '::1', '2025-12-25 04:18:05'),
(2082, 4, 'Sample collected', 'pemeriksaan_sampel', 17, '::1', '2025-12-25 04:40:05'),
(2083, 1, 'Sampel auto-stored: whole_blood', 'sampel_storage', 4, '::1', '2025-12-25 05:05:24'),
(2084, 4, 'Sample accepted with conditions', 'pemeriksaan_sampel', 17, '::1', '2025-12-25 05:05:24'),
(2085, 4, 'User logged out', 'users', NULL, '::1', '2025-12-25 05:20:53'),
(2086, 11, 'User logged in', 'users', NULL, '::1', '2025-12-25 05:20:58'),
(2087, 11, 'Validasi hasil pemeriksaan', 'pemeriksaan_lab', 36, '::1', '2025-12-25 05:32:51'),
(2088, 11, 'User logged out', 'users', NULL, '::1', '2025-12-25 05:32:58'),
(2089, 4, 'User logged in', 'users', NULL, '::1', '2025-12-25 05:33:04'),
(2090, 4, 'Lab request accepted', 'pemeriksaan_lab', 35, '::1', '2025-12-25 05:33:08'),
(2091, 4, 'User logged out', 'users', NULL, '::1', '2025-12-25 05:33:38'),
(2092, 11, 'User logged in', 'users', NULL, '::1', '2025-12-25 05:33:47'),
(2093, 11, 'Validasi hasil pemeriksaan', 'pemeriksaan_lab', 35, '::1', '2025-12-25 05:33:55'),
(2094, 11, 'User logged out', 'users', NULL, '::1', '2025-12-25 06:27:10'),
(2095, 7, 'User logged in', 'users', NULL, '::1', '2025-12-25 06:27:22'),
(2096, 7, 'Examination request created: LAB20250014', 'pemeriksaan_lab', 37, '::1', '2025-12-25 06:27:42'),
(2097, 7, 'User logged out', 'users', NULL, '::1', '2025-12-25 06:27:46'),
(2098, 4, 'User logged in', 'users', NULL, '::1', '2025-12-25 06:27:56'),
(2099, 4, 'Lab request accepted', 'pemeriksaan_lab', 37, '::1', '2025-12-25 06:28:01'),
(2100, 4, 'User logged out', 'users', NULL, '::1', '2025-12-25 06:28:18'),
(2101, 7, 'User logged in', 'users', NULL, '::1', '2025-12-25 06:28:23'),
(2102, 7, 'Examination request created: LAB20250015', 'pemeriksaan_lab', 38, '::1', '2025-12-25 06:28:50'),
(2103, 7, 'User logged out', 'users', NULL, '::1', '2025-12-25 06:28:53'),
(2104, 4, 'User logged in', 'users', NULL, '::1', '2025-12-25 06:28:57'),
(2105, 4, 'Lab request accepted', 'pemeriksaan_lab', 38, '::1', '2025-12-25 06:29:00'),
(2106, 4, 'User logged out', 'users', NULL, '::1', '2025-12-25 07:50:18'),
(2107, 7, 'User logged in', 'users', NULL, '::1', '2025-12-25 07:50:24'),
(2108, 7, 'Examination request created: LAB20250016', 'pemeriksaan_lab', 39, '::1', '2025-12-25 07:51:35'),
(2109, 7, 'User logged out', 'users', NULL, '::1', '2025-12-25 07:51:38'),
(2110, 4, 'User logged in', 'users', NULL, '::1', '2025-12-25 07:51:44'),
(2111, 4, 'Lab request accepted', 'pemeriksaan_lab', 39, '::1', '2025-12-25 07:51:48'),
(2112, 4, 'Sample collected', 'pemeriksaan_sampel', 21, '::1', '2025-12-25 11:00:23'),
(2113, 4, 'Sample collected', 'pemeriksaan_sampel', 22, '::1', '2025-12-25 11:00:26'),
(2114, 4, 'Sample collected', 'pemeriksaan_sampel', 23, '::1', '2025-12-25 11:00:28'),
(2115, 1, 'Sampel auto-stored: whole_blood', 'sampel_storage', 5, '::1', '2025-12-25 11:00:35'),
(2116, 4, 'Sample accepted with conditions', 'pemeriksaan_sampel', 21, '::1', '2025-12-25 11:00:35'),
(2117, 1, 'Sampel auto-stored: serum', 'sampel_storage', 6, '::1', '2025-12-25 11:00:41'),
(2118, 4, 'Sample accepted with conditions', 'pemeriksaan_sampel', 22, '::1', '2025-12-25 11:00:41'),
(2119, 1, 'Sampel auto-stored: plasma', 'sampel_storage', 7, '::1', '2025-12-25 11:00:47'),
(2120, 4, 'Sample accepted with conditions', 'pemeriksaan_sampel', 23, '::1', '2025-12-25 11:00:47'),
(2121, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-25 11:47:06'),
(2122, 4, 'Mengakses halaman kelola inventory petugas', 'system', NULL, '::1', '2025-12-25 11:47:09'),
(2123, 4, 'User logged out', 'users', NULL, '::1', '2025-12-25 12:17:57'),
(2124, 7, 'User logged in', 'users', NULL, '::1', '2025-12-25 12:18:03'),
(2125, 7, 'User logged out', 'users', NULL, '::1', '2025-12-25 12:19:08'),
(2126, 1, 'User logged in', 'users', NULL, '::1', '2025-12-25 12:19:17'),
(2127, 1, 'Mengakses dashboard', 'system', NULL, '::1', '2025-12-25 12:19:17'),
(2128, 1, 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-12-25 12:19:43'),
(2129, 1, 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-12-25 12:19:44'),
(2130, 1, 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-12-25 12:19:53'),
(2131, 1, 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-12-25 12:23:56'),
(2132, 1, 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-12-25 12:23:56'),
(2133, 1, 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-12-25 12:23:57'),
(2134, 1, 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-12-25 12:23:58'),
(2135, 1, 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-12-25 12:24:58'),
(2136, 1, 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-12-25 12:25:00'),
(2137, 1, 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-12-25 12:25:01'),
(2138, 1, 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-12-25 12:25:01'),
(2139, 1, 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-12-25 12:25:02'),
(2140, 1, 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-12-25 12:25:03'),
(2141, 1, 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-12-25 12:25:03'),
(2142, 1, 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-12-25 12:25:05'),
(2143, 1, 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-12-25 12:32:53'),
(2144, 1, 'User logged out', 'users', NULL, '::1', '2025-12-25 12:33:10'),
(2145, 7, 'User logged in', 'users', NULL, '::1', '2025-12-25 12:33:16'),
(2146, 7, 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-12-25 12:34:10'),
(2147, 7, 'User logged out', 'users', NULL, '::1', '2025-12-25 12:34:12'),
(2148, 1, 'User logged in', 'users', NULL, '::1', '2025-12-25 12:34:57'),
(2149, 1, 'Mengakses dashboard', 'system', NULL, '::1', '2025-12-25 12:34:57'),
(2150, 1, 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-12-25 12:35:00'),
(2151, 1, 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-12-25 12:35:01'),
(2152, 1, 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-12-25 12:35:02'),
(2153, 1, 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-12-25 12:35:03'),
(2154, 1, 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-12-25 12:35:04'),
(2155, 1, 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-12-25 12:35:05'),
(2156, 1, 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-12-25 12:35:09'),
(2157, 1, 'User admin berhasil login ke sistem', NULL, NULL, '127.0.0.1', '2025-12-25 12:36:29'),
(2158, 1, 'Mengakses halaman kelola inventory', 'system', NULL, '::1', '2025-12-25 12:37:31'),
(2159, 1, 'Mengakses laporan aktivitas', 'system', NULL, '::1', '2025-12-25 12:37:32'),
(2160, 1, 'Mengakses dashboard', 'system', NULL, '::1', '2025-12-25 13:00:14'),
(2161, 1, 'User logged out', 'users', NULL, '::1', '2025-12-25 13:00:18'),
(2162, 7, 'User logged in', 'users', NULL, '::1', '2025-12-25 13:00:22'),
(2163, 7, 'Patient created: Rinas sari', 'pasien', 22, '::1', '2025-12-25 13:04:04'),
(2164, 7, 'Mengakses laporan pemeriksaan', 'system', NULL, '::1', '2025-12-25 13:18:32'),
(2165, 7, 'User logged out', 'users', NULL, '::1', '2025-12-25 13:22:15'),
(2166, 4, 'User logged in', 'users', NULL, '::1', '2025-12-25 13:22:25'),
(2167, 4, 'Sample created', 'pemeriksaan_sampel', 24, '::1', '2025-12-25 13:22:46'),
(2168, 4, 'Sample collected', 'pemeriksaan_sampel', 24, '::1', '2025-12-25 13:22:49'),
(2169, 1, 'Sampel auto-stored: lain', 'sampel_storage', 8, '::1', '2025-12-25 13:22:53'),
(2170, 4, 'Sample accepted with conditions', 'pemeriksaan_sampel', 24, '::1', '2025-12-25 13:22:53');

-- --------------------------------------------------------

--
-- Table structure for table `administrasi`
--

CREATE TABLE `administrasi` (
  `administrasi_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nama_admin` varchar(100) NOT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `administrasi`
--

INSERT INTO `administrasi` (`administrasi_id`, `user_id`, `nama_admin`, `telepon`, `created_at`) VALUES
(1, 2, 'Admin Front Office', '085282182747', '2025-08-28 10:41:13'),
(2, 7, 'Firdaus', '0852-8218-2747', '2025-09-12 18:01:18');

-- --------------------------------------------------------

--
-- Table structure for table `administrator`
--

CREATE TABLE `administrator` (
  `admin_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nama_admin` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `administrator`
--

INSERT INTO `administrator` (`admin_id`, `user_id`, `nama_admin`, `created_at`) VALUES
(1, 1, 'Super Administrator', '2025-08-28 10:41:13');

-- --------------------------------------------------------

--
-- Table structure for table `alat_laboratorium`
--

CREATE TABLE `alat_laboratorium` (
  `alat_id` int(11) NOT NULL,
  `nama_alat` varchar(100) NOT NULL,
  `kode_unik` varchar(50) DEFAULT NULL,
  `merek_model` varchar(100) DEFAULT NULL,
  `lokasi` varchar(100) DEFAULT NULL,
  `status_alat` enum('Normal','Perlu Kalibrasi','Rusak','Sedang Kalibrasi') DEFAULT 'Normal',
  `jadwal_kalibrasi` date DEFAULT NULL,
  `tanggal_kalibrasi_terakhir` date DEFAULT NULL,
  `riwayat_perbaikan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alat_laboratorium`
--

INSERT INTO `alat_laboratorium` (`alat_id`, `nama_alat`, `kode_unik`, `merek_model`, `lokasi`, `status_alat`, `jadwal_kalibrasi`, `tanggal_kalibrasi_terakhir`, `riwayat_perbaikan`, `created_at`, `updated_at`) VALUES
(1, 'Mikroskop Cahaya', 'ALT001', 'Olympus CX23', 'Lab Mikrobiologi', 'Normal', '2025-03-15', '2024-12-15', NULL, '2024-01-10 01:00:00', '2024-12-15 03:30:00'),
(2, 'Sentrifuge', 'ALT002', 'Thermo Scientific Sorvall ST 8', 'Lab Hematologi', 'Normal', '2025-06-20', '2024-12-20', NULL, '2024-02-15 02:00:00', '2024-12-20 04:00:00'),
(3, 'Spectrophotometer', 'ALT003', 'Shimadzu UV-1800', 'Lab Kimia Klinik', 'Normal', '2025-04-10', '2024-11-10', NULL, '2024-03-05 03:00:00', '2024-11-10 07:00:00'),
(4, 'Hematology Analyzer', 'ALT004', 'Sysmex XN-1000', 'Lab Hematologi', 'Normal', '2025-05-25', '2024-11-25', NULL, '2024-01-20 04:00:00', '2024-11-25 02:30:00'),
(5, 'Chemistry Analyzer', 'ALT005', 'Roche Cobas c311', 'Lab Kimia Klinik', 'Normal', '2025-07-15', '2024-12-15', NULL, '2024-02-10 05:00:00', '2024-12-15 08:00:00'),
(6, 'Inkubator CO2', 'ALT006', 'Thermo Forma Series II', 'Lab Mikrobiologi', 'Normal', '2025-08-20', '2024-11-20', NULL, '2024-03-15 06:00:00', '2024-11-20 03:00:00'),
(7, 'Centrifuge Tabletop', 'ALT007', 'Eppendorf 5810R', 'Lab Serologi', 'Normal', '2025-06-30', '2024-12-01', NULL, '2024-04-01 07:00:00', '2024-12-01 04:30:00'),
(8, 'Water Bath', 'ALT008', 'Memmert WNB 7', 'Lab Serologi', 'Normal', '2025-12-24', '2025-12-21', 'Kalibrasi selesai pada 21/12/2025 oleh Suryanto. Hasil: Passed', '2024-01-25 01:30:00', '2025-12-21 07:19:35'),
(9, 'Refrigerator', 'ALT009', 'Panasonic MBR-514', 'Lab Penyimpanan', 'Normal', '2025-12-25', '2025-12-31', 'Kalibrasi selesai pada 31/12/2025 oleh Suryanto. Hasil: Passed', '2024-02-20 02:30:00', '2025-12-21 05:00:07'),
(10, 'Freezer -20°C', 'ALT010', 'Thermo Forma -20', 'Lab Penyimpanan', 'Normal', '2025-11-20', '2024-12-20', NULL, '2024-03-10 03:30:00', '2024-12-20 08:00:00'),
(11, 'Urine Analyzer', 'ALT011', 'Sysmex UF-1000i', 'Lab Urinologi', 'Normal', '2025-05-05', '2024-11-05', NULL, '2024-04-15 04:30:00', '2024-11-05 03:00:00'),
(12, 'pH Meter', 'ALT012', 'Mettler Toledo SevenCompact', 'Lab Kimia Klinik', 'Normal', '2025-07-25', '2024-12-25', NULL, '2024-01-30 05:30:00', '2024-12-25 04:00:00'),
(13, 'Autoclave', 'ALT013', 'Hirayama HV-50', 'Lab Sterilisasi', 'Normal', '2025-12-31', '2025-12-21', 'Kalibrasi selesai pada 09/12/2025 oleh Suryanto. Hasil: Passed\r\nKalibrasi selesai pada 21/12/2025 oleh Suryanto. Hasil: Passed\r\nKalibrasi selesai pada 21/12/2025 oleh Suryanto. Hasil: Passed\r\nKalibrasi selesai pada 21/12/2025 oleh Suryanto. Hasil: Passed', '2024-02-25 06:30:00', '2025-12-21 11:25:29'),
(14, 'PCR Machine', 'ALT014', 'Applied Biosystems VeriFlex', 'Lab Molekuler', 'Normal', '2025-09-20', '2024-12-20', NULL, '2024-03-20 07:30:00', '2024-12-20 06:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `calibration_history`
--

CREATE TABLE `calibration_history` (
  `calibration_id` int(11) NOT NULL,
  `alat_id` int(11) NOT NULL,
  `tanggal_kalibrasi` date NOT NULL,
  `hasil_kalibrasi` text DEFAULT NULL,
  `teknisi` varchar(100) DEFAULT NULL,
  `sertifikat_no` varchar(100) DEFAULT NULL,
  `next_calibration_date` date DEFAULT NULL,
  `status` enum('Passed','Failed','Conditional') DEFAULT 'Passed',
  `catatan` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `calibration_history`
--

INSERT INTO `calibration_history` (`calibration_id`, `alat_id`, `tanggal_kalibrasi`, `hasil_kalibrasi`, `teknisi`, `sertifikat_no`, `next_calibration_date`, `status`, `catatan`, `user_id`, `created_at`) VALUES
(6, 9, '2025-12-31', 'Passed', 'Suryanto', NULL, '2025-12-25', 'Passed', '', 4, '2025-12-21 05:00:07'),
(7, 8, '2025-12-21', 'Passed', 'Suryanto', NULL, '2025-12-24', 'Passed', '', 4, '2025-12-21 07:19:35'),
(8, 13, '2025-12-09', 'Passed', 'Suryanto', NULL, '2025-12-21', 'Passed', '', 4, '2025-12-21 09:04:26'),
(9, 13, '2025-12-21', 'Passed', 'Suryanto', NULL, '2025-12-15', 'Passed', '', 4, '2025-12-21 09:32:52'),
(10, 13, '2025-12-21', 'Passed', 'Suryanto', NULL, '2025-12-21', 'Passed', '', 4, '2025-12-21 11:25:14'),
(11, 13, '2025-12-21', 'Passed', 'Suryanto', NULL, '2025-12-31', 'Passed', '', 4, '2025-12-21 11:25:29');

-- --------------------------------------------------------

--
-- Table structure for table `hematologi`
--

CREATE TABLE `hematologi` (
  `hematologi_id` int(11) NOT NULL,
  `pemeriksaan_id` int(11) NOT NULL,
  `hemoglobin` decimal(5,2) DEFAULT NULL,
  `hematokrit` decimal(5,2) DEFAULT NULL,
  `leukosit` decimal(5,2) DEFAULT NULL,
  `trombosit` decimal(8,2) DEFAULT NULL,
  `eritrosit` decimal(5,2) DEFAULT NULL,
  `mcv` decimal(5,2) DEFAULT NULL,
  `mch` decimal(5,2) DEFAULT NULL,
  `mchc` decimal(5,2) DEFAULT NULL,
  `eosinofil` decimal(5,2) DEFAULT NULL,
  `basofil` decimal(5,2) DEFAULT NULL,
  `neutrofil` decimal(5,2) DEFAULT NULL,
  `limfosit` decimal(5,2) DEFAULT NULL,
  `monosit` decimal(5,2) DEFAULT NULL,
  `laju_endap_darah` decimal(5,2) DEFAULT NULL,
  `clotting_time` int(11) DEFAULT NULL COMMENT 'dalam detik',
  `bleeding_time` int(11) DEFAULT NULL COMMENT 'dalam detik',
  `golongan_darah` enum('A','B','AB','O') DEFAULT NULL,
  `rhesus` enum('+','-') DEFAULT NULL,
  `malaria` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `harga_paket_darah_rutin` decimal(10,2) DEFAULT 40000.00 COMMENT 'Harga Paket Darah Rutin',
  `harga_led` decimal(10,2) DEFAULT 15000.00 COMMENT 'Harga Laju Endap Darah',
  `harga_clotting` decimal(10,2) DEFAULT 15000.00 COMMENT 'Harga Clotting Time',
  `harga_bleeding` decimal(10,2) DEFAULT 15000.00 COMMENT 'Harga Bleeding Time',
  `harga_goldar` decimal(10,2) DEFAULT 20000.00 COMMENT 'Harga Golongan Darah + Rhesus',
  `harga_malaria` decimal(10,2) DEFAULT 15000.00 COMMENT 'Harga Tes Malaria',
  `total_harga_hematologi` decimal(10,2) DEFAULT 0.00 COMMENT 'Total Harga Hematologi'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hematologi`
--

INSERT INTO `hematologi` (`hematologi_id`, `pemeriksaan_id`, `hemoglobin`, `hematokrit`, `leukosit`, `trombosit`, `eritrosit`, `mcv`, `mch`, `mchc`, `eosinofil`, `basofil`, `neutrofil`, `limfosit`, `monosit`, `laju_endap_darah`, `clotting_time`, `bleeding_time`, `golongan_darah`, `rhesus`, `malaria`, `created_at`, `harga_paket_darah_rutin`, `harga_led`, `harga_clotting`, `harga_bleeding`, `harga_goldar`, `harga_malaria`, `total_harga_hematologi`) VALUES
(20, 26, 12.00, 42.00, 2.00, 124.00, 2.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12.00, 2, 4, 'B', '+', 'positif', '2025-12-19 13:30:50', 40000.00, 15000.00, 15000.00, 15000.00, 20000.00, 15000.00, 120000.00),
(21, 27, 12.00, 42.00, 21.00, 214.00, 24.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 21.00, 12, 1, 'A', '+', 'positif', '2025-12-19 13:52:24', 40000.00, 15000.00, 15000.00, 15000.00, 20000.00, 15000.00, 120000.00),
(22, 33, 13.00, 30.00, 23.00, 342.00, 45.00, 34.00, 43.00, 34.00, 2.00, 1.00, 56.00, 32.00, 3.00, 14.00, 15, 6, 'B', '+', 'positif', '2025-12-21 11:34:37', 40000.00, 15000.00, 15000.00, 15000.00, 20000.00, 15000.00, 120000.00),
(23, 32, 13.00, 43.00, 4.00, 123.00, 4.00, 80.00, 27.00, 32.00, 1.00, 1.00, 50.00, 20.00, 2.00, 14.00, 5, 3, 'O', '+', 'positif', '2025-12-21 12:05:16', 40000.00, 15000.00, 15000.00, 15000.00, 20000.00, 15000.00, 120000.00);

--
-- Triggers `hematologi`
--
DELIMITER $$
CREATE TRIGGER `tr_hematologi_calculate_total` BEFORE INSERT ON `hematologi` FOR EACH ROW BEGIN
    SET NEW.total_harga_hematologi = 0;
    
    -- Paket Darah Rutin (jika ada hemoglobin atau hematokrit)
    IF NEW.hemoglobin IS NOT NULL OR NEW.hematokrit IS NOT NULL THEN
        SET NEW.total_harga_hematologi = NEW.total_harga_hematologi + NEW.harga_paket_darah_rutin;
    END IF;
    
    -- Laju Endap Darah
    IF NEW.laju_endap_darah IS NOT NULL THEN
        SET NEW.total_harga_hematologi = NEW.total_harga_hematologi + NEW.harga_led;
    END IF;
    
    -- Clotting Time
    IF NEW.clotting_time IS NOT NULL THEN
        SET NEW.total_harga_hematologi = NEW.total_harga_hematologi + NEW.harga_clotting;
    END IF;
    
    -- Bleeding Time
    IF NEW.bleeding_time IS NOT NULL THEN
        SET NEW.total_harga_hematologi = NEW.total_harga_hematologi + NEW.harga_bleeding;
    END IF;
    
    -- Golongan Darah + Rhesus
    IF NEW.golongan_darah IS NOT NULL OR NEW.rhesus IS NOT NULL THEN
        SET NEW.total_harga_hematologi = NEW.total_harga_hematologi + NEW.harga_goldar;
    END IF;
    
    -- Malaria
    IF NEW.malaria IS NOT NULL THEN
        SET NEW.total_harga_hematologi = NEW.total_harga_hematologi + NEW.harga_malaria;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_hematologi_calculate_total_update` BEFORE UPDATE ON `hematologi` FOR EACH ROW BEGIN
    SET NEW.total_harga_hematologi = 0;
    
    -- Paket Darah Rutin
    IF NEW.hemoglobin IS NOT NULL OR NEW.hematokrit IS NOT NULL THEN
        SET NEW.total_harga_hematologi = NEW.total_harga_hematologi + NEW.harga_paket_darah_rutin;
    END IF;
    
    -- Laju Endap Darah
    IF NEW.laju_endap_darah IS NOT NULL THEN
        SET NEW.total_harga_hematologi = NEW.total_harga_hematologi + NEW.harga_led;
    END IF;
    
    -- Clotting Time
    IF NEW.clotting_time IS NOT NULL THEN
        SET NEW.total_harga_hematologi = NEW.total_harga_hematologi + NEW.harga_clotting;
    END IF;
    
    -- Bleeding Time
    IF NEW.bleeding_time IS NOT NULL THEN
        SET NEW.total_harga_hematologi = NEW.total_harga_hematologi + NEW.harga_bleeding;
    END IF;
    
    -- Golongan Darah + Rhesus
    IF NEW.golongan_darah IS NOT NULL OR NEW.rhesus IS NOT NULL THEN
        SET NEW.total_harga_hematologi = NEW.total_harga_hematologi + NEW.harga_goldar;
    END IF;
    
    -- Malaria
    IF NEW.malaria IS NOT NULL THEN
        SET NEW.total_harga_hematologi = NEW.total_harga_hematologi + NEW.harga_malaria;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_hematologi_update_invoice` AFTER INSERT ON `hematologi` FOR EACH ROW BEGIN
    CALL sp_update_invoice_from_examination(NEW.pemeriksaan_id);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_hematologi_update_invoice_on_update` AFTER UPDATE ON `hematologi` FOR EACH ROW BEGIN
    CALL sp_update_invoice_from_examination(NEW.pemeriksaan_id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `ims`
--

CREATE TABLE `ims` (
  `ims_id` int(11) NOT NULL,
  `pemeriksaan_id` int(11) NOT NULL,
  `sifilis` enum('Reaktif','Non-Reaktif') DEFAULT NULL,
  `duh_tubuh` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `harga_sifilis` decimal(10,2) DEFAULT 100000.00 COMMENT 'Harga Tes Sifilis',
  `harga_duh_tubuh` decimal(10,2) DEFAULT 50000.00 COMMENT 'Harga Tes Duh Tubuh',
  `total_harga_ims` decimal(10,2) DEFAULT 0.00 COMMENT 'Total Harga IMS'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ims`
--

INSERT INTO `ims` (`ims_id`, `pemeriksaan_id`, `sifilis`, `duh_tubuh`, `created_at`, `harga_sifilis`, `harga_duh_tubuh`, `total_harga_ims`) VALUES
(2, 36, 'Reaktif', 'positif', '2025-12-25 04:21:16', 100000.00, 50000.00, 150000.00);

--
-- Triggers `ims`
--
DELIMITER $$
CREATE TRIGGER `tr_ims_calculate_total` BEFORE INSERT ON `ims` FOR EACH ROW BEGIN
    SET NEW.total_harga_ims = 
        (CASE WHEN NEW.sifilis IS NOT NULL THEN NEW.harga_sifilis ELSE 0 END) +
        (CASE WHEN NEW.duh_tubuh IS NOT NULL THEN NEW.harga_duh_tubuh ELSE 0 END);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_ims_calculate_total_update` BEFORE UPDATE ON `ims` FOR EACH ROW BEGIN
    SET NEW.total_harga_ims = 
        (CASE WHEN NEW.sifilis IS NOT NULL THEN NEW.harga_sifilis ELSE 0 END) +
        (CASE WHEN NEW.duh_tubuh IS NOT NULL THEN NEW.harga_duh_tubuh ELSE 0 END);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_ims_update_invoice` AFTER INSERT ON `ims` FOR EACH ROW BEGIN
    CALL sp_update_invoice_from_examination(NEW.pemeriksaan_id);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_ims_update_invoice_on_update` AFTER UPDATE ON `ims` FOR EACH ROW BEGIN
    CALL sp_update_invoice_from_examination(NEW.pemeriksaan_id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_maintenance_logs`
--

CREATE TABLE `inventory_maintenance_logs` (
  `log_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_type` enum('alat','reagen') NOT NULL,
  `action` varchar(100) NOT NULL,
  `status` varchar(50) NOT NULL,
  `notes` text DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_notifications`
--

CREATE TABLE `inventory_notifications` (
  `notification_id` int(11) NOT NULL,
  `type` enum('low_stock','expiry','calibration','maintenance') NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `item_type` enum('alat','reagen') DEFAULT NULL,
  `priority` enum('low','medium','high','critical') DEFAULT 'medium',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `invoice_id` int(11) NOT NULL,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice`
--

INSERT INTO `invoice` (`invoice_id`, `pemeriksaan_id`, `nomor_invoice`, `tanggal_invoice`, `jenis_pembayaran`, `total_biaya`, `status_pembayaran`, `metode_pembayaran`, `nomor_kartu_bpjs`, `nomor_sep`, `tanggal_pembayaran`, `keterangan`, `created_at`) VALUES
(15, 24, 'INV-2025-0001', '2025-12-10', 'umum', 0.00, 'belum_bayar', NULL, NULL, NULL, NULL, NULL, '2025-12-12 09:04:03'),
(16, 28, 'INV-2025-0016', '2025-12-14', 'umum', 480000.00, 'belum_bayar', NULL, NULL, NULL, NULL, NULL, '2025-12-14 04:35:41'),
(17, 26, 'INV-2025-0017', '2025-12-19', 'umum', 0.00, 'belum_bayar', NULL, NULL, NULL, NULL, NULL, '2025-12-19 13:30:50'),
(18, 27, 'INV-2025-0018', '2025-12-20', 'umum', 0.00, 'belum_bayar', NULL, NULL, NULL, NULL, NULL, '2025-12-19 13:51:50'),
(19, 25, 'INV-2025-0019', '2025-12-25', 'umum', 0.00, 'belum_bayar', NULL, NULL, NULL, NULL, NULL, '2025-12-19 14:13:18'),
(20, 29, 'INV-2025-0020', '2025-12-19', 'umum', 290000.00, 'belum_bayar', NULL, NULL, NULL, NULL, NULL, '2025-12-19 14:33:56'),
(21, 31, 'INV-2025-0021', '2025-12-21', 'umum', 0.00, 'belum_bayar', NULL, NULL, NULL, NULL, NULL, '2025-12-21 11:24:17'),
(22, 33, 'INV-2025-0022', '2025-12-22', 'umum', 120000.00, 'belum_bayar', NULL, NULL, NULL, NULL, NULL, '2025-12-21 11:34:37'),
(23, 32, 'INV-2025-0023', '2025-12-23', 'umum', 120000.00, 'belum_bayar', NULL, NULL, NULL, NULL, NULL, '2025-12-21 11:55:08'),
(24, 36, 'INV-2025-0024', '2025-12-25', 'umum', 0.00, 'belum_bayar', NULL, NULL, NULL, NULL, NULL, '2025-12-25 04:21:16'),
(25, 35, 'INV-2025-0025', '2025-12-24', 'umum', 0.00, 'belum_bayar', NULL, NULL, NULL, NULL, NULL, '2025-12-25 05:33:37');

-- --------------------------------------------------------

--
-- Table structure for table `kimia_darah`
--

CREATE TABLE `kimia_darah` (
  `kimia_id` int(11) NOT NULL,
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
  `harga_gds` decimal(10,2) DEFAULT 15000.00 COMMENT 'Harga Gula Darah Sewaktu',
  `harga_gdp` decimal(10,2) DEFAULT 15000.00 COMMENT 'Harga Gula Darah Puasa',
  `harga_gd2pp` decimal(10,2) DEFAULT 15000.00 COMMENT 'Harga Gula Darah 2 Jam PP',
  `harga_chol_total` decimal(10,2) DEFAULT 25000.00 COMMENT 'Harga Cholesterol Total',
  `harga_chol_hdl` decimal(10,2) DEFAULT 30000.00 COMMENT 'Harga Cholesterol HDL',
  `harga_chol_ldl` decimal(10,2) DEFAULT 30000.00 COMMENT 'Harga Cholesterol LDL',
  `harga_trigliserida` decimal(10,2) DEFAULT 25000.00 COMMENT 'Harga Trigliserida',
  `harga_asam_urat` decimal(10,2) DEFAULT 15000.00 COMMENT 'Harga Asam Urat',
  `harga_ureum` decimal(10,2) DEFAULT 30000.00 COMMENT 'Harga Ureum',
  `harga_creatinin` decimal(10,2) DEFAULT 30000.00 COMMENT 'Harga Creatinin',
  `harga_sgpt` decimal(10,2) DEFAULT 30000.00 COMMENT 'Harga SGPT',
  `harga_sgot` decimal(10,2) DEFAULT 30000.00 COMMENT 'Harga SGOT',
  `total_harga_kimia` decimal(10,2) DEFAULT 0.00 COMMENT 'Total Harga Kimia Darah'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kimia_darah`
--

INSERT INTO `kimia_darah` (`kimia_id`, `pemeriksaan_id`, `gula_darah_sewaktu`, `gula_darah_puasa`, `gula_darah_2jam_pp`, `cholesterol_total`, `cholesterol_hdl`, `cholesterol_ldl`, `trigliserida`, `asam_urat`, `ureum`, `creatinin`, `sgpt`, `sgot`, `created_at`, `harga_gds`, `harga_gdp`, `harga_gd2pp`, `harga_chol_total`, `harga_chol_hdl`, `harga_chol_ldl`, `harga_trigliserida`, `harga_asam_urat`, `harga_ureum`, `harga_creatinin`, `harga_sgpt`, `harga_sgot`, `total_harga_kimia`) VALUES
(4, 24, 53.00, 43.00, 34.00, 34.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-12 09:04:03', 15000.00, 15000.00, 15000.00, 25000.00, 30000.00, 30000.00, 25000.00, 15000.00, 30000.00, 30000.00, 30000.00, 30000.00, 70000.00),
(5, 27, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 42.00, 52.00, '2025-12-19 13:52:25', 15000.00, 15000.00, 15000.00, 25000.00, 30000.00, 30000.00, 25000.00, 15000.00, 30000.00, 30000.00, 30000.00, 30000.00, 60000.00),
(6, 29, 65.00, 45.00, 45.00, 453.00, 54.00, 545.00, 54.00, 54.00, 45.00, 45.00, 54.00, 54.00, '2025-12-19 14:33:56', 15000.00, 15000.00, 15000.00, 25000.00, 30000.00, 30000.00, 25000.00, 15000.00, 30000.00, 30000.00, 30000.00, 30000.00, 290000.00),
(7, 31, 70.00, 70.00, 575.00, NULL, NULL, NULL, 57.00, 57.00, 57.00, 57.00, NULL, NULL, '2025-12-21 11:24:17', 15000.00, 15000.00, 15000.00, 25000.00, 30000.00, 30000.00, 25000.00, 15000.00, 30000.00, 30000.00, 30000.00, 30000.00, 145000.00),
(8, 36, 60.00, 70.00, 160.00, 999.99, 40.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-25 04:21:16', 15000.00, 15000.00, 15000.00, 25000.00, 30000.00, 30000.00, 25000.00, 15000.00, 30000.00, 30000.00, 30000.00, 30000.00, 100000.00),
(9, 35, NULL, NULL, NULL, NULL, NULL, NULL, 124.00, 21.00, 12.00, 6.00, 23.00, 52.00, '2025-12-25 05:33:37', 15000.00, 15000.00, 15000.00, 25000.00, 30000.00, 30000.00, 25000.00, 15000.00, 30000.00, 30000.00, 30000.00, 30000.00, 160000.00);

--
-- Triggers `kimia_darah`
--
DELIMITER $$
CREATE TRIGGER `tr_kimia_darah_calculate_total` BEFORE INSERT ON `kimia_darah` FOR EACH ROW BEGIN
    SET NEW.total_harga_kimia = 
        (CASE WHEN NEW.gula_darah_sewaktu IS NOT NULL THEN NEW.harga_gds ELSE 0 END) +
        (CASE WHEN NEW.gula_darah_puasa IS NOT NULL THEN NEW.harga_gdp ELSE 0 END) +
        (CASE WHEN NEW.gula_darah_2jam_pp IS NOT NULL THEN NEW.harga_gd2pp ELSE 0 END) +
        (CASE WHEN NEW.cholesterol_total IS NOT NULL THEN NEW.harga_chol_total ELSE 0 END) +
        (CASE WHEN NEW.cholesterol_hdl IS NOT NULL THEN NEW.harga_chol_hdl ELSE 0 END) +
        (CASE WHEN NEW.cholesterol_ldl IS NOT NULL THEN NEW.harga_chol_ldl ELSE 0 END) +
        (CASE WHEN NEW.trigliserida IS NOT NULL THEN NEW.harga_trigliserida ELSE 0 END) +
        (CASE WHEN NEW.asam_urat IS NOT NULL THEN NEW.harga_asam_urat ELSE 0 END) +
        (CASE WHEN NEW.ureum IS NOT NULL THEN NEW.harga_ureum ELSE 0 END) +
        (CASE WHEN NEW.creatinin IS NOT NULL THEN NEW.harga_creatinin ELSE 0 END) +
        (CASE WHEN NEW.sgpt IS NOT NULL THEN NEW.harga_sgpt ELSE 0 END) +
        (CASE WHEN NEW.sgot IS NOT NULL THEN NEW.harga_sgot ELSE 0 END);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_kimia_darah_calculate_total_update` BEFORE UPDATE ON `kimia_darah` FOR EACH ROW BEGIN
    SET NEW.total_harga_kimia = 
        (CASE WHEN NEW.gula_darah_sewaktu IS NOT NULL THEN NEW.harga_gds ELSE 0 END) +
        (CASE WHEN NEW.gula_darah_puasa IS NOT NULL THEN NEW.harga_gdp ELSE 0 END) +
        (CASE WHEN NEW.gula_darah_2jam_pp IS NOT NULL THEN NEW.harga_gd2pp ELSE 0 END) +
        (CASE WHEN NEW.cholesterol_total IS NOT NULL THEN NEW.harga_chol_total ELSE 0 END) +
        (CASE WHEN NEW.cholesterol_hdl IS NOT NULL THEN NEW.harga_chol_hdl ELSE 0 END) +
        (CASE WHEN NEW.cholesterol_ldl IS NOT NULL THEN NEW.harga_chol_ldl ELSE 0 END) +
        (CASE WHEN NEW.trigliserida IS NOT NULL THEN NEW.harga_trigliserida ELSE 0 END) +
        (CASE WHEN NEW.asam_urat IS NOT NULL THEN NEW.harga_asam_urat ELSE 0 END) +
        (CASE WHEN NEW.ureum IS NOT NULL THEN NEW.harga_ureum ELSE 0 END) +
        (CASE WHEN NEW.creatinin IS NOT NULL THEN NEW.harga_creatinin ELSE 0 END) +
        (CASE WHEN NEW.sgpt IS NOT NULL THEN NEW.harga_sgpt ELSE 0 END) +
        (CASE WHEN NEW.sgot IS NOT NULL THEN NEW.harga_sgot ELSE 0 END);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_kimia_update_invoice` AFTER INSERT ON `kimia_darah` FOR EACH ROW BEGIN
    CALL sp_update_invoice_from_examination(NEW.pemeriksaan_id);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_kimia_update_invoice_on_update` AFTER UPDATE ON `kimia_darah` FOR EACH ROW BEGIN
    CALL sp_update_invoice_from_examination(NEW.pemeriksaan_id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `lab`
--

CREATE TABLE `lab` (
  `lab_id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab`
--

INSERT INTO `lab` (`lab_id`, `nama`, `alamat`, `telephone`, `email`, `created_at`) VALUES
(1, 'Laboratorium Labsy', 'Jl. Tata Bumi No.3, Area Sawah, Banyuraden, Kec. Gamping, Kabupaten Sleman, Daerah Istimewa Yogyakarta 55293', '(0274) 617601', 'info@labsy.com', '2025-08-28 10:41:13');

-- --------------------------------------------------------

--
-- Table structure for table `laporan`
--

CREATE TABLE `laporan` (
  `laporan_id` int(11) NOT NULL,
  `pemeriksaan_id` int(11) NOT NULL,
  `jenis_laporan` varchar(100) DEFAULT NULL,
  `isi_laporan` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `dibuat_oleh` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `master_kondisi_sampel`
--

CREATE TABLE `master_kondisi_sampel` (
  `kondisi_id` int(11) NOT NULL,
  `jenis_sampel` enum('whole_blood','serum','plasma','urin','feses','sputum') NOT NULL,
  `kode_kondisi` varchar(50) NOT NULL COMMENT 'Kode unik, misal: WB_HEMOLISIS',
  `nama_kondisi` varchar(100) NOT NULL COMMENT 'Label yang ditampilkan',
  `kategori` enum('normal','warning','critical') DEFAULT 'normal',
  `deskripsi` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `urutan` int(11) DEFAULT 0 COMMENT 'Untuk sorting display',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_kondisi_sampel`
--

INSERT INTO `master_kondisi_sampel` (`kondisi_id`, `jenis_sampel`, `kode_kondisi`, `nama_kondisi`, `kategori`, `deskripsi`, `is_active`, `urutan`, `created_at`) VALUES
(1, 'whole_blood', 'WB_NORMAL', 'Normal', 'normal', NULL, 1, 1, '2025-12-08 11:37:17'),
(2, 'whole_blood', 'WB_HEMOLISIS', 'Hemolisis', 'critical', NULL, 1, 2, '2025-12-08 11:37:17'),
(3, 'whole_blood', 'WB_LIPEMIA', 'Lipemia', 'warning', NULL, 1, 3, '2025-12-08 11:37:17'),
(4, 'whole_blood', 'WB_IKTERIK', 'Ikterik', 'warning', NULL, 1, 4, '2025-12-08 11:37:17'),
(5, 'whole_blood', 'WB_KOAGULASI', 'Koagulasi Parsial', 'critical', NULL, 1, 5, '2025-12-08 11:37:17'),
(6, 'whole_blood', 'WB_VOLUME_KURANG', 'Volume Kurang', 'warning', NULL, 1, 6, '2025-12-08 11:37:17'),
(7, 'whole_blood', 'WB_VOLUME_LEBIH', 'Volume Lebih', 'normal', NULL, 1, 7, '2025-12-08 11:37:17'),
(8, 'whole_blood', 'WB_KONTAMINASI', 'Kontaminasi', 'critical', NULL, 1, 8, '2025-12-08 11:37:17'),
(9, 'serum', 'SR_NORMAL', 'Normal', 'normal', NULL, 1, 1, '2025-12-08 11:37:17'),
(10, 'serum', 'SR_HEMOLISIS', 'Hemolisis', 'critical', NULL, 1, 2, '2025-12-08 11:37:17'),
(11, 'serum', 'SR_LIPEMIA', 'Lipemia', 'warning', NULL, 1, 3, '2025-12-08 11:37:17'),
(12, 'serum', 'SR_IKTERIK', 'Ikterik', 'warning', NULL, 1, 4, '2025-12-08 11:37:17'),
(13, 'serum', 'SR_KOAGULASI', 'Koagulasi Parsial', 'critical', NULL, 1, 5, '2025-12-08 11:37:17'),
(14, 'serum', 'SR_VOLUME_KURANG', 'Volume Kurang', 'warning', NULL, 1, 6, '2025-12-08 11:37:17'),
(15, 'serum', 'SR_VOLUME_LEBIH', 'Volume Lebih', 'normal', NULL, 1, 7, '2025-12-08 11:37:17'),
(16, 'serum', 'SR_KONTAMINASI', 'Kontaminasi', 'critical', NULL, 1, 8, '2025-12-08 11:37:17'),
(17, 'plasma', 'PL_NORMAL', 'Normal', 'normal', NULL, 1, 1, '2025-12-08 11:37:17'),
(18, 'plasma', 'PL_HEMOLISIS', 'Hemolisis', 'critical', NULL, 1, 2, '2025-12-08 11:37:17'),
(19, 'plasma', 'PL_LIPEMIA', 'Lipemia', 'warning', NULL, 1, 3, '2025-12-08 11:37:17'),
(20, 'plasma', 'PL_IKTERIK', 'Ikterik', 'warning', NULL, 1, 4, '2025-12-08 11:37:17'),
(21, 'plasma', 'PL_KOAGULASI', 'Koagulasi Parsial', 'critical', NULL, 1, 5, '2025-12-08 11:37:17'),
(22, 'plasma', 'PL_VOLUME_KURANG', 'Volume Kurang', 'warning', NULL, 1, 6, '2025-12-08 11:37:17'),
(23, 'plasma', 'PL_VOLUME_LEBIH', 'Volume Lebih', 'normal', NULL, 1, 7, '2025-12-08 11:37:17'),
(24, 'plasma', 'PL_KONTAMINASI', 'Kontaminasi', 'critical', NULL, 1, 8, '2025-12-08 11:37:17'),
(25, 'urin', 'UR_NORMAL', 'Normal', 'normal', NULL, 1, 1, '2025-12-08 11:37:17'),
(26, 'urin', 'UR_KERUH', 'Keruh', 'warning', NULL, 1, 2, '2025-12-08 11:37:17'),
(27, 'urin', 'UR_MERAH_COKLAT', 'Merah/Kecoklatan', 'warning', NULL, 1, 3, '2025-12-08 11:37:17'),
(28, 'urin', 'UR_KUNING_GELAP', 'Kuning Gelap', 'normal', NULL, 1, 4, '2025-12-08 11:37:17'),
(29, 'urin', 'UR_BUSA', 'Busa Berlebihan', 'warning', NULL, 1, 5, '2025-12-08 11:37:17'),
(30, 'urin', 'UR_BAU', 'Bau Menyengat', 'warning', NULL, 1, 6, '2025-12-08 11:37:17'),
(31, 'feses', 'FS_NORMAL', 'Normal', 'normal', NULL, 1, 1, '2025-12-08 11:37:17'),
(32, 'feses', 'FS_DIARE', 'Diare', 'warning', NULL, 1, 2, '2025-12-08 11:37:17'),
(33, 'feses', 'FS_LEMBEK', 'Lembek/Berminyak', 'normal', NULL, 1, 3, '2025-12-08 11:37:17'),
(34, 'feses', 'FS_HITAM', 'Hitam', 'warning', NULL, 1, 4, '2025-12-08 11:37:17'),
(35, 'feses', 'FS_MERAH', 'Merah Segar', 'critical', NULL, 1, 5, '2025-12-08 11:37:17'),
(36, 'feses', 'FS_PUCAT', 'Pucat', 'warning', NULL, 1, 6, '2025-12-08 11:37:17'),
(37, 'feses', 'FS_BAU', 'Bau Menyengat', 'warning', NULL, 1, 7, '2025-12-08 11:37:17'),
(38, 'feses', 'FS_LENDIR', 'Ada Lendir', 'warning', NULL, 1, 8, '2025-12-08 11:37:17'),
(39, 'feses', 'FS_DARAH_SAMAR', 'Ada Darah Samar', 'critical', NULL, 1, 9, '2025-12-08 11:37:17'),
(40, 'sputum', 'SP_MUKOID', 'Mukoid', 'normal', NULL, 1, 1, '2025-12-08 11:37:17'),
(41, 'sputum', 'SP_PURULEN', 'Purulen', 'warning', NULL, 1, 2, '2025-12-08 11:37:17'),
(42, 'sputum', 'SP_MUKOPURULEN', 'Mukopurulen', 'warning', NULL, 1, 3, '2025-12-08 11:37:17'),
(43, 'sputum', 'SP_BERDARAH', 'Berdarah', 'critical', NULL, 1, 4, '2025-12-08 11:37:17'),
(44, 'sputum', 'SP_BERBUIH', 'Berbuih', 'warning', NULL, 1, 5, '2025-12-08 11:37:17'),
(45, 'sputum', 'SP_KENTAL', 'Kental', 'normal', NULL, 1, 6, '2025-12-08 11:37:17'),
(46, 'sputum', 'SP_BAU', 'Bau Busuk', 'warning', NULL, 1, 7, '2025-12-08 11:37:17');

-- --------------------------------------------------------

--
-- Table structure for table `mls`
--

CREATE TABLE `mls` (
  `mls_id` int(11) NOT NULL,
  `pemeriksaan_id` int(11) NOT NULL,
  `jenis_tes` varchar(100) DEFAULT NULL,
  `hasil` varchar(500) DEFAULT NULL,
  `nilai_rujukan` varchar(100) DEFAULT NULL,
  `satuan` varchar(50) DEFAULT NULL,
  `metode` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pasien`
--

CREATE TABLE `pasien` (
  `pasien_id` int(11) NOT NULL,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pasien`
--

INSERT INTO `pasien` (`pasien_id`, `nama`, `nik`, `jenis_kelamin`, `tempat_lahir`, `tanggal_lahir`, `umur`, `alamat_domisili`, `pekerjaan`, `telepon`, `kontak_darurat`, `riwayat_pasien`, `permintaan_pemeriksaan`, `dokter_perujuk`, `asal_rujukan`, `nomor_rujukan`, `tanggal_rujukan`, `diagnosis_awal`, `rekomendasi_pemeriksaan`, `nomor_registrasi`, `created_at`) VALUES
(2, 'Budi yanti', '3175081234567890', 'L', 'Yogyakarta', '1995-07-15', 30, 'Jl. Kesehatan No. 123, Jogja Selatan', 'Karyawan Negri', '08123456780', 'Siti Santoso - 08123456788', 'Paru-paru', 'TBC', 'Dr. Ahmad Rahman, Sp.PD', 'RS Umum Jakarta', 'RUJ/2025/001', '2025-09-06', 'Suspek Diabetes', 'Gula darah puasa, HbA1c', 'REG20250002', '2025-09-05 19:48:18'),
(3, 'Siti Rahayup', '3175082345678901', 'P', 'Bandung', '1995-07-08', 30, 'Jl. Melati No. 456, Bandung', 'Guru', '08234567890', 'Ahmad Rahayu - 08234567889', 'Diabetes tipe 2', 'Gula Darah, Urinologi', 'Dr. Siti Nurhaliza, Sp.PD', 'Klinik Sehat Bandung', 'RUJ/2025/002', '2025-09-06', 'Kontrol Diabetes', 'Gula darah, Urin lengkap', 'REG20250003', '2025-09-05 19:48:18'),
(4, 'Ahmad Wijaya ', '3175083456789012', 'L', 'Yogyakarta', '1978-12-10', 46, 'Jl. Kenanga No. 789, Surabaya', 'Wiraswasta', '08345678901', 'Rina Wijaya - 08345678900', 'Kolesterol tinggi', 'Kimia Darah', 'Dr. Bambang Sutopo, Sp.JP', 'RS Jantung Surabaya', 'RUJ/2025/003', '2025-09-06', 'Dislipidemia', 'Profil lipid lengkap', 'REG20250004', '2025-09-05 19:48:18'),
(5, 'Rina Sari', '3175084567890121', 'P', 'Yogyakarta', '1995-05-18', 29, 'Jl. Anggrek No. 321, Yogyakarta', 'Dokter', '08456789012', 'Doni Sari - 08456789011', 'Sehat', 'Medical Check Up', 'Dr. Retno Wulan, Sp.OG', 'RS Ibu dan Anak Yogya', 'RUJ/2025/004', '2025-09-07', 'MCU Pranikah', 'Lab lengkap, TORCH', 'REG20250005', '2025-09-05 19:48:18'),
(6, 'Doni Pratama', '3175085678901234', 'L', 'Medan', '1987-09-25', 37, 'Jl. Cempaka No. 654, Medan', 'Insinyur', '08567890123', 'Maya Pratama - 08567890122', 'Asam urat tinggi', 'Kimia Darah, Hematologi', 'Dr. Indra Gunawan, Sp.PD', 'Klinik Pratama Medan', 'RUJ/2025/005', '2025-09-07', 'Hiperurisemia', 'Asam urat, fungsi ginjal', 'REG20250006', '2025-09-05 19:48:18'),
(7, 'Maya Indah', '3175086789012345', 'P', 'Makassar', '1992-02-14', 32, 'Jl. Sakura No. 987, Makassar', 'Perawat', '08678901234', 'Andi Indah - 08678901233', 'Anemia ringan', 'Hematologi, Urinologi', 'Dr. Andi Mappaware, Sp.PD', 'RS Wahidin Makassar', 'RUJ/2025/006', '2025-09-07', 'Anemia Defisiensi Besi', 'Hemoglobin, Ferritin', 'REG20250007', '2025-09-05 19:48:18'),
(8, 'Andi Susanto', '3175087890123456', 'L', 'Palembang', '1983-11-30', 41, 'Jl. Dahlia No. 147, Palembang', 'Manager', '08789012345', 'Linda Susanto - 08789012344', 'Hipertensi', 'Kimia Darah', 'Dr. Hasan Basri, Sp.PD', 'RS Mohammad Hoesin', 'RUJ/2025/007', '2025-09-07', 'Hipertensi Grade 2', 'Fungsi ginjal, elektrolit', 'REG20250008', '2025-09-05 19:48:18'),
(9, 'Linda Suheri', '3175088901234567', 'L', 'Semarang', '2000-06-15', 25, 'Jl. Tulip No. 258, Semarang', 'Entreperenurship', '08890123456', 'Rudi Kartika - 08890123455', 'Sehat', 'Serologi, TBC', 'Dr. Wahyu Indarto, Sp.P', 'RS Kariadi Semarang', 'RUJ/2025/008', '2025-09-07', 'Suspek TB Paru', 'Dahak BTA, TCM', 'REG20250009', '2025-09-05 19:48:18'),
(10, 'Rudi Hermawan', '3175089012345678', 'L', 'Denpasar', '1991-04-12', 33, 'Jl. Kamboja No. 369, Denpasar', 'Pilot', '08901234567', 'Dewi Hermawan - 08901234566', 'Sehat', 'Medical Check Up', 'Dr. Made Wirawan, Sp.KO', 'RS Sanglah Denpasar', 'RUJ/2025/009', '2025-09-07', 'MCU Profesi', 'Lab lengkap, EKG', 'REG20250010', '2025-09-05 19:48:18'),
(11, 'Dewi Lestari', '3175090123456789', 'P', 'Balikpapan', '1986-06-28', 38, 'Jl. Marigold No. 741, Balikpapan', 'Farmasis', '09012345678', 'Agus Lestari - 09012345677', 'Kolesterol tinggi', 'Kimia Darah, Hematologi', 'Dr. Yusuf Rahman, Sp.PD', 'RS Pertamina Balikpapan', 'RUJ/2025/010', '2025-09-07', 'Dislipidemia', 'Profil lipid, HbA1c', 'REG20250011', '2025-09-05 19:48:18'),
(12, 'Ochaa Terbaru', '3175081234567889', 'P', 'Brebes', '2004-01-17', 21, 'Kost Putra Salsabiela 2,Jl.Lawu, Seturan, Caturtunggal, Depok (Jl. Lawu 05 no 03) DEPOK, KAB. SLEMAN, DI YOGYAKARTA', 'Wiraswasta', '08123456780', 'Siti Santoso - 08123456788', 'Batuk', 'TBC', 'Dr. Ahmad Rahman, Sp.PD', 'JIH', 'JIH/2025/003', '2025-09-18', 'Batuk berdarah', 'TCM', 'REG20250012', '2025-09-16 07:20:14'),
(13, 'Calista Meidiana', '3175088901234345', 'P', 'Bandung', '2014-08-11', 11, 'Jl. Melati No. 45, Kel. Sukamaju, Kec. Cicendo, Kota Bandung, Jawa Barat', 'Akuntan', '08234567867', 'Riko Pramana- 08890123455', 'Pasien memiliki riwayat hipertensi sejak tahun 2020, rutin minum obat Amlodipine 5 mg per hari. Tidak ada riwayat alergi obat.', 'Pemeriksaan laboratorium darah lengkap, cek fungsi ginjal, dan EKG.', 'Dr. Siti Nurhaliza, Sp.PD', 'Klinik Sehat Bandung', 'KSB/2025/001', '2025-09-24', 'Hipertensi dengan dugaan komplikasi ginjal.', 'Pemeriksaan penunjang berupa USG abdomen dan konsultasi spesialis penyakit dalam.', 'REG20250013', '2025-09-29 12:21:53'),
(14, 'Adi Putra ', '1272131231231231', 'L', 'Payakumbuh', '2006-01-05', 19, 'Yohyakarya', 'Marketing digital', '085282182721', 'suryanto- 081243192131', 'Pasien mengalami nyeri dada sejak 2 minggu terakhir, terutama saat beraktivitas. Riwayat hipertensi 5 tahun, rutin minum obat, tidak ada riwayat alergi obat.', 'Dilakukan pemeriksaan EKG, foto thorax, serta tes darah lengkap untuk evaluasi fungsi jantung.', 'dr. Andi Suharto Wijaya, Sp.PD', 'Klinik Sehat Sentosa', 'RJN-2025-0929-001', '2025-09-30', 'Angina Pektoris (nyeri dada akibat iskemia jantung)', 'Pemeriksaan laboratorium, EKG, rujuk ke spesialis jantung untuk evaluasi lanjutan.', 'REG20250014', '2025-09-30 07:31:44'),
(17, 'Rizki Pangestu', '3354642324324234', 'L', 'payakumbuh', '2021-03-08', 4, 'Jl.Lawu, Seturan, Caturtunggal, Depok (Jl. Lawu 05 no 03) DEPOK, KAB. SLEMAN, DI YOGYAKARTA', 'Marketing', '085282182747', 'Rina Wijaya - 08345678900', 'Pasien perempuan, 45 tahun, mengeluhkan nyeri perut kanan atas sejak 2 minggu terakhir. Nyeri terasa hilang timbul, disertai mual, tidak ada muntah darah atau diare. Riwayat hipertensi sejak 5 tahun, rutin konsumsi obat. Tidak ada alergi obat yang diketahui.', 'USG Abdomen untuk evaluasi kelainan hepatobilier.', 'Dr. Bambang Sutopo, Sp.JP', 'Puskesmas Sukamaju', 'JIH/2025/003', '2025-09-30', 'Dugaan Kolelitiasis (batu empedu)', 'Dugaan Kolelitiasis (batu empedu)', 'REG20250015', '2025-10-01 03:57:25'),
(19, 'Hatta Pramana', '3175081234567891', 'L', 'payakumbuh', '2025-11-14', 0, 'Kost Putra Salsabiela 2,Jl.Lawu, Seturan, Caturtunggal, Depok (Jl. Lawu 05 no 03) DEPOK, KAB. SLEMAN, DI YOGYAKARTA', 'Marketing', '085282182747', 'Rina Wijaya - 08345678900', 'Batuk', 'Tbc', 'Dr. Bambang Sutopo, Sp.JP', 'RS Jantung Surabaya', 'RUJ/2025/003', '2025-11-19', 'Tbc', 'TBC', 'REG20250016', '2025-11-01 02:18:13'),
(20, 'Firdaus', '3175081234567880', 'L', 'payakumbuh', '2025-11-05', 0, 'Kost maguwo,sleman jogjakarta', 'Administrasi', '085282182747', 'Rina Wijaya - 08345678900', 'TBC +', 'TBC +', 'dr. Andi Setiawan, Sp.PD', 'RS Umum Jakarta', 'RUJ/2025/003', '2025-11-13', 'Batuk 3 minggu', 'TBC Microsofis', 'REG20250017', '2025-11-01 03:17:50'),
(21, 'Vewe', '3175084567890127', 'L', 'Yogyakarta', '2025-12-12', 0, 'yogya', 'Guru', '0852-8218-2747', 'Ahmad Rahayu - 08234567889', 'tbc', 'tbc', 'Dr. Retno Wulan, Sp.OG', 'RS Ibu dan Anak Yogya', 'RUJ/2025/002', '2025-12-08', 'tbc', 'tbc', 'REG20250018', '2025-12-19 17:52:06'),
(22, 'Rinas sari', '1234123535123124', 'P', 'Bandung', '2025-12-31', 0, 'Yogyakarta', 'Guru', '08456789012', 'Doni Sari - 08456789011', 'BATUK', 'TBC', 'Dr. Retno Wulan, Sp.OG', 'RS Ibu dan Anak Yogya', 'RUJ/2025/004', '2025-12-26', 'TBC', 'SAKIT', 'REG20250019', '2025-12-25 13:04:04');

-- --------------------------------------------------------

--
-- Table structure for table `patient_requests`
--

CREATE TABLE `patient_requests` (
  `permintaan_id` int(11) NOT NULL,
  `pasien_id` int(11) DEFAULT NULL,
  `judul_permintaan` varchar(200) NOT NULL,
  `deskripsi_permintaan` text NOT NULL,
  `prioritas` enum('low','medium','high','urgent') DEFAULT 'medium',
  `status` enum('pending','approved','rejected','completed') DEFAULT 'pending',
  `catatan` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `processed_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `processed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pemeriksaan_detail`
--

CREATE TABLE `pemeriksaan_detail` (
  `detail_id` int(11) NOT NULL,
  `pemeriksaan_id` int(11) NOT NULL,
  `jenis_pemeriksaan` varchar(100) NOT NULL,
  `sub_pemeriksaan` text DEFAULT NULL COMMENT 'JSON array sub pemeriksaan',
  `urutan` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pemeriksaan_detail`
--

INSERT INTO `pemeriksaan_detail` (`detail_id`, `pemeriksaan_id`, `jenis_pemeriksaan`, `sub_pemeriksaan`, `urutan`, `created_at`) VALUES
(1, 24, 'Kimia Darah', '[\"gula_darah_sewaktu\",\"gula_darah_puasa\",\"gula_darah_2jam_pp\",\"cholesterol_total\"]', 1, '2025-12-09 00:23:08'),
(2, 24, 'Urinologi', '[\"protein\"]', 2, '2025-12-09 00:23:08'),
(3, 25, 'Urinologi', '[\"urin_rutin\",\"protein\"]', 1, '2025-12-09 00:52:48'),
(4, 26, 'Urinologi', '[\"urin_rutin\",\"protein\"]', 1, '2025-12-09 00:55:20'),
(5, 26, 'Hematologi', '[\"paket_darah_rutin\",\"laju_endap_darah\",\"clotting_time\"]', 2, '2025-12-09 00:55:20'),
(6, 27, 'Hematologi', '[\"paket_darah_rutin\",\"laju_endap_darah\"]', 1, '2025-12-13 11:45:53'),
(7, 27, 'Kimia Darah', '[\"sgpt\",\"sgot\"]', 2, '2025-12-13 11:45:53'),
(8, 27, 'Urinologi', '[\"protein\"]', 3, '2025-12-13 11:45:53'),
(9, 28, 'TBC', '[\"dahak\"]', 1, '2025-12-13 11:46:28'),
(10, 29, 'Kimia Darah', '[\"gula_darah_sewaktu\",\"gula_darah_puasa\",\"gula_darah_2jam_pp\",\"cholesterol_total\",\"sgpt\",\"sgot\"]', 1, '2025-12-19 14:31:53'),
(11, 30, 'Kimia Darah', '[\"gula_darah_sewaktu\",\"gula_darah_puasa\",\"gula_darah_2jam_pp\",\"cholesterol_total\"]', 1, '2025-12-19 17:56:57'),
(12, 30, 'Hematologi', '[\"paket_darah_rutin\",\"laju_endap_darah\"]', 2, '2025-12-19 17:56:57'),
(13, 30, 'Serologi', '[\"rdt_antigen\",\"widal\",\"hbsag\",\"ns1\"]', 3, '2025-12-19 17:56:57'),
(14, 31, 'Kimia Darah', '[\"gula_darah_sewaktu\",\"gula_darah_puasa\",\"gula_darah_2jam_pp\",\"trigliserida\",\"asam_urat\",\"ureum\",\"creatinin\"]', 1, '2025-12-21 04:56:29'),
(15, 31, 'Serologi', NULL, 2, '2025-12-21 04:56:29'),
(16, 32, 'Hematologi', '[\"paket_darah_rutin\",\"laju_endap_darah\",\"clotting_time\"]', 1, '2025-12-21 08:21:49'),
(17, 33, 'Hematologi', '[\"paket_darah_rutin\",\"laju_endap_darah\",\"clotting_time\",\"bleeding_time\",\"golongan_darah\",\"malaria\"]', 1, '2025-12-21 08:42:35'),
(18, 34, 'Kimia Darah', '[\"gula_darah_sewaktu\",\"gula_darah_puasa\",\"gula_darah_2jam_pp\",\"cholesterol_total\",\"cholesterol_hdl\",\"cholesterol_ldl\",\"trigliserida\",\"asam_urat\",\"ureum\",\"creatinin\",\"sgpt\",\"sgot\"]', 1, '2025-12-21 12:22:20'),
(19, 34, 'TBC', '[\"dahak\",\"tcm\"]', 2, '2025-12-21 12:22:20'),
(20, 34, 'IMS', '[\"sifilis\",\"duh_tubuh\"]', 3, '2025-12-21 12:22:20'),
(21, 34, 'Hematologi', '[\"paket_darah_rutin\",\"laju_endap_darah\",\"clotting_time\",\"bleeding_time\",\"golongan_darah\"]', 4, '2025-12-21 12:22:20'),
(22, 35, 'Kimia Darah', '[\"trigliserida\",\"asam_urat\",\"ureum\",\"creatinin\",\"sgpt\",\"sgot\"]', 1, '2025-12-21 12:33:30'),
(23, 35, 'Serologi', '[\"rdt_antigen\",\"widal\",\"hbsag\",\"ns1\"]', 2, '2025-12-21 12:33:30'),
(24, 35, 'TBC', '[\"dahak\",\"tcm\"]', 3, '2025-12-21 12:33:30'),
(25, 36, 'Kimia Darah', '[\"gula_darah_sewaktu\",\"gula_darah_puasa\",\"gula_darah_2jam_pp\",\"cholesterol_total\",\"cholesterol_hdl\"]', 1, '2025-12-25 04:17:48'),
(26, 36, 'Urinologi', '[\"urin_rutin\",\"protein\",\"tes_kehamilan\"]', 2, '2025-12-25 04:17:48'),
(27, 36, 'IMS', '[\"sifilis\",\"duh_tubuh\"]', 3, '2025-12-25 04:17:48'),
(28, 37, 'TBC', '[\"dahak\",\"tcm\"]', 1, '2025-12-25 06:27:42'),
(29, 38, 'Kimia Darah', '[\"gula_darah_sewaktu\",\"gula_darah_puasa\",\"gula_darah_2jam_pp\",\"sgpt\",\"sgot\"]', 1, '2025-12-25 06:28:50'),
(30, 39, 'Hematologi', '[\"paket_darah_rutin\",\"clotting_time\",\"bleeding_time\",\"malaria\"]', 1, '2025-12-25 07:51:35');

--
-- Triggers `pemeriksaan_detail`
--
DELIMITER $$
CREATE TRIGGER `tr_update_jenis_pemeriksaan_main` AFTER INSERT ON `pemeriksaan_detail` FOR EACH ROW BEGIN
    DECLARE jenis_list TEXT;
    
    SELECT GROUP_CONCAT(jenis_pemeriksaan ORDER BY urutan SEPARATOR ', ')
    INTO jenis_list
    FROM pemeriksaan_detail
    WHERE pemeriksaan_id = NEW.pemeriksaan_id;
    
    UPDATE pemeriksaan_lab 
    SET jenis_pemeriksaan = jenis_list
    WHERE pemeriksaan_id = NEW.pemeriksaan_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_update_jenis_pemeriksaan_main_after_delete` AFTER DELETE ON `pemeriksaan_detail` FOR EACH ROW BEGIN
    DECLARE jenis_list TEXT;
    
    SELECT GROUP_CONCAT(jenis_pemeriksaan ORDER BY urutan SEPARATOR ', ')
    INTO jenis_list
    FROM pemeriksaan_detail
    WHERE pemeriksaan_id = OLD.pemeriksaan_id;
    
    UPDATE pemeriksaan_lab 
    SET jenis_pemeriksaan = IFNULL(jenis_list, '')
    WHERE pemeriksaan_id = OLD.pemeriksaan_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `pemeriksaan_lab`
--

CREATE TABLE `pemeriksaan_lab` (
  `pemeriksaan_id` int(11) NOT NULL,
  `pasien_id` int(11) NOT NULL,
  `petugas_id` int(11) DEFAULT NULL,
  `lab_id` int(11) DEFAULT NULL,
  `nomor_pemeriksaan` varchar(50) NOT NULL,
  `tanggal_pemeriksaan` date NOT NULL,
  `jenis_pemeriksaan` varchar(100) DEFAULT NULL,
  `status_pasien` enum('puasa','belum_puasa','minum_obat') DEFAULT NULL COMMENT 'Status kondisi pasien saat pemeriksaan',
  `keterangan_obat` text DEFAULT NULL COMMENT 'Nama obat jika status_pasien = minum_obat',
  `status_pemeriksaan` enum('pending','progress','selesai','cancelled') DEFAULT 'pending',
  `keterangan` text DEFAULT NULL,
  `biaya` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `sub_pemeriksaan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pemeriksaan_lab`
--

INSERT INTO `pemeriksaan_lab` (`pemeriksaan_id`, `pasien_id`, `petugas_id`, `lab_id`, `nomor_pemeriksaan`, `tanggal_pemeriksaan`, `jenis_pemeriksaan`, `status_pasien`, `keterangan_obat`, `status_pemeriksaan`, `keterangan`, `biaya`, `created_at`, `updated_at`, `completed_at`, `started_at`, `sub_pemeriksaan`) VALUES
(24, 20, 1, NULL, 'LAB20250001', '2025-12-10', 'Kimia Darah, Urinologi', 'belum_puasa', '', 'selesai', '', 0.00, '2025-12-09 00:23:08', '2025-12-14 00:29:22', '2025-12-14 00:29:22', '2025-12-09 00:24:53', NULL),
(25, 20, 1, NULL, 'LAB20250002', '2025-12-25', 'Urinologi', 'puasa', '', 'selesai', '', 0.00, '2025-12-09 00:52:48', '2025-12-19 14:15:39', '2025-12-19 14:15:39', '2025-12-09 00:53:39', NULL),
(26, 20, 1, NULL, 'LAB20250003', '2025-12-19', 'Urinologi, Hematologi', 'puasa', '', 'selesai', '', 0.00, '2025-12-09 00:55:20', '2025-12-19 13:40:24', '2025-12-19 13:40:24', '2025-12-09 00:55:35', NULL),
(27, 20, 1, NULL, 'LAB20250004', '2025-12-20', 'Hematologi, Kimia Darah, Urinologi', 'puasa', '', 'selesai', '', 0.00, '2025-12-13 11:45:53', '2025-12-19 13:52:49', '2025-12-19 13:52:49', '2025-12-13 11:46:59', NULL),
(28, 11, 1, NULL, 'LAB20250005', '2025-12-14', 'TBC', 'minum_obat', 'Amlopine', 'selesai', '', 480000.00, '2025-12-13 11:46:28', '2025-12-14 04:37:33', '2025-12-14 04:37:33', '2025-12-13 11:46:56', NULL),
(29, 17, 1, NULL, 'LAB20250006', '2025-12-19', 'Kimia Darah', 'puasa', '', 'selesai', '', 290000.00, '2025-12-19 14:31:53', '2025-12-19 14:34:19', '2025-12-19 14:34:19', '2025-12-19 14:32:46', NULL),
(30, 13, 1, NULL, 'LAB20250007', '2025-12-20', 'Kimia Darah, Hematologi, Serologi', 'minum_obat', 'Metformin 500mg', 'progress', '', NULL, '2025-12-19 17:56:57', '2025-12-19 17:57:15', NULL, '2025-12-19 17:57:15', NULL),
(31, 12, 1, NULL, 'LAB20250008', '2025-12-21', 'Kimia Darah, Serologi', 'puasa', '', 'selesai', 'di proses', 0.00, '2025-12-21 04:56:29', '2025-12-21 11:27:05', '2025-12-21 11:27:05', '2025-12-21 04:56:50', NULL),
(32, 8, 1, NULL, 'LAB20250009', '2025-12-23', 'Hematologi', 'puasa', '', 'selesai', 'di proses', 120000.00, '2025-12-21 08:21:49', '2025-12-21 12:16:26', '2025-12-21 12:16:26', '2025-12-21 09:30:02', NULL),
(33, 12, 1, NULL, 'LAB20250010', '2025-12-22', 'Hematologi', 'puasa', '', 'selesai', '', 120000.00, '2025-12-21 08:42:35', '2025-12-21 11:34:48', '2025-12-21 11:34:48', '2025-12-21 11:23:45', NULL),
(34, 20, 1, NULL, 'LAB20250011', '2025-12-23', 'Kimia Darah, TBC, IMS, Hematologi', 'belum_puasa', '', 'progress', '', NULL, '2025-12-21 12:22:20', '2025-12-21 12:32:13', NULL, '2025-12-21 12:32:13', NULL),
(35, 21, 1, NULL, 'LAB20250012', '2025-12-24', 'Kimia Darah, Serologi, TBC', 'puasa', '', 'selesai', '', 0.00, '2025-12-21 12:33:30', '2025-12-25 05:33:55', '2025-12-25 05:33:55', '2025-12-25 05:33:08', NULL),
(36, 11, 1, NULL, 'LAB20250013', '2025-12-25', 'Kimia Darah, Urinologi, IMS', 'puasa', '', 'selesai', '', 0.00, '2025-12-25 04:17:48', '2025-12-25 05:32:51', '2025-12-25 05:32:51', '2025-12-25 04:18:05', NULL),
(37, 6, 1, NULL, 'LAB20250014', '2025-12-25', 'TBC', 'puasa', '', 'progress', '', NULL, '2025-12-25 06:27:42', '2025-12-25 06:28:01', NULL, '2025-12-25 06:28:01', NULL),
(38, 19, 1, NULL, 'LAB20250015', '2025-12-26', 'Kimia Darah', 'puasa', '', 'progress', '', NULL, '2025-12-25 06:28:50', '2025-12-25 06:29:00', NULL, '2025-12-25 06:29:00', NULL),
(39, 8, 1, NULL, 'LAB20250016', '2025-12-26', 'Hematologi', 'belum_puasa', '', 'progress', '', NULL, '2025-12-25 07:51:35', '2025-12-25 07:51:48', NULL, '2025-12-25 07:51:48', NULL);

--
-- Triggers `pemeriksaan_lab`
--
DELIMITER $$
CREATE TRIGGER `tr_pemeriksaan_status_update` BEFORE UPDATE ON `pemeriksaan_lab` FOR EACH ROW BEGIN
    -- Always update the updated_at timestamp
    SET NEW.updated_at = NOW();
    
    -- Set started_at when status changes to 'progress'
    IF NEW.status_pemeriksaan = 'progress' AND OLD.status_pemeriksaan = 'pending' THEN
        SET NEW.started_at = NOW();
    END IF;
    
    -- Set completed_at when status changes to 'selesai'
    IF NEW.status_pemeriksaan = 'selesai' AND OLD.status_pemeriksaan != 'selesai' THEN
        SET NEW.completed_at = NOW();
    END IF;
    
    -- Clear completed_at if status changes from 'selesai' to something else
    IF NEW.status_pemeriksaan != 'selesai' AND OLD.status_pemeriksaan = 'selesai' THEN
        SET NEW.completed_at = NULL;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `pemeriksaan_sampel`
--

CREATE TABLE `pemeriksaan_sampel` (
  `sampel_id` int(11) NOT NULL,
  `pemeriksaan_id` int(11) NOT NULL,
  `jenis_sampel` varchar(50) NOT NULL,
  `keterangan_sampel` text DEFAULT NULL COMMENT 'Untuk sampel lain',
  `diambil_oleh` int(11) DEFAULT NULL COMMENT 'user_id petugas',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `tanggal_pengambilan` datetime DEFAULT NULL COMMENT 'Waktu sampel diambil',
  `petugas_pengambil_id` int(11) DEFAULT NULL COMMENT 'Petugas yang mengambil sampel',
  `status_sampel` enum('belum_diambil','sudah_diambil','diterima','ditolak') DEFAULT 'belum_diambil',
  `tanggal_evaluasi` datetime DEFAULT NULL COMMENT 'Waktu sampel dievaluasi',
  `petugas_evaluasi_id` int(11) DEFAULT NULL COMMENT 'Petugas yang evaluasi kondisi',
  `catatan_penolakan` text DEFAULT NULL COMMENT 'Alasan jika sampel ditolak',
  `kondisi_sampel` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Array kondisi_id dari master' CHECK (json_valid(`kondisi_sampel`)),
  `catatan_kondisi` text DEFAULT NULL COMMENT 'Catatan kondisi'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pemeriksaan_sampel`
--

INSERT INTO `pemeriksaan_sampel` (`sampel_id`, `pemeriksaan_id`, `jenis_sampel`, `keterangan_sampel`, `diambil_oleh`, `created_at`, `tanggal_pengambilan`, `petugas_pengambil_id`, `status_sampel`, `tanggal_evaluasi`, `petugas_evaluasi_id`, `catatan_penolakan`, `kondisi_sampel`, `catatan_kondisi`) VALUES
(1, 24, 'whole_blood', NULL, NULL, '2025-12-09 00:23:08', NULL, NULL, 'belum_diambil', NULL, NULL, NULL, NULL, NULL),
(2, 25, 'whole_blood', NULL, NULL, '2025-12-09 00:52:48', '2025-12-19 21:13:39', 1, 'diterima', '2025-12-19 21:13:48', 1, '', '[\"1\",\"4\"]', NULL),
(3, 26, 'whole_blood', NULL, NULL, '2025-12-09 00:55:20', '2025-12-13 18:22:54', 1, 'diterima', '2025-12-13 18:27:07', 1, '', '[\"1\",\"5\",\"1\",\"5\"]', NULL),
(4, 27, 'plasma', NULL, NULL, '2025-12-13 11:45:53', NULL, NULL, 'diterima', '2025-12-21 11:35:28', 1, NULL, '[\"17\", \"19\"]', 'Test trigger auto storage'),
(5, 28, 'serum', NULL, NULL, '2025-12-13 11:46:28', '2025-12-14 11:35:50', 1, 'diterima', '2025-12-14 11:36:01', 1, '', '[\"12\"]', NULL),
(6, 29, 'whole_blood', NULL, NULL, '2025-12-19 14:31:53', '2025-12-19 21:32:58', 1, 'diterima', '2025-12-19 21:33:08', 1, '', '[\"3\",\"4\"]', NULL),
(7, 29, 'serum', NULL, NULL, '2025-12-19 14:31:53', '2025-12-19 21:33:01', 1, 'diterima', '2025-12-19 21:33:19', 1, '', '[\"9\",\"11\",\"12\",\"15\"]', NULL),
(8, 30, 'whole_blood', NULL, NULL, '2025-12-19 17:56:57', '2025-12-20 18:53:57', 1, 'diterima', '2025-12-21 11:30:07', 1, '', '[\"1\",\"1\"]', NULL),
(9, 30, 'serum', NULL, NULL, '2025-12-19 17:56:57', '2025-12-20 18:55:10', 1, 'diterima', '2025-12-20 18:55:17', 1, '', '[\"9\",\"11\",\"14\"]', NULL),
(10, 31, 'whole_blood', NULL, NULL, '2025-12-21 04:56:29', '2025-12-21 11:57:00', 1, 'diterima', '2025-12-21 11:57:12', 1, '', '[\"1\",\"1\"]', NULL),
(11, 32, 'whole_blood', NULL, NULL, '2025-12-21 08:21:49', '2025-12-21 18:24:32', 1, 'diterima', '2025-12-21 18:24:41', 1, '', '[\"1\",\"1\"]', NULL),
(12, 33, 'whole_blood', NULL, NULL, '2025-12-21 08:42:35', NULL, NULL, 'belum_diambil', NULL, NULL, NULL, NULL, NULL),
(13, 34, 'whole_blood', NULL, NULL, '2025-12-21 12:22:20', NULL, NULL, 'belum_diambil', NULL, NULL, NULL, NULL, NULL),
(14, 35, 'whole_blood', NULL, NULL, '2025-12-21 12:33:30', NULL, NULL, 'belum_diambil', NULL, NULL, NULL, NULL, NULL),
(15, 35, 'serum', NULL, NULL, '2025-12-21 12:33:30', NULL, NULL, 'belum_diambil', NULL, NULL, NULL, NULL, NULL),
(16, 35, 'plasma', NULL, NULL, '2025-12-21 12:33:30', NULL, NULL, 'belum_diambil', NULL, NULL, NULL, NULL, NULL),
(17, 36, 'whole_blood', NULL, NULL, '2025-12-25 04:17:48', '2025-12-25 11:40:05', 1, 'diterima', '2025-12-25 12:05:24', 1, '', '[\"1\",\"2\",\"3\",\"4\"]', NULL),
(18, 36, 'plasma', NULL, NULL, '2025-12-25 04:17:48', NULL, NULL, 'belum_diambil', NULL, NULL, NULL, NULL, NULL),
(19, 37, 'whole_blood', NULL, NULL, '2025-12-25 06:27:42', NULL, NULL, 'belum_diambil', NULL, NULL, NULL, NULL, NULL),
(20, 38, 'whole_blood', NULL, NULL, '2025-12-25 06:28:50', NULL, NULL, 'belum_diambil', NULL, NULL, NULL, NULL, NULL),
(21, 39, 'whole_blood', NULL, NULL, '2025-12-25 07:51:35', '2025-12-25 18:00:23', 1, 'diterima', '2025-12-25 18:00:35', 1, '', '[\"1\",\"3\",\"4\",\"6\"]', NULL),
(22, 39, 'serum', NULL, NULL, '2025-12-25 07:51:35', '2025-12-25 18:00:26', 1, 'diterima', '2025-12-25 18:00:41', 1, '', '[\"9\",\"11\",\"14\",\"15\"]', NULL),
(23, 39, 'plasma', NULL, NULL, '2025-12-25 07:51:35', '2025-12-25 18:00:28', 1, 'diterima', '2025-12-25 18:00:47', 1, '', '[\"17\",\"19\",\"20\",\"22\",\"23\"]', NULL),
(24, 39, 'lain', 'Jenis: Tenggorokan', NULL, '2025-12-25 13:22:46', '2025-12-25 20:22:49', 1, 'diterima', '2025-12-25 20:22:53', 1, '', NULL, NULL);

--
-- Triggers `pemeriksaan_sampel`
--
DELIMITER $$
CREATE TRIGGER `tr_sampel_auto_storage` AFTER UPDATE ON `pemeriksaan_sampel` FOR EACH ROW BEGIN
    -- ✅ DECLARE harus di paling awal sebelum statement lain
    DECLARE v_masa_berlaku INT;
    DECLARE v_suhu_min DECIMAL(5,2);
    DECLARE v_suhu_max DECIMAL(5,2);
    
    -- Sekarang baru IF statement
    IF NEW.status_sampel = 'diterima' AND OLD.status_sampel != 'diterima' THEN
        
        -- Ambil informasi masa berlaku sampel
        SELECT masa_berlaku_hari, suhu_optimal_min, suhu_optimal_max
        INTO v_masa_berlaku, v_suhu_min, v_suhu_max
        FROM sampel_expiry
        WHERE jenis_sampel = NEW.jenis_sampel
        LIMIT 1;
        
        -- Insert ke sampel_storage dengan default values
        INSERT INTO sampel_storage (
            sampel_id,
            lokasi_penyimpanan,
            suhu_penyimpanan,
            tanggal_masuk,
            status_penyimpanan,
            petugas_id,
            keterangan
        ) VALUES (
            NEW.sampel_id,
            -- Default lokasi berdasarkan jenis sampel
            CASE NEW.jenis_sampel
                WHEN 'whole_blood' THEN 'Kulkas Lab Hematologi - Rak A'
                WHEN 'serum' THEN 'Kulkas Lab Kimia - Rak B'
                WHEN 'plasma' THEN 'Kulkas Lab Kimia - Rak C'
                WHEN 'urin' THEN 'Kulkas Lab Urinologi - Rak D'
                WHEN 'feses' THEN 'Kulkas Lab Mikrobiologi - Rak E'
                WHEN 'sputum' THEN 'Kulkas Lab TBC - Rak F'
                ELSE 'Storage Umum'
            END,
            -- Default suhu berdasarkan jenis sampel
            COALESCE(v_suhu_min, 4.00),
            NOW(),
            'tersimpan',
            NEW.petugas_evaluasi_id,
            CONCAT('Auto-stored after sample acceptance. Valid until: ', 
                   DATE_FORMAT(DATE_ADD(NEW.tanggal_evaluasi, INTERVAL COALESCE(v_masa_berlaku, 7) DAY), '%Y-%m-%d %H:%i:%s'))
        );
        
        -- Log ke activity_log
        INSERT INTO activity_log (
            user_id,
            activity,
            table_affected,
            record_id,
            ip_address
        ) VALUES (
            NEW.petugas_evaluasi_id,
            CONCAT('Sampel auto-stored: ', NEW.jenis_sampel),
            'sampel_storage',
            LAST_INSERT_ID(),
            '::1'
        );
        
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `petugas_lab`
--

CREATE TABLE `petugas_lab` (
  `petugas_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nama_petugas` varchar(100) NOT NULL,
  `jenis_keahlian` varchar(100) DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `petugas_lab`
--

INSERT INTO `petugas_lab` (`petugas_id`, `user_id`, `nama_petugas`, `jenis_keahlian`, `telepon`, `alamat`, `created_at`) VALUES
(1, 4, 'Sari Oktavia', 'Analis Laboratorium', '', '', '2025-08-28 10:41:13');

-- --------------------------------------------------------

--
-- Table structure for table `qc_parameters`
--

CREATE TABLE `qc_parameters` (
  `parameter_id` int(11) NOT NULL,
  `alat_id` int(11) DEFAULT NULL COMMENT 'FK ke alat, NULL jika parameter umum',
  `parameter_name` varchar(200) NOT NULL,
  `parameter_code` varchar(50) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL COMMENT 'Satuan pengukuran',
  `min_value` decimal(10,2) DEFAULT NULL,
  `max_value` decimal(10,2) DEFAULT NULL,
  `target_value` decimal(10,2) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `qc_parameters`
--

INSERT INTO `qc_parameters` (`parameter_id`, `alat_id`, `parameter_name`, `parameter_code`, `unit`, `min_value`, `max_value`, `target_value`, `is_active`, `created_at`) VALUES
(8, 1, 'Suhu Operasional', 'TEMP', '░C', 20.00, 30.00, 25.00, 1, '2025-12-25 05:21:26'),
(9, 1, 'Kondisi Fisik', 'PHYSICAL', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(10, 1, 'Kalibrasi', 'CALIBRATION', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(11, 1, 'Akurasi', 'ACCURACY', '%', 95.00, 100.00, 98.00, 1, '2025-12-25 05:21:26'),
(12, 1, 'Presisi', 'PRECISION', '%CV', 0.00, 5.00, 2.00, 1, '2025-12-25 05:21:26'),
(13, 1, 'Kebersihan', 'CLEANLINESS', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(14, 1, 'Fungsi Utama', 'FUNCTION', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(15, 1, 'Stabilitas', 'STABILITY', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(16, 2, 'Suhu Operasional', 'TEMP', '░C', 20.00, 30.00, 25.00, 1, '2025-12-25 05:21:26'),
(17, 2, 'Kondisi Fisik', 'PHYSICAL', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(18, 2, 'Kalibrasi', 'CALIBRATION', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(19, 2, 'Akurasi', 'ACCURACY', '%', 95.00, 100.00, 98.00, 1, '2025-12-25 05:21:26'),
(20, 2, 'Presisi', 'PRECISION', '%CV', 0.00, 5.00, 2.00, 1, '2025-12-25 05:21:26'),
(21, 2, 'Kebersihan', 'CLEANLINESS', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(22, 2, 'Fungsi Utama', 'FUNCTION', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(23, 2, 'Stabilitas', 'STABILITY', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(24, 3, 'Suhu Operasional', 'TEMP', '░C', 20.00, 30.00, 25.00, 1, '2025-12-25 05:21:26'),
(25, 3, 'Kondisi Fisik', 'PHYSICAL', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(26, 3, 'Kalibrasi', 'CALIBRATION', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(27, 3, 'Akurasi', 'ACCURACY', '%', 95.00, 100.00, 98.00, 1, '2025-12-25 05:21:26'),
(28, 3, 'Presisi', 'PRECISION', '%CV', 0.00, 5.00, 2.00, 1, '2025-12-25 05:21:26'),
(29, 3, 'Kebersihan', 'CLEANLINESS', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(30, 3, 'Fungsi Utama', 'FUNCTION', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(31, 3, 'Stabilitas', 'STABILITY', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(32, 4, 'Suhu Operasional', 'TEMP', '░C', 20.00, 30.00, 25.00, 1, '2025-12-25 05:21:26'),
(33, 4, 'Kondisi Fisik', 'PHYSICAL', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(34, 4, 'Kalibrasi', 'CALIBRATION', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(35, 4, 'Akurasi', 'ACCURACY', '%', 95.00, 100.00, 98.00, 1, '2025-12-25 05:21:26'),
(36, 4, 'Presisi', 'PRECISION', '%CV', 0.00, 5.00, 2.00, 1, '2025-12-25 05:21:26'),
(37, 4, 'Kebersihan', 'CLEANLINESS', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(38, 4, 'Fungsi Utama', 'FUNCTION', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(39, 4, 'Stabilitas', 'STABILITY', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(40, 5, 'Suhu Operasional', 'TEMP', '░C', 20.00, 30.00, 25.00, 1, '2025-12-25 05:21:26'),
(41, 5, 'Kondisi Fisik', 'PHYSICAL', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(42, 5, 'Kalibrasi', 'CALIBRATION', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(43, 5, 'Akurasi', 'ACCURACY', '%', 95.00, 100.00, 98.00, 1, '2025-12-25 05:21:26'),
(44, 5, 'Presisi', 'PRECISION', '%CV', 0.00, 5.00, 2.00, 1, '2025-12-25 05:21:26'),
(45, 5, 'Kebersihan', 'CLEANLINESS', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(46, 5, 'Fungsi Utama', 'FUNCTION', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(47, 5, 'Stabilitas', 'STABILITY', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(48, 6, 'Suhu Operasional', 'TEMP', '░C', 20.00, 30.00, 25.00, 1, '2025-12-25 05:21:26'),
(49, 6, 'Kondisi Fisik', 'PHYSICAL', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(50, 6, 'Kalibrasi', 'CALIBRATION', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(51, 6, 'Akurasi', 'ACCURACY', '%', 95.00, 100.00, 98.00, 1, '2025-12-25 05:21:26'),
(52, 6, 'Presisi', 'PRECISION', '%CV', 0.00, 5.00, 2.00, 1, '2025-12-25 05:21:26'),
(53, 6, 'Kebersihan', 'CLEANLINESS', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(54, 6, 'Fungsi Utama', 'FUNCTION', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(55, 6, 'Stabilitas', 'STABILITY', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(56, 7, 'Suhu Operasional', 'TEMP', '░C', 20.00, 30.00, 25.00, 1, '2025-12-25 05:21:26'),
(57, 7, 'Kondisi Fisik', 'PHYSICAL', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(58, 7, 'Kalibrasi', 'CALIBRATION', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(59, 7, 'Akurasi', 'ACCURACY', '%', 95.00, 100.00, 98.00, 1, '2025-12-25 05:21:26'),
(60, 7, 'Presisi', 'PRECISION', '%CV', 0.00, 5.00, 2.00, 1, '2025-12-25 05:21:26'),
(61, 7, 'Kebersihan', 'CLEANLINESS', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(62, 7, 'Fungsi Utama', 'FUNCTION', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(63, 7, 'Stabilitas', 'STABILITY', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(64, 8, 'Suhu Operasional', 'TEMP', '░C', 20.00, 30.00, 25.00, 1, '2025-12-25 05:21:26'),
(65, 8, 'Kondisi Fisik', 'PHYSICAL', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(66, 8, 'Kalibrasi', 'CALIBRATION', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(67, 8, 'Akurasi', 'ACCURACY', '%', 95.00, 100.00, 98.00, 1, '2025-12-25 05:21:26'),
(68, 8, 'Presisi', 'PRECISION', '%CV', 0.00, 5.00, 2.00, 1, '2025-12-25 05:21:26'),
(69, 8, 'Kebersihan', 'CLEANLINESS', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(70, 8, 'Fungsi Utama', 'FUNCTION', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(71, 8, 'Stabilitas', 'STABILITY', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(72, 9, 'Suhu Operasional', 'TEMP', '░C', 20.00, 30.00, 25.00, 1, '2025-12-25 05:21:26'),
(73, 9, 'Kondisi Fisik', 'PHYSICAL', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(74, 9, 'Kalibrasi', 'CALIBRATION', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(75, 9, 'Akurasi', 'ACCURACY', '%', 95.00, 100.00, 98.00, 1, '2025-12-25 05:21:26'),
(76, 9, 'Presisi', 'PRECISION', '%CV', 0.00, 5.00, 2.00, 1, '2025-12-25 05:21:26'),
(77, 9, 'Kebersihan', 'CLEANLINESS', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(78, 9, 'Fungsi Utama', 'FUNCTION', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(79, 9, 'Stabilitas', 'STABILITY', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(80, 10, 'Suhu Operasional', 'TEMP', '░C', 20.00, 30.00, 25.00, 1, '2025-12-25 05:21:26'),
(81, 10, 'Kondisi Fisik', 'PHYSICAL', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(82, 10, 'Kalibrasi', 'CALIBRATION', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(83, 10, 'Akurasi', 'ACCURACY', '%', 95.00, 100.00, 98.00, 1, '2025-12-25 05:21:26'),
(84, 10, 'Presisi', 'PRECISION', '%CV', 0.00, 5.00, 2.00, 1, '2025-12-25 05:21:26'),
(85, 10, 'Kebersihan', 'CLEANLINESS', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(86, 10, 'Fungsi Utama', 'FUNCTION', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(87, 10, 'Stabilitas', 'STABILITY', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(88, 11, 'Suhu Operasional', 'TEMP', '░C', 20.00, 30.00, 25.00, 1, '2025-12-25 05:21:26'),
(89, 11, 'Kondisi Fisik', 'PHYSICAL', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(90, 11, 'Kalibrasi', 'CALIBRATION', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(91, 11, 'Akurasi', 'ACCURACY', '%', 95.00, 100.00, 98.00, 1, '2025-12-25 05:21:26'),
(92, 11, 'Presisi', 'PRECISION', '%CV', 0.00, 5.00, 2.00, 1, '2025-12-25 05:21:26'),
(93, 11, 'Kebersihan', 'CLEANLINESS', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(94, 11, 'Fungsi Utama', 'FUNCTION', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(95, 11, 'Stabilitas', 'STABILITY', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(96, 12, 'Suhu Operasional', 'TEMP', '░C', 20.00, 30.00, 25.00, 1, '2025-12-25 05:21:26'),
(97, 12, 'Kondisi Fisik', 'PHYSICAL', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(98, 12, 'Kalibrasi', 'CALIBRATION', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(99, 12, 'Akurasi', 'ACCURACY', '%', 95.00, 100.00, 98.00, 1, '2025-12-25 05:21:26'),
(100, 12, 'Presisi', 'PRECISION', '%CV', 0.00, 5.00, 2.00, 1, '2025-12-25 05:21:26'),
(101, 12, 'Kebersihan', 'CLEANLINESS', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(102, 12, 'Fungsi Utama', 'FUNCTION', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(103, 12, 'Stabilitas', 'STABILITY', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(104, 13, 'Suhu Operasional', 'TEMP', '░C', 20.00, 30.00, 25.00, 1, '2025-12-25 05:21:26'),
(105, 13, 'Kondisi Fisik', 'PHYSICAL', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(106, 13, 'Kalibrasi', 'CALIBRATION', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(107, 13, 'Akurasi', 'ACCURACY', '%', 95.00, 100.00, 98.00, 1, '2025-12-25 05:21:26'),
(108, 13, 'Presisi', 'PRECISION', '%CV', 0.00, 5.00, 2.00, 1, '2025-12-25 05:21:26'),
(109, 13, 'Kebersihan', 'CLEANLINESS', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(110, 13, 'Fungsi Utama', 'FUNCTION', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(111, 13, 'Stabilitas', 'STABILITY', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(112, 14, 'Suhu Operasional', 'TEMP', '░C', 20.00, 30.00, 25.00, 1, '2025-12-25 05:21:26'),
(113, 14, 'Kondisi Fisik', 'PHYSICAL', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(114, 14, 'Kalibrasi', 'CALIBRATION', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(115, 14, 'Akurasi', 'ACCURACY', '%', 95.00, 100.00, 98.00, 1, '2025-12-25 05:21:26'),
(116, 14, 'Presisi', 'PRECISION', '%CV', 0.00, 5.00, 2.00, 1, '2025-12-25 05:21:26'),
(117, 14, 'Kebersihan', 'CLEANLINESS', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(118, 14, 'Fungsi Utama', 'FUNCTION', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26'),
(119, 14, 'Stabilitas', 'STABILITY', 'Pass/Fail', 0.00, 1.00, 1.00, 1, '2025-12-25 05:21:26');

-- --------------------------------------------------------

--
-- Table structure for table `quality_control`
--

CREATE TABLE `quality_control` (
  `qc_id` int(11) NOT NULL,
  `alat_id` int(11) NOT NULL COMMENT 'FK ke alat_laboratorium',
  `tanggal_qc` date NOT NULL COMMENT 'Tanggal QC dilakukan',
  `waktu_qc` time DEFAULT NULL COMMENT 'Waktu QC dilakukan',
  `parameter_qc` varchar(200) DEFAULT NULL COMMENT 'Parameter yang diuji',
  `nilai_hasil` text DEFAULT NULL COMMENT 'Nilai hasil pengujian',
  `nilai_standar` varchar(100) DEFAULT NULL COMMENT 'Nilai standar/rujukan',
  `hasil_qc` enum('Passed','Failed','Conditional','Under Review') DEFAULT 'Passed',
  `teknisi` varchar(100) DEFAULT NULL COMMENT 'Nama teknisi yang melakukan QC',
  `supervisor` varchar(100) DEFAULT NULL COMMENT 'Nama supervisor yang mereview',
  `catatan` text DEFAULT NULL COMMENT 'Catatan hasil QC',
  `tindakan_korektif` text DEFAULT NULL COMMENT 'Tindakan jika QC failed',
  `status` enum('Active','Archived','Cancelled') DEFAULT 'Active',
  `qc_type` enum('Internal','External','Proficiency') DEFAULT 'Internal' COMMENT 'Jenis QC',
  `batch_number` varchar(50) DEFAULT NULL COMMENT 'Nomor batch reagen yang digunakan',
  `user_id` int(11) DEFAULT NULL COMMENT 'User yang input data',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quality_control`
--

INSERT INTO `quality_control` (`qc_id`, `alat_id`, `tanggal_qc`, `waktu_qc`, `parameter_qc`, `nilai_hasil`, `nilai_standar`, `hasil_qc`, `teknisi`, `supervisor`, `catatan`, `tindakan_korektif`, `status`, `qc_type`, `batch_number`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 8, '2025-12-21', '14:19:00', '[{\"name\":\"Akurasi Pengukuran\",\"unit\":\"%\"},{\"name\":\"Kelembaban\",\"unit\":\"%\"},{\"name\":\"Linearitas\",\"unit\":\"R\\u00b2\"},{\"name\":\"Presisi\",\"unit\":\"CV%\"},{\"name\":\"Sensitivitas\",\"unit\":\"%\"},{\"name\":\"Spesifisit', '[95,40,1,0.5,90,90,18]', '[{\"min\":95,\"max\":100},{\"min\":40,\"max\":60},{\"min\":0.95,\"max\":1},{\"min\":0,\"max\":5},{\"min\":90,\"max\":100', 'Passed', 'Suryanto', 'supervisor', 'Alat berhasil di lakuka qc', '', 'Active', '', '', 4, '2025-12-21 07:21:54', '2025-12-21 08:46:30'),
(2, 9, '2025-12-21', '16:33:00', '[{\"name\":\"Akurasi Pengukuran\",\"unit\":\"%\"},{\"name\":\"Kelembaban\",\"unit\":\"%\"},{\"name\":\"Linearitas\",\"unit\":\"R\\u00b2\"},{\"name\":\"Presisi\",\"unit\":\"CV%\"},{\"name\":\"Sensitivitas\",\"unit\":\"%\"},{\"name\":\"Spesifisit', '[98,40,1,4,90,90,18]', '[{\"min\":95,\"max\":100},{\"min\":40,\"max\":60},{\"min\":0.95,\"max\":1},{\"min\":0,\"max\":5},{\"min\":90,\"max\":100', 'Passed', 'Suryanto', '', 'Berhasil', 'Reapir', 'Active', '', '', 4, '2025-12-21 09:34:55', '2025-12-21 09:34:55'),
(3, 9, '2025-12-21', '16:35:00', '[{\"name\":\"Akurasi Pengukuran\",\"unit\":\"%\"},{\"name\":\"Kelembaban\",\"unit\":\"%\"},{\"name\":\"Linearitas\",\"unit\":\"R\\u00b2\"},{\"name\":\"Presisi\",\"unit\":\"CV%\"},{\"name\":\"Sensitivitas\",\"unit\":\"%\"},{\"name\":\"Spesifisit', '[97,45,1,5,90,90,18]', '[{\"min\":95,\"max\":100},{\"min\":40,\"max\":60},{\"min\":0.95,\"max\":1},{\"min\":0,\"max\":5},{\"min\":90,\"max\":100', 'Passed', 'Suryanto', 'supervisor', '', '', 'Active', '', '', 4, '2025-12-21 09:36:25', '2025-12-21 11:33:38'),
(4, 13, '2025-12-21', '18:25:00', '[{\"name\":\"Akurasi Pengukuran\",\"unit\":\"%\"},{\"name\":\"Kelembaban\",\"unit\":\"%\"},{\"name\":\"Linearitas\",\"unit\":\"R\\u00b2\"},{\"name\":\"Presisi\",\"unit\":\"CV%\"},{\"name\":\"Sensitivitas\",\"unit\":\"%\"},{\"name\":\"Spesifisit', '[96,45,1,1,90,90,18]', '[{\"min\":95,\"max\":100},{\"min\":40,\"max\":60},{\"min\":0.95,\"max\":1},{\"min\":0,\"max\":5},{\"min\":90,\"max\":100', 'Passed', 'Suryanto', 'supervisor', 'Ya berhasil ', '', 'Active', '', '', 4, '2025-12-21 11:26:24', '2025-12-21 11:31:21');

--
-- Triggers `quality_control`
--
DELIMITER $$
CREATE TRIGGER `tr_qc_update_alat_status` AFTER INSERT ON `quality_control` FOR EACH ROW BEGIN
    IF NEW.hasil_qc = 'Failed' THEN
        UPDATE alat_laboratorium 
        SET status_alat = 'Perlu Kalibrasi',
            updated_at = NOW()
        WHERE alat_id = NEW.alat_id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `reagen`
--

CREATE TABLE `reagen` (
  `reagen_id` int(11) NOT NULL,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reagen`
--

INSERT INTO `reagen` (`reagen_id`, `nama_reagen`, `kode_unik`, `jumlah_stok`, `satuan`, `lokasi_penyimpanan`, `tanggal_dipakai`, `expired_date`, `stok_minimal`, `status`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'Reagen Hematologi Complete', 'REA001', 50, 'botol', 'Kulkas Lab Hematologi', '2024-11-01', '2025-11-01', 10, 'Kadaluarsa', 'Untuk pemeriksaan darah lengkap', '2024-10-15 01:00:00', '2024-12-19 03:00:00'),
(2, 'Reagen Kimia Darah (Glukosa)', 'REA002', 75, 'vial', 'Kulkas Lab Kimia', '2024-10-15', '2025-10-15', 15, 'Kadaluarsa', 'Untuk tes gula darah', '2024-09-20 02:00:00', '2024-12-19 03:00:00'),
(3, 'Reagen Cholesterol Total', 'REA003', 60, 'vial', 'Kulkas Lab Kimia', '2024-11-10', '2025-11-10', 10, 'Kadaluarsa', 'Untuk pemeriksaan kolesterol', '2024-10-05 03:00:00', '2024-12-19 03:00:00'),
(5, 'Reagen Ureum/BUN', 'REA005', 40, 'vial', 'Kulkas Lab Kimia', '2024-11-05', '2025-11-05', 10, 'Kadaluarsa', 'Untuk pemeriksaan fungsi ginjal', '2024-10-10 05:00:00', '2024-12-19 03:00:00'),
(6, 'Reagen Creatinin', 'REA006', 55, 'vial', 'Kulkas Lab Kimia', '2024-10-25', '2025-10-25', 10, 'Kadaluarsa', 'Untuk pemeriksaan fungsi ginjal', '2024-09-25 06:00:00', '2024-12-19 03:00:00'),
(7, 'Reagen SGOT/AST', 'REA007', 35, 'vial', 'Kulkas Lab Kimia', '2024-11-15', '2025-11-15', 10, 'Kadaluarsa', 'Untuk pemeriksaan fungsi hati', '2024-10-20 07:00:00', '2024-12-19 03:00:00'),
(8, 'Reagen SGPT/ALT', 'REA008', 38, 'vial', 'Kulkas Lab Kimia', '2024-11-20', '2025-11-20', 10, 'Kadaluarsa', 'Untuk pemeriksaan fungsi hati', '2024-10-25 08:00:00', '2024-12-19 03:00:00'),
(9, 'Reagen Urine Stick 10 Parameter', 'REA009', 100, 'strip', 'Rak Lab Urinologi', '2024-10-01', '2025-10-01', 20, 'Kadaluarsa', 'Untuk urinalisis', '2024-09-10 01:30:00', '2024-12-19 03:00:00');

--
-- Triggers `reagen`
--
DELIMITER $$
CREATE TRIGGER `tr_reagen_auto_status_insert` BEFORE INSERT ON `reagen` FOR EACH ROW BEGIN
    -- Auto update status based on stock and expiry
    IF NEW.expired_date IS NOT NULL AND NEW.expired_date < CURDATE() THEN
        SET NEW.status = 'Kadaluarsa';
    ELSEIF NEW.expired_date IS NOT NULL AND NEW.expired_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN
        SET NEW.status = 'Hampir Habis';
    ELSEIF NEW.jumlah_stok <= NEW.stok_minimal THEN
        SET NEW.status = 'Hampir Habis';
    ELSEIF NEW.status IS NULL OR NEW.status = '' THEN
        SET NEW.status = 'Tersedia';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_reagen_auto_status_update` BEFORE UPDATE ON `reagen` FOR EACH ROW BEGIN
    -- Auto update status based on stock and expiry
    IF NEW.expired_date IS NOT NULL AND NEW.expired_date < CURDATE() THEN
        SET NEW.status = 'Kadaluarsa';
    ELSEIF NEW.expired_date IS NOT NULL AND NEW.expired_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN
        SET NEW.status = 'Hampir Habis';
    ELSEIF NEW.jumlah_stok <= NEW.stok_minimal THEN
        SET NEW.status = 'Hampir Habis';
    ELSEIF NEW.jumlah_stok > NEW.stok_minimal AND (NEW.expired_date IS NULL OR NEW.expired_date > DATE_ADD(CURDATE(), INTERVAL 30 DAY)) THEN
        SET NEW.status = 'Tersedia';
    END IF;
    
    -- Set updated_at
    SET NEW.updated_at = NOW();
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_reagen_stock_movement` AFTER UPDATE ON `reagen` FOR EACH ROW BEGIN
    -- Log stock movement if stock changed
    IF NEW.jumlah_stok != OLD.jumlah_stok THEN
        INSERT INTO stock_movements (
            reagen_id, 
            movement_type, 
            quantity_changed, 
            stock_before, 
            stock_after, 
            notes, 
            movement_date
        ) VALUES (
            NEW.reagen_id,
            CASE 
                WHEN NEW.jumlah_stok > OLD.jumlah_stok THEN 'add'
                WHEN NEW.jumlah_stok < OLD.jumlah_stok THEN 'subtract'
                ELSE 'adjust'
            END,
            ABS(NEW.jumlah_stok - OLD.jumlah_stok),
            OLD.jumlah_stok,
            NEW.jumlah_stok,
            'Auto-logged stock change',
            NOW()
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `sampel_expiry`
--

CREATE TABLE `sampel_expiry` (
  `expiry_id` int(11) NOT NULL,
  `jenis_sampel` varchar(50) NOT NULL,
  `masa_berlaku_hari` int(11) NOT NULL COMMENT 'Masa berlaku dalam hari',
  `suhu_optimal_min` decimal(5,2) DEFAULT NULL,
  `suhu_optimal_max` decimal(5,2) DEFAULT NULL,
  `catatan_penyimpanan` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Master data masa berlaku setiap jenis sampel';

--
-- Dumping data for table `sampel_expiry`
--

INSERT INTO `sampel_expiry` (`expiry_id`, `jenis_sampel`, `masa_berlaku_hari`, `suhu_optimal_min`, `suhu_optimal_max`, `catatan_penyimpanan`, `is_active`, `created_at`) VALUES
(1, 'whole_blood', 7, 2.00, 8.00, 'Simpan di kulkas 2-8°C, jangan dibekukan', 1, '2025-12-14 07:43:04'),
(2, 'serum', 7, 2.00, 8.00, 'Dapat disimpan hingga 7 hari di kulkas, atau 6 bulan di freezer -20°C', 1, '2025-12-14 07:43:04'),
(3, 'plasma', 7, 2.00, 8.00, 'Simpan di kulkas 2-8°C untuk penggunaan segera, freezer untuk jangka panjang', 1, '2025-12-14 07:43:04'),
(4, 'urin', 2, 2.00, 8.00, 'Harus diperiksa dalam 2 jam atau simpan di kulkas maksimal 24 jam', 1, '2025-12-14 07:43:04'),
(5, 'feses', 1, 2.00, 8.00, 'Segera periksa atau simpan di kulkas maksimal 24 jam', 1, '2025-12-14 07:43:04'),
(6, 'sputum', 1, 2.00, 8.00, 'Segera diperiksa, dapat disimpan di kulkas maksimal 24 jam', 1, '2025-12-14 07:43:04');

-- --------------------------------------------------------

--
-- Table structure for table `sampel_kondisi`
--

CREATE TABLE `sampel_kondisi` (
  `id` int(11) NOT NULL,
  `sampel_id` int(11) NOT NULL,
  `kondisi_id` int(11) DEFAULT NULL COMMENT 'NULL jika kondisi custom',
  `kondisi_custom` varchar(200) DEFAULT NULL COMMENT 'Isi manual jika pilih "Lainnya"',
  `tingkat_keparahan` enum('ringan','sedang','berat') DEFAULT NULL,
  `keterangan` text DEFAULT NULL COMMENT 'Catatan tambahan',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sampel_storage`
--

CREATE TABLE `sampel_storage` (
  `storage_id` int(11) NOT NULL,
  `sampel_id` int(11) NOT NULL,
  `lokasi_penyimpanan` varchar(100) NOT NULL COMMENT 'Freezer/kulkas/rak nomor berapa',
  `suhu_penyimpanan` decimal(5,2) DEFAULT NULL COMMENT 'Suhu dalam Celsius',
  `tanggal_masuk` datetime NOT NULL DEFAULT current_timestamp(),
  `tanggal_keluar` datetime DEFAULT NULL,
  `status_penyimpanan` enum('tersimpan','diproses','dibuang','dikembalikan') DEFAULT 'tersimpan',
  `volume_sampel` decimal(10,2) DEFAULT NULL COMMENT 'Volume dalam ml/gram',
  `satuan_volume` varchar(20) DEFAULT 'ml',
  `keterangan` text DEFAULT NULL,
  `petugas_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tracking lokasi dan kondisi penyimpanan sampel';

--
-- Dumping data for table `sampel_storage`
--

INSERT INTO `sampel_storage` (`storage_id`, `sampel_id`, `lokasi_penyimpanan`, `suhu_penyimpanan`, `tanggal_masuk`, `tanggal_keluar`, `status_penyimpanan`, `volume_sampel`, `satuan_volume`, `keterangan`, `petugas_id`, `created_at`) VALUES
(1, 4, 'Kulkas Lab Kimia - Rak C', 2.00, '2025-12-21 11:35:28', NULL, 'tersimpan', NULL, 'ml', '', 1, '2025-12-21 04:35:28'),
(2, 10, 'Kulkas Lab Hematologi - Rak A', 2.00, '2025-12-21 11:57:12', NULL, 'tersimpan', NULL, 'ml', '', 1, '2025-12-21 04:57:12'),
(3, 11, 'Kulkas Lab Hematologi - Rak A', 2.00, '2025-12-21 18:24:41', NULL, 'tersimpan', NULL, 'ml', '', 1, '2025-12-21 11:24:41'),
(4, 17, 'Kulkas Lab Hematologi - Rak A', 2.00, '2025-12-25 12:05:24', NULL, 'tersimpan', NULL, 'ml', 'Auto-stored after sample acceptance. Valid until: 2026-01-01 12:05:24', 1, '2025-12-25 05:05:24'),
(5, 21, 'Kulkas Lab Hematologi - Rak A', 2.00, '2025-12-25 18:00:35', NULL, 'tersimpan', NULL, 'ml', 'Auto-stored after sample acceptance. Valid until: 2026-01-01 18:00:35', 1, '2025-12-25 11:00:35'),
(6, 22, 'Kulkas Lab Kimia - Rak B', 2.00, '2025-12-25 18:00:41', NULL, 'tersimpan', NULL, 'ml', 'Auto-stored after sample acceptance. Valid until: 2026-01-01 18:00:41', 1, '2025-12-25 11:00:41'),
(7, 23, 'Kulkas Lab Kimia - Rak C', 2.00, '2025-12-25 18:00:47', NULL, 'tersimpan', NULL, 'ml', 'Auto-stored after sample acceptance. Valid until: 2026-01-01 18:00:47', 1, '2025-12-25 11:00:47'),
(8, 24, 'Storage Umum', 4.00, '2025-12-25 20:22:53', NULL, 'tersimpan', NULL, 'ml', 'Auto-stored after sample acceptance. Valid until: 2026-01-01 20:22:53', 1, '2025-12-25 13:22:53');

--
-- Triggers `sampel_storage`
--
DELIMITER $$
CREATE TRIGGER `tr_sampel_storage_check` AFTER INSERT ON `sampel_storage` FOR EACH ROW BEGIN
    -- Check suhu dan update status jika tidak sesuai
    DECLARE v_suhu_min DECIMAL(5,2);
    DECLARE v_suhu_max DECIMAL(5,2);
    DECLARE v_jenis_sampel VARCHAR(50);
    
    -- Get jenis sampel
    SELECT ps.jenis_sampel INTO v_jenis_sampel
    FROM pemeriksaan_sampel ps
    WHERE ps.sampel_id = NEW.sampel_id;
    
    -- Get suhu optimal
    SELECT suhu_optimal_min, suhu_optimal_max 
    INTO v_suhu_min, v_suhu_max
    FROM sampel_expiry
    WHERE jenis_sampel = v_jenis_sampel;
    
    -- Create notification if suhu tidak sesuai
    IF NEW.suhu_penyimpanan IS NOT NULL AND (
        NEW.suhu_penyimpanan < v_suhu_min OR 
        NEW.suhu_penyimpanan > v_suhu_max
    ) THEN
        INSERT INTO inventory_notifications (
            type,
            title,
            message,
            item_id,
            item_type,
            priority
        ) VALUES (
            'maintenance',
            'Suhu Penyimpanan Sampel Tidak Optimal',
            CONCAT('Sampel ', v_jenis_sampel, ' di ', NEW.lokasi_penyimpanan, 
                   ' memiliki suhu ', NEW.suhu_penyimpanan, 
                   '°C (optimal: ', v_suhu_min, '-', v_suhu_max, '°C)'),
            NEW.sampel_id,
            'sampel',
            'high'
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `serologi_imunologi`
--

CREATE TABLE `serologi_imunologi` (
  `serologi_id` int(11) NOT NULL,
  `pemeriksaan_id` int(11) NOT NULL,
  `rdt_antigen` enum('Positif','Negatif') DEFAULT NULL,
  `widal` text DEFAULT NULL,
  `hbsag` enum('Reaktif','Non-Reaktif') DEFAULT NULL,
  `ns1` enum('Positif','Negatif') DEFAULT NULL,
  `hiv` enum('Reaktif','Non-Reaktif') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `harga_rdt_antigen` decimal(10,2) DEFAULT 75000.00 COMMENT 'Harga RDT Antigen',
  `harga_widal` decimal(10,2) DEFAULT 33000.00 COMMENT 'Harga Widal',
  `harga_hbsag` decimal(10,2) DEFAULT 35000.00 COMMENT 'Harga HbsAg',
  `harga_ns1` decimal(10,2) DEFAULT 100000.00 COMMENT 'Harga NS1',
  `harga_hiv` decimal(10,2) DEFAULT 125000.00 COMMENT 'Harga HIV',
  `total_harga_serologi` decimal(10,2) DEFAULT 0.00 COMMENT 'Total Harga Serologi'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `serologi_imunologi`
--

INSERT INTO `serologi_imunologi` (`serologi_id`, `pemeriksaan_id`, `rdt_antigen`, `widal`, `hbsag`, `ns1`, `hiv`, `created_at`, `harga_rdt_antigen`, `harga_widal`, `harga_hbsag`, `harga_ns1`, `harga_hiv`, `total_harga_serologi`) VALUES
(3, 31, 'Positif', 'Positif', 'Reaktif', 'Positif', 'Reaktif', '2025-12-21 11:24:17', 75000.00, 33000.00, 35000.00, 100000.00, 125000.00, 368000.00),
(4, 35, 'Negatif', 'positif', 'Reaktif', 'Positif', NULL, '2025-12-25 05:33:37', 75000.00, 33000.00, 35000.00, 100000.00, 125000.00, 243000.00);

--
-- Triggers `serologi_imunologi`
--
DELIMITER $$
CREATE TRIGGER `tr_serologi_calculate_total` BEFORE INSERT ON `serologi_imunologi` FOR EACH ROW BEGIN
    SET NEW.total_harga_serologi = 
        (CASE WHEN NEW.rdt_antigen IS NOT NULL THEN NEW.harga_rdt_antigen ELSE 0 END) +
        (CASE WHEN NEW.widal IS NOT NULL THEN NEW.harga_widal ELSE 0 END) +
        (CASE WHEN NEW.hbsag IS NOT NULL THEN NEW.harga_hbsag ELSE 0 END) +
        (CASE WHEN NEW.ns1 IS NOT NULL THEN NEW.harga_ns1 ELSE 0 END) +
        (CASE WHEN NEW.hiv IS NOT NULL THEN NEW.harga_hiv ELSE 0 END);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_serologi_calculate_total_update` BEFORE UPDATE ON `serologi_imunologi` FOR EACH ROW BEGIN
    SET NEW.total_harga_serologi = 
        (CASE WHEN NEW.rdt_antigen IS NOT NULL THEN NEW.harga_rdt_antigen ELSE 0 END) +
        (CASE WHEN NEW.widal IS NOT NULL THEN NEW.harga_widal ELSE 0 END) +
        (CASE WHEN NEW.hbsag IS NOT NULL THEN NEW.harga_hbsag ELSE 0 END) +
        (CASE WHEN NEW.ns1 IS NOT NULL THEN NEW.harga_ns1 ELSE 0 END) +
        (CASE WHEN NEW.hiv IS NOT NULL THEN NEW.harga_hiv ELSE 0 END);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_serologi_update_invoice` AFTER INSERT ON `serologi_imunologi` FOR EACH ROW BEGIN
    CALL sp_update_invoice_from_examination(NEW.pemeriksaan_id);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_serologi_update_invoice_on_update` AFTER UPDATE ON `serologi_imunologi` FOR EACH ROW BEGIN
    CALL sp_update_invoice_from_examination(NEW.pemeriksaan_id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `movement_id` int(11) NOT NULL,
  `reagen_id` int(11) NOT NULL,
  `movement_type` enum('add','subtract','adjust','use','expired','damaged') NOT NULL,
  `quantity_changed` int(11) NOT NULL,
  `stock_before` int(11) NOT NULL,
  `stock_after` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `movement_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `storage_temperature_log`
--

CREATE TABLE `storage_temperature_log` (
  `log_id` int(11) NOT NULL,
  `lokasi_storage` varchar(100) NOT NULL,
  `suhu_tercatat` decimal(5,2) NOT NULL,
  `kelembaban` decimal(5,2) DEFAULT NULL COMMENT 'Kelembaban dalam %',
  `tanggal_pencatatan` datetime NOT NULL DEFAULT current_timestamp(),
  `status_suhu` enum('normal','warning','critical') DEFAULT 'normal',
  `petugas_id` int(11) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Log monitoring suhu ruang penyimpanan sampel';

-- --------------------------------------------------------

--
-- Table structure for table `supervisor`
--

CREATE TABLE `supervisor` (
  `supervisor_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nama_supervisor` varchar(100) NOT NULL,
  `jenis_keahlian` varchar(100) DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbc`
--

CREATE TABLE `tbc` (
  `tbc_id` int(11) NOT NULL,
  `pemeriksaan_id` int(11) NOT NULL,
  `dahak` enum('Negatif','Scanty','+1','+2','+3') DEFAULT NULL,
  `tcm` enum('Detected','Not Detected') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `harga_dahak` decimal(10,2) DEFAULT 30000.00 COMMENT 'Harga Tes Dahak',
  `harga_tcm` decimal(10,2) DEFAULT 450000.00 COMMENT 'Harga TCM',
  `total_harga_tbc` decimal(10,2) DEFAULT 0.00 COMMENT 'Total Harga TBC'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbc`
--

INSERT INTO `tbc` (`tbc_id`, `pemeriksaan_id`, `dahak`, `tcm`, `created_at`, `harga_dahak`, `harga_tcm`, `total_harga_tbc`) VALUES
(2, 28, '+1', 'Not Detected', '2025-12-14 04:35:41', 30000.00, 450000.00, 480000.00),
(3, 35, '+2', 'Detected', '2025-12-25 05:33:37', 30000.00, 450000.00, 480000.00);

--
-- Triggers `tbc`
--
DELIMITER $$
CREATE TRIGGER `tr_tbc_calculate_total` BEFORE INSERT ON `tbc` FOR EACH ROW BEGIN
    SET NEW.total_harga_tbc = 
        (CASE WHEN NEW.dahak IS NOT NULL THEN NEW.harga_dahak ELSE 0 END) +
        (CASE WHEN NEW.tcm IS NOT NULL THEN NEW.harga_tcm ELSE 0 END);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_tbc_calculate_total_update` BEFORE UPDATE ON `tbc` FOR EACH ROW BEGIN
    SET NEW.total_harga_tbc = 
        (CASE WHEN NEW.dahak IS NOT NULL THEN NEW.harga_dahak ELSE 0 END) +
        (CASE WHEN NEW.tcm IS NOT NULL THEN NEW.harga_tcm ELSE 0 END);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_tbc_update_invoice` AFTER INSERT ON `tbc` FOR EACH ROW BEGIN
    CALL sp_update_invoice_from_examination(NEW.pemeriksaan_id);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_tbc_update_invoice_on_update` AFTER UPDATE ON `tbc` FOR EACH ROW BEGIN
    CALL sp_update_invoice_from_examination(NEW.pemeriksaan_id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `timeline_progres`
--

CREATE TABLE `timeline_progres` (
  `timeline_id` int(11) NOT NULL,
  `pemeriksaan_id` int(11) NOT NULL,
  `status` varchar(100) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `petugas_id` int(11) DEFAULT NULL,
  `tanggal_update` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `timeline_progres`
--

INSERT INTO `timeline_progres` (`timeline_id`, `pemeriksaan_id`, `status`, `keterangan`, `petugas_id`, `tanggal_update`) VALUES
(41, 24, 'Hasil Diinput', 'Hasil pemeriksaan telah diinput (0 jenis pemeriksaan)', 1, '2025-12-09 00:41:45'),
(42, 24, 'Hasil Diinput', 'Hasil pemeriksaan telah diinput (0 jenis pemeriksaan)', 1, '2025-12-09 00:43:23'),
(43, 24, 'Hasil Diinput', 'Hasil pemeriksaan telah diinput (0 jenis pemeriksaan)', 1, '2025-12-09 00:46:15'),
(44, 24, 'Hasil Diinput', 'Hasil pemeriksaan telah diinput (0 jenis pemeriksaan)', 1, '2025-12-09 00:46:16'),
(45, 26, 'Hasil Diinput', 'Hasil pemeriksaan telah diinput (0 jenis pemeriksaan)', 1, '2025-12-09 03:06:32'),
(46, 24, 'Hasil Diinput', 'Hasil pemeriksaan telah diinput (2 jenis pemeriksaan)', 1, '2025-12-12 09:04:03'),
(47, 26, 'Sampel Diambil', 'Sampel Whole Blood telah diambil', 1, '2025-12-13 11:22:54'),
(48, 26, 'Sampel Diterima', 'Sampel Whole Blood telah diterima dengan 1 kondisi tercatat', 1, '2025-12-13 11:27:07'),
(49, 28, 'Hasil Diinput', 'Hasil pemeriksaan tbc telah diinput dan siap untuk divalidasi', 1, '2025-12-14 04:35:41'),
(50, 28, 'Sampel Diambil', 'Sampel Serum telah diambil', 1, '2025-12-14 04:35:50'),
(51, 28, 'Sampel Diterima', 'Sampel Serum telah diterima dengan 1 kondisi tercatat', 1, '2025-12-14 04:36:01'),
(52, 26, 'Hasil Diinput', 'Hasil pemeriksaan telah diinput (2 jenis)', 1, '2025-12-19 13:30:50'),
(53, 27, 'Hasil Diinput', 'Hasil pemeriksaan telah diinput (3 jenis)', 1, '2025-12-19 13:51:50'),
(54, 27, 'Hasil Diinput', 'Hasil pemeriksaan telah diinput (3 jenis)', 1, '2025-12-19 13:52:25'),
(55, 25, 'Hasil Diinput', 'Hasil pemeriksaan urinologi telah diinput dan siap untuk divalidasi', 1, '2025-12-19 14:13:18'),
(56, 25, 'Sampel Diambil', 'Sampel Whole Blood telah diambil', 1, '2025-12-19 14:13:39'),
(57, 25, 'Sampel Diterima', 'Sampel Whole Blood telah diterima dengan 2 kondisi tercatat', 1, '2025-12-19 14:13:48'),
(58, 29, 'Sampel Diambil', 'Sampel Whole Blood telah diambil', 1, '2025-12-19 14:32:58'),
(59, 29, 'Sampel Diambil', 'Sampel Serum telah diambil', 1, '2025-12-19 14:33:01'),
(60, 29, 'Sampel Diterima', 'Sampel Whole Blood telah diterima dengan 2 kondisi tercatat', 1, '2025-12-19 14:33:08'),
(61, 29, 'Sampel Diterima', 'Sampel Serum telah diterima dengan 4 kondisi tercatat', 1, '2025-12-19 14:33:19'),
(62, 29, 'Hasil Diinput', 'Hasil pemeriksaan kimia_darah telah diinput dan siap untuk divalidasi', 1, '2025-12-19 14:33:56'),
(63, 30, 'Sampel Diambil', 'Sampel Whole Blood telah diambil', 1, '2025-12-20 11:53:57'),
(64, 30, 'Sampel Diterima', 'Sampel Whole Blood telah diterima dengan 2 kondisi tercatat', 1, '2025-12-20 11:54:05'),
(65, 30, 'Sampel Diambil', 'Sampel Serum telah diambil', 1, '2025-12-20 11:55:10'),
(66, 30, 'Sampel Diterima', 'Sampel Serum telah diterima dengan 3 kondisi tercatat', 1, '2025-12-20 11:55:17'),
(67, 31, 'Sampel Diambil', 'Sampel Whole Blood telah diambil', 1, '2025-12-21 04:57:00'),
(68, 31, 'Sampel Diterima', 'Sampel Whole Blood telah diterima dengan 2 kondisi tercatat', 1, '2025-12-21 04:57:12'),
(69, 31, 'Status Diperbarui', 'sedang di proses', 1, '2025-12-21 07:43:53'),
(70, 31, 'Status Diperbarui', 'sedang progress', 1, '2025-12-21 07:50:51'),
(71, 31, 'Status Diperbarui', 'ya proses', 1, '2025-12-21 07:54:33'),
(72, 31, 'Status Diperbarui', 'Sedang di proses', 1, '2025-12-21 08:29:14'),
(73, 31, 'Status Diperbarui', 'sedang di proses', 1, '2025-12-21 08:38:34'),
(74, 31, 'Status Diperbarui', 'Melakukan analisis sample', 1, '2025-12-21 09:16:49'),
(75, 31, 'Status Diperbarui', 'di proses', 1, '2025-12-21 09:24:40'),
(76, 31, 'Hasil Diinput', 'Hasil pemeriksaan telah diinput (2 jenis)', 1, '2025-12-21 11:24:17'),
(77, 32, 'Sampel Diambil', 'Sampel Whole Blood telah diambil', 1, '2025-12-21 11:24:32'),
(78, 32, 'Sampel Diterima', 'Sampel Whole Blood telah diterima dengan 5 kondisi tercatat', 1, '2025-12-21 11:24:41'),
(79, 33, 'Hasil Diinput', 'Hasil pemeriksaan hematologi telah diinput dan siap untuk divalidasi', 1, '2025-12-21 11:34:37'),
(80, 32, 'Status Diperbarui', 'sedang berjalan', 1, '2025-12-21 11:35:50'),
(81, 32, 'Status Diperbarui', 'sedang di proses', 1, '2025-12-21 11:41:40'),
(82, 32, 'Status Diperbarui', 'di proses', 1, '2025-12-21 11:54:18'),
(83, 32, 'Hasil Diinput', 'Hasil pemeriksaan hematologi telah diinput dan siap untuk divalidasi', 1, '2025-12-21 11:55:08'),
(84, 32, 'Hasil Diinput', 'Hasil pemeriksaan hematologi telah diinput dan siap untuk divalidasi', 1, '2025-12-21 11:59:58'),
(85, 32, 'Hasil Diinput', 'Hasil pemeriksaan hematologi telah diinput dan siap untuk divalidasi', 1, '2025-12-21 12:05:16'),
(86, 36, 'Hasil Diinput', 'Hasil pemeriksaan telah diinput (3 jenis)', 1, '2025-12-25 04:21:16'),
(87, 36, 'Sampel Diambil', 'Sampel Whole Blood telah diambil', 1, '2025-12-25 04:40:05'),
(88, 36, 'Sampel Diterima', 'Sampel Whole Blood telah diterima dengan 4 kondisi tercatat', 1, '2025-12-25 05:05:24'),
(89, 35, 'Hasil Diinput', 'Hasil pemeriksaan telah diinput (3 jenis)', 1, '2025-12-25 05:33:37'),
(90, 39, 'Sampel Diambil', 'Sampel Whole Blood telah diambil', 1, '2025-12-25 11:00:23'),
(91, 39, 'Sampel Diambil', 'Sampel Serum telah diambil', 1, '2025-12-25 11:00:26'),
(92, 39, 'Sampel Diambil', 'Sampel Plasma telah diambil', 1, '2025-12-25 11:00:28'),
(93, 39, 'Sampel Diterima', 'Sampel Whole Blood telah diterima dengan 4 kondisi tercatat', 1, '2025-12-25 11:00:35'),
(94, 39, 'Sampel Diterima', 'Sampel Serum telah diterima dengan 4 kondisi tercatat', 1, '2025-12-25 11:00:41'),
(95, 39, 'Sampel Diterima', 'Sampel Plasma telah diterima dengan 5 kondisi tercatat', 1, '2025-12-25 11:00:47'),
(96, 39, 'Sampel Ditambahkan', 'Sampel Lain - Lain telah ditambahkan', 1, '2025-12-25 13:22:46'),
(97, 39, 'Sampel Diambil', 'Sampel Lain - Lain telah diambil', 1, '2025-12-25 13:22:49'),
(98, 39, 'Sampel Diterima', 'Sampel Lain - Lain telah diterima', 1, '2025-12-25 13:22:53');

-- --------------------------------------------------------

--
-- Table structure for table `urinologi`
--

CREATE TABLE `urinologi` (
  `urinologi_id` int(11) NOT NULL,
  `pemeriksaan_id` int(11) NOT NULL,
  `makroskopis` text DEFAULT NULL,
  `mikroskopis` text DEFAULT NULL,
  `kimia_ph` decimal(3,1) DEFAULT NULL,
  `protein_regular` varchar(50) DEFAULT NULL,
  `berat_jenis` decimal(3,1) DEFAULT NULL COMMENT 'Berat jenis dalam satuan desimal',
  `glukosa` enum('Negatif','+1','+2','+3','+4') DEFAULT NULL COMMENT 'Hasil pemeriksaan glukosa',
  `keton` enum('Negatif','+1','+2','+3','+4') DEFAULT NULL COMMENT 'Hasil pemeriksaan keton',
  `bilirubin` enum('Negatif','+1','+2','+3','+4') DEFAULT NULL COMMENT 'Hasil pemeriksaan bilirubin',
  `urobilinogen` enum('Negatif','+1','+2','+3','+4') DEFAULT NULL COMMENT 'Hasil pemeriksaan urobilinogen',
  `protein` text DEFAULT NULL,
  `tes_kehamilan` enum('Positif','Negatif') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `harga_urin_rutin` decimal(10,2) DEFAULT 25000.00 COMMENT 'Harga Urin Rutin',
  `harga_protein` decimal(10,2) DEFAULT 10000.00 COMMENT 'Harga Tes Protein',
  `harga_tes_kehamilan` decimal(10,2) DEFAULT 15000.00 COMMENT 'Harga Tes Kehamilan',
  `total_harga_urinologi` decimal(10,2) DEFAULT 0.00 COMMENT 'Total Harga Urinologi'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `urinologi`
--

INSERT INTO `urinologi` (`urinologi_id`, `pemeriksaan_id`, `makroskopis`, `mikroskopis`, `kimia_ph`, `protein_regular`, `berat_jenis`, `glukosa`, `keton`, `bilirubin`, `urobilinogen`, `protein`, `tes_kehamilan`, `created_at`, `harga_urin_rutin`, `harga_protein`, `harga_tes_kehamilan`, `total_harga_urinologi`) VALUES
(7, 24, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '34', NULL, '2025-12-12 09:04:03', 25000.00, 10000.00, 15000.00, 10000.00),
(8, 26, 'bau', 'bakteri', 5.0, '+1', 99.9, '+3', '+3', '+2', '+1', '23', NULL, '2025-12-19 13:30:50', 25000.00, 10000.00, 15000.00, 0.00),
(9, 27, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '24', 'Positif', '2025-12-19 13:52:25', 25000.00, 10000.00, 15000.00, 0.00),
(10, 25, 'bau', 'eritrosit', 60.0, '+2', 99.9, '+2', '+2', '+1', '+1', '25', 'Positif', '2025-12-19 14:13:18', 25000.00, 10000.00, 15000.00, 0.00),
(11, 36, 'bau', 'kristal', 50.0, '+2', 99.9, '+3', '+1', '+2', '+2', '25', 'Positif', '2025-12-25 04:21:16', 25000.00, 10000.00, 15000.00, 0.00);

--
-- Triggers `urinologi`
--
DELIMITER $$
CREATE TRIGGER `tr_urinologi_update_invoice` AFTER INSERT ON `urinologi` FOR EACH ROW BEGIN
    CALL sp_update_invoice_from_examination(NEW.pemeriksaan_id);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_urinologi_update_invoice_on_update` AFTER UPDATE ON `urinologi` FOR EACH ROW BEGIN
    CALL sp_update_invoice_from_examination(NEW.pemeriksaan_id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `urinologi_backup`
--

CREATE TABLE `urinologi_backup` (
  `urinologi_id` int(11) NOT NULL DEFAULT 0,
  `pemeriksaan_id` int(11) NOT NULL,
  `makroskopis` text DEFAULT NULL,
  `mikroskopis` text DEFAULT NULL,
  `kimia_ph` decimal(3,1) DEFAULT NULL,
  `protein_regular` varchar(50) DEFAULT NULL,
  `berat_jenis` varchar(20) DEFAULT NULL,
  `glukosa` varchar(50) DEFAULT NULL,
  `keton` varchar(50) DEFAULT NULL,
  `bilirubin` varchar(50) DEFAULT NULL,
  `urobilinogen` varchar(50) DEFAULT NULL,
  `protein` text DEFAULT NULL,
  `tes_kehamilan` enum('Positif','Negatif') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `harga_urin_rutin` decimal(10,2) DEFAULT 25000.00 COMMENT 'Harga Urin Rutin',
  `harga_protein` decimal(10,2) DEFAULT 10000.00 COMMENT 'Harga Tes Protein',
  `harga_tes_kehamilan` decimal(10,2) DEFAULT 15000.00 COMMENT 'Harga Tes Kehamilan',
  `total_harga_urinologi` decimal(10,2) DEFAULT 0.00 COMMENT 'Total Harga Urinologi'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `urinologi_backup`
--

INSERT INTO `urinologi_backup` (`urinologi_id`, `pemeriksaan_id`, `makroskopis`, `mikroskopis`, `kimia_ph`, `protein_regular`, `berat_jenis`, `glukosa`, `keton`, `bilirubin`, `urobilinogen`, `protein`, `tes_kehamilan`, `created_at`, `harga_urin_rutin`, `harga_protein`, `harga_tes_kehamilan`, `total_harga_urinologi`) VALUES
(7, 24, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '34', NULL, '2025-12-12 09:04:03', 25000.00, 10000.00, 15000.00, 10000.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','administrasi','dokter','petugas_lab','supervisor') NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'superadmin', '0192023a7bbd73250516f069df18b500', 'admin', 1, '2025-08-28 10:41:13', '2025-08-28 10:41:13'),
(2, 'administrasi', 'feed5d47c860f422712ac902a89865db', 'administrasi', 1, '2025-08-28 10:41:13', '2025-09-30 07:21:42'),
(3, 'dr_budi', 'cab2d8232139ee4f469a920732578f71', 'dokter', 0, '2025-08-28 10:41:13', '2025-08-29 07:05:04'),
(4, 'lab_sari', '081c49b8c66a69aad79f4bca8334e0ef', 'petugas_lab', 1, '2025-08-28 10:41:13', '2025-12-19 14:45:32'),
(7, 'Firdaus', 'de28f8f7998f23ab4194b51a6029416f', 'administrasi', 1, '2025-09-12 13:01:18', '2025-09-30 09:11:57'),
(11, 'supervisor', 'de28f8f7998f23ab4194b51a6029416f', 'supervisor', 1, '2025-12-09 00:24:11', '2025-12-19 14:51:37');

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_examination_stats`
-- (See below for the actual view)
--
CREATE TABLE `v_examination_stats` (
`exam_date` date
,`status_pemeriksaan` enum('pending','progress','selesai','cancelled')
,`count` bigint(21)
,`avg_processing_hours` decimal(24,4)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_inventory_sampel`
-- (See below for the actual view)
--
CREATE TABLE `v_inventory_sampel` (
`storage_id` int(11)
,`sampel_id` int(11)
,`jenis_sampel` varchar(50)
,`pemeriksaan_id` int(11)
,`nomor_pemeriksaan` varchar(50)
,`nama_pasien` varchar(100)
,`nik` varchar(20)
,`lokasi_penyimpanan` varchar(100)
,`suhu_penyimpanan` decimal(5,2)
,`volume_sampel` decimal(10,2)
,`satuan_volume` varchar(20)
,`tanggal_masuk` datetime
,`status_penyimpanan` enum('tersimpan','diproses','dibuang','dikembalikan')
,`masa_berlaku_hari` int(11)
,`tanggal_kadaluarsa` datetime
,`hari_tersisa` int(7)
,`status_masa_berlaku` varchar(17)
,`petugas_penyimpan` varchar(50)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_inventory_status`
-- (See below for the actual view)
--
CREATE TABLE `v_inventory_status` (
`tipe_inventory` varchar(6)
,`item_id` int(11)
,`nama_item` varchar(100)
,`kode_unik` varchar(50)
,`jumlah_stok` int(11)
,`stok_minimal` int(11)
,`status` varchar(16)
,`expired_date` date
,`alert_level` varchar(15)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_invoice_cetak`
-- (See below for the actual view)
--
CREATE TABLE `v_invoice_cetak` (
`nomor_invoice` varchar(50)
,`tanggal_invoice` date
,`nama_pasien` varchar(100)
,`nik` varchar(20)
,`umur` int(11)
,`alamat_domisili` text
,`telepon` varchar(20)
,`nomor_pemeriksaan` varchar(50)
,`jenis_pemeriksaan` varchar(100)
,`tanggal_pemeriksaan` date
,`total_biaya` decimal(10,2)
,`metode_pembayaran` varchar(50)
,`status_pembayaran` enum('belum_bayar','lunas','cicilan')
,`tanggal_pembayaran` date
,`keterangan` text
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_invoice_detail`
-- (See below for the actual view)
--
CREATE TABLE `v_invoice_detail` (
`invoice_id` int(11)
,`nomor_invoice` varchar(50)
,`tanggal_invoice` date
,`jenis_pembayaran` enum('umum','bpjs')
,`total_biaya` decimal(10,2)
,`status_pembayaran` enum('belum_bayar','lunas','cicilan')
,`metode_pembayaran` varchar(50)
,`nomor_kartu_bpjs` varchar(50)
,`nomor_sep` varchar(50)
,`nama_pasien` varchar(100)
,`nik` varchar(20)
,`nomor_pemeriksaan` varchar(50)
,`jenis_pemeriksaan` varchar(100)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_pemeriksaan_detail`
-- (See below for the actual view)
--
CREATE TABLE `v_pemeriksaan_detail` (
`pemeriksaan_id` int(11)
,`nomor_pemeriksaan` varchar(50)
,`tanggal_pemeriksaan` date
,`jenis_pemeriksaan` varchar(100)
,`status_pemeriksaan` enum('pending','progress','selesai','cancelled')
,`biaya` decimal(10,2)
,`nama_pasien` varchar(100)
,`nik` varchar(20)
,`riwayat_pasien` text
,`dokter_perujuk` varchar(100)
,`asal_rujukan` varchar(100)
,`nomor_rujukan` varchar(50)
,`tanggal_rujukan` date
,`diagnosis_awal` text
,`rekomendasi_pemeriksaan` text
,`nama_petugas` varchar(100)
,`nama_lab` varchar(100)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_pemeriksaan_lengkap`
-- (See below for the actual view)
--
CREATE TABLE `v_pemeriksaan_lengkap` (
`pemeriksaan_id` int(11)
,`nomor_pemeriksaan` varchar(50)
,`tanggal_pemeriksaan` date
,`status_pemeriksaan` enum('pending','progress','selesai','cancelled')
,`status_pasien` enum('puasa','belum_puasa','minum_obat')
,`keterangan_obat` text
,`nama_pasien` varchar(100)
,`nik` varchar(20)
,`nomor_registrasi` varchar(50)
,`jenis_pemeriksaan_list` mediumtext
,`sampel_list` mediumtext
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_qc_detail`
-- (See below for the actual view)
--
CREATE TABLE `v_qc_detail` (
`qc_id` int(11)
,`tanggal_qc` date
,`waktu_qc` time
,`parameter_qc` varchar(200)
,`nilai_hasil` text
,`nilai_standar` varchar(100)
,`hasil_qc` enum('Passed','Failed','Conditional','Under Review')
,`teknisi` varchar(100)
,`supervisor` varchar(100)
,`catatan` text
,`tindakan_korektif` text
,`status` enum('Active','Archived','Cancelled')
,`qc_type` enum('Internal','External','Proficiency')
,`batch_number` varchar(50)
,`nama_alat` varchar(100)
,`kode_alat` varchar(50)
,`merek_model` varchar(100)
,`status_alat` enum('Normal','Perlu Kalibrasi','Rusak','Sedang Kalibrasi')
,`created_by` varchar(50)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_qc_statistics`
-- (See below for the actual view)
--
CREATE TABLE `v_qc_statistics` (
`period` varchar(7)
,`total_qc` bigint(21)
,`passed_count` decimal(22,0)
,`failed_count` decimal(22,0)
,`conditional_count` decimal(22,0)
,`pass_rate` decimal(28,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_sampel_dashboard`
-- (See below for the actual view)
--
CREATE TABLE `v_sampel_dashboard` (
`total_sampel` bigint(21)
,`belum_diambil` decimal(22,0)
,`sudah_diambil` decimal(22,0)
,`diterima` decimal(22,0)
,`ditolak` decimal(22,0)
,`sedang_disimpan` decimal(22,0)
,`jumlah_lokasi_storage` bigint(21)
,`sampel_akan_expired` decimal(22,0)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_sampel_status`
-- (See below for the actual view)
--
CREATE TABLE `v_sampel_status` (
`sampel_id` int(11)
,`pemeriksaan_id` int(11)
,`jenis_sampel` varchar(50)
,`status_sampel` enum('belum_diambil','sudah_diambil','diterima','ditolak')
,`tanggal_pengambilan` datetime
,`tanggal_evaluasi` datetime
,`nomor_pemeriksaan` varchar(50)
,`nama_pasien` varchar(100)
,`nik` varchar(20)
,`lokasi_penyimpanan` varchar(100)
,`suhu_penyimpanan` decimal(5,2)
,`volume_sampel` decimal(10,2)
,`satuan_volume` varchar(20)
,`status_penyimpanan` enum('tersimpan','diproses','dibuang','dikembalikan')
,`masa_berlaku_hari` int(11)
,`status_berlaku` varchar(13)
,`tanggal_kadaluarsa` datetime
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_user_details`
-- (See below for the actual view)
--
CREATE TABLE `v_user_details` (
`user_id` int(11)
,`username` varchar(50)
,`role` enum('admin','administrasi','dokter','petugas_lab','supervisor')
,`is_active` tinyint(1)
,`created_at` timestamp
,`nama_lengkap` varchar(100)
);

-- --------------------------------------------------------

--
-- Structure for view `v_examination_stats`
--
DROP TABLE IF EXISTS `v_examination_stats`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_examination_stats`  AS SELECT cast(`pemeriksaan_lab`.`tanggal_pemeriksaan` as date) AS `exam_date`, `pemeriksaan_lab`.`status_pemeriksaan` AS `status_pemeriksaan`, count(0) AS `count`, avg(case when `pemeriksaan_lab`.`started_at` is not null and `pemeriksaan_lab`.`completed_at` is not null then timestampdiff(HOUR,`pemeriksaan_lab`.`started_at`,`pemeriksaan_lab`.`completed_at`) else NULL end) AS `avg_processing_hours` FROM `pemeriksaan_lab` WHERE `pemeriksaan_lab`.`tanggal_pemeriksaan` >= curdate() - interval 30 day GROUP BY cast(`pemeriksaan_lab`.`tanggal_pemeriksaan` as date), `pemeriksaan_lab`.`status_pemeriksaan` ORDER BY cast(`pemeriksaan_lab`.`tanggal_pemeriksaan` as date) DESC, `pemeriksaan_lab`.`status_pemeriksaan` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `v_inventory_sampel`
--
DROP TABLE IF EXISTS `v_inventory_sampel`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_inventory_sampel`  AS SELECT `ss`.`storage_id` AS `storage_id`, `ss`.`sampel_id` AS `sampel_id`, `ps`.`jenis_sampel` AS `jenis_sampel`, `ps`.`pemeriksaan_id` AS `pemeriksaan_id`, `pl`.`nomor_pemeriksaan` AS `nomor_pemeriksaan`, `p`.`nama` AS `nama_pasien`, `p`.`nik` AS `nik`, `ss`.`lokasi_penyimpanan` AS `lokasi_penyimpanan`, `ss`.`suhu_penyimpanan` AS `suhu_penyimpanan`, `ss`.`volume_sampel` AS `volume_sampel`, `ss`.`satuan_volume` AS `satuan_volume`, `ss`.`tanggal_masuk` AS `tanggal_masuk`, `ss`.`status_penyimpanan` AS `status_penyimpanan`, `se`.`masa_berlaku_hari` AS `masa_berlaku_hari`, `ss`.`tanggal_masuk`+ interval `se`.`masa_berlaku_hari` day AS `tanggal_kadaluarsa`, to_days(`ss`.`tanggal_masuk` + interval `se`.`masa_berlaku_hari` day) - to_days(current_timestamp()) AS `hari_tersisa`, CASE WHEN to_days(`ss`.`tanggal_masuk` + interval `se`.`masa_berlaku_hari` day) - to_days(current_timestamp()) < 0 THEN 'Kadaluarsa' WHEN to_days(`ss`.`tanggal_masuk` + interval `se`.`masa_berlaku_hari` day) - to_days(current_timestamp()) <= 1 THEN 'Segera Kadaluarsa' WHEN to_days(`ss`.`tanggal_masuk` + interval `se`.`masa_berlaku_hari` day) - to_days(current_timestamp()) <= 3 THEN 'Perlu Perhatian' ELSE 'Normal' END AS `status_masa_berlaku`, `u`.`username` AS `petugas_penyimpan` FROM (((((`sampel_storage` `ss` left join `pemeriksaan_sampel` `ps` on(`ss`.`sampel_id` = `ps`.`sampel_id`)) left join `pemeriksaan_lab` `pl` on(`ps`.`pemeriksaan_id` = `pl`.`pemeriksaan_id`)) left join `pasien` `p` on(`pl`.`pasien_id` = `p`.`pasien_id`)) left join `sampel_expiry` `se` on(`ps`.`jenis_sampel` = `se`.`jenis_sampel`)) left join `users` `u` on(`ss`.`petugas_id` = `u`.`user_id`)) WHERE `ss`.`status_penyimpanan` = 'tersimpan' ORDER BY to_days(`ss`.`tanggal_masuk` + interval `se`.`masa_berlaku_hari` day) - to_days(current_timestamp()) ASC ;

-- --------------------------------------------------------

--
-- Structure for view `v_inventory_status`
--
DROP TABLE IF EXISTS `v_inventory_status`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_inventory_status`  AS SELECT 'reagen' AS `tipe_inventory`, `reagen`.`reagen_id` AS `item_id`, `reagen`.`nama_reagen` AS `nama_item`, coalesce(`reagen`.`kode_unik`,concat('REA',lpad(`reagen`.`reagen_id`,3,'0'))) AS `kode_unik`, `reagen`.`jumlah_stok` AS `jumlah_stok`, `reagen`.`stok_minimal` AS `stok_minimal`, `reagen`.`status` AS `status`, `reagen`.`expired_date` AS `expired_date`, CASE WHEN `reagen`.`status` = 'Kadaluarsa' THEN 'Urgent' WHEN `reagen`.`status` = 'Hampir Habis' THEN 'Warning' WHEN `reagen`.`jumlah_stok` <= `reagen`.`stok_minimal` THEN 'Low Stock' ELSE 'OK' END AS `alert_level` FROM `reagen`union all select 'alat' AS `tipe_inventory`,`alat_laboratorium`.`alat_id` AS `item_id`,`alat_laboratorium`.`nama_alat` AS `nama_item`,coalesce(`alat_laboratorium`.`kode_unik`,concat('ALT',lpad(`alat_laboratorium`.`alat_id`,3,'0'))) AS `kode_unik`,NULL AS `jumlah_stok`,NULL AS `stok_minimal`,`alat_laboratorium`.`status_alat` AS `status`,`alat_laboratorium`.`jadwal_kalibrasi` AS `expired_date`,case when `alat_laboratorium`.`status_alat` = 'Rusak' then 'Urgent' when `alat_laboratorium`.`status_alat` = 'Perlu Kalibrasi' then 'Warning' when `alat_laboratorium`.`jadwal_kalibrasi` <= curdate() then 'Calibration Due' else 'OK' end AS `alert_level` from `alat_laboratorium`  ;

-- --------------------------------------------------------

--
-- Structure for view `v_invoice_cetak`
--
DROP TABLE IF EXISTS `v_invoice_cetak`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_invoice_cetak`  AS SELECT `i`.`nomor_invoice` AS `nomor_invoice`, `i`.`tanggal_invoice` AS `tanggal_invoice`, `p`.`nama` AS `nama_pasien`, `p`.`nik` AS `nik`, `p`.`umur` AS `umur`, `p`.`alamat_domisili` AS `alamat_domisili`, `p`.`telepon` AS `telepon`, `pe`.`nomor_pemeriksaan` AS `nomor_pemeriksaan`, `pe`.`jenis_pemeriksaan` AS `jenis_pemeriksaan`, `pe`.`tanggal_pemeriksaan` AS `tanggal_pemeriksaan`, `i`.`total_biaya` AS `total_biaya`, `i`.`metode_pembayaran` AS `metode_pembayaran`, `i`.`status_pembayaran` AS `status_pembayaran`, `i`.`tanggal_pembayaran` AS `tanggal_pembayaran`, `i`.`keterangan` AS `keterangan` FROM ((`invoice` `i` join `pemeriksaan_lab` `pe` on(`i`.`pemeriksaan_id` = `pe`.`pemeriksaan_id`)) join `pasien` `p` on(`pe`.`pasien_id` = `p`.`pasien_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `v_invoice_detail`
--
DROP TABLE IF EXISTS `v_invoice_detail`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_invoice_detail`  AS SELECT `i`.`invoice_id` AS `invoice_id`, `i`.`nomor_invoice` AS `nomor_invoice`, `i`.`tanggal_invoice` AS `tanggal_invoice`, `i`.`jenis_pembayaran` AS `jenis_pembayaran`, `i`.`total_biaya` AS `total_biaya`, `i`.`status_pembayaran` AS `status_pembayaran`, `i`.`metode_pembayaran` AS `metode_pembayaran`, `i`.`nomor_kartu_bpjs` AS `nomor_kartu_bpjs`, `i`.`nomor_sep` AS `nomor_sep`, `p`.`nama` AS `nama_pasien`, `p`.`nik` AS `nik`, `pe`.`nomor_pemeriksaan` AS `nomor_pemeriksaan`, `pe`.`jenis_pemeriksaan` AS `jenis_pemeriksaan` FROM ((`invoice` `i` left join `pemeriksaan_lab` `pe` on(`i`.`pemeriksaan_id` = `pe`.`pemeriksaan_id`)) left join `pasien` `p` on(`pe`.`pasien_id` = `p`.`pasien_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `v_pemeriksaan_detail`
--
DROP TABLE IF EXISTS `v_pemeriksaan_detail`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_pemeriksaan_detail`  AS SELECT `p`.`pemeriksaan_id` AS `pemeriksaan_id`, `p`.`nomor_pemeriksaan` AS `nomor_pemeriksaan`, `p`.`tanggal_pemeriksaan` AS `tanggal_pemeriksaan`, `p`.`jenis_pemeriksaan` AS `jenis_pemeriksaan`, `p`.`status_pemeriksaan` AS `status_pemeriksaan`, `p`.`biaya` AS `biaya`, `pas`.`nama` AS `nama_pasien`, `pas`.`nik` AS `nik`, `pas`.`riwayat_pasien` AS `riwayat_pasien`, `pas`.`dokter_perujuk` AS `dokter_perujuk`, `pas`.`asal_rujukan` AS `asal_rujukan`, `pas`.`nomor_rujukan` AS `nomor_rujukan`, `pas`.`tanggal_rujukan` AS `tanggal_rujukan`, `pas`.`diagnosis_awal` AS `diagnosis_awal`, `pas`.`rekomendasi_pemeriksaan` AS `rekomendasi_pemeriksaan`, `pl`.`nama_petugas` AS `nama_petugas`, `l`.`nama` AS `nama_lab` FROM (((`pemeriksaan_lab` `p` left join `pasien` `pas` on(`p`.`pasien_id` = `pas`.`pasien_id`)) left join `petugas_lab` `pl` on(`p`.`petugas_id` = `pl`.`petugas_id`)) left join `lab` `l` on(`p`.`lab_id` = `l`.`lab_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `v_pemeriksaan_lengkap`
--
DROP TABLE IF EXISTS `v_pemeriksaan_lengkap`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_pemeriksaan_lengkap`  AS SELECT `pl`.`pemeriksaan_id` AS `pemeriksaan_id`, `pl`.`nomor_pemeriksaan` AS `nomor_pemeriksaan`, `pl`.`tanggal_pemeriksaan` AS `tanggal_pemeriksaan`, `pl`.`status_pemeriksaan` AS `status_pemeriksaan`, `pl`.`status_pasien` AS `status_pasien`, `pl`.`keterangan_obat` AS `keterangan_obat`, `p`.`nama` AS `nama_pasien`, `p`.`nik` AS `nik`, `p`.`nomor_registrasi` AS `nomor_registrasi`, group_concat(distinct `pd`.`jenis_pemeriksaan` order by `pd`.`urutan` ASC separator ', ') AS `jenis_pemeriksaan_list`, group_concat(distinct `ps`.`jenis_sampel` order by `ps`.`jenis_sampel` ASC separator ', ') AS `sampel_list` FROM (((`pemeriksaan_lab` `pl` left join `pasien` `p` on(`pl`.`pasien_id` = `p`.`pasien_id`)) left join `pemeriksaan_detail` `pd` on(`pl`.`pemeriksaan_id` = `pd`.`pemeriksaan_id`)) left join `pemeriksaan_sampel` `ps` on(`pl`.`pemeriksaan_id` = `ps`.`pemeriksaan_id`)) GROUP BY `pl`.`pemeriksaan_id` ;

-- --------------------------------------------------------

--
-- Structure for view `v_qc_detail`
--
DROP TABLE IF EXISTS `v_qc_detail`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_qc_detail`  AS SELECT `qc`.`qc_id` AS `qc_id`, `qc`.`tanggal_qc` AS `tanggal_qc`, `qc`.`waktu_qc` AS `waktu_qc`, `qc`.`parameter_qc` AS `parameter_qc`, `qc`.`nilai_hasil` AS `nilai_hasil`, `qc`.`nilai_standar` AS `nilai_standar`, `qc`.`hasil_qc` AS `hasil_qc`, `qc`.`teknisi` AS `teknisi`, `qc`.`supervisor` AS `supervisor`, `qc`.`catatan` AS `catatan`, `qc`.`tindakan_korektif` AS `tindakan_korektif`, `qc`.`status` AS `status`, `qc`.`qc_type` AS `qc_type`, `qc`.`batch_number` AS `batch_number`, `al`.`nama_alat` AS `nama_alat`, `al`.`kode_unik` AS `kode_alat`, `al`.`merek_model` AS `merek_model`, `al`.`status_alat` AS `status_alat`, `u`.`username` AS `created_by` FROM ((`quality_control` `qc` left join `alat_laboratorium` `al` on(`qc`.`alat_id` = `al`.`alat_id`)) left join `users` `u` on(`qc`.`user_id` = `u`.`user_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `v_qc_statistics`
--
DROP TABLE IF EXISTS `v_qc_statistics`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_qc_statistics`  AS SELECT date_format(`qc`.`tanggal_qc`,'%Y-%m') AS `period`, count(0) AS `total_qc`, sum(case when `qc`.`hasil_qc` = 'Passed' then 1 else 0 end) AS `passed_count`, sum(case when `qc`.`hasil_qc` = 'Failed' then 1 else 0 end) AS `failed_count`, sum(case when `qc`.`hasil_qc` = 'Conditional' then 1 else 0 end) AS `conditional_count`, round(sum(case when `qc`.`hasil_qc` = 'Passed' then 1 else 0 end) / count(0) * 100,2) AS `pass_rate` FROM `quality_control` AS `qc` WHERE `qc`.`status` = 'Active' GROUP BY date_format(`qc`.`tanggal_qc`,'%Y-%m') ORDER BY date_format(`qc`.`tanggal_qc`,'%Y-%m') DESC ;

-- --------------------------------------------------------

--
-- Structure for view `v_sampel_dashboard`
--
DROP TABLE IF EXISTS `v_sampel_dashboard`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_sampel_dashboard`  AS SELECT count(distinct `ps`.`sampel_id`) AS `total_sampel`, sum(case when `ps`.`status_sampel` = 'belum_diambil' then 1 else 0 end) AS `belum_diambil`, sum(case when `ps`.`status_sampel` = 'sudah_diambil' then 1 else 0 end) AS `sudah_diambil`, sum(case when `ps`.`status_sampel` = 'diterima' then 1 else 0 end) AS `diterima`, sum(case when `ps`.`status_sampel` = 'ditolak' then 1 else 0 end) AS `ditolak`, sum(case when `ss`.`status_penyimpanan` = 'tersimpan' then 1 else 0 end) AS `sedang_disimpan`, count(distinct `ss`.`lokasi_penyimpanan`) AS `jumlah_lokasi_storage`, sum(case when `ps`.`tanggal_pengambilan` is not null and `ps`.`tanggal_pengambilan` + interval `se`.`masa_berlaku_hari` day < current_timestamp() + interval 2 day then 1 else 0 end) AS `sampel_akan_expired` FROM ((`pemeriksaan_sampel` `ps` left join `sampel_storage` `ss` on(`ps`.`sampel_id` = `ss`.`sampel_id` and `ss`.`status_penyimpanan` = 'tersimpan')) left join `sampel_expiry` `se` on(`ps`.`jenis_sampel` = `se`.`jenis_sampel`)) ;

-- --------------------------------------------------------

--
-- Structure for view `v_sampel_status`
--
DROP TABLE IF EXISTS `v_sampel_status`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_sampel_status`  AS SELECT `ps`.`sampel_id` AS `sampel_id`, `ps`.`pemeriksaan_id` AS `pemeriksaan_id`, `ps`.`jenis_sampel` AS `jenis_sampel`, `ps`.`status_sampel` AS `status_sampel`, `ps`.`tanggal_pengambilan` AS `tanggal_pengambilan`, `ps`.`tanggal_evaluasi` AS `tanggal_evaluasi`, `pl`.`nomor_pemeriksaan` AS `nomor_pemeriksaan`, `p`.`nama` AS `nama_pasien`, `p`.`nik` AS `nik`, `ss`.`lokasi_penyimpanan` AS `lokasi_penyimpanan`, `ss`.`suhu_penyimpanan` AS `suhu_penyimpanan`, `ss`.`volume_sampel` AS `volume_sampel`, `ss`.`satuan_volume` AS `satuan_volume`, `ss`.`status_penyimpanan` AS `status_penyimpanan`, `se`.`masa_berlaku_hari` AS `masa_berlaku_hari`, CASE WHEN `ps`.`tanggal_pengambilan` is null THEN NULL WHEN `ps`.`tanggal_pengambilan` + interval `se`.`masa_berlaku_hari` day < current_timestamp() THEN 'expired' WHEN `ps`.`tanggal_pengambilan` + interval `se`.`masa_berlaku_hari` day < current_timestamp() + interval 1 day THEN 'expiring_soon' ELSE 'valid' END AS `status_berlaku`, `ps`.`tanggal_pengambilan`+ interval `se`.`masa_berlaku_hari` day AS `tanggal_kadaluarsa` FROM ((((`pemeriksaan_sampel` `ps` left join `sampel_storage` `ss` on(`ps`.`sampel_id` = `ss`.`sampel_id` and `ss`.`status_penyimpanan` = 'tersimpan')) left join `sampel_expiry` `se` on(`ps`.`jenis_sampel` = `se`.`jenis_sampel`)) left join `pemeriksaan_lab` `pl` on(`ps`.`pemeriksaan_id` = `pl`.`pemeriksaan_id`)) left join `pasien` `p` on(`pl`.`pasien_id` = `p`.`pasien_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `v_user_details`
--
DROP TABLE IF EXISTS `v_user_details`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_user_details`  AS SELECT `u`.`user_id` AS `user_id`, `u`.`username` AS `username`, `u`.`role` AS `role`, `u`.`is_active` AS `is_active`, `u`.`created_at` AS `created_at`, CASE WHEN `u`.`role` = 'admin' THEN `a`.`nama_admin` WHEN `u`.`role` = 'administrasi' THEN `adm`.`nama_admin` WHEN `u`.`role` = 'petugas_lab' THEN `pl`.`nama_petugas` WHEN `u`.`role` = 'supervisor' THEN `s`.`nama_supervisor` ELSE NULL END AS `nama_lengkap` FROM ((((`users` `u` left join `administrator` `a` on(`u`.`user_id` = `a`.`user_id`)) left join `administrasi` `adm` on(`u`.`user_id` = `adm`.`user_id`)) left join `petugas_lab` `pl` on(`u`.`user_id` = `pl`.`user_id`)) left join `supervisor` `s` on(`u`.`user_id` = `s`.`user_id`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `administrasi`
--
ALTER TABLE `administrasi`
  ADD PRIMARY KEY (`administrasi_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `administrator`
--
ALTER TABLE `administrator`
  ADD PRIMARY KEY (`admin_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `alat_laboratorium`
--
ALTER TABLE `alat_laboratorium`
  ADD PRIMARY KEY (`alat_id`),
  ADD UNIQUE KEY `kode_unik` (`kode_unik`),
  ADD KEY `idx_alat_status` (`status_alat`),
  ADD KEY `idx_status_jadwal` (`status_alat`,`jadwal_kalibrasi`),
  ADD KEY `idx_lokasi` (`lokasi`);

--
-- Indexes for table `calibration_history`
--
ALTER TABLE `calibration_history`
  ADD PRIMARY KEY (`calibration_id`),
  ADD KEY `idx_alat_id` (`alat_id`),
  ADD KEY `idx_tanggal_kalibrasi` (`tanggal_kalibrasi`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `hematologi`
--
ALTER TABLE `hematologi`
  ADD PRIMARY KEY (`hematologi_id`),
  ADD KEY `pemeriksaan_id` (`pemeriksaan_id`);

--
-- Indexes for table `ims`
--
ALTER TABLE `ims`
  ADD PRIMARY KEY (`ims_id`),
  ADD KEY `pemeriksaan_id` (`pemeriksaan_id`);

--
-- Indexes for table `inventory_maintenance_logs`
--
ALTER TABLE `inventory_maintenance_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `item_type` (`item_type`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `inventory_notifications`
--
ALTER TABLE `inventory_notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `type` (`type`),
  ADD KEY `priority` (`priority`),
  ADD KEY `is_read` (`is_read`),
  ADD KEY `created_at` (`created_at`),
  ADD KEY `item_reference` (`item_id`,`item_type`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`invoice_id`),
  ADD UNIQUE KEY `nomor_invoice` (`nomor_invoice`),
  ADD KEY `pemeriksaan_id` (`pemeriksaan_id`),
  ADD KEY `idx_invoice_status` (`status_pembayaran`),
  ADD KEY `idx_invoice_jenis` (`jenis_pembayaran`);

--
-- Indexes for table `kimia_darah`
--
ALTER TABLE `kimia_darah`
  ADD PRIMARY KEY (`kimia_id`),
  ADD KEY `pemeriksaan_id` (`pemeriksaan_id`);

--
-- Indexes for table `lab`
--
ALTER TABLE `lab`
  ADD PRIMARY KEY (`lab_id`);

--
-- Indexes for table `laporan`
--
ALTER TABLE `laporan`
  ADD PRIMARY KEY (`laporan_id`),
  ADD KEY `pemeriksaan_id` (`pemeriksaan_id`),
  ADD KEY `dibuat_oleh` (`dibuat_oleh`);

--
-- Indexes for table `master_kondisi_sampel`
--
ALTER TABLE `master_kondisi_sampel`
  ADD PRIMARY KEY (`kondisi_id`),
  ADD UNIQUE KEY `uk_jenis_kode` (`jenis_sampel`,`kode_kondisi`),
  ADD KEY `idx_jenis_sampel` (`jenis_sampel`),
  ADD KEY `idx_kondisi_kategori` (`kategori`),
  ADD KEY `idx_kondisi_active` (`is_active`);

--
-- Indexes for table `mls`
--
ALTER TABLE `mls`
  ADD PRIMARY KEY (`mls_id`),
  ADD KEY `pemeriksaan_id` (`pemeriksaan_id`);

--
-- Indexes for table `pasien`
--
ALTER TABLE `pasien`
  ADD PRIMARY KEY (`pasien_id`),
  ADD UNIQUE KEY `nik` (`nik`),
  ADD UNIQUE KEY `nomor_registrasi` (`nomor_registrasi`),
  ADD KEY `idx_pasien_nik` (`nik`);

--
-- Indexes for table `patient_requests`
--
ALTER TABLE `patient_requests`
  ADD PRIMARY KEY (`permintaan_id`),
  ADD KEY `pasien_id` (`pasien_id`),
  ADD KEY `status` (`status`),
  ADD KEY `prioritas` (`prioritas`);

--
-- Indexes for table `pemeriksaan_detail`
--
ALTER TABLE `pemeriksaan_detail`
  ADD PRIMARY KEY (`detail_id`),
  ADD KEY `pemeriksaan_id` (`pemeriksaan_id`),
  ADD KEY `idx_detail_pemeriksaan` (`pemeriksaan_id`,`urutan`);

--
-- Indexes for table `pemeriksaan_lab`
--
ALTER TABLE `pemeriksaan_lab`
  ADD PRIMARY KEY (`pemeriksaan_id`),
  ADD UNIQUE KEY `nomor_pemeriksaan` (`nomor_pemeriksaan`),
  ADD KEY `petugas_id` (`petugas_id`),
  ADD KEY `lab_id` (`lab_id`),
  ADD KEY `idx_pemeriksaan_pasien` (`pasien_id`),
  ADD KEY `idx_pemeriksaan_tanggal` (`tanggal_pemeriksaan`),
  ADD KEY `idx_pemeriksaan_status` (`status_pemeriksaan`),
  ADD KEY `idx_pemeriksaan_updated` (`updated_at`),
  ADD KEY `idx_pemeriksaan_completed` (`completed_at`),
  ADD KEY `idx_pemeriksaan_started` (`started_at`);

--
-- Indexes for table `pemeriksaan_sampel`
--
ALTER TABLE `pemeriksaan_sampel`
  ADD PRIMARY KEY (`sampel_id`),
  ADD KEY `pemeriksaan_id` (`pemeriksaan_id`),
  ADD KEY `diambil_oleh` (`diambil_oleh`),
  ADD KEY `idx_sampel_pemeriksaan` (`pemeriksaan_id`),
  ADD KEY `idx_sampel_jenis` (`jenis_sampel`),
  ADD KEY `idx_sampel_status` (`status_sampel`),
  ADD KEY `idx_sampel_evaluasi` (`tanggal_evaluasi`);

--
-- Indexes for table `petugas_lab`
--
ALTER TABLE `petugas_lab`
  ADD PRIMARY KEY (`petugas_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `qc_parameters`
--
ALTER TABLE `qc_parameters`
  ADD PRIMARY KEY (`parameter_id`),
  ADD KEY `idx_param_alat` (`alat_id`);

--
-- Indexes for table `quality_control`
--
ALTER TABLE `quality_control`
  ADD PRIMARY KEY (`qc_id`),
  ADD KEY `idx_qc_alat` (`alat_id`),
  ADD KEY `idx_qc_tanggal` (`tanggal_qc`),
  ADD KEY `idx_qc_hasil` (`hasil_qc`),
  ADD KEY `idx_qc_status` (`status`),
  ADD KEY `fk_qc_user` (`user_id`),
  ADD KEY `idx_qc_composite` (`tanggal_qc`,`hasil_qc`,`status`),
  ADD KEY `idx_qc_user_date` (`user_id`,`tanggal_qc`);

--
-- Indexes for table `reagen`
--
ALTER TABLE `reagen`
  ADD PRIMARY KEY (`reagen_id`),
  ADD UNIQUE KEY `kode_unik` (`kode_unik`),
  ADD KEY `idx_reagen_status` (`status`),
  ADD KEY `idx_reagen_expired` (`expired_date`),
  ADD KEY `idx_status_stock` (`status`,`jumlah_stok`),
  ADD KEY `idx_expired_status` (`expired_date`,`status`),
  ADD KEY `idx_lokasi_penyimpanan` (`lokasi_penyimpanan`);

--
-- Indexes for table `sampel_expiry`
--
ALTER TABLE `sampel_expiry`
  ADD PRIMARY KEY (`expiry_id`),
  ADD UNIQUE KEY `uk_jenis_sampel` (`jenis_sampel`);

--
-- Indexes for table `sampel_kondisi`
--
ALTER TABLE `sampel_kondisi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sampel_id` (`sampel_id`),
  ADD KEY `idx_kondisi_id` (`kondisi_id`);

--
-- Indexes for table `sampel_storage`
--
ALTER TABLE `sampel_storage`
  ADD PRIMARY KEY (`storage_id`),
  ADD KEY `idx_sampel_storage` (`sampel_id`),
  ADD KEY `idx_storage_lokasi` (`lokasi_penyimpanan`),
  ADD KEY `idx_storage_status` (`status_penyimpanan`),
  ADD KEY `fk_storage_petugas` (`petugas_id`);

--
-- Indexes for table `serologi_imunologi`
--
ALTER TABLE `serologi_imunologi`
  ADD PRIMARY KEY (`serologi_id`),
  ADD KEY `pemeriksaan_id` (`pemeriksaan_id`);

--
-- Indexes for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`movement_id`),
  ADD KEY `idx_reagen_id` (`reagen_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_movement_date` (`movement_date`);

--
-- Indexes for table `storage_temperature_log`
--
ALTER TABLE `storage_temperature_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_temp_lokasi` (`lokasi_storage`),
  ADD KEY `idx_temp_tanggal` (`tanggal_pencatatan`),
  ADD KEY `idx_temp_status` (`status_suhu`),
  ADD KEY `fk_temp_petugas` (`petugas_id`);

--
-- Indexes for table `supervisor`
--
ALTER TABLE `supervisor`
  ADD PRIMARY KEY (`supervisor_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tbc`
--
ALTER TABLE `tbc`
  ADD PRIMARY KEY (`tbc_id`),
  ADD KEY `pemeriksaan_id` (`pemeriksaan_id`);

--
-- Indexes for table `timeline_progres`
--
ALTER TABLE `timeline_progres`
  ADD PRIMARY KEY (`timeline_id`),
  ADD KEY `pemeriksaan_id` (`pemeriksaan_id`),
  ADD KEY `petugas_id` (`petugas_id`);

--
-- Indexes for table `urinologi`
--
ALTER TABLE `urinologi`
  ADD PRIMARY KEY (`urinologi_id`),
  ADD KEY `pemeriksaan_id` (`pemeriksaan_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_users_username` (`username`),
  ADD KEY `idx_users_role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2171;

--
-- AUTO_INCREMENT for table `administrasi`
--
ALTER TABLE `administrasi`
  MODIFY `administrasi_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `administrator`
--
ALTER TABLE `administrator`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `alat_laboratorium`
--
ALTER TABLE `alat_laboratorium`
  MODIFY `alat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `calibration_history`
--
ALTER TABLE `calibration_history`
  MODIFY `calibration_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `hematologi`
--
ALTER TABLE `hematologi`
  MODIFY `hematologi_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `ims`
--
ALTER TABLE `ims`
  MODIFY `ims_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `inventory_maintenance_logs`
--
ALTER TABLE `inventory_maintenance_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_notifications`
--
ALTER TABLE `inventory_notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `invoice_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `kimia_darah`
--
ALTER TABLE `kimia_darah`
  MODIFY `kimia_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `lab`
--
ALTER TABLE `lab`
  MODIFY `lab_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `laporan`
--
ALTER TABLE `laporan`
  MODIFY `laporan_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `master_kondisi_sampel`
--
ALTER TABLE `master_kondisi_sampel`
  MODIFY `kondisi_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `mls`
--
ALTER TABLE `mls`
  MODIFY `mls_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pasien`
--
ALTER TABLE `pasien`
  MODIFY `pasien_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `patient_requests`
--
ALTER TABLE `patient_requests`
  MODIFY `permintaan_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pemeriksaan_detail`
--
ALTER TABLE `pemeriksaan_detail`
  MODIFY `detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `pemeriksaan_lab`
--
ALTER TABLE `pemeriksaan_lab`
  MODIFY `pemeriksaan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `pemeriksaan_sampel`
--
ALTER TABLE `pemeriksaan_sampel`
  MODIFY `sampel_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `petugas_lab`
--
ALTER TABLE `petugas_lab`
  MODIFY `petugas_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `qc_parameters`
--
ALTER TABLE `qc_parameters`
  MODIFY `parameter_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=135;

--
-- AUTO_INCREMENT for table `quality_control`
--
ALTER TABLE `quality_control`
  MODIFY `qc_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `reagen`
--
ALTER TABLE `reagen`
  MODIFY `reagen_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `sampel_expiry`
--
ALTER TABLE `sampel_expiry`
  MODIFY `expiry_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `sampel_kondisi`
--
ALTER TABLE `sampel_kondisi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sampel_storage`
--
ALTER TABLE `sampel_storage`
  MODIFY `storage_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `serologi_imunologi`
--
ALTER TABLE `serologi_imunologi`
  MODIFY `serologi_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `movement_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `storage_temperature_log`
--
ALTER TABLE `storage_temperature_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supervisor`
--
ALTER TABLE `supervisor`
  MODIFY `supervisor_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbc`
--
ALTER TABLE `tbc`
  MODIFY `tbc_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `timeline_progres`
--
ALTER TABLE `timeline_progres`
  MODIFY `timeline_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `urinologi`
--
ALTER TABLE `urinologi`
  MODIFY `urinologi_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `administrasi`
--
ALTER TABLE `administrasi`
  ADD CONSTRAINT `administrasi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `administrator`
--
ALTER TABLE `administrator`
  ADD CONSTRAINT `administrator_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `calibration_history`
--
ALTER TABLE `calibration_history`
  ADD CONSTRAINT `fk_calibration_alat` FOREIGN KEY (`alat_id`) REFERENCES `alat_laboratorium` (`alat_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_calibration_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `hematologi`
--
ALTER TABLE `hematologi`
  ADD CONSTRAINT `hematologi_ibfk_1` FOREIGN KEY (`pemeriksaan_id`) REFERENCES `pemeriksaan_lab` (`pemeriksaan_id`);

--
-- Constraints for table `ims`
--
ALTER TABLE `ims`
  ADD CONSTRAINT `ims_ibfk_1` FOREIGN KEY (`pemeriksaan_id`) REFERENCES `pemeriksaan_lab` (`pemeriksaan_id`);

--
-- Constraints for table `invoice`
--
ALTER TABLE `invoice`
  ADD CONSTRAINT `invoice_ibfk_1` FOREIGN KEY (`pemeriksaan_id`) REFERENCES `pemeriksaan_lab` (`pemeriksaan_id`);

--
-- Constraints for table `kimia_darah`
--
ALTER TABLE `kimia_darah`
  ADD CONSTRAINT `kimia_darah_ibfk_1` FOREIGN KEY (`pemeriksaan_id`) REFERENCES `pemeriksaan_lab` (`pemeriksaan_id`);

--
-- Constraints for table `laporan`
--
ALTER TABLE `laporan`
  ADD CONSTRAINT `laporan_ibfk_1` FOREIGN KEY (`pemeriksaan_id`) REFERENCES `pemeriksaan_lab` (`pemeriksaan_id`),
  ADD CONSTRAINT `laporan_ibfk_2` FOREIGN KEY (`dibuat_oleh`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `mls`
--
ALTER TABLE `mls`
  ADD CONSTRAINT `mls_ibfk_1` FOREIGN KEY (`pemeriksaan_id`) REFERENCES `pemeriksaan_lab` (`pemeriksaan_id`);

--
-- Constraints for table `patient_requests`
--
ALTER TABLE `patient_requests`
  ADD CONSTRAINT `fk_patient_requests_pasien` FOREIGN KEY (`pasien_id`) REFERENCES `pasien` (`pasien_id`) ON DELETE CASCADE;

--
-- Constraints for table `pemeriksaan_detail`
--
ALTER TABLE `pemeriksaan_detail`
  ADD CONSTRAINT `fk_pemeriksaan_detail` FOREIGN KEY (`pemeriksaan_id`) REFERENCES `pemeriksaan_lab` (`pemeriksaan_id`) ON DELETE CASCADE;

--
-- Constraints for table `pemeriksaan_lab`
--
ALTER TABLE `pemeriksaan_lab`
  ADD CONSTRAINT `pemeriksaan_lab_ibfk_1` FOREIGN KEY (`pasien_id`) REFERENCES `pasien` (`pasien_id`),
  ADD CONSTRAINT `pemeriksaan_lab_ibfk_3` FOREIGN KEY (`petugas_id`) REFERENCES `petugas_lab` (`petugas_id`),
  ADD CONSTRAINT `pemeriksaan_lab_ibfk_4` FOREIGN KEY (`lab_id`) REFERENCES `lab` (`lab_id`);

--
-- Constraints for table `pemeriksaan_sampel`
--
ALTER TABLE `pemeriksaan_sampel`
  ADD CONSTRAINT `fk_pemeriksaan_sampel` FOREIGN KEY (`pemeriksaan_id`) REFERENCES `pemeriksaan_lab` (`pemeriksaan_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sampel_petugas` FOREIGN KEY (`diambil_oleh`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `petugas_lab`
--
ALTER TABLE `petugas_lab`
  ADD CONSTRAINT `petugas_lab_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `qc_parameters`
--
ALTER TABLE `qc_parameters`
  ADD CONSTRAINT `fk_param_alat` FOREIGN KEY (`alat_id`) REFERENCES `alat_laboratorium` (`alat_id`) ON DELETE CASCADE;

--
-- Constraints for table `quality_control`
--
ALTER TABLE `quality_control`
  ADD CONSTRAINT `fk_qc_alat` FOREIGN KEY (`alat_id`) REFERENCES `alat_laboratorium` (`alat_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_qc_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `sampel_kondisi`
--
ALTER TABLE `sampel_kondisi`
  ADD CONSTRAINT `fk_sk_kondisi` FOREIGN KEY (`kondisi_id`) REFERENCES `master_kondisi_sampel` (`kondisi_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_sk_sampel` FOREIGN KEY (`sampel_id`) REFERENCES `pemeriksaan_sampel` (`sampel_id`) ON DELETE CASCADE;

--
-- Constraints for table `sampel_storage`
--
ALTER TABLE `sampel_storage`
  ADD CONSTRAINT `fk_storage_petugas` FOREIGN KEY (`petugas_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_storage_sampel` FOREIGN KEY (`sampel_id`) REFERENCES `pemeriksaan_sampel` (`sampel_id`) ON DELETE CASCADE;

--
-- Constraints for table `serologi_imunologi`
--
ALTER TABLE `serologi_imunologi`
  ADD CONSTRAINT `serologi_imunologi_ibfk_1` FOREIGN KEY (`pemeriksaan_id`) REFERENCES `pemeriksaan_lab` (`pemeriksaan_id`);

--
-- Constraints for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `fk_stock_movements_reagen` FOREIGN KEY (`reagen_id`) REFERENCES `reagen` (`reagen_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_stock_movements_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `storage_temperature_log`
--
ALTER TABLE `storage_temperature_log`
  ADD CONSTRAINT `fk_temp_petugas` FOREIGN KEY (`petugas_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `supervisor`
--
ALTER TABLE `supervisor`
  ADD CONSTRAINT `supervisor_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `tbc`
--
ALTER TABLE `tbc`
  ADD CONSTRAINT `tbc_ibfk_1` FOREIGN KEY (`pemeriksaan_id`) REFERENCES `pemeriksaan_lab` (`pemeriksaan_id`);

--
-- Constraints for table `timeline_progres`
--
ALTER TABLE `timeline_progres`
  ADD CONSTRAINT `timeline_progres_ibfk_1` FOREIGN KEY (`pemeriksaan_id`) REFERENCES `pemeriksaan_lab` (`pemeriksaan_id`),
  ADD CONSTRAINT `timeline_progres_ibfk_2` FOREIGN KEY (`petugas_id`) REFERENCES `petugas_lab` (`petugas_id`);

--
-- Constraints for table `urinologi`
--
ALTER TABLE `urinologi`
  ADD CONSTRAINT `urinologi_ibfk_1` FOREIGN KEY (`pemeriksaan_id`) REFERENCES `pemeriksaan_lab` (`pemeriksaan_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
