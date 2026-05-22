CREATE DATABASE IF NOT EXISTS migas_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE migas_db;

DROP TABLE IF EXISTS proyek_sumur;
CREATE TABLE proyek_sumur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_proyek VARCHAR(150) NOT NULL,
    nama_sumur VARCHAR(150) NOT NULL,
    lokasi_lapangan VARCHAR(150) NOT NULL,
    status_proyek ENUM('Direncanakan','Berjalan','Selesai') DEFAULT 'Direncanakan',
    jenis_produksi ENUM('Minyak','Gas','Minyak dan Gas') DEFAULT 'Minyak',
    cadangan_mbbl DECIMAL(14,2) DEFAULT 0,
    tahun_awal INT NOT NULL,
    jangka_waktu INT NOT NULL,
    harga_minyak_usd DECIMAL(14,2) DEFAULT 0,
    persentase_pajak DECIMAL(6,2) DEFAULT 0,
    metode_depresiasi VARCHAR(100) DEFAULT 'Garis Lurus / Straight Line',
    decline_produksi DECIMAL(6,2) DEFAULT 0,
    keterangan TEXT,
    produksi_tahun1 DECIMAL(14,2) DEFAULT 0,
    produksi_tahun2 DECIMAL(14,2) DEFAULT 0,
    produksi_tahun3 DECIMAL(14,2) DEFAULT 0,
    produksi_tahun4 DECIMAL(14,2) DEFAULT 0,
    opex_tahun DECIMAL(14,2) DEFAULT 0,
    kenaikan_opex DECIMAL(6,2) DEFAULT 0,
    capital DECIMAL(14,2) DEFAULT 0,
    non_capital DECIMAL(14,2) DEFAULT 0,
    nilai_depresiasi DECIMAL(14,2) DEFAULT 0,
    pajak_penghasilan DECIMAL(6,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO proyek_sumur (
    nama_proyek, nama_sumur, lokasi_lapangan, status_proyek, jenis_produksi,
    cadangan_mbbl, tahun_awal, jangka_waktu, harga_minyak_usd, persentase_pajak,
    metode_depresiasi, decline_produksi, keterangan,
    produksi_tahun1, produksi_tahun2, produksi_tahun3, produksi_tahun4,
    opex_tahun, kenaikan_opex, capital, non_capital, nilai_depresiasi, pajak_penghasilan
) VALUES (
    'Gunung Bakaran', 'GB-01', 'Lapangan Gunung Bakaran', 'Berjalan', 'Minyak',
    4320, 2026, 10, 32, 51,
    'Garis Lurus / Straight Line', 3, 'Contoh proyek berdasarkan soal perhitungan field management migas.',
    175, 201, 217, 198,
    180, 2.5, 13000, 8000, 0, 51
);
