-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mar 24, 2022 at 04:28 PM
-- Server version: 5.6.16
-- PHP Version: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `profile_matching`
--

-- --------------------------------------------------------

--
-- Table structure for table `administrator`
--

CREATE TABLE IF NOT EXISTS `administrator` (
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
(4, 'Hahahhaa', 'inigua', '$2y$10$5u/JBCd4ubU.7lHa3cVzPeZIX.VBw0LUP0h.FAaTWNyWHJTnAlHta', 'Y', '0', 1234567890123456);

-- --------------------------------------------------------

--
-- Table structure for table `aspek`
--

CREATE TABLE IF NOT EXISTS `aspek` (
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
('A001', 'Pertamaa', 100, 60, 40);

-- --------------------------------------------------------

--
-- Table structure for table `detail_kandidat`
--

CREATE TABLE IF NOT EXISTS `detail_kandidat` (
  `id_detail` int(5) NOT NULL,
  `id_kandidat` int(5) NOT NULL,
  `kode_faktor` varchar(3) NOT NULL,
  `nilai_faktor` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `faktor`
--

CREATE TABLE IF NOT EXISTS `faktor` (
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
('F01', 'A001', 'Penampilan', 'SF', 1),
('F02', 'A001', 'Zxs', 'CF', 5);

-- --------------------------------------------------------

--
-- Table structure for table `kandidat`
--

CREATE TABLE IF NOT EXISTS `kandidat` (
  `id_kandidat` int(5) NOT NULL,
  `nik` varchar(20) NOT NULL,
  `nilai_akhir` float NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pegawai`
--

CREATE TABLE IF NOT EXISTS `pegawai` (
  `nik` bigint(20) NOT NULL,
  `nama_pegawai` varchar(40) NOT NULL,
  `tempat_lahir` varchar(40) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `alamat` varchar(100) NOT NULL,
  `pendidikan` varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pegawai`
--

INSERT INTO `pegawai` (`nik`, `nama_pegawai`, `tempat_lahir`, `tanggal_lahir`, `jenis_kelamin`, `alamat`, `pendidikan`) VALUES
(6543210123456789, 'Ttt', 'Wonosobo', '2021-01-14', 'L', 'jauh', 'S1'),
(1234567890123456, 'Asda', 'Asdas', '2020-12-21', 'L', 'okook', 'S1');

-- --------------------------------------------------------

--
-- Table structure for table `pekerja`
--

CREATE TABLE IF NOT EXISTS `pekerja` (
  `id_pekerja` int(11) NOT NULL,
  `nik` int(20) NOT NULL,
  `nama_pekerja` varchar(40) NOT NULL,
  `tempat_lahir` varchar(40) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `jenis_kelamin` enum('L','P','','') NOT NULL,
  `alamat` varchar(100) NOT NULL,
  `pendidikan` varchar(10) NOT NULL,
  `tgl_diterima` date DEFAULT NULL,
  `faktor` varchar(20) DEFAULT NULL,
  `nilai_akhir` float DEFAULT NULL,
  `kandidat_terima` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
