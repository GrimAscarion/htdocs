-- ==========================================
-- DATABASE ANGKRINGAN V2 (STRUKTUR LENGKAP)
-- ==========================================

CREATE DATABASE IF NOT EXISTS db_angkringan;
USE db_angkringan;

-- Reset tabel untuk memastikan struktur benar (Hati-hati, data lama akan terhapus)
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS stok_harian;
DROP TABLE IF EXISTS menu;
DROP TABLE IF EXISTS kategori;
DROP TABLE IF EXISTS user;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. TABEL KATEGORI
CREATE TABLE kategori (
    id_kategori INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(50) NOT NULL
);

-- 2. TABEL MENU
CREATE TABLE menu (
    id_menu INT AUTO_INCREMENT PRIMARY KEY,
    id_kategori INT NOT NULL,
    nama_menu VARCHAR(100) NOT NULL,
    harga INT NOT NULL,
    ketersediaan ENUM('Tersedia', 'Habis') DEFAULT 'Tersedia',
    FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori) ON DELETE CASCADE
);

-- 3. TABEL STOK HARIAN
-- Sudah menyertakan kolom stok_awal setelah id_menu
CREATE TABLE stok_harian (
    id_stok INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL,
    id_menu INT NOT NULL,
    stok_awal INT NOT NULL DEFAULT 0,
    sisa_stok INT NOT NULL,
    FOREIGN KEY (id_menu) REFERENCES menu(id_menu) ON DELETE CASCADE
);

-- 4. TABEL USER (Untuk Fitur Login)
CREATE TABLE user (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL
);


-- ==========================================
-- INPUT DATA MASTER & AKSES LOGIN
-- ==========================================

-- Input Data Kategori
INSERT INTO kategori (nama_kategori) VALUES 
('Frozen Food'), ('Tusukan / Sate'), ('Baceman'), ('Nasi Kucing'), ('Minuman'), ('Ayam Potong');

-- Input Data Menu Lengkap
INSERT INTO menu (id_kategori, nama_menu, harga) VALUES
-- Frozen Food (ID Kategori: 1)
(1, 'Scallop', 3000),      -- ID Menu: 1
(1, 'Fishroll', 3000),     -- ID Menu: 2
(1, 'Bakso', 3000),        -- ID Menu: 3
(1, 'Otak-otak', 3000),    -- ID Menu: 4

-- Tusukan / Sate (ID Kategori: 2)
(2, 'Sate Telur Puyuh', 3000),     -- ID Menu: 5
(2, 'Sate Kulit Ayam', 3000),      -- ID Menu: 6
(2, 'Sate Ati Ayam', 3000),        -- ID Menu: 7
(2, 'Sate Usus Ayam', 3000),       -- ID Menu: 8
(2, 'Sate Jantung Ayam', 3000),    -- ID Menu: 9
(2, 'Sate Ampela Ayam', 3000),     -- ID Menu: 10
(2, 'Sate Kerongkongan Sapi', 3000),-- ID Menu: 11
(2, 'Sate Paru Sapi', 3000),        -- ID Menu: 12
(2, 'Sate Kikil Sapi', 3000),       -- ID Menu: 13
(2, 'Sate Paru Ayam', 3000),        -- ID Menu: 14
(2, 'Sate Tulang Muda Sapi', 3000), -- ID Menu: 15
(2, 'Sate Ampela Sapi', 3000),      -- ID Menu: 16

-- Baceman (ID Kategori: 3)
(3, 'Tempe Bacem', 3000),   -- ID Menu: 17
(3, 'Tahu Bacem', 3000),    -- ID Menu: 18

-- Ayam Potong (ID Kategori: 6)
(6, 'Sayap Ayam', 8000),    -- ID Menu: 19
(6, 'Kepala Ayam', 8000);   -- ID Menu: 20

-- Input Akun Admin Default untuk Login
INSERT INTO user (username, password) VALUES ('admin', 'admin123');


-- ==========================================
-- INPUT DATA TRANSAKSI STOK HARIAN
-- ==========================================

-- Data Stok: Rabu, 13 Mei 2026 (stok_awal disamakan dengan sisa_stok sebagai data awal)
INSERT INTO stok_harian (tanggal, id_menu, stok_awal, sisa_stok) VALUES
('2026-05-13', 8, 4, 4),    -- Usus
('2026-05-13', 7, 7, 7),    -- Ati Ayam
('2026-05-13', 14, 1, 1),   -- Paru Ayam
('2026-05-13', 10, 2, 2),   -- Ampela Ayam
('2026-05-13', 6, 14, 14),  -- Kulit Ayam
('2026-05-13', 5, 6, 6),    -- Telur Puyuh
('2026-05-13', 13, 4, 4),   -- Kikil Sapi
('2026-05-13', 12, 1, 1),   -- Paru Sapi
('2026-05-13', 11, 19, 19), -- Kerongkongan Sapi
('2026-05-13', 3, 1, 1),    -- Bakso
('2026-05-13', 2, 4, 4),    -- Fishroll
('2026-05-13', 4, 6, 6),    -- Otak-otak
('2026-05-13', 1, 8, 8),    -- Scallop
('2026-05-13', 17, 5, 5),   -- Tempe
('2026-05-13', 18, 10, 10), -- Tahu
('2026-05-13', 19, 3, 3),   -- Sayap
('2026-05-13', 20, 2, 2);   -- Kepala

-- Data Stok Terbaru: Jumat, 15 Mei 2026
INSERT INTO stok_harian (tanggal, id_menu, stok_awal, sisa_stok) VALUES
('2026-05-15', 8, 2, 2),     -- Usus
('2026-05-15', 6, 17, 17),   -- Kulit ayam
('2026-05-15', 5, 19, 19),   -- Telur puyuh
('2026-05-15', 13, 2, 2),    -- Kikil sapi
('2026-05-15', 11, 7, 7),    -- Kerongkongan sapi
('2026-05-15', 15, 5, 5),    -- Tulang muda sapi
('2026-05-15', 7, 3, 3),     -- Ati ayam
('2026-05-15', 14, 1, 1),    -- Paru ayam
('2026-05-15', 10, 2, 2),    -- Ampela ayam
('2026-05-15', 4, 10, 10),   -- Otak-otak
('2026-05-15', 1, 1, 1),     -- Scallop
('2026-05-15', 2, 2, 2),     -- Fishroll
('2026-05-15', 3, 3, 3),     -- Bakso
('2026-05-15', 18, 8, 8),    -- Tahu
('2026-05-15', 19, 2, 2),    -- Sayap
('2026-05-15', 20, 3, 3);    -- Kepala