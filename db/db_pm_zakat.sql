-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 29, 2022 at 03:18 AM
-- Server version: 10.4.20-MariaDB
-- PHP Version: 8.0.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_pm_zakat`
--

-- --------------------------------------------------------

--
-- Table structure for table `administrator`
--

CREATE TABLE `administrator` (
  `id_administrator` int(3) NOT NULL,
  `nama_administrator` varchar(30) NOT NULL,
  `username` varchar(25) NOT NULL,
  `password` varchar(225) NOT NULL,
  `aktif` char(1) NOT NULL DEFAULT 'N',
  `level` varchar(6) NOT NULL,
  `nik_pegawai` bigint(16) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `administrator`
--

INSERT INTO `administrator` (`id_administrator`, `nama_administrator`, `username`, `password`, `aktif`, `level`, `nik_pegawai`) VALUES
(1, 'Administrator', 'admin', '$2y$10$NY2kKQBgx29bPmoP1YXe/eijF24VMm7VU8FE2u0YBkfQ5HgMNOCpy', 'Y', '1', 0),
(6, 'Admin1', 'admin1', '$2y$10$.Mu6PlHJ77S4w9LvVget1ueLcHTR1ebHXs/Wm/oW8U/dXEsGBQCwa', 'Y', '0', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `aspek`
--

CREATE TABLE `aspek` (
  `kode_aspek` varchar(4) NOT NULL,
  `nama_aspek` varchar(50) NOT NULL,
  `bobot` float NOT NULL,
  `bobot_cf` float NOT NULL,
  `bobot_sf` float NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `aspek`
--

INSERT INTO `aspek` (`kode_aspek`, `nama_aspek`, `bobot`, `bobot_cf`, `bobot_sf`) VALUES
('A001', 'Pertamaa', 60, 70, 30),
('A002', 'Sosial', 40, 70, 30);

-- --------------------------------------------------------

--
-- Table structure for table `calon_penerima`
--

CREATE TABLE `calon_penerima` (
  `nik` bigint(20) NOT NULL,
  `nama_pegawai` varchar(40) NOT NULL,
  `tempat_lahir` varchar(40) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `alamat` varchar(100) NOT NULL,
  `pendidikan` varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `calon_penerima`
--

INSERT INTO `calon_penerima` (`nik`, `nama_pegawai`, `tempat_lahir`, `tanggal_lahir`, `jenis_kelamin`, `alamat`, `pendidikan`) VALUES
(6543210123456789, 'Ttt', '', '0000-00-00', 'P', 'jauh', ''),
(123456, 'Asda', '', '0000-00-00', 'L', 'okook', '');

-- --------------------------------------------------------

--
-- Table structure for table `detail_calon`
--

CREATE TABLE `detail_calon` (
  `id_detail` int(5) NOT NULL,
  `id_kandidat` int(5) NOT NULL,
  `kode_faktor` varchar(3) NOT NULL,
  `nilai_faktor` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `faktor`
--

CREATE TABLE `faktor` (
  `kode_faktor` varchar(3) NOT NULL,
  `kode_aspek` varchar(4) NOT NULL,
  `nama_faktor` varchar(50) DEFAULT NULL,
  `jenis_faktor` enum('CF','SF') DEFAULT NULL,
  `nilai_target` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `faktor`
--

INSERT INTO `faktor` (`kode_faktor`, `kode_aspek`, `nama_faktor`, `jenis_faktor`, `nilai_target`) VALUES
('F01', 'A001', 'Penampilan', 'SF', 4),
('F02', 'A001', 'Jumlah Tanggungan', 'CF', 4),
('F03', 'A002', 'Jenis Kendaraan', 'SF', 3);

-- --------------------------------------------------------

--
-- Table structure for table `kandidat`
--

CREATE TABLE `kandidat` (
  `id_kandidat` int(11) NOT NULL,
  `nik` varchar(20) NOT NULL,
  `nilai_akhir` float NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `penerima`
--

CREATE TABLE `penerima` (
  `id_pekerja` int(11) NOT NULL,
  `nik` int(20) NOT NULL,
  `nama_pekerja` varchar(40) NOT NULL,
  `tempat_lahir` varchar(40) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `jenis_kelamin` enum('L','P','','') NOT NULL,
  `alamat` varchar(100) NOT NULL,
  `pendidikan` varchar(10) NOT NULL,
  `tgl_diterima` date DEFAULT NULL,
  `faktor` char(255) DEFAULT NULL,
  `nilai_faktor` char(50) NOT NULL,
  `nilai_akhir` float DEFAULT NULL,
  `kandidat_terima` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `administrator`
--
ALTER TABLE `administrator`
  ADD PRIMARY KEY (`id_administrator`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `administrator`
--
ALTER TABLE `administrator`
  MODIFY `id_administrator` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
