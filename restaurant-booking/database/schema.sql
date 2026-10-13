-- ============================================================
-- Sistem Pemesanan Restoran - Database Schema
-- Database: MySQL / MariaDB
-- Charset: utf8mb4 (mendukung emoji dan karakter khusus)
-- ============================================================

-- Buat database jika belum ada
CREATE DATABASE IF NOT EXISTS `restaurant_booking`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `restaurant_booking`;

-- ============================================================
-- Tabel: customers
-- Deskripsi: Menyimpan data pelanggan restoran
-- ============================================================
DROP TABLE IF EXISTS `reservation_details`;
DROP TABLE IF EXISTS `reservations`;
DROP TABLE IF EXISTS `menus`;
DROP TABLE IF EXISTS `menu_categories`;
DROP TABLE IF EXISTS `restaurant_tables`;
DROP TABLE IF EXISTS `customers`;

CREATE TABLE `customers` (
    `id`         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(100)    NOT NULL COMMENT 'Nama lengkap pelanggan',
    `phone`      VARCHAR(20)     NOT NULL COMMENT 'Nomor telepon pelanggan',
    `email`      VARCHAR(100)    DEFAULT NULL COMMENT 'Alamat email pelanggan',
    `created_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_customers_name` (`name`),
    INDEX `idx_customers_phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Data pelanggan restoran';

-- ============================================================
-- Tabel: restaurant_tables
-- Deskripsi: Menyimpan data meja restoran beserta kapasitas dan status
-- Status: tersedia, dipesan, digunakan
-- ============================================================
CREATE TABLE `restaurant_tables` (
    `id`           INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    `table_number` VARCHAR(10)    NOT NULL COMMENT 'Nomor meja (misal: M01, M02)',
    `capacity`     TINYINT UNSIGNED NOT NULL DEFAULT 4 COMMENT 'Kapasitas maksimal orang',
    `status`       ENUM('tersedia','dipesan','digunakan') NOT NULL DEFAULT 'tersedia',
    `created_at`   DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_table_number` (`table_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Data meja restoran';

-- ============================================================
-- Tabel: menu_categories
-- Deskripsi: Kategori menu (Makanan Utama, Minuman, Dessert, dll.)
-- ============================================================
CREATE TABLE `menu_categories` (
    `id`         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(50)   NOT NULL COMMENT 'Nama kategori menu',
    `created_at` DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_category_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Kategori menu restoran';

-- ============================================================
-- Tabel: menus
-- Deskripsi: Daftar menu makanan dan minuman
-- Relasi: many-to-one ke menu_categories
-- ============================================================
CREATE TABLE `menus` (
    `id`           INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `category_id`  INT UNSIGNED    NOT NULL COMMENT 'FK ke menu_categories',
    `name`         VARCHAR(100)    NOT NULL COMMENT 'Nama menu',
    `price`        DECIMAL(12,2)   NOT NULL DEFAULT 0.00 COMMENT 'Harga satuan',
    `is_available` TINYINT(1)      NOT NULL DEFAULT 1 COMMENT '1=tersedia, 0=tidak tersedia',
    `created_at`   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_menus_category` (`category_id`),
    INDEX `idx_menus_available` (`is_available`),
    CONSTRAINT `fk_menus_category` FOREIGN KEY (`category_id`)
        REFERENCES `menu_categories` (`id`)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Daftar menu restoran';

-- ============================================================
-- Tabel: reservations
-- Deskripsi: Data pemesanan/reservasi (header transaksi)
-- Relasi: many-to-one ke customers dan restaurant_tables
-- Status: menunggu, dikonfirmasi, selesai, dibatalkan
-- ============================================================
CREATE TABLE `reservations` (
    `id`               INT UNSIGNED      NOT NULL AUTO_INCREMENT,
    `customer_id`      INT UNSIGNED      NOT NULL COMMENT 'FK ke customers',
    `table_id`         INT UNSIGNED      NOT NULL COMMENT 'FK ke restaurant_tables',
    `reservation_date` DATE              NOT NULL COMMENT 'Tanggal reservasi',
    `reservation_time` TIME              NOT NULL COMMENT 'Waktu reservasi',
    `guest_count`      TINYINT UNSIGNED  NOT NULL DEFAULT 1 COMMENT 'Jumlah tamu',
    `status`           ENUM('menunggu','dikonfirmasi','selesai','dibatalkan')
                       NOT NULL DEFAULT 'menunggu',
    `notes`            TEXT              DEFAULT NULL COMMENT 'Catatan tambahan',
    `total_amount`     DECIMAL(14,2)     NOT NULL DEFAULT 0.00 COMMENT 'Total harga pesanan',
    `created_at`       DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_reservations_customer` (`customer_id`),
    INDEX `idx_reservations_table` (`table_id`),
    INDEX `idx_reservations_date` (`reservation_date`),
    INDEX `idx_reservations_status` (`status`),
    CONSTRAINT `fk_reservations_customer` FOREIGN KEY (`customer_id`)
        REFERENCES `customers` (`id`)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT `fk_reservations_table` FOREIGN KEY (`table_id`)
        REFERENCES `restaurant_tables` (`id`)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Data pemesanan restoran';

-- ============================================================
-- Tabel: reservation_details
-- Deskripsi: Detail menu dalam pemesanan (detail transaksi)
-- Relasi: many-to-one ke reservations dan menus
-- Satu pemesanan bisa memiliki banyak detail menu
-- ============================================================
CREATE TABLE `reservation_details` (
    `id`             INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `reservation_id` INT UNSIGNED    NOT NULL COMMENT 'FK ke reservations',
    `menu_id`        INT UNSIGNED    NOT NULL COMMENT 'FK ke menus',
    `quantity`        SMALLINT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Jumlah pesanan',
    `price`          DECIMAL(12,2)   NOT NULL COMMENT 'Harga satuan saat pesan',
    `subtotal`       DECIMAL(14,2)   NOT NULL COMMENT 'quantity * price',
    `created_at`     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_resdetail_reservation` (`reservation_id`),
    INDEX `idx_resdetail_menu` (`menu_id`),
    CONSTRAINT `fk_resdetail_reservation` FOREIGN KEY (`reservation_id`)
        REFERENCES `reservations` (`id`)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `fk_resdetail_menu` FOREIGN KEY (`menu_id`)
        REFERENCES `menus` (`id`)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Detail menu dalam pemesanan';
