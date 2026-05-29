-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql301.byetcluster.com
-- Waktu pembuatan: 28 Bulan Mei 2026 pada 15.51
-- Versi server: 11.4.11-MariaDB
-- Versi PHP: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_42042343_tokoelektroniksuryamakmur`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `barang`
--

CREATE TABLE `barang` (
  `id_barang` int(11) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `harga_jual` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `barang`
--

INSERT INTO `barang` (`id_barang`, `id_kategori`, `nama_barang`, `harga_jual`) VALUES
(1, 1, 'Kipas Angin Miyako KAD-1827', 185000),
(2, 1, 'Kipas Angin Cosmos CAF-1826', 175000),
(3, 1, 'Kipas Angin Dinding Panasonic F-409', 350000),
(4, 1, 'Kipas Angin Tornado Maspion MV-1803', 165000),
(5, 1, 'Kipas Angin Duduk Sanken SFD-88', 220000),
(6, 1, 'Kipas Angin Stand Polytron PSF-50AR', 395000),
(7, 1, 'Kipas Angin Tower Sharp PJC13TBK', 650000),
(8, 1, 'Kipas Angin Exhaust Panasonic FV-20', 450000),
(9, 1, 'Kipas Angin Mini USB Krisbow', 85000),
(10, 1, 'Kipas Angin Langit-langit KDK 56SEP', 1250000),
(11, 2, 'AC Daikin FTKC25UV 1 PK', 3950000),
(12, 2, 'AC Panasonic CS-PN5WKJ 0.5 PK', 2750000),
(13, 2, 'AC Sharp AH-A5UCD 0.5 PK', 2650000),
(14, 2, 'AC LG T05EV4 0.5 PK', 2850000),
(15, 2, 'AC Samsung AR05TGHQASINFE 0.5 PK', 2900000),
(16, 2, 'AC Daikin FTKC35UV 1.5 PK', 4750000),
(17, 2, 'AC Polytron PAC-12FTM 1 PK', 3200000),
(18, 2, 'AC Gree GWC-09D3D 1 PK', 3100000),
(19, 2, 'AC Aqua QAC-09FBCV 1 PK', 3050000),
(20, 2, 'AC Haier HSU-09VKT 1 PK', 3150000),
(21, 3, 'Kulkas 1 Pintu Sharp SJ-11MSD', 1750000),
(22, 3, 'Kulkas 1 Pintu Aqua AQR-D115 100L', 1550000),
(23, 3, 'Kulkas 1 Pintu Polytron PRG 11800', 1600000),
(24, 3, 'Kulkas 2 Pintu LG GN-B372PLGB', 4200000),
(25, 3, 'Kulkas 2 Pintu Samsung RT38K501JS8', 4500000),
(26, 3, 'Kulkas 2 Pintu Sharp SJ-246MD', 3950000),
(27, 3, 'Kulkas 2 Pintu Panasonic NR-BN301', 4100000),
(28, 3, 'Kulkas Side by Side Samsung', 12500000),
(29, 3, 'Kulkas 2 Pintu Hisense RD-23DR', 3250000),
(30, 3, 'Kulkas Mini Sanken SRF-119 45L', 750000),
(31, 4, 'Mesin Cuci Sharp ES-G705P-S 7KG', 1750000),
(32, 4, 'Mesin Cuci Polytron PAW 7511 7KG', 1650000),
(33, 4, 'Mesin Cuci Sanken AW-S898TR 8KG', 1900000),
(34, 4, 'Mesin Cuci LG P750N 7KG 2 Tabung', 1550000),
(35, 4, 'Mesin Cuci Samsung WA10T5260BY 10KG', 3200000),
(36, 4, 'Mesin Cuci Front Load Panasonic 7KG', 4500000),
(37, 4, 'Mesin Cuci Aqua AQW-100DD 10KG', 2350000),
(38, 4, 'Mesin Cuci Electrolux EWT1274D2WB', 3800000),
(39, 4, 'Mesin Cuci Bosch WAJ24060ID 8KG', 6500000),
(40, 4, 'Mesin Cuci LG FV1409S3W 9KG Front', 5800000),
(41, 5, 'TV LED Polytron 32 PLD-32V1853', 1950000),
(42, 5, 'TV LED TCL 32A3 Smart TV', 1800000),
(43, 5, 'TV LED Panasonic TH-32FS500G 32\"', 2350000),
(44, 5, 'TV LED Samsung 32\" UA32T4500AK Smart', 2750000),
(45, 5, 'TV LED LG 43\" 43LM5500PTA Smart', 4250000),
(46, 5, 'TV LED Sony KDL-43W660G 43\" Smart', 5500000),
(47, 5, 'TV LED Samsung 55\" UA55TU8000K UHD', 8500000),
(48, 5, 'TV LED LG 55\" 55NANO75TPA NanoCell', 9500000),
(49, 5, 'TV OLED Sony KD-55A8H 55\"', 22000000),
(50, 5, 'TV LED Sharp 40\" 2T-C40EG1i Android', 3750000),
(51, 6, 'Speaker Bluetooth JBL Go 3', 450000),
(52, 6, 'Speaker Bluetooth JBL Flip 6', 1350000),
(53, 6, 'Speaker Bluetooth Sony SRS-XB23', 1250000),
(54, 6, 'Speaker Tower Polytron PAS-8E28', 850000),
(55, 6, 'Soundbar Samsung HW-T400', 1650000),
(56, 6, 'Headphone Sony WH-1000XM4', 4500000),
(57, 6, 'Headphone JBL Tune 510BT', 550000),
(58, 6, 'Earphone Samsung Galaxy Buds2', 1350000),
(59, 6, 'Radio Tape Polytron XS-2301', 650000),
(60, 6, 'Home Theater Sony HT-S20R', 2950000),
(61, 7, 'Samsung Galaxy A15 6/128GB', 2499000),
(62, 7, 'Samsung Galaxy A35 8/256GB', 4499000),
(63, 7, 'Xiaomi Redmi 13C 8/256GB', 1999000),
(64, 7, 'Xiaomi Redmi Note 13 8/256GB', 2999000),
(65, 7, 'Oppo A18 4/128GB', 1799000),
(66, 7, 'Oppo Reno 11F 5G 8/256GB', 3999000),
(67, 7, 'Realme C67 8/256GB', 2199000),
(68, 7, 'Vivo Y28 8/256GB', 2299000),
(69, 7, 'Samsung Galaxy Tab A9 4/64GB', 2799000),
(70, 7, 'Xiaomi Pad 6 8/256GB', 4999000),
(71, 8, 'Laptop Acer Aspire 3 A314 i3-N305', 5499000),
(72, 8, 'Laptop Lenovo IdeaPad Slim 3 i5', 7499000),
(73, 8, 'Laptop ASUS Vivobook 14 i3', 5999000),
(74, 8, 'Laptop HP 14s-dq5102TU i3', 5799000),
(75, 8, 'Laptop ASUS TUF Gaming A15 Ryzen 5', 10999000),
(76, 8, 'Laptop Lenovo LOQ 15IRX9 i5 RTX3050', 12999000),
(77, 8, 'Printer Canon PIXMA G2020', 1350000),
(78, 8, 'Printer Epson EcoTank L3250 WiFi', 2150000),
(79, 8, 'Monitor LG 24\" 24MK430H-B FHD', 1850000),
(80, 8, 'Keyboard Wireless Logitech MK235', 350000),
(81, 9, 'Rice Cooker Miyako MCM-509', 285000),
(82, 9, 'Rice Cooker Panasonic SR-WN18 1.8L', 550000),
(83, 9, 'Blender Philips HR2041 2L', 450000),
(84, 9, 'Blender Sharp EM-13L-W', 350000),
(85, 9, 'Setrika Philips GC1010', 185000),
(86, 9, 'Setrika Uap Panasonic NI-M300TGSP', 650000),
(87, 9, 'Microwave Panasonic NN-ST34HMTTE', 1250000),
(88, 9, 'Microwave Sharp R-21D0(S)V-IN', 1150000),
(89, 9, 'Dispenser Miyako WD-389 HDC', 350000),
(90, 9, 'Water Heater Ariston AN6 6L', 1150000),
(91, 10, 'Kabel HDMI Rexus 2m', 85000),
(92, 10, 'Stabilizer Listrik Matsunaga SVC 500', 350000),
(93, 10, 'Stop Kontak Panasonic 5 Lubang', 125000),
(94, 10, 'Powerbank Xiaomi 10000mAh 22.5W', 285000),
(95, 10, 'Charger Adaptor 65W GaN Anker', 350000),
(96, 10, 'Kabel USB-C 1m Baseus', 95000),
(97, 10, 'Memory Card SanDisk 64GB Class10', 185000),
(98, 10, 'Flash Disk SanDisk Cruzer Blade 32GB', 85000),
(99, 10, 'Remote AC Universal Chunghop', 125000),
(100, 10, 'Bracket TV Dinding 32-55\" Sturdy', 185000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_transaksi`
--

CREATE TABLE `detail_transaksi` (
  `id_barang` int(11) NOT NULL,
  `id_transaksi` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `subtotal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `detail_transaksi`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE IF NOT EXISTS `admin` (
  `id_admin` INT NOT NULL AUTO_INCREMENT,
  `nama`     VARCHAR(100) NOT NULL,
  `username` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id_admin`),
  UNIQUE KEY `uq_admin_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id_admin`, `nama`, `username`, `password`) VALUES
(1, 'Administrator', 'admin', '$2y$10$Nu35w4pteLfc7BDCIkDPkecjw8wsH8Y2GMfIewUbXLT7zzW6WOxwq');

-- -------------------------------------------------------


--
-- Dumping data untuk tabel `admin`
--

-- -------------------------------------------------------

INSERT INTO `detail_transaksi` (`id_barang`, `id_transaksi`, `jumlah`, `subtotal`) VALUES
(1, 1, 1, 185000),
(81, 1, 1, 285000),
(85, 1, 2, 370000),
(51, 1, 1, 450000),
(89, 1, 2, 700000),
(93, 1, 3, 375000),
(99, 1, 1, 125000),
(91, 1, 5, 425000),
(9, 1, 2, 170000),
(98, 1, 5, 425000),
(80, 1, 1, 350000),
(62, 2, 1, 4499000),
(69, 2, 1, 2799000),
(57, 2, 1, 550000),
(77, 2, 1, 1350000),
(94, 2, 2, 570000),
(96, 2, 3, 285000),
(98, 2, 3, 255000),
(11, 3, 1, 3950000),
(91, 3, 2, 170000),
(99, 3, 1, 125000),
(96, 3, 1, 95000),
(93, 3, 1, 45000),
(49, 4, 1, 22000000),
(100, 4, 1, 185000),
(91, 4, 2, 170000),
(92, 4, 1, 350000),
(99, 4, 1, 125000),
(63, 5, 1, 1999000),
(57, 5, 1, 550000),
(96, 5, 1, 95000),
(75, 6, 1, 10999000),
(80, 6, 1, 350000),
(91, 6, 2, 170000),
(98, 6, 2, 170000),
(96, 6, 1, 95000),
(42, 7, 1, 1800000),
(47, 8, 1, 8500000),
(35, 8, 1, 3200000),
(24, 8, 1, 4200000),
(100, 8, 1, 185000),
(92, 8, 1, 350000),
(91, 8, 1, 85000),
(61, 9, 1, 2499000),
(83, 9, 1, 450000),
(82, 9, 1, 550000),
(85, 9, 2, 370000),
(94, 9, 1, 285000),
(97, 9, 1, 185000),
(93, 9, 2, 250000),
(98, 9, 1, 85000),
(72, 10, 1, 7499000),
(80, 10, 1, 350000),
(64, 11, 1, 2999000),
(96, 11, 1, 95000),
(98, 11, 1, 85000),
(49, 12, 1, 22000000),
(56, 12, 1, 4500000),
(100, 12, 1, 185000),
(91, 12, 1, 85000),
(92, 12, 1, 350000),
(44, 13, 1, 2750000),
(54, 13, 1, 850000),
(81, 13, 2, 570000),
(89, 13, 1, 350000),
(93, 13, 2, 250000),
(99, 13, 1, 125000),
(98, 13, 2, 170000),
(31, 14, 1, 1750000),
(87, 14, 1, 1250000),
(84, 14, 1, 350000),
(85, 14, 1, 185000),
(89, 14, 1, 350000),
(93, 14, 1, 125000),
(96, 14, 1, 95000),
(99, 14, 1, 125000),
(91, 14, 1, 85000),
(76, 15, 1, 12999000),
(46, 15, 1, 5500000),
(91, 15, 1, 85000),
(100, 15, 1, 185000),
(96, 15, 1, 95000),
(48, 16, 1, 9500000),
(100, 16, 1, 185000),
(67, 17, 1, 2199000),
(59, 17, 1, 650000),
(83, 17, 1, 450000),
(94, 17, 1, 285000),
(96, 17, 1, 95000),
(98, 17, 2, 170000),
(25, 18, 1, 4500000),
(36, 18, 1, 4500000),
(14, 18, 1, 2850000),
(93, 18, 3, 375000),
(99, 18, 1, 125000),
(91, 18, 2, 170000),
(98, 18, 1, 85000),
(9, 19, 2, 170000),
(89, 19, 1, 350000),
(93, 19, 3, 375000),
(99, 19, 1, 125000),
(91, 19, 2, 170000),
(66, 20, 1, 3999000),
(55, 20, 1, 1650000),
(88, 20, 1, 1150000),
(82, 20, 1, 550000),
(94, 20, 1, 285000),
(97, 20, 2, 370000),
(98, 20, 2, 170000),
(93, 20, 1, 125000),
(99, 20, 1, 125000),
(91, 20, 1, 85000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kasir`
--

CREATE TABLE `kasir` (
  `id_kasir` int(11) NOT NULL,
  `nama_kasir` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kasir`
--

INSERT INTO `kasir` (`id_kasir`, `nama_kasir`, `username`, `password`) VALUES
(1, 'Dzulfikar Nuril Al-Amien', 'dzulfikar', 'dzulfikar123'),
(2, 'Zukovski Tangguh Dirgantara', 'zukovski', 'zukovski123'),
(3, 'Dhimas Dwi Prasetyo', 'dhimas', 'dhimas123'),
(4, 'Dewi Lestari', 'dewi', 'dewi321'),
(5, 'Rizky Pratama', 'rizky', 'rizky654');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`) VALUES
(1, 'Kipas Angin'),
(2, 'Air Conditioner'),
(3, 'Kulkas'),
(4, 'Mesin Cuci'),
(5, 'Televisi'),
(6, 'Audio & Speaker'),
(7, 'Smartphone & Tablet'),
(8, 'Laptop & Komputer'),
(9, 'Peralatan Dapur'),
(10, 'Aksesoris Elektronik');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `id_kasir` int(11) NOT NULL,
  `tanggal` datetime DEFAULT NULL,
  `total` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `id_kasir`, `tanggal`, `total`) VALUES
(1, 1, '2025-01-03 08:15:00', 3935000),
(2, 2, '2025-01-04 09:30:00', 10299000),
(3, 3, '2025-01-05 10:00:00', 4385000),
(4, 1, '2025-01-06 11:20:00', 14750000),
(5, 4, '2025-01-07 13:00:00', 2635000),
(6, 2, '2025-01-08 14:15:00', 8950000),
(7, 5, '2025-01-09 08:45:00', 1800000),
(8, 3, '2025-01-10 10:30:00', 18200000),
(9, 1, '2025-01-11 12:00:00', 5284000),
(10, 4, '2025-01-12 15:00:00', 7649000),
(11, 5, '2025-01-13 09:00:00', 3149000),
(12, 2, '2025-01-14 11:45:00', 22950000),
(13, 3, '2025-01-15 13:30:00', 6448000),
(14, 1, '2025-01-16 16:00:00', 4535000),
(15, 4, '2025-01-17 08:20:00', 16299000),
(16, 5, '2025-01-18 10:10:00', 9085000),
(17, 2, '2025-01-19 14:00:00', 3849000),
(18, 3, '2025-01-20 09:50:00', 12998000),
(19, 1, '2025-01-21 11:00:00', 1220000),
(20, 4, '2025-01-22 15:30:00', 8784000);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id_barang`),
  ADD KEY `fk_barang_ke_kategori` (`id_kategori`);

--
-- Indeks untuk tabel `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD KEY `fk_detailtransaksi_ke_barang` (`id_barang`),
  ADD KEY `fk_detailtransaksi_ke_transaksi` (`id_transaksi`);

--
-- Indeks untuk tabel `kasir`
--
ALTER TABLE `kasir`
  ADD PRIMARY KEY (`id_kasir`);

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indeks untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `fk_transaksi_ke_kasir` (`id_kasir`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `barang`
--
ALTER TABLE `barang`
  MODIFY `id_barang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT untuk tabel `kasir`
--
ALTER TABLE `kasir`
  MODIFY `id_kasir` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `barang`
--
ALTER TABLE `barang`
  ADD CONSTRAINT `fk_barang_ke_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD CONSTRAINT `fk_detailtransaksi_ke_barang` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_detailtransaksi_ke_transaksi` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `fk_transaksi_ke_kasir` FOREIGN KEY (`id_kasir`) REFERENCES `kasir` (`id_kasir`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
