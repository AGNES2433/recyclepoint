-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 18 Nov 2026 pada 12.18
-- Versi server: 10.11.16-MariaDB
-- Versi PHP: 8.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Basis data: `db_recyclepoint`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori_sampah`
--

CREATE TABLE `kategori_sampah` (
  `id` int(11) NOT NULL,
  `kode_kategori` varchar(20) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `harga_per_kg` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori_sampah`
--

INSERT INTO `kategori_sampah` (`id`, `kode_kategori`, `nama_kategori`, `harga_per_kg`, `created_at`) VALUES
(1, 'KAT-01', 'Plastik (Botol / Gelas)', 3000, '2026-11-18 06:12:57'),
(2, 'KAT-02', 'Kertas & Kardus', 2000, '2026-11-18 06:12:57'),
(3, 'KAT-03', 'Kaleng / Aluminium', 8000, '2026-11-18 06:12:57'),
(4, 'KAT-04', 'Botol Kaca', 1500, '2026-11-18 06:12:57'),
(6, 'KAT-05', 'kardus', 2000, '2026-11-18 06:25:24'),
(7, 'KAT-07', 'kardus', 1000, '2026-11-18 06:34:15'),
(8, 'KAT-08', 'aluminium', 600, '2026-11-18 06:34:36'),
(9, 'KAT-09', 'botol kaca', 1400, '2026-11-18 11:09:46'),
(10, 'KAT-10', 'botol plas', 2000, '2026-11-18 11:10:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `nasabah`
--

CREATE TABLE `nasabah` (
  `id` int(11) NOT NULL,
  `kode_nasabah` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `no_hp` varchar(25) NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `saldo` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `nasabah`
--

INSERT INTO `nasabah` (`id`, `kode_nasabah`, `nama`, `no_hp`, `alamat`, `saldo`, `created_at`) VALUES
(1, 'NSB-01', 'Budi Santoso', '0812-8890-1122', 'Jl. Kenanga RT 01/03', 20000, '2026-11-18 06:12:57'),
(2, 'NSB-02', 'Siti Rahma', '0856-4321-7788', 'Jl. Melati RT 04/02', 9000, '2026-11-18 06:12:57'),
(3, 'NSB-03', 'Andi Wijaya', '0813-9001-3344', 'Perum Asri Blok B2', 0, '2026-11-18 06:12:57'),
(4, 'NSB-04', 'Antonia Dea Bay', '081223455543', 'Jl. Siliwangi/ rt02-rw08', 10400, '2026-11-18 06:24:13'),
(5, 'NSB-05', 'Miamariette', '053477869123', 'Jl. Trans Ende-Bajawa/ rt005-rw001', 21500, '2026-11-18 06:33:37'),
(6, 'NSB-06', 'Etheldreda', '089877645432', 'Jl.jiwamurni/rt001-rw010', 42000, '2026-11-18 11:08:53');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi_setoran`
--

CREATE TABLE `transaksi_setoran` (
  `id` int(11) NOT NULL,
  `kode_transaksi` varchar(25) NOT NULL,
  `nasabah_id` int(11) NOT NULL,
  `kategori_id` int(11) NOT NULL,
  `berat_kg` decimal(8,2) NOT NULL,
  `harga_per_kg` int(11) NOT NULL,
  `total_nilai` int(11) NOT NULL,
  `catatan` varchar(255) DEFAULT '',
  `tanggal_setor` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaksi_setoran`
--

INSERT INTO `transaksi_setoran` (`id`, `kode_transaksi`, `nasabah_id`, `kategori_id`, `berat_kg`, `harga_per_kg`, `total_nilai`, `catatan`, `tanggal_setor`) VALUES
(1, 'TRX-1001', 1, 1, 2.00, 3000, 6000, 'Botol bersih tanpa label', '2026-11-18 13:12:57'),
(2, 'TRX-1002', 1, 3, 1.00, 8000, 8000, 'Kaleng minuman', '2026-11-18 13:12:57'),
(3, 'TRX-1003', 2, 2, 4.50, 2000, 9000, 'Kardus bekas paket', '2026-11-18 13:12:57'),
(4, 'TRX-1004', 4, 6, 5.20, 2000, 10400, 'sudah di lipat rapih dan di ikat', '2026-11-18 13:26:13'),
(5, 'TRX-1005', 5, 7, 10.40, 1000, 10400, 'sudah', '2026-11-18 13:35:00'),
(6, 'TRX-1006', 5, 8, 18.50, 600, 11100, 'dikemas rapih', '2026-11-18 13:35:33'),
(7, 'TRX-1007', 6, 9, 10.00, 1400, 14000, '', '2026-11-18 18:10:36'),
(8, 'TRX-1008', 6, 10, 14.00, 2000, 28000, '', '2026-11-18 18:10:56'),
(9, 'TRX-1009', 1, 1, 2.00, 3000, 6000, '', '2026-11-18 18:11:00');

--
-- Indeks untuk tabel yang dibuang
--

--
-- Indeks untuk tabel `kategori_sampah`
--
ALTER TABLE `kategori_sampah`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_kategori` (`kode_kategori`);

--
-- Indeks untuk tabel `nasabah`
--
ALTER TABLE `nasabah`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_nasabah` (`kode_nasabah`);

--
-- Indeks untuk tabel `transaksi_setoran`
--
ALTER TABLE `transaksi_setoran`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_transaksi` (`kode_transaksi`),
  ADD KEY `fk_setoran_nasabah` (`nasabah_id`),
  ADD KEY `fk_setoran_kategori` (`kategori_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `kategori_sampah`
--
ALTER TABLE `kategori_sampah`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `nasabah`
--
ALTER TABLE `nasabah`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `transaksi_setoran`
--
ALTER TABLE `transaksi_setoran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `transaksi_setoran`
--
ALTER TABLE `transaksi_setoran`
  ADD CONSTRAINT `fk_setoran_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategori_sampah` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_setoran_nasabah` FOREIGN KEY (`nasabah_id`) REFERENCES `nasabah` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
