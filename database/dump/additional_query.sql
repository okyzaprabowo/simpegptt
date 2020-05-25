-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 25 Bulan Mei 2020 pada 12.20
-- Versi server: 10.4.11-MariaDB
-- Versi PHP: 7.4.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sipegptt`
--

-- --------------------------------------------------------

ALTER TABLE `pegawai` ADD `is_enable` BOOLEAN NOT NULL DEFAULT TRUE AFTER `foto`;

--
-- Struktur dari tabel `waktu_absen`
--

CREATE TABLE `waktu_absen` (
  `jam_masuk_mulai_scan` int(11) DEFAULT NULL,
  `jam_masuk_akhir_scan` int(11) DEFAULT NULL,
  `jam_keluar_mulai_scan` int(11) DEFAULT NULL,
  `jam_keluar_akhir_scan` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `waktu_absen`
--

INSERT INTO `waktu_absen` (`jam_masuk_mulai_scan`, `jam_masuk_akhir_scan`, `jam_keluar_mulai_scan`, `jam_keluar_akhir_scan`) VALUES
(0, 0, 0, 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
