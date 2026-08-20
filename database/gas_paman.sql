-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 21 Agu 2026 pada 09.40
-- Versi server: 10.11.14-MariaDB-0ubuntu0.24.04.1
-- Versi PHP: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gas_paman`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `bank_soal`
--

CREATE TABLE `bank_soal` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `jenis` enum('pre_test','post_test') NOT NULL,
  `tanggal` date DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'nonaktif',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `bank_soal`
--

INSERT INTO `bank_soal` (`id`, `judul`, `deskripsi`, `jenis`, `tanggal`, `status`, `created_by`, `created_at`) VALUES
(31, 'pre test', 'Diimport otomatis dari link soal — 15/08/2026 19:41', 'pre_test', '2026-08-15', 'aktif', 6, '2026-08-15 11:41:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `catatan_files`
--

CREATE TABLE `catatan_files` (
  `id` int(11) NOT NULL,
  `catatan_id` int(11) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `catatan_files`
--

INSERT INTO `catatan_files` (`id`, `catatan_id`, `file_path`, `created_at`) VALUES
(60, 87, 'b44f9d1d7211d243dd18.jpg', '2026-08-12 08:58:03'),
(61, 87, '25f41b64cee3094c1864.jpg', '2026-08-12 08:58:03'),
(62, 88, '32baa213b7cfa5ee12d9.jpg', '2026-08-12 09:01:51'),
(63, 88, '32e4f153ea9cd25f8189.jpg', '2026-08-12 09:01:51'),
(64, 89, '10f72077239a795e8486.jpg', '2026-08-12 09:03:25'),
(65, 89, '9620fdf8b129c55b6433.jpg', '2026-08-12 09:03:25'),
(66, 90, '4d29b13f0d92ad74ce1a.jpg', '2026-08-12 09:05:06'),
(67, 90, 'e141f67b53b2186fceca.jpg', '2026-08-12 09:05:06'),
(68, 91, '20875fe861faa1bfa214.jpg', '2026-08-12 09:07:03'),
(69, 91, '45c0a943d6eb3a6e2750.jpg', '2026-08-12 09:07:03'),
(70, 92, '998b7925c576fb2e9d1f.HEIC', '2026-08-13 05:24:22'),
(71, 93, '78b675675b646474aef3.HEIC', '2026-08-13 05:27:30'),
(72, 94, '77229f4256f3d579b7b3.HEIC', '2026-08-13 05:30:37'),
(73, 95, 'fc09fcb443433b298a02.HEIC', '2026-08-13 05:33:13'),
(74, 96, 'bd442e243cfb8bb4ced7.HEIC', '2026-08-13 05:36:21'),
(75, 97, '73628ebabb056a4eaacd.jpeg', '2026-08-13 23:40:36'),
(76, 98, 'd7294832aafeabecc2e5.jpeg', '2026-08-13 23:48:02'),
(77, 99, 'c04250fdf16e4a453b06.jpeg', '2026-08-13 23:58:27'),
(78, 100, '48df87dcd97252267e39.jpeg', '2026-08-14 00:04:19'),
(79, 101, 'f87480faa52c2f05381c.jpeg', '2026-08-14 00:12:33'),
(80, 102, '354b063d8644584cec91.jpeg', '2026-08-14 05:01:22'),
(81, 103, '3fa029b9d91a75c3d14c.jpeg', '2026-08-14 05:03:17'),
(82, 104, '8e53de6e6a32c971b4f9.jpeg', '2026-08-14 05:04:41'),
(83, 105, '90a99b3597e21f72feaa.jpeg', '2026-08-14 05:05:37'),
(84, 106, '406f5389a3683d738301.jpeg', '2026-08-14 05:07:28'),
(85, 107, '9b61c77409f5d4519ab9.jpg', '2026-08-14 05:10:49'),
(86, 107, '9e2ec00d389f8b28219c.jpg', '2026-08-14 05:10:49'),
(87, 107, 'ea34dd0d7935579e10f0.jpg', '2026-08-14 05:10:49'),
(88, 108, 'f11573437d01c1f5bf40.jpeg', '2026-08-14 05:10:51'),
(89, 109, '65adb6ccaaac6836b027.jpg', '2026-08-14 05:12:55'),
(90, 109, 'b1123ef7d93a6acad032.jpg', '2026-08-14 05:12:55'),
(91, 109, '1ea74abbaac4791468ab.jpg', '2026-08-14 05:12:55'),
(92, 110, 'd92f3a7136d82d95fbf2.jpeg', '2026-08-14 05:16:07'),
(93, 111, '9cb6982167bd5b12d251.jpg', '2026-08-14 05:16:31'),
(94, 111, 'b9fef2b0d8998a424f89.jpg', '2026-08-14 05:16:31'),
(95, 111, '8a52b10f2278ddecf889.jpg', '2026-08-14 05:16:31'),
(96, 112, '0975a5c79580e21b5af7.jpeg', '2026-08-14 05:19:40'),
(97, 113, 'b1e4f791896fef7411fd.jpeg', '2026-08-14 05:22:31'),
(98, 114, 'e5b9e0741fa80c0bbd72.jpeg', '2026-08-14 05:25:26'),
(99, 115, '10c87b3ed2bfc52c7665.jpg', '2026-08-14 05:28:03'),
(100, 115, '1b742d99b773463fd010.jpg', '2026-08-14 05:28:03'),
(101, 115, '36697bd04f1e742f3782.jpg', '2026-08-14 05:28:03'),
(102, 116, '38566f3b560260f5b8cc.jpg', '2026-08-14 05:29:13'),
(103, 116, 'e7ef93753851c6d4d4a0.jpg', '2026-08-14 05:29:13'),
(104, 116, '1ec4b351670f7c863684.jpg', '2026-08-14 05:29:13'),
(105, 117, 'bd2fe5681a0cfc19cc8a.jpg', '2026-08-14 05:33:51'),
(106, 117, 'e1d3a7128879cfdcc9eb.jpg', '2026-08-14 05:33:51'),
(107, 117, '4a2b88feaad9af8501f0.jpg', '2026-08-14 05:33:51'),
(108, 118, '29537c48c4db4f8cffcb.jpg', '2026-08-14 05:35:02'),
(109, 118, 'c0db50d2ff043707a2c0.jpg', '2026-08-14 05:35:02'),
(110, 118, '3454da7f5d2eaa26f3ab.jpg', '2026-08-14 05:35:02'),
(111, 119, '1df5aff7167bc6f3c203.jpg', '2026-08-14 05:39:18'),
(112, 119, '9efb6362f4032e96f23c.jpg', '2026-08-14 05:39:18'),
(113, 119, '310b9d04bdf62dcf6c9d.jpg', '2026-08-14 05:39:18'),
(114, 120, '4789e027b47de284b242.jpg', '2026-08-14 05:40:17'),
(115, 120, 'c795bcc2b81d131078f9.jpg', '2026-08-14 05:40:17'),
(116, 120, '430b10b11244c6185786.jpg', '2026-08-14 05:40:17'),
(117, 121, 'bb4beb4c95ce85b89bb7.jpg', '2026-08-14 05:42:14'),
(118, 121, '79c92ca363e6fe4169e1.jpg', '2026-08-14 05:42:14'),
(119, 121, '5247b61ef541ceef8091.jpg', '2026-08-14 05:42:14'),
(120, 122, '27108816b05e87aa3ad8.jpg', '2026-08-14 05:45:50'),
(121, 122, '78f4b202ec4220b88626.jpg', '2026-08-14 05:45:50'),
(122, 122, 'f41597e340398796baf1.jpg', '2026-08-14 05:45:50'),
(123, 123, 'ec77c7aae7dbab179252.jpg', '2026-08-14 05:48:28'),
(124, 123, '2bcf2b45fc1c99c7080a.jpg', '2026-08-14 05:48:28'),
(125, 123, 'a2db3eb4868df37c3fd7.jpg', '2026-08-14 05:48:28'),
(126, 124, '0115ea35bc02ea0fed44.jpg', '2026-08-14 06:50:34'),
(127, 125, 'c18698079662515f3222.jpg', '2026-08-14 06:58:04'),
(128, 126, '43323b44ceeabe841bf3.jpg', '2026-08-14 07:09:15'),
(129, 127, '7d2aef7828c7f8662b55.jpg', '2026-08-14 07:12:15'),
(130, 128, 'e531f05a02d0392ed886.HEIC', '2026-08-14 07:17:24'),
(131, 129, 'd0e725fb8a5476c807e1.jpg', '2026-08-14 07:18:49'),
(132, 130, '56bee0d57144a8db4cf2.HEIC', '2026-08-14 07:20:23'),
(133, 131, 'eff49e9983dd237119b0.HEIC', '2026-08-14 07:24:04'),
(134, 132, '3142b7c4062f2f784f68.jpg', '2026-08-14 07:24:25'),
(135, 133, '7106ac110e3c234345ed.HEIC', '2026-08-14 07:29:42'),
(136, 134, 'ff8ffd783b5fc30d538a.jpg', '2026-08-14 07:30:41'),
(137, 135, 'a73ff6689adf0db10dd8.HEIC', '2026-08-14 07:31:54'),
(138, 136, '65d4e106d7396f67f625.jpg', '2026-08-14 07:42:10'),
(139, 137, '9337f15da659eccf8653.jpg', '2026-08-14 07:47:00'),
(140, 138, '6aaf50c18e969916de78.jpg', '2026-08-14 07:53:53'),
(141, 139, 'c08a9681e4ecd3ad9398.HEIC', '2026-08-14 08:41:56'),
(142, 139, '2e3b2cab52312b051824.HEIC', '2026-08-14 08:41:56'),
(143, 139, '16af717bebc75767e674.HEIC', '2026-08-14 08:41:56'),
(144, 140, 'e68ef5fba506607d336e.HEIC', '2026-08-14 08:44:24'),
(145, 140, '9a1c2264db7037a5fbc0.HEIC', '2026-08-14 08:44:24'),
(146, 140, '7b4d72ebdf13d01100ac.HEIC', '2026-08-14 08:44:24'),
(147, 141, '34f692d969ba5abf0730.HEIC', '2026-08-14 08:45:49'),
(148, 141, '874ed0e6fafb6e6ce051.HEIC', '2026-08-14 08:45:49'),
(149, 141, 'c954305d8240f2bf12c4.HEIC', '2026-08-14 08:45:49'),
(150, 142, 'a0fe046f74dab696cc42.HEIC', '2026-08-14 08:47:13'),
(151, 142, '12eb8f4e845db42a93f1.HEIC', '2026-08-14 08:47:13'),
(152, 142, 'db2651fb967bc6da92af.HEIC', '2026-08-14 08:47:13'),
(153, 143, 'ab0863d73836c830303c.HEIC', '2026-08-14 08:50:02'),
(154, 143, '1a1db9845d8e5c1b3d0a.HEIC', '2026-08-14 08:50:02'),
(155, 143, '2a82cc7bef6ef18c8d17.HEIC', '2026-08-14 08:50:02'),
(156, 144, '4703e2060d5a26947035.jpeg', '2026-08-16 00:07:02'),
(157, 145, '0e816d2186d87a988040.jpeg', '2026-08-16 00:12:42'),
(158, 146, '624588995e4f66e9a69f.jpeg', '2026-08-16 00:15:56'),
(159, 147, '5c6d68c8f91bb1d869ab.jpeg', '2026-08-16 00:19:23'),
(160, 148, 'f271af5e7dbf66558e00.jpeg', '2026-08-16 00:24:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `catatan_harian`
--

CREATE TABLE `catatan_harian` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `nama_konsumen` varchar(255) DEFAULT NULL,
  `usia` int(11) DEFAULT NULL,
  `jenis_kelamin` enum('Pria','Wanita') DEFAULT NULL,
  `pekerjaan` varchar(100) DEFAULT NULL,
  `jumlah_peserta` int(11) DEFAULT 0,
  `jenis_komunitas` varchar(50) DEFAULT NULL,
  `informasi` text DEFAULT NULL,
  `lokasi` varchar(255) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `koordinat_manual` tinyint(1) NOT NULL DEFAULT 0,
  `no_hp` varchar(20) DEFAULT NULL,
  `hasil_pre_test` varchar(255) DEFAULT NULL,
  `hasil_post_test` varchar(255) DEFAULT NULL,
  `nilai_pre_test` decimal(5,2) DEFAULT NULL,
  `nilai_post_test` decimal(5,2) DEFAULT NULL,
  `lokasi_gps` varchar(255) DEFAULT NULL,
  `foto_kegiatan` varchar(255) DEFAULT NULL,
  `status_review` enum('draft','pending','approved','revisi') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `lampiran_hasil_test` varchar(255) DEFAULT NULL,
  `catatan_revisi` text DEFAULT NULL,
  `kab_kota` varchar(100) DEFAULT NULL,
  `kecamatan` varchar(100) DEFAULT NULL,
  `desa` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `catatan_harian`
--

INSERT INTO `catatan_harian` (`id`, `user_id`, `tanggal`, `nama_konsumen`, `usia`, `jenis_kelamin`, `pekerjaan`, `jumlah_peserta`, `jenis_komunitas`, `informasi`, `lokasi`, `alamat`, `latitude`, `longitude`, `koordinat_manual`, `no_hp`, `hasil_pre_test`, `hasil_post_test`, `nilai_pre_test`, `nilai_post_test`, `lokasi_gps`, `foto_kegiatan`, `status_review`, `created_at`, `lampiran_hasil_test`, `catatan_revisi`, `kab_kota`, `kecamatan`, `desa`) VALUES
(82, 28, '2026-07-22', 'Nurazisa', 28, 'Wanita', 'Mahasiswa', 0, NULL, 'Materi edukasi yang disampaikan mencakup pemahaman mengenai keamanan pangan, obat, dan kosmetik, serta pengenalan Balai Besar Pengawas Obat dan Makanan (BBPOM) beserta tugas dan fungsinya dalam melakukan pengawasan terhadap obat dan makanan yang beredar di masyarakat. Pada aspek pangan, peserta diberikan pemahaman mengenai ciri-ciri makanan yang aman dan layak dikonsumsi, termasuk cara mengenali pangan yang berpotensi mengandung bahan berbahaya seperti boraks, formalin, dan Rhodamin B, serta dampaknya terhadap kesehatan. Pada aspek kosmetik, peserta diberikan edukasi mengenai cara memilih kosmetik yang aman dengan memperhatikan kemasan, label, izin edar, komposisi, dan tanggal kedaluwarsa, serta dikenalkan dengan bahan berbahaya yang dapat ditemukan dalam kosmetik, seperti hidrokuinon dan asam retinoat (tretinoin). Selain itu, peserta diberikan pemahaman mengenai cara memilih dan menggunakan obat secara aman, termasuk mengenali informasi penting pada kemasan obat, seperti nama obat, komposisi, aturan pakai, tanggal kedaluwarsa, nomor izin edar, serta penandaan golongan obat. Edukasi juga mencakup penerapan prinsip Cek KLIK (Kemasan, Label, Izin Edar, dan Kedaluwarsa) sebagai langkah sederhana bagi masyarakat untuk memastikan keamanan dan legalitas produk obat, makanan, dan kosmetik sebelum membeli maupun menggunakannya', 'Halaman Apotek Caturwarga IV', 'Jl. Caturwarga No 16 Kota Mataram, Desa/Kel. Pagutan, Kec. Mataram, Kota Mataram, Nusa Tenggara Barat', -8.5833000, 116.1167000, 0, '081936311102', NULL, NULL, 30.00, 100.00, NULL, NULL, 'pending', '2026-08-11 07:36:40', NULL, NULL, NULL, NULL, NULL),
(83, 28, '2026-07-22', 'Hari', 30, 'Pria', 'Wirausaha', 0, NULL, 'Materi edukasi yang disampaikan mencakup pemahaman mengenai keamanan pangan, obat, dan kosmetik, serta pengenalan Balai Besar Pengawas Obat dan Makanan (BBPOM) beserta tugas dan fungsinya dalam melakukan pengawasan terhadap obat dan makanan yang beredar di masyarakat. Pada aspek pangan, peserta diberikan pemahaman mengenai ciri-ciri makanan yang aman dan layak dikonsumsi, termasuk cara mengenali pangan yang berpotensi mengandung bahan berbahaya seperti boraks, formalin, dan Rhodamin B, serta dampaknya terhadap kesehatan. Pada aspek kosmetik, peserta diberikan edukasi mengenai cara memilih kosmetik yang aman dengan memperhatikan kemasan, label, izin edar, komposisi, dan tanggal kedaluwarsa, serta dikenalkan dengan bahan berbahaya yang dapat ditemukan dalam kosmetik, seperti hidrokuinon dan asam retinoat (tretinoin). Selain itu, peserta diberikan pemahaman mengenai cara memilih dan menggunakan obat secara aman, termasuk mengenali informasi penting pada kemasan obat, seperti nama obat, komposisi, aturan pakai, tanggal kedaluwarsa, nomor izin edar, serta penandaan golongan obat. Edukasi juga mencakup penerapan prinsip Cek KLIK (Kemasan, Label, Izin Edar, dan Kedaluwarsa) sebagai langkah sederhana bagi masyarakat untuk memastikan keamanan dan legalitas produk obat, makanan, dan kosmetik sebelum membeli maupun menggunakannya', 'Halaman Apotek Caturwarga IV', 'Jl. Caturwarga No 16 Kota Mataram, Desa/Kel. Pagutan, Kec. Mataram, Kota Mataram, Nusa Tenggara Barat', -8.6172775, 116.1120703, 0, '081938594061', NULL, NULL, 50.00, 100.00, NULL, NULL, 'pending', '2026-08-11 07:38:00', NULL, NULL, NULL, NULL, NULL),
(84, 28, '2026-07-22', 'Mega Maya ', 27, 'Wanita', 'Mahasiswa', 0, NULL, 'Materi edukasi yang disampaikan mencakup pemahaman mengenai keamanan pangan, obat, dan kosmetik, serta pengenalan Balai Besar Pengawas Obat dan Makanan (BBPOM) beserta tugas dan fungsinya dalam melakukan pengawasan terhadap obat dan makanan yang beredar di masyarakat. Pada aspek pangan, peserta diberikan pemahaman mengenai ciri-ciri makanan yang aman dan layak dikonsumsi, termasuk cara mengenali pangan yang berpotensi mengandung bahan berbahaya seperti boraks, formalin, dan Rhodamin B, serta dampaknya terhadap kesehatan. Pada aspek kosmetik, peserta diberikan edukasi mengenai cara memilih kosmetik yang aman dengan memperhatikan kemasan, label, izin edar, komposisi, dan tanggal kedaluwarsa, serta dikenalkan dengan bahan berbahaya yang dapat ditemukan dalam kosmetik, seperti hidrokuinon dan asam retinoat (tretinoin). Selain itu, peserta diberikan pemahaman mengenai cara memilih dan menggunakan obat secara aman, termasuk mengenali informasi penting pada kemasan obat, seperti nama obat, komposisi, aturan pakai, tanggal kedaluwarsa, nomor izin edar, serta penandaan golongan obat. Edukasi juga mencakup penerapan prinsip Cek KLIK (Kemasan, Label, Izin Edar, dan Kedaluwarsa) sebagai langkah sederhana bagi masyarakat untuk memastikan keamanan dan legalitas produk obat, makanan, dan kosmetik sebelum membeli maupun menggunakannya', 'Halaman Apotek Caturwarga IV', 'Jl. Caturwarga No 16 Kota Mataram, Desa/Kel. Pagutan, Kec. Mataram, Kota Mataram, Nusa Tenggara Barat', -8.6172775, 116.1120703, 0, '081999578246', NULL, NULL, 80.00, 100.00, NULL, NULL, 'pending', '2026-08-11 07:40:00', NULL, NULL, NULL, NULL, NULL),
(85, 28, '2026-07-22', 'Melva Marshaniswa', 28, 'Wanita', 'Mahasiswa', 0, NULL, 'Materi edukasi yang disampaikan mencakup pemahaman mengenai keamanan pangan, obat, dan kosmetik, serta pengenalan Balai Besar Pengawas Obat dan Makanan (BBPOM) beserta tugas dan fungsinya dalam melakukan pengawasan terhadap obat dan makanan yang beredar di masyarakat. Pada aspek pangan, peserta diberikan pemahaman mengenai ciri-ciri makanan yang aman dan layak dikonsumsi, termasuk cara mengenali pangan yang berpotensi mengandung bahan berbahaya seperti boraks, formalin, dan Rhodamin B, serta dampaknya terhadap kesehatan. Pada aspek kosmetik, peserta diberikan edukasi mengenai cara memilih kosmetik yang aman dengan memperhatikan kemasan, label, izin edar, komposisi, dan tanggal kedaluwarsa, serta dikenalkan dengan bahan berbahaya yang dapat ditemukan dalam kosmetik, seperti hidrokuinon dan asam retinoat (tretinoin). Selain itu, peserta diberikan pemahaman mengenai cara memilih dan menggunakan obat secara aman, termasuk mengenali informasi penting pada kemasan obat, seperti nama obat, komposisi, aturan pakai, tanggal kedaluwarsa, nomor izin edar, serta penandaan golongan obat. Edukasi juga mencakup penerapan prinsip Cek KLIK (Kemasan, Label, Izin Edar, dan Kedaluwarsa) sebagai langkah sederhana bagi masyarakat untuk memastikan keamanan dan legalitas produk obat, makanan, dan kosmetik sebelum membeli maupun menggunakannya', 'Halaman Apotek Caturwarga IV', 'Jl. Caturwarga No 16 Kota Mataram, Desa/Kel. Pagutan, Kec. Mataram, Kota Mataram, Nusa Tenggara Barat', -8.5837726, 116.1068500, 0, '085930973777', NULL, NULL, 70.00, 100.00, NULL, NULL, 'pending', '2026-08-11 07:41:26', NULL, NULL, NULL, NULL, NULL),
(86, 28, '2026-07-22', 'Dini Karmila', 26, 'Wanita', 'Mahasiswa', 0, NULL, 'Materi edukasi yang disampaikan mencakup pemahaman mengenai keamanan pangan, obat, dan kosmetik, serta pengenalan Balai Besar Pengawas Obat dan Makanan (BBPOM) beserta tugas dan fungsinya dalam melakukan pengawasan terhadap obat dan makanan yang beredar di masyarakat. Pada aspek pangan, peserta diberikan pemahaman mengenai ciri-ciri makanan yang aman dan layak dikonsumsi, termasuk cara mengenali pangan yang berpotensi mengandung bahan berbahaya seperti boraks, formalin, dan Rhodamin B, serta dampaknya terhadap kesehatan. Pada aspek kosmetik, peserta diberikan edukasi mengenai cara memilih kosmetik yang aman dengan memperhatikan kemasan, label, izin edar, komposisi, dan tanggal kedaluwarsa, serta dikenalkan dengan bahan berbahaya yang dapat ditemukan dalam kosmetik, seperti hidrokuinon dan asam retinoat (tretinoin). Selain itu, peserta diberikan pemahaman mengenai cara memilih dan menggunakan obat secara aman, termasuk mengenali informasi penting pada kemasan obat, seperti nama obat, komposisi, aturan pakai, tanggal kedaluwarsa, nomor izin edar, serta penandaan golongan obat. Edukasi juga mencakup penerapan prinsip Cek KLIK (Kemasan, Label, Izin Edar, dan Kedaluwarsa) sebagai langkah sederhana bagi masyarakat untuk memastikan keamanan dan legalitas produk obat, makanan, dan kosmetik sebelum membeli maupun menggunakannya', 'Halaman Apotek Caturwarga IV', 'Jl. Caturwarga No 16 Kota Mataram, Desa/Kel. Pagutan, Kec. Mataram, Kota Mataram, Nusa Tenggara Barat', -8.6172775, 116.1120703, 0, '081907067815', NULL, NULL, 70.00, 100.00, NULL, NULL, 'pending', '2026-08-11 07:42:37', NULL, NULL, NULL, NULL, NULL),
(87, 25, '2026-07-22', 'Hj. Maryani ', 40, 'Wanita', 'Ibu Rumah Tangga', 0, NULL, 'Materi yang disampaikan adalah terkait dengan bagaimana pemilihan obat yang aman, mencegah resistensi antibiotik dengan 4T, mengetahui tips dan trick bagaimana pemilihan kosmetik yang sesuai termasuk bahan berbahaya pada kosmetik yang harus dihindari. Menginformasikan terkait layanan BPOM Mobile serta menjelaskan apa itu CEK KLIK. Serta, menjelaskan bagaimana cara memilih pangan aman dan apa saja bahan berbahaya yang harus dihindari dalam pangan. Tidak lupa informasi bagaimana menggunakan suplemen kesehatan yang benar.', 'Aula Posyandu', 'Posyandu Kenanga Lingkungan Bawak Bagek Utara, Desa/Kel. Dasan Agung, Kec. Selaparang, Kota Mataram, Nusa Tenggara Barat', -8.5761587, 116.0996904, 0, '081558266553', NULL, NULL, 50.00, 70.00, NULL, NULL, 'pending', '2026-08-12 08:58:03', NULL, NULL, NULL, NULL, NULL),
(88, 25, '2026-07-22', 'A. Ridho Ramadhan', 23, 'Pria', '-', 0, NULL, 'Materi yang disampaikan adalah terkait dengan bagaimana pemilihan obat yang aman, mencegah resistensi antibiotik dengan 4T, mengetahui tips dan trick bagaimana pemilihan kosmetik yang sesuai termasuk bahan berbahaya pada kosmetik yang harus dihindari. Menginformasikan terkait layanan BPOM Mobile serta menjelaskan apa itu CEK KLIK. Serta, menjelaskan bagaimana cara memilih pangan aman dan apa saja bahan berbahaya yang harus dihindari dalam pangan. Tidak lupa informasi bagaimana menggunakan suplemen kesehatan yang benar.', 'Aula Posyandu', 'Posyandu Kenanga Lingkungan Bawak Bagek Utara, Desa/Kel. Dasan Agung, Kec. Selaparang, Kota Mataram, Nusa Tenggara Barat', -8.5761603, 116.0996888, 0, '0895346276908', NULL, NULL, 90.00, 100.00, NULL, NULL, 'pending', '2026-08-12 09:01:51', NULL, NULL, NULL, NULL, NULL),
(89, 25, '2026-07-22', 'Sulatiyah', 32, 'Wanita', 'Ibu Rumah Tangga', 0, NULL, 'Materi yang disampaikan adalah terkait dengan bagaimana pemilihan obat yang aman, mencegah resistensi antibiotik dengan 4T, mengetahui tips dan trick bagaimana pemilihan kosmetik yang sesuai termasuk bahan berbahaya pada kosmetik yang harus dihindari. Menginformasikan terkait layanan BPOM Mobile serta menjelaskan apa itu CEK KLIK. Serta, menjelaskan bagaimana cara memilih pangan aman dan apa saja bahan berbahaya yang harus dihindari dalam pangan. Tidak lupa informasi bagaimana menggunakan suplemen kesehatan yang benar.', 'Aula Posyandu', 'Posyandu Kenanga Lingkungan Bawak Bagek Utara, Desa/Kel. Dasan Agung, Kec. Selaparang, Kota Mataram, Nusa Tenggara Barat', -8.5761690, 116.0996739, 0, '081907361570', NULL, NULL, 90.00, 90.00, NULL, NULL, 'pending', '2026-08-12 09:03:25', NULL, NULL, NULL, NULL, NULL),
(90, 25, '2026-07-22', 'Siti Hadamah', 45, 'Wanita', 'Ibu Rumah Tangga', 0, NULL, 'Materi yang disampaikan adalah terkait dengan bagaimana pemilihan obat yang aman, mencegah resistensi antibiotik dengan 4T, mengetahui tips dan trick bagaimana pemilihan kosmetik yang sesuai termasuk bahan berbahaya pada kosmetik yang harus dihindari. Menginformasikan terkait layanan BPOM Mobile serta menjelaskan apa itu CEK KLIK. Serta, menjelaskan bagaimana cara memilih pangan aman dan apa saja bahan berbahaya yang harus dihindari dalam pangan. Tidak lupa informasi bagaimana menggunakan suplemen kesehatan yang benar.', 'Aula Posyandu', 'Posyandu Kenanga Lingkungan Bawak Bagek Utara, Desa/Kel. Dasan Agung, Kec. Selaparang, Kota Mataram, Nusa Tenggara Barat', -8.5761709, 116.0996747, 0, '081558266553', NULL, NULL, 70.00, 80.00, NULL, NULL, 'pending', '2026-08-12 09:05:06', NULL, NULL, NULL, NULL, NULL),
(91, 25, '2026-07-22', 'Bu Sahra', 35, 'Wanita', 'Ibu Rumah Tangga', 0, NULL, 'Materi yang disampaikan adalah terkait dengan bagaimana pemilihan obat yang aman, mencegah resistensi antibiotik dengan 4T, mengetahui tips dan trick bagaimana pemilihan kosmetik yang sesuai termasuk bahan berbahaya pada kosmetik yang harus dihindari. Menginformasikan terkait layanan BPOM Mobile serta menjelaskan apa itu CEK KLIK. Serta, menjelaskan bagaimana cara memilih pangan aman dan apa saja bahan berbahaya yang harus dihindari dalam pangan. Tidak lupa informasi bagaimana menggunakan suplemen kesehatan yang benar.', 'Aula Posyandu', 'Posyandu Kenanga Lingkungan Bawak Bagek Utara, Desa/Kel. Dasan Agung, Kec. Selaparang, Kota Mataram, Nusa Tenggara Barat', -8.5761751, 116.0996848, 0, '081558266553', NULL, NULL, 20.00, 70.00, NULL, NULL, 'pending', '2026-08-12 09:07:03', NULL, NULL, NULL, NULL, NULL),
(92, 30, '2026-08-10', 'Mahnun', 47, 'Wanita', 'Ibu Rumah Tangga', 0, NULL, 'Program edukasi keamanan obat dan makanan berbasis keluarga melalui kolaborasi kader dan mahasiswa. Cara mengenali pangan aman, membaca label/Nomor Izin Edar (NIE), serta bahaya Boraks, Formalin, dan pewarna tekstil. Panduan logo penggolongan obat, pencegahan bahaya antibiotik/narkoba, serta bahaya kosmetik bermerkuri.  Pengenalan Jamu/OHT/Fitofarmaka, penggunaan suplemen yang bijak, serta penerapan Cek KLIK melalui aplikasi BPOM Mobile.', 'Rumah Warga, Di Dekat Posyandu', 'Desa/Kel. Kuripan Utara, Kec. Kuripan, Lombok Barat, Nusa Tenggara Barat', -8.6718889, 116.1220480, 0, '087727123143', NULL, NULL, 40.00, 80.00, NULL, NULL, 'pending', '2026-08-13 05:24:22', 'HASIL_TEST_d215ac3fca03ef523c5f.pdf', NULL, NULL, NULL, NULL),
(93, 30, '2026-08-10', 'HALIMATUL SADIAHI', 44, 'Wanita', 'Ibu Rumah Tangga', 0, NULL, 'Program edukasi keamanan obat dan makanan berbasis keluarga melalui kolaborasi kader dan mahasiswa. Cara mengenali pangan aman, membaca label/Nomor Izin Edar (NIE), serta bahaya Boraks, Formalin, dan pewarna tekstil. Panduan logo penggolongan obat, pencegahan bahaya antibiotik/narkoba, serta bahaya kosmetik bermerkuri.  Pengenalan Jamu/OHT/Fitofarmaka, penggunaan suplemen yang bijak, serta penerapan Cek KLIK melalui aplikasi BPOM Mobile.', 'Rumah Warga, Di Dekat Posyandu', 'Desa/Kel. Kuripan Utara, Kec. Kuripan, Lombok Barat, Nusa Tenggara Barat', -8.6718889, 116.1220480, 0, '087727123143', NULL, NULL, 50.00, 80.00, NULL, NULL, 'pending', '2026-08-13 05:27:30', 'HASIL_TEST_888a20edb3c485c27bc6.pdf', NULL, NULL, NULL, NULL),
(94, 30, '2026-08-10', 'NOVIANA', 25, 'Wanita', 'Ibu Rumah Tangga', 0, NULL, 'Program edukasi keamanan obat dan makanan berbasis keluarga melalui kolaborasi kader dan mahasiswa. Cara mengenali pangan aman, membaca label/Nomor Izin Edar (NIE), serta bahaya Boraks, Formalin, dan pewarna tekstil. Panduan logo penggolongan obat, pencegahan bahaya antibiotik/narkoba, serta bahaya kosmetik bermerkuri.  Pengenalan Jamu/OHT/Fitofarmaka, penggunaan suplemen yang bijak, serta penerapan Cek KLIK melalui aplikasi BPOM Mobile.', 'Rumah Warga, Di Dekat Posyandu', 'KUMBUNG BARAT, Desa/Kel. Kuripan Utara, Kec. Kuripan, Lombok Barat, Nusa Tenggara Barat', -8.6718889, 116.1220480, 0, '0878845663389', NULL, NULL, 50.00, 80.00, NULL, NULL, 'pending', '2026-08-13 05:30:37', 'HASIL_TEST_90b01b5a67eee4832905.pdf', NULL, NULL, NULL, NULL),
(95, 30, '2026-08-10', 'SUMARNI', 23, 'Wanita', 'Ibu Rumah Tangga', 0, NULL, 'Program edukasi keamanan obat dan makanan berbasis keluarga melalui kolaborasi kader dan mahasiswa. Cara mengenali pangan aman, membaca label/Nomor Izin Edar (NIE), serta bahaya Boraks, Formalin, dan pewarna tekstil. Panduan logo penggolongan obat, pencegahan bahaya antibiotik/narkoba, serta bahaya kosmetik bermerkuri.  Pengenalan Jamu/OHT/Fitofarmaka, penggunaan suplemen yang bijak, serta penerapan Cek KLIK melalui aplikasi BPOM Mobile.', 'Rumah Warga, Di Dekat Posyandu', 'KUMBUNG BARAT, Desa/Kel. Kuripan Utara, Kec. Kuripan, Lombok Barat, Nusa Tenggara Barat', -8.6718889, 116.1220480, 0, '087851764338', NULL, NULL, 30.00, 80.00, NULL, NULL, 'pending', '2026-08-13 05:33:13', 'HASIL_TEST_a52513037a4f32478949.pdf', NULL, NULL, NULL, NULL),
(96, 30, '2026-08-10', 'BQ EMI KALSUM', 32, 'Wanita', 'Ibu Rumah Tangga', 0, NULL, 'Program edukasi keamanan obat dan makanan berbasis keluarga melalui kolaborasi kader dan mahasiswa. Cara mengenali pangan aman, membaca label/Nomor Izin Edar (NIE), serta bahaya Boraks, Formalin, dan pewarna tekstil. Panduan logo penggolongan obat, pencegahan bahaya antibiotik/narkoba, serta bahaya kosmetik bermerkuri.  Pengenalan Jamu/OHT/Fitofarmaka, penggunaan suplemen yang bijak, serta penerapan Cek KLIK melalui aplikasi BPOM Mobile.', 'Rumah Warga, Di Dekat Posyandu', 'KUMBUNG BARAT, Desa/Kel. Kuripan Utara, Kec. Kuripan, Lombok Barat, Nusa Tenggara Barat', -8.6718889, 116.1220480, 0, '081917208160', NULL, NULL, 90.00, 100.00, NULL, NULL, 'pending', '2026-08-13 05:36:21', 'HASIL_TEST_880bc3c8905925496646.pdf', NULL, NULL, NULL, NULL),
(97, 31, '2026-07-22', 'Sinthja', 28, 'Pria', 'Pegawai Swasta', 0, NULL, 'Inovasi GAS-PAMAN', 'Pos Yandu Dusun Agung', 'Desa/Kel. Dasan Agung, Kec. Selaparang, Kota Mataram, Nusa Tenggara Barat', -8.5763162, 116.0995875, 0, '087865827345', NULL, NULL, 90.00, 90.00, NULL, NULL, 'pending', '2026-08-13 23:40:36', 'HASIL_TEST_d8e7eec5f44a898801f3.pdf', NULL, NULL, NULL, NULL),
(98, 31, '2026-07-22', 'Syarifa', 26, 'Wanita', 'Pegawai Swasta', 0, NULL, 'Inovasi GAS-PAMAN', 'Pos Yandu Dusun Agung', 'Desa/Kel. Dasan Agung, Kec. Mataram, Kota Mataram, Nusa Tenggara Barat', -8.5764852, 116.0994973, 0, '081936775617', NULL, NULL, 90.00, 90.00, NULL, NULL, 'pending', '2026-08-13 23:48:02', 'HASIL_TEST_efc595a0d56b4b862aaa.pdf', NULL, NULL, NULL, NULL),
(99, 31, '2026-07-22', 'Septin', 25, 'Wanita', 'Pegawai Swasta', 0, NULL, 'Inovasi GAS-PAMAN', 'Pos Yandu Dusun Agung', 'Desa/Kel. Dasan Agung, Kec. Mataram, Kota Mataram, Nusa Tenggara Barat', -8.5764716, 116.0995091, 0, '081907644111', NULL, NULL, 80.00, 80.00, NULL, NULL, 'pending', '2026-08-13 23:58:27', 'HASIL_TEST_d52958904a6fd7ef7e29.pdf', NULL, NULL, NULL, NULL),
(100, 31, '2026-07-22', 'Mariani', 50, 'Wanita', 'Ibu Rumah Tangga', 0, NULL, 'Inovasi GAS-PAMAN', 'Pos Yandu Dusun Agung', 'Desa/Kel. Dasan Agung, Kec. Mataram, Kota Mataram, Nusa Tenggara Barat', -8.5764825, 116.0994901, 0, '-', NULL, NULL, 30.00, 70.00, NULL, NULL, 'pending', '2026-08-14 00:04:19', 'HASIL_TEST_92b9164b11fd73a36500.pdf', NULL, NULL, NULL, NULL),
(101, 31, '2026-07-22', 'Khaeroni', 28, 'Pria', 'Pegawai Swasta', 0, NULL, 'Inovasi GAS-PAMAN', 'Pos Yandu Dusun Agung', 'Desa/Kel. Dasan Agung, Kec. Mataram, Kota Mataram, Nusa Tenggara Barat', -8.5765470, 116.0994657, 0, '-', NULL, NULL, 80.00, 90.00, NULL, NULL, 'pending', '2026-08-14 00:12:33', 'HASIL_TEST_4b22427adfb7e0368a56.pdf', NULL, NULL, NULL, NULL),
(102, 33, '2026-06-24', 'Riyanti', 40, 'Wanita', '-', 0, NULL, 'Dilakukan penyuluhan mengenai keamanan pangan, obat, dan kosmetik kepada masyarakat dengan memperkenalkan program CEK KLIK (Cek Kemasan, Label, Izin Edar, dan Kedaluwarsa) sebagai langkah sederhana dalam memastikan keamanan produk sebelum dibeli atau digunakan. Selain itu, disampaikan informasi mengenai GAS-PAMAN (Keluarga Sadar Pangan Aman) dari BPOM Mataram sebagai upaya meningkatkan kesadaran keluarga dalam memilih, mengolah, dan mengonsumsi pangan yang aman, serta meningkatkan pengetahuan masyarakat dalam mengenali dan menggunakan produk pangan, obat, dan kosmetik yang memenuhi ketentuan.', 'Posyandu Lavender', 'Jl. Gn. Lawu No.289, Dasan Agung Baru, Kec. Selaparang, Kota Mataram, Nusa Tenggara Bar., Desa/Kel. Dasan Agung Baru, Kec. Selaparang, Kota Mataram, Nusa Tenggara Barat', -8.5781842, 116.0850686, 0, '082359010137', NULL, NULL, 80.00, 90.00, NULL, NULL, 'pending', '2026-08-14 05:01:22', 'HASIL_TEST_adaf9d0fd73647afd6e5.pdf', NULL, NULL, NULL, NULL),
(103, 33, '2026-08-24', 'Siti Aisya', 38, 'Wanita', '-', 0, NULL, 'Dilakukan penyuluhan mengenai keamanan pangan, obat, dan kosmetik kepada masyarakat dengan memperkenalkan program CEK KLIK (Cek Kemasan, Label, Izin Edar, dan Kedaluwarsa) sebagai langkah sederhana dalam memastikan keamanan produk sebelum dibeli atau digunakan. Selain itu, disampaikan informasi mengenai GAS-PAMAN (Keluarga Sadar Pangan Aman) dari BPOM Mataram sebagai upaya meningkatkan kesadaran keluarga dalam memilih, mengolah, dan mengonsumsi pangan yang aman, serta meningkatkan pengetahuan masyarakat dalam mengenali dan menggunakan produk pangan, obat, dan kosmetik yang memenuhi ketentuan.\r\n', 'Posyandu Lavender', 'Jl. Gn. Lawu No.289, Dasan Agung Baru, Kec. Selaparang, Kota Mataram, Nusa Tenggara Bar., Desa/Kel. Dasan Agung Baru, Kec. Selaparang, Kota Mataram, Nusa Tenggara Barat', -8.5833000, 116.1167000, 0, '-', NULL, NULL, 60.00, 80.00, NULL, NULL, 'pending', '2026-08-14 05:03:17', 'HASIL_TEST_ca45c9f2bcf9733c9c4a.pdf', NULL, NULL, NULL, NULL),
(104, 33, '2026-08-24', 'Baiq Nuraini', 55, 'Wanita', '-', 0, NULL, 'Dilakukan penyuluhan mengenai keamanan pangan, obat, dan kosmetik kepada masyarakat dengan memperkenalkan program CEK KLIK (Cek Kemasan, Label, Izin Edar, dan Kedaluwarsa) sebagai langkah sederhana dalam memastikan keamanan produk sebelum dibeli atau digunakan. Selain itu, disampaikan informasi mengenai GAS-PAMAN (Keluarga Sadar Pangan Aman) dari BPOM Mataram sebagai upaya meningkatkan kesadaran keluarga dalam memilih, mengolah, dan mengonsumsi pangan yang aman, serta meningkatkan pengetahuan masyarakat dalam mengenali dan menggunakan produk pangan, obat, dan kosmetik yang memenuhi ketentuan.\r\n', 'Posyandu Lavender', 'Jl. Gn. Lawu No.289, Dasan Agung Baru, Kec. Selaparang, Kota Mataram, Nusa Tenggara Bar., Desa/Kel. Dasan Agung Baru, Kec. Selaparang, Kota Mataram, Nusa Tenggara Barat', -8.5833000, 116.1167000, 0, '-', NULL, NULL, 30.00, 30.00, NULL, NULL, 'pending', '2026-08-14 05:04:41', 'HASIL_TEST_b53b807e167bc04b0718.pdf', NULL, NULL, NULL, NULL),
(105, 33, '2026-08-14', 'Sri Megawati', 55, 'Wanita', '-', 0, NULL, 'Dilakukan penyuluhan mengenai keamanan pangan, obat, dan kosmetik kepada masyarakat dengan memperkenalkan program CEK KLIK (Cek Kemasan, Label, Izin Edar, dan Kedaluwarsa) sebagai langkah sederhana dalam memastikan keamanan produk sebelum dibeli atau digunakan. Selain itu, disampaikan informasi mengenai GAS-PAMAN (Keluarga Sadar Pangan Aman) dari BPOM Mataram sebagai upaya meningkatkan kesadaran keluarga dalam memilih, mengolah, dan mengonsumsi pangan yang aman, serta meningkatkan pengetahuan masyarakat dalam mengenali dan menggunakan produk pangan, obat, dan kosmetik yang memenuhi ketentuan.\r\n', 'Posyandu Lavender', 'Jl. Gn. Lawu No.289, Dasan Agung Baru, Kec. Selaparang, Kota Mataram, Nusa Tenggara Bar., Desa/Kel. Dasan Agung Baru, Kec. Selaparang, Kota Mataram, Nusa Tenggara Barat', -8.5833000, 116.1167000, 0, '-', NULL, NULL, 90.00, 100.00, NULL, NULL, 'pending', '2026-08-14 05:05:37', 'HASIL_TEST_4d27be21a0718e01c155.pdf', NULL, NULL, NULL, NULL),
(106, 33, '2026-08-14', 'Rahmi Dwi Pratiwi', 40, 'Wanita', '-', 0, NULL, 'Dilakukan penyuluhan mengenai keamanan pangan, obat, dan kosmetik kepada masyarakat dengan memperkenalkan program CEK KLIK (Cek Kemasan, Label, Izin Edar, dan Kedaluwarsa) sebagai langkah sederhana dalam memastikan keamanan produk sebelum dibeli atau digunakan. Selain itu, disampaikan informasi mengenai GAS-PAMAN (Keluarga Sadar Pangan Aman) dari BPOM Mataram sebagai upaya meningkatkan kesadaran keluarga dalam memilih, mengolah, dan mengonsumsi pangan yang aman, serta meningkatkan pengetahuan masyarakat dalam mengenali dan menggunakan produk pangan, obat, dan kosmetik yang memenuhi ketentuan.\r\n', 'Posyandu Lavender', 'Jl. Gn. Lawu No.289, Dasan Agung Baru, Kec. Selaparang, Kota Mataram, Nusa Tenggara Bar., Desa/Kel. Dasan Agung Baru, Kec. Selaparang, Kota Mataram, Nusa Tenggara Barat', -8.5833000, 116.1167000, 0, '087850934133', NULL, NULL, 60.00, 80.00, NULL, NULL, 'pending', '2026-08-14 05:07:28', 'HASIL_TEST_2e2e3813d1712dd4e462.pdf', NULL, NULL, NULL, NULL),
(107, 35, '2026-07-24', 'Rabiatul aini', 55, 'Wanita', 'Kader posyandu', 0, NULL, 'Rhodamin b, boraks dan formalin', 'Posyandu lavender', 'Lingkungan darul hikmah, Desa/Kel. Dasan Agung, Kec. Selaparang, Kota Mataram, Nusa Tenggara Barat', -8.5781842, 116.0850686, 0, '087750494132', NULL, NULL, 80.00, 90.00, NULL, NULL, 'pending', '2026-08-14 05:10:49', 'HASIL_TEST_b6b054654af96c50f3de.jpg', NULL, NULL, NULL, NULL),
(108, 29, '2026-07-20', 'Muliana', 26, 'Pria', 'ibu rumah tangga', 0, NULL, 'menjelaskan pangan yang di konsumsi aman, cara membuang sampah obat yang benar, kosmetik yang aman', 'Rumah Agen Gas Paman', 'Monjok Kebon Jaya Barat, Desa/Kel. Rembiga, Kec. selaparang, Kota Mataram, Nusa Tenggara Barat', -8.5796633, 116.1029969, 0, '081999112933', NULL, NULL, 80.00, 99.99, NULL, NULL, 'pending', '2026-08-14 05:10:51', 'HASIL_TEST_11fac508b66967fa238c.pdf', NULL, NULL, NULL, NULL),
(109, 35, '2026-08-14', 'Rabiatul aini', 55, 'Wanita', 'Kader posyandu', 0, NULL, 'Rhodamin B, boraks dan formalin', 'Posyandu lavender', 'Lingkungan darul hikmah, Desa/Kel. Dasan Agung, Kec. Selaparang, Kota Mataram, Nusa Tenggara Barat', -8.5781842, 116.0850686, 0, '087750494132', NULL, NULL, 80.00, 90.00, NULL, NULL, 'pending', '2026-08-14 05:12:55', 'HASIL_TEST_249b379547960b947320.jpg', NULL, NULL, NULL, NULL),
(110, 29, '2026-07-20', 'Netin', 26, 'Wanita', 'ibu rumah tangga', 0, NULL, 'Edukasi pangan aman, pembuangan obat yang benar, kosmetik yang berbahaya da penggunaan aplikasi BPOM mobile', 'Rumah agent gas Paman', 'Asrama TNI AU Lanud Zam, Desa/Kel. Rembiga, Kec. Selaparang, Kota Mataram, Nusa Tenggara Barat', -8.5849432, 116.1160837, 0, '085337585867', NULL, NULL, 90.00, 99.88, NULL, NULL, 'pending', '2026-08-14 05:16:07', 'HASIL_TEST_02356cb1580e9a4cbb78.pdf', NULL, NULL, NULL, NULL),
(111, 35, '2026-08-14', 'Rabiatul aini', 55, 'Wanita', 'Kader posyandu', 0, NULL, 'Rhodamin B, boraks dan formalin', 'Posyandu lavender', 'Lingkungan darul hikmah, Desa/Kel. Dasan Agung, Kec. Selaparang, Kota Mataram, Nusa Tenggara Barat', -8.5781842, 116.0850686, 0, '087750494132', NULL, NULL, 80.00, 90.00, NULL, NULL, 'pending', '2026-08-14 05:16:31', 'HASIL_TEST_470752e51e2850212f4d.jpg', NULL, NULL, NULL, NULL),
(112, 29, '2026-07-20', 'Ernamawati', 25, 'Wanita', 'Ibu Rumah Tangga', 0, NULL, 'Edukasi pangan aman, pembuangan obat yang benar, kosmetik yang berbahaya da penggunaan aplikasi BPOM mobile', 'Rumah agent Gas Paman', 'Karang Baru, Desa/Kel. Rembiga, Kec. Selaparang, Kota Mataram, Nusa Tenggara Barat', -8.5849432, 116.1160837, 0, '087865382550', NULL, NULL, 80.00, 100.00, NULL, NULL, 'pending', '2026-08-14 05:19:40', 'HASIL_TEST_24c889428f7d562b0535.pdf', NULL, NULL, NULL, NULL),
(113, 29, '2026-07-20', 'Ninda Tiarni Pusmawati', 25, 'Wanita', 'ibu rumah tangga', 0, NULL, 'Edukasi pangan aman, pembuangan obat yang benar, kosmetik yang berbahaya da penggunaan aplikasi BPOM mobile', 'Rumah agent Gas Paman', 'Monjok, Desa/Kel. Rembiga, Kec. Selaparang, Kota Mataram, Nusa Tenggara Barat', -8.5849432, 116.1160837, 0, '081770440104', NULL, NULL, 80.00, 100.00, NULL, NULL, 'pending', '2026-08-14 05:22:31', 'HASIL_TEST_8b806464928f1e48b530.pdf', NULL, NULL, NULL, NULL),
(114, 29, '2026-07-20', 'Ayu Apriliani', 25, 'Wanita', 'ibu rumah tangga', 0, NULL, 'Edukasi pangan aman, pembuangan obat yang benar, kosmetik yang berbahaya da penggunaan aplikasi BPOM mobile', 'Rumah agen Gas Paman', 'Johar Pelita, Desa/Kel. Rembiga, Kec. Selaparang, Kota Mataram, Nusa Tenggara Barat', -8.5849432, 116.1160837, 0, '087885341220', NULL, NULL, 60.00, 99.90, NULL, NULL, 'pending', '2026-08-14 05:25:26', 'HASIL_TEST_1e1bdae9394ab5307ad5.pdf', NULL, NULL, NULL, NULL),
(115, 35, '2026-07-24', 'Siti zulaeha', 43, 'Wanita', 'Kader posyandu', 0, NULL, 'Rhodamin b, boraks dan formalin', 'Posyandu lavender', 'Lingkungan darul hikmah, Desa/Kel. Dasan Agung, Kec. Selaparang, Kota Mataram, Nusa Tenggara Barat', -8.5792781, 116.0894983, 0, '082441541132', NULL, NULL, 81.00, 96.00, NULL, NULL, 'pending', '2026-08-14 05:28:03', 'HASIL_TEST_e1692964f929f0b1fa9a.jpg', NULL, NULL, NULL, NULL),
(116, 35, '2026-07-24', 'Siti zulaeha', 43, 'Wanita', 'Kader posyandu', 0, NULL, 'Rhodamin b, boraks dan formalin', 'Posyandu lavender', 'Lingkungan darul hikmah, Desa/Kel. Dasan Agung, Kec. Selaparang, Kota Mataram, Nusa Tenggara Barat', -8.5814847, 116.0898421, 0, '082441541132', NULL, NULL, 81.00, 96.00, NULL, NULL, 'pending', '2026-08-14 05:29:13', 'HASIL_TEST_3ab3dcc97cbc472e3ffe.jpg', NULL, NULL, NULL, NULL),
(117, 35, '2026-07-24', 'Ramlah', 56, 'Wanita', 'Kader posyandu', 0, NULL, 'Rhodamin b, boraks dan formalin', 'Posyandu lavender', 'Lingkungan darul hikmah, Desa/Kel. Dasan Agung, Kec. Selaparang, Kota Mataram, Nusa Tenggara Barat', -8.5801529, 116.0889826, 0, '085332142134', NULL, NULL, 70.00, 86.00, NULL, NULL, 'pending', '2026-08-14 05:33:51', 'HASIL_TEST_3dd29800dd30b59f04bd.jpg', NULL, NULL, NULL, NULL),
(118, 35, '2026-07-24', 'Ramlah', 56, 'Wanita', 'Kader posyandu', 0, NULL, 'Rhodamin b, boraks dan formalin', 'Posyandu lavender', 'Lingkungan darul hikmah, Desa/Kel. Dasan Agung, Kec. Selaparang, Kota Mataram, Nusa Tenggara Barat', -8.5825944, 116.0884868, 0, '085332142134', NULL, NULL, 70.00, 86.00, NULL, NULL, 'pending', '2026-08-14 05:35:02', 'HASIL_TEST_5b0537b69761fda8c85a.jpg', NULL, NULL, NULL, NULL),
(119, 35, '2026-07-24', 'Nulen Meilani', 34, 'Wanita', 'Kader posyandu', 0, NULL, 'Rhodamin b, boraks dan formalin', 'Posyandu lavender', 'Lingkungan darul hikmah, Desa/Kel. Dasan Agung, Kec. Selaparang, Kota Mataram, Nusa Tenggara Barat', -8.5815370, 116.0881231, 0, '085441271342', NULL, NULL, 80.00, 100.00, NULL, NULL, 'pending', '2026-08-14 05:39:18', 'HASIL_TEST_200a659ecbc7f9add014.jpg', NULL, NULL, NULL, NULL),
(120, 35, '2026-07-24', 'Nulen Meilani', 34, 'Wanita', 'Kader posyandu', 0, NULL, 'Rhodamin b, boraks dan formalin', 'Posyandu lavender', 'Lingkungan darul hikmah, Desa/Kel. Dasan Agung, Kec. Selaparang, Kota Mataram, Nusa Tenggara Barat', -8.5809102, 116.0893264, 0, '085441271342', NULL, NULL, 80.00, 100.00, NULL, NULL, 'pending', '2026-08-14 05:40:17', 'HASIL_TEST_969458279e3d98b4764a.jpg', NULL, NULL, NULL, NULL),
(121, 35, '2026-07-24', 'Nulen Meilani', 34, 'Wanita', 'Kader posyandu', 0, NULL, 'Rhodamin b, boraks dan formalin', 'Posyandu lavender', 'Lingkungan darul hikmah, Desa/Kel. Dasan Agung, Kec. Selaparang, Kota Mataram, Nusa Tenggara Barat', -8.5809102, 116.0893264, 0, '085441271342', NULL, NULL, 80.00, 100.00, NULL, NULL, 'pending', '2026-08-14 05:42:14', 'HASIL_TEST_5892996ff66fd12a2d06.jpg', NULL, NULL, NULL, NULL),
(122, 35, '2026-07-24', 'Ramlah', 60, 'Wanita', 'Kader posyandu', 0, NULL, 'Rhodamin b, boraks dan formalin', 'Posyandu lavender', 'Lingkungan darul hikmah, Desa/Kel. Dasan Agung, Kec. Selaparang, Kota Mataram, Nusa Tenggara Barat', -8.5816936, 116.0888107, 0, '085342156872', NULL, NULL, 68.00, 89.00, NULL, NULL, 'pending', '2026-08-14 05:45:50', 'HASIL_TEST_0779598289b153005191.jpg', NULL, NULL, NULL, NULL),
(123, 35, '2026-07-24', 'Ramlah', 60, 'Wanita', 'Kader posyandu', 0, NULL, 'Rhodamin b, boraks dan formalin', 'Posyandu lavender', 'Lingkungan darul hikmah, Desa/Kel. Dasan Agung, Kec. Selaparang, Kota Mataram, Nusa Tenggara Barat', -8.5804010, 116.0907016, 0, '085342156872', NULL, NULL, 68.00, 89.00, NULL, NULL, 'pending', '2026-08-14 05:48:28', 'HASIL_TEST_f022bc4c2bca7057001a.jpg', NULL, NULL, NULL, NULL),
(124, 26, '2026-08-12', 'Enun', 49, 'Wanita', 'Ibu Rumah Tangga', 0, NULL, 'Inovasi Gas Paman mengenai obat,kosmetik, dan makanan', 'Halaman rumah pemberi edukasi ', 'Rembiga Timur, Desa/Kel. Rembiga, Kec. Selaparang, Kota Mataram, Nusa Tenggara Barat', -8.5838994, 116.1173300, 0, '0821654982', NULL, NULL, 80.00, 90.00, NULL, NULL, 'pending', '2026-08-14 06:50:34', 'HASIL_TEST_f06c80b519ff9f1e6926.pdf', NULL, NULL, NULL, NULL),
(125, 26, '2026-08-12', 'Sri Maharni', 51, 'Wanita', 'Ibu Rumah Tangga', 0, NULL, 'Inovasi Gas Paman mengenai obat,kosmetik, dan makanan', 'Teras Rumah Pemberi Edukasi', 'Desa/Kel. Rembiga, Kec. Mataram, Kota Mataram, Nusa Tenggara Barat', -8.5838994, 116.1173300, 0, '081915950943', NULL, NULL, 80.00, 100.00, NULL, NULL, 'pending', '2026-08-14 06:58:04', 'HASIL_TEST_0272f2e000fc91e87a8c.pdf', NULL, NULL, NULL, NULL),
(126, 26, '2026-08-12', 'Dini Ariani', 47, 'Wanita', 'Ibu Rumah Tangga', 0, NULL, 'Inovasi Gas Paman mengenai obat,kosmetik, dan makanan', 'Teras Rumah Pemberi Edukasi', 'Desa/Kel. Rembiga, Kec. Mataram, Kota Mataram, Nusa Tenggara Barat', -8.5838994, 116.1173300, 0, '085961432086', NULL, NULL, 90.00, 100.00, NULL, NULL, 'pending', '2026-08-14 07:09:15', 'HASIL_TEST_5cb7226648ea06c9754a.pdf', NULL, NULL, NULL, NULL),
(127, 34, '2026-07-24', 'Wahida', 60, 'Wanita', 'Ibu rumah tangga', 0, NULL, 'Cek klik, pangan, obat, kosmetik, dan obat tradisional', 'Posyandu', 'Jl. Gunung batur. No 25  Darul Hikmah, Desa/Kel. Dasan Agung Baru, Kec. Selaparang, Kota Mataram, Nusa Tenggara Barat', -8.5781880, 116.0999160, 0, '081918902907', NULL, NULL, 70.00, 80.00, NULL, NULL, 'pending', '2026-08-14 07:12:15', 'HASIL_TEST_ea62097c52c1a0d5f193.pdf', NULL, NULL, NULL, NULL),
(128, 27, '2026-08-10', 'Reni Irmatuzzohrah', 33, 'Wanita', 'Ibu Rumah Tangga', 0, NULL, 'Sosialisasi GAS-PAMAN, Seri Pangan, Seri Kosmetik dan Obat, Seri Obat Tradisional dan Suplemen Makanan', 'Posyandu Bangau Kumbung ', 'Dusun Kumbung Barat, Desa/Kel. Kuripan Utara, Kec. Kuripan, Lombok Barat, Nusa Tenggara Barat', -8.5833000, 116.1167000, 0, '081927270141', NULL, NULL, 70.00, 100.00, NULL, NULL, 'pending', '2026-08-14 07:17:23', 'HASIL_TEST_1da7837b5786c5a68488.pdf', NULL, NULL, NULL, NULL),
(129, 26, '2026-08-12', 'Heni ', 38, 'Wanita', 'Ibu Rumah Tangga', 0, NULL, 'Inovasi Gas Paman mengenai obat,kosmetik, dan makanan', 'Teras Rumah Pemberi Edukasi ', 'Desa/Kel. Rembiga, Kec. Selaparang, Kota Mataram, Nusa Tenggara Barat', -8.5838994, 116.1173300, 0, '081147132111', NULL, NULL, 90.00, 100.00, NULL, NULL, 'pending', '2026-08-14 07:18:49', 'HASIL_TEST_14ab44554063ded1305a.pdf', NULL, NULL, NULL, NULL),
(130, 27, '2026-08-10', 'SAHIRUN', 33, 'Wanita', 'Ibu Rumah Tangga', 0, NULL, 'Sosialisasi GAS-PAMAN, Seri Pangan, Seri Kosmetik dan Obat, Seri Obat Tradisional dan Suplemen Makanan', 'Posyandu Bangau Kumbung ', 'Dusun Kumbung Barat, Desa/Kel. Kuripan Utara, Kec. Kuripan, Lombok Barat, Nusa Tenggara Barat', -8.6718889, 116.1220480, 0, '085932534685', NULL, NULL, 50.00, 100.00, NULL, NULL, 'pending', '2026-08-14 07:20:23', 'HASIL_TEST_d7233b5e37bfdf41ffee.pdf', NULL, NULL, NULL, NULL),
(131, 27, '2026-08-10', 'SAMIRAH', 54, 'Wanita', 'Ibu Rumah Tangga', 0, NULL, 'Sosialisasi GAS-PAMAN, Seri Pangan, Seri Kosmetik dan Obat, Seri Obat Tradisional dan Suplemen Makanan', 'Posyandu Bangau Kumbung', 'Dusun Kumbung Barat, Desa/Kel. Kuripan, Kec. Kuripan, Lombok Barat, Nusa Tenggara Barat', -8.6718889, 116.1220480, 0, '085932534685', NULL, NULL, 50.00, 70.00, NULL, NULL, 'pending', '2026-08-14 07:24:04', 'HASIL_TEST_812c739c51ad02469796.pdf', NULL, NULL, NULL, NULL),
(132, 26, '2026-08-13', 'Hurin Ain', 41, 'Wanita', 'Ibu Rumah Tangga', 0, NULL, 'Inovasi gaspaman mengenai obat kosmetik dan makanan ', 'Teras Rumah Pemberi Edukasi ', 'Desa/Kel. Rembiga, Kec. Selaparang, Kota Mataram, Nusa Tenggara Barat', -8.5838994, 116.1173300, 0, '+62 877-2703-7120', NULL, NULL, 100.00, 100.00, NULL, NULL, 'pending', '2026-08-14 07:24:25', 'HASIL_TEST_602e817738214d884b7f.pdf', NULL, NULL, NULL, NULL),
(133, 27, '2026-08-10', 'SAHRAM', 54, 'Wanita', 'Ibu Rumah Tangga', 0, NULL, 'Sosialisasi GAS-PAMAN, Seri Pangan, Seri Kosmetik dan Obat, Seri Obat Tradisional dan Suplemen Makanan', 'Posyandu Bangau Kumbung ', 'Dusun Kumbung Barat, Desa/Kel. Kuripan Utara, Kec. Kuripan, Lombok Barat, Nusa Tenggara Barat', -8.6718889, 116.1220480, 0, '085932534685', NULL, NULL, 50.00, 80.00, NULL, NULL, 'pending', '2026-08-14 07:29:42', 'HASIL_TEST_0b1eb50023eed8a04ee1.pdf', NULL, NULL, NULL, NULL),
(134, 34, '2026-07-24', 'Baiq rujiah', 54, 'Wanita', 'Ibu rumah tangga', 0, NULL, '085338305419', 'Posyandu', 'Jl. Gunung batur. No 25  Darul Hikmah, Desa/Kel. Dasan Agung Baru, Kec. Selaparang, Kota Mataram, Nusa Tenggara Barat', -8.5781842, 116.0850686, 0, '085338305419', NULL, NULL, 90.00, 90.00, NULL, NULL, 'pending', '2026-08-14 07:30:41', 'HASIL_TEST_fe93542f39082f23b55b.pdf', NULL, NULL, NULL, NULL),
(135, 27, '2026-08-10', 'RINI HAIRUL UMINAH', 30, 'Wanita', 'Ibu Rumah Tangga', 0, NULL, 'Sosialisasi GAS-PAMAN, Seri Pangan, Seri Kosmetik dan Obat, Seri Obat Tradisional dan Suplemen Makanan', 'Posyandu Bangau Kumbung ', 'Dusun Kumbung Barat, Desa/Kel. Kuripan Utara, Kec. Kuripan, Lombok Barat, Nusa Tenggara Barat', -8.6718889, 116.1220480, 0, '083147766962', NULL, NULL, 60.00, 100.00, NULL, NULL, 'pending', '2026-08-14 07:31:54', 'HASIL_TEST_27425f8d98da4a0c0ff3.pdf', NULL, NULL, NULL, NULL),
(136, 34, '2026-07-24', 'Siti', 48, 'Wanita', 'Ibu rumah tangga', 0, NULL, 'Cek klik, obat, obat tradisional, pangan, kosmetik', 'Posyandu', 'Jl. Gunung batur. No 25  Darul Hikmah, Desa/Kel. Dasan Agung Baru, Kec. Selaparang, Kota Mataram, Nusa Tenggara Barat', -8.5781842, 116.0850686, 0, '087817278108', NULL, NULL, 80.00, 80.00, NULL, NULL, 'pending', '2026-08-14 07:42:10', 'HASIL_TEST_d2a824c2b0d6e5c05aa5.pdf', NULL, NULL, NULL, NULL),
(137, 34, '2026-07-24', 'Sri megawati', 59, 'Wanita', 'Ibu rumah tangga', 0, NULL, 'Cek klik, obat, obat tradisional, pangan, kosmetik', 'Posyandu', 'Jl. Gunung batur. No 25  Darul Hikmah, Desa/Kel. Dasan Agung Baru, Kec. Selaparang, Kota Mataram, Nusa Tenggara Barat', -8.5781842, 116.0850686, 0, '+62 895-2857-9663', NULL, NULL, 90.00, 100.00, NULL, NULL, 'pending', '2026-08-14 07:47:00', 'HASIL_TEST_77e648f42a652b7e107b.pdf', NULL, NULL, NULL, NULL),
(138, 34, '2026-07-24', 'Rahmi dwi pratiwi', 68, 'Wanita', 'Ibu rumah tangga', 0, NULL, 'Cek klik, obat, obat tradisonal, pangan, kosmetik', 'Posyandu', 'Jl. Gunung batur. No 25  Darul Hikmah, Desa/Kel. Dasan Agung Baru, Kec. Selaparang, Kota Mataram, Nusa Tenggara Barat', -8.5833000, 116.1167000, 0, '+62 813-3289-7539', NULL, NULL, 60.00, 80.00, NULL, NULL, 'pending', '2026-08-14 07:53:53', 'HASIL_TEST_75f6930da6f695b5f368.pdf', NULL, NULL, NULL, NULL),
(139, 32, '2026-08-10', 'Siti Nurjannah', 32, 'Wanita', 'Ibu Rumah Tangga', 0, NULL, 'Seri 4 Inovasi GAS-PAMAN (OT, SK, dan BPOM Mobile)', 'Rumah Warga', 'Kumbung Barat, Desa/Kel. Labuapi, Kec. Labuapi, Lombok Barat, Nusa Tenggara Barat', -8.5833000, 116.1167000, 0, '087744870694', NULL, NULL, 40.00, 100.00, NULL, NULL, 'pending', '2026-08-14 08:41:56', 'HASIL_TEST_74afc42e9a5dc3c5f2b3.pdf', NULL, NULL, NULL, NULL),
(140, 32, '2026-08-10', 'Nur Linda', 36, 'Wanita', 'Ibu Rumah Tangga', 0, NULL, 'Seri 4 Inovasi GAS-PAMAN (OT, SK, dan BPOM Mobile)', 'Rumah Warga', 'Kumbung Barat, Desa/Kel. Labuapi, Kec. Labuapi, Lombok Barat, Nusa Tenggara Barat', -8.6718889, 116.1220480, 0, '087744870694', NULL, NULL, 70.00, 100.00, NULL, NULL, 'pending', '2026-08-14 08:44:24', 'HASIL_TEST_958ef9f0b2fe0bab4c69.pdf', NULL, NULL, NULL, NULL),
(141, 32, '2026-08-10', 'Winda Oktavia', 28, 'Wanita', 'Ibu Rumah Tangga', 0, NULL, 'Seri 4 Inovasi GAS-PAMAN (OT, SK, dan BPOM Mobile)', 'Rumah Warga', 'Kumbung Barat, Desa/Kel. Labuapi, Kec. Labuapi, Lombok Barat, Nusa Tenggara Barat', -8.6718889, 116.1220480, 0, '087744870694', NULL, NULL, 70.00, 100.00, NULL, NULL, 'pending', '2026-08-14 08:45:49', 'HASIL_TEST_27d646ce6c6ea0b74cbc.pdf', NULL, NULL, NULL, NULL),
(142, 32, '2026-08-10', 'Sabedah', 35, 'Wanita', 'Ibu Rumah Tangga', 0, NULL, 'Seri 4 Inovasi GAS-PAMAN (OT, SK, dan BPOM Mobile)', 'Rumah Warga', 'Kumbung Barat, Desa/Kel. Labuapi, Kec. Labuapi, Lombok Barat, Nusa Tenggara Barat', -8.6718889, 116.1220480, 0, '087744870694', NULL, NULL, 40.00, 100.00, NULL, NULL, 'pending', '2026-08-14 08:47:13', 'HASIL_TEST_6cba9cb68197cb6489d0.pdf', NULL, NULL, NULL, NULL),
(143, 32, '2026-08-10', 'Puji Utari', 6, 'Wanita', 'Ibu Rumah Tangga', 0, NULL, 'Seri 4 Inovasi GAS-PAMAN (OT, SK, dan BPOM Mobile)', 'Rumah Warga', 'Kumbung Barat, Desa/Kel. Labuapi, Kec. Labuapi, Lombok Barat, Nusa Tenggara Barat', -8.6718889, 116.1220480, 0, '087744870694', NULL, NULL, 80.00, 100.00, NULL, NULL, 'pending', '2026-08-14 08:50:02', 'HASIL_TEST_d6fe5f1761ec162b34f2.pdf', NULL, NULL, NULL, NULL),
(144, 24, '2026-08-16', 'zahwa', 25, 'Wanita', 'wirausaha', 0, NULL, 'Cek kemasan, label, izin edar, dan kedaluwarsa (CEK KLIK).\r\nMinum antibiotik sesuai aturan dan sampai tuntas.\r\nGunakan obat sesuai golongan dan aturan pakai.\r\nHindari penyalahgunaan obat.\r\nPilih kosmetik yang aman dan punya izin edar.\r\nWaspadai bahan berbahaya pada kosmetik.\r\nPilih jamu/obat tradisional yang aman dan tidak mengandung BKO.\r\nGunakan suplemen seperlunya, tidak berlebihan.\r\nCek legalitas produk lewat BPOM Mobile.\r\nBiasakan membaca label makanan sebelum membeli.', 'teras', 'Desa/Kel. Pejanggik, Kec. Mataram, Kota Mataram, Nusa Tenggara Barat', -8.5838443, 116.1121888, 0, '081353576928', NULL, NULL, 10.00, 100.00, NULL, NULL, 'pending', '2026-08-16 00:07:02', 'HASIL_TEST_033732400724cf4e8a02.pdf', NULL, NULL, NULL, NULL),
(145, 24, '2026-08-16', 'Mahyani', 29, 'Wanita', 'wirausaha', 0, NULL, 'Cek kemasan, label, izin edar, dan kedaluwarsa (CEK KLIK).\r\nMinum antibiotik sesuai aturan dan sampai tuntas.\r\nGunakan obat sesuai golongan dan aturan pakai.\r\nHindari penyalahgunaan obat.\r\nPilih kosmetik yang aman dan punya izin edar.\r\nWaspadai bahan berbahaya pada kosmetik.\r\nPilih jamu/obat tradisional yang aman dan tidak mengandung BKO.\r\nGunakan suplemen seperlunya, tidak berlebihan.\r\nCek legalitas produk lewat BPOM Mobile.\r\nBiasakan membaca label makanan sebelum membeli.', 'ruang teras', 'Desa/Kel. Pejanggik, Kec. Mataram, Kota Mataram, Nusa Tenggara Barat', -8.5838443, 116.1121888, 0, '0895401288577', NULL, NULL, 40.00, 100.00, NULL, NULL, 'pending', '2026-08-16 00:12:42', 'HASIL_TEST_4e85cf1037e63c4ee6da.pdf', NULL, NULL, NULL, NULL),
(146, 24, '2026-08-16', 'melina', 32, 'Wanita', 'wirausaha', 0, NULL, 'Cek kemasan, label, izin edar, dan kedaluwarsa (CEK KLIK).\r\nMinum antibiotik sesuai aturan dan sampai tuntas.\r\nGunakan obat sesuai golongan dan aturan pakai.\r\nHindari penyalahgunaan obat.\r\nPilih kosmetik yang aman dan punya izin edar.\r\nWaspadai bahan berbahaya pada kosmetik.\r\nPilih jamu/obat tradisional yang aman dan tidak mengandung BKO.\r\nGunakan suplemen seperlunya, tidak berlebihan.\r\nCek legalitas produk lewat BPOM Mobile.\r\nBiasakan membaca label makanan sebelum membeli.', 'ruang teras', 'Desa/Kel. Pejanggik, Kec. Mataram, Kota Mataram, Nusa Tenggara Barat', -8.5838443, 116.1121888, 0, '081236556236', NULL, NULL, 90.00, 100.00, NULL, NULL, 'pending', '2026-08-16 00:15:56', 'HASIL_TEST_54e45ba6462f9580dfe9.pdf', NULL, NULL, NULL, NULL),
(147, 24, '2026-08-16', 'wanda', 35, 'Wanita', 'wirausaha', 0, NULL, 'Cek kemasan, label, izin edar, dan kedaluwarsa (CEK KLIK).\r\nMinum antibiotik sesuai aturan dan sampai tuntas.\r\nGunakan obat sesuai golongan dan aturan pakai.\r\nHindari penyalahgunaan obat.\r\nPilih kosmetik yang aman dan punya izin edar.\r\nWaspadai bahan berbahaya pada kosmetik.\r\nPilih jamu/obat tradisional yang aman dan tidak mengandung BKO.\r\nGunakan suplemen seperlunya, tidak berlebihan.\r\nCek legalitas produk lewat BPOM Mobile.\r\nBiasakan membaca label makanan sebelum membeli.', 'ruang teras', 'Desa/Kel. Pejanggik, Kec. Mataram, Kota Mataram, Nusa Tenggara Barat', -8.5838443, 116.1121888, 0, '081236214893', NULL, NULL, 90.00, 100.00, NULL, NULL, 'pending', '2026-08-16 00:19:23', 'HASIL_TEST_7d60d0b97c0e784afaef.pdf', NULL, NULL, NULL, NULL),
(148, 24, '2026-08-16', 'lalu masud', 32, 'Pria', 'wirausaha', 0, NULL, 'Cek kemasan, label, izin edar, dan kedaluwarsa (CEK KLIK).\r\nMinum antibiotik sesuai aturan dan sampai tuntas.\r\nGunakan obat sesuai golongan dan aturan pakai.\r\nHindari penyalahgunaan obat.\r\nPilih kosmetik yang aman dan punya izin edar.\r\nWaspadai bahan berbahaya pada kosmetik.\r\nPilih jamu/obat tradisional yang aman dan tidak mengandung BKO.\r\nGunakan suplemen seperlunya, tidak berlebihan.\r\nCek legalitas produk lewat BPOM Mobile.\r\nBiasakan membaca label makanan sebelum membeli.', 'ruang teras', 'Desa/Kel. Pejanggik, Kec. Mataram, Kota Mataram, Nusa Tenggara Barat', -8.5838443, 116.1121888, 0, '087754337727', NULL, NULL, 30.00, 100.00, NULL, NULL, 'pending', '2026-08-16 00:24:32', 'HASIL_TEST_5e551f0412f2e0909114.pdf', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_jawaban`
--

CREATE TABLE `detail_jawaban` (
  `id` int(11) NOT NULL,
  `hasil_test_id` int(11) NOT NULL,
  `pertanyaan_id` int(11) NOT NULL,
  `opsi_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `hasil_test`
--

CREATE TABLE `hasil_test` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bank_soal_id` int(11) NOT NULL,
  `nilai` decimal(5,2) NOT NULL,
  `jawaban_benar` int(11) NOT NULL,
  `total_pertanyaan` int(11) NOT NULL,
  `status_sertifikat` enum('belum','menunggu_ttd','disetujui') NOT NULL DEFAULT 'belum',
  `signed_by` int(11) DEFAULT NULL,
  `signed_at` datetime DEFAULT NULL,
  `is_manual` tinyint(1) DEFAULT 0,
  `catatan_manual` text DEFAULT NULL,
  `waktu_mulai` datetime NOT NULL,
  `waktu_selesai` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `sertifikat_file` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `hasil_test_soal`
--

CREATE TABLE `hasil_test_soal` (
  `id` int(11) NOT NULL,
  `hasil_test_id` int(11) NOT NULL,
  `pertanyaan_id` int(11) NOT NULL,
  `urutan` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `log_laporan`
--

CREATE TABLE `log_laporan` (
  `id` int(11) NOT NULL,
  `catatan_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `aksi` varchar(20) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `log_laporan`
--

INSERT INTO `log_laporan` (`id`, `catatan_id`, `user_id`, `aksi`, `keterangan`, `created_at`) VALUES
(11, 48, 12, 'buat', 'Membuat laporan untuk jamal', '2026-08-04 14:54:19'),
(12, 49, 12, 'buat', 'Membuat laporan untuk jamal', '2026-08-04 15:14:31'),
(13, 50, 12, 'buat', 'Membuat laporan untuk  i made jaya diarta', '2026-08-04 15:16:26'),
(14, 51, 12, 'buat', 'Membuat laporan untuk  i made jaya diarta', '2026-08-04 15:39:46'),
(15, 52, 12, 'buat', 'Membuat laporan untuk udin', '2026-08-04 15:40:45'),
(16, 53, 12, 'buat', 'Membuat laporan untuk udin', '2026-08-04 15:41:50'),
(19, 56, 17, 'buat', 'Membuat laporan untuk jumenah', '2026-08-05 06:21:18'),
(20, 57, 17, 'buat', 'Membuat laporan untuk tuak anam', '2026-08-05 06:42:15'),
(25, 53, 12, 'edit', 'Mengedit laporan: udin', '2026-08-05 12:53:32'),
(26, 66, 21, 'buat', 'Membuat laporan untuk udin', '2026-08-06 16:27:36'),
(27, 66, 21, 'edit', 'Mengedit laporan: jamal', '2026-08-06 16:36:12'),
(28, 67, 21, 'buat', 'Membuat laporan untuk udin', '2026-08-06 16:39:48'),
(29, 68, 23, 'buat', 'Membuat laporan untuk inak endun', '2026-08-12 06:30:21'),
(30, 69, 23, 'buat', 'Membuat laporan untuk inak menah', '2026-08-12 07:11:25'),
(32, 70, 3, 'buat', 'Mengirim laporan dari draft: Test Konsumen X Edit', '2026-08-13 00:45:34'),
(33, 70, 3, 'edit', 'Mengedit laporan: Test Konsumen X Edit', '2026-08-13 00:45:42'),
(39, NULL, 2, 'hapus', 'Menghapus laporan #78 (oleh agen)', '2026-08-13 09:48:38'),
(42, 82, 28, 'buat', 'Membuat laporan untuk Nurazisa', '2026-08-11 07:36:40'),
(43, 83, 28, 'buat', 'Membuat laporan untuk Hari', '2026-08-11 07:38:00'),
(44, 84, 28, 'buat', 'Membuat laporan untuk Mega Maya ', '2026-08-11 07:40:00'),
(45, 85, 28, 'buat', 'Membuat laporan untuk Melva Marshaniswa', '2026-08-11 07:41:26'),
(46, 86, 28, 'buat', 'Membuat laporan untuk Dini Karmila', '2026-08-11 07:42:37'),
(47, 87, 25, 'buat', 'Membuat laporan untuk Hj. Maryani ', '2026-08-12 08:58:03'),
(48, 88, 25, 'buat', 'Membuat laporan untuk A. Ridho Ramadhan', '2026-08-12 09:01:51'),
(49, 89, 25, 'buat', 'Membuat laporan untuk Sulatiyah', '2026-08-12 09:03:25'),
(50, 90, 25, 'buat', 'Membuat laporan untuk Siti Hadamah', '2026-08-12 09:05:06'),
(51, 91, 25, 'buat', 'Membuat laporan untuk Bu Sahra', '2026-08-12 09:07:03'),
(52, 92, 30, 'buat', 'Membuat laporan untuk Mahnun', '2026-08-13 05:24:22'),
(53, 93, 30, 'buat', 'Membuat laporan untuk HALIMATUL SADIAHI', '2026-08-13 05:27:30'),
(54, 94, 30, 'buat', 'Membuat laporan untuk NOVIANA', '2026-08-13 05:30:37'),
(55, 95, 30, 'buat', 'Membuat laporan untuk SUMARNI', '2026-08-13 05:33:13'),
(56, 96, 30, 'buat', 'Membuat laporan untuk BQ EMI KALSUM', '2026-08-13 05:36:21'),
(57, 97, 31, 'buat', 'Membuat laporan untuk Sinthja', '2026-08-13 23:40:36'),
(58, 98, 31, 'buat', 'Membuat laporan untuk Syarifa', '2026-08-13 23:48:02'),
(59, 99, 31, 'buat', 'Membuat laporan untuk Septin', '2026-08-13 23:58:27'),
(60, 100, 31, 'buat', 'Membuat laporan untuk Mariani', '2026-08-14 00:04:20'),
(61, 101, 31, 'buat', 'Membuat laporan untuk Khaeroni', '2026-08-14 00:12:33'),
(62, 101, 31, 'edit', 'Mengedit laporan: Khaeroni', '2026-08-14 00:14:43'),
(63, 100, 31, 'edit', 'Mengedit laporan: Mariani', '2026-08-14 00:15:00'),
(64, 99, 31, 'edit', 'Mengedit laporan: Septin', '2026-08-14 00:15:23'),
(65, 98, 31, 'edit', 'Mengedit laporan: Syarifa', '2026-08-14 00:15:41'),
(66, 102, 33, 'buat', 'Membuat laporan untuk Riyanti', '2026-08-14 05:01:22'),
(67, 103, 33, 'buat', 'Membuat laporan untuk Siti Aisya', '2026-08-14 05:03:17'),
(68, 104, 33, 'buat', 'Membuat laporan untuk Baiq Nuraini', '2026-08-14 05:04:41'),
(69, 105, 33, 'buat', 'Membuat laporan untuk Sri Megawati', '2026-08-14 05:05:37'),
(70, 106, 33, 'buat', 'Membuat laporan untuk Rahmi Dwi Pratiwi', '2026-08-14 05:07:28'),
(71, 107, 35, 'buat', 'Membuat laporan untuk Rabiatul aini', '2026-08-14 05:10:49'),
(72, 108, 29, 'buat', 'Membuat laporan untuk Muliana', '2026-08-14 05:10:51'),
(73, 109, 35, 'buat', 'Membuat laporan untuk Rabiatul aini', '2026-08-14 05:12:55'),
(74, 110, 29, 'buat', 'Membuat laporan untuk Netin', '2026-08-14 05:16:07'),
(75, 111, 35, 'buat', 'Membuat laporan untuk Rabiatul aini', '2026-08-14 05:16:31'),
(76, 112, 29, 'buat', 'Membuat laporan untuk Ernamawati', '2026-08-14 05:19:40'),
(77, 113, 29, 'buat', 'Membuat laporan untuk Ninda Tiarni Pusmawati', '2026-08-14 05:22:31'),
(78, 114, 29, 'buat', 'Membuat laporan untuk Ayu Apriliani', '2026-08-14 05:25:26'),
(79, 115, 35, 'buat', 'Membuat laporan untuk Siti zulaeha', '2026-08-14 05:28:03'),
(80, 116, 35, 'buat', 'Membuat laporan untuk Siti zulaeha', '2026-08-14 05:29:13'),
(81, 117, 35, 'buat', 'Membuat laporan untuk Ramlah', '2026-08-14 05:33:51'),
(82, 118, 35, 'buat', 'Membuat laporan untuk Ramlah', '2026-08-14 05:35:02'),
(83, 119, 35, 'buat', 'Membuat laporan untuk Nulen Meilani', '2026-08-14 05:39:18'),
(84, 120, 35, 'buat', 'Membuat laporan untuk Nulen Meilani', '2026-08-14 05:40:17'),
(85, 121, 35, 'buat', 'Membuat laporan untuk Nulen Meilani', '2026-08-14 05:42:14'),
(86, 122, 35, 'buat', 'Membuat laporan untuk Ramlah', '2026-08-14 05:45:50'),
(87, 123, 35, 'buat', 'Membuat laporan untuk Ramlah', '2026-08-14 05:48:28'),
(88, 124, 26, 'buat', 'Membuat laporan untuk Enun', '2026-08-14 06:50:34'),
(89, 125, 26, 'buat', 'Membuat laporan untuk Sri y', '2026-08-14 06:58:04'),
(90, 126, 26, 'buat', 'Membuat laporan untuk Dini Ariani', '2026-08-14 07:09:15'),
(91, 127, 34, 'buat', 'Membuat laporan untuk Wahida', '2026-08-14 07:12:15'),
(92, 128, 27, 'buat', 'Membuat laporan untuk Reni Irmatuzzohrah', '2026-08-14 07:17:24'),
(93, 129, 26, 'buat', 'Membuat laporan untuk Heni ', '2026-08-14 07:18:49'),
(94, 130, 27, 'buat', 'Membuat laporan untuk SAHIRUN', '2026-08-14 07:20:23'),
(95, 131, 27, 'buat', 'Membuat laporan untuk SAMIRAH', '2026-08-14 07:24:04'),
(96, 132, 26, 'buat', 'Membuat laporan untuk Hurin Ain', '2026-08-14 07:24:25'),
(97, 131, 27, 'edit', 'Mengedit laporan: SAMIRAH', '2026-08-14 07:25:36'),
(98, 126, 26, 'edit', 'Mengedit laporan: Dini Ariani', '2026-08-14 07:25:54'),
(99, 125, 26, 'edit', 'Mengedit laporan: Sri Maharni', '2026-08-14 07:26:34'),
(100, 133, 27, 'buat', 'Membuat laporan untuk SAHRAM', '2026-08-14 07:29:42'),
(101, 134, 34, 'buat', 'Membuat laporan untuk Baiq rujiah', '2026-08-14 07:30:42'),
(102, 135, 27, 'buat', 'Membuat laporan untuk RINI HAIRUL UMINAH', '2026-08-14 07:31:54'),
(103, 136, 34, 'buat', 'Membuat laporan untuk Siti', '2026-08-14 07:42:10'),
(104, 137, 34, 'buat', 'Membuat laporan untuk Sri megawati', '2026-08-14 07:47:00'),
(105, 138, 34, 'buat', 'Membuat laporan untuk Rahmi dwi pratiwi', '2026-08-14 07:53:53'),
(106, 139, 32, 'buat', 'Membuat laporan untuk Siti Nurjannah', '2026-08-14 08:41:56'),
(107, 140, 32, 'buat', 'Membuat laporan untuk Nur Linda', '2026-08-14 08:44:24'),
(108, 141, 32, 'buat', 'Membuat laporan untuk Winda Oktavia', '2026-08-14 08:45:49'),
(109, 142, 32, 'buat', 'Membuat laporan untuk Sabedah', '2026-08-14 08:47:13'),
(110, 143, 32, 'buat', 'Membuat laporan untuk Puji Utari', '2026-08-14 08:50:02'),
(111, 144, 24, 'buat', 'Membuat laporan untuk zahwa', '2026-08-16 00:07:02'),
(112, 145, 24, 'buat', 'Membuat laporan untuk Mahyani', '2026-08-16 00:12:42'),
(113, 146, 24, 'buat', 'Membuat laporan untuk melina', '2026-08-16 00:15:56'),
(114, 147, 24, 'buat', 'Membuat laporan untuk wanda', '2026-08-16 00:19:23'),
(115, 148, 24, 'buat', 'Membuat laporan untuk lalu masud', '2026-08-16 00:24:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role_target` varchar(20) DEFAULT NULL,
  `tipe` varchar(40) NOT NULL DEFAULT 'umum',
  `judul` varchar(200) NOT NULL,
  `pesan` text DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `ref_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `notifikasi`
--

INSERT INTO `notifikasi` (`id`, `user_id`, `role_target`, `tipe`, `judul`, `pesan`, `link`, `ref_id`, `is_read`, `created_at`) VALUES
(1, 1, NULL, 'test_selesai', 'minah menyelesaikan Pre-Test', 'Nilai: 100 (1/1 benar).', 'detail-hasil-test?id=19', 19, 0, '2026-08-12 07:09:58'),
(2, 6, NULL, 'test_selesai', 'minah menyelesaikan Pre-Test', 'Nilai: 100 (1/1 benar).', 'detail-hasil-test?id=19', 19, 0, '2026-08-12 07:09:58'),
(3, 14, NULL, 'test_selesai', 'minah menyelesaikan Pre-Test', 'Nilai: 100 (1/1 benar).', 'detail-hasil-test?id=19', 19, 0, '2026-08-12 07:09:58'),
(4, 1, NULL, 'test_selesai', 'minah menyelesaikan Post-Test', 'Nilai: 0 (0/1 benar).', 'detail-hasil-test?id=20', 20, 0, '2026-08-12 07:10:07'),
(5, 6, NULL, 'test_selesai', 'minah menyelesaikan Post-Test', 'Nilai: 0 (0/1 benar).', 'detail-hasil-test?id=20', 20, 0, '2026-08-12 07:10:07'),
(6, 14, NULL, 'test_selesai', 'minah menyelesaikan Post-Test', 'Nilai: 0 (0/1 benar).', 'detail-hasil-test?id=20', 20, 0, '2026-08-12 07:10:07'),
(7, 1, NULL, 'laporan_baru', 'Laporan baru dari minah', 'Agen mengirim laporan untuk konsumen: inak menah. Silakan review.', 'detail-catatan?id=69', 69, 0, '2026-08-12 07:11:25'),
(8, 6, NULL, 'laporan_baru', 'Laporan baru dari minah', 'Agen mengirim laporan untuk konsumen: inak menah. Silakan review.', 'detail-catatan?id=69', 69, 1, '2026-08-12 07:11:25'),
(9, 14, NULL, 'laporan_baru', 'Laporan baru dari minah', 'Agen mengirim laporan untuk konsumen: inak menah. Silakan review.', 'detail-catatan?id=69', 69, 1, '2026-08-12 07:11:25'),
(10, 23, NULL, 'laporan_approve', 'Laporan disetujui', 'Laporan untuk konsumen \"inak menah\" telah disetujui admin.', 'detail-catatan?id=69', 69, 0, '2026-08-12 23:20:42'),
(29, 1, NULL, 'test_selesai', 'Klise menyelesaikan Pre-Test', 'Nilai: 100 (1/1 benar).', 'detail-hasil-test?id=24', 24, 0, '2026-08-13 00:59:50'),
(30, 6, NULL, 'test_selesai', 'Klise menyelesaikan Pre-Test', 'Nilai: 100 (1/1 benar).', 'detail-hasil-test?id=24', 24, 1, '2026-08-13 00:59:50'),
(31, 14, NULL, 'test_selesai', 'Klise menyelesaikan Pre-Test', 'Nilai: 100 (1/1 benar).', 'detail-hasil-test?id=24', 24, 0, '2026-08-13 00:59:50'),
(35, 1, NULL, 'agen_baru', 'Agen baru mendaftar: AgenTest', 'ID Agen: TEMP · Email: tes@gmail.com', 'detail-agen?id=36', 36, 0, '2026-08-21 01:17:23'),
(36, 6, NULL, 'agen_baru', 'Agen baru mendaftar: AgenTest', 'ID Agen: TEMP · Email: tes@gmail.com', 'detail-agen?id=36', 36, 0, '2026-08-21 01:17:23'),
(37, 14, NULL, 'agen_baru', 'Agen baru mendaftar: AgenTest', 'ID Agen: TEMP · Email: tes@gmail.com', 'detail-agen?id=36', 36, 0, '2026-08-21 01:17:23');

-- --------------------------------------------------------

--
-- Struktur dari tabel `opsi_jawaban`
--

CREATE TABLE `opsi_jawaban` (
  `id` int(11) NOT NULL,
  `pertanyaan_id` int(11) NOT NULL,
  `teks_opsi` text NOT NULL,
  `adalah_benar` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `opsi_jawaban`
--

INSERT INTO `opsi_jawaban` (`id`, `pertanyaan_id`, `teks_opsi`, `adalah_benar`) VALUES
(1258, 317, 'Cek Kemasan, Kualitas, Izin Edar, dan Kedaluwarsa', 0),
(1259, 317, 'Cek Kemasan, Label, Izin Edar, dan Kedaluwarsa', 1),
(1260, 317, 'Cek Keamanan, Label, Informasi, dan Kedaluwarsa', 0),
(1261, 317, 'Cek Kemasan, Label, Informasi Komposisi, dan Kedaluwarsa', 0),
(1262, 318, 'Halo BPOM', 0),
(1263, 318, 'BPOM Mobile', 1),
(1264, 318, 'Cek KLIK', 0),
(1265, 318, 'SP4N-LAPOR!', 0),
(1266, 319, 'Menyampaikan bahwa produk telah terdaftar karena nomor izin edarnya ditemukan.', 0),
(1267, 319, 'Menyarankan masyarakat tetap menggunakan produk selama kemasannya masih utuh.', 0),
(1268, 319, 'Menjelaskan bahwa kesesuaian identitas produk dengan data resmi perlu diperhatikan dan mengarahkan masyarakat menggunakan kanal resmi apabila ditemukan ketidaksesuaian.', 1),
(1269, 319, 'Menyarankan masyarakat meminta penjual mengirimkan foto sertifikat izin edar sebagai bukti tambahan.', 0),
(1270, 320, '1500-533', 0),
(1271, 320, '0878-7150-0533', 1),
(1272, 320, '021-4244691', 0),
(1273, 320, '1500-911', 0),
(1274, 321, 'Instagram @bpom.mataram, Facebook bpom.mataram, dan YouTube Balai Besar POM di Mataram', 1),
(1275, 321, 'Instagram @bpom_ri, Facebook Badan POM RI, dan YouTube Badan POM RI', 0),
(1276, 321, 'Instagram @bpom.mataram, Facebook Badan POM RI, dan YouTube BPOM Mobile', 0),
(1277, 321, 'Instagram @halobpom, Facebook bpom.mataram, dan YouTube Cek BPOM', 0),
(1278, 322, 'Meminta masyarakat mengunggah produk tersebut ke media sosial agar menjadi perhatian publik.', 0),
(1279, 322, 'Meminta masyarakat menghubungi penjual terlebih dahulu dan menyelesaikan permasalahan secara pribadi.', 0),
(1280, 322, 'Mengarahkan masyarakat menggunakan kanal pengaduan resmi BPOM/BBPOM di Mataram dan membantu memberikan informasi yang diperlukan untuk pengaduan.', 1),
(1281, 322, 'Meminta masyarakat membawa produk tersebut kepada kader agar kader dapat menentukan apakah produk tersebut ilegal.', 0),
(1282, 323, 'Pernyataan tersebut dapat diterima karena kondisi kemasan merupakan indikator utama keamanan produk.', 0),
(1283, 323, 'Konsumen tetap perlu memastikan unsur Izin Edar, karena CEK KLIK mencakup seluruh unsur, bukan hanya kondisi fisik dan informasi pada kemasan.', 1),
(1284, 323, 'Nomor izin edar hanya perlu diperiksa untuk obat, sedangkan pangan dan kosmetik cukup diperiksa labelnya.', 0),
(1285, 323, 'Nomor izin edar dapat diabaikan apabila produk dibeli dari toko yang telah dikenal.', 0),
(1286, 324, 'Ya, karena seluruh produk yang beredar wajib langsung muncul pada hasil pencarian.', 0),
(1287, 324, 'Ya, selama nomor pada kemasan tidak dapat ditemukan dalam satu kali pencarian.', 0),
(1288, 324, 'Kader perlu membantu memastikan kembali data produk yang dicari dan, apabila terdapat dugaan masalah, mengarahkan masyarakat menggunakan kanal resmi BPOM untuk memperoleh informasi atau menyampaikan pengaduan.', 1),
(1289, 324, 'Tidak perlu melakukan tindakan apa pun selama produk memiliki label dan tanggal kedaluwarsa.', 0),
(1290, 325, 'Melakukan pemeriksaan terhadap produk dan menetapkan status ilegal berdasarkan hasil pengamatan.', 0),
(1291, 325, 'Menyita produk agar tidak kembali beredar di masyarakat.', 0),
(1292, 325, 'Memberikan edukasi, membantu masyarakat melakukan pengecekan melalui kanal resmi, serta mengarahkan pengaduan kepada pihak yang berwenang.', 1),
(1293, 325, 'Menghubungi penjual dan meminta produk ditarik dari peredaran atas nama BPOM.', 0),
(1294, 326, 'Ya, karena seluruh unsur CEK KLIK telah terpenuhi dan data produk telah ditemukan dalam sistem BPOM.', 0),
(1295, 326, 'Ya, karena adanya izin edar menunjukkan produk tersebut sesuai untuk digunakan oleh semua konsumen.', 0),
(1296, 326, 'Informasi tersebut menunjukkan produk telah melewati pemeriksaan dasar konsumen melalui CEK KLIK, tetapi masyarakat tetap perlu memperhatikan informasi penggunaan dan kesesuaian produk dengan kondisi dirinya.', 1),
(1297, 326, 'Belum dapat digunakan karena setiap kosmetik harus terlebih dahulu diuji secara mandiri oleh konsumen meskipun telah memiliki izin edar.', 0),
(1298, 327, 'Obat bebas karena dapat diperoleh masyarakat tanpa resep', 0),
(1299, 327, 'Obat bebas terbatas yang penggunaannya perlu memperhatikan peringatan khusus pada kemasan', 1),
(1300, 327, 'Obat keras yang hanya dapat digunakan berdasarkan resep dokter', 0),
(1301, 327, 'Obat yang penggunaannya tidak memerlukan perhatian khusus karena termasuk obat tanpa resep', 0),
(1302, 328, 'Tramadol, Trihexyphenidyl, dan Dextromethorphan.', 1),
(1303, 328, 'Parasetamol, Oralit, dan Vitamin C.', 0),
(1304, 328, 'Ibuprofen, Zinc, dan Antasida.', 0),
(1305, 328, 'Amoksisilin, CTM, dan Vitamin B Kompleks.', 0),
(1306, 329, 'Benar, karena izin edar menunjukkan obat dapat digunakan tanpa pengawasan tenaga kesehatan.', 0),
(1307, 329, 'Benar, selama konsumen mengikuti dosis yang tercantum pada kemasan.', 0),
(1308, 329, 'Tidak tepat, karena tanda tersebut menunjukkan obat keras sehingga penggunaannya harus mengikuti ketentuan dan pengawasan tenaga kesehatan.', 1),
(1309, 329, 'Tidak tepat hanya apabila obat tersebut digunakan oleh anak-anak.', 0),
(1310, 330, 'Tidak menimbulkan dampak apabila digunakan sesekali.', 0),
(1311, 330, 'Ketergantungan, kerusakan organ tubuh, hingga kematian.', 1),
(1312, 330, 'Nafsu makan meningkat dan tubuh menjadi lebih sehat.', 0),
(1313, 330, 'Tubuh menjadi kebal terhadap semua penyakit.', 0),
(1314, 331, 'DKL, GKL, dan DBL.', 0),
(1315, 331, 'MD, ML, dan PIRT.', 1),
(1316, 331, 'NA, NB, NC, dan ND.', 0),
(1317, 331, 'TR, TI, HT, dan FF.', 0),
(1318, 332, 'Obat yang memiliki izin edar berarti dapat digunakan secara bebas oleh siapa saja.', 0),
(1319, 332, 'Izin edar menunjukkan produk telah memperoleh persetujuan peredaran, sedangkan cara penggunaan tetap harus mengikuti ketentuan untuk golongan dan penggunaannya.', 0),
(1320, 332, 'Izin edar hanya menunjukkan bahwa produsen obat telah terdaftar, bukan produknya.', 1),
(1321, 332, 'Obat yang memiliki izin edar tidak perlu lagi diperhatikan tanggal kedaluwarsa dan aturan pakainya.', 0),
(1322, 333, 'Antibiotik dapat digunakan kembali selama obat belum kedaluwarsa.', 0),
(1323, 333, 'Antibiotik dapat digunakan apabila gejalanya sama dengan penyakit sebelumnya.', 1),
(1324, 333, 'Penggunaan antibiotik sebaiknya mengikuti diagnosis dan ketentuan tenaga kesehatan karena tidak semua penyakit memerlukan antibiotik.', 0),
(1325, 333, 'Antibiotik dapat digunakan terlebih dahulu dan dihentikan ketika gejala mulai membaik.', 0),
(1326, 334, 'Karena antibiotik hanya boleh digunakan pada penyakit yang disebabkan virus.', 0),
(1327, 334, 'Karena penggunaan yang tidak rasional dapat berkontribusi terhadap terjadinya resistensi antimikroba sehingga infeksi menjadi lebih sulit ditangani.', 1),
(1328, 334, 'Karena seluruh antibiotik memiliki efek samping yang sama pada setiap pengguna.', 0),
(1329, 334, 'Karena antibiotik tidak boleh digunakan lebih dari satu kali dalam kehidupan seseorang.', 0),
(1330, 335, 'Menjelaskan bahwa nomor izin edar pada foto sudah cukup untuk membuktikan legalitas produk.', 0),
(1331, 335, 'Meminta konsumen memastikan produk yang diterima sesuai dengan informasi izin edar dan melakukan verifikasi melalui kanal resmi BPOM.', 0),
(1332, 335, 'Menyarankan konsumen melihat jumlah ulasan positif sebelum menggunakan obat.', 1),
(1333, 335, 'Menyarankan konsumen membeli kembali dari penjual yang sama apabila tidak muncul efek samping.', 0),
(1334, 336, 'Hanya digunakan untuk mengetahui harga obat yang beredar.', 0),
(1335, 336, 'Digunakan untuk mengetahui apakah masyarakat cocok menggunakan suatu obat.', 0),
(1336, 336, 'Dapat membantu masyarakat memverifikasi produk yang terdaftar, mengecek izin edar, memperoleh informasi pengawasan, dan menyampaikan pengaduan.', 1),
(1337, 336, 'Digunakan oleh petugas BPOM untuk menggantikan pemeriksaan laboratorium terhadap obat.', 0),
(1338, 337, 'Mengikuti pilihan anggota keluarga karena obat tersebut sudah terbukti efektif.', 0),
(1339, 337, 'Memilih obat berdasarkan harga dan merek yang paling dikenal.', 0),
(1340, 337, 'Memperhatikan kondisi dan keluhan, membaca informasi pada kemasan/label, serta menggunakan obat sesuai aturan dan golongannya.', 1),
(1341, 337, 'Menggunakan obat dengan dosis lebih tinggi agar efeknya lebih cepat.', 0),
(1342, 338, 'Membaca aturan pakai sebelum menggunakan obat.', 0),
(1343, 338, 'Mengecek tanggal kedaluwarsa sebelum membeli obat.', 0),
(1344, 338, 'Menggunakan antibiotik milik anggota keluarga karena merasa gejalanya serupa.', 1),
(1345, 338, 'Mengecek izin edar obat menggunakan BPOM Mobile.', 0),
(1346, 339, 'Produk dapat langsung digunakan karena seluruh unsur CEK KLIK dan verifikasi BPOM Mobile telah terpenuhi.', 0),
(1347, 339, 'Produk dapat digunakan selama nomor izin edar dan tanggal kedaluwarsa sesuai dengan informasi pada kemasan.', 0),
(1348, 339, 'Verifikasi legalitas merupakan langkah penting, tetapi tidak otomatis berarti obat tersebut tepat digunakan untuk setiap keluhan; penggunaan tetap perlu memperhatikan indikasi, aturan pakai, dan kondisi pengguna.', 1),
(1349, 339, 'Produk sebaiknya tidak digunakan karena obat yang dibeli melalui media sosial tidak dapat dipastikan keamanannya meskipun terdaftar.', 0),
(1350, 340, 'Pangan aman terutama ditentukan oleh tidak adanya bahan kimia berbahaya.', 0),
(1351, 340, 'Keamanan pangan mencakup pengendalian cemaran biologis, kimia, dan aspek higiene-sanitasi sepanjang pangan ditangani.', 1),
(1352, 340, 'Pangan yang telah dimasak dapat dianggap aman karena proses pemanasan menghilangkan seluruh sumber bahaya.', 0),
(1353, 340, 'Pangan yang berasal dari bahan baku segar memiliki risiko keamanan lebih rendah dibandingkan pangan olahan.', 0),
(1354, 341, 'Ikan segar – nasi goreng – mi instan', 1),
(1355, 341, 'Kerupuk – bakso – ikan segar', 0),
(1356, 341, 'Mi basah – buah potong – daging segar', 0),
(1357, 341, 'Nasi kotak – ikan segar – tahu mentah', 0),
(1358, 342, 'Formalin terutama digunakan untuk meningkatkan kekenyalan, sedangkan boraks berfungsi mempertahankan warna pangan.', 0),
(1359, 342, 'Formalin dan boraks merupakan bahan yang dapat disalahgunakan dalam pangan, antara lain untuk memberikan karakteristik tertentu atau memperpanjang daya simpan.', 1),
(1360, 342, 'Formalin dan boraks merupakan BTP yang dapat digunakan pada semua jenis pangan selama jumlahnya dibatasi.', 0),
(1361, 342, 'Formalin lebih sering digunakan pada pangan bertekstur kenyal, sedangkan boraks terutama digunakan sebagai pewarna.', 0),
(1362, 343, 'Mi basah, tahu, daging segar, ikan segar dan ikan asin', 1),
(1363, 343, 'Bakso, cilok, lontong, kerupuk gendar', 0),
(1364, 343, 'Kerupuk, terasi, gulali, sirup merah', 0),
(1365, 343, 'Mi instan, biskuit, susu UHT, minuman serbuk', 0),
(1366, 344, 'Mi basah, tahu, ikan asin, daging segar', 0),
(1367, 344, 'Bakso, cilok, lontong, kerupuk gendar', 1),
(1368, 344, 'Kerupuk, terasi, gulali, dan sirup berwarna merah', 0),
(1369, 344, 'Tahu, mi basah, terasi, dan ikan segar', 0),
(1370, 345, 'Tekstur sangat lunak, mudah putus, dan cepat kehilangan kerenyahan.', 0),
(1371, 345, 'Tekstur sangat kenyal, tidak cepat putus, terasa getir, dan renyah.', 1),
(1372, 345, 'Warna lebih pucat, mudah hancur, dan tidak memiliki rasa.', 0),
(1373, 345, 'Permukaan berlendir, berbau tajam, dan mengalami perubahan warna.', 0),
(1374, 346, 'meningkatkan rasa manis dan mempertahankan warna pangan;', 0),
(1375, 346, 'meningkatkan daya awet dan membuat tekstur pangan lebih kompak/kenyal;', 1),
(1376, 346, 'mempercepat proses pematangan dan meningkatkan aroma;', 0),
(1377, 346, 'meningkatkan nilai gizi dan mempertahankan kadar air pangan.', 0),
(1378, 347, 'cemaran mikrobiologis pada pangan;', 0),
(1379, 347, 'bahan tambahan pangan yang penggunaannya harus dibatasi;', 0),
(1380, 347, 'bahan berbahaya yang disalahgunakan dalam pangan;', 1),
(1381, 347, 'bahan penolong yang digunakan dalam proses pengolahan pangan.', 0),
(1382, 348, 'Setiap BTP merupakan bahan berbahaya apabila masuk ke dalam pangan.', 0),
(1383, 348, 'Penggunaan BTP menjadi masalah keamanan pangan apabila digunakan melebihi batas maksimal yang diizinkan.', 1),
(1384, 348, 'BTP hanya digunakan untuk memberikan warna sehingga tidak berkaitan dengan keamanan pangan.', 0),
(1385, 348, 'BTP tidak termasuk dalam pengawasan keamanan pangan selama digunakan pada pangan olahan.', 0),
(1386, 349, '(1), (2), dan (3)', 0),
(1387, 349, '(1), (3), dan (4)', 0),
(1388, 349, '(2), (3), dan (5)', 0),
(1389, 349, '(1), (2), (3), dan (4)', 1),
(1390, 350, 'P-IRT diikuti 15 Digit Angka', 0),
(1391, 350, 'MD/ML diikuti 15 digit angka', 1),
(1392, 350, 'P-IRT diikuti 12 Digit Angka', 0),
(1393, 350, 'MD/ML diikuti 12 Digit Angka', 0),
(1394, 351, 'Boraks, Formalin, Natrium Benzoat, dan Rhodamin B', 0),
(1395, 351, 'Formalin, Rhodamin B, Tartrazin, dan Boraks', 0),
(1396, 351, 'Boraks, Formalin, Rhodamin B, dan Methanyl Yellow', 1),
(1397, 351, 'Boraks, Kalium Sorbat, Formalin, dan Rhodamin B', 0),
(1398, 352, 'Roti tawar, biskuit, dan susu UHT', 0),
(1399, 352, 'Keripik, cokelat, dan wafer', 0),
(1400, 352, 'Nugget, sosis, dan cilok', 0),
(1401, 352, 'Mie basah, kerupuk, dan pencok nasi', 1),
(1402, 353, 'Gangguan pencernaan sementara tanpa efek jangka panjang', 0),
(1403, 353, 'Meningkatkan daya tahan tubuh', 0),
(1404, 353, 'Gangguan fungsi hati, ginjal, dan meningkatkan risiko kanker', 1),
(1405, 353, 'Menambah masa simpan makanan tanpa risiko kesehatan', 0),
(1406, 354, 'MD untuk makanan tradisional dan ML untuk makanan modern', 0),
(1407, 354, 'MD untuk pangan produksi dalam negeri dan ML untuk pangan impor', 1),
(1408, 354, 'MD menunjukkan produk halal, sedangkan ML menunjukkan produk impor', 0),
(1409, 354, 'MD digunakan untuk makanan siap saji dan ML untuk makanan beku', 0),
(1410, 355, 'Kode huruf dan 15 digit angka.', 1),
(1411, 355, 'Kode huruf dan 9 digit angka.', 0),
(1412, 355, 'Kode huruf dan 12 digit angka.', 0),
(1413, 355, 'Kode huruf dan 10 digit angka.', 0),
(1414, 356, 'Produk dapat dianggap aman karena memiliki banyak pengguna.', 0),
(1415, 356, 'Produk belum dapat dinyatakan legal hanya berdasarkan popularitas dan testimoni; status legalitas perlu diverifikasi melalui kanal resmi BPOM.', 1),
(1416, 356, 'Produk dapat digunakan selama tidak menimbulkan efek samping pada penggunaan pertama.', 0),
(1417, 356, 'Produk dapat dianggap legal apabila penjual dapat menunjukkan bukti pembelian dari distributor.', 0),
(1418, 357, 'Merkuri, Hidrokuinon, dan Asam Retinoat', 1),
(1419, 357, 'Merkuri, Niacinamide, dan Asam Retinoat', 0),
(1420, 357, 'Hidrokuinon, Gliserin, dan Merkuri', 0),
(1421, 357, 'Asam Retinoat, Vitamin E, dan Merkuri', 0),
(1422, 358, 'Kulit menjadi lebih cerah secara permanen tanpa efek samping.', 0),
(1423, 358, 'Kerusakan kulit, ginjal, sistem saraf, dan dapat membahayakan janin.', 1),
(1424, 358, 'Hanya menyebabkan kulit menjadi kering.', 0),
(1425, 358, 'Aman digunakan selama tidak setiap hari.', 0),
(1426, 359, 'NB', 0),
(1427, 359, 'NC', 0),
(1428, 359, 'NA', 1),
(1429, 359, 'NE', 0),
(1430, 360, 'Membeli produk yang sedang viral di media sosial.', 0),
(1431, 360, 'Memilih produk dengan harga paling mahal.', 0),
(1432, 360, 'Memastikan produk memenuhi prinsip CEK KLIK dan memiliki izin edar BPOM.', 1),
(1433, 360, 'Membeli produk yang digunakan oleh influencer terkenal.', 0),
(1434, 361, 'Izin edar merupakan salah satu informasi penting dalam memastikan legalitas kosmetik, tetapi konsumen tetap perlu memperhatikan label, cara penggunaan, dan kondisi produk.', 1),
(1435, 361, 'Adanya izin edar berarti kosmetik pasti cocok digunakan oleh setiap orang tanpa memperhatikan kondisi kulit.', 0),
(1436, 361, 'Kosmetik yang memiliki izin edar tidak perlu lagi diperiksa tanggal kedaluwarsanya.', 0),
(1437, 361, 'Izin edar menunjukkan bahwa kosmetik memiliki khasiat medis yang sama dengan obat.', 0),
(1438, 362, 'Menganggap nomor tersebut valid karena format nomornya sudah tercetak pada kemasan.', 0),
(1439, 362, 'Mengabaikan perbedaan tersebut selama produk dijual oleh toko yang memiliki banyak pembeli.', 0),
(1440, 362, 'Menganggap terdapat ketidaksesuaian yang perlu ditindaklanjuti melalui kanal resmi BPOM sebelum produk digunakan atau dibeli.', 1),
(1441, 362, 'Meminta penjual mengganti foto produk dengan foto kemasan yang lebih jelas.', 0),
(1442, 363, 'Gliserin, kolagen, dan aloe vera', 0),
(1443, 363, 'Vitamin E, niacinamide, dan asam hialuronat', 0),
(1444, 363, 'Merkuri, hidrokuinon, dan merah K3', 1),
(1445, 363, 'Air, alkohol, dan bahan pelembap', 0),
(1446, 364, '“Terdaftar BPOM dan digunakan sesuai petunjuk.”', 0),
(1447, 364, '“Membantu menjaga kelembapan kulit.”', 0),
(1448, 364, '“Memutihkan kulit secara instan dalam tiga hari tanpa efek samping.”', 1),
(1449, 364, '“Mengandung bahan pelembap untuk membantu menjaga kondisi kulit.”', 0),
(1450, 365, 'Memeriksa rating penjual → melihat jumlah ulasan → membandingkan harga → membeli.', 0),
(1451, 365, 'Memeriksa klaim produk → melihat jumlah pengikut penjual → memastikan kemasan menarik → membeli.', 0),
(1452, 365, 'Menerapkan CEK KLIK, memverifikasi izin edar melalui kanal resmi BPOM, kemudian mempertimbangkan informasi label dan penggunaan produk.', 1),
(1453, 365, 'Meminta penjual menjamin keamanan produk → menyimpan percakapan → membeli apabila penjual memberikan garansi.', 0),
(1454, 366, 'Kosmetik impor tidak perlu diverifikasi karena telah melalui pengawasan negara asal.', 0),
(1455, 366, 'Kosmetik impor dapat langsung digunakan apabila memiliki label berbahasa asing dan dijual melalui marketplace resmi.', 0),
(1456, 366, 'Asal produk bukan merupakan pengganti verifikasi legalitas; konsumen tetap perlu memastikan status peredaran produk sesuai ketentuan yang berlaku di Indonesia.', 1),
(1457, 366, 'Kosmetik impor selalu memiliki risiko lebih tinggi dibandingkan kosmetik produksi dalam negeri.', 0),
(1458, 367, 'Kemasan menarik – label berwarna – harga terjangkau – banyak ulasan', 0),
(1459, 367, 'Kemasan baik – informasi label diperhatikan – izin edar diverifikasi – kedaluwarsa diperiksa', 1),
(1460, 367, 'Kemasan utuh – influencer terkenal – izin edar tercetak – harga sesuai pasaran', 0),
(1461, 367, 'Label lengkap – penjual terpercaya – produk viral – tanggal produksi diketahui', 0),
(1462, 368, 'Produk dapat diverifikasi pada sistem resmi BPOM.', 0),
(1463, 368, 'Informasi pada label dapat dibaca dan diperiksa.', 0),
(1464, 368, 'Produk memiliki banyak testimoni positif dari pengguna media sosial.', 1),
(1465, 368, 'Kondisi kemasan dan tanggal kedaluwarsa diperiksa sebelum digunakan.', 0),
(1466, 369, 'Produk A paling aman karena memiliki izin edar, sedangkan klaim tidak memengaruhi penilaian konsumen.', 0),
(1467, 369, 'Produk B dapat dipertimbangkan karena bahan alami merupakan indikator utama keamanan kosmetik.', 0),
(1468, 369, 'Produk C menunjukkan kombinasi informasi yang lebih sesuai untuk dipertimbangkan konsumen, sedangkan produk A tetap perlu dicermati informasi/klaimnya dan produk B memiliki aspek legalitas yang perlu dipertanyakan.', 1),
(1469, 369, 'Produk A dan C sama-sama dapat langsung digunakan tanpa pemeriksaan lebih lanjut karena keduanya memiliki nomor izin edar.', 0),
(1470, 370, 'Produk yang seluruh bahan penyusunnya berasal dari tumbuhan dan digunakan hanya berdasarkan pengalaman turun-temurun.', 0),
(1471, 370, 'Bahan, ramuan bahan, atau produk yang berasal dari sumber daya alam dan digunakan untuk pemeliharaan, peningkatan, pencegahan, pengobatan, dan/atau pemulihan kesehatan berdasarkan pembuktian empiris dan/atau ilmiah.', 1),
(1472, 370, 'Produk berbahan alami yang telah melalui uji klinik dan dapat digunakan sebagai pengganti obat modern.', 0),
(1473, 370, 'Semua produk berbahan tumbuhan yang dikonsumsi untuk menjaga kesehatan tanpa memerlukan pembuktian khasiat.', 0),
(1474, 371, 'Parasetamol, Vitamin C, Zinc, dan Sildenafil.', 0),
(1475, 371, 'Fenilbutazon, Curcumin, Jahe, dan Sildenafil.', 0),
(1476, 371, 'Deksametason, Sildenafil, Fenilbutazon, dan Parasetamol.', 1),
(1477, 371, 'Deksametason, Jahe, Temulawak, dan Parasetamol.', 0),
(1478, 372, 'Tetap mengonsumsi karena efeknya cepat dirasakan.', 0),
(1479, 372, 'Mengurangi dosis agar lebih aman.', 0),
(1480, 372, 'Menghentikan penggunaan, menyarankan membeli produk berizin edar BPOM, serta melaporkan bila diduga produk ilegal', 1),
(1481, 372, 'Mengonsumsi bersama vitamin agar efek samping berkurang.', 0),
(1482, 373, 'Jamu berbasis empiris, sedangkan OHT dan Fitofarmaka memiliki tingkat pembuktian dan standardisasi yang lebih tinggi.', 1),
(1483, 373, 'Jamu dan OHT dibuktikan secara empiris, sedangkan Fitofarmaka hanya dibedakan berdasarkan bentuk sediaannya.', 0),
(1484, 373, 'Jamu, OHT, dan Fitofarmaka memiliki tingkat pembuktian yang sama, tetapi menggunakan bahan baku berbeda.', 0),
(1485, 373, 'OHT dan Fitofarmaka hanya dibedakan berdasarkan logo, sedangkan pembuktian khasiatnya sama.', 0),
(1486, 374, 'Diawali dengan POM TR/TI/TL/HT/FF/QD/QI/QL dan diikuti 9 digit angka.', 1),
(1487, 374, 'Diawali dengan POM MD/ML dan diikuti 12 digit angka.', 0),
(1488, 374, 'Diawali dengan NIE dan diikuti kode produsen serta nomor registrasi.', 0),
(1489, 374, 'Diawali dengan TR kemudian diikuti nomor batch produk.', 0),
(1490, 375, 'BKO merupakan salah satu kelompok bahan alam yang dapat ditambahkan untuk memperkuat khasiat jamu.', 0),
(1491, 375, 'BKO merupakan zat kimia obat yang ditambahkan secara ilegal ke produk herbal dan tidak dicantumkan pada label.', 1),
(1492, 375, 'BKO diperbolehkan dalam jamu selama jumlahnya lebih rendah daripada dosis obat modern.', 0),
(1493, 375, 'BKO hanya menjadi masalah apabila menyebabkan perubahan warna atau rasa pada produk.', 0),
(1494, 376, 'Jamu pada dasarnya memiliki efek yang selalu lambat sehingga efek cepat menunjukkan produk tidak mengandung bahan alam.', 0),
(1495, 376, 'Efek cepat dapat menjadi indikasi adanya bahan kimia obat yang ditambahkan secara ilegal untuk menghasilkan efek tertentu secara instan.', 1),
(1496, 376, 'Efek cepat menunjukkan bahwa produk telah melalui uji klinik dan memiliki pembuktian ilmiah yang kuat.', 0),
(1497, 376, 'Efek cepat menunjukkan kandungan bahan aktif alami dalam jamu berada pada konsentrasi yang lebih tinggi.', 0),
(1498, 377, 'Konsumen tidak dapat mengetahui keberadaan BKO hanya dari warna dan rasa produk karena BKO sengaja tidak dicantumkan pada label.', 1),
(1499, 377, 'BKO selalu menyebabkan perubahan warna sehingga mudah dikenali dari kemasan.', 0),
(1500, 377, 'BKO dapat dicantumkan pada label selama produsen menggunakan istilah bahan aktif alami.', 0),
(1501, 377, 'Keberadaan BKO tidak menjadi masalah apabila produk memiliki nomor izin edar.', 0),
(1502, 378, 'Memastikan produk berasal dari tanaman yang telah dikenal secara turun-temurun.', 0),
(1503, 378, 'Memastikan produk memiliki kemasan menarik dan mencantumkan banyak manfaat kesehatan.', 0),
(1504, 378, 'Memeriksa informasi pada kemasan serta melakukan verifikasi izin edar melalui kanal resmi BPOM.', 1),
(1505, 378, 'Memastikan produk telah memiliki banyak testimoni dan dijual oleh toko yang telah lama beroperasi.', 0),
(1506, 379, 'Produk berbahan alami pada dasarnya tidak memiliki risiko karena berasal dari tumbuhan.', 0),
(1507, 379, 'Produk yang disebut herbal tidak memerlukan perhatian terhadap izin edar selama digunakan secara tradisional.', 0),
(1508, 379, 'Asal bahan dari alam tidak dengan sendirinya menjamin keamanan produk; legalitas dan informasi produk tetap perlu diperhatikan.', 1),
(1509, 379, 'Produk tradisional lebih aman daripada obat modern karena telah digunakan turun-temurun.', 0),
(1510, 380, 'Menentukan apakah suatu produk memberikan efek terapi yang sesuai dengan kondisi kesehatan pengguna.', 0),
(1511, 380, 'Memverifikasi produk yang terdaftar di BPOM melalui pemindaian 2D barcode serta menyampaikan keluhan atau masalah produk.', 1),
(1512, 380, 'Menggantikan konsultasi tenaga kesehatan sebelum mengonsumsi produk kesehatan.', 0),
(1513, 380, 'Menentukan dosis penggunaan produk berdasarkan usia dan berat badan.', 0),
(1514, 381, 'Pernyataan 1 dan 2 saja', 0),
(1515, 381, 'Pernyataan 1, 2, dan 3 saja', 0),
(1516, 381, 'Pernyataan 2, 3, dan 4 saja', 0),
(1517, 381, 'Pernyataan 1, 2, 3, dan 4', 1),
(1518, 382, 'Memastikan informasi izin edar dapat diverifikasi dan sesuai dengan produk yang diperiksa', 1),
(1519, 382, 'Memastikan produk memiliki banyak ulasan positif', 0),
(1520, 382, 'Memastikan harga produk tidak jauh berbeda dengan produk sejenis', 0),
(1521, 382, 'Memastikan produk dipromosikan oleh penjual resmi', 0),
(1522, 383, 'Melakukan pemeriksaan dan menetapkan produk sebagai ilegal', 0),
(1523, 383, 'Menyita produk agar tidak digunakan masyarakat', 0),
(1524, 383, 'Memberikan edukasi, membantu masyarakat melakukan pengecekan, dan mengarahkan pengaduan melalui kanal resmi BPOM', 1),
(1525, 383, 'Mengunggah identitas penjual agar masyarakat mengetahui pihak yang menjual produk', 0),
(1526, 384, 'Meminta masyarakat mengirim foto produk → kader menentukan aman/tidak → produk digunakan', 0),
(1527, 384, 'Mengajarkan CEK KLIK → membantu masyarakat melakukan verifikasi izin edar melalui BPOM Mobile → menjelaskan pentingnya membaca informasi produk → mengarahkan pengaduan apabila ditemukan dugaan masalah', 1),
(1528, 384, 'Meminta masyarakat melihat testimoni → mengecek harga → membandingkan dengan produk lain', 0),
(1529, 384, 'Meminta masyarakat menghubungi penjual → meminta bukti izin edar → menggunakan produk apabila penjual memberikan jawaban', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pertanyaan`
--

CREATE TABLE `pertanyaan` (
  `id` int(11) NOT NULL,
  `bank_soal_id` int(11) NOT NULL,
  `teks_pertanyaan` text NOT NULL,
  `kategori` enum('umum','komoditi_pangan','kosmetik','obat_bahan_alam','obat') DEFAULT 'umum',
  `urutan` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pertanyaan`
--

INSERT INTO `pertanyaan` (`id`, `bank_soal_id`, `teks_pertanyaan`, `kategori`, `urutan`) VALUES
(317, 31, 'Seorang kader akan menjelaskan kepada masyarakat cara sederhana memilih produk obat dan makanan sebelum membeli. Kepanjangan CEK KLIK yang tepat adalah...', 'umum', 1),
(318, 31, 'Seorang warga membeli kosmetik secara daring dan ingin memastikan apakah produk tersebut memiliki izin edar BPOM. Aplikasi yang paling tepat diperkenalkan oleh Agen GAS-PAMAN adalah...', 'umum', 2),
(319, 31, 'Hasil pencarian suatu produk pada BPOM Mobile menunjukkan adanya nomor izin edar yang sama dengan yang tercetak pada kemasan. Namun, nama produk dan produsennya berbeda. Sikap kader yang paling tepat adalah...', 'umum', 3),
(320, 31, 'Masyarakat di wilayah kerja BBPOM di Mataram ingin memperoleh informasi atau menyampaikan pengaduan terkait obat dan makanan. Nomor layanan BBPOM di Mataram yang dapat digunakan adalah...', 'umum', 4),
(321, 31, 'Seorang kader ingin mengarahkan masyarakat mengikuti informasi dan edukasi yang dipublikasikan khusus oleh BBPOM di Mataram. Kombinasi media sosial yang tepat adalah...', 'umum', 5),
(322, 31, 'Seorang masyarakat menemukan produk obat yang diduga tidak memiliki izin edar dan ingin menyampaikan laporan. Sebagai Agen GAS-PAMAN, tindakan yang paling tepat adalah...', 'umum', 6),
(323, 31, 'Seorang konsumen berkata: “Kemasan produk saya sudah bagus, labelnya lengkap, dan belum kedaluwarsa. Jadi saya tidak perlu mengecek nomor izin edar.” Respons Agen GAS-PAMAN yang paling tepat adalah...', 'umum', 7),
(324, 31, 'Dalam edukasi GAS-PAMAN, seorang masyarakat bertanya: “Kalau produk tidak muncul saat saya cari di BPOM Mobile, berarti produk itu pasti palsu, kan?” Jawaban kader yang paling tepat adalah...', 'umum', 8),
(325, 31, 'Manakah tindakan yang paling sesuai dengan batas peran Agen GAS-PAMAN ketika menemukan dugaan produk obat dan makanan yang tidak memenuhi ketentuan? “Berarti saya tinggal beli dan gunakan saja?”', 'umum', 9),
(326, 31, 'Seorang kader GAS-PAMAN sedang memberikan edukasi kepada masyarakat mengenai kosmetik yang dibeli melalui media sosial. Masyarakat menunjukkan produk dengan kondisi kemasan utuh, label lengkap, nomor izin edar tercantum, dan tanggal kedaluwarsa masih berlaku. Kader kemudian membantu melakukan pengecekan melalui BPOM Mobile dan menemukan data produk yang sesuai. Setelah itu masyarakat bertanya: Jawaban kader yang paling tepat adalah...', 'umum', 10),
(327, 31, 'Seorang konsumen menemukan obat dengan tanda lingkaran biru dengan tepi hitam pada kemasannya. Berdasarkan klasifikasi obat, produk tersebut termasuk...', 'obat', 11),
(328, 31, 'Obat yang paling sering disalahgunakan oleh remaja adalah ....', 'obat', 12),
(329, 31, 'Pada kemasan suatu obat terdapat tanda huruf K dalam lingkaran merah dengan garis tepi hitam. Seorang konsumen mengatakan obat tersebut aman digunakan tanpa konsultasi karena obat tersebut sudah memiliki izin edar. Tanggapan yang paling tepat adalah...', 'obat', 13),
(330, 31, 'Penyalahgunaan obat dalam jangka panjang dapat menyebabkan ....', 'obat', 14),
(331, 31, 'Kode nomor izin edar yang benar untuk Obat Bahan Alam adalah ....', 'obat', 15),
(332, 31, 'Pernyataan berikut yang paling tepat mengenai hubungan izin edar dan penggunaan obat adalah... “Dulu obat ini menyembuhkan saya, jadi sekarang pasti cocok juga.”', 'obat', 16),
(333, 31, 'Seorang anggota keluarga mengalami pilek selama dua hari dan meminta antibiotik yang masih tersisa dari resep sebelumnya. Ia beralasan: Sebagai Agen GAS-PAMAN, respons yang paling tepat adalah...', 'obat', 17),
(334, 31, 'Mengapa penggunaan antibiotik secara tidak tepat menjadi perhatian dalam edukasi GAS-PAMAN?', 'obat', 18),
(335, 31, 'Seorang masyarakat membeli obat melalui marketplace. Penjual mencantumkan foto kemasan dengan nomor izin edar BPOM sehingga konsumen menganggap produk tersebut aman. Sebagai kader, langkah edukasi yang paling tepat adalah...', 'obat', 19),
(336, 31, 'Manakah pernyataan yang paling lengkap mengenai pemanfaatan BPOM Mobile dalam konteks edukasi obat?', 'obat', 20),
(337, 31, 'Seorang masyarakat mengalami sakit kepala ringan dan ingin membeli obat sendiri. Ia langsung memilih obat berdasarkan merek yang pernah digunakan anggota keluarganya. Sebagai Agen GAS-PAMAN, edukasi yang paling tepat adalah...', 'obat', 21),
(338, 31, 'Manakah perilaku berikut yang paling berisiko menunjukkan penggunaan obat yang tidak rasional?', 'obat', 22),
(339, 31, 'Seorang kader menemukan kasus berikut: Ibu A membeli obat melalui media sosial. Kemasan obat masih baik, terdapat nomor izin edar, dan tanggal kedaluwarsa belum lewat. Ketika kader membantu mengecek melalui BPOM Mobile, produk dapat ditemukan dalam database. Ibu A kemudian mengatakan bahwa ia akan menggunakan obat tersebut untuk mengobati keluhan yang sama seperti yang pernah dialami tetangganya. Pernyataan kader yang paling tepat adalah...', 'obat', 23),
(340, 31, 'Pernyataan yang paling tepat mengenai keamanan pangan adalah...', 'komoditi_pangan', 24),
(341, 31, 'Berdasarkan materi, kelompok pangan yang termasuk dalam kategori pangan segar, pangan siap saji, dan pangan olahan secara berurutan adalah...', 'komoditi_pangan', 25),
(342, 31, 'Pernyataan yang paling tepat mengenai formalin dan boraks pada pangan adalah...', 'komoditi_pangan', 26),
(343, 31, 'Berdasarkan materi, kombinasi pangan yang paling tepat dikaitkan dengan formalin adalah...', 'komoditi_pangan', 27),
(344, 31, 'Kombinasi pangan yang dalam materi dikaitkan dengan boraks adalah...', 'komoditi_pangan', 28),
(345, 31, 'Ciri berikut yang paling konsisten dengan karakteristik pangan yang dicurigai mengandung boraks berdasarkan materi adalah...', 'komoditi_pangan', 29),
(346, 31, 'Penggunaan boraks pada pangan dapat memberikan karakteristik tertentu pada produk. Tujuan penyalahgunaan boraks yang disebutkan dalam materi adalah...', 'komoditi_pangan', 30),
(347, 31, 'Dalam materi, formalin, boraks, Rhodamin B, dan Methanyl Yellow ditempatkan sebagai contoh...', 'komoditi_pangan', 31),
(348, 31, 'Pernyataan berikut mengenai BTP yang paling sesuai dengan materi adalah... (1) Formalin → mi basah (2) Boraks → bakso (3) Boraks → kerupuk tempe (4) Formalin → tahu (5) Pewarna tekstil → kerupuk dan terasi', 'komoditi_pangan', 32),
(349, 31, 'Perhatikan pasangan berikut: Pasangan yang sesuai dengan materi adalah...', 'komoditi_pangan', 33),
(350, 31, 'Nomor izin edar pangan olahan yang diterbitkan BPOM terdiri atas ....', 'komoditi_pangan', 34),
(351, 31, 'Manakah yang merupakan bahan kimia yang dilarang ditambahkan ke dalam pangan?', 'komoditi_pangan', 35),
(352, 31, 'Produk pangan yang paling sering ditemukan mengandung boraks adalah .... Natrium 300 mg per sajian Jumlah sajian per kemasan 4', 'komoditi_pangan', 36),
(353, 31, 'Penggunaan formalin pada pangan dalam jangka panjang dapat menyebabkan .... Jika seluruh kemasan dikonsumsi sekaligus, berapa natrium yang masuk ke tubuh?', 'komoditi_pangan', 37),
(354, 31, 'Manakah pernyataan yang benar mengenai nomor izin edar pangan?', 'komoditi_pangan', 38),
(355, 31, 'Setelah edukasi, masyarakat mulai mengecek izin edar produk melalui BPOM Mobile sebelum membeli. Namun mereka masih sering membeli pangan siap saji yang tidak higienis. Berdasarkan tujuan GAS-PAMAN, kondisi ini menunjukkan bahwa...', 'komoditi_pangan', 39),
(356, 31, 'Suatu kosmetik memiliki kemasan menarik, dipromosikan oleh influencer, dan banyak mendapat ulasan positif. Namun, ketika dilakukan pengecekan, produk tersebut tidak ditemukan dalam data produk yang terdaftar pada BPOM. Kesimpulan yang paling tepat adalah...', 'kosmetik', 40),
(357, 31, 'Bahan berikut yang dilarang digunakan dalam kosmetik adalah ....', 'kosmetik', 41),
(358, 31, 'Dampak penggunaan kosmetik yang mengandung merkuri dalam jangka panjang adalah ....', 'kosmetik', 42),
(359, 31, 'Kode yang terdapat pada awal nomor notifikasi kosmetik yang diproduksi di Thailand adalah ....', 'kosmetik', 43),
(360, 31, 'Langkah paling tepat saat membeli kosmetik agar terhindar dari produk ilegal adalah ....', 'kosmetik', 44),
(361, 31, 'Manakah pernyataan yang paling tepat mengenai izin edar kosmetik?', 'kosmetik', 45),
(362, 31, 'Dalam melakukan pengecekan kosmetik, seseorang menemukan nomor izin edar pada kemasan, tetapi hasil pencarian menunjukkan nama produk atau pemilik nomor tersebut tidak sesuai. Tindakan yang paling tepat adalah...', 'kosmetik', 46),
(363, 31, 'Dalam edukasi keamanan kosmetik, bahan berikut perlu mendapat perhatian karena dapat ditemukan pada kosmetik ilegal:', 'kosmetik', 47),
(364, 31, 'Pernyataan berikut yang paling perlu dicermati oleh konsumen ketika memilih kosmetik adalah...', 'kosmetik', 48),
(365, 31, 'Manakah urutan tindakan yang paling tepat ketika konsumen hendak membeli kosmetik melalui marketplace?', 'kosmetik', 49),
(366, 31, 'Pernyataan yang paling tepat mengenai kosmetik yang berasal dari luar negeri adalah...', 'kosmetik', 50),
(367, 31, 'Manakah kombinasi pemeriksaan yang paling menunjukkan penerapan CEK KLIK secara benar pada kosmetik?', 'kosmetik', 51),
(368, 31, 'Pernyataan berikut yang paling lemah sebagai dasar untuk menyimpulkan keamanan kosmetik adalah... Produk A * Nomor izin edar tercantum * Data produk sesuai ketika diverifikasi * Kemasan dan label lengkap * Klaim promosi: “Memutihkan kulit secara permanen dalam 7 hari.” Produk B * Tidak ditemukan ketika diverifikasi * Dijual dengan harga sangat murah * Klaim: “100% aman karena menggunakan bahan alami.” Produk C * Data produk sesuai dengan hasil verifikasi * Label dan kemasan tersedia * Klaim penggunaan sesuai fungsi kosmetik * Tidak memberikan janji hasil yang berlebihan', 'kosmetik', 52),
(369, 31, 'Pernyataan yang paling tepat adalah...', 'kosmetik', 53),
(370, 31, 'Pernyataan yang paling tepat untuk menggambarkan Obat Bahan Alam (OBA) adalah...', 'obat_bahan_alam', 54),
(371, 31, 'Manakah yang termasuk Bahan Kimia Obat (BKO) yang sering ditemukan pada jamu ilegal?', 'obat_bahan_alam', 55),
(372, 31, 'Seorang warga mengeluh setelah mengonsumsi jamu pegal linu yang memberikan efek sangat cepat, tetapi pada kemasannya tidak terdapat nomor izin edar BPOM. Sebagai kader GAS-PAMAN, saran yang paling tepat adalah ....', 'obat_bahan_alam', 56),
(373, 31, 'Perbedaan utama antara Jamu, Obat Herbal Terstandar (OHT), dan Fitofarmaka dalam konteks pembuktian ilmiah adalah...', 'obat_bahan_alam', 57),
(374, 31, 'Manakah yang paling tepat mengenai nomor izin edar Obat Bahan Alam berdasarkan materi?', 'obat_bahan_alam', 58),
(375, 31, 'Pernyataan yang paling tepat mengenai Bahan Kimia Obat (BKO) dalam jamu adalah...', 'obat_bahan_alam', 59),
(376, 31, 'Mengapa klaim “efek terasa sangat cepat” perlu menjadi salah satu hal yang diwaspadai konsumen ketika memilih jamu?', 'obat_bahan_alam', 60),
(377, 31, 'Pernyataan berikut yang paling tepat menjelaskan mengapa keberadaan BKO dalam jamu menjadi risiko bagi konsumen adalah...', 'obat_bahan_alam', 61),
(378, 31, 'Materi menyebutkan BKO sengaja disembunyikan sehingga konsumen tidak menyadari bahan kimia yang dikonsumsi. Dari pilihan berikut, tindakan yang paling kuat untuk memastikan legalitas produk obat bahan alam adalah...', 'obat_bahan_alam', 62),
(379, 31, 'Pernyataan berikut yang paling tepat adalah...', 'obat_bahan_alam', 63),
(380, 31, 'Dalam materi GAS-PAMAN, BPOM Mobile dapat dimanfaatkan masyarakat terutama untuk...', 'obat_bahan_alam', 64),
(381, 31, 'Perhatikan pernyataan berikut: Pernyataan 1: Produk berbahan alam tidak otomatis bebas risiko. Pernyataan 2: Efek yang dirasakan sangat cepat pada jamu dapat menjadi hal yang perlu diwaspadai. Pernyataan 3: BKO dapat sengaja ditambahkan tanpa dicantumkan pada label. Pernyataan 4: Verifikasi izin edar merupakan bagian penting dalam memilih produk yang aman. Kombinasi pernyataan yang sesuai dengan materi GAS-PAMAN adalah... * Kemasan utuh * Label terbaca * Nomor izin edar tercantum * Tanggal kedaluwarsa masih berlaku', 'obat_bahan_alam', 65),
(382, 31, 'Seorang kader menemukan produk dengan kondisi: Langkah yang masih perlu dilakukan untuk memastikan penerapan CEK KLIK tidak hanya bersifat administratif adalah...', 'obat_bahan_alam', 66),
(383, 31, 'Manakah yang paling sesuai dengan posisi Agen GAS-PAMAN ketika menemukan dugaan produk obat dan makanan yang bermasalah?', 'obat_bahan_alam', 67),
(384, 31, 'Seorang kader sedang mengedukasi masyarakat mengenai kosmetik yang dibeli secara online. Urutan pendekatan yang paling sesuai dengan semangat GAS-PAMAN adalah...', 'obat_bahan_alam', 68);

-- --------------------------------------------------------

--
-- Struktur dari tabel `sampling_hasil`
--

CREATE TABLE `sampling_hasil` (
  `id` int(11) NOT NULL,
  `periode_id` int(11) NOT NULL,
  `catatan_id` int(11) NOT NULL,
  `urutan` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `sampling_hasil`
--

INSERT INTO `sampling_hasil` (`id`, `periode_id`, `catatan_id`, `urutan`, `created_at`) VALUES
(53, 5, 54, 1, '2026-08-05 12:37:02'),
(54, 5, 56, 2, '2026-08-05 12:37:02'),
(55, 5, 57, 3, '2026-08-05 12:37:02'),
(56, 6, 52, 1, '2026-08-05 13:14:35'),
(57, 6, 47, 2, '2026-08-05 13:14:35'),
(58, 6, 51, 3, '2026-08-05 13:14:35'),
(59, 7, 57, 1, '2026-08-06 02:30:21'),
(60, 7, 47, 2, '2026-08-06 02:30:21'),
(61, 7, 51, 3, '2026-08-06 02:30:21'),
(62, 8, 57, 1, '2026-08-06 02:30:54'),
(63, 8, 59, 2, '2026-08-06 02:30:54'),
(64, 8, 46, 3, '2026-08-06 02:30:54'),
(65, 9, 54, 1, '2026-08-06 02:31:03'),
(66, 9, 47, 2, '2026-08-06 02:31:03'),
(67, 9, 59, 3, '2026-08-06 02:31:03'),
(68, 10, 67, 1, '2026-08-07 09:14:44'),
(69, 10, 56, 2, '2026-08-07 09:14:44'),
(70, 10, 52, 3, '2026-08-07 09:14:44'),
(71, 11, 66, 1, '2026-08-07 09:14:59'),
(72, 11, 47, 2, '2026-08-07 09:14:59'),
(73, 11, 48, 3, '2026-08-07 09:14:59'),
(74, 12, 53, 1, '2026-08-13 06:39:01'),
(75, 12, 68, 2, '2026-08-13 06:39:01'),
(76, 12, 54, 3, '2026-08-13 06:39:01'),
(77, 12, 50, 4, '2026-08-13 06:39:01'),
(78, 13, 56, 1, '2026-08-19 05:22:53'),
(79, 13, 98, 2, '2026-08-19 05:22:53'),
(80, 13, 97, 3, '2026-08-19 05:22:53'),
(81, 13, 119, 4, '2026-08-19 05:22:53'),
(82, 13, 123, 5, '2026-08-19 05:22:53'),
(83, 13, 125, 6, '2026-08-19 05:22:53'),
(84, 13, 135, 7, '2026-08-19 05:22:53'),
(85, 13, 122, 8, '2026-08-19 05:22:53'),
(86, 13, 148, 9, '2026-08-19 05:22:53'),
(87, 13, 111, 10, '2026-08-19 05:22:53'),
(88, 13, 144, 11, '2026-08-19 05:22:53'),
(89, 13, 109, 12, '2026-08-19 05:22:53'),
(90, 13, 112, 13, '2026-08-19 05:22:53'),
(91, 13, 80, 14, '2026-08-19 05:22:53'),
(92, 13, 143, 15, '2026-08-19 05:22:53'),
(93, 13, 53, 16, '2026-08-19 05:22:53'),
(94, 13, 138, 17, '2026-08-19 05:22:53'),
(95, 13, 102, 18, '2026-08-19 05:22:53'),
(96, 14, 96, 1, '2026-08-19 05:23:04'),
(97, 14, 82, 2, '2026-08-19 05:23:04'),
(98, 14, 74, 3, '2026-08-19 05:23:04'),
(99, 14, 94, 4, '2026-08-19 05:23:04'),
(100, 14, 124, 5, '2026-08-19 05:23:04'),
(101, 14, 129, 6, '2026-08-19 05:23:04'),
(102, 14, 105, 7, '2026-08-19 05:23:04'),
(103, 14, 146, 8, '2026-08-19 05:23:04'),
(104, 14, 139, 9, '2026-08-19 05:23:04'),
(105, 14, 103, 10, '2026-08-19 05:23:04'),
(106, 14, 127, 11, '2026-08-19 05:23:04'),
(107, 14, 140, 12, '2026-08-19 05:23:04'),
(108, 14, 101, 13, '2026-08-19 05:23:04'),
(109, 14, 46, 14, '2026-08-19 05:23:04'),
(110, 14, 50, 15, '2026-08-19 05:23:04'),
(111, 14, 69, 16, '2026-08-19 05:23:04'),
(112, 14, 122, 17, '2026-08-19 05:23:04'),
(113, 14, 92, 18, '2026-08-19 05:23:04');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sampling_periode`
--

CREATE TABLE `sampling_periode` (
  `id` int(11) NOT NULL,
  `dibuat_oleh` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 20,
  `status` enum('aktif','selesai') NOT NULL DEFAULT 'aktif',
  `filter_info` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `sampling_periode`
--

INSERT INTO `sampling_periode` (`id`, `dibuat_oleh`, `jumlah`, `status`, `filter_info`, `created_at`, `reset_at`) VALUES
(1, 6, 20, 'selesai', '{\"agen\":\"\",\"mulai\":\"\",\"selesai\":\"\",\"search\":\"\",\"status\":\"\",\"pool\":26}', '2026-08-04 00:26:15', '2026-08-04 00:26:59'),
(2, 6, 20, 'selesai', '{\"agen\":\"\",\"mulai\":\"\",\"selesai\":\"\",\"search\":\"\",\"status\":\"\",\"pool\":31}', '2026-08-04 02:25:10', '2026-08-04 02:42:29'),
(3, 6, 6, 'selesai', '{\"agen\":\"\",\"mulai\":\"\",\"selesai\":\"\",\"search\":\"\",\"status\":\"\",\"pool\":31,\"percent\":20,\"sample_size\":6}', '2026-08-04 02:43:14', '2026-08-04 02:43:34'),
(4, 6, 6, 'selesai', '{\"agen\":\"\",\"mulai\":\"\",\"selesai\":\"\",\"search\":\"\",\"status\":\"\",\"pool\":31,\"percent\":20,\"sample_size\":6}', '2026-08-04 03:45:04', '2026-08-04 03:45:16'),
(5, 6, 3, 'selesai', '{\"agen\":\"\",\"mulai\":\"\",\"selesai\":\"\",\"search\":\"\",\"status\":\"\",\"pool\":14,\"percent\":20,\"sample_size\":3}', '2026-08-05 12:37:02', '2026-08-05 13:14:31'),
(6, 14, 3, 'selesai', '{\"agen\":\"\",\"mulai\":\"\",\"selesai\":\"\",\"search\":\"\",\"status\":\"\",\"pool\":14,\"percent\":20,\"sample_size\":3}', '2026-08-05 13:14:35', '2026-08-06 02:12:20'),
(7, 6, 3, 'selesai', '{\"agen\":\"\",\"mulai\":\"\",\"selesai\":\"\",\"search\":\"\",\"status\":\"\",\"pool\":14,\"percent\":20,\"sample_size\":3}', '2026-08-06 02:30:21', '2026-08-06 02:30:51'),
(8, 6, 3, 'selesai', '{\"agen\":\"\",\"mulai\":\"\",\"selesai\":\"\",\"search\":\"\",\"status\":\"\",\"pool\":14,\"percent\":20,\"sample_size\":3}', '2026-08-06 02:30:54', '2026-08-06 02:31:01'),
(9, 6, 3, 'selesai', '{\"agen\":\"\",\"mulai\":\"\",\"selesai\":\"\",\"search\":\"\",\"status\":\"\",\"pool\":14,\"percent\":20,\"sample_size\":3}', '2026-08-06 02:31:03', '2026-08-07 09:14:02'),
(10, 14, 3, 'selesai', '{\"agen\":\"\",\"mulai\":\"\",\"selesai\":\"\",\"search\":\"\",\"status\":\"\",\"pool\":16,\"percent\":20,\"sample_size\":3}', '2026-08-07 09:14:44', '2026-08-07 09:14:53'),
(11, 14, 3, 'selesai', '{\"agen\":\"\",\"mulai\":\"\",\"selesai\":\"\",\"search\":\"\",\"status\":\"\",\"pool\":16,\"percent\":20,\"sample_size\":3}', '2026-08-07 09:14:59', '2026-08-13 03:11:11'),
(12, 6, 4, 'selesai', '{\"agen\":\"\",\"mulai\":\"\",\"selesai\":\"\",\"search\":\"\",\"status\":\"\",\"pool\":18,\"percent\":20,\"sample_size\":4}', '2026-08-13 06:39:01', '2026-08-19 05:22:50'),
(13, 14, 18, 'selesai', '{\"agen\":\"\",\"mulai\":\"\",\"selesai\":\"\",\"search\":\"\",\"status\":\"\",\"pool\":88,\"percent\":20,\"sample_size\":18}', '2026-08-19 05:22:53', '2026-08-19 05:23:00'),
(14, 14, 18, 'aktif', '{\"agen\":\"\",\"mulai\":\"\",\"selesai\":\"\",\"search\":\"\",\"status\":\"\",\"pool\":88,\"percent\":20,\"sample_size\":18}', '2026-08-19 05:23:04', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `agen_id` varchar(20) DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff','agen','kabalai') NOT NULL DEFAULT 'agen',
  `jenis_kelamin` enum('Pria','Wanita') NOT NULL,
  `usia` int(3) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `koordinat_manual` tinyint(1) NOT NULL DEFAULT 0,
  `nama_instansi` varchar(150) NOT NULL DEFAULT '',
  `magang_mulai` date DEFAULT NULL,
  `magang_selesai` date DEFAULT NULL,
  `nomor_hp` varchar(16) NOT NULL,
  `pekerjaan` varchar(100) DEFAULT NULL,
  `kampus` varchar(150) DEFAULT NULL,
  `jurusan` varchar(150) DEFAULT NULL,
  `foto_profil` varchar(255) DEFAULT 'default.png',
  `tanda_tangan` varchar(255) DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expire` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `agen_id`, `nama`, `email`, `password`, `role`, `jenis_kelamin`, `usia`, `alamat`, `latitude`, `longitude`, `koordinat_manual`, `nama_instansi`, `magang_mulai`, `magang_selesai`, `nomor_hp`, `pekerjaan`, `kampus`, `jurusan`, `foto_profil`, `tanda_tangan`, `status`, `created_at`, `reset_token`, `token_expire`) VALUES
(1, NULL, 'Bayu Santoso', 'l1nux3r69@gmail.com', '$2y$10$yPhrL15r.vcyDuQpLHXmkeRzQDm6Hd2uYRJCGrmHaCQ/2.n99W.6a', 'admin', 'Pria', 22, 'Cakranegara Barat', NULL, NULL, 0, 'Universitas Bumigora', NULL, NULL, '085161176056', NULL, NULL, NULL, 'AVATAR_1c9e10f65eadaa9f.png', NULL, 'aktif', '2026-02-19 05:31:32', NULL, NULL),
(6, NULL, 'faris', 'farishawari83@gmail.com', '$2y$10$m/jjkd.WAq4l0.cQAVFCD.flT044pWvra/wMavYxkX.tTrqQ6ETTS', 'admin', 'Pria', 21, 'CAKRANEGARA', NULL, NULL, 0, 'UniversitasBumigora', NULL, NULL, '087752432270', NULL, NULL, NULL, 'AVATAR_303d1f22475a17e4.png', NULL, 'aktif', '2026-07-28 00:38:19', NULL, NULL),
(9, 'AG-003', 'Agen Test', 'agen@test.com', '$2y$10$3ZgzWTCenIv4XawYEpjKTOpSTSvTvmVvuAamf.855DqruK2oPjr9S', 'agen', 'Pria', 21, 'Desa/Kel. Pringgarata, Kec. Pringgarata, LOMBOK TENGAH, Nusa Tenggara Barat', -8.6173438, 116.2466787, 0, 'Universitas Bumigora', NULL, NULL, '085829002910', 'Mahasiswa', '', '', 'AVATAR_a85fc45c4f1da851.png', NULL, 'aktif', '2026-07-28 01:03:21', NULL, NULL),
(13, NULL, 'Yogi Abaso Mataram, S.Si., Apt.', 'kabalai@bbpom.go.id', '$2y$10$PgoJJciYsXNAoKnVZrPJiOOkQ5vp/OcaLTqGqx/FcZbFaOARf4GMa', 'kabalai', 'Pria', NULL, 'Jl. Catur Warga, Mataram Timur, kecamatan Mataram, Nusa tenggara barat 83121', NULL, NULL, 0, '', NULL, NULL, '', '', NULL, NULL, 'default.png', 'TTD_e3a5f0b18665a511.png', 'aktif', '2026-07-30 00:12:49', NULL, NULL),
(14, NULL, 'wisnu', 'wisnu@gmail.com', '$2y$10$eMD.Wm/shBHZ4rm93tw1qeQ7ivKmUXdou2lc3Au6vIsrpDCsOWsbu', 'admin', 'Pria', 21, 'Desa/Kel. Ampenan Selatan, Kec. Ampenan, Kota Mataram, Nusa Tenggara Barat', -8.5781842, 116.0850686, 0, 'Magang BBPOM', NULL, NULL, '081234567891', '', NULL, NULL, 'default.png', NULL, 'aktif', '2026-08-05 01:56:29', NULL, NULL),
(24, 'AG-009', 'Alfizar Daffa Zulkarnain', 'alfizardaffa2006@gmail.com', '$2y$10$9q5Forr5A24CdYiQNudEB.7Dl7hVoWO9kVVvOcbvWKR0.vSHuLPku', 'agen', 'Pria', 20, 'Desa/Kel. Pagesangan Barat, Kec. Mataram, Kota Mataram, Nusa Tenggara Barat', -8.5838994, 116.1173300, 0, 'Universitas Mataram', NULL, NULL, '081236214891', '', NULL, NULL, 'default.png', NULL, 'aktif', '2026-08-11 00:37:21', NULL, NULL),
(25, 'AG-010', 'Desna Auliya Khairiyatin', 'dsnauliya18@gmail.com', '$2y$10$.Be.N0Rg0zVzbLrpS/fjiOHSQcZnCWKV06oElfoajF4oN4YssVSZS', 'agen', 'Wanita', 20, NULL, -8.5761587, 116.0996904, 0, 'Universitas Mataram', NULL, NULL, '081558266553', NULL, NULL, NULL, 'default.png', NULL, 'aktif', '2026-08-11 00:42:37', NULL, NULL),
(26, 'AG-011', 'Hasna Adifa Khouri Azalia', 'hasnaadifaka@gmail.com', '$2y$10$OM8VwHE6hdsaO2l6s48s3e8LqIMvJs5KRDxd4Zmz/L8PDGHfdZ5Dm', 'agen', 'Wanita', 21, NULL, -8.5838994, 116.1173300, 0, 'Universitas Mataram ', NULL, NULL, '089637830571', NULL, NULL, NULL, 'default.png', NULL, 'aktif', '2026-08-11 00:47:22', NULL, NULL),
(27, 'AG-012', 'Nurul Azlinia ', 'nurul.azlinia@gmail.com', '$2y$10$Se1WzP0YBzEq/ZTc8SivTOhWe./3c8YF8OxnLhwMxNnjD3IBigbnC', 'agen', 'Wanita', 20, NULL, -8.5833000, 116.1167000, 0, 'Universitas Mataram ', NULL, NULL, '085932534685', NULL, NULL, NULL, 'default.png', NULL, 'aktif', '2026-08-11 00:55:09', NULL, NULL),
(28, 'AG-013', 'Zamil Hukmi', 'zamilhukmi5@gmail.com', '$2y$10$co.JoIW8e/ydPvx5I77uWO62J85cMK1g/rB1VI1TgAZhM6BvvCs26', 'agen', 'Pria', 20, 'Jl. Raya Tanjung KM 1, Desa/Kel. Midang, Kec. Gunungsari, Lombok Barat, Nusa Tenggara Barat', -8.5833000, 116.1167000, 0, 'Universitas Mataram', NULL, NULL, '089508833804', 'Mahasiswa', NULL, NULL, 'default.png', NULL, 'aktif', '2026-08-11 00:57:10', NULL, NULL),
(29, 'AG-014', 'Hayatun Nupus', 'hayatunnupus931@gmail.com', '$2y$10$FPAE/Yj13QqNpKZiecYNCOJcWbBJeviQq6PESSJlOrI4h5L/vC04C', 'agen', 'Wanita', 22, NULL, -8.5796633, 116.1029969, 0, 'Universitas Mataram', NULL, NULL, '087876040025', NULL, NULL, NULL, 'default.png', NULL, 'aktif', '2026-08-11 00:59:50', NULL, NULL),
(30, 'AG-015', 'Ni Made Putri Santika', 'nimadeputrisantika@gmail.com', '$2y$10$f9qMfdN8hFTu9UgNUtFO4.Y4t.2kI3zyyWEiKPTGP9iiU/QbwAOLS', 'agen', 'Wanita', 20, NULL, -8.6718889, 116.1220480, 0, 'Universitas Mataram', NULL, NULL, '087727123143', NULL, NULL, NULL, 'default.png', NULL, 'aktif', '2026-08-13 05:05:45', NULL, NULL),
(31, 'AG-016', 'Dede Muhammad Valent', 'dedemuhammadvalent@gmail.com', '$2y$10$SOPgIXtSDz8G23ArC1bmseHX.0BAMaVk7udyKC2tI5QwSXDvsho4q', 'agen', 'Pria', 22, NULL, -8.5763162, 116.0995875, 0, 'IPB', NULL, NULL, '089507217200', NULL, NULL, NULL, 'default.png', NULL, 'aktif', '2026-08-13 23:13:41', NULL, NULL),
(32, 'AG-017', 'Dini Liyasri Ramadhani', 'diniliyasrir@gmail.com', '$2y$10$R0APUTefKy/kgW1h8/Vboeclg8WnUOvnpWtwOUTMSQ0YptHoztuJi', 'agen', 'Wanita', 20, NULL, -8.5833000, 116.1167000, 0, 'Universitas Mataram', NULL, NULL, '087744870694', NULL, NULL, NULL, 'default.png', NULL, 'aktif', '2026-08-14 01:30:35', NULL, NULL),
(33, 'AG-018', 'Nur Ulul Azmi ', 'n.azmiulul@apps.ipb.ac.id', '$2y$10$RmoxahgY0GOzHni0r74VjuiBmG3RkK92Ev9UhoRDyXMUkuuTcH8HS', 'agen', 'Pria', 21, NULL, -8.5781842, 116.0850686, 0, 'Institut Pertanian Bogor ', NULL, NULL, '087735392191', NULL, NULL, NULL, 'default.png', NULL, 'aktif', '2026-08-14 03:18:02', NULL, NULL),
(34, 'AG-019', 'M.SYAIFULLAH', 'ipul26@gmail.com', '$2y$10$9Oizvh6hi6GBPUdTo4seCuGfIaDQhAlerqHKvfekRxkbpDD0oCkR2', 'agen', 'Pria', 21, 'Jl.segara anak, NO.34, Desa/Kel. Taman Sari, Kec. Ampenan, Kota Mataram, Nusa Tenggara Barat', -8.5818707, 116.0818332, 0, 'UNIVERSITAS MATARAM', NULL, NULL, '081246199776', 'Mahasiswa', NULL, NULL, 'default.png', NULL, 'aktif', '2026-08-14 03:46:36', NULL, NULL),
(35, 'J1A02310030', 'Adithya w surbini', 'adithyaws01@gmail.com', '$2y$10$iFpwVZw6hqagWFff8kLA6etwhfQwlbSG9xDD8D1i61PI0Bz9UU8jq', 'agen', 'Pria', 21, 'Jl. Sumbawa- Mataram, Desa/Kel. Lopok Beru, Kec. Mataram, Kota Mataram, Nusa Tenggara Barat', -8.5694004, 117.5585794, 0, 'Universitas Mataram', NULL, NULL, '082342012122', 'Mahasiswa', NULL, NULL, 'default.png', NULL, 'aktif', '2026-08-14 04:25:03', NULL, NULL),
(36, 'TEMP', 'AgenTest', 'tes@gmail.com', '$2y$10$FGZGpX48.PeFwbzBBgxLQOzIb/h75Pwum7mSmhimXBdnkSuw.WK1u', 'agen', 'Pria', 67, 'Desa/Kel. Senggigi, Kec. Batu Layar, Lombok Barat, Nusa Tenggara Barat', -8.5257170, 116.0672514, 0, '....', NULL, NULL, '0892320442', NULL, NULL, NULL, 'default.png', NULL, 'aktif', '2026-08-21 01:17:23', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `bank_soal`
--
ALTER TABLE `bank_soal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indeks untuk tabel `catatan_files`
--
ALTER TABLE `catatan_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `catatan_id` (`catatan_id`);

--
-- Indeks untuk tabel `catatan_harian`
--
ALTER TABLE `catatan_harian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_ch_tanggal` (`tanggal`),
  ADD KEY `idx_ch_status` (`status_review`),
  ADD KEY `idx_ch_latlng` (`latitude`,`longitude`),
  ADD KEY `idx_ch_status_draft` (`status_review`);

--
-- Indeks untuk tabel `detail_jawaban`
--
ALTER TABLE `detail_jawaban`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hasil_test_id` (`hasil_test_id`),
  ADD KEY `pertanyaan_id` (`pertanyaan_id`),
  ADD KEY `opsi_id` (`opsi_id`);

--
-- Indeks untuk tabel `hasil_test`
--
ALTER TABLE `hasil_test`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `bank_soal_id` (`bank_soal_id`),
  ADD KEY `fk_hasil_test_signed_by` (`signed_by`),
  ADD KEY `idx_ht_status_sert` (`status_sertifikat`);

--
-- Indeks untuk tabel `hasil_test_soal`
--
ALTER TABLE `hasil_test_soal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_hts_hasil` (`hasil_test_id`),
  ADD KEY `idx_hts_pertanyaan` (`pertanyaan_id`);

--
-- Indeks untuk tabel `log_laporan`
--
ALTER TABLE `log_laporan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_log_user` (`user_id`),
  ADD KEY `idx_log_catatan` (`catatan_id`),
  ADD KEY `idx_log_created` (`created_at`);

--
-- Indeks untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notif_user` (`user_id`),
  ADD KEY `idx_notif_read` (`user_id`,`is_read`),
  ADD KEY `idx_notif_created` (`created_at`);

--
-- Indeks untuk tabel `opsi_jawaban`
--
ALTER TABLE `opsi_jawaban`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pertanyaan_id` (`pertanyaan_id`);

--
-- Indeks untuk tabel `pertanyaan`
--
ALTER TABLE `pertanyaan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bank_soal_id` (`bank_soal_id`);

--
-- Indeks untuk tabel `sampling_hasil`
--
ALTER TABLE `sampling_hasil`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_periode_catatan` (`periode_id`,`catatan_id`),
  ADD KEY `idx_sampling_periode` (`periode_id`);

--
-- Indeks untuk tabel `sampling_periode`
--
ALTER TABLE `sampling_periode`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sampling_status` (`status`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `uq_users_agen_id` (`agen_id`),
  ADD KEY `idx_users_latlng` (`latitude`,`longitude`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `bank_soal`
--
ALTER TABLE `bank_soal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT untuk tabel `catatan_files`
--
ALTER TABLE `catatan_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=161;

--
-- AUTO_INCREMENT untuk tabel `catatan_harian`
--
ALTER TABLE `catatan_harian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=149;

--
-- AUTO_INCREMENT untuk tabel `detail_jawaban`
--
ALTER TABLE `detail_jawaban`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT untuk tabel `hasil_test`
--
ALTER TABLE `hasil_test`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT untuk tabel `hasil_test_soal`
--
ALTER TABLE `hasil_test_soal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `log_laporan`
--
ALTER TABLE `log_laporan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=125;

--
-- AUTO_INCREMENT untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT untuk tabel `opsi_jawaban`
--
ALTER TABLE `opsi_jawaban`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1530;

--
-- AUTO_INCREMENT untuk tabel `pertanyaan`
--
ALTER TABLE `pertanyaan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=385;

--
-- AUTO_INCREMENT untuk tabel `sampling_hasil`
--
ALTER TABLE `sampling_hasil`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT untuk tabel `sampling_periode`
--
ALTER TABLE `sampling_periode`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `bank_soal`
--
ALTER TABLE `bank_soal`
  ADD CONSTRAINT `bank_soal_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `catatan_files`
--
ALTER TABLE `catatan_files`
  ADD CONSTRAINT `catatan_files_ibfk_1` FOREIGN KEY (`catatan_id`) REFERENCES `catatan_harian` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `catatan_harian`
--
ALTER TABLE `catatan_harian`
  ADD CONSTRAINT `catatan_harian_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `detail_jawaban`
--
ALTER TABLE `detail_jawaban`
  ADD CONSTRAINT `detail_jawaban_ibfk_1` FOREIGN KEY (`hasil_test_id`) REFERENCES `hasil_test` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_jawaban_ibfk_2` FOREIGN KEY (`pertanyaan_id`) REFERENCES `pertanyaan` (`id`),
  ADD CONSTRAINT `detail_jawaban_ibfk_3` FOREIGN KEY (`opsi_id`) REFERENCES `opsi_jawaban` (`id`);

--
-- Ketidakleluasaan untuk tabel `hasil_test`
--
ALTER TABLE `hasil_test`
  ADD CONSTRAINT `fk_hasil_test_signed_by` FOREIGN KEY (`signed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hasil_test_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hasil_test_ibfk_2` FOREIGN KEY (`bank_soal_id`) REFERENCES `bank_soal` (`id`);

--
-- Ketidakleluasaan untuk tabel `opsi_jawaban`
--
ALTER TABLE `opsi_jawaban`
  ADD CONSTRAINT `opsi_jawaban_ibfk_1` FOREIGN KEY (`pertanyaan_id`) REFERENCES `pertanyaan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pertanyaan`
--
ALTER TABLE `pertanyaan`
  ADD CONSTRAINT `pertanyaan_ibfk_1` FOREIGN KEY (`bank_soal_id`) REFERENCES `bank_soal` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
