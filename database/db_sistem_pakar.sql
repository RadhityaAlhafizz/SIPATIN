-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 27 Jun 2026 pada 11.26
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_sistem_pakar`
--
CREATE DATABASE IF NOT EXISTS `db_sistem_pakar` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `db_sistem_pakar`;

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `basis_aturan`
--

CREATE TABLE `basis_aturan` (
  `id` int(11) NOT NULL,
  `id_penyakit` int(11) NOT NULL,
  `id_gejala` int(11) NOT NULL,
  `id_solusi` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data untuk tabel `basis_aturan`
--

INSERT INTO `basis_aturan` (`id`, `id_penyakit`, `id_gejala`, `id_solusi`, `created_at`) VALUES
(16, 1, 4, 6, '2026-06-22 13:58:24'),
(17, 1, 6, 6, '2026-06-22 13:59:43'),
(18, 1, 16, 6, '2026-06-26 03:04:56'),
(19, 13, 8, 8, '2026-06-26 03:09:48'),
(20, 13, 9, 8, '2026-06-26 03:10:00'),
(21, 13, 17, 8, '2026-06-26 03:10:25'),
(22, 13, 19, 8, '2026-06-26 03:10:37'),
(23, 3, 16, 9, '2026-06-26 03:11:03'),
(24, 3, 2, 9, '2026-06-26 03:11:16'),
(25, 3, 3, 9, '2026-06-26 03:11:35'),
(26, 3, 5, 9, '2026-06-26 03:11:51'),
(27, 3, 7, 9, '2026-06-26 03:12:03'),
(28, 3, 17, 9, '2026-06-26 03:12:19'),
(29, 4, 16, 10, '2026-06-26 03:13:20'),
(30, 4, 3, 10, '2026-06-26 03:13:36'),
(31, 4, 9, 10, '2026-06-26 03:13:53'),
(32, 4, 18, 10, '2026-06-26 03:14:16'),
(33, 5, 16, 11, '2026-06-26 03:14:37'),
(34, 5, 2, 11, '2026-06-26 03:14:53'),
(35, 5, 4, 11, '2026-06-26 03:15:28'),
(36, 5, 8, 11, '2026-06-26 03:15:59'),
(37, 5, 17, 11, '2026-06-26 03:16:17'),
(38, 5, 19, 11, '2026-06-26 03:16:32'),
(39, 10, 3, 12, '2026-06-26 03:16:55'),
(40, 10, 6, 12, '2026-06-26 03:17:08'),
(41, 10, 15, 12, '2026-06-26 03:17:27'),
(42, 10, 8, 12, '2026-06-26 03:18:05'),
(43, 10, 19, 12, '2026-06-26 03:18:17'),
(44, 1, 20, 6, '2026-06-26 04:15:25'),
(45, 1, 21, 6, '2026-06-26 04:15:47'),
(46, 1, 22, 6, '2026-06-26 04:16:07'),
(47, 1, 23, 6, '2026-06-26 04:16:22'),
(48, 10, 21, 12, '2026-06-26 04:21:18'),
(49, 3, 22, 9, '2026-06-26 04:22:08'),
(50, 4, 22, 10, '2026-06-26 04:22:44'),
(51, 5, 22, 11, '2026-06-26 04:23:12');

-- --------------------------------------------------------

--
-- Struktur dari tabel `gejala`
--

CREATE TABLE `gejala` (
  `id` int(11) NOT NULL,
  `kode_gejala` varchar(10) DEFAULT NULL,
  `nama_gejala` varchar(200) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data untuk tabel `gejala`
--

INSERT INTO `gejala` (`id`, `kode_gejala`, `nama_gejala`, `created_at`) VALUES
(2, 'G002', 'Ikan terlihat gelisah', '2026-05-20 12:56:24'),
(3, 'G003', 'Warna tubuh ikan tampak pucat', '2026-05-20 12:56:24'),
(4, 'G004', 'Ikan sering berenang di permukaan air', '2026-05-20 12:56:24'),
(5, 'G005', 'Insang ikan bergerak cepat saat bernapas', '2026-05-20 12:56:24'),
(6, 'G006', 'Ikan terlihat lemah dan kurang aktif', '2026-05-20 12:56:24'),
(7, 'G007', 'Tubuh ikan tampak lebih kurus', '2026-05-20 12:56:24'),
(8, 'G008', 'Ikan cenderung menggosokkan tubuh pada benda di sekitar kolam', '2026-05-20 12:56:24'),
(9, 'G009', 'Permukaan tubuh ikan tampak buram', '2026-05-20 12:56:24'),
(10, 'G010', 'Ikan cenderung berkumpul di area masuknya air', '2026-05-20 12:56:24'),
(15, 'G011', 'Ikan sering meloncat dari permukaan air', '2026-06-22 14:07:01'),
(16, 'G001', 'Nafsu makan ikan menurun', '2026-06-22 14:07:18'),
(17, 'G012', 'Insang ikan tampak memerah atau kecoklatan', '2026-06-26 03:02:08'),
(18, 'G013', 'Ika sering naik ke permukaan untuk mengambil udara', '2026-06-26 03:02:35'),
(19, 'G014', 'Sirip atau permukaan kulit ikan mengalami kerusakan', '2026-06-26 03:02:53'),
(20, 'G015', 'Terdapat bercak merah pada permukaan tubuh ikan', '2026-06-26 04:12:59'),
(21, 'G016', 'Terdapat luka borok pada tubuh ikan', '2026-06-26 04:13:12'),
(22, 'G017', 'Tubuh ikan terlihat lebih berlendir dari biasanya', '2026-06-26 04:13:31'),
(23, 'G018', 'Perut ikan tampak membesar atau menggembung', '2026-06-26 04:13:49');

-- --------------------------------------------------------

--
-- Struktur dari tabel `log_diagnosa`
--

CREATE TABLE `log_diagnosa` (
  `id` int(11) NOT NULL,
  `nama_pengguna` varchar(100) DEFAULT 'Anonim',
  `gejala_dipilih` text DEFAULT NULL,
  `hasil_diagnosa` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `penyakit_utama` varchar(100) DEFAULT NULL,
  `persentase` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data untuk tabel `log_diagnosa`
--

INSERT INTO `log_diagnosa` (`id`, `nama_pengguna`, `gejala_dipilih`, `hasil_diagnosa`, `created_at`, `penyakit_utama`, `persentase`) VALUES
(265, 'bico', '[\"Luka terbuka atau borok pada tubuh\",\"Perut ikan membesar atau kembung\"]', 'Aeromonas (100%)', '2026-06-25 12:23:31', 'Aeromonas', 100.00),
(266, 'bico', '[\"Luka terbuka atau borok pada tubuh\",\"Perut ikan membesar atau kembung\"]', 'Aeromonas (100%)', '2026-06-26 02:07:04', 'Aeromonas', 100.00),
(267, 'bico', '[\"Nafsu makan ikan menurun\",\"Ikan terlihat gelisah\",\"Warna tubuh ikan tampak pucat\"]', 'Dactylogyrus sp. (42.9%), Epistylis sp. (40%), Ichthyophthirius (Ich) (28.6%), Flexibacter sp. (16.7%), Aeromonas hydrophila (14.3%)', '2026-06-26 08:25:26', 'Dactylogyrus sp.', 42.90),
(268, 'bico', '[\"Nafsu makan ikan menurun\",\"Ikan terlihat gelisah\",\"Warna tubuh ikan tampak pucat\"]', 'Dactylogyrus sp. (42.9%), Epistylis sp. (40%), Ichthyophthirius (Ich) (28.6%), Flexibacter sp. (16.7%), Aeromonas hydrophila (14.3%)', '2026-06-26 13:22:05', 'Dactylogyrus sp.', 42.90),
(269, 'bico', '[\"Nafsu makan ikan menurun\",\"Ikan terlihat gelisah\",\"Warna tubuh ikan tampak pucat\"]', 'Dactylogyrus sp. (42.9%), Epistylis sp. (40%), Ichthyophthirius (Ich) (28.6%), Flexibacter sp. (16.7%), Aeromonas hydrophila (14.3%)', '2026-06-26 13:23:22', 'Dactylogyrus sp.', 42.90),
(270, 'bico', '[\"Nafsu makan ikan menurun\",\"Ikan terlihat gelisah\",\"Warna tubuh ikan tampak pucat\"]', 'Dactylogyrus sp. (42.9%), Epistylis sp. (40%), Ichthyophthirius (Ich) (28.6%), Flexibacter sp. (16.7%), Aeromonas hydrophila (14.3%)', '2026-06-26 13:24:33', 'Dactylogyrus sp.', 42.90);

-- --------------------------------------------------------

--
-- Struktur dari tabel `penyakit`
--

CREATE TABLE `penyakit` (
  `id` int(11) NOT NULL,
  `kode_penyakit` varchar(10) DEFAULT NULL,
  `nama_penyakit` varchar(100) DEFAULT NULL,
  `jenis` enum('Bakteri','Virus','Jamur','Parasit') NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data untuk tabel `penyakit`
--

INSERT INTO `penyakit` (`id`, `kode_penyakit`, `nama_penyakit`, `jenis`, `deskripsi`, `created_at`) VALUES
(1, 'P001', 'Aeromonas hydrophila', 'Bakteri', 'Penyakit bakterial penyebab bercak merah pada tubuh ikan yang dapat menyebabkan luka, pendarahan, dan kematian ikan', '2026-05-20 12:56:24'),
(3, 'P003', 'Dactylogyrus sp.', 'Parasit', 'Parasit ektoparasit yang menyerang insang ikan sehingga menyebabkan gangguan pernapasan dan produksi lendir berlebih.', '2026-05-20 12:56:24'),
(4, 'P004', 'Epistylis sp.', 'Parasit', 'Parasit protozoa yang menyerang permukaan tubuh dan insang ikan serta berkembang pada perairan dengan bahan organik tinggi', '2026-05-20 12:56:24'),
(5, 'P005', 'Ichthyophthirius (Ich)', 'Parasit', 'Parasit Ichthyophthirius multifiliis pada kulit ikan.', '2026-05-20 12:56:24'),
(10, 'P006', 'Flexibacter sp.', 'Bakteri', 'Bakteri patogen penyebab penyakit kolumnaris yang ditandai dengan luka pada tubuh, pembusukan sirip, dan kerusakan jaringan ikan', '2026-06-08 15:10:31'),
(13, 'P002', 'Saprolegnia sp.', 'Jamur', 'Penyakit yang disebabkan oleh jamur air yang menyerang kulit, sirip, dan telur ikan dengan ciri muncul benang putih seperti kapas.', '2026-06-26 02:09:48');

-- --------------------------------------------------------

--
-- Struktur dari tabel `solusi`
--

CREATE TABLE `solusi` (
  `id` int(11) NOT NULL,
  `kode_solusi` varchar(10) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data untuk tabel `solusi`
--

INSERT INTO `solusi` (`id`, `kode_solusi`, `deskripsi`, `created_at`) VALUES
(6, 'S001', 'Berikan antibiotik sesuai dosis dan petunjuk penggunaan, seperti oksitetrasiklin. Dapat juga menggunakan bahan alami seperti ekstrak bawang putih atau kunyit yang memiliki sifat antibakteri. Pastikan kualitas pakan dan kebersihan air tetap terjaga, serta lakukan pemberian probiotik secara rutin untuk membantu meningkatkan daya tahan tubuh ikan dan mempercepat pemulihan.', '2026-06-22 13:48:36'),
(8, 'S002', 'Lakukan perendaman menggunakan larutan garam dengan konsentrasi 2–5 g/L selama 15–30 menit atau gunakan larutan KMnO₄ dengan dosis 2–4 mg/L sesuai kebutuhan. Alternatif lain dapat menggunakan ekstrak daun sirih atau bawang putih. Hindari luka pada ikan serta jaga kualitas air agar tetap bersih dan stabil selama masa pemulihan.', '2026-06-26 03:06:54'),
(9, 'S003', 'Lakukan perendaman menggunakan formalin sekitar 25 ppm atau gunakan obat antiparasit sesuai dosis yang dianjurkan. Sebelum penebaran ke kolam utama, lakukan karantina pada benih untuk mencegah penyebaran penyakit. Jaga kebersihan kolam dan lakukan pemantauan kondisi ikan secara berkala.', '2026-06-26 03:07:06'),
(10, 'S004', 'Gunakan perendaman dengan garam berkonsentrasi 3–5 g/L atau formalin dosis rendah sesuai kebutuhan. Bersihkan sisa pakan dan kotoran di kolam secara rutin agar tidak menjadi media berkembangnya penyebab penyakit. Pastikan kualitas air dan kepadatan ikan tetap terkontrol.', '2026-06-26 03:07:18'),
(11, 'S005', 'Gunakan kombinasi formalin dan malachite green sesuai dosis yang aman. Alternatif lain dapat menggunakan bahan alami seperti ekstrak daun sirih atau meniran. Jaga suhu air tetap stabil dan hindari perubahan lingkungan yang dapat menyebabkan stres pada ikan.', '2026-06-26 03:07:31'),
(12, 'S006', 'Lakukan perendaman menggunakan larutan KMnO₄ dengan dosis sekitar 2 mg/L atau berikan antibiotik apabila kondisi sudah cukup parah. Pastikan air tetap bersih, sirkulasi berjalan baik, serta hindari kondisi yang menyebabkan ikan saling bergesekan atau terluka.', '2026-06-26 03:07:41');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `basis_aturan`
--
ALTER TABLE `basis_aturan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_penyakit` (`id_penyakit`),
  ADD KEY `id_gejala` (`id_gejala`),
  ADD KEY `id_solusi` (`id_solusi`);

--
-- Indeks untuk tabel `gejala`
--
ALTER TABLE `gejala`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode` (`kode_gejala`);

--
-- Indeks untuk tabel `log_diagnosa`
--
ALTER TABLE `log_diagnosa`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `penyakit`
--
ALTER TABLE `penyakit`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode` (`kode_penyakit`);

--
-- Indeks untuk tabel `solusi`
--
ALTER TABLE `solusi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode` (`kode_solusi`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `basis_aturan`
--
ALTER TABLE `basis_aturan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT untuk tabel `gejala`
--
ALTER TABLE `gejala`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT untuk tabel `log_diagnosa`
--
ALTER TABLE `log_diagnosa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=271;

--
-- AUTO_INCREMENT untuk tabel `penyakit`
--
ALTER TABLE `penyakit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `solusi`
--
ALTER TABLE `solusi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `basis_aturan`
--
ALTER TABLE `basis_aturan`
  ADD CONSTRAINT `basis_aturan_ibfk_1` FOREIGN KEY (`id_penyakit`) REFERENCES `penyakit` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `basis_aturan_ibfk_2` FOREIGN KEY (`id_gejala`) REFERENCES `gejala` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `basis_aturan_ibfk_3` FOREIGN KEY (`id_solusi`) REFERENCES `solusi` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
