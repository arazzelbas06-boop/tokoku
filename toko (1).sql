-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 09, 2026 at 07:22 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `toko`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `CariPelanggan` (IN `p_id` INT)   BEGIN
    SELECT *
    FROM pelanggan
    WHERE id_pelanggan = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `HitungPelanggan` (OUT `jumlah` INT)   BEGIN
    SELECT COUNT(*)
    INTO jumlah
    FROM pelanggan;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `LaporanTransaksi` (IN `p_id_pelanggan` INT)   BEGIN
    SELECT
        t.id_transaksi,
        p.nama AS nama_pelanggan,
        t.tanggal,
        t.nama_barang,
        t.jumlah,
        t.harga_satuan,
        t.total
    FROM transaksi t
    JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
    WHERE t.id_pelanggan = p_id_pelanggan
    ORDER BY t.tanggal;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `TambahPelanggan` (IN `p_nama` VARCHAR(100), IN `p_kota` VARCHAR(50))   BEGIN
    INSERT INTO pelanggan (nama, kota)
    VALUES (p_nama, p_kota);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `TampilPelanggan` ()   BEGIN
    SELECT * FROM pelanggan;
END$$

--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `HitungDiskon` (`harga` DECIMAL(12,2), `diskon` DECIMAL(5,2)) RETURNS DECIMAL(12,2) DETERMINISTIC NO SQL BEGIN
    RETURN harga * diskon / 100;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `HitungPajak` (`harga` DECIMAL(12,2)) RETURNS DECIMAL(12,2) DETERMINISTIC NO SQL BEGIN
    RETURN harga * 0.11;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `HitungTotal` (`harga` DECIMAL(12,2), `jumlah` INT) RETURNS DECIMAL(12,2) DETERMINISTIC NO SQL BEGIN
    RETURN harga * jumlah;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `KategoriBelanja` (`total` DECIMAL(12,2)) RETURNS VARCHAR(30) CHARSET utf8mb4 COLLATE utf8mb4_general_ci DETERMINISTIC NO SQL BEGIN
    RETURN CASE
        WHEN total >= 1000000 THEN 'Pelanggan Gold'
        WHEN total >= 500000  THEN 'Pelanggan Silver'
        ELSE 'Pelanggan Reguler'
    END;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `KonversiNilai` (`nilai` DECIMAL(5,2)) RETURNS VARCHAR(2) CHARSET utf8mb4 COLLATE utf8mb4_general_ci DETERMINISTIC NO SQL BEGIN
    RETURN CASE
        WHEN nilai >= 90 THEN 'A'
        WHEN nilai >= 80 THEN 'B'
        WHEN nilai >= 70 THEN 'C'
        WHEN nilai >= 60 THEN 'D'
        ELSE 'E'
    END;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `log_pelanggan`
--

CREATE TABLE `log_pelanggan` (
  `id_log` int(11) NOT NULL,
  `aktivitas` varchar(50) DEFAULT NULL,
  `id_pelanggan` int(11) DEFAULT NULL,
  `waktu` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_pelanggan`
--

INSERT INTO `log_pelanggan` (`id_log`, `aktivitas`, `id_pelanggan`, `waktu`) VALUES
(1, 'INSERT PELANGGAN', 13, '2026-11-09 04:16:00'),
(2, 'INSERT PELANGGAN', 14, '2026-11-09 04:20:58'),
(3, 'INSERT PELANGGAN', 15, '2026-11-09 04:22:03');

-- --------------------------------------------------------

--
-- Table structure for table `log_perubahan_pelanggan`
--

CREATE TABLE `log_perubahan_pelanggan` (
  `id_log` int(11) NOT NULL,
  `id_pelanggan` int(11) DEFAULT NULL,
  `nama_lama` varchar(100) DEFAULT NULL,
  `nama_baru` varchar(100) DEFAULT NULL,
  `waktu` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_perubahan_pelanggan`
--

INSERT INTO `log_perubahan_pelanggan` (`id_log`, `id_pelanggan`, `nama_lama`, `nama_baru`, `waktu`) VALUES
(1, 13, 'Rina Test', 'Rina Wijaya', '2026-11-09 04:16:00'),
(2, 14, 'Rina Test', 'Rina Wijaya', '2026-11-09 04:21:03');

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id_pelanggan` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `alamat` varchar(150) DEFAULT NULL,
  `kota` varchar(50) DEFAULT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id_pelanggan`, `nama`, `alamat`, `kota`, `no_hp`, `email`) VALUES
(1, 'Ahmad Fauzi', 'Jl. Merdeka No. 1, Sumenep', 'Sumenep', '081234567801', 'ahmad@mail.com'),
(2, 'Siti Aisyah', 'Jl. Diponegoro No. 5, Sumenep', 'Sumenep', '081234567802', 'siti@mail.com'),
(3, 'Budi Santoso', 'Jl. Trunojoyo No. 10, Sumenep', 'Sumenep', '081234567803', 'budi@mail.com'),
(4, 'Dewi Lestari', 'Jl. Kamboja No. 3, Pamekasan', 'Pamekasan', '081234567804', 'dewi@mail.com'),
(5, 'Rudi Hartono', 'Jl. Pahlawan No. 7, Pamekasan', 'Pamekasan', '081234567805', 'rudi@mail.com'),
(6, 'Nur Hidayah', 'Jl. Sudirman No. 12, Sampang', 'Sampang', '081234567806', 'nur@mail.com'),
(7, 'Eko Prasetyo', 'Jl. Melati No. 8, Sampang', 'Sampang', '081234567807', 'eko@mail.com'),
(8, 'Fitri Handayani', 'Jl. Mawar No. 15, Bangkalan', 'Bangkalan', '081234567808', 'fitri@mail.com'),
(9, 'Hendra Wijaya', 'Jl. Anggrek No. 20, Bangkalan', 'Bangkalan', '081234567809', 'hendra@mail.com'),
(10, 'Maya Sari', 'Jl. Cempaka No. 25, Surabaya', 'Surabaya', '081234567810', 'maya@mail.com'),
(11, 'Rina Marlina', 'Jl. Kenanga No. 30, Sumenep', 'Sumenep', '081234567811', 'rina@mail.com'),
(12, 'Dewi', NULL, 'Ponorogo', NULL, NULL);

--
-- Triggers `pelanggan`
--
DELIMITER $$
CREATE TRIGGER `after_insert_pelanggan` AFTER INSERT ON `pelanggan` FOR EACH ROW BEGIN
    INSERT INTO log_pelanggan (aktivitas, id_pelanggan)
    VALUES ('INSERT PELANGGAN', NEW.id_pelanggan);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_update_pelanggan` AFTER UPDATE ON `pelanggan` FOR EACH ROW BEGIN
    INSERT INTO log_perubahan_pelanggan (id_pelanggan, nama_lama, nama_baru)
    VALUES (OLD.id_pelanggan, OLD.nama, NEW.nama);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `id_pelanggan` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `harga_satuan` decimal(12,2) NOT NULL,
  `total` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `id_pelanggan`, `tanggal`, `nama_barang`, `jumlah`, `harga_satuan`, `total`) VALUES
(1, 1, '2026-10-01', 'Beras 5kg', 2, 65000.00, 130000.00),
(2, 2, '2026-10-01', 'Minyak Goreng 2L', 3, 38000.00, 114000.00),
(3, 3, '2026-10-02', 'Gula Pasir 1kg', 5, 16000.00, 80000.00),
(4, 4, '2026-10-02', 'Telur 1kg', 2, 30000.00, 60000.00),
(5, 5, '2026-10-03', 'Kopi Bubuk', 4, 12000.00, 48000.00),
(6, 6, '2026-10-03', 'Teh Celup', 3, 10000.00, 30000.00),
(7, 7, '2026-10-04', 'Susu UHT 1L', 6, 18000.00, 108000.00),
(8, 8, '2026-10-04', 'Mie Instan Dus', 1, 95000.00, 95000.00),
(9, 9, '2026-10-05', 'Sabun Mandi', 10, 4500.00, 45000.00),
(10, 10, '2026-10-05', 'Sampo Sachet', 20, 1000.00, 20000.00),
(11, 1, '2026-10-06', 'Tepung Terigu 1kg', 3, 13000.00, 39000.00),
(12, 2, '2026-10-07', 'Garam 500g', 5, 3000.00, 15000.00),
(13, 3, '2026-10-07', 'Kecap Manis', 2, 15000.00, 30000.00),
(14, 4, '2026-10-08', 'Sarden Kaleng', 4, 14000.00, 56000.00),
(15, 5, '2026-10-08', 'Biskuit', 6, 9000.00, 54000.00),
(16, 1, '2026-10-09', 'Air Mineral Dus', 2, 45000.00, 90000.00),
(17, 6, '2026-10-10', 'Deterjen 1kg', 2, 25000.00, 50000.00),
(18, 7, '2026-10-10', 'Pasta Gigi', 3, 12000.00, 36000.00),
(19, 8, '2026-10-11', 'Roti Tawar', 2, 16000.00, 32000.00),
(20, 9, '2026-10-12', 'Beras 5kg', 1, 65000.00, 65000.00);

--
-- Triggers `transaksi`
--
DELIMITER $$
CREATE TRIGGER `before_insert_transaksi` BEFORE INSERT ON `transaksi` FOR EACH ROW BEGIN
    IF NEW.total < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Total belanja tidak boleh negatif';
    END IF;

    IF NEW.jumlah <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Jumlah barang harus lebih dari 0';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_laporan_transaksi`
-- (See below for the actual view)
--
CREATE TABLE `v_laporan_transaksi` (
`id_transaksi` int(11)
,`tanggal` date
,`nama_pelanggan` varchar(100)
,`nama_barang` varchar(100)
,`jumlah` int(11)
,`harga_satuan` decimal(12,2)
,`total` decimal(12,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_total_belanja_pelanggan`
-- (See below for the actual view)
--
CREATE TABLE `v_total_belanja_pelanggan` (
`id_pelanggan` int(11)
,`nama` varchar(100)
,`jumlah_transaksi` bigint(21)
,`total_belanja` decimal(34,2)
);

-- --------------------------------------------------------

--
-- Structure for view `v_laporan_transaksi`
--
DROP TABLE IF EXISTS `v_laporan_transaksi`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_laporan_transaksi`  AS SELECT `t`.`id_transaksi` AS `id_transaksi`, `t`.`tanggal` AS `tanggal`, `p`.`nama` AS `nama_pelanggan`, `t`.`nama_barang` AS `nama_barang`, `t`.`jumlah` AS `jumlah`, `t`.`harga_satuan` AS `harga_satuan`, `t`.`total` AS `total` FROM (`transaksi` `t` join `pelanggan` `p` on(`t`.`id_pelanggan` = `p`.`id_pelanggan`)) ;

-- --------------------------------------------------------

--
-- Structure for view `v_total_belanja_pelanggan`
--
DROP TABLE IF EXISTS `v_total_belanja_pelanggan`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_total_belanja_pelanggan`  AS SELECT `p`.`id_pelanggan` AS `id_pelanggan`, `p`.`nama` AS `nama`, count(`t`.`id_transaksi`) AS `jumlah_transaksi`, coalesce(sum(`t`.`total`),0) AS `total_belanja` FROM (`pelanggan` `p` left join `transaksi` `t` on(`p`.`id_pelanggan` = `t`.`id_pelanggan`)) GROUP BY `p`.`id_pelanggan`, `p`.`nama` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `log_pelanggan`
--
ALTER TABLE `log_pelanggan`
  ADD PRIMARY KEY (`id_log`);

--
-- Indexes for table `log_perubahan_pelanggan`
--
ALTER TABLE `log_perubahan_pelanggan`
  ADD PRIMARY KEY (`id_log`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `id_pelanggan` (`id_pelanggan`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `log_pelanggan`
--
ALTER TABLE `log_pelanggan`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `log_perubahan_pelanggan`
--
ALTER TABLE `log_perubahan_pelanggan`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id_pelanggan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
