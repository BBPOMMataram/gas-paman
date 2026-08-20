-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 22 Bulan Mei 2026 pada 21.19
-- Versi server: 10.11.16-MariaDB-cll-lve
-- Versi PHP: 8.4.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `xlaj2839_bbpom`
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
(1, 'Pre-Test 1', 'Test', 'pre_test', '2026-04-08', 'nonaktif', 1, '2026-04-08 03:51:01'),
(2, 'Post-Test contoh', 'Post', 'post_test', '2026-04-08', 'nonaktif', 1, '2026-04-08 03:51:56'),
(3, 'Pre-Test ke 2', '', 'pre_test', '2026-04-08', 'aktif', 1, '2026-04-08 04:06:57'),
(4, 'Post-Test Ke 2', '', 'post_test', '2026-04-08', 'aktif', 1, '2026-04-08 04:07:54');

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
(7, 6, '2894bab75523de917850.png', '2026-02-19 06:22:06'),
(8, 10, '05b8564f65f8f463a6cf.png', '2026-02-19 15:52:14'),
(9, 10, 'f9f7a8a86602964745a3.png', '2026-02-19 15:52:14'),
(10, 11, '0085cde0310e2a11b063.png', '2026-02-20 03:03:03'),
(12, 16, '1cd441a4789091e03730.png', '2026-02-23 01:53:14');

-- --------------------------------------------------------

--
-- Struktur dari tabel `catatan_harian`
--

CREATE TABLE `catatan_harian` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `nama_konsumen` varchar(255) DEFAULT NULL,
  `usia` int(3) DEFAULT NULL,
  `jenis_kelamin` enum('Pria','Wanita') DEFAULT NULL,
  `pekerjaan` varchar(100) DEFAULT NULL,
  `jumlah_peserta` int(11) DEFAULT 0,
  `jenis_komunitas` varchar(50) DEFAULT NULL,
  `informasi` text DEFAULT NULL,
  `lokasi` varchar(255) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `hasil_pre_test` varchar(255) DEFAULT NULL,
  `hasil_post_test` varchar(255) DEFAULT NULL,
  `nilai_pre_test` decimal(5,2) DEFAULT NULL,
  `nilai_post_test` decimal(5,2) DEFAULT NULL,
  `lampiran_hasil_test` varchar(255) DEFAULT NULL,
  `lokasi_gps` varchar(255) DEFAULT NULL,
  `foto_kegiatan` varchar(255) DEFAULT NULL,
  `status_review` enum('draft','pending','approved','revisi') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `catatan_harian`
--

INSERT INTO `catatan_harian` (`id`, `user_id`, `tanggal`, `nama_konsumen`, `jumlah_peserta`, `jenis_komunitas`, `informasi`, `lokasi`, `alamat`, `no_hp`, `lokasi_gps`, `foto_kegiatan`, `status_review`, `created_at`) VALUES
(6, 2, '2026-02-19', 'Muklis', 1, 'UMKM', 'lkjnasd', 'Mandalika', NULL, NULL, NULL, NULL, 'pending', '2026-02-19 06:22:06'),
(10, 2, '2026-02-19', 'Inak Esun', 1, 'Sekolah', 'Materi membuat nasi puyung yang lebih nikmat anjay', 'Puyung Lombok tengah', NULL, NULL, NULL, NULL, 'pending', '2026-02-19 15:52:14'),
(11, 3, '2026-02-20', 'Sosialisasi Gas-Paman', 42, 'Lainnya', 'Mikroteaching', 'Aula BBPOM Mataram', NULL, NULL, NULL, NULL, 'pending', '2026-02-20 03:03:03'),
(16, 3, '2026-02-23', 'Wisnu', 0, 'UMKM', 'Jasdas\r\nknbasdasd\r\nkjnasdnasdnasd', 'Rumahnya', 'Jalan Krg Sampalan', '085161176056', NULL, NULL, 'pending', '2026-02-23 01:53:14');

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

--
-- Dumping data untuk tabel `detail_jawaban`
--

INSERT INTO `detail_jawaban` (`id`, `hasil_test_id`, `pertanyaan_id`, `opsi_id`) VALUES
(1, 1, 1, 2),
(2, 2, 2, 6),
(3, 3, 3, 11),
(4, 4, 4, 14);

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
  `waktu_mulai` datetime NOT NULL,
  `waktu_selesai` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `hasil_test`
--

INSERT INTO `hasil_test` (`id`, `user_id`, `bank_soal_id`, `nilai`, `jawaban_benar`, `total_pertanyaan`, `waktu_mulai`, `waktu_selesai`, `created_at`) VALUES
(1, 3, 1, 0.00, 0, 1, '2026-04-08 12:05:17', '2026-04-08 12:05:22', '2026-04-08 04:05:22'),
(2, 3, 2, 100.00, 1, 1, '2026-04-08 12:05:29', '2026-04-08 12:05:32', '2026-04-08 04:05:32'),
(3, 3, 3, 100.00, 1, 1, '2026-04-08 12:08:37', '2026-04-08 12:08:42', '2026-04-08 04:08:42'),
(4, 3, 4, 0.00, 0, 1, '2026-04-08 12:08:43', '2026-04-08 12:08:47', '2026-04-08 04:08:47');

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
(1, 1, 'Test a', 0),
(2, 1, 'test b', 0),
(3, 1, 'test c', 1),
(4, 1, 'test d', 0),
(5, 2, 'Joko Widodo', 0),
(6, 2, 'Soekarno', 1),
(7, 2, 'Anies', 0),
(8, 2, 'Ganjar', 0),
(9, 3, 'Nicolas Tesla', 0),
(10, 3, 'Abraham Lincoln', 0),
(11, 3, 'Alexander Graham Bell', 1),
(12, 3, 'Joko Widodo', 0),
(13, 4, 'Sahroni', 0),
(14, 4, 'Kepala BGN', 0),
(15, 4, 'Bahlil', 0),
(16, 4, 'B.J Habibie', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pertanyaan`
--

CREATE TABLE `pertanyaan` (
  `id` int(11) NOT NULL,
  `bank_soal_id` int(11) NOT NULL,
  `teks_pertanyaan` text NOT NULL,
  `urutan` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pertanyaan`
--

INSERT INTO `pertanyaan` (`id`, `bank_soal_id`, `teks_pertanyaan`, `urutan`) VALUES
(1, 1, 'Test', 1),
(2, 2, 'Siapa Nama presiden pertama indonesia', 1),
(3, 3, 'Siapa penemu bohlam?', 1),
(4, 4, 'Siapa Bapak Demokrasi indonesia', 1);

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
  `role` enum('admin','staff','agen') NOT NULL DEFAULT 'agen',
  `jenis_kelamin` enum('Pria','Wanita') NOT NULL,
  `usia` int(3) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `nama_instansi` varchar(30) NOT NULL,
  `nomor_hp` varchar(16) NOT NULL,
  `foto_profil` varchar(255) DEFAULT 'default.png',
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expire` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `agen_id`, `nama`, `email`, `password`, `role`, `jenis_kelamin`, `usia`, `alamat`, `nama_instansi`, `nomor_hp`, `foto_profil`, `status`, `created_at`, `reset_token`, `token_expire`) VALUES
(1, NULL, 'Bayu Santoso', 'l1nux3r69@gmail.com', '$2y$10$yPhrL15r.vcyDuQpLHXmkeRzQDm6Hd2uYRJCGrmHaCQ/2.n99W.6a', 'admin', 'Pria', 22, 'Cakranegara Barat', 'Universitas Bumigora', '2301020001', '085161176056', 'AVATAR_1c9e10f65eadaa9f.png', 'aktif', '2026-02-19 05:31:32', NULL, NULL),
(2, 'AG-092', 'Mamat', 'agen01@gmail.com', '$2y$10$5dBsK.JwgoatC5Ge34Bt8.ZRQszD76sB3JkKH5sIrsSnaVsJ1wE8C', 'agen', 'Pria', 22, 'Monjok Baru', 'Magang BBPOM', '1338988292', '087766667776', 'default.png', 'aktif', '2026-02-19 06:21:27', NULL, NULL),
(3, 'AG-001', 'Klise', 'klise@gmail.com', '$2y$10$eBHV8XvQavdr.R31U4dqpOm6gLOND2bTq.Wa7aOMAkDhwr6WKKYPm', 'agen', 'Wanita', 22, 'Monjok Lama', 'BBPOM', '2301020001', '086676665524', 'AVATAR_706f2f0507a67083.jpeg', 'aktif', '2026-02-19 06:26:11', NULL, NULL);

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
  ADD KEY `user_id` (`user_id`);

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
  ADD KEY `bank_soal_id` (`bank_soal_id`);

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
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `bank_soal`
--
ALTER TABLE `bank_soal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `catatan_files`
--
ALTER TABLE `catatan_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `catatan_harian`
--
ALTER TABLE `catatan_harian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `detail_jawaban`
--
ALTER TABLE `detail_jawaban`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `hasil_test`
--
ALTER TABLE `hasil_test`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `opsi_jawaban`
--
ALTER TABLE `opsi_jawaban`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `pertanyaan`
--
ALTER TABLE `pertanyaan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
