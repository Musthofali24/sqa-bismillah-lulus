-- Buat database
CREATE DATABASE IF NOT EXISTS db_penjualan;
USE db_penjualan;

-- Tabel Kategori
CREATE TABLE kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL UNIQUE
);

-- Tabel Produk
CREATE TABLE produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(200) NOT NULL,
    harga DECIMAL(10,2) NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    kategori_id INT NOT NULL,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE RESTRICT
);

-- Tabel Pelanggan
CREATE TABLE pelanggan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(200) NOT NULL,
    telepon VARCHAR(20),
    alamat TEXT
);

-- Tabel Transaksi Header
CREATE TABLE transaksi_header (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pelanggan_id INT NOT NULL,
    tanggal DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2) NOT NULL,
    metode_pembayaran ENUM('cash','transfer','qris') NOT NULL,
    status_pembayaran ENUM('lunas','belum_lunas') DEFAULT 'lunas',
    FOREIGN KEY (pelanggan_id) REFERENCES pelanggan(id) ON DELETE RESTRICT
);

-- Tabel Transaksi Detail
CREATE TABLE transaksi_detail (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaksi_id INT NOT NULL,
    produk_id INT NOT NULL,
    qty INT NOT NULL,
    harga_satuan DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (transaksi_id) REFERENCES transaksi_header(id) ON DELETE CASCADE,
    FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE RESTRICT
);

-- Data awal untuk testing (opsional)
INSERT INTO kategori (nama) VALUES ('Alat Tulis'), ('Elektronik');
INSERT INTO produk (nama, harga, stok, kategori_id) VALUES 
('Pensil 2B', 2500, 100, 1),
('Buku Tulis', 5000, 50, 1),
('Mouse', 75000, 20, 2);
INSERT INTO pelanggan (nama, telepon, alamat) VALUES 
('Budi Santoso', '08123456789', 'Jl. Merdeka No. 1'),
('Siti Aminah', '08129876543', 'Jl. Sudirman No. 10');