# PT. Rinjani Inti Karya Solusi - Admin Panel

## Cara Instalasi

### 1. Requirements
- PHP >= 7.4
- MySQL / MariaDB
- Apache / XAMPP / LARAGON

### 2. Langkah Instalasi

1. **Copy folder** `rinjani_project` ke dalam folder `htdocs` (XAMPP) atau `www` (LARAGON)

2. **Buat Database**
   - Buka phpMyAdmin
   - Buat database baru bernama `rinjani_db`
   - Import file `database/rinjani_db.sql`

3. **Konfigurasi Database**
   Edit file `config/config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');       // sesuaikan username DB
   define('DB_PASS', '');           // sesuaikan password DB
   define('DB_NAME', 'rinjani_db');
   define('APP_URL', 'http://localhost/rinjani_project'); // sesuaikan URL
   ```

4. **Akses Aplikasi**
   Buka browser: `http://localhost/rinjani_project`

### 3. Login Default
- **Email:** admin@rinjani.com
- **Password:** password

---

## Fitur Aplikasi

| Halaman | Fitur |
|---------|-------|
| Dashboard | Statistik, grafik donut, proyek terbaru, jadwal hari ini |
| Proyek | CRUD, upload foto, filter status |
| Jadwal | CRUD, relasi ke proyek, filter tanggal |
| Pekerja | CRUD data pekerja |
| Subkontraktor | CRUD data mitra |
| Galeri | Upload & kelola foto dokumentasi |
| Pengguna | Manajemen akun user |
| Pengaturan | Edit profil & ganti password |

---

## Struktur Folder

```
rinjani_project/
├── admin/
│   ├── dashboard.php
│   ├── proyek.php
│   ├── jadwal.php
│   ├── pekerja.php
│   ├── subkontraktor.php
│   ├── galeri.php
│   ├── pengguna.php
│   └── pengaturan.php
├── auth/
│   ├── login.php
│   └── logout.php
├── config/
│   └── config.php
├── includes/
│   ├── header.php
│   ├── sidebar.php
│   └── footer.php
├── assets/
│   ├── css/style.css
│   └── js/script.js
├── uploads/
│   ├── proyek/
│   └── galeri/
├── database/
│   └── rinjani_db.sql
└── index.php
```

---

© 2026 PT. Rinjani Inti Karya Solusi
