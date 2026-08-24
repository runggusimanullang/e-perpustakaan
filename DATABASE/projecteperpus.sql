-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 21, 2025 at 02:43 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `projecteperpus`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(150) NOT NULL,
  `foto` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `fullname`, `email`, `password`, `foto`) VALUES
(18, 'admin', 'admin', 'auth@gmail.com', '21232f297a57a5a743894a0e4a801fc3', 'admin_20250715_040740.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `book`
--

CREATE TABLE `book` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `adminor` varchar(100) NOT NULL,
  `publisher` varchar(100) NOT NULL,
  `amount` int(11) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `photo_filename` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `pdf_filename` varchar(100) DEFAULT NULL,
  `nomor_rak_buku` varchar(20) NOT NULL,
  `tahun` year(4) NOT NULL,
  `isbn` char(13) NOT NULL
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book`
--

INSERT INTO `book` (`id`, `title`, `adminor`, `publisher`, `amount`, `category`, `photo_filename`, `description`, `pdf_filename`, `nomor_rak_buku`, `tahun`, `isbn`) VALUES
(25, 'Contoh 1', 'Dedy Suhendra', 'Pusat Perbukuan', 45, 'Sastra', 'Contoh 1_20250704_153920.webp', 'Belajar adalah cara untuk mencari ilmu dan pengetahuan', 'Contoh 1_1751636360.pdf', 'A112', '2021', '9786026640055'),
(26, 'Contoh 2', 'Dr. Riinawati', ' KANHAYA KARYA', 19, 'Sains dan Teknologi', 'Contoh 2_20250704_154052.png', 'Sekolah tempat kita di ajarkan tentang ilmu adap dan pengetahuan', 'Contoh 2_1751636452.pdf', 'K787', '2018', '9786237349143'),
(27, 'Pendidikan Pancasila', 'Rusdi Hidayat', 'Universitas mahakam', 16, 'Ensiklopedia', 'Pendidikan Pancasila_20250715_041059.jpg', 'Saling meghargai untuk menjaga kerukunan antar warga', 'Pendidikan Pancasila_1752545459.pdf', 'LL26', '2009', '098765432143'),
(28, 'Contih 4', 'Seyapno', 'Budias', 67, 'Novel', 'Contih 4_20250704_154347.jpeg', 'Akan mencari kehidupan akan ilmu', 'Contih 4_1751636627.pdf', 'LKU01', '2023', '23456789990'),
(29, 'Contoh 5', 'Elit ', 'Pusat Perbukuan', 22, 'Ensiklopedia', 'Contoh 5_20250704_173433.png', 'Saat kita ingin untuk bahagia kita harus berani untuk hidup', 'Contoh 5_1751643273.pdf', 'Z543', '2020', '09766576543');

-- --------------------------------------------------------

--
-- Table structure for table `borrowing`
--

CREATE TABLE `borrowing` (
  `id` int(11) NOT NULL,
  `borrower_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `loan_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `status` varchar(50) NOT NULL,
  `actual_return_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrowing`
--

INSERT INTO `borrowing` (`id`, `borrower_id`, `book_id`, `loan_date`, `return_date`, `status`, `actual_return_date`) VALUES
(96, 25, 25, '2025-07-04', '2025-07-06', 'Sudah Kembali', '2025-07-10 00:00:00'),
(97, 25, 26, '2025-07-04', '2025-07-06', 'Sudah Kembali', '2025-07-08 00:00:00'),
(98, 26, 29, '2025-07-08', '2025-07-10', 'Sudah Kembali', '2025-07-11 00:00:00'),
(99, 26, 29, '2025-07-08', '2025-07-10', 'Sudah Kembali', '2025-07-11 00:00:00'),
(100, 27, 27, '2025-07-11', '2025-07-13', 'Dipinjam', NULL),
(101, 26, 27, '2025-07-19', '2025-07-21', 'Dipinjam', NULL),
(102, 25, 27, '2025-07-19', '2025-07-21', 'Dipinjam', NULL),
(103, 25, 25, '2025-07-17', '2025-07-19', 'Sudah Kembali', '2025-07-20 00:00:00'),
(104, 25, 27, '2025-07-20', '2025-07-22', 'Dipinjam', NULL),
(105, 25, 29, '2025-07-20', '2025-07-22', 'Sudah Kembali', '2025-07-20 00:00:00'),
(107, 25, 29, '2025-07-20', '2025-07-22', 'Sudah Kembali', '2025-07-20 00:00:00'),
(108, 25, 26, '2025-07-20', '2025-07-22', 'Belum Kembali', NULL),
(109, 26, 29, '2025-07-20', '2025-07-22', 'Belum Kembali', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `address` varchar(200) NOT NULL,
  `kelas` varchar(20) DEFAULT NULL,
  `phone_number` varchar(15) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `photo_filename` varchar(100) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`id`, `fullname`, `address`, `kelas`, `phone_number`, `gender`, `photo_filename`, `username`, `password`) VALUES
(25, 'Siswa Satu', 'Cikarang', '4', '+6282278988722', 'laki-laki', 'Siswa Satu_20250704_153619.jpeg', 'member1', 'c7764cfed23c5ca3bb393308a0da2306'),
(26, 'Siswa Dua', 'Jayakarta', '3', '+6287765432109', 'perempuan', 'Siswa Dua_20250704_154750.jpeg', 'member2', '88ed421f060aadcacbd63f28d889797f'),
(27, 'Siswa Tiga', 'Kuningan', '6', '+62877652311236', 'perempuan', 'Siswa Tiga_20250704_173041.jpeg', 'member3', '3ef4802d8a37022fd187fbd829d1c4d6'),
(28, 'Siswa Empat', 'Cikaruya', '5', '+6285567566544', 'perempuan', 'Siswa Empat_20250704_173214.jpeg', 'member4', 'a998123003066ac9fa7de4b100e7c4bc'),
(29, 'Della Novtrisia', 'Cawang', '6', '+628223456789', 'perempuan', 'Della Novtrisia_20250715_041246.jpeg', 'member5', 'd2515df72fadf7066556b3a4253afcdc');

--
-- Indexes for dumped tables
--

CREATE TABLE bills (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    borrowing_id INT(11) NOT NULL,
    borrower_id INT(11) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('Belum Dibayar', 'Menunggu Verifikasi', 'Sudah Dibayar', 'Dibatalkan') DEFAULT 'Belum Dibayar',
    payment_date DATE NULL,
    payment_method VARCHAR(50) NULL,
    payment_proof VARCHAR(255) NULL 
    created_at DATETIME NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (borrowing_id) REFERENCES borrowing(id),
    FOREIGN KEY (borrower_id) REFERENCES siswa(id) -- atau tabel user yang sesuai
);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `book`
--
ALTER TABLE `book`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `borrowing`
--
ALTER TABLE `borrowing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `borrower_id` (`borrower_id`);

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `book`
--
ALTER TABLE `book`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `borrowing`
--
ALTER TABLE `borrowing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `borrowing`
--
ALTER TABLE `borrowing`
  ADD CONSTRAINT `borrowing_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `book` (`id`),
  ADD CONSTRAINT `borrowing_ibfk_3` FOREIGN KEY (`borrower_id`) REFERENCES `siswa` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
