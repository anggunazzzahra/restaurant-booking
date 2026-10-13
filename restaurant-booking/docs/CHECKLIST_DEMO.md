# Checklist Demonstrasi — Sertifikasi SKKNI Pemrograman

## Pemetaan 9 Unit Kompetensi SKKNI

Centang kolom Status setelah item didemonstrasikan. Setiap item menunjuk ke file yang dapat dibuka saat demo.

### 1. J.620100.009.01 — Menggunakan Spesifikasi Program

| No | Item Demonstrasi | Lokasi File | Status |
|----|-----------------|-------------|--------|
| 1 | Aturan bisnis dari spesifikasi: tanggal tidak boleh lampau, tamu ≤ kapasitas meja, minimal 1 menu | `app/Services/ValidationService.php` | ☐ |
| 2 | Aturan bentrok jadwal 2 jam pada meja dan tanggal yang sama | `app/Models/Reservation.php` (`hasConflict`) | ☐ |
| 3 | Rancangan basis data diwujudkan menjadi skema | `database/schema.sql`, `docs/SPESIFIKASI_DATABASE.md` | ☐ |
| 4 | Fitur pada spesifikasi diwujudkan menjadi rute | `routes/web.php` | ☐ |
| 5 | Spesifikasi, DFD, ERD, dan rancangan antarmuka | Laporan Pengembangan Program, BAB II | ☐ |

---

### 2. J.620100.016.01 — Menulis Kode sesuai Guidelines & Best Practices

| No | Item Demonstrasi | Lokasi File | Status |
|----|-----------------|-------------|--------|
| 1 | Pemisahan tanggung jawab: router, controller, service, model, view | `routes/`, `app/` | ☐ |
| 2 | Penamaan konsisten (class PascalCase, method camelCase, tabel snake_case) | `app/`, `database/schema.sql` | ☐ |
| 3 | Konstanta untuk nilai tetap (`VALID_STATUSES`, `STATUS_LABELS`) | `app/Models/Reservation.php` | ☐ |
| 4 | Prepared statement dan escaping output (`e()`) | `app/Models/Model.php`, `config/app.php` | ☐ |
| 5 | Konfigurasi di luar kode (`.env`) | `.env.example`, `config/database.php` | ☐ |
| 6 | Type hint dan return type pada method | `app/Services/*.php`, `app/Models/*.php` | ☐ |

---

### 3. J.620100.017.02 — Mengimplementasikan Pemrograman Terstruktur

| No | Item Demonstrasi | Lokasi File | Status |
|----|-----------------|-------------|--------|
| 1 | Percabangan `if / elseif / else` pada router | `routes/web.php` | ☐ |
| 2 | Pencocokan pola URL dengan `preg_match` | `routes/web.php` | ☐ |
| 3 | Perulangan `foreach` untuk menyimpan item menu dan menghitung total | `app/Services/ReservationService.php` | ☐ |
| 4 | Perulangan `foreach` pada tampilan data | `app/Views/*/index.php` | ☐ |
| 5 | Fungsi: `formatRupiah`, `e`, `redirect`, `setFlash`, `getFlash` | `config/app.php` | ☐ |
| 6 | Array asosiatif dan multidimensi (data form, item menu) | `app/Controllers/ReservationController.php` | ☐ |

---

### 4. J.620100.018.02 — Mengimplementasikan Pemrograman Berorientasi Objek

| No | Item Demonstrasi | Lokasi File | Status |
|----|-----------------|-------------|--------|
| 1 | Class, object, property, dan method | `app/Models/*.php`, `app/Controllers/*.php`, `app/Services/*.php` | ☐ |
| 2 | **Inheritance**: enam model `extends Model` | `app/Models/Customer.php` dan lainnya | ☐ |
| 3 | **Encapsulation**: property `protected` / `private` | `app/Models/Model.php`, `app/Services/ReservationService.php` | ☐ |
| 4 | **Abstraction**: `abstract class Model` | `app/Models/Model.php` | ☐ |
| 5 | Singleton: constructor `private`, `getInstance()` | `config/database.php` | ☐ |
| 6 | Dependency injection pada service | `app/Services/ReservationService.php` | ☐ |

Catatan: tidak ada method yang di-*override* pada kelas turunan, sehingga polimorfisme (*overriding*) tidak diklaim sebagai terpenuhi.

---

### 5. J.620100.019.02 — Menggunakan Library/Komponen Pre-existing

| No | Item Demonstrasi | Lokasi File | Status |
|----|-----------------|-------------|--------|
| 1 | PDO (ekstensi bawaan PHP) untuk akses basis data | `config/database.php` | ☐ |
| 2 | Bootstrap 5.3.2 dan Bootstrap Icons 1.11.3 (CDN) | `app/Views/layouts/main.php` | ☐ |
| 3 | PHPUnit ^10.5 melalui Composer | `composer.json`, `phpunit.xml` | ☐ |
| 4 | Apache `mod_rewrite` untuk URL bersih | `.htaccess`, `public/.htaccess` | ☐ |

---

### 6. J.620100.021.02 — Menerapkan Akses Basis Data

| No | Item Demonstrasi | Lokasi File | Status |
|----|-----------------|-------------|--------|
| 1 | Koneksi PDO singleton dengan mode exception | `config/database.php` | ☐ |
| 2 | CRUD generik dengan prepared statement | `app/Models/Model.php` | ☐ |
| 3 | `JOIN` beberapa tabel | `app/Models/Reservation.php` (`getFullDetail`, `getReport`) | ☐ |
| 4 | Agregasi `COUNT`, `SUM`, `GROUP BY` | `app/Models/Reservation.php` (`getReportSummary`, `getMenuSoldReport`) | ☐ |
| 5 | Transaksi `beginTransaction`, `commit`, `rollBack` | `app/Services/ReservationService.php` | ☐ |
| 6 | Foreign key `RESTRICT` / `CASCADE` dan pesan saat gagal hapus | `database/schema.sql`, `app/Controllers/CustomerController.php` | ☐ |
| 7 | DDL dan data contoh | `database/schema.sql`, `database/seeder.sql` | ☐ |

---

### 7. J.620100.023.02 — Membuat Dokumentasi Kode Program

| No | Item Demonstrasi | Lokasi File | Status |
|----|-----------------|-------------|--------|
| 1 | PHPDoc pada class dan method (`@param`, `@return`) | `app/`, `config/` | ☐ |
| 2 | Komentar langkah algoritma | `app/Services/ReservationService.php` | ☐ |
| 3 | README proyek | `README.md` | ☐ |
| 4 | Panduan instalasi | `docs/PANDUAN_INSTALASI.md` | ☐ |
| 5 | Spesifikasi database | `docs/SPESIFIKASI_DATABASE.md` | ☐ |

---

### 8. J.620100.025.02 — Melakukan Debugging

| No | Item Demonstrasi | Lokasi File | Status |
|----|-----------------|-------------|--------|
| 1 | Mode debug lewat `APP_DEBUG` dan log ke `logs/error.log` | `config/app.php`, `.env` | ☐ |
| 2 | `try / catch` untuk `PDOException` dan `Exception` | `app/Controllers/*.php`, `app/Services/ReservationService.php` | ☐ |
| 3 | Rollback transaksi saat gagal | `app/Services/ReservationService.php` | ☐ |
| 4 | Pesan kesalahan validasi dan *flash message* | `app/Services/ValidationService.php`, `config/app.php` | ☐ |
| 5 | Catatan bug yang ditemukan dan diperbaiki | Laporan Pengembangan Program, BAB IV (4.3) | ☐ |

---

### 9. J.620100.033.02 — Melaksanakan Pengujian Unit Program

| No | Item Demonstrasi | Lokasi File | Status |
|----|-----------------|-------------|--------|
| 1 | Konfigurasi PHPUnit | `phpunit.xml`, `tests/bootstrap.php` | ☐ |
| 2 | 18 test pada `ValidationService` (valid, tidak valid, batas nilai, kalkulasi) | `tests/Unit/ValidationServiceTest.php` | ☐ |
| 3 | 6 test pada konstanta model dan fungsi helper | `tests/Unit/ModelTest.php` | ☐ |
| 4 | `setUp()` dan assertion (`assertTrue`, `assertFalse`, `assertEquals`, `assertArrayHasKey`) | `tests/Unit/*.php` | ☐ |
| 5 | Menjalankan `vendor/bin/phpunit` dan menyimpan hasilnya | Terminal; lihat `docs/PANDUAN_INSTALASI.md` | ☐ |

---

## Prosedur Demonstrasi

### Persiapan (5 menit)
1. ☐ Buka XAMPP, start Apache & MySQL
2. ☐ Buka browser, akses `http://localhost/restaurant-booking/public/`
3. ☐ Pastikan dashboard tampil dengan benar
4. ☐ Buka source code di editor (VS Code / Notepad++)

### Demo 1: Dashboard & Navigasi (3 menit)
1. ☐ Tunjukkan halaman Dashboard dengan statistik
2. ☐ Navigasi melalui sidebar (responsif)
3. ☐ Jelaskan layout MVC (layouts/main.php)

### Demo 2: CRUD Pelanggan (5 menit)
1. ☐ Lihat daftar pelanggan
2. ☐ Tambah pelanggan baru (tunjukkan validasi)
3. ☐ Edit pelanggan
4. ☐ Hapus pelanggan (tunjukkan konfirmasi)
5. ☐ Tunjukkan CSRF token di source form

### Demo 3: CRUD Meja (3 menit)
1. ☐ Lihat daftar meja dengan status
2. ☐ Tambah meja baru
3. ☐ Tunjukkan validasi duplikat nomor meja

### Demo 4: CRUD Menu & Kategori (5 menit)
1. ☐ Lihat daftar kategori, tambah kategori baru
2. ☐ Lihat daftar menu, tambah menu baru
3. ☐ Tunjukkan relasi menu-kategori (JOIN)
4. ☐ Edit dan hapus menu

### Demo 5: Pemesanan (10 menit) — **Paling Penting**
1. ☐ Buat pemesanan baru (pilih pelanggan, meja, menu)
2. ☐ Tambah beberapa item menu secara dinamis (JavaScript)
3. ☐ Submit dan tunjukkan transaksi database berhasil
4. ☐ Lihat detail pemesanan (show)
5. ☐ Ubah status pemesanan (menunggu → dikonfirmasi → selesai)
6. ☐ Edit pemesanan yang sudah ada
7. ☐ Jelaskan deteksi konflik jadwal di source code

### Demo 6: Laporan (3 menit)
1. ☐ Lihat laporan dengan filter tanggal
2. ☐ Lihat ringkasan per status
3. ☐ Lihat menu terlaris
4. ☐ Cetak laporan (halaman print-friendly)

### Demo 7: Source Code Review (10 menit)
1. ☐ Tunjukkan struktur folder MVC
2. ☐ Jelaskan abstract class Model (OOP)
3. ☐ Jelaskan Service layer (ReservationService)
4. ☐ Tunjukkan prepared statements (PDO)
5. ☐ Tunjukkan validasi dan error handling
6. ☐ Tunjukkan routing (pattern matching)

### Demo 8: Unit Testing (5 menit)
1. ☐ Jalankan `composer test` di terminal
2. ☐ Tunjukkan hasil test (passed/failed)
3. ☐ Jelaskan test case yang dibuat
4. ☐ Tunjukkan kode test di editor

### Penutup (2 menit)
1. ☐ Tunjukkan dokumentasi (README, panduan instalasi)
2. ☐ Rangkum 9 unit kompetensi yang diterapkan
