# Sistem Pemesanan Restoran

Aplikasi web untuk mengelola pemesanan restoran, dikembangkan sebagai proyek praktik untuk **Sertifikasi SKKNI Pemrograman** di Universitas Lampung.

## Fitur Utama

- **Dashboard** — Ringkasan statistik dan pemesanan terbaru
- **Manajemen Pelanggan** — CRUD data pelanggan dengan pencarian
- **Manajemen Meja** — CRUD meja restoran dengan status (tersedia/dipesan/digunakan)
- **Manajemen Menu** — CRUD menu makanan/minuman beserta kategori
- **Pemesanan** — Buat, edit, lihat detail, dan ubah status pemesanan dengan multiple item menu
- **Laporan** — Laporan pemesanan berdasarkan rentang tanggal, menu terlaris, versi cetak

## Teknologi

| Komponen | Teknologi |
|----------|-----------|
| Backend | PHP 8.0+ (Native, tanpa framework) |
| Database | MySQL / MariaDB |
| Frontend | Bootstrap 5, Bootstrap Icons |
| Server | Apache (XAMPP) |
| Arsitektur | MVC (Model-View-Controller) |
| Testing | PHPUnit 10 |

## Arsitektur MVC

```
restaurant-booking/
├── app/
│   ├── Controllers/     # Controller: menerima request, koordinasi
│   ├── Models/          # Model: akses database, business logic
│   ├── Services/        # Service: logika bisnis kompleks
│   └── Views/           # View: tampilan HTML
│       └── layouts/     # Layout utama (template)
├── config/              # Konfigurasi database & aplikasi
├── database/            # Schema SQL & seeder
├── docs/                # Dokumentasi
├── public/              # Entry point, assets (CSS/JS)
├── routes/              # Router URL
└── tests/               # PHPUnit tests
```

## Prinsip Pemrograman yang Diterapkan

### OOP (Object-Oriented Programming)
- **Inheritance**: Semua model mewarisi abstract class `Model`
- **Encapsulation**: Properties `protected`/`private`
- **Polymorphism**: Override method di child class
- **Abstraction**: Abstract base class

### Design Patterns
- **Singleton**: Koneksi database PDO
- **MVC**: Pemisahan Model, View, Controller
- **Service Layer**: Logika bisnis di `ReservationService`
- **Active Record**: Base model dengan CRUD generik

### Keamanan
- **CSRF Protection**: Token pada setiap form POST
- **SQL Injection Prevention**: PDO Prepared Statements
- **XSS Prevention**: Output escaping dengan `htmlspecialchars()`
- **Database Transactions**: Untuk operasi multi-tabel (pemesanan)

## Instalasi

Lihat panduan lengkap: [docs/PANDUAN_INSTALASI.md](docs/PANDUAN_INSTALASI.md)

### Singkat:
1. Instal XAMPP (PHP 8.0+)
2. Salin folder ke `C:\xampp\htdocs\restaurant-booking\`
3. Salin `.env.example` → `.env`
4. Buat database `restaurant_booking` di phpMyAdmin
5. Import `database/schema.sql` lalu `database/seeder.sql`
6. Akses: `http://localhost/restaurant-booking/public/`

## Unit Testing

```bash
# Instal PHPUnit via Composer
composer install

# Jalankan test
composer test
```

## Pemetaan SKKNI

Proyek ini menyediakan bukti implementasi untuk 9 unit kompetensi SKKNI (status pemenuhan ditentukan asesor berdasarkan bukti):

| No | Kode Unit | Judul | Implementasi |
|----|-----------|-------|-------------|
| 1 | J.620100.009.01 | Menggunakan Spesifikasi Program | Aturan bisnis, skema database, rute sesuai spesifikasi |
| 2 | J.620100.016.01 | Menulis Kode sesuai Guidelines & Best Practices | Pemisahan MVC + service, penamaan konsisten, prepared statement |
| 3 | J.620100.017.02 | Mengimplementasikan Pemrograman Terstruktur | Percabangan, perulangan, fungsi, array |
| 4 | J.620100.018.02 | Mengimplementasikan Pemrograman Berorientasi Objek | Class, inheritance, encapsulation, abstraction, singleton |
| 5 | J.620100.019.02 | Menggunakan Library/Komponen Pre-existing | PDO, Bootstrap 5, Bootstrap Icons, PHPUnit |
| 6 | J.620100.021.02 | Menerapkan Akses Basis Data | PDO, CRUD, JOIN, agregasi, transaksi |
| 7 | J.620100.023.02 | Membuat Dokumentasi Kode Program | PHPDoc, README, panduan |
| 8 | J.620100.025.02 | Melakukan Debugging | try/catch, rollback, log error, catatan perbaikan bug |
| 9 | J.620100.033.02 | Melaksanakan Pengujian Unit Program | PHPUnit, 24 test |

Detail lengkap: [docs/CHECKLIST_DEMO.md](docs/CHECKLIST_DEMO.md)

## Lisensi

Proyek ini dibuat untuk keperluan pendidikan dan sertifikasi.
