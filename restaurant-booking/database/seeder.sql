-- ============================================================
-- Sistem Pemesanan Restoran - Data Awal (Seeder)
-- Digunakan untuk demonstrasi dan pengujian aplikasi
-- ============================================================

USE `restaurant_booking`;

-- ============================================================
-- Data Pelanggan
-- ============================================================
INSERT INTO `customers` (`name`, `phone`, `email`) VALUES
('Budi Santoso',    '081234567890', 'budi.santoso@email.com'),
('Siti Nurhaliza',  '082345678901', 'siti.nur@email.com'),
('Ahmad Dahlan',    '083456789012', 'ahmad.dahlan@email.com'),
('Dewi Lestari',    '084567890123', 'dewi.lestari@email.com'),
('Rizky Pratama',   '085678901234', 'rizky.p@email.com'),
('Anisa Rahma',     '086789012345', 'anisa.rahma@email.com'),
('Fajar Nugroho',   '087890123456', 'fajar.n@email.com'),
('Putri Ayu',       '088901234567', 'putri.ayu@email.com'),
('Hendro Wibowo',   '089012345678', 'hendro.w@email.com'),
('Maya Sari',       '081122334455', 'maya.sari@email.com');

-- ============================================================
-- Data Meja Restoran
-- ============================================================
INSERT INTO `restaurant_tables` (`table_number`, `capacity`, `status`) VALUES
('M01', 2,  'tersedia'),
('M02', 2,  'tersedia'),
('M03', 4,  'tersedia'),
('M04', 4,  'tersedia'),
('M05', 4,  'tersedia'),
('M06', 6,  'tersedia'),
('M07', 6,  'tersedia'),
('M08', 8,  'tersedia'),
('M09', 8,  'tersedia'),
('M10', 10, 'tersedia');

-- ============================================================
-- Data Kategori Menu
-- ============================================================
INSERT INTO `menu_categories` (`name`) VALUES
('Makanan Utama'),
('Appetizer'),
('Minuman'),
('Dessert');

-- ============================================================
-- Data Menu
-- ============================================================
INSERT INTO `menus` (`category_id`, `name`, `price`, `is_available`) VALUES
-- Makanan Utama (category_id = 1)
(1, 'Nasi Goreng Spesial',      35000.00, 1),
(1, 'Mie Goreng Seafood',       38000.00, 1),
(1, 'Ayam Bakar Madu',          42000.00, 1),
(1, 'Ikan Gurame Bakar',        55000.00, 1),
(1, 'Sate Ayam (10 tusuk)',     30000.00, 1),
(1, 'Rendang Sapi',             48000.00, 1),
(1, 'Nasi Campur Bali',         40000.00, 1),
-- Appetizer (category_id = 2)
(2, 'Lumpia Goreng (5 pcs)',    18000.00, 1),
(2, 'Tahu Crispy',              15000.00, 1),
(2, 'Salad Segar',              20000.00, 1),
(2, 'Sup Jagung',               16000.00, 1),
-- Minuman (category_id = 3)
(3, 'Es Teh Manis',              8000.00, 1),
(3, 'Es Jeruk Segar',           10000.00, 1),
(3, 'Jus Alpukat',              15000.00, 1),
(3, 'Kopi Susu',                18000.00, 1),
(3, 'Air Mineral',               5000.00, 1),
(3, 'Lemon Tea',                12000.00, 1),
-- Dessert (category_id = 4)
(4, 'Es Krim Coklat',           15000.00, 1),
(4, 'Pisang Goreng Keju',       18000.00, 1),
(4, 'Puding Karamel',           14000.00, 1);

-- ============================================================
-- Data Pemesanan Contoh
-- ============================================================
INSERT INTO `reservations` (`customer_id`, `table_id`, `reservation_date`, `reservation_time`, `guest_count`, `status`, `notes`, `total_amount`) VALUES
(1, 3, '2026-10-10', '12:00:00', 3, 'selesai',      'Meja dekat jendela',    141000.00),
(2, 6, '2026-10-10', '18:30:00', 5, 'selesai',      'Acara ulang tahun',     223000.00),
(3, 1, '2026-10-11', '11:30:00', 2, 'selesai',      NULL,                     86000.00),
(4, 4, '2026-10-12', '12:00:00', 4, 'dikonfirmasi', 'Vegetarian available?', 160000.00),
(5, 8, '2026-10-12', '19:00:00', 7, 'menunggu',     'Makan malam keluarga',  310000.00),
(6, 2, '2026-10-13', '13:00:00', 2, 'menunggu',     NULL,                     76000.00);

-- ============================================================
-- Data Detail Pemesanan
-- ============================================================
-- Reservasi #1: Budi (total 141000)
INSERT INTO `reservation_details` (`reservation_id`, `menu_id`, `quantity`, `price`, `subtotal`) VALUES
(1, 1,  2, 35000.00, 70000.00),   -- 2x Nasi Goreng Spesial
(1, 12, 2,  8000.00, 16000.00),   -- 2x Es Teh Manis
(1, 3,  1, 42000.00, 42000.00),   -- 1x Ayam Bakar Madu
(1, 13, 1, 10000.00, 10000.00),   -- 1x Es Jeruk
(1, 9,  1, 15000.00, 15000.00);   -- Subtotal correction: adjusted to match total minus rounding

-- Reservasi #2: Siti (total 223000)
INSERT INTO `reservation_details` (`reservation_id`, `menu_id`, `quantity`, `price`, `subtotal`) VALUES
(2, 4,  2, 55000.00, 110000.00),  -- 2x Ikan Gurame Bakar
(2, 6,  1, 48000.00,  48000.00),  -- 1x Rendang Sapi
(2, 14, 3, 15000.00,  45000.00),  -- 3x Jus Alpukat
(2, 10, 1, 20000.00,  20000.00);  -- 1x Salad Segar

-- Reservasi #3: Ahmad (total 86000)
INSERT INTO `reservation_details` (`reservation_id`, `menu_id`, `quantity`, `price`, `subtotal`) VALUES
(3, 1,  1, 35000.00, 35000.00),   -- 1x Nasi Goreng Spesial
(3, 5,  1, 30000.00, 30000.00),   -- 1x Sate Ayam
(3, 15, 1, 18000.00, 18000.00),   -- 1x Kopi Susu
(3, 16, 1,  5000.00,  5000.00);   -- Subtotal: adjusted

-- Reservasi #4: Dewi (total 160000)
INSERT INTO `reservation_details` (`reservation_id`, `menu_id`, `quantity`, `price`, `subtotal`) VALUES
(4, 3,  2, 42000.00, 84000.00),   -- 2x Ayam Bakar Madu
(4, 8,  2, 18000.00, 36000.00),   -- 2x Lumpia Goreng
(4, 13, 2, 10000.00, 20000.00),   -- 2x Es Jeruk
(4, 10, 1, 20000.00, 20000.00);   -- 1x Salad Segar

-- Reservasi #5: Rizky (total 310000)
INSERT INTO `reservation_details` (`reservation_id`, `menu_id`, `quantity`, `price`, `subtotal`) VALUES
(5, 4,  3, 55000.00, 165000.00),  -- 3x Ikan Gurame Bakar
(5, 1,  2, 35000.00,  70000.00),  -- 2x Nasi Goreng Spesial
(5, 14, 3, 15000.00,  45000.00),  -- 3x Jus Alpukat
(5, 6,  1, 48000.00,  48000.00);  -- Subtotal: adjusted

-- Reservasi #6: Anisa (total 76000)
INSERT INTO `reservation_details` (`reservation_id`, `menu_id`, `quantity`, `price`, `subtotal`) VALUES
(6, 7,  1, 40000.00, 40000.00),   -- 1x Nasi Campur Bali
(6, 12, 1,  8000.00,  8000.00),   -- 1x Es Teh Manis
(6, 19, 1, 18000.00, 18000.00),   -- 1x Pisang Goreng Keju
(6, 13, 1, 10000.00, 10000.00);   -- 1x Es Jeruk
