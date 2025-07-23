-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 23, 2025 at 01:13 PM
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
-- Database: `kantin_management1.4`
--

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `id_customer` int(11) NOT NULL,
  `nama_pelanggan` varchar(100) NOT NULL,
  `kontak` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`id_customer`, `nama_pelanggan`, `kontak`) VALUES
(1, 'Rina Marlina', '081234567890'),
(2, 'Agus Setiawan', '082345678901'),
(3, 'Dewi Lestari', '083456789012'),
(4, 'Budi Santoso', '081122334455'),
(5, 'Citra Lestari', '082233445566'),
(6, 'Doni Firmansyah', '083344556677'),
(7, 'Eka Putri', '084455667788'),
(8, 'Fajar Nugraha', '085566778899'),
(9, 'Niar', '085233241244'),
(10, 'Tes Hamba Allah', '089');

-- --------------------------------------------------------

--
-- Table structure for table `customer_voucher`
--

CREATE TABLE `customer_voucher` (
  `id_customer_voucher` int(11) NOT NULL,
  `id_customer` int(11) NOT NULL,
  `id_voucher` int(11) NOT NULL,
  `status_penggunaan` varchar(20) NOT NULL,
  `id_transaksi` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_voucher`
--

INSERT INTO `customer_voucher` (`id_customer_voucher`, `id_customer`, `id_voucher`, `status_penggunaan`, `id_transaksi`) VALUES
(1, 1, 1, 'tersedia', NULL),
(2, 1, 2, 'terpakai', 1);

-- --------------------------------------------------------

--
-- Table structure for table `expense`
--

CREATE TABLE `expense` (
  `id_expense` int(11) NOT NULL,
  `keterangan` varchar(255) NOT NULL,
  `jumlah` decimal(10,2) NOT NULL,
  `tanggal` date NOT NULL,
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id_inventory` int(11) NOT NULL,
  `nama_bahan` varchar(100) NOT NULL,
  `satuan` varchar(20) NOT NULL,
  `stok_saat_ini` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id_inventory`, `nama_bahan`, `satuan`, `stok_saat_ini`) VALUES
(1, 'Nasi Putih', 'porsi', 50.00),
(2, 'Ayam Potong', 'pcs', 30.00),
(3, 'Telur Ayam', 'butir', 100.00),
(4, 'Minyak Goreng', 'liter', 5.00),
(5, 'Bawang Merah', 'kg', 2.00),
(6, 'Teh Celup', 'pcs', 150.00),
(7, 'Gula Pasir', 'kg', 10.00),
(8, 'Bubuk Kopi', 'kg', 3.00),
(9, 'Es Batu', 'kg', 20.00),
(10, 'Mie Telor', 'kg', 10.00),
(11, 'Sayuran (Kol, Sawi)', 'pcs', 15.00),
(12, 'Bumbu Soto Banjar', 'kg', 5.00),
(13, 'Ayam Suwir', 'ons', 20.00),
(14, 'Santan Kelapa', 'liter', 8.00),
(15, 'Soun', 'pak', 30.00),
(16, 'Jeruk Peras', 'kg', 2.00),
(17, 'Kecap Manis', 'pcs', 50.00),
(18, 'Saus Sambal', 'liter', 3.00),
(19, 'Tahu', 'kg', 5.00),
(20, 'Tempe', 'kg', 5.00),
(21, 'Tepung Bumbu', 'liter', 10.00),
(22, 'Daun Bawang & Seledri', 'ikat', 10.00),
(23, 'Bawang Goreng', 'kg', 1.00);

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id_menu` int(11) NOT NULL,
  `nama_menu` varchar(100) NOT NULL,
  `harga_jual` decimal(10,2) NOT NULL,
  `kategori` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id_menu`, `nama_menu`, `harga_jual`, `kategori`) VALUES
(1, 'Nasi Goreng Spesial', 15000.00, 'Makanan Berat'),
(2, 'Ayam Goreng + Nasi', 18000.00, 'Makanan Berat'),
(3, 'Es Teh Manis', 5000.00, 'Minuman'),
(4, 'Kopi Hitam', 6000.00, 'Minuman'),
(5, 'Mie Goreng Telor', 17000.00, 'Makanan Berat'),
(6, 'Soto Banjar', 18000.00, 'Makanan Berat'),
(7, 'Gorengan (3 pcs)', 22000.00, 'Makanan Berat'),
(8, 'Es Jeruk', 20000.00, 'Makanan Berat'),
(9, 'Bakwan Surabaya', 2000.00, 'Makanan Ringan'),
(10, 'Lontong Balap', 15000.00, 'Makanan Berat'),
(11, 'Kopi Susu Gula Aren', 18000.00, 'Minuman'),
(12, 'Americano', 15000.00, 'Minuman'),
(13, 'Caffe Latte', 20000.00, 'Minuman'),
(14, 'Teh Panas', 4000.00, 'Minuman');

-- --------------------------------------------------------

--
-- Table structure for table `recipe`
--

CREATE TABLE `recipe` (
  `id_recipe` int(11) NOT NULL,
  `jumlah_bahan` decimal(10,2) NOT NULL,
  `id_menu` int(11) NOT NULL,
  `id_inventory` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipe`
--

INSERT INTO `recipe` (`id_recipe`, `jumlah_bahan`, `id_menu`, `id_inventory`) VALUES
(24, 1.00, 1, 1),
(25, 1.00, 1, 3),
(26, 0.05, 1, 4),
(27, 0.02, 1, 5),
(28, 0.02, 1, 17),
(29, 0.01, 1, 23),
(30, 1.00, 2, 1),
(31, 1.00, 2, 2),
(32, 0.10, 2, 4),
(33, 1.00, 3, 6),
(34, 0.02, 3, 7),
(35, 0.10, 3, 9),
(36, 0.01, 4, 8),
(37, 0.02, 4, 7),
(38, 0.50, 5, 10),
(39, 1.00, 5, 3),
(40, 0.10, 5, 11),
(41, 0.05, 5, 4),
(42, 0.02, 5, 17),
(43, 0.01, 5, 23),
(44, 1.00, 6, 12),
(45, 0.10, 6, 13),
(46, 2.00, 6, 14),
(47, 0.20, 6, 15),
(48, 0.25, 6, 3),
(49, 0.10, 6, 22),
(50, 0.01, 6, 23),
(51, 1.50, 7, 19),
(52, 0.15, 7, 20),
(53, 0.05, 7, 21),
(54, 0.10, 7, 4),
(55, 0.20, 8, 16),
(56, 0.02, 8, 7),
(57, 0.10, 8, 9);

-- --------------------------------------------------------

--
-- Table structure for table `syslog`
--

CREATE TABLE `syslog` (
  `id_log` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `role` varchar(20) NOT NULL,
  `waktu` datetime NOT NULL DEFAULT current_timestamp(),
  `aktivitas` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `syslog`
--

INSERT INTO `syslog` (`id_log`, `id_user`, `username`, `role`, `waktu`, `aktivitas`) VALUES
(1, 1, 'Administrator', 'administrator', '2025-07-22 13:48:16', 'User berhasil login.'),
(2, 3, 'Baihaqi', 'dapur', '2025-07-22 13:51:46', 'User berhasil login.'),
(3, 27, 'Azhar', 'supervisor', '2025-07-22 13:55:59', 'User berhasil login.'),
(4, 2, 'Aline Buchanan', 'kasir', '2025-07-22 14:09:53', 'User berhasil login.'),
(5, 3, 'Baihaqi', 'dapur', '2025-07-22 14:13:18', 'User berhasil login.'),
(6, 1, 'Administrator', 'administrator', '2025-07-22 14:13:31', 'User berhasil login.'),
(7, 2, 'Aline Buchanan', 'kasir', '2025-07-22 14:13:58', 'User berhasil login.'),
(8, 3, 'Baihaqi', 'dapur', '2025-07-22 14:17:42', 'User berhasil login.'),
(9, 2, 'Aline Buchanan', 'kasir', '2025-07-22 14:17:50', 'User berhasil login.'),
(10, 2, 'Aline Buchanan', 'kasir', '2025-07-23 02:11:48', 'User berhasil login.'),
(11, 1, 'Administrator', 'administrator', '2025-07-23 02:12:25', 'User berhasil login.'),
(12, 2, 'Aline Buchanan', 'kasir', '2025-07-23 18:12:37', 'User berhasil login.'),
(13, 1, 'Administrator', 'administrator', '2025-07-23 18:16:57', 'User berhasil login.'),
(14, 2, 'Aline Buchanan', 'kasir', '2025-07-23 18:18:35', 'User berhasil login.'),
(15, 2, 'Aline Buchanan', 'kasir', '2025-07-23 18:18:49', 'Membuat transaksi baru #6 dengan total Rp 42,000'),
(16, 2, 'Aline Buchanan', 'kasir', '2025-07-23 18:24:33', 'Membuat transaksi baru #7 dengan total Rp 18,000'),
(17, 2, 'Aline Buchanan', 'kasir', '2025-07-23 18:25:19', 'Membuat transaksi baru #8 dengan total Rp 20,000'),
(18, 2, 'Aline Buchanan', 'kasir', '2025-07-23 18:30:09', 'Membuat transaksi baru #9 dengan total Rp 22,000'),
(19, 2, 'Aline Buchanan', 'kasir', '2025-07-23 18:30:28', 'Menandai transaksi #8 telah dilayani (status: selesai).'),
(20, 2, 'Aline Buchanan', 'kasir', '2025-07-23 18:30:52', 'Menandai transaksi #9 telah dilayani (status: selesai).'),
(21, 2, 'Aline Buchanan', 'kasir', '2025-07-23 18:31:18', 'Membuat transaksi baru #10 dengan total Rp 18,000'),
(22, 2, 'Aline Buchanan', 'kasir', '2025-07-23 18:35:45', 'Membuat transaksi baru #11 dengan total Rp 20,000'),
(23, 25, 'Muktasim Daroini', 'supervisor', '2025-07-23 18:36:45', 'User berhasil login.'),
(24, 1, 'Administrator', 'administrator', '2025-07-23 18:40:11', 'User berhasil login.'),
(25, 25, 'Muktasim Daroini', 'supervisor', '2025-07-23 18:44:45', 'User berhasil login.'),
(26, 25, 'Muktasim Daroini', 'supervisor', '2025-07-23 18:45:09', 'Membuat transaksi hutang baru #12 oleh Niar'),
(27, 25, 'Muktasim Daroini', 'supervisor', '2025-07-23 18:45:24', 'Menandai transaksi #12 telah dilayani (status: selesai).'),
(28, 25, 'Muktasim Daroini', 'supervisor', '2025-07-23 18:47:33', 'Membuat transaksi hutang baru #13 oleh Tes Hamba Allah'),
(29, 1, 'Administrator', 'administrator', '2025-07-23 18:47:47', 'User berhasil login.'),
(30, 1, 'Administrator', 'administrator', '2025-07-23 18:47:55', 'Menghapus transaksi #2'),
(31, 1, 'Administrator', 'administrator', '2025-07-23 18:48:10', 'Menghapus transaksi #12'),
(32, 25, 'Muktasim Daroini', 'supervisor', '2025-07-23 18:52:28', 'User berhasil login.');

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `id_transaction` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_customer` int(11) DEFAULT NULL,
  `waktu_transaksi` datetime NOT NULL,
  `total_bayar` decimal(10,2) NOT NULL,
  `metode_pembayaran` varchar(50) NOT NULL,
  `status_pesanan` enum('pending','diproses','selesai','dibatalkan') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction`
--

INSERT INTO `transaction` (`id_transaction`, `id_user`, `id_customer`, `waktu_transaksi`, `total_bayar`, `metode_pembayaran`, `status_pesanan`) VALUES
(1, 2, 4, '2025-07-22 09:15:00', 39000.00, 'Tunai', 'selesai'),
(3, 1, NULL, '2025-07-22 11:05:00', 35000.00, 'QRIS', 'selesai'),
(4, 2, 6, '2025-07-22 12:20:00', 22000.00, 'Tunai', 'selesai'),
(5, 2, 7, '2025-07-22 13:00:00', 20000.00, 'QRIS', 'selesai'),
(6, 2, NULL, '2025-07-23 12:18:49', 42000.00, 'QRIS', 'selesai'),
(7, 2, NULL, '2025-07-23 12:24:33', 18000.00, 'QRIS', 'selesai'),
(8, 2, NULL, '2025-07-23 12:25:19', 20000.00, 'Tunai', 'selesai'),
(9, 2, NULL, '2025-07-23 12:30:09', 22000.00, 'Tunai', 'selesai'),
(10, 2, NULL, '2025-07-23 12:31:18', 18000.00, 'Tunai', 'pending'),
(11, 2, NULL, '2025-07-23 12:35:45', 20000.00, 'Hutang', 'pending'),
(13, 25, 10, '2025-07-23 12:47:33', 15000.00, 'Hutang', 'diproses');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_detail`
--

CREATE TABLE `transaction_detail` (
  `id_detail` int(11) NOT NULL,
  `id_transaction` int(11) NOT NULL,
  `id_menu` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `harga_saat_transaksi` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction_detail`
--

INSERT INTO `transaction_detail` (`id_detail`, `id_transaction`, `id_menu`, `jumlah`, `harga_saat_transaksi`) VALUES
(1, 1, 5, 1, 17000.00),
(2, 1, 12, 1, 15000.00),
(3, 1, 9, 2, 2000.00),
(6, 3, 8, 1, 20000.00),
(7, 3, 10, 1, 15000.00),
(8, 4, 6, 1, 18000.00),
(9, 4, 14, 1, 4000.00),
(10, 5, 13, 1, 20000.00),
(11, 6, 7, 1, 22000.00),
(12, 6, 13, 1, 20000.00),
(13, 7, 2, 1, 18000.00),
(14, 8, 8, 1, 20000.00),
(15, 9, 7, 1, 22000.00),
(16, 10, 6, 1, 18000.00),
(17, 11, 13, 1, 20000.00),
(19, 13, 12, 1, 15000.00);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `nama_user` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `nama_user`, `username`, `password`, `role`) VALUES
(1, 'Administrator', 'administrator', '$2y$10$6168zRn1nkO1it3SHSi.zOuNCV4VuIVoX1j5UVEBnNCqKrLngk4ii', 'administrator'),
(2, 'Kasir01', 'kasir', '$2y$10$y/vJxyE7uSMI9WLk636hu.JDr9t6DXP1Y.LOO0Oqzk/RWkFmvFIGu', 'kasir'),
(3, 'Dapur01', 'dapur', '$2y$10$NAS67eAdrrs29DnuQhGwBOH8..slwDr6I2q8.TNH1aZcslUkbt3xS', 'dapur'),
(25, 'Muktasim Daroini', 'tasim', '$2y$10$gsO/mB1ytvFqzzLM9enSkuPTnLSCDD.cCr4jBqDL74gAHebMOjGRy', 'supervisor'),
(26, 'Lutfi', 'lutfi', '$2y$10$M18nCf0BrwDXfjfjGX48auJum.E0OC3acVcq4mDDSGIlgOPPHW3fC', 'supervisor'),
(27, 'Azhar', 'azhar', '$2y$10$OEZ3Rwjb6UlNIB.iGZeXaeGBD0Yly9wccbUYydgtbPpWGfAN5RKH.', 'supervisor'),
(28, 'Akmaluddin', 'akmal', '$2y$10$4nWm.98bllW9y/7fVKrsTud70bcCggnwmWG94S4V4cDWfwC1aOCMq', 'supervisor'),
(29, 'Baihaqi', 'baihaqi', '$2y$10$YEPYnGLPobEjA1g2YQGpRu.AzcIDuOd1PkT03RMdABFrgo8SKoY8O', 'supervisor'),
(30, 'Aline Buchanan', 'aline', '$2y$10$HuqJGrln2Ttb/VkQgLU/c.fLAVesWGMJ1EDnf4lZl5Ibc7zQGxD42', 'supervisor'),
(31, 'Gesit Satria Kuncoro', 'gesit', '$2y$10$FYG7.BUk4lZoDTZPqHqBU.8jEADGWe3ASaQ8dmjWoIpOJZZsQznHK', 'supervisor'),
(32, 'Suryadi Haryanto', 'surya', '$2y$10$0WHgYDJEFsiFMu2OVgewqeUOjUSXWTXwiUfy4QASRykD0XPLTDiFO', 'supervisor');

-- --------------------------------------------------------

--
-- Table structure for table `voucher`
--

CREATE TABLE `voucher` (
  `id_voucher` int(11) NOT NULL,
  `kode_voucher` varchar(50) NOT NULL,
  `deskripsi` varchar(255) DEFAULT NULL,
  `tipe_diskon` varchar(20) NOT NULL,
  `nilai_diskon` decimal(10,2) NOT NULL,
  `tanggal_tidakberlaku` date DEFAULT NULL,
  `id_user_pembuat` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `voucher`
--

INSERT INTO `voucher` (`id_voucher`, `kode_voucher`, `deskripsi`, `tipe_diskon`, `nilai_diskon`, `tanggal_tidakberlaku`, `id_user_pembuat`) VALUES
(1, 'DISKON10PERSEN', 'Diskon 10% untuk seluruh transaksi', 'persentase', 10.00, '2025-12-31', 1),
(2, 'POTONG5RB', 'Potongan harga langsung Rp 5.000', 'nominal', 5000.00, '2025-10-31', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id_customer`);

--
-- Indexes for table `customer_voucher`
--
ALTER TABLE `customer_voucher`
  ADD PRIMARY KEY (`id_customer_voucher`),
  ADD KEY `id_customer` (`id_customer`),
  ADD KEY `id_voucher` (`id_voucher`),
  ADD KEY `id_transaksi` (`id_transaksi`);

--
-- Indexes for table `expense`
--
ALTER TABLE `expense`
  ADD PRIMARY KEY (`id_expense`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id_inventory`),
  ADD UNIQUE KEY `nama_bahan` (`nama_bahan`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id_menu`),
  ADD UNIQUE KEY `nama_menu` (`nama_menu`);

--
-- Indexes for table `recipe`
--
ALTER TABLE `recipe`
  ADD PRIMARY KEY (`id_recipe`),
  ADD KEY `id_menu` (`id_menu`),
  ADD KEY `id_inventory` (`id_inventory`);

--
-- Indexes for table `syslog`
--
ALTER TABLE `syslog`
  ADD PRIMARY KEY (`id_log`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`id_transaction`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_customer` (`id_customer`);

--
-- Indexes for table `transaction_detail`
--
ALTER TABLE `transaction_detail`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_transaction` (`id_transaction`),
  ADD KEY `id_menu` (`id_menu`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `voucher`
--
ALTER TABLE `voucher`
  ADD PRIMARY KEY (`id_voucher`),
  ADD UNIQUE KEY `kode_voucher` (`kode_voucher`),
  ADD KEY `id_user_pembuat` (`id_user_pembuat`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `id_customer` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `customer_voucher`
--
ALTER TABLE `customer_voucher`
  MODIFY `id_customer_voucher` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `expense`
--
ALTER TABLE `expense`
  MODIFY `id_expense` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id_inventory` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `recipe`
--
ALTER TABLE `recipe`
  MODIFY `id_recipe` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `syslog`
--
ALTER TABLE `syslog`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `transaction`
--
ALTER TABLE `transaction`
  MODIFY `id_transaction` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `transaction_detail`
--
ALTER TABLE `transaction_detail`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `voucher`
--
ALTER TABLE `voucher`
  MODIFY `id_voucher` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `customer_voucher`
--
ALTER TABLE `customer_voucher`
  ADD CONSTRAINT `customer_voucher_ibfk_1` FOREIGN KEY (`id_customer`) REFERENCES `customer` (`id_customer`),
  ADD CONSTRAINT `customer_voucher_ibfk_2` FOREIGN KEY (`id_voucher`) REFERENCES `voucher` (`id_voucher`),
  ADD CONSTRAINT `customer_voucher_ibfk_3` FOREIGN KEY (`id_transaksi`) REFERENCES `transaction` (`id_transaction`);

--
-- Constraints for table `expense`
--
ALTER TABLE `expense`
  ADD CONSTRAINT `expense_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

--
-- Constraints for table `recipe`
--
ALTER TABLE `recipe`
  ADD CONSTRAINT `recipe_ibfk_1` FOREIGN KEY (`id_menu`) REFERENCES `menu` (`id_menu`),
  ADD CONSTRAINT `recipe_ibfk_2` FOREIGN KEY (`id_inventory`) REFERENCES `inventory` (`id_inventory`);

--
-- Constraints for table `transaction`
--
ALTER TABLE `transaction`
  ADD CONSTRAINT `transaction_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`),
  ADD CONSTRAINT `transaction_ibfk_2` FOREIGN KEY (`id_customer`) REFERENCES `customer` (`id_customer`);

--
-- Constraints for table `transaction_detail`
--
ALTER TABLE `transaction_detail`
  ADD CONSTRAINT `transaction_detail_ibfk_1` FOREIGN KEY (`id_transaction`) REFERENCES `transaction` (`id_transaction`),
  ADD CONSTRAINT `transaction_detail_ibfk_2` FOREIGN KEY (`id_menu`) REFERENCES `menu` (`id_menu`);

--
-- Constraints for table `voucher`
--
ALTER TABLE `voucher`
  ADD CONSTRAINT `voucher_ibfk_1` FOREIGN KEY (`id_user_pembuat`) REFERENCES `user` (`id_user`);

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `hapus_log_tua` ON SCHEDULE EVERY 1 DAY STARTS '2025-07-22 13:47:46' ON COMPLETION NOT PRESERVE ENABLE DO DELETE FROM syslog WHERE waktu < NOW() - INTERVAL 3 MONTH$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
