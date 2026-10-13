# Spesifikasi Database — Sistem Pemesanan Restoran

## Entity Relationship Diagram (ERD) — Deskripsi Tekstual

```
┌──────────────┐       ┌──────────────────┐       ┌─────────────────┐
│  customers   │       │   reservations   │       │restaurant_tables│
├──────────────┤       ├──────────────────┤       ├─────────────────┤
│ PK id        │──1:N──│ FK customer_id   │  N:1──│ PK id           │
│    name      │       │ FK table_id      │───────│    table_number  │
│    phone     │       │    reservation_  │       │    capacity      │
│    email     │       │      date        │       │    status        │
│    created_at│       │    reservation_  │       │    created_at    │
│    updated_at│       │      time        │       │    updated_at    │
└──────────────┘       │    guest_count   │       └─────────────────┘
                       │    status        │
                       │    notes         │
                       │    total_amount  │
                       │    created_at    │
                       │    updated_at    │
                       └────────┬─────────┘
                                │
                                │ 1:N
                                │
                       ┌────────┴─────────┐       ┌─────────────────┐
                       │reservation_     │       │     menus        │
                       │  details         │       ├─────────────────┤
                       ├──────────────────┤  N:1──│ PK id           │
                       │ PK id            │───────│ FK category_id  │
                       │ FK reservation_id│       │    name          │
                       │ FK menu_id       │       │    price         │
                       │    quantity       │       │    is_available  │
                       │    price          │       │    created_at    │
                       │    subtotal       │       │    updated_at    │
                       │    created_at     │       └────────┬────────┘
                       └──────────────────┘                │
                                                           │ N:1
                                                           │
                                                  ┌────────┴────────┐
                                                  │ menu_categories │
                                                  ├─────────────────┤
                                                  │ PK id           │
                                                  │    name          │
                                                  │    created_at    │
                                                  │    updated_at    │
                                                  └─────────────────┘
```

## Daftar Tabel

### 1. customers (Pelanggan)

| Kolom | Tipe Data | Constraint | Keterangan |
|-------|-----------|------------|------------|
| id | INT | PK, AUTO_INCREMENT | ID unik |
| name | VARCHAR(100) | NOT NULL, INDEX | Nama pelanggan |
| phone | VARCHAR(20) | NOT NULL, INDEX | Nomor telepon |
| email | VARCHAR(100) | NULL | Email (opsional) |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | ON UPDATE CURRENT_TIMESTAMP | Waktu diperbarui |

### 2. restaurant_tables (Meja Restoran)

| Kolom | Tipe Data | Constraint | Keterangan |
|-------|-----------|------------|------------|
| id | INT | PK, AUTO_INCREMENT | ID unik |
| table_number | VARCHAR(10) | NOT NULL, UNIQUE | Nomor meja |
| capacity | INT | NOT NULL, DEFAULT 4 | Kapasitas orang |
| status | ENUM | 'tersedia','dipesan','digunakan' | Status meja |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | ON UPDATE CURRENT_TIMESTAMP | Waktu diperbarui |

### 3. menu_categories (Kategori Menu)

| Kolom | Tipe Data | Constraint | Keterangan |
|-------|-----------|------------|------------|
| id | INT | PK, AUTO_INCREMENT | ID unik |
| name | VARCHAR(50) | NOT NULL, UNIQUE | Nama kategori |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | ON UPDATE CURRENT_TIMESTAMP | Waktu diperbarui |

### 4. menus (Menu Makanan/Minuman)

| Kolom | Tipe Data | Constraint | Keterangan |
|-------|-----------|------------|------------|
| id | INT | PK, AUTO_INCREMENT | ID unik |
| category_id | INT | FK → menu_categories(id) | ID kategori |
| name | VARCHAR(100) | NOT NULL | Nama menu |
| price | DECIMAL(12,2) | NOT NULL, DEFAULT 0 | Harga (Rp) |
| is_available | TINYINT(1) | DEFAULT 1 | 1=tersedia, 0=tidak |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | ON UPDATE CURRENT_TIMESTAMP | Waktu diperbarui |

### 5. reservations (Pemesanan)

| Kolom | Tipe Data | Constraint | Keterangan |
|-------|-----------|------------|------------|
| id | INT | PK, AUTO_INCREMENT | ID unik |
| customer_id | INT | FK → customers(id) | ID pelanggan |
| table_id | INT | FK → restaurant_tables(id) | ID meja |
| reservation_date | DATE | NOT NULL | Tanggal reservasi |
| reservation_time | TIME | NOT NULL | Waktu reservasi |
| guest_count | INT | NOT NULL, DEFAULT 1 | Jumlah tamu |
| status | ENUM | 'menunggu','dikonfirmasi','selesai','dibatalkan' | Status |
| notes | TEXT | NULL | Catatan tambahan |
| total_amount | DECIMAL(14,2) | DEFAULT 0 | Total harga |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | ON UPDATE CURRENT_TIMESTAMP | Waktu diperbarui |

### 6. reservation_details (Detail Pemesanan)

| Kolom | Tipe Data | Constraint | Keterangan |
|-------|-----------|------------|------------|
| id | INT | PK, AUTO_INCREMENT | ID unik |
| reservation_id | INT | FK → reservations(id) ON DELETE CASCADE | ID pemesanan |
| menu_id | INT | FK → menus(id) | ID menu |
| quantity | INT | NOT NULL, DEFAULT 1 | Jumlah pesanan |
| price | DECIMAL(12,2) | NOT NULL | Harga saat pesan |
| subtotal | DECIMAL(14,2) | NOT NULL | quantity × price |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Waktu dibuat |

## Relasi Antar Tabel

| Dari | Ke | Tipe | Keterangan |
|------|-----|------|------------|
| customers | reservations | 1:N | Satu pelanggan bisa punya banyak pemesanan |
| restaurant_tables | reservations | 1:N | Satu meja bisa dipakai banyak pemesanan |
| menu_categories | menus | 1:N | Satu kategori punya banyak menu |
| reservations | reservation_details | 1:N | Satu pemesanan punya banyak item (CASCADE DELETE) |
| menus | reservation_details | 1:N | Satu menu bisa muncul di banyak detail |

## Indeks

- `customers`: INDEX pada `name`, `phone` (pencarian cepat)
- `restaurant_tables`: UNIQUE pada `table_number`
- `menu_categories`: UNIQUE pada `name`
- `reservations`: INDEX pada `customer_id`, `table_id` (FK)
- `reservation_details`: INDEX pada `reservation_id`, `menu_id` (FK)
