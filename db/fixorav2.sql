-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 29, 2026 at 02:40 AM
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
-- Database: `fixorav2`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang_tersedias`
--

CREATE TABLE `barang_tersedias` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pengajuan_item_id` bigint(20) UNSIGNED NOT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `tahun_perolehan` year(4) NOT NULL,
  `kondisi` varchar(255) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bidangs`
--

CREATE TABLE `bidangs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_bidang` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bidangs`
--

INSERT INTO `bidangs` (`id`, `nama_bidang`, `created_at`, `updated_at`) VALUES
(1, 'perangkat komputer', '2026-10-26 02:57:39', '2026-10-26 02:57:39'),
(2, 'alat kesehatan', '2026-10-26 02:57:45', '2026-10-26 02:57:45'),
(3, 'alat tulis kertas', '2026-10-27 03:06:17', '2026-10-27 03:06:17'),
(4, 'keuangan', '2026-10-28 03:31:35', '2026-10-28 03:31:35');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-dian@rsmz|10.10.9.83', 'i:1;', 1793149416),
('laravel-cache-dian@rsmz|10.10.9.83:timer', 'i:1793149416;', 1793149416);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `detail_barangs`
--

CREATE TABLE `detail_barangs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `request_id` bigint(20) UNSIGNED NOT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `kode_aset` varchar(255) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `spesifikasi` text DEFAULT NULL,
  `alasan` varchar(255) DEFAULT NULL,
  `no_surat` varchar(255) DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `tanggal_acc` date DEFAULT NULL,
  `tanggal_kerusakan` date DEFAULT NULL,
  `tanggal_verif` timestamp NULL DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `karyawans`
--

CREATE TABLE `karyawans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bidang_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nip` varchar(255) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `jabatan` varchar(255) NOT NULL,
  `ruangan` varchar(255) NOT NULL,
  `ttd` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `karyawans`
--

INSERT INTO `karyawans` (`id`, `bidang_id`, `nip`, `nama`, `jabatan`, `ruangan`, `ttd`, `created_at`, `updated_at`) VALUES
(1, NULL, '000', 'Asministrator', 'null', 'admin', NULL, '2026-10-28 02:17:32', '2026-10-28 02:17:32'),
(2, NULL, '001', 'Ahmad Ainur Ridho, S.Kep.Ns', 'Kepala Ruangan', 'RUANG ANGGREK', NULL, '2026-10-28 02:21:03', '2026-10-28 02:21:03'),
(3, NULL, '002', 'Diana Yusnita Syukri', 'Kepala Ruangan', 'RUANG BOUGENVILE', NULL, '2026-10-28 02:21:48', '2026-10-28 02:21:48'),
(4, NULL, '003', 'Siska Okiana', 'Kepala Ruangan', 'RUANG CEMPAKA', NULL, '2026-10-28 02:22:12', '2026-10-28 02:22:12'),
(5, NULL, '004', 'Farida Kristina A', 'Kepala Ruangan', 'RUANG DAHLIA', NULL, '2026-10-28 02:22:40', '2026-10-28 02:22:40'),
(6, NULL, '005', 'Agus Efendi, S.Kep.Ns', 'Kepala Ruangan', 'RUANG FLAMBOYAN', NULL, '2026-10-28 02:31:03', '2026-10-28 02:31:03'),
(7, NULL, '006', 'Juma\'adi, S.Kep.Ns', 'Kepala Ruangan', 'RUANG GRAHA UTAMA', NULL, '2026-10-28 02:31:40', '2026-10-28 02:31:40'),
(8, NULL, '007', 'Moh. Atom Muzaki', 'Kepala Ruangan', 'MCU', NULL, '2026-10-28 02:32:08', '2026-10-28 02:32:08'),
(9, NULL, '008', 'Musnati', 'Kepala Ruangan', 'RUANG KENANGA', NULL, '2026-10-28 02:32:45', '2026-10-28 02:32:45'),
(10, NULL, '009', 'Sumaidah', 'Kepala Ruangan', 'RUANG MAWAR', NULL, '2026-10-28 02:33:21', '2026-10-28 02:33:21'),
(11, NULL, '010', 'Aini Luthfiyah', 'Kepala Ruangan', 'RUANG MELATI', NULL, '2026-10-28 02:33:46', '2026-10-28 02:33:46'),
(12, NULL, '011', 'Moh. Safiudin', 'Kepala Ruangan', 'RUANG TULIP', NULL, '2026-10-28 02:34:13', '2026-10-28 02:34:13'),
(13, NULL, '012', 'Arrizal Amin F', 'Kepala Ruangan', 'RUANG MATAHARI', NULL, '2026-10-28 02:34:39', '2026-10-28 02:34:39'),
(14, NULL, '013', 'L. Makbul Anam', 'Kepala Ruangan', 'RUANG SAKURA', NULL, '2026-10-28 02:35:11', '2026-10-28 02:35:11'),
(15, NULL, '015', 'Sumarsono, S.Kep.Ns', 'Kepala Ruangan', 'RUANG TERATAI', NULL, '2026-10-28 02:35:36', '2026-10-28 02:35:36'),
(16, NULL, '017', 'Siti Roihatul Wardiyah', 'Kepala Ruangan', 'IGD', NULL, '2026-10-28 02:36:01', '2026-10-28 02:36:01'),
(17, NULL, '018', 'Faridatus Saleha', 'Kepala Ruangan', 'INSTALASI HEMODIALISA', NULL, '2026-10-28 02:36:50', '2026-10-28 02:36:50'),
(18, NULL, '019', 'Hoiri', 'Kepala Ruangan', 'INSTALASI BEDAH SENTRAL (IBS)', NULL, '2026-10-28 02:37:10', '2026-10-28 02:37:10'),
(19, NULL, '020', 'Lukman Fauzi', 'Kepala Ruangan', 'Recovery Room (RR)', NULL, '2026-10-28 02:37:27', '2026-10-28 02:37:27'),
(20, NULL, '021', 'Laboratorium', 'Kepala Ruangan', 'LABORATORIUM', NULL, '2026-10-28 02:38:01', '2026-10-28 02:38:01'),
(21, NULL, '022', 'Ayu Nurjannah', 'Kepala Ruangan', 'FARMASI', NULL, '2026-10-28 02:38:53', '2026-10-28 02:38:53'),
(22, NULL, '023', 'Abdurrachem', 'Kepala Ruangan', 'RADIOLOGI', NULL, '2026-10-28 02:39:30', '2026-10-28 02:39:30'),
(23, NULL, '032', 'Khusnul Khotimah, S.Kep.Ns', 'Kepala Ruangan', 'POLI RAWAT JALAN', NULL, '2026-10-28 02:42:55', '2026-10-28 02:42:55'),
(24, NULL, '031', 'Anitha Carolina', 'Kepala Ruangan', 'GIZI', NULL, '2026-10-28 02:43:18', '2026-10-28 02:43:18'),
(25, NULL, '030', 'Andy Andrian Hasan, S.E', 'Kepala Ruangan', 'DIREKTUR', NULL, '2026-10-28 02:43:40', '2026-10-28 02:43:40'),
(26, NULL, '029', 'Nurul Hotimah', 'Kepala Bidang', 'TATA USAHA', NULL, '2026-10-28 02:44:32', '2026-10-28 02:46:18'),
(27, 2, '1', 'Siti Hunainah,. M.Kes', 'Kepala Ruangan', 'PENUNJANG', NULL, '2026-10-28 02:45:04', '2026-10-28 03:23:52'),
(28, 2, '2', 'dr. Siti Hotimah, Sp. PK', 'Kepala Bidang', 'RUANG WADIR', NULL, '2026-10-28 02:50:01', '2026-10-28 03:25:51'),
(29, 4, '4', 'Yenny Rosagita, Se. Ak, MM', 'Kepala Bidang', 'KEUANGAN', NULL, '2026-10-28 02:53:24', '2026-10-28 06:47:41'),
(30, 1, '5', 'dr. Manarul Aini', 'Kepala Bidang', 'MANAJEMEN', NULL, '2026-10-28 02:54:16', '2026-10-28 03:21:10'),
(31, NULL, '6', 'Dr. Felix Michael S', 'Koordinator', 'REKAM MEDIS', NULL, '2026-10-28 02:59:53', '2026-10-28 02:59:53'),
(32, NULL, '7', 'Mas\'udi Prihanto', 'Kepala Ruangan', 'INSTALASI PEMELIHARAAN SARANA', NULL, '2026-10-28 03:00:58', '2026-10-28 03:00:58'),
(33, NULL, '8', 'Abd. Wahid', 'Koordinator', 'KEUANGAN', NULL, '2026-10-28 03:02:22', '2026-10-28 03:02:22'),
(34, NULL, '9', 'drg. Hendry Eko Wahyudi, Sp.KG', 'Koordinator', 'MANAJEMEN', NULL, '2026-10-28 03:03:56', '2026-10-28 03:03:56'),
(35, NULL, '10', 'Salman Farisi', 'Koordinator', 'MANAJEMEN', NULL, '2026-10-28 03:04:11', '2026-10-28 03:04:11'),
(36, NULL, '11', 'Amin Jakfar S', 'Koordinator', 'MANAJEMEN', NULL, '2026-10-28 03:04:36', '2026-10-28 03:04:36'),
(37, NULL, '12', 'Slamet Riyadi, S.Kep.Ns', 'Koordinator', 'TATA USAHA', NULL, '2026-10-28 03:04:57', '2026-10-28 03:04:57'),
(38, NULL, '13', 'Ika Tutrik Oktavia', 'Koordinator', 'MANAJEMEN', NULL, '2026-10-28 03:05:19', '2026-10-28 03:05:19'),
(39, NULL, '144', 'R.Dian Tri M', 'Koordinator', 'KEUANGAN', NULL, '2026-10-28 03:05:46', '2026-10-28 04:01:29'),
(40, 1, '15', 'Riza Rihardina', 'Koordinator', 'SIMRS', NULL, '2026-10-28 03:06:15', '2026-10-28 06:39:01'),
(41, NULL, '16', 'Denny Setyo W', 'Koordinator', 'MANAJEMEN', NULL, '2026-10-28 03:06:51', '2026-10-28 03:06:51'),
(42, NULL, '56', 'dr.Bhakti S T, M. Kes', 'Wakil Direktur', 'RUANG WADIR', NULL, '2026-10-28 03:28:30', '2026-10-28 03:28:30'),
(43, NULL, '59', 'drg. DEWI KUSUMAWATI', 'Wakil Direktur', 'RUANG WADIR', NULL, '2026-10-28 03:28:45', '2026-10-28 03:28:45');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2026_10_06_092509_add_no_surat_to_pengajuans_table', 1),
(2, '2026_10_06_092509_add_no_surat_to_pengajuanssdsdsds_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengadaan_barangs`
--

CREATE TABLE `pengadaan_barangs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `dasar_usulan` varchar(255) NOT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `spesifikasi_teknis` varchar(255) DEFAULT NULL,
  `satuan` text DEFAULT NULL,
  `perkiraanharga` varchar(255) DEFAULT NULL,
  `total_harga` varchar(255) DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `tanggal_acc` date DEFAULT NULL,
  `tanggal_kerusakan` date DEFAULT NULL,
  `tanggal_verif` timestamp NULL DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengajuans`
--

CREATE TABLE `pengajuans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `karyawan_id` bigint(20) UNSIGNED NOT NULL,
  `bidang_id` bigint(20) UNSIGNED DEFAULT NULL,
  `no_pengajuan` varchar(255) NOT NULL,
  `tanggal_pengajuan` date NOT NULL,
  `tahun_anggaran` year(4) DEFAULT NULL,
  `instalasi` varchar(255) NOT NULL,
  `dasar_usulan` varchar(255) DEFAULT NULL,
  `log_status_penerima` varchar(255) DEFAULT NULL,
  `log_status_atasan` varchar(255) DEFAULT NULL,
  `log_status_keuangan` varchar(255) DEFAULT NULL,
  `log_status_direktur` varchar(255) DEFAULT NULL,
  `alasan_justifikasi` text DEFAULT NULL,
  `manfaat` text DEFAULT NULL,
  `dampak` text DEFAULT NULL,
  `kondisi_barang_lama` text DEFAULT NULL,
  `ket_barang_lama` text DEFAULT NULL,
  `foto_barang` varchar(255) DEFAULT NULL,
  `penawaran_harga` varchar(255) DEFAULT NULL,
  `data_kerusakan` varchar(255) DEFAULT NULL,
  `barjas` varchar(255) DEFAULT NULL,
  `barajas_lainnya` varchar(255) DEFAULT NULL,
  `dasar_usulan_lainnya` varchar(255) DEFAULT NULL,
  `penerima_id` bigint(20) UNSIGNED DEFAULT NULL,
  `atasan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `id_keuangan` bigint(20) UNSIGNED DEFAULT NULL,
  `direktur_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ppk_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kpa_id` bigint(20) UNSIGNED DEFAULT NULL,
  `total_pengajuan` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_disetujui` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_disetujui_direktur` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `id_acc_kabidPelayanan` bigint(20) UNSIGNED DEFAULT NULL,
  `id_acc_kabidPenunjang` bigint(20) UNSIGNED DEFAULT NULL,
  `diterima_at` timestamp NULL DEFAULT NULL,
  `disetujui_kabid_at` timestamp NULL DEFAULT NULL,
  `disetujui_keuangan_at` timestamp NULL DEFAULT NULL,
  `disetujui_direktur_at` timestamp NULL DEFAULT NULL,
  `catatan_unit` text DEFAULT NULL,
  `catatan_bidang` text DEFAULT NULL,
  `catatan_perencanaan` text DEFAULT NULL,
  `catatan_ipsrs` text DEFAULT NULL,
  `catatan_farmasi` text DEFAULT NULL,
  `catatan_keuangan` text DEFAULT NULL,
  `catatan_direktur` text DEFAULT NULL,
  `alasan_direktur` text DEFAULT NULL,
  `analisis_kebutuhan` varchar(255) DEFAULT NULL,
  `data_pengguna` varchar(255) DEFAULT NULL,
  `spesifikasi_teknis` varchar(255) DEFAULT NULL,
  `dokumen_pendukung_lainnya` varchar(255) DEFAULT NULL,
  `tanggal_survei` date DEFAULT NULL,
  `catatan_survei` text DEFAULT NULL,
  `prioritas` text DEFAULT NULL,
  `log_ppk` varchar(255) DEFAULT NULL,
  `ppk_at` timestamp NULL DEFAULT NULL,
  `catatan_ppk` varchar(255) DEFAULT NULL,
  `log_kpa` varchar(255) DEFAULT NULL,
  `catatan_kpa` varchar(255) DEFAULT NULL,
  `kpa` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `kategori` varchar(255) DEFAULT NULL,
  `acc_kabidPelayanan_at` timestamp NULL DEFAULT NULL,
  `acc_kabidPenunjang_at` timestamp NULL DEFAULT NULL,
  `log_acc_kabidPelayanan` varchar(255) DEFAULT NULL,
  `log_acc_kabidPenunjang` varchar(255) DEFAULT NULL,
  `selesai_at` timestamp NULL DEFAULT NULL,
  `no_surat` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengajuans`
--

INSERT INTO `pengajuans` (`id`, `karyawan_id`, `bidang_id`, `no_pengajuan`, `tanggal_pengajuan`, `tahun_anggaran`, `instalasi`, `dasar_usulan`, `log_status_penerima`, `log_status_atasan`, `log_status_keuangan`, `log_status_direktur`, `alasan_justifikasi`, `manfaat`, `dampak`, `kondisi_barang_lama`, `ket_barang_lama`, `foto_barang`, `penawaran_harga`, `data_kerusakan`, `barjas`, `barajas_lainnya`, `dasar_usulan_lainnya`, `penerima_id`, `atasan_id`, `id_keuangan`, `direktur_id`, `ppk_id`, `kpa_id`, `total_pengajuan`, `total_disetujui`, `total_disetujui_direktur`, `status`, `id_acc_kabidPelayanan`, `id_acc_kabidPenunjang`, `diterima_at`, `disetujui_kabid_at`, `disetujui_keuangan_at`, `disetujui_direktur_at`, `catatan_unit`, `catatan_bidang`, `catatan_perencanaan`, `catatan_ipsrs`, `catatan_farmasi`, `catatan_keuangan`, `catatan_direktur`, `alasan_direktur`, `analisis_kebutuhan`, `data_pengguna`, `spesifikasi_teknis`, `dokumen_pendukung_lainnya`, `tanggal_survei`, `catatan_survei`, `prioritas`, `log_ppk`, `ppk_at`, `catatan_ppk`, `log_kpa`, `catatan_kpa`, `kpa`, `created_at`, `updated_at`, `kategori`, `acc_kabidPelayanan_at`, `acc_kabidPenunjang_at`, `log_acc_kabidPelayanan`, `log_acc_kabidPenunjang`, `selesai_at`, `no_surat`) VALUES
(1, 39, 2, 'PGD-20261028-4376', '2026-10-28', '2026', 'KEUANGAN', 'Penambahan Kapasitas Pelayanan', NULL, 'disetujui_kabid', NULL, NULL, 'dasdad', 'sadasdasdsa', 'asdadasd', 'Rusak Ringan', 'sadasdas', NULL, NULL, NULL, 'barang', NULL, NULL, NULL, 27, NULL, NULL, 27, NULL, 1000000.00, 0.00, 0.00, 'menunggu_keuangan', 28, 27, NULL, '2026-10-28 05:52:47', NULL, NULL, NULL, 'dadqadsqwdqwdqwadw', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Disetujui oleh PPK', '2026-10-28 05:53:16', 'asdadsadsad', NULL, NULL, NULL, '2026-10-28 04:25:08', '2026-10-28 05:53:16', 'pengadaan', '2026-10-28 05:48:32', '2026-10-28 05:52:47', 'disetujui oleh kabid pelayanan', 'disetujui oleh kabid penunjang', NULL, NULL),
(2, 39, 2, 'PGD-20261028-2880', '2026-10-28', '2026', 'KEUANGAN', 'Program Kerja', 'disetujui_koordinator', 'disetujui_kabid', 'disetujui_keuangan', NULL, 'asdasdasdas', 'asdsadasdas', 'dsadasdasd', 'Rusak berat', 'asdasdas', NULL, NULL, NULL, 'barang', NULL, NULL, 28, 27, 29, NULL, 27, 43, 1000000.00, 1000000.00, 1000000.00, 'menunggu_direktur', 28, 27, '2026-10-28 04:52:47', '2026-10-28 05:58:00', '2026-10-28 06:07:46', NULL, 'sdsadsad', 'wrgdsgsfslhf lsfhjlsfhjslfhslkfhsl;f hsd', NULL, NULL, NULL, 'asdasdasdsa', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Disetujui oleh PPK', '2026-10-28 05:59:30', 'kjkljklkljkjkljljlkjkl', 'Disetujui oleh KPA', 'sadasdasd', '2026-10-28 06:08:02', '2026-10-28 04:27:51', '2026-10-28 06:08:02', 'pengadaan', '2026-10-28 05:57:26', '2026-10-28 05:58:00', 'disetujui oleh kabid pelayanan', 'disetujui oleh kabid penunjang', NULL, NULL),
(3, 39, 3, 'PGD-20261028-1752', '2026-10-28', '2026', 'KEUANGAN', 'Penambahan Kapasitas Pelayanan', NULL, 'disetujui_kabid', 'disetujui_keuangan', 'disetujui', 'asdadsad', 'adsadasdsa', 'adasdsad', 'Rusak Ringan', 'sadasdasd', NULL, NULL, NULL, 'barang', NULL, NULL, 40, 29, 29, 25, 27, 43, 5000000.00, 5000000.00, 5000000.00, 'disetujui', NULL, 29, '2026-10-28 04:38:57', '2026-10-28 05:57:08', '2026-10-28 06:00:16', '2026-10-28 06:07:07', 'asdadadasdasd', 'asdadasdsad', NULL, NULL, NULL, 'zDaSDasxaSXA', 'sadasdas', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Disetujui oleh PPK', '2026-10-28 05:59:48', 'dasdasdxasdxsaxd', 'Disetujui oleh KPA', 'asdsadasd', '2026-10-28 06:04:34', '2026-10-28 04:28:29', '2026-10-28 06:07:07', 'pengadaan', NULL, '2026-10-28 05:57:08', NULL, 'disetujui oleh kabid penunjang', NULL, '445/1/434.203.100.02/2026');

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan_items`
--

CREATE TABLE `pengajuan_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pengajuan_id` bigint(20) UNSIGNED NOT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `spesifikasi` text DEFAULT NULL,
  `dokumen_upload` varchar(255) DEFAULT NULL,
  `satuan` varchar(255) DEFAULT NULL,
  `jumlah` int(11) NOT NULL,
  `harga` bigint(20) UNSIGNED DEFAULT NULL,
  `jumlah_disetujui` int(10) UNSIGNED DEFAULT NULL,
  `harga_disetujui` bigint(20) UNSIGNED DEFAULT NULL,
  `harga_satuan` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `kepemilikan` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengajuan_items`
--

INSERT INTO `pengajuan_items` (`id`, `pengajuan_id`, `nama_barang`, `spesifikasi`, `dokumen_upload`, `satuan`, `jumlah`, `harga`, `jumlah_disetujui`, `harga_disetujui`, `harga_satuan`, `created_at`, `updated_at`, `kepemilikan`) VALUES
(1, 1, 'sadasd', 'dsadasdasdsad', NULL, 'Unit', 1, 1000000, 1, NULL, 1000000.00, '2026-10-28 04:25:08', '2026-10-28 04:25:08', 'ila'),
(2, 2, 'dasdsadasd', 'asdasdasd', NULL, 'Buah', 1, 1000000, 1, NULL, 1000000.00, '2026-10-28 04:27:51', '2026-10-28 04:27:51', 'sd'),
(3, 2, 'asdsadsadsa', 'asdasdas', NULL, 'Unit', 10, 5000000, 10, NULL, 500000.00, '2026-10-28 04:28:29', '2026-10-28 04:28:29', 'sd');

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bidang_id` bigint(20) UNSIGNED DEFAULT NULL,
  `penerima_id` bigint(20) UNSIGNED DEFAULT NULL,
  `atasan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `karyawan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `request_type` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `ruangan` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('B0CWsnXXGrBA4YaOG2p9Jg9KqSr8dt8XXQ5xUoIv', 7, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVDJZc0NJNDFjN0xLWXhickxWR3ZTN1FFMlhaamprWmJjRzgxTkQxRyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7czo5OiJkYXNoYm9hcmQiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo3O30=', 1793169462),
('iRMzsbWSNVES7G8L2l0Y5oEFWIP2wiJo54RB3BL5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiN0U5dEM5Y3l6dlRxaUhPN2NnVzlXejVFQldZTmYySTJOUmdWbkFJbCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fX0=', 1793170156),
('qOgc0QWDlzhN5dDpQqKkdKM9H9mjdU7LtaizX3lU', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:158.0) Gecko/20100101 Firefox/158.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiVzlGcHR4TXF2ZDJzbG9tUHhkM1RZbUNqQWZvOWowcGRKejQ5eUZueCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7czo5OiJkYXNoYm9hcmQiO31zOjM6InVybCI7YToxOntzOjg6ImludGVuZGVkIjtzOjM5OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvbXVsdGlyb2xlMy9hdGFzYW4iO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTozO30=', 1793170027),
('vzGHA80RB5dTV7tgMkKeUXroaKOlsQEjdnqOjMNo', 10, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoid1pCSGl4ZXVveDV2SjBzb3F4SkVZYnVuOERNSXNaaHFMNlBPWWp6MyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9rZXVhbmdhbiI7czo1OiJyb3V0ZSI7czoxODoia2V1YW5nYW4ucGVuZ2FkYWFuIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTA7fQ==', 1793170062);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `karyawan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `role2` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `subrole` varchar(255) DEFAULT NULL,
  `kategori` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `karyawan_id`, `email`, `email_verified_at`, `password`, `role`, `role2`, `is_active`, `remember_token`, `created_at`, `updated_at`, `subrole`, `kategori`) VALUES
(1, 'Admin RSMZ', 1, 'admin@rsmz.com', '2026-10-28 02:17:32', '$2y$12$ORZFIzRGtpCCeQtHtNp1gu4h.oVB1slGzp8SoyNQ35apHPdbFqmyu', 'admin', NULL, 1, NULL, '2026-10-28 02:17:33', '2026-10-28 02:17:33', NULL, NULL),
(2, 'R.Dian Tri M', 39, 'dian@rsmz.com', NULL, '$2y$12$mRUZ4nrDOdGJHRKjqAcij.IfRfKhfENriZ8oaNGpkdXROD7Yw4Keq', 'pemohon', NULL, 1, NULL, '2026-10-28 03:18:06', '2026-10-28 03:57:41', NULL, NULL),
(3, 'Riza Rihardina', 40, 'riza@rsmz.com', NULL, '$2y$12$Hoq39HNgUWq9hNaRAz8U6eYVGnnvawyptZNbwy0ydbrnUfluD2ACe', 'penerima', NULL, 1, NULL, '2026-10-28 03:18:32', '2026-10-28 03:18:32', NULL, NULL),
(5, 'dr. Manarul Aini', 30, 'drmanarul@rsmz.com', NULL, '$2y$12$TIhPKriITli5vW0RksVhbOZqEKQpkDeWLEOSPpig56JBOFcNT6WYC', 'atasan', NULL, 1, NULL, '2026-10-28 03:21:10', '2026-10-28 03:21:10', NULL, NULL),
(6, 'Siti Hunainah,. M.Kes', 27, 'hunai@rsmz.com', NULL, '$2y$12$sJ/N3V67VyE/1u1X9BesIufJGpte5ISKLffidGugjKnf5KTnJfFXW', 'atasan', 'ppk', 1, NULL, '2026-10-28 03:23:52', '2026-10-28 03:23:52', 'kabid_penunjang', NULL),
(7, 'dr. Siti Hotimah, Sp. PK', 28, 'drhotim@rsmz.com', NULL, '$2y$12$bFyRMAV1hMt1DxRPs4bcLeai9rKzDgWmOPktGxLp1uu/4l2SfJu4e', 'atasan', 'penerima', 1, NULL, '2026-10-28 03:25:51', '2026-10-28 03:25:51', 'kabid_pelayanan', NULL),
(8, 'dr.Bhakti S T, M. Kes', 42, 'drbhakti@rsmz.com', NULL, '$2y$12$UCxPMGjmuia9bVGZ/lxxDufv09hUxQ0Eqd3MpS.Mwlzm9pYxt5rFG', 'ppk', NULL, 1, NULL, '2026-10-28 03:29:09', '2026-10-28 03:29:09', NULL, NULL),
(9, 'drg. DEWI KUSUMAWATI', 43, 'drgdewi@rsmz.com', NULL, '$2y$12$GVuk8gYxnWuBFaGz5Xb4runZTEKrrJAl7QqXNc00Yq5sbD80ne/1u', 'kpa', NULL, 1, NULL, '2026-10-28 03:29:51', '2026-10-28 03:29:51', NULL, NULL),
(10, 'Yenny Rosagita, Se. Ak, MM', 29, 'yenny@rsmz.com', NULL, '$2y$12$PcbnLDZlSf5XMj0SuOmu/Oq4RiiBsyhqWFkKdrZoeU9OoTKVVpMEy', 'atasan', 'keuangan', 1, NULL, '2026-10-28 03:32:12', '2026-10-28 03:32:12', NULL, NULL),
(11, 'Andy Andrian Hasan, S.E', 25, 'andy@rsmz.com', NULL, '$2y$12$Bk5kzevQdOmqaXuVCGOa4OQOFrpr5DhHyQxu951tugcO.Gsz2/JLu', 'direktur', NULL, 1, NULL, '2026-10-28 06:05:57', '2026-10-28 06:05:57', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang_tersedias`
--
ALTER TABLE `barang_tersedias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `barang_tersedias_pengajuan_item_id_foreign` (`pengajuan_item_id`);

--
-- Indexes for table `bidangs`
--
ALTER TABLE `bidangs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `detail_barangs`
--
ALTER TABLE `detail_barangs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `detail_barangs_request_id_foreign` (`request_id`);

--
-- Indexes for table `karyawans`
--
ALTER TABLE `karyawans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `karyawans_nip_unique` (`nip`),
  ADD KEY `karyawans_bidang_id_foreign` (`bidang_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `pengadaan_barangs`
--
ALTER TABLE `pengadaan_barangs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengajuans`
--
ALTER TABLE `pengajuans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pengajuans_no_pengajuan_unique` (`no_pengajuan`),
  ADD KEY `pengajuans_karyawan_id_foreign` (`karyawan_id`),
  ADD KEY `pengajuans_bidang_id_foreign` (`bidang_id`),
  ADD KEY `pengajuans_penerima_id_foreign` (`penerima_id`),
  ADD KEY `pengajuans_atasan_id_foreign` (`atasan_id`),
  ADD KEY `pengajuans_id_keuangan_foreign` (`id_keuangan`),
  ADD KEY `pengajuans_direktur_id_foreign` (`direktur_id`),
  ADD KEY `pengajuans_ppk_id_foreign` (`ppk_id`),
  ADD KEY `pengajuans_kpa_id_foreign` (`kpa_id`),
  ADD KEY `pengajuans_id_acc_kabidpelayanan_foreign` (`id_acc_kabidPelayanan`),
  ADD KEY `pengajuans_id_acc_kabidpenunjang_foreign` (`id_acc_kabidPenunjang`);

--
-- Indexes for table `pengajuan_items`
--
ALTER TABLE `pengajuan_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengajuan_items_pengajuan_id_foreign` (`pengajuan_id`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requests_bidang_id_foreign` (`bidang_id`),
  ADD KEY `requests_penerima_id_foreign` (`penerima_id`),
  ADD KEY `requests_atasan_id_foreign` (`atasan_id`),
  ADD KEY `requests_karyawan_id_foreign` (`karyawan_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_karyawan_id_foreign` (`karyawan_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barang_tersedias`
--
ALTER TABLE `barang_tersedias`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bidangs`
--
ALTER TABLE `bidangs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `detail_barangs`
--
ALTER TABLE `detail_barangs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `karyawans`
--
ALTER TABLE `karyawans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pengadaan_barangs`
--
ALTER TABLE `pengadaan_barangs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengajuans`
--
ALTER TABLE `pengajuans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pengajuan_items`
--
ALTER TABLE `pengajuan_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `barang_tersedias`
--
ALTER TABLE `barang_tersedias`
  ADD CONSTRAINT `barang_tersedias_pengajuan_item_id_foreign` FOREIGN KEY (`pengajuan_item_id`) REFERENCES `pengajuan_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `detail_barangs`
--
ALTER TABLE `detail_barangs`
  ADD CONSTRAINT `detail_barangs_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `karyawans`
--
ALTER TABLE `karyawans`
  ADD CONSTRAINT `karyawans_bidang_id_foreign` FOREIGN KEY (`bidang_id`) REFERENCES `bidangs` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `pengajuans`
--
ALTER TABLE `pengajuans`
  ADD CONSTRAINT `pengajuans_atasan_id_foreign` FOREIGN KEY (`atasan_id`) REFERENCES `karyawans` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pengajuans_bidang_id_foreign` FOREIGN KEY (`bidang_id`) REFERENCES `bidangs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pengajuans_direktur_id_foreign` FOREIGN KEY (`direktur_id`) REFERENCES `karyawans` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pengajuans_id_acc_kabidpelayanan_foreign` FOREIGN KEY (`id_acc_kabidPelayanan`) REFERENCES `karyawans` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `pengajuans_id_acc_kabidpenunjang_foreign` FOREIGN KEY (`id_acc_kabidPenunjang`) REFERENCES `karyawans` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `pengajuans_id_keuangan_foreign` FOREIGN KEY (`id_keuangan`) REFERENCES `karyawans` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pengajuans_karyawan_id_foreign` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawans` (`id`),
  ADD CONSTRAINT `pengajuans_kpa_id_foreign` FOREIGN KEY (`kpa_id`) REFERENCES `karyawans` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pengajuans_penerima_id_foreign` FOREIGN KEY (`penerima_id`) REFERENCES `karyawans` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pengajuans_ppk_id_foreign` FOREIGN KEY (`ppk_id`) REFERENCES `karyawans` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `pengajuan_items`
--
ALTER TABLE `pengajuan_items`
  ADD CONSTRAINT `pengajuan_items_pengajuan_id_foreign` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `requests_atasan_id_foreign` FOREIGN KEY (`atasan_id`) REFERENCES `karyawans` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `requests_bidang_id_foreign` FOREIGN KEY (`bidang_id`) REFERENCES `bidangs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `requests_karyawan_id_foreign` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawans` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `requests_penerima_id_foreign` FOREIGN KEY (`penerima_id`) REFERENCES `karyawans` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_karyawan_id_foreign` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawans` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
