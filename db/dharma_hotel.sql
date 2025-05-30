-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 30, 2025 at 04:00 PM
-- Server version: 8.0.30
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dharma_hotel`
--
CREATE DATABASE IF NOT EXISTS `dharma_hotel` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `dharma_hotel`;

-- --------------------------------------------------------

--
-- Table structure for table `about`
--

DROP TABLE IF EXISTS `about`;
CREATE TABLE IF NOT EXISTS `about` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tentangkami_beranda` varchar(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `deskripsi` text NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `youtube` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `about`
--

INSERT INTO `about` (`id`, `tentangkami_beranda`, `deskripsi`, `alamat`, `facebook`, `instagram`, `youtube`) VALUES
(1, 'Dharma Hotel adalah tempat penginapan elegan dan nyaman yang terletak di jantung Kabupaten Blitar. Kami menawarkan pengalaman menginap yang tenang dan mewah, dengan berbagai fasilitas terbaik untuk wisatawan dan pelancong bisnis. Komitmen kami adalah memberikan layanan berkualitas tinggi dengan sentuhan budaya lokal.', 'Dharma Hotel adalah Hotel Bintang 5 Terbaik di Blitar Raya. Ayo Tunggu Apa lagi? Reservasi segera!!!', 'Jl. Kh. Muhammad Diki No. 1001, Kecamatan Damai, Kabupaten Blitar, Jawa Timur', 'dharmahotel', 'dharmahotel', 'dharmahotel');

-- --------------------------------------------------------

--
-- Table structure for table `bg`
--

DROP TABLE IF EXISTS `bg`;
CREATE TABLE IF NOT EXISTS `bg` (
  `id` int NOT NULL AUTO_INCREMENT,
  `gambar` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `bg`
--

INSERT INTO `bg` (`id`, `gambar`) VALUES
(1, 'bg_6823350f1287a.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `fasilitas`
--

DROP TABLE IF EXISTS `fasilitas`;
CREATE TABLE IF NOT EXISTS `fasilitas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) DEFAULT NULL,
  `deskripsi` text,
  `gambar` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `fasilitas`
--

INSERT INTO `fasilitas` (`id`, `nama`, `deskripsi`, `gambar`) VALUES
(1, 'Wi-Fi Gratis', 'Akses internet cepat dan stabil tersedia di seluruh area hotel untuk kenyamanan Anda.', 'wifi.jpg'),
(2, 'Parkir Luas', 'Area parkir yang aman dan luas tersedia untuk kendaraan tamu hotel tanpa biaya tambahan.', 'parkir.jpg'),
(3, 'Ballroom Mewah', 'Ballroom elegan dengan kapasitas besar, cocok untuk acara pernikahan, seminar, dan konferensi.', 'pernikahan.jpg'),
(4, 'Kolam Renang Outdoor', 'Kolam renang luar ruangan dengan pemandangan taman, cocok untuk relaksasi.', 'kolam.jpg'),
(5, 'Restoran 24 Jam', 'Nikmati hidangan lokal dan internasional di restoran hotel kami yang buka 24 jam.', 'restoran.jpg'),
(6, 'Spa dan Sauna', 'Fasilitas spa dan sauna tersedia untuk kenyamanan dan relaksasi Anda.', 'spa.jpg'),
(7, 'Pusat Kebugaran', 'Gym dengan peralatan lengkap tersedia gratis untuk semua tamu.', 'gym.jpg'),
(8, 'Layanan Kamar', 'Layanan kamar tersedia 24 jam untuk memenuhi kebutuhan Anda kapan saja.', 'roomservice.jpg'),
(9, 'Layanan Antar-Jemput Bandara', 'Kami menyediakan layanan antar-jemput ke bandara dengan biaya tambahan.', 'shuttle.jpg'),
(10, 'Laundry & Dry Cleaning', 'Layanan cuci dan setrika profesional untuk pakaian Anda.', 'laundry.jpg'),
(11, 'Ruang Rapat & Konferensi', 'Fasilitas pertemuan dilengkapi proyektor dan sound system modern.', 'meeting.jpg'),
(15, 'Keamanan 24 Jam', 'Keamanan dan CCTV aktif 24 jam demi kenyamanan tamu.', 'security.jpg'),
(16, 'Tempat Bermain Anak', 'PlayGround yang lengkap untuk anak Anda.', 'fasilitas_68233bdf23c66.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `galeri`
--

DROP TABLE IF EXISTS `galeri`;
CREATE TABLE IF NOT EXISTS `galeri` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kategori` enum('fasilitas','event','kamar','sekitar','lainnya') NOT NULL,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text,
  `gambar` varchar(255) NOT NULL,
  `tanggal` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `galeri`
--

INSERT INTO `galeri` (`id`, `kategori`, `judul`, `deskripsi`, `gambar`, `tanggal`) VALUES
(1, 'fasilitas', 'Kolam Renang', 'Kolam renang dengan fasilitas lengkap untuk kenyamanan tamu.', 'kolam.jpg', '2025-05-12 00:21:35'),
(2, 'fasilitas', 'Spa', 'Nikmati relaksasi terbaik di spa kami yang tenang.', 'spa.jpg', '2025-05-12 00:21:35'),
(3, 'fasilitas', 'Restoran', 'Restoran dengan menu lezat dan suasana yang nyaman.', 'restoran.jpg', '2025-05-12 00:21:35'),
(4, 'fasilitas', 'Gym', 'Fasilitas gym dengan peralatan modern untuk kebugaran Anda.', 'gym.jpg', '2025-05-12 00:21:35'),
(5, 'event', 'Pesta Ulang Tahun', 'Acara ulang tahun dengan dekorasi yang menakjubkan.', 'ulang_tahun.jpg', '2025-05-12 00:21:35'),
(6, 'event', 'Seminar Teknologi', 'Seminar mengenai perkembangan teknologi terbaru.', 'seminarteknologi.jpg', '2025-05-12 00:21:35'),
(7, 'event', 'Pameran Seni', 'Pameran seni kontemporer dengan karya-karya dari seniman lokal.', 'seni.jpg', '2025-05-12 00:21:35'),
(8, 'event', 'Konser Musik', 'Konser musik akustik di malam yang menyenangkan.', 'konser_musik.jpg', '2025-05-12 00:21:35'),
(9, 'kamar', 'Kamar Deluxe', 'Kamar deluxe dengan pemandangan yang menakjubkan dan fasilitas premium.', 'deluxe.jpg', '2025-05-12 00:21:35'),
(10, 'kamar', 'Kamar Standar', 'Kamar standar dengan kenyamanan untuk setiap tamu.', 'standard.jpg', '2025-05-12 00:21:35'),
(11, 'kamar', 'Kamar Superior', 'Kamar superior dengan desain modern dan fasilitas lengkap.', 'superior.jpg', '2025-05-12 00:21:35'),
(12, 'kamar', 'Kamar Bussiness', 'Kamar bussiness dengan layanan premium bagi tamu VIP.', 'business.jpg', '2025-05-12 00:21:35'),
(13, 'sekitar', 'Pantai', 'Pantai indah yang berada dekat dengan hotel kami.', 'pantai.jpg', '2025-05-12 00:21:35'),
(14, 'sekitar', 'Taman Kota', 'Taman kota yang asri dan cocok untuk berjalan-jalan santai.', 'tamankota.jpg', '2025-05-12 00:21:35'),
(15, 'sekitar', 'Pasar Tradisional', 'Pasar tradisional yang menawarkan berbagai barang unik dan khas.', 'pasar_tradisional.jpg', '2025-05-12 00:21:35'),
(16, 'sekitar', 'Monumen Bersejarah', 'Monumen bersejarah yang menjadi simbol kebanggaan kota.', 'monumen_bersejarah.jpg', '2025-05-12 00:21:35'),
(17, 'lainnya', 'Dekorasi Interior', 'Desain interior hotel yang elegan dan modern.', 'dekorasi_interior.jpg', '2025-05-12 00:21:35'),
(18, 'lainnya', 'Area Parkir', 'Area parkir yang luas dan aman untuk tamu kami.', 'parkir.jpg', '2025-05-12 00:21:35'),
(19, 'lainnya', 'Taman Bunga', 'Taman bunga yang indah untuk bersantai dan menikmati keindahan alam.', 'taman.jpg', '2025-05-12 00:21:35'),
(20, 'lainnya', 'Pemandangan Matahari Terbenam', 'Pemandangan matahari terbenam yang mempesona di hotel kami.', 'matahari_terbenam.jpg', '2025-05-12 00:21:35'),
(21, 'event', '11', 'njajal thok', 'galeri_1747152639.jpg', '2025-05-13 16:10:39');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
CREATE TABLE IF NOT EXISTS `news` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `gambar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `author` varchar(100) DEFAULT NULL,
  `published_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_published` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `slug`, `content`, `gambar`, `author`, `published_at`, `updated_at`, `is_published`) VALUES
(1, 'Promo Spesial Akhir Tahun di Dharma Hotel!!!', 'promo-spesial-akhir-tahun-dharma-hotel', 'Nikmati diskon hingga 30% untuk semua tipe kamar selama bulan Desember. Jangan lewatkan kesempatan menginap nyaman dengan harga hemat hanya di Dharma Hotel!', 'diskon.jpg', 'Admin Dharma', '2025-12-01 09:00:00', '2025-05-13 20:09:12', 1),
(2, 'Renovasi Lobby dan Fasilitas Baru!', 'renovasi-lobby-dan-fasilitas-baru', 'Kami sedang melakukan pembaruan besar pada area lobby utama dan akan menghadirkan fasilitas baru seperti lounge VIP dan coffee corner. Mohon maaf atas ketidaknyamanannya.', 'renovasi.jpg', 'Admin Dharma', '2025-11-20 10:30:00', '2025-05-12 06:39:45', 1),
(3, 'Lowongan Kerja: Resepsionis & Housekeeping', 'lowongan-kerja-resepsionis-housekeeping', 'Dharma Hotel membuka kesempatan bergabung sebagai resepsionis dan petugas housekeeping. Kirimkan lamaran Anda sebelum 15 Desember 2025.', 'housekeeping.jpg', 'HR Dharma', '2025-11-15 14:00:00', '2025-05-12 06:37:32', 1),
(4, 'Paket Honeymoon Spesial untuk Pengantin Baru', 'paket-honeymoon-spesial-pengantin-baru', 'Nikmati pengalaman romantis bersama pasangan dengan paket honeymoon yang kami tawarkan, lengkap dengan makan malam eksklusif dan dekorasi kamar spesial.', 'pakethoneymoon.jpg', 'Marketing Dharma', '2025-10-28 08:00:00', '2025-05-12 06:38:21', 1),
(5, 'Kegiatan CSR Dharma Hotel: Bersih-Bersih Pantai', 'csr-dharma-hotel-bersih-bersih-pantai', 'Sebagai bentuk kepedulian terhadap lingkungan, Dharma Hotel mengadakan kegiatan bersih-bersih pantai bersama staf dan warga sekitar pada tanggal 20 Oktober 2025.', 'csr.jpg', 'Admin Dharma', '2025-10-21 13:15:00', '2025-05-12 06:36:05', 1);

-- --------------------------------------------------------

--
-- Table structure for table `rekrutmen`
--

DROP TABLE IF EXISTS `rekrutmen`;
CREATE TABLE IF NOT EXISTS `rekrutmen` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `posisi` varchar(100) NOT NULL,
  `departemen` varchar(100) NOT NULL,
  `deskripsi` text,
  `kualifikasi` text,
  `gambar` varchar(255) DEFAULT NULL,
  `jumlah_kebutuhan` int NOT NULL,
  `tanggal_buka` date NOT NULL,
  `tanggal_tutup` date NOT NULL,
  `status` enum('dibuka','ditutup','selesai') DEFAULT 'dibuka',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `rekrutmen`
--

INSERT INTO `rekrutmen` (`id`, `posisi`, `departemen`, `deskripsi`, `kualifikasi`, `gambar`, `jumlah_kebutuhan`, `tanggal_buka`, `tanggal_tutup`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Resepsionis', 'Front Office', 'Melayani tamu check-in dan check-out serta memberikan informasi hotel.', 'Minimal D3 Perhotelan, komunikatif, berpenampilan menarik.', 'resepsionis.jpg', 2, '2025-06-01', '2025-06-30', 'dibuka', '2025-05-30 07:18:39', '2025-05-30 07:28:01'),
(2, 'Housekeeping', 'Operasional', 'Membersihkan dan merapikan kamar tamu serta area umum hotel.', 'Minimal SMA, rajin dan disiplin.', 'house.jpg', 3, '2025-06-01', '2025-06-25', 'dibuka', '2025-05-30 07:18:39', '2025-05-30 07:28:22'),
(3, 'Chef de Partie', 'F&B Kitchen', 'Bertanggung jawab atas bagian dapur tertentu.', 'Minimal D3 Tata Boga, pengalaman 2 tahun.', 'chef.jpg', 1, '2025-06-05', '2025-06-20', 'dibuka', '2025-05-30 07:18:39', '2025-05-30 08:30:16'),
(4, 'Security', 'Keamanan', 'Menjaga keamanan dan ketertiban area hotel.', 'Minimal SMA, tinggi badan minimal 165 cm.', 'satpam.jpg', 2, '2025-05-20', '2025-06-15', 'dibuka', '2025-05-30 07:18:39', '2025-05-30 07:28:46'),
(5, 'Marketing Executive', 'Pemasaran', 'Mengembangkan strategi pemasaran dan menjalin kerja sama bisnis.', 'Minimal S1 Pemasaran atau Komunikasi.', 'marketing.jpg', 1, '2025-06-01', '2025-06-30', 'ditutup', '2025-05-30 07:18:39', '2025-05-30 07:39:15'),
(6, 'Jaringan', 'F&B Kitchen', 'adas', 'wewe', '68396d5d3d6f3_Picture5.jpg', 1, '2025-05-30', '2025-06-07', 'dibuka', '2025-05-30 08:33:33', '2025-05-30 08:33:33'),
(7, 'sasa', 'sa', 'w', 'ssss', '68396df996b45_Picture4.jpg', 3, '2025-05-30', '2025-06-05', 'dibuka', '2025-05-30 08:36:09', '2025-05-30 08:36:09'),
(8, 'asasaas', 'q', 'aa', 'aa', '68396eaef124f_Blue Screen Of Death (2560x1440).jpg', 3, '2025-05-30', '2025-05-30', 'dibuka', '2025-05-30 08:39:10', '2025-05-30 08:39:10');

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

DROP TABLE IF EXISTS `room`;
CREATE TABLE IF NOT EXISTS `room` (
  `id_kamar` int NOT NULL AUTO_INCREMENT,
  `nama_kamar` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` varchar(1000) COLLATE utf8mb4_general_ci NOT NULL,
  `harga` int NOT NULL,
  `ketersediaan` int NOT NULL,
  `gambar` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_kamar`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room`
--

INSERT INTO `room` (`id_kamar`, `nama_kamar`, `deskripsi`, `harga`, `ketersediaan`, `gambar`) VALUES
(1, 'Standard', 'Kamar Standard kami dirancang untuk memberikan kenyamanan optimal dengan harga terjangkau. Dilengkapi dengan tempat tidur queen-size, AC, televisi layar datar, meja kerja, serta kamar mandi pribadi dengan shower air panas. Ideal untuk pelancong solo atau pasangan yang mencari tempat istirahat yang praktis dan bersih.', 300000, 997, 'standard.jpg'),
(2, 'Superior', 'Kamar Superior menawarkan ruang yang lebih luas dengan desain interior modern dan elegan. Fasilitas termasuk tempat tidur king-size, AC, LED TV, minibar, serta area duduk yang nyaman. Cocok untuk tamu yang menginginkan kenyamanan lebih selama menginap baik untuk urusan bisnis maupun liburan.', 450000, 998, 'superior.jpg'),
(3, 'Deluxe', 'Kamar Deluxe kami memberikan sentuhan kemewahan dengan balkon pribadi yang menyuguhkan pemandangan taman. Dilengkapi tempat tidur king-size, kamar mandi dengan bathtub, perlengkapan mandi premium, meja kerja, serta layanan kamar 24 jam. Sangat sesuai bagi tamu yang menginginkan pengalaman menginap yang lebih eksklusif.', 600000, 999, 'deluxe.jpg'),
(4, 'Suite', 'Suite adalah pilihan tertinggi dengan ruang tamu terpisah, kamar tidur luas, dan fasilitas lengkap seperti mesin kopi, kulkas, sofa nyaman, serta kamar mandi mewah dengan bathtub dan shower terpisah. Suite sangat ideal untuk keluarga atau tamu VIP yang mendambakan kenyamanan maksimal dan privasi selama menginap.', 900000, 999, 'suite.jpg'),
(5, 'Presidential Suite', 'Kamar termewah dengan layanan pribadi, jacuzzi, dan akses lounge VIP.', 1200000, 1000, 'presidential.jpg'),
(6, 'Single Room', 'Kamar kecil dengan satu tempat tidur, cocok untuk pelancong solo.', 200000, 1000, 'single.jpg'),
(8, 'Business Room', 'Dilengkapi dengan meja kerja, WiFi cepat, dan pencahayaan yang baik untuk keperluan kerja.', 500000, 1000, 'business.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `nik` varchar(20) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` enum('L','P') DEFAULT NULL,
  `alamat` text,
  `telepon` varchar(20) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `nik` (`nik`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `foto`, `nik`, `tanggal_lahir`, `jenis_kelamin`, `alamat`, `telepon`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'diki@example.com', 'admin', 'Diki', 'user.jpg', '32100123456700045', '2025-05-13', 'L', 'Jl. Melati No.109', 'Jl. Melati No.109', 'admin', '2025-05-11 07:48:00', '2025-05-13 09:07:18'),
(2, 'icas', 'icas@example.com', 'icas', 'Icas', 'user.jpg', '3210012345670002', '1999-05-01', 'L', 'Jl. Melati No.109', 'Jl. Melati No.109', 'admin', '2025-05-11 07:48:00', '2025-05-13 09:07:23'),
(3, 'dharma', 'dharma@example.com', 'dharma', 'Dharma', 'user.jpg', '3210012345670004', '1999-05-01', 'L', 'Jl. Melati No.109', 'Jl. Melati No.109', 'admin', '2025-05-11 07:48:00', '2025-05-13 09:07:19'),
(7, 'user', 'andi@example.com', 'user', 'Andi Saputra', 'user.jpg', '3210012345670001', '1999-05-01', 'L', 'Jl. Melati No.109', '081234560001', 'user', '2025-05-11 23:58:56', '2025-05-13 09:03:55');

-- --------------------------------------------------------

--
-- Table structure for table `reservasi`
--

DROP TABLE IF EXISTS `reservasi`;
CREATE TABLE IF NOT EXISTS `reservasi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `jumlah_tamu` int DEFAULT '1',
  `room_id` int NOT NULL,
  `status` enum('pending','disetujui','dibatalkan') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'pending',
  `total_harga` decimal(10,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `fk_room_id` (`room_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `reservasi`
--

INSERT INTO `reservasi` (`id`, `user_id`, `check_in`, `check_out`, `jumlah_tamu`, `room_id`, `status`, `total_harga`, `created_at`, `updated_at`) VALUES
(9, 7, '2025-04-29', '2025-05-21', 11121, 1, 'disetujui', 6600000.00, '2025-05-13 08:36:12', '2025-05-13 08:51:26'),
(12, 7, '2025-06-06', '2025-06-20', 3, 1, 'pending', 12600000.00, '2025-05-13 16:00:13', '2025-05-13 16:00:13'),
(13, 7, '2025-05-14', '2025-05-30', 1, 1, 'dibatalkan', 4800000.00, '2025-05-13 16:02:07', '2025-05-13 16:17:09'),
(14, 7, '2025-05-14', '2025-05-23', 1, 1, 'pending', 2700000.00, '2025-05-13 16:16:41', '2025-05-13 16:16:41');

-- --------------------------------------------------------

--
-- Table structure for table `pelamar`
--

DROP TABLE IF EXISTS `pelamar`;
CREATE TABLE IF NOT EXISTS `pelamar` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `rekrutmen_id` bigint NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `alamat` text,
  `pendidikan_terakhir` varchar(100) DEFAULT NULL,
  `pengalaman_kerja` text,
  `cv_file` varchar(255) DEFAULT NULL,
  `status_lamaran` enum('baru','diproses','ditolak','diterima') DEFAULT 'baru',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_rekrutmen` (`rekrutmen_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pelamar`
--

INSERT INTO `pelamar` (`id`, `rekrutmen_id`, `nama_lengkap`, `email`, `no_hp`, `alamat`, `pendidikan_terakhir`, `pengalaman_kerja`, `cv_file`, `status_lamaran`, `created_at`, `updated_at`) VALUES
(1, 1, 'Andi Saputra', 'andi@example.com', '081234567890', 'Jl. Melati No. 10, Blitar', 'SMA', 'Petani', 'cv/andi_saputra.pdf', 'baru', '2025-06-01 17:00:00', '2025-05-30 07:22:09'),
(2, 1, 'Putri Lestari', 'putri@example.com', '081298765432', 'Jl. Kenanga No. 5, Blitar', 'SMA', 'Petani', 'cv/putri_lestari.pdf', 'baru', '2025-06-02 17:00:00', '2025-05-30 07:22:10'),
(3, 2, 'Budi Santoso', 'budi@example.com', '081345678912', 'Jl. Mawar No. 12, Blitar', 'SMA', 'Petani', 'cv/budi_santoso.pdf', 'baru', '2025-05-31 17:00:00', '2025-05-30 07:22:10'),
(4, 3, 'Sari Ayuningtyas', 'sari@example.com', '081234123412', 'Jl. Anggrek No. 3, Blitar', 'SMA', 'Petani', 'cv/sari_ayuningtyas.pdf', 'baru', '2025-06-05 17:00:00', '2025-05-30 07:22:11'),
(5, 4, 'Rian Firmansyah', 'rian@example.com', '081212121212', 'Jl. Cempaka No. 7, Blitar', 'SMA', 'Petani', 'cv/rian_firmansyah.pdf', 'baru', '2025-05-24 17:00:00', '2025-05-30 07:22:14'),
(7, 1, 'asas', 'admin@gmail.com', '12', '222', 'qqwee', 'ewe', '68396edbd23da_14022-Article Text-64052-1-10-20240614.pdf', 'baru', '2025-05-30 08:39:55', '2025-05-30 08:39:55');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

DROP TABLE IF EXISTS `pembayaran`;
CREATE TABLE IF NOT EXISTS `pembayaran` (
  `id` int NOT NULL AUTO_INCREMENT,
  `reservasi_id` int NOT NULL,
  `foto_bukti` varchar(255) DEFAULT NULL,
  `tanggal_bayar` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` enum('menunggu_verifikasi','terverifikasi','ditolak') DEFAULT 'menunggu_verifikasi',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `reservasi_id` (`reservasi_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id`, `reservasi_id`, `foto_bukti`, `tanggal_bayar`, `status`, `created_at`, `updated_at`) VALUES
(8, 9, 'bukti_682307bb2b9af-{081937DA-6C89-4C03-A388-2AEA8113AC8E}.png', '2025-05-13 15:36:12', 'terverifikasi', '2025-05-13 15:36:12', '2025-05-13 15:51:04'),
(11, 12, 'bukti_68236d0e00f81-2.-The-Playground.jpg', '2025-05-13 23:00:13', 'menunggu_verifikasi', '2025-05-13 23:00:13', '2025-05-13 23:02:22'),
(12, 13, NULL, '2025-05-13 23:02:07', 'ditolak', '2025-05-13 23:02:07', '2025-05-13 23:17:09'),
(13, 14, NULL, '2025-05-13 23:16:41', 'menunggu_verifikasi', '2025-05-13 23:16:41', '2025-05-13 23:16:41');

-- --------------------------------------------------------

--
-- Table structure for table `ulasan`
--

DROP TABLE IF EXISTS `ulasan`;
CREATE TABLE IF NOT EXISTS `ulasan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `isi_ulasan` text NOT NULL,
  `rating` tinyint DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `reservasi_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `fk_ulasan_reservasi` (`reservasi_id`),
  CONSTRAINT `ulasan_chk_1` CHECK ((`rating` between 1 and 5))
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `ulasan`
--

INSERT INTO `ulasan` (`id`, `user_id`, `isi_ulasan`, `rating`, `tanggal`, `reservasi_id`) VALUES
(52, 7, '123', 4, '2025-05-13', 9);


--
-- Constraints for dumped tables
--

--
-- Constraints for table `pelamar`
--
ALTER TABLE `pelamar`
  ADD CONSTRAINT `fk_rekrutmen` FOREIGN KEY (`rekrutmen_id`) REFERENCES `rekrutmen` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`reservasi_id`) REFERENCES `reservasi` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reservasi`
--
ALTER TABLE `reservasi`
  ADD CONSTRAINT `fk_room_id` FOREIGN KEY (`room_id`) REFERENCES `room` (`id_kamar`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `reservasi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ulasan`
--
ALTER TABLE `ulasan`
  ADD CONSTRAINT `fk_ulasan_reservasi` FOREIGN KEY (`reservasi_id`) REFERENCES `reservasi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ulasan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;