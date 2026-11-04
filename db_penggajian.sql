-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 04, 2026 at 10:37 AM
-- Server version: 8.0.30
-- PHP Version: 8.5.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_penggajian`
--

-- --------------------------------------------------------

--
-- Table structure for table `karyawan`
--

CREATE TABLE `karyawan` (
  `id_karyawan` int NOT NULL,
  `nik` varchar(20) NOT NULL,
  `nama_karyawan` varchar(50) NOT NULL,
  `jabatan` varchar(30) NOT NULL,
  `gaji_pokok` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `karyawan`
--

INSERT INTO `karyawan` (`id_karyawan`, `nik`, `nama_karyawan`, `jabatan`, `gaji_pokok`) VALUES
(1, 'EMP001', 'Budi Santoso', 'Software Engineer', 30000000),
(2, 'EMP002', 'Siti Aminah', 'UI/UX Designer', 14800000),
(3, 'EMP003', 'Ahmad Dani', 'Admin Staff', 16100000),
(4, 'EMP004', 'Muhammad Afgan Akhtar', 'Staff IT', 16500000),
(5, 'EMP005', 'Affan Dwi Putra', 'Staff Keuangan', 17000000),
(6, 'EMP006', 'Rebowa Miftahurrahman', 'Staff Operasional', 15000000),
(7, 'EMP007', 'Radhiyya Bariq Kautsar', 'Staff Marketing', 25800000);

-- --------------------------------------------------------

--
-- Table structure for table `penggajian`
--

CREATE TABLE `penggajian` (
  `id_gaji` int NOT NULL,
  `id_karyawan` int NOT NULL,
  `bulan` varchar(20) NOT NULL,
  `tahun` varchar(4) NOT NULL,
  `tunjangan` int DEFAULT '0',
  `potongan` int DEFAULT '0',
  `total_gaji` int NOT NULL,
  `tanggal_bayar` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `penggajian`
--

INSERT INTO `penggajian` (`id_gaji`, `id_karyawan`, `bulan`, `tahun`, `tunjangan`, `potongan`, `total_gaji`, `tanggal_bayar`) VALUES
(1, 7, 'November', '2026', 5000000, 1000000, 29800000, '2026-11-04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`id_karyawan`);

--
-- Indexes for table `penggajian`
--
ALTER TABLE `penggajian`
  ADD PRIMARY KEY (`id_gaji`),
  ADD KEY `id_karyawan` (`id_karyawan`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `id_karyawan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `penggajian`
--
ALTER TABLE `penggajian`
  MODIFY `id_gaji` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `penggajian`
--
ALTER TABLE `penggajian`
  ADD CONSTRAINT `penggajian_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
