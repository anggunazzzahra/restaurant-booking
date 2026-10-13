# Panduan Instalasi — Sistem Pemesanan Restoran

## Persyaratan Sistem

| Komponen | Versi Minimum | Keterangan |
|----------|--------------|------------|
| XAMPP    | 8.0+         | Berisi Apache, MySQL/MariaDB, PHP |
| PHP      | 8.0          | Dengan ekstensi PDO, pdo_mysql |
| MySQL    | 5.7+ / MariaDB 10.4+ | InnoDB engine |
| Browser  | Chrome/Firefox/Edge terbaru | Untuk mengakses aplikasi |
| Composer | 2.x          | Untuk instalasi PHPUnit (opsional) |

---

## Langkah 1: Instalasi XAMPP

1. **Download XAMPP**
   - Kunjungi: https://www.apachefriends.org/download.html
   - Pilih versi **XAMPP for Windows** dengan PHP 8.0 atau lebih baru
   - Download installer (.exe)

2. **Instal XAMPP**
   - Jalankan installer yang sudah didownload
   - Pilih komponen: **Apache**, **MySQL**, **PHP**, **phpMyAdmin**
   - Pilih lokasi instalasi default: `C:\xampp`
   - Klik **Next** hingga selesai

3. **Jalankan XAMPP**
   - Buka XAMPP Control Panel
   - Klik **Start** pada modul **Apache**
   - Klik **Start** pada modul **MySQL**
   - Pastikan kedua modul berstatus hijau (Running)

4. **Verifikasi Instalasi**
   - Buka browser, akses: `http://localhost`
   - Halaman dashboard XAMPP harus muncul

---

## Langkah 2: Konfigurasi Apache (mod_rewrite)

1. Buka file `C:\xampp\apache\conf\httpd.conf`
2. Cari baris: `#LoadModule rewrite_module modules/mod_rewrite.so`
3. Hapus tanda `#` di depannya menjadi:
   ```
   LoadModule rewrite_module modules/mod_rewrite.so
   ```
4. Cari bagian `<Directory "C:/xampp/htdocs">` dan pastikan `AllowOverride` diset ke `All`:
   ```apache
   <Directory "C:/xampp/htdocs">
       AllowOverride All
       Require all granted
   </Directory>
   ```
5. Restart Apache dari XAMPP Control Panel

---

## Langkah 3: Menyalin Proyek

1. **Salin folder proyek**
   - Salin seluruh folder `restaurant-booking` ke `C:\xampp\htdocs\`
   - Struktur akhir: `C:\xampp\htdocs\restaurant-booking\`

2. **Konfigurasi environment**
   - Salin file `.env.example` menjadi `.env`:
     ```
     copy .env.example .env
     ```
   - Edit file `.env` sesuai kebutuhan (biasanya tidak perlu diubah untuk default XAMPP):
     ```
     DB_HOST=localhost
     DB_PORT=3306
     DB_DATABASE=restaurant_booking
     DB_USERNAME=root
     DB_PASSWORD=
     ```

---

## Langkah 4: Membuat Database

### Opsi A: Via phpMyAdmin (GUI)

1. Buka browser: `http://localhost/phpmyadmin`
2. Klik tab **Databases** (Basis Data)
3. Pada kolom "Create database", ketik: `restaurant_booking`
4. Pilih Collation: `utf8mb4_unicode_ci`
5. Klik **Create**
6. Pilih database `restaurant_booking` di sidebar kiri
7. Klik tab **Import**
8. Klik **Browse/Pilih File**, pilih: `database/schema.sql`
9. Klik **Go/Kirim** untuk mengeksekusi
10. Ulangi langkah 7-9 untuk file `database/seeder.sql`

### Opsi B: Via Command Line

```bash
# Buka terminal/CMD, masuk ke direktori MySQL
cd C:\xampp\mysql\bin

# Login ke MySQL
mysql -u root

# Jalankan perintah SQL:
CREATE DATABASE restaurant_booking CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE restaurant_booking;

# Import schema
SOURCE C:/xampp/htdocs/restaurant-booking/database/schema.sql;

# Import data contoh (seeder)
SOURCE C:/xampp/htdocs/restaurant-booking/database/seeder.sql;

# Keluar
EXIT;
```

---

## Langkah 5: Mengakses Aplikasi

1. Pastikan Apache dan MySQL sudah berjalan di XAMPP Control Panel
2. Buka browser
3. Akses: **http://localhost/restaurant-booking/public/**
4. Halaman Dashboard akan tampil

---

## Langkah 6: Instalasi PHPUnit (Opsional — untuk Testing)

1. **Instal Composer** (jika belum ada)
   - Download: https://getcomposer.org/download/
   - Jalankan installer

2. **Instal dependensi PHPUnit**
   ```bash
   cd C:\xampp\htdocs\restaurant-booking
   composer install
   ```

3. **Jalankan unit test**
   ```bash
   # Dari direktori proyek
   vendor\bin\phpunit

   # Atau melalui Composer script
   composer test
   ```

---

## Troubleshooting

### Apache tidak bisa Start
- **Port 80 sudah dipakai**: Buka XAMPP → Config Apache → `httpd.conf`, ubah `Listen 80` menjadi `Listen 8080`. Akses dengan `http://localhost:8080/...`
- **Skype/IIS menggunakan port 80**: Matikan Skype atau IIS, lalu restart Apache

### Halaman 404 / Not Found
- Pastikan mod_rewrite sudah aktif (Langkah 2)
- Pastikan file `.htaccess` ada di folder `restaurant-booking/` dan `restaurant-booking/public/`
- Pastikan `AllowOverride All` sudah dikonfigurasi

### Error "Class not found" atau "require_once failed"
- Pastikan file `.env` sudah dibuat dari `.env.example`
- Pastikan path di `.env` sesuai konfigurasi XAMPP

### Error Koneksi Database
- Pastikan MySQL sudah berjalan di XAMPP
- Pastikan database `restaurant_booking` sudah dibuat
- Cek username/password di file `.env`

### Error "CSRF Token Invalid"
- Hapus cookies browser untuk localhost
- Pastikan session sudah aktif (session_start)

### Dropdown "Pilih Menu" kosong / tidak bisa menambah item pemesanan
- Pastikan memakai versi proyek terbaru (seluruh folder `restaurant-booking` ditimpa, bukan sebagian file)
- Tekan **Ctrl + F5** di browser agar file JavaScript lama tidak dipakai lagi
- Pastikan di menu **Menu Makanan** ada menu berstatus *Tersedia* dan sudah punya kategori

### Tampilan berantakan / muncul tulisan "Warning" atau "Fatal error" di halaman
- Biasanya tanda proyek yang dipakai masih versi lama. Hapus folder `C:\xampp\htdocs\restaurant-booking`, lalu salin ulang dari zip terbaru
- Database tidak perlu di-import ulang bila sudah pernah dibuat (data lama tetap aman di MySQL)

---

## Catatan Keamanan

Aplikasi ini mengimplementasikan beberapa praktik keamanan:

1. **CSRF Protection** — Token CSRF pada setiap form POST
2. **Prepared Statements** — Mencegah SQL Injection via PDO
3. **XSS Prevention** — Output di-escape dengan `htmlspecialchars()`
4. **Error Handling** — Mode debug diatur via `.env` (`APP_DEBUG`)
5. **Session Security** — Session diregenerasi setelah operasi penting
