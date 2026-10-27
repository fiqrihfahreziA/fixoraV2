-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 22, 2026 at 03:38 AM
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

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, NULL, '000', 'Asministrator', 'null', 'admin', NULL, '2026-10-15 04:42:32', '2026-10-15 04:42:32'),
(2, 1, '001', 'NAFAS FANDORA NN', 'Kepala Ruangan', 'RUANG BOUGENVILE', '1792039593_1791250988_1785724054_ani ratmono.png', '2026-10-15 04:46:33', '2026-10-16 02:14:04'),
(3, 2, '002', 'riza rahardina', 'Kepala Ruangan', 'SIMRS', '1792039629_1791251058_1785978350_siti julaeha.png', '2026-10-15 04:47:09', '2026-10-16 00:51:20'),
(4, 1, '003', 'lee min', 'Kepala Bidang', 'MANAJEMEN', '1792039683_1791250926_1785723948_ach wahyudi.png', '2026-10-15 04:48:03', '2026-10-16 02:15:16'),
(5, NULL, '006', 'doni muhidin', 'Kepala Ruangan', 'RUANG BOUGENVILE', '1792039956_1791250926_1785723948_ach wahyudi.png', '2026-10-15 04:52:36', '2026-10-16 03:27:45'),
(6, NULL, '008', 'elena', 'Wakil Direktur', 'MANAJEMEN', '1792117085_1787792465_Diyah Kesling.png', '2026-10-16 02:18:05', '2026-10-16 02:18:05'),
(7, NULL, '009', 'dito', 'Kepala Bidang', 'KEUANGAN', '1792117106_1785723948_ach wahyudi.png', '2026-10-16 02:18:26', '2026-10-16 02:28:52'),
(8, NULL, '010', 'Denandra', 'Administrator', 'DIREKTUR', '1792117304_1792039683_1791250926_1785723948_ach wahyudi.png', '2026-10-16 02:21:44', '2026-10-16 02:24:23'),
(9, NULL, '0022', 'kevin', 'Kepala Ruangan', 'MANAJEMEN', '1792118034_1791251058_1785978350_siti julaeha.png', '2026-10-16 02:33:54', '2026-10-16 02:34:16'),
(10, 1, '0086', 'OBBADI, TN', 'Kepala Ruangan', 'FLAMBOYAN', '1792121048_1785724054_ani ratmono.png', '2026-10-16 03:24:08', '2026-10-16 03:25:02'),
(11, 2, '0658', 'dr bagyo', 'Kepala Bidang', 'MANAJEMEN', '1792369922_indah daniati.png', '2026-10-19 00:32:02', '2026-10-19 00:32:26');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `selesai_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, 'Admin RSMZ', 1, 'admin@rsmz.com', '2026-10-22 01:34:48', '$2y$12$Sy/VtyrMp.ONtnJQM4DKQO.1TSNczg3FZcPZIK6LWwVRFRWosL0Bm', 'admin', NULL, 1, NULL, '2026-10-22 01:34:49', '2026-10-22 01:34:49', NULL, NULL);

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `detail_barangs`
--
ALTER TABLE `detail_barangs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `karyawans`
--
ALTER TABLE `karyawans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengadaan_barangs`
--
ALTER TABLE `pengadaan_barangs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengajuans`
--
ALTER TABLE `pengajuans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengajuan_items`
--
ALTER TABLE `pengajuan_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
