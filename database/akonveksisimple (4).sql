-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 23, 2025 at 11:18 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `akonveksisimple`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `proses_antrian_baru` (IN `p_custom_id` INT, IN `p_tanggal` DATE)   BEGIN
    DECLARE v_last_urutan INT;
    DECLARE v_kapasitas_tersedia INT;
    DECLARE v_tanggal_kapasitas DATE;
    DECLARE v_antrian_id INT;
    
    -- Hitung urutan terakhir
    SELECT COALESCE(MAX(urutan), 0) INTO v_last_urutan 
    FROM antrian 
    WHERE tanggal = p_tanggal;
    
    -- Insert antrian baru
    INSERT INTO antrian (custom_id, tanggal, urutan, status)
    VALUES (p_custom_id, p_tanggal, v_last_urutan + 1, 'Dalam Antrian Produksi');
    
    SET v_antrian_id = LAST_INSERT_ID();
    
    -- Cari kapasitas tersedia
    SELECT tanggal INTO v_tanggal_kapasitas FROM kapasitas 
    WHERE kapasitas_terpakai < kapasitas_total 
    ORDER BY tanggal ASC LIMIT 1;
    
    -- Jika ada kapasitas
    IF v_tanggal_kapasitas IS NOT NULL THEN
        -- Update kapasitas
        UPDATE kapasitas 
        SET kapasitas_terpakai = kapasitas_terpakai + 1 
        WHERE tanggal = v_tanggal_kapasitas;
        
        -- Update antrian
        UPDATE antrian 
        SET status = 'Dalam Produksi', 
            tanggal = v_tanggal_kapasitas 
        WHERE id = v_antrian_id;
        
        -- Update pesanan
        UPDATE pesanan 
        SET status = 'Dalam Produksi' 
        WHERE id = p_custom_id;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_kapasitas_production` (IN `p_tanggal` DATE)   BEGIN
    DECLARE v_total INT;
    DECLARE v_terpakai INT;
    DECLARE v_sisa INT;
    DECLARE v_count INT;
    
    -- Dapatkan atau buat record kapasitas
    INSERT INTO kapasitas (tanggal, kapasitas_total, kapasitas_terpakai, created_at, updated_at)
    VALUES (p_tanggal, 120, 0, NOW(), NOW())
    ON DUPLICATE KEY UPDATE updated_at = NOW();
    
    -- Dapatkan nilai kapasitas
    SELECT kapasitas_total, kapasitas_terpakai INTO v_total, v_terpakai
    FROM kapasitas WHERE tanggal = p_tanggal;
    
    -- Hitung sisa kapasitas
    SET v_sisa = v_total - v_terpakai;
    
    -- Hitung antrian yang bisa dipindahkan ke produksi
    SELECT COUNT(*) INTO v_count
    FROM antrian
    WHERE tanggal = p_tanggal 
    AND status = 'Dalam Antrian Produksi'
    ORDER BY urutan ASC
    LIMIT v_sisa;
    
    -- Update status antrian dan kapasitas jika ada yang bisa diproses
    IF v_count > 0 THEN
        -- Update status antrian
        UPDATE antrian
        SET status = 'Dalam Produksi',
            updated_at = NOW()
        WHERE id IN (
            SELECT id FROM (
                SELECT id 
                FROM antrian
                WHERE tanggal = p_tanggal 
                AND status = 'Dalam Antrian Produksi'
                ORDER BY urutan ASC
                LIMIT v_sisa
            ) AS temp
        );
        
        -- Update kapasitas terpakai
        UPDATE kapasitas
        SET kapasitas_terpakai = kapasitas_terpakai + v_count,
            updated_at = NOW()
        WHERE tanggal = p_tanggal;
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `antrian`
--

CREATE TABLE `antrian` (
  `id` bigint UNSIGNED NOT NULL,
  `custom_id` bigint UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `jumlah` int DEFAULT '1',
  `status` enum('Dalam Antrian Produksi','Dalam Produksi','Selesai Produksi') COLLATE utf8mb4_unicode_ci DEFAULT 'Dalam Antrian Produksi',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `antrian`
--

INSERT INTO `antrian` (`id`, `custom_id`, `tanggal`, `jumlah`, `status`, `created_at`, `updated_at`) VALUES
(43, 61, '2025-06-20', 26, 'Selesai Produksi', '2025-06-20 06:33:26', '2025-06-20 06:34:55'),
(44, 62, '2025-06-20', 26, 'Selesai Produksi', '2025-06-20 06:33:35', '2025-06-20 06:35:18');

-- --------------------------------------------------------

--
-- Table structure for table `bahan`
--

CREATE TABLE `bahan` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `satuan` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stok` decimal(10,2) NOT NULL DEFAULT '0.00',
  `harga` decimal(10,2) NOT NULL,
  `img` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bahan`
--

INSERT INTO `bahan` (`id`, `nama`, `satuan`, `stok`, `harga`, `img`, `created_at`, `updated_at`) VALUES
(1, 'Kain corduroy', 'Meter', '150.00', '20000.00', NULL, '2025-05-11 11:49:58', '2025-05-11 12:04:16'),
(2, 'Benang jahit', 'Roll', '150.00', '10000.00', 'bahan_images/5aJWZx6qEan6l20AAyMIVnZxOcH57ossbLsdq4s6.jpg', '2025-05-11 11:58:50', '2025-06-04 00:35:23'),
(3, 'Resleting', 'Buah', '300.00', '7000.00', NULL, '2025-05-11 12:01:52', '2025-05-11 12:04:05'),
(4, 'Kain furing', 'Meter', '150.00', '25000.00', NULL, '2025-05-11 12:06:32', '2025-05-11 12:06:32'),
(5, 'Kain Canvas', 'Meter', '250.00', '20000.00', NULL, '2025-05-15 04:58:21', '2025-05-15 04:58:21'),
(6, 'Polyester', 'Meter', '50.00', '35000.00', NULL, '2025-05-26 23:23:49', '2025-05-26 23:23:49'),
(7, 'Cotton', 'Meter', '50.00', '25000.00', NULL, '2025-05-26 23:24:13', '2025-05-26 23:24:13'),
(8, 'kain levis', 'Meter', '50.00', '10000.00', NULL, '2025-05-27 05:25:11', '2025-05-27 05:25:11'),
(9, 'Kain Katun', 'Meter', '10.00', '23000.00', NULL, '2025-05-27 05:53:17', '2025-05-27 05:53:17');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `produk_id` bigint UNSIGNED NOT NULL,
  `warna_id` bigint UNSIGNED DEFAULT NULL,
  `ukuran_id` bigint UNSIGNED DEFAULT NULL,
  `jumlah` int NOT NULL DEFAULT '1',
  `harga_satuan` decimal(10,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `customer_id`, `produk_id`, `warna_id`, `ukuran_id`, `jumlah`, `harga_satuan`, `subtotal`, `created_at`, `updated_at`) VALUES
(50, 8, 3, 5, 9, 30, '160500.00', '4815000.00', '2025-06-20 06:49:34', '2025-06-20 06:49:34');

-- --------------------------------------------------------

--
-- Table structure for table `custom`
--

CREATE TABLE `custom` (
  `id` bigint UNSIGNED NOT NULL,
  `pesanan_id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `template_id` bigint UNSIGNED DEFAULT NULL,
  `ukuran` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `warna` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jumlah` int NOT NULL DEFAULT '1',
  `harga_estimasi` decimal(10,2) DEFAULT NULL,
  `status` enum('Belum Diproduksi','Dalam Antrian Produksi','Dalam Produksi','Selesai Produksi') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Belum Diproduksi',
  `estimasi_selesai` date DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `img` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `custom`
--

INSERT INTO `custom` (`id`, `pesanan_id`, `customer_id`, `template_id`, `ukuran`, `warna`, `model`, `jumlah`, `harga_estimasi`, `status`, `estimasi_selesai`, `tanggal_mulai`, `catatan`, `img`, `created_at`, `updated_at`) VALUES
(61, 110, 8, 4, 'xl', '#50C878', 'Kaos', 26, '100800.00', 'Selesai Produksi', NULL, NULL, 'oke', 'custom/sLpZVeLiEmUI4FPQzh9OU7PJ6UfOHo1px5hhdkhP.png', '2025-06-20 06:29:29', '2025-06-20 06:34:55'),
(62, 110, 8, 4, 'l', '#50C878', 'Kaos', 26, '100800.00', 'Selesai Produksi', NULL, NULL, 'oke', 'custom/tNpvlgNkJdVPL8QevjAUHQ4Y0nurY7AuHwA2LIVM.png', '2025-06-20 06:29:29', '2025-06-20 06:35:18');

--
-- Triggers `custom`
--
DELIMITER $$
CREATE TRIGGER `trg_after_insert_custom` AFTER INSERT ON `custom` FOR EACH ROW -- MODIFIKASI UNTUK trg_after_insert_custom DAN trg_after_update_custom
-- (Asumsikan kedua trigger ini berjalan pada tabel `custom`)

BEGIN
    DECLARE total_custom INT;
    DECLARE total_selesai INT;
    DECLARE status_pembayaran_pesanan VARCHAR(50);
    DECLARE current_pesanan_status VARCHAR(50); -- Tambahan untuk mencegah overwrite status 'Selesai Produksi'

    -- Ambil status pembayaran dan status pesanan dari tabel pesanan yang terkait
    SELECT status_pembayaran, status INTO status_pembayaran_pesanan, current_pesanan_status
    FROM pesanan
    WHERE id = NEW.pesanan_id;

    -- Hanya lanjutkan jika pembayaran sudah diverifikasi atau sudah lunas/DP
    -- Dan status pesanan belum "Selesai Produksi" (agar tidak berubah kembali)
    IF (status_pembayaran_pesanan = 'Pembayaran Diverifikasi' OR status_pembayaran_pesanan = 'DP' OR status_pembayaran_pesanan = 'Lunas')
       AND current_pesanan_status <> 'Selesai Produksi' THEN -- Penting: Hindari mengganti status 'Selesai Produksi'
        -- Hitung total custom untuk pesanan ini
        SELECT COUNT(*) INTO total_custom
        FROM custom
        WHERE pesanan_id = NEW.pesanan_id;

        -- Hitung jumlah custom yang sudah selesai produksi
        SELECT COUNT(*) INTO total_selesai
        FROM custom
        WHERE pesanan_id = NEW.pesanan_id AND status = 'Selesai Produksi';

        IF total_selesai = total_custom THEN
            UPDATE pesanan
            SET status = 'Selesai Produksi'
            WHERE id = NEW.pesanan_id;
        ELSE
            -- Jika tidak semua selesai, dan belum 'Selesai Produksi', maka set 'Dalam Produksi'
            UPDATE pesanan
            SET status = 'Dalam Produksi'
            WHERE id = NEW.pesanan_id;
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_after_update_custom` AFTER UPDATE ON `custom` FOR EACH ROW BEGIN
    DECLARE total_custom INT;
    DECLARE total_selesai INT;

    -- Hitung total custom untuk pesanan ini
    SELECT COUNT(*) INTO total_custom
    FROM custom
    WHERE pesanan_id = NEW.pesanan_id;

    -- Hitung jumlah custom yang sudah selesai produksi
    SELECT COUNT(*) INTO total_selesai
    FROM custom
    WHERE pesanan_id = NEW.pesanan_id AND status = 'Selesai Produksi';

    IF total_selesai = total_custom THEN
        UPDATE pesanan
        SET status = 'Selesai Produksi'
        WHERE id = NEW.pesanan_id;
    ELSE
        UPDATE pesanan
        SET status = 'Dalam Produksi'
        WHERE id = NEW.pesanan_id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `no_telp` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `img` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`id`, `nama`, `email`, `password`, `alamat`, `no_telp`, `img`, `created_at`, `updated_at`) VALUES
(7, 'infra', 'indra@gmail.com', '$2y$12$b.7/ZFXIPAko8NkdnuWkcuLFhQ0bDPjMOiDI2TIl//FkDtmgZr7.q', 'merdeka08', '09374940302', NULL, '2025-06-20 06:13:39', '2025-06-20 06:13:39'),
(8, 'Rayhan', 'rayhan@gmail.com', '$2y$12$172PIMVnunQlTd2V6cnOAee4lZo7iwwc9Zz2PSc5Y64136xjf8cDO', 'walet', '0985873747', NULL, '2025-06-20 06:26:06', '2025-06-20 06:26:06');

-- --------------------------------------------------------

--
-- Table structure for table `keuangan`
--

CREATE TABLE `keuangan` (
  `id` bigint UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `total_pemasukan` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_pengeluaran` decimal(15,2) NOT NULL DEFAULT '0.00',
  `saldo` decimal(15,2) NOT NULL DEFAULT '0.00',
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `keuangan`
--

INSERT INTO `keuangan` (`id`, `tanggal`, `total_pemasukan`, `total_pengeluaran`, `saldo`, `catatan`, `created_at`, `updated_at`) VALUES
(1, '2025-05-22', '0.00', '0.00', '0.00', NULL, '2025-05-22 12:27:30', '2025-06-20 12:35:15'),
(3, '2025-05-23', '0.00', '0.00', '0.00', NULL, '2025-05-22 12:30:57', '2025-06-20 12:35:05'),
(4, '2025-05-27', '0.00', '0.00', '0.00', NULL, '2025-05-27 13:20:57', '2025-06-20 12:35:19');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(2, '2025_03_07_091124_create_table_konveksi', 1),
(3, '2025_05_13_074158_create_keuangan_tables', 2),
(4, '2025_05_16_103051_add_kategori_to_templates_table', 3),
(5, '2025_05_16_115655_create_cart_table', 4),
(6, '2025_05_16_134141_update_user_id_to_customer_id_in_cart_table', 5),
(7, '2025_05_16_170057_update_pesanan_table_add_pembayaran_id_and_remove_fields', 6);

-- --------------------------------------------------------

--
-- Table structure for table `pemasukan`
--

CREATE TABLE `pemasukan` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `sumber` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `tanggal` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `pemasukan`
--
DELIMITER $$
CREATE TRIGGER `after_delete_pemasukan` AFTER DELETE ON `pemasukan` FOR EACH ROW BEGIN
  UPDATE keuangan 
  SET total_pemasukan = total_pemasukan - OLD.jumlah,
      saldo = saldo - OLD.jumlah,
      updated_at = NOW()
  WHERE tanggal = OLD.tanggal;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_insert_pemasukan` AFTER INSERT ON `pemasukan` FOR EACH ROW BEGIN
  INSERT INTO keuangan (tanggal, total_pemasukan, total_pengeluaran, saldo, created_at, updated_at)
  VALUES (NEW.tanggal, NEW.jumlah, 0, NEW.jumlah, NOW(), NOW())
  ON DUPLICATE KEY UPDATE 
    total_pemasukan = total_pemasukan + NEW.jumlah,
    saldo = saldo + NEW.jumlah,
    updated_at = NOW();
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_update_pemasukan` AFTER UPDATE ON `pemasukan` FOR EACH ROW BEGIN
  IF OLD.tanggal = NEW.tanggal THEN
    UPDATE keuangan 
    SET total_pemasukan = total_pemasukan - OLD.jumlah + NEW.jumlah,
        saldo = saldo - OLD.jumlah + NEW.jumlah,
        updated_at = NOW()
    WHERE tanggal = NEW.tanggal;
  ELSE
    -- Kurangi dari tanggal lama
    UPDATE keuangan 
    SET total_pemasukan = total_pemasukan - OLD.jumlah,
        saldo = saldo - OLD.jumlah,
        updated_at = NOW()
    WHERE tanggal = OLD.tanggal;

    -- Tambahkan ke tanggal baru
    INSERT INTO keuangan (tanggal, total_pemasukan, total_pengeluaran, saldo, created_at, updated_at)
    VALUES (NEW.tanggal, NEW.jumlah, 0, NEW.jumlah, NOW(), NOW())
    ON DUPLICATE KEY UPDATE 
      total_pemasukan = total_pemasukan + NEW.jumlah,
      saldo = saldo + NEW.jumlah,
      updated_at = NOW();
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id` bigint UNSIGNED NOT NULL,
  `pesanan_id` bigint UNSIGNED NOT NULL,
  `jumlah` decimal(10,2) NOT NULL,
  `metode` enum('Transfer Bank','COD','Kartu Kredit','Lainnya') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('Menunggu Konfirmasi','Berhasil','Gagal','Dibatalkan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Menunggu Konfirmasi',
  `bukti_bayar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_bayar` datetime DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `is_dp` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id`, `pesanan_id`, `jumlah`, `metode`, `status`, `bukti_bayar`, `tanggal_bayar`, `catatan`, `is_dp`, `created_at`, `updated_at`) VALUES
(94, 110, '2620800.00', 'Transfer Bank', 'Gagal', NULL, '2025-06-20 00:00:00', NULL, 1, '2025-06-20 06:29:29', '2025-06-20 06:30:16'),
(95, 110, '2620800.00', 'Transfer Bank', 'Berhasil', 'bukti_bayar_ulang/rh5pUQ9ovUx6Wrb0MlcrQxdST18e8Xg6UEyGfDX6.png', '2025-06-20 00:00:00', 'tolong acc', 1, '2025-06-20 06:31:23', '2025-06-20 06:32:24'),
(96, 110, '2620800.00', 'Transfer Bank', 'Berhasil', 'bukti_pelunasan/Tdnx1p85fUNdGcxuiUfJB6oxFWvLVNa4tBPaMBC7.png', '2025-06-20 00:00:00', 'oke', 0, '2025-06-20 06:37:39', '2025-06-20 06:38:23');

-- --------------------------------------------------------

--
-- Table structure for table `pengeluaran`
--

CREATE TABLE `pengeluaran` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `kategori` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `tanggal` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `pengeluaran`
--
DELIMITER $$
CREATE TRIGGER `after_delete_pengeluaran` AFTER DELETE ON `pengeluaran` FOR EACH ROW BEGIN
  UPDATE keuangan 
  SET total_pengeluaran = total_pengeluaran - OLD.jumlah,
      saldo = saldo + OLD.jumlah,
      updated_at = NOW()
  WHERE tanggal = OLD.tanggal;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_insert_pengeluaran` AFTER INSERT ON `pengeluaran` FOR EACH ROW BEGIN
  INSERT INTO keuangan (tanggal, total_pemasukan, total_pengeluaran, saldo, created_at, updated_at)
  VALUES (NEW.tanggal, 0, NEW.jumlah, -NEW.jumlah, NOW(), NOW())
  ON DUPLICATE KEY UPDATE 
    total_pengeluaran = total_pengeluaran + NEW.jumlah,
    saldo = saldo - NEW.jumlah,
    updated_at = NOW();
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_update_pengeluaran` AFTER UPDATE ON `pengeluaran` FOR EACH ROW BEGIN
  IF OLD.tanggal = NEW.tanggal THEN
    UPDATE keuangan 
    SET total_pengeluaran = total_pengeluaran - OLD.jumlah + NEW.jumlah,
        saldo = saldo + OLD.jumlah - NEW.jumlah,
        updated_at = NOW()
    WHERE tanggal = NEW.tanggal;
  ELSE
    UPDATE keuangan 
    SET total_pengeluaran = total_pengeluaran - OLD.jumlah,
        saldo = saldo + OLD.jumlah,
        updated_at = NOW()
    WHERE tanggal = OLD.tanggal;

    INSERT INTO keuangan (tanggal, total_pemasukan, total_pengeluaran, saldo, created_at, updated_at)
    VALUES (NEW.tanggal, 0, NEW.jumlah, -NEW.jumlah, NOW(), NOW())
    ON DUPLICATE KEY UPDATE 
      total_pengeluaran = total_pengeluaran + NEW.jumlah,
      saldo = saldo - NEW.jumlah,
      updated_at = NOW();
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `pengiriman`
--

CREATE TABLE `pengiriman` (
  `id` bigint UNSIGNED NOT NULL,
  `pesanan_id` bigint UNSIGNED NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `kurir` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `resi` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto_resi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `biaya` decimal(10,2) NOT NULL,
  `status` enum('Dalam Pengiriman','Selesai Pengiriman') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Dalam Pengiriman',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengiriman`
--

INSERT INTO `pengiriman` (`id`, `pesanan_id`, `alamat`, `kurir`, `resi`, `foto_resi`, `biaya`, `status`, `created_at`, `updated_at`) VALUES
(12, 110, 'walet', 'sikilat', '002', 'resi/eORi6pg7RnvNaXuxU9lbh1OcsRBNmYw9RBfHqhIn.png', '15000.00', 'Selesai Pengiriman', '2025-06-20 06:40:34', '2025-06-20 06:40:51');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `pembayaran_id` bigint UNSIGNED DEFAULT NULL,
  `total_harga` decimal(10,2) NOT NULL,
  `status_pembayaran` enum('Menunggu Verifikasi','DP','Lunas','Pembayaran Gagal') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Menunggu Verifikasi',
  `sisa_pembayaran` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` enum('Menunggu Pembayaran','Menunggu Konfirmasi','Pembayaran Diverifikasi','Dalam Antrian Produksi','Dalam Produksi','Selesai Produksi','Sedang Pengemasan','Siap Dikirim','Dalam Pengiriman','Selesai Pengiriman','Selesai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Menunggu Pembayaran',
  `tanggal_selesai` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id`, `customer_id`, `pembayaran_id`, `total_harga`, `status_pembayaran`, `sisa_pembayaran`, `status`, `tanggal_selesai`, `created_at`, `updated_at`) VALUES
(110, 8, 94, '5241600.00', 'Lunas', '0.00', 'Selesai Pengiriman', '2025-06-20', '2025-06-20 06:29:29', '2025-06-20 06:40:51');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan_detail`
--

CREATE TABLE `pesanan_detail` (
  `id` bigint UNSIGNED NOT NULL,
  `pesanan_id` bigint UNSIGNED NOT NULL,
  `produk_id` bigint UNSIGNED DEFAULT NULL,
  `custom_id` bigint UNSIGNED DEFAULT NULL,
  `jumlah` int NOT NULL,
  `ukuran` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `warna` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `harga` decimal(10,2) NOT NULL,
  `sub_total` decimal(10,2) GENERATED ALWAYS AS ((`jumlah` * `harga`)) STORED,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pesanan_detail`
--

INSERT INTO `pesanan_detail` (`id`, `pesanan_id`, `produk_id`, `custom_id`, `jumlah`, `ukuran`, `warna`, `harga`, `created_at`, `updated_at`) VALUES
(81, 110, NULL, 61, 26, 'xl', '#50C878', '100800.00', '2025-06-20 06:29:29', '2025-06-20 06:29:29'),
(82, 110, NULL, 62, 26, 'l', '#50C878', '100800.00', '2025-06-20 06:29:29', '2025-06-20 06:29:29');

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `total_stok` int NOT NULL DEFAULT '0',
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `img` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id`, `nama`, `kategori`, `harga`, `total_stok`, `deskripsi`, `img`, `created_at`, `updated_at`) VALUES
(3, 'Work Jackett', 'Jaket', '160500.00', 78, '<h2>Jaket Corduroy – Siap Pakai, Stylish, & Gratis Ongkir!</h2>\r\n<p>Mau tampil keren tanpa ribet desain? Jaket corduroy kami hadir sebagai pilihan terbaik untuk kamu yang ingin langsung pakai tanpa harus menunggu proses custom. Dibuat dari bahan berkualitas dan jahitan rapi, jaket ini cocok untuk dipakai sehari-hari, nongkrong, kuliah, hingga hangout!</p><br><br>\r\n\r\n<h3>Kenapa Pilih Jaket Ini?</h3>\r\n<ul>\r\n  <li>✅ Desain kekinian & siap pakai</li>\r\n  <li>✅ Gratis Ongkir ke seluruh Indonesia</li>\r\n  <li>✅ Jahitan rapi dan kuat</li>\r\n  <li>✅ Nyaman dipakai di segala cuaca</li>\r\n  <li>✅ Bisa dijadikan hadiah atau koleksi pribadi</li>\r\n</ul><br><br>\r\n\r\n<h3>📦 Tersedia dalam berbagai ukuran (S, M, L, XL)<br>\r\n🚚 Gratis Ongkir langsung ke rumah kamu!<br>\r\n💬Pesan sekarang dan rasakan kenyamanan jaket berkualitas tinggi!</h3>', 'produk_images/InHhS3NWBxr0zs4KZ3yta05ZGR5WNdC9lkaj9scg.png', '2025-05-11 13:32:56', '2025-06-08 23:36:23'),
(5, 'Topi Toker', 'Topi', '30000.00', 22, '<h2>Jaket Corduroy – Siap Pakai, Stylish, & Gratis Ongkir!</h2>\r\n<p>Mau tampil keren tanpa ribet desain? Jaket corduroy kami hadir sebagai pilihan terbaik untuk kamu yang ingin langsung pakai tanpa harus menunggu proses custom. Dibuat dari bahan berkualitas dan jahitan rapi, jaket ini cocok untuk dipakai sehari-hari, nongkrong, kuliah, hingga hangout!</p><br><br>\r\n\r\n<h3>Kenapa Pilih Jaket Ini?</h3>\r\n<ul>\r\n  <li>✅ Desain kekinian & siap pakai</li>\r\n  <li>✅ Gratis Ongkir ke seluruh Indonesia</li>\r\n  <li>✅ Jahitan rapi dan kuat</li>\r\n  <li>✅ Nyaman dipakai di segala cuaca</li>\r\n  <li>✅ Bisa dijadikan hadiah atau koleksi pribadi</li>\r\n</ul><br><br>\r\n\r\n<h3>📦 Tersedia dalam berbagai ukuran (S, M, L, XL)<br>\r\n🚚 Gratis Ongkir langsung ke rumah kamu!<br>\r\n💬Pesan sekarang dan rasakan kenyamanan jaket berkualitas tinggi!</h3>', 'produk_images/pvnNkI1wyEsuRGsJY8ptIy7x2o0azSZjx3qOqOQI.jpg', '2025-05-27 05:30:46', '2025-06-20 05:55:34');

-- --------------------------------------------------------

--
-- Table structure for table `produk_bahan`
--

CREATE TABLE `produk_bahan` (
  `id` bigint UNSIGNED NOT NULL,
  `produk_id` bigint UNSIGNED NOT NULL,
  `bahan_id` bigint UNSIGNED NOT NULL,
  `jumlah` decimal(10,2) NOT NULL,
  `harga` decimal(15,2) NOT NULL,
  `sub_total` decimal(15,2) GENERATED ALWAYS AS ((`jumlah` * `harga`)) STORED,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `produk_bahan`
--

INSERT INTO `produk_bahan` (`id`, `produk_id`, `bahan_id`, `jumlah`, `harga`, `created_at`, `updated_at`) VALUES
(101, 5, 8, '1.00', '10000.00', '2025-06-04 00:33:49', '2025-06-04 00:33:49'),
(102, 5, 2, '1.00', '10000.00', '2025-06-04 00:33:49', '2025-06-04 00:33:49'),
(104, 3, 1, '2.00', '20000.00', '2025-06-05 04:46:27', '2025-06-05 04:46:27'),
(105, 3, 2, '1.00', '10000.00', '2025-06-05 04:46:27', '2025-06-05 04:46:27'),
(106, 3, 3, '1.00', '7000.00', '2025-06-05 04:46:27', '2025-06-05 04:46:27'),
(107, 3, 4, '2.00', '25000.00', '2025-06-05 04:46:27', '2025-06-05 04:46:27');

-- --------------------------------------------------------

--
-- Table structure for table `produk_gambar`
--

CREATE TABLE `produk_gambar` (
  `id` bigint UNSIGNED NOT NULL,
  `produk_id` bigint UNSIGNED NOT NULL,
  `gambar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `produk_gambar`
--

INSERT INTO `produk_gambar` (`id`, `produk_id`, `gambar`, `created_at`, `updated_at`) VALUES
(40, 3, 'produk_gambar/ZkZ0cqH3i5ASvlZ1VbWgAv0P8S7I5jQDE34OUKHA.png', '2025-06-05 04:46:27', '2025-06-05 04:46:27'),
(41, 3, 'produk_gambar/WYNF2aV0Lpz8PZRNPM1rr5MvMvBrCzE2r3ZFv5rm.png', '2025-06-05 04:46:27', '2025-06-05 04:46:27'),
(42, 3, 'produk_gambar/DjiHooTWLlkwWvdgY4HBXrEfOrVmHrZk1EY8b40O.png', '2025-06-05 04:46:27', '2025-06-05 04:46:27');

-- --------------------------------------------------------

--
-- Table structure for table `produk_ukuran`
--

CREATE TABLE `produk_ukuran` (
  `id` bigint UNSIGNED NOT NULL,
  `produk_id` bigint UNSIGNED NOT NULL,
  `warna_id` bigint UNSIGNED NOT NULL,
  `ukuran` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stok` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `produk_ukuran`
--

INSERT INTO `produk_ukuran` (`id`, `produk_id`, `warna_id`, `ukuran`, `stok`, `created_at`, `updated_at`) VALUES
(9, 3, 5, 'Xl', 30, '2025-05-11 13:32:56', '2025-05-15 01:44:05'),
(10, 3, 5, 'L', 0, '2025-05-11 13:32:56', '2025-06-05 04:46:27'),
(11, 3, 6, 'L', 48, '2025-05-14 23:54:15', '2025-06-08 23:36:23'),
(16, 5, 9, 'All Size', 5, '2025-05-27 05:30:46', '2025-06-04 00:33:49'),
(17, 5, 10, 'All Size', 7, '2025-05-27 05:30:46', '2025-06-20 05:55:34'),
(18, 5, 11, 'All Size', 10, '2025-05-27 05:30:46', '2025-06-08 23:36:23');

-- --------------------------------------------------------

--
-- Table structure for table `produk_warna`
--

CREATE TABLE `produk_warna` (
  `id` bigint UNSIGNED NOT NULL,
  `produk_id` bigint UNSIGNED NOT NULL,
  `warna` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `produk_warna`
--

INSERT INTO `produk_warna` (`id`, `produk_id`, `warna`, `created_at`, `updated_at`) VALUES
(5, 3, '#000000', '2025-05-11 13:32:56', '2025-05-14 23:54:48'),
(6, 3, '#2875D0', '2025-05-14 23:54:15', '2025-05-26 13:13:20'),
(9, 5, '#0b1f2a', '2025-05-27 05:30:46', '2025-05-27 05:30:46'),
(10, 5, '#ff0033', '2025-05-27 05:30:46', '2025-05-27 05:30:46'),
(11, 5, '#4B5320', '2025-05-27 05:30:46', '2025-05-27 05:30:46');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `nama`, `created_at`, `updated_at`) VALUES
(1, 'Manager', '2025-03-07 11:34:29', '2025-04-26 02:01:44'),
(3, 'Kasir', '2025-03-10 13:13:37', '2025-05-11 02:54:12'),
(4, 'Admin Ecommerce', '2025-03-10 13:26:01', '2025-04-26 02:21:13'),
(7, 'Produksi', '2025-05-26 13:16:21', '2025-05-26 13:16:21');

-- --------------------------------------------------------

--
-- Table structure for table `template`
--

CREATE TABLE `template` (
  `id` bigint UNSIGNED NOT NULL,
  `model` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `kategori` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `harga_estimasi` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `template`
--

INSERT INTO `template` (`id`, `model`, `deskripsi`, `kategori`, `harga_estimasi`, `created_at`, `updated_at`) VALUES
(3, 'Jacket Coach', '<h2>Jaket Custom – Bebas Desain, Gratis Bordir atau Sablon!</h2>\r\n<p>Ingin tampil beda dengan jaket yang mencerminkan gaya dan identitasmu sendiri? Kami hadir untuk mewujudkannya!<br>\r\nBuat jaket custom sesuai keinginanmu, baik untuk pribadi, komunitas, sekolah, maupun kebutuhan bisnis. \r\n<strong>Dapatkan bordir atau sablon secara GRATIS tanpa biaya tambahan!</strong></p><br><br>\r\n\r\n<h3>Kenapa Pilih Jaket Custom Kami?</h3>\r\n<ul>\r\n  <li>✅ Desain sesuai keinginan (logo, tulisan, gambar bebas)</li>\r\n  <li>✅ GRATIS bordir atau sablon (4 sisi)</li>\r\n  <li>✅ Kualitas bahan premium dan nyaman dipakai</li>\r\n  <li>✅ Proses cepat dan rapi</li>\r\n  <li>✅ Cocok untuk event, hadiah, atau seragam komunitas</li>\r\n</ul><br><br>\r\n\r\n<h3>Cara Pemesanan</h3>\r\n<ol>\r\n  <li>Pilih model jaket yang kamu suka (hoodie, bomber, varsity, dll).</li>\r\n  <li>Kirimkan desain atau logo yang ingin dicetak/bordir.</li>\r\n  <li>Tentukan ukuran dan jumlah pesanan.</li>\r\n  <li>Kami akan mengerjakan dan mengirimkan jaket custom kamu dalam waktu singkat!</li>\r\n</ol><br><br>\r\n\r\n<h3>📦 <strong>Minimal order: 15 pcs – Bisa custom satuan!</strong><br>\r\n💬 <strong>Hubungi kami sekarang untuk konsultasi dan desain GRATIS!</strong><h3><br><br>', 'Jaket', '246600.00', '2025-05-26 23:59:39', '2025-05-27 00:16:34'),
(4, 'Kaos', '<h2>Jaket Custom – Bebas Desain, Gratis Bordir atau Sablon!</h2>\r\n<p>Ingin tampil beda dengan jaket yang mencerminkan gaya dan identitasmu sendiri? Kami hadir untuk mewujudkannya!<br>\r\nBuat jaket custom sesuai keinginanmu, baik untuk pribadi, komunitas, sekolah, maupun kebutuhan bisnis. \r\n<strong>Dapatkan bordir atau sablon secara GRATIS tanpa biaya tambahan!</strong></p><br><br>\r\n\r\n<h3>Kenapa Pilih Kaos Kami?</h3>\r\n<ul>\r\n  <li>✅ Desain sesuai keinginan (logo, tulisan, gambar bebas)</li>\r\n  <li>✅ GRATIS bordir atau sablon (4 sisi)</li>\r\n  <li>✅ Kualitas bahan premium dan nyaman dipakai</li>\r\n  <li>✅ Proses cepat dan rapi</li>\r\n  <li>✅ Cocok untuk event, hadiah, atau seragam komunitas</li>\r\n</ul><br><br>\r\n\r\n<h3>Cara Pemesanan</h3>\r\n<ol>\r\n  <li>Pilih model Kaos yang kamu suka (hoodie, bomber, varsity, dll).</li>\r\n  <li>Kirimkan desain atau logo yang ingin dicetak/bordir.</li>\r\n  <li>Tentukan ukuran dan jumlah pesanan.</li>\r\n  <li>Kami akan mengerjakan dan mengirimkan jaket custom kamu dalam waktu singkat!</li>\r\n</ol><br><br>\r\n\r\n<h3>📦 <strong>Minimal order: 15 pcs – Bisa custom satuan!</strong><br>\r\n💬 <strong>Hubungi kami sekarang untuk konsultasi dan desain GRATIS!</strong><h3><br><br>', 'Baju', '100800.00', '2025-05-27 06:00:12', '2025-05-27 06:00:12');

-- --------------------------------------------------------

--
-- Table structure for table `template_detail`
--

CREATE TABLE `template_detail` (
  `id` bigint UNSIGNED NOT NULL,
  `template_id` bigint UNSIGNED NOT NULL,
  `bahan_id` bigint UNSIGNED NOT NULL,
  `jumlah` decimal(10,2) NOT NULL,
  `harga` decimal(10,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `template_detail`
--

INSERT INTO `template_detail` (`id`, `template_id`, `bahan_id`, `jumlah`, `harga`, `subtotal`, `created_at`, `updated_at`) VALUES
(38, 3, 6, '2.00', '35000.00', '70000.00', '2025-05-27 00:16:34', '2025-05-27 00:16:34'),
(39, 3, 7, '2.00', '25000.00', '50000.00', '2025-05-27 00:16:34', '2025-05-27 00:16:34'),
(40, 3, 2, '1.00', '10000.00', '10000.00', '2025-05-27 00:16:34', '2025-05-27 00:16:34'),
(41, 3, 3, '1.00', '7000.00', '7000.00', '2025-05-27 00:16:34', '2025-05-27 00:16:34'),
(42, 4, 9, '2.00', '23000.00', '46000.00', '2025-05-27 06:00:12', '2025-05-27 06:00:12'),
(43, 4, 2, '1.00', '10000.00', '10000.00', '2025-05-27 06:00:12', '2025-05-27 06:00:12');

-- --------------------------------------------------------

--
-- Table structure for table `template_gambar`
--

CREATE TABLE `template_gambar` (
  `id` bigint UNSIGNED NOT NULL,
  `template_id` bigint UNSIGNED NOT NULL,
  `gambar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `template_gambar`
--

INSERT INTO `template_gambar` (`id`, `template_id`, `gambar`, `created_at`, `updated_at`) VALUES
(5, 3, 'template/lKSXxPIx2G033ku1pC56zdBLxDDw0MsvXLRveYVT.jpg', '2025-05-26 23:59:39', '2025-05-26 23:59:39'),
(6, 3, 'template/JD6fIGXjg05fVEZI5SrtgpTqHj8j5qZmA1xwh86J.png', '2025-05-27 00:02:28', '2025-05-27 00:02:28'),
(7, 3, 'template/8RjF5haTmMYbDRFkJx1no5IkEKFEqejiW98vMWhd.png', '2025-05-27 00:02:28', '2025-05-27 00:02:28'),
(8, 3, 'template/HXjJouAAP2rqxv5oPvkwIv5WpspILKNovrRMVmm7.png', '2025-05-27 00:02:28', '2025-05-27 00:02:28'),
(9, 3, 'template/KJPqhvsjf2T4VPHhkaqG0K8reEhB2ltLpAsYKFw4.png', '2025-05-27 00:02:28', '2025-05-27 00:02:28'),
(10, 4, 'template/nE4dtnDjYkA843Read4jlkZRCdz3yhI4m0KIlMoc.jpg', '2025-05-27 06:00:12', '2025-05-27 06:00:12'),
(11, 4, 'template/TsKP3aa3YJVgkJbnoZea7kx4DVoJD8z5Zz2AU7fh.jpg', '2025-05-27 06:00:12', '2025-05-27 06:00:12');

-- --------------------------------------------------------

--
-- Table structure for table `template_warna`
--

CREATE TABLE `template_warna` (
  `id` bigint UNSIGNED NOT NULL,
  `template_id` bigint UNSIGNED NOT NULL,
  `warna` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `template_warna`
--

INSERT INTO `template_warna` (`id`, `template_id`, `warna`, `created_at`, `updated_at`) VALUES
(23, 3, '#000080', '2025-05-27 00:16:34', '2025-05-27 00:16:34'),
(24, 3, '#000000', '2025-05-27 00:16:34', '2025-05-27 00:16:34'),
(25, 4, '#800000', '2025-05-27 06:00:12', '2025-05-27 06:00:12'),
(26, 4, '#800020', '2025-05-27 06:00:12', '2025-05-27 06:00:12'),
(27, 4, '#80800', '2025-05-27 06:00:12', '2025-05-27 06:00:12'),
(28, 4, '#50C878', '2025-05-27 06:00:12', '2025-05-27 06:00:12');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `img` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `nama`, `email`, `password`, `img`, `created_at`, `updated_at`) VALUES
(3, 1, 'julian arwansah', 'julianarwansahh@gmail.com', '$2y$12$IvAjofK.TAglIzIIL/3MheKll5/H7lYmFnazhqxLleWEkXtunL2Ey', '1748519549.jpg', '2025-03-21 12:19:37', '2025-05-29 04:52:29'),
(4, 4, 'ecomerce', 'ecomerce@gmail.com', '$2y$12$xuFjMD1n.cmudNyYUPpeyOvmwU/GfooRBy25asZJSVcCwvmFd6z1y', NULL, '2025-03-22 01:39:54', '2025-05-26 13:19:21'),
(5, 3, 'kasir', 'kasir@gmail.com', '$2y$12$UJvzpJabbZ3vjecDUDmdzej72qcQlBaKM7oRrQvJMiilpSzoHp92q', NULL, '2025-04-18 06:07:13', '2025-05-26 13:18:58'),
(8, 7, 'Produksi', 'produksi@gmail.com', '$2y$12$0gg8yh9RwggfJOaXWytpgewNLMyIpZxeSyM4HjbdJ8bRtm7esT262', NULL, '2025-05-26 13:16:47', '2025-05-26 13:16:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `antrian`
--
ALTER TABLE `antrian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `antrian_custom_id_foreign` (`custom_id`),
  ADD KEY `antrian_tanggal_urutan_index` (`tanggal`);

--
-- Indexes for table `bahan`
--
ALTER TABLE `bahan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cart_produk_id_foreign` (`produk_id`),
  ADD KEY `cart_warna_id_foreign` (`warna_id`),
  ADD KEY `cart_ukuran_id_foreign` (`ukuran_id`),
  ADD KEY `cart_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `custom`
--
ALTER TABLE `custom`
  ADD PRIMARY KEY (`id`),
  ADD KEY `custom_pesanan_id_foreign` (`pesanan_id`),
  ADD KEY `custom_customer_id_foreign` (`customer_id`),
  ADD KEY `custom_template_id_foreign` (`template_id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `keuangan`
--
ALTER TABLE `keuangan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `keuangan_tanggal_unique` (`tanggal`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pemasukan`
--
ALTER TABLE `pemasukan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pemasukan_user_id_foreign` (`user_id`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pembayaran_pesanan_id_foreign` (`pesanan_id`);

--
-- Indexes for table `pengeluaran`
--
ALTER TABLE `pengeluaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengeluaran_user_id_foreign` (`user_id`);

--
-- Indexes for table `pengiriman`
--
ALTER TABLE `pengiriman`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengiriman_pesanan_id_foreign` (`pesanan_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pesanan_customer_id_foreign` (`customer_id`),
  ADD KEY `pesanan_pembayaran_id_foreign` (`pembayaran_id`);

--
-- Indexes for table `pesanan_detail`
--
ALTER TABLE `pesanan_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pesanan_detail_pesanan_id_foreign` (`pesanan_id`),
  ADD KEY `pesanan_detail_produk_id_foreign` (`produk_id`),
  ADD KEY `pesanan_detail_custom_id_foreign` (`custom_id`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `produk_bahan`
--
ALTER TABLE `produk_bahan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produk_bahan_produk_id_foreign` (`produk_id`),
  ADD KEY `produk_bahan_bahan_id_foreign` (`bahan_id`);

--
-- Indexes for table `produk_gambar`
--
ALTER TABLE `produk_gambar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produk_gambar_produk_id_foreign` (`produk_id`);

--
-- Indexes for table `produk_ukuran`
--
ALTER TABLE `produk_ukuran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ukuran_produk_id_foreign` (`produk_id`),
  ADD KEY `ukuran_warna_id_foreign` (`warna_id`);

--
-- Indexes for table `produk_warna`
--
ALTER TABLE `produk_warna`
  ADD PRIMARY KEY (`id`),
  ADD KEY `warna_produk_id_foreign` (`produk_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `template`
--
ALTER TABLE `template`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `template_detail`
--
ALTER TABLE `template_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `template_detail_template_id_foreign` (`template_id`),
  ADD KEY `template_detail_bahan_id_foreign` (`bahan_id`);

--
-- Indexes for table `template_gambar`
--
ALTER TABLE `template_gambar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `template_gambar_template_id_foreign` (`template_id`);

--
-- Indexes for table `template_warna`
--
ALTER TABLE `template_warna`
  ADD PRIMARY KEY (`id`),
  ADD KEY `template_warna_template_id_foreign` (`template_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users_role_id_foreign` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `antrian`
--
ALTER TABLE `antrian`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `bahan`
--
ALTER TABLE `bahan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `custom`
--
ALTER TABLE `custom`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `keuangan`
--
ALTER TABLE `keuangan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pemasukan`
--
ALTER TABLE `pemasukan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `pengeluaran`
--
ALTER TABLE `pengeluaran`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pengiriman`
--
ALTER TABLE `pengiriman`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT for table `pesanan_detail`
--
ALTER TABLE `pesanan_detail`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `produk_bahan`
--
ALTER TABLE `produk_bahan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `produk_gambar`
--
ALTER TABLE `produk_gambar`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `produk_ukuran`
--
ALTER TABLE `produk_ukuran`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `produk_warna`
--
ALTER TABLE `produk_warna`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `template`
--
ALTER TABLE `template`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `template_detail`
--
ALTER TABLE `template_detail`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `template_gambar`
--
ALTER TABLE `template_gambar`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `template_warna`
--
ALTER TABLE `template_warna`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `antrian`
--
ALTER TABLE `antrian`
  ADD CONSTRAINT `antrian_custom_id_foreign` FOREIGN KEY (`custom_id`) REFERENCES `custom` (`id`);

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ukuran_id_foreign` FOREIGN KEY (`ukuran_id`) REFERENCES `produk_ukuran` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `cart_warna_id_foreign` FOREIGN KEY (`warna_id`) REFERENCES `produk_warna` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `custom`
--
ALTER TABLE `custom`
  ADD CONSTRAINT `custom_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`),
  ADD CONSTRAINT `custom_pesanan_id_foreign` FOREIGN KEY (`pesanan_id`) REFERENCES `pesanan` (`id`),
  ADD CONSTRAINT `custom_template_id_foreign` FOREIGN KEY (`template_id`) REFERENCES `template` (`id`);

--
-- Constraints for table `pemasukan`
--
ALTER TABLE `pemasukan`
  ADD CONSTRAINT `pemasukan_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_pesanan_id_foreign` FOREIGN KEY (`pesanan_id`) REFERENCES `pesanan` (`id`);

--
-- Constraints for table `pengeluaran`
--
ALTER TABLE `pengeluaran`
  ADD CONSTRAINT `pengeluaran_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `pengiriman`
--
ALTER TABLE `pengiriman`
  ADD CONSTRAINT `pengiriman_pesanan_id_foreign` FOREIGN KEY (`pesanan_id`) REFERENCES `pesanan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`),
  ADD CONSTRAINT `pesanan_pembayaran_id_foreign` FOREIGN KEY (`pembayaran_id`) REFERENCES `pembayaran` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `pesanan_detail`
--
ALTER TABLE `pesanan_detail`
  ADD CONSTRAINT `pesanan_detail_custom_id_foreign` FOREIGN KEY (`custom_id`) REFERENCES `custom` (`id`),
  ADD CONSTRAINT `pesanan_detail_pesanan_id_foreign` FOREIGN KEY (`pesanan_id`) REFERENCES `pesanan` (`id`),
  ADD CONSTRAINT `pesanan_detail_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`);

--
-- Constraints for table `produk_bahan`
--
ALTER TABLE `produk_bahan`
  ADD CONSTRAINT `produk_bahan_bahan_id_foreign` FOREIGN KEY (`bahan_id`) REFERENCES `bahan` (`id`),
  ADD CONSTRAINT `produk_bahan_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`);

--
-- Constraints for table `produk_gambar`
--
ALTER TABLE `produk_gambar`
  ADD CONSTRAINT `produk_gambar_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`);

--
-- Constraints for table `produk_ukuran`
--
ALTER TABLE `produk_ukuran`
  ADD CONSTRAINT `ukuran_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`),
  ADD CONSTRAINT `ukuran_warna_id_foreign` FOREIGN KEY (`warna_id`) REFERENCES `produk_warna` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `produk_warna`
--
ALTER TABLE `produk_warna`
  ADD CONSTRAINT `warna_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`);

--
-- Constraints for table `template_detail`
--
ALTER TABLE `template_detail`
  ADD CONSTRAINT `template_detail_bahan_id_foreign` FOREIGN KEY (`bahan_id`) REFERENCES `bahan` (`id`),
  ADD CONSTRAINT `template_detail_template_id_foreign` FOREIGN KEY (`template_id`) REFERENCES `template` (`id`);

--
-- Constraints for table `template_gambar`
--
ALTER TABLE `template_gambar`
  ADD CONSTRAINT `template_gambar_template_id_foreign` FOREIGN KEY (`template_id`) REFERENCES `template` (`id`);

--
-- Constraints for table `template_warna`
--
ALTER TABLE `template_warna`
  ADD CONSTRAINT `template_warna_template_id_foreign` FOREIGN KEY (`template_id`) REFERENCES `template` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
