-- ============================================================
-- Database: rinjani_db
-- PT. Rinjani Inti Karya Solusi
-- ============================================================

CREATE DATABASE IF NOT EXISTS rinjani_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE rinjani_db;

-- Tabel Pengguna
CREATE TABLE IF NOT EXISTS pengguna (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','user') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Proyek
CREATE TABLE IF NOT EXISTS proyek (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_proyek VARCHAR(200) NOT NULL,
    deskripsi TEXT,
    lokasi VARCHAR(200),
    tanggal_mulai DATE,
    tanggal_selesai DATE,
    status ENUM('Berjalan','Selesai','Tertunda') DEFAULT 'Berjalan',
    foto VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Pekerja
CREATE TABLE IF NOT EXISTS pekerja (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    jabatan VARCHAR(100),
    telepon VARCHAR(20),
    email VARCHAR(100),
    alamat TEXT,
    foto VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Subkontraktor
CREATE TABLE IF NOT EXISTS subkontraktor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_perusahaan VARCHAR(200) NOT NULL,
    kontak_person VARCHAR(100),
    telepon VARCHAR(20),
    email VARCHAR(100),
    alamat TEXT,
    bidang VARCHAR(200),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Jadwal
CREATE TABLE IF NOT EXISTS jadwal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kegiatan VARCHAR(200) NOT NULL,
    tanggal DATE NOT NULL,
    waktu TIME,
    lokasi VARCHAR(200),
    penanggung_jawab VARCHAR(100),
    proyek_id INT,
    status ENUM('Aktif','Selesai','Dibatalkan') DEFAULT 'Aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (proyek_id) REFERENCES proyek(id) ON DELETE SET NULL
);

-- Tabel Galeri
CREATE TABLE IF NOT EXISTS galeri (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(200),
    foto VARCHAR(255) NOT NULL,
    proyek_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (proyek_id) REFERENCES proyek(id) ON DELETE SET NULL
);

-- ============================================================
-- Data Sample
-- ============================================================

-- Admin default: password = admin123
INSERT INTO pengguna (nama, email, password, role) VALUES
('Admin', 'admin@rinjani.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Proyek sample
INSERT INTO proyek (nama_proyek, deskripsi, lokasi, tanggal_mulai, tanggal_selesai, status) VALUES
('Pembangunan Gudang PT. ABC', 'Pembangunan gudang industri kapasitas 500 ton', 'Jakarta Utara', '2026-04-01', '2026-07-01', 'Berjalan'),
('Pengelasan Pipa Industri', 'Pengelasan pipa baja diameter 12 inch untuk pabrik', 'Bekasi', '2026-04-15', '2026-06-15', 'Berjalan'),
('Pengeboran Sumur Dalam', 'Pengeboran sumur artesis kedalaman 150 meter', 'Bogor', '2026-03-01', '2026-05-01', 'Selesai'),
('Kanopi Baja Ringan', 'Pemasangan kanopi baja ringan area parkir', 'Depok', '2026-04-20', '2026-05-20', 'Berjalan'),
('Renovasi Workshop', 'Renovasi total workshop produksi', 'Tangerang', '2026-03-15', '2026-04-30', 'Selesai'),
('Konstruksi Jembatan Mini', 'Pembangunan jembatan penghubung antar gedung', 'Jakarta Selatan', '2026-05-01', '2026-08-01', 'Berjalan'),
('Pemasangan Atap Metal', 'Pemasangan atap metal deck area pabrik', 'Cikarang', '2026-02-01', '2026-03-31', 'Selesai'),
('Fabrikasi Tangki Air', 'Fabrikasi dan instalasi tangki air 50.000 liter', 'Cibitung', '2026-05-10', '2026-07-10', 'Berjalan'),
('Instalasi Struktur Baja', 'Instalasi struktur baja untuk bangunan 3 lantai', 'Serpong', '2026-01-15', '2026-04-15', 'Selesai'),
('Pagar Besi Industri', 'Pembuatan dan pemasangan pagar besi kawasan industri', 'Karawang', '2026-04-01', '2026-05-15', 'Tertunda'),
('Pemasangan Crane Overhead', 'Instalasi crane overhead kapasitas 5 ton', 'Purwakarta', '2026-05-20', '2026-07-20', 'Berjalan'),
('Konstruksi Mezzanine', 'Pembangunan lantai mezzanine gudang', 'Subang', '2026-03-01', '2026-05-30', 'Berjalan');

-- Pekerja sample
INSERT INTO pekerja (nama, jabatan, telepon, email) VALUES
('Budi Santoso', 'Mandor Lapangan', '081234567890', 'budi@rinjani.com'),
('Andi Wijaya', 'Teknisi Pengeboran', '081234567891', 'andi@rinjani.com'),
('Rudi Hartono', 'Welder Senior', '081234567892', 'rudi@rinjani.com'),
('Sari Dewi', 'Admin Proyek', '081234567893', 'sari@rinjani.com'),
('Hendra Kusuma', 'Supervisor Konstruksi', '081234567894', 'hendra@rinjani.com'),
('Agus Salim', 'Operator Alat Berat', '081234567895', 'agus@rinjani.com'),
('Dian Pratiwi', 'Quality Control', '081234567896', 'dian@rinjani.com'),
('Eko Nugroho', 'Teknisi Listrik', '081234567897', 'eko@rinjani.com'),
('Fitri Handayani', 'Drafter CAD', '081234567898', 'fitri@rinjani.com'),
('Gunawan Setiawan', 'Kepala Teknik', '081234567899', 'gunawan@rinjani.com'),
('Hermawan', 'Pekerja Fabrikasi', '081234567800', 'hermawan@rinjani.com'),
('Indra Lesmana', 'Koordinator Lapangan', '081234567801', 'indra@rinjani.com'),
('Joko Widodo', 'Safety Officer', '081234567802', 'joko@rinjani.com'),
('Kartika Sari', 'Estimator', '081234567803', 'kartika@rinjani.com'),
('Lukman Hakim', 'Teknisi Pengelasan', '081234567804', 'lukman@rinjani.com'),
('Mira Susanti', 'Sekretaris', '081234567805', 'mira@rinjani.com'),
('Nanang Sudrajat', 'Operator Crane', '081234567806', 'nanang@rinjani.com'),
('Oktavia Putri', 'Purchasing', '081234567807', 'oktavia@rinjani.com'),
('Purnomo', 'Tukang Las', '081234567808', 'purnomo@rinjani.com'),
('Qori Anisa', 'Administrasi', '081234567809', 'qori@rinjani.com'),
('Rahmat Hidayat', 'Mandor Besi', '081234567810', 'rahmat@rinjani.com'),
('Slamet Riyadi', 'Pekerja Konstruksi', '081234567811', 'slamet@rinjani.com'),
('Tono Sutrisno', 'Teknisi Mekanik', '081234567812', 'tono@rinjani.com'),
('Umar Bakri', 'Pekerja Umum', '081234567813', 'umar@rinjani.com'),
('Vina Astuti', 'Akuntansi', '081234567814', 'vina@rinjani.com'),
('Wahyu Santoso', 'Project Manager', '081234567815', 'wahyu@rinjani.com'),
('Yudi Permana', 'Site Engineer', '081234567816', 'yudi@rinjani.com');

-- Subkontraktor sample
INSERT INTO subkontraktor (nama_perusahaan, kontak_person, telepon, email, bidang) VALUES
('CV. Maju Jaya', 'Bambang', '02112345678', 'cvmajujaya@gmail.com', 'Pengelasan & Fabrikasi'),
('PT. Karya Bersama', 'Sutanto', '02112345679', 'karyabersama@gmail.com', 'Konstruksi Baja'),
('UD. Teknik Mandiri', 'Priyanto', '02112345680', 'tenikmandiri@gmail.com', 'Pengeboran'),
('CV. Bangunan Kuat', 'Suharto', '02112345681', 'bangunkuat@gmail.com', 'Sipil & Arsitektur'),
('PT. Logam Utama', 'Wibowo', '02112345682', 'logamutama@gmail.com', 'Supplier Material Baja'),
('CV. Elektro Prima', 'Santoso', '02112345683', 'elektroprima@gmail.com', 'Instalasi Listrik'),
('UD. Las Jaya', 'Hartono', '02112345684', 'lasjaya@gmail.com', 'Pengelasan Spesialis'),
('PT. Alat Berat Nusantara', 'Kusuma', '02112345685', 'alatberat@gmail.com', 'Rental Alat Berat');

-- Jadwal sample
INSERT INTO jadwal (kegiatan, tanggal, waktu, lokasi, penanggung_jawab, proyek_id, status) VALUES
('Pengelasan Struktur Baja', '2026-06-14', '08:00:00', 'PT. ABC', 'Budi Santoso', 1, 'Aktif'),
('Pengeboran Sumur', '2026-06-14', '10:00:00', 'Villa Citra', 'Andi Wijaya', 3, 'Aktif'),
('Pemasangan Kanopi', '2026-06-14', '13:00:00', 'Gudang Sentral', 'Rudi Hartono', 4, 'Aktif'),
('Inspeksi Material', '2026-06-15', '09:00:00', 'Workshop Rinjani', 'Hendra Kusuma', 1, 'Aktif'),
('Rapat Koordinasi Proyek', '2026-06-15', '14:00:00', 'Kantor Pusat', 'Wahyu Santoso', NULL, 'Aktif'),
('Pengiriman Material Baja', '2026-06-16', '07:00:00', 'Cikarang', 'Agus Salim', 2, 'Aktif'),
('Quality Control Pengelasan', '2026-06-16', '10:00:00', 'Bekasi', 'Dian Pratiwi', 2, 'Aktif'),
('Pemasangan Atap', '2026-06-17', '08:00:00', 'Serpong', 'Eko Nugroho', 6, 'Aktif'),
('Survey Lokasi Baru', '2026-06-17', '11:00:00', 'Karawang', 'Gunawan Setiawan', NULL, 'Aktif'),
('Penyelesaian Dokumentasi', '2026-06-18', '09:00:00', 'Kantor Pusat', 'Fitri Handayani', NULL, 'Aktif'),
('Pengelasan Pipa Lanjutan', '2026-06-13', '08:00:00', 'Bekasi', 'Rudi Hartono', 2, 'Selesai'),
('Cek Progress Gudang', '2026-06-12', '10:00:00', 'Jakarta Utara', 'Budi Santoso', 1, 'Selesai'),
('Meeting Client PT. ABC', '2026-06-10', '14:00:00', 'Kantor PT. ABC', 'Wahyu Santoso', 1, 'Selesai'),
('Fabrikasi Tangki', '2026-06-18', '08:00:00', 'Cibitung', 'Hermawan', 8, 'Aktif'),
('Instalasi Crane', '2026-06-19', '07:00:00', 'Purwakarta', 'Nanang Sudrajat', 11, 'Aktif'),
('Briefing Safety', '2026-06-19', '08:00:00', 'Kantor Pusat', 'Joko Widodo', NULL, 'Aktif'),
('Penyelesaian Mezzanine', '2026-06-20', '08:00:00', 'Subang', 'Indra Lesmana', 12, 'Aktif'),
('Pengecatan Struktur', '2026-06-20', '10:00:00', 'Jakarta Selatan', 'Lukman Hakim', 6, 'Aktif');
