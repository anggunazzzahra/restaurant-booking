<?php
/**
 * Class ValidationService
 *
 * Layanan validasi input server-side.
 * Menyediakan method untuk validasi data pelanggan, meja,
 * menu, dan pemesanan.
 *
 * Menerapkan pemrograman terstruktur: percabangan, perulangan,
 * fungsi validasi, dan penanganan error.
 *
 * @package RestaurantBooking\Services
 */
class ValidationService
{
    /** @var array Kumpulan pesan error */
    private array $errors = [];

    /**
     * Mendapatkan semua error validasi.
     *
     * @return array Daftar error
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Memeriksa apakah ada error validasi.
     *
     * @return bool True jika ada error
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Menambah pesan error.
     *
     * @param string $field   Nama field
     * @param string $message Pesan error
     */
    public function addError(string $field, string $message): void
    {
        $this->errors[$field] = $message;
    }

    /**
     * Mengosongkan error.
     */
    public function clearErrors(): void
    {
        $this->errors = [];
    }

    /**
     * Mendapatkan pesan error pertama (untuk flash message).
     *
     * @return string Pesan error pertama atau string kosong
     */
    public function getFirstError(): string
    {
        return !empty($this->errors) ? reset($this->errors) : '';
    }

    // ====================================================================
    // VALIDASI PELANGGAN
    // ====================================================================

    /**
     * Memvalidasi data pelanggan.
     *
     * Aturan validasi:
     * - Nama: wajib, maks 100 karakter
     * - Telepon: wajib, format nomor Indonesia (08xx), 10-15 digit
     * - Email: opsional, harus valid jika diisi
     *
     * @param array $data Data pelanggan
     * @return bool True jika valid
     */
    public function validateCustomer(array $data): bool
    {
        $this->clearErrors();

        // Validasi nama
        $name = trim($data['name'] ?? '');
        if (empty($name)) {
            $this->addError('name', 'Nama pelanggan wajib diisi.');
        } elseif (mb_strlen($name) > 100) {
            $this->addError('name', 'Nama pelanggan maksimal 100 karakter.');
        }

        // Validasi telepon
        $phone = trim($data['phone'] ?? '');
        if (empty($phone)) {
            $this->addError('phone', 'Nomor telepon wajib diisi.');
        } elseif (!preg_match('/^08[0-9]{8,13}$/', $phone)) {
            $this->addError('phone', 'Format nomor telepon tidak valid (contoh: 081234567890).');
        }

        // Validasi email (opsional)
        $email = trim($data['email'] ?? '');
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addError('email', 'Format email tidak valid.');
        } elseif (!empty($email) && mb_strlen($email) > 100) {
            $this->addError('email', 'Email maksimal 100 karakter.');
        }

        return !$this->hasErrors();
    }

    // ====================================================================
    // VALIDASI MEJA
    // ====================================================================

    /**
     * Memvalidasi data meja restoran.
     *
     * @param array $data Data meja
     * @return bool True jika valid
     */
    public function validateTable(array $data): bool
    {
        $this->clearErrors();

        // Validasi nomor meja
        $tableNumber = trim($data['table_number'] ?? '');
        if (empty($tableNumber)) {
            $this->addError('table_number', 'Nomor meja wajib diisi.');
        } elseif (mb_strlen($tableNumber) > 10) {
            $this->addError('table_number', 'Nomor meja maksimal 10 karakter.');
        }

        // Validasi kapasitas
        $capacity = $data['capacity'] ?? 0;
        if (!is_numeric($capacity) || (int)$capacity < 1) {
            $this->addError('capacity', 'Kapasitas meja harus minimal 1 orang.');
        } elseif ((int)$capacity > 50) {
            $this->addError('capacity', 'Kapasitas meja maksimal 50 orang.');
        }

        // Validasi status
        $status = $data['status'] ?? 'tersedia';
        if (!in_array($status, ['tersedia', 'dipesan', 'digunakan'], true)) {
            $this->addError('status', 'Status meja tidak valid.');
        }

        return !$this->hasErrors();
    }

    // ====================================================================
    // VALIDASI MENU
    // ====================================================================

    /**
     * Memvalidasi data menu.
     *
     * @param array $data Data menu
     * @return bool True jika valid
     */
    public function validateMenu(array $data): bool
    {
        $this->clearErrors();

        // Validasi nama menu
        $name = trim($data['name'] ?? '');
        if (empty($name)) {
            $this->addError('name', 'Nama menu wajib diisi.');
        } elseif (mb_strlen($name) > 100) {
            $this->addError('name', 'Nama menu maksimal 100 karakter.');
        }

        // Validasi kategori
        $categoryId = $data['category_id'] ?? 0;
        if (!is_numeric($categoryId) || (int)$categoryId < 1) {
            $this->addError('category_id', 'Kategori menu wajib dipilih.');
        }

        // Validasi harga
        $price = $data['price'] ?? 0;
        if (!is_numeric($price) || (float)$price < 0) {
            $this->addError('price', 'Harga menu harus berupa angka positif.');
        } elseif ((float)$price > 99999999.99) {
            $this->addError('price', 'Harga menu terlalu besar.');
        }

        return !$this->hasErrors();
    }

    // ====================================================================
    // VALIDASI PEMESANAN
    // ====================================================================

    /**
     * Memvalidasi data pemesanan.
     *
     * Aturan:
     * - Pelanggan dan meja harus valid
     * - Tanggal tidak boleh di masa lalu
     * - Waktu harus dalam format valid
     * - Jumlah tamu harus > 0
     * - Minimal 1 item menu
     *
     * @param array $data Data pemesanan
     * @return bool True jika valid
     */
    public function validateReservation(array $data): bool
    {
        $this->clearErrors();

        // Validasi pelanggan
        if (empty($data['customer_id']) || !is_numeric($data['customer_id'])) {
            $this->addError('customer_id', 'Pelanggan wajib dipilih.');
        }

        // Validasi meja
        if (empty($data['table_id']) || !is_numeric($data['table_id'])) {
            $this->addError('table_id', 'Meja wajib dipilih.');
        }

        // Validasi tanggal
        $date = $data['reservation_date'] ?? '';
        if (empty($date)) {
            $this->addError('reservation_date', 'Tanggal reservasi wajib diisi.');
        } elseif (!$this->isValidDate($date)) {
            $this->addError('reservation_date', 'Format tanggal tidak valid.');
        } elseif ($date < date('Y-m-d')) {
            $this->addError('reservation_date', 'Tanggal reservasi tidak boleh di masa lalu.');
        }

        // Validasi waktu
        $time = $data['reservation_time'] ?? '';
        if (empty($time)) {
            $this->addError('reservation_time', 'Waktu reservasi wajib diisi.');
        } elseif (!$this->isValidTime($time)) {
            $this->addError('reservation_time', 'Format waktu tidak valid.');
        }

        // Validasi jumlah tamu
        $guestCount = $data['guest_count'] ?? 0;
        if (!is_numeric($guestCount) || (int)$guestCount < 1) {
            $this->addError('guest_count', 'Jumlah tamu harus minimal 1 orang.');
        } elseif ((int)$guestCount > 100) {
            $this->addError('guest_count', 'Jumlah tamu terlalu banyak.');
        }

        // Validasi item menu
        $menuItems = $data['menu_items'] ?? [];
        if (empty($menuItems) || !is_array($menuItems)) {
            $this->addError('menu_items', 'Minimal 1 menu harus dipilih.');
        } else {
            // Validasi setiap item menu
            foreach ($menuItems as $index => $item) {
                if (empty($item['menu_id']) || !is_numeric($item['menu_id'])) {
                    $this->addError("menu_item_{$index}", "Menu item ke-" . ($index + 1) . " tidak valid.");
                }
                if (empty($item['quantity']) || (int)$item['quantity'] < 1) {
                    $this->addError("menu_qty_{$index}", "Jumlah menu item ke-" . ($index + 1) . " harus minimal 1.");
                }
            }
        }

        // Validasi status
        $status = $data['status'] ?? 'menunggu';
        if (!in_array($status, ['menunggu', 'dikonfirmasi', 'selesai', 'dibatalkan'], true)) {
            $this->addError('status', 'Status pemesanan tidak valid.');
        }

        return !$this->hasErrors();
    }

    /**
     * Memvalidasi kapasitas meja terhadap jumlah tamu.
     *
     * @param int $guestCount    Jumlah tamu
     * @param int $tableCapacity Kapasitas meja
     * @return bool True jika kapasitas mencukupi
     */
    public function validateCapacity(int $guestCount, int $tableCapacity): bool
    {
        if ($guestCount > $tableCapacity) {
            $this->addError('guest_count',
                "Jumlah tamu ({$guestCount}) melebihi kapasitas meja ({$tableCapacity} orang).");
            return false;
        }
        return true;
    }

    // ====================================================================
    // HELPER FUNCTIONS
    // ====================================================================

    /**
     * Memvalidasi format tanggal (Y-m-d).
     *
     * @param string $date String tanggal
     * @return bool True jika format valid
     */
    private function isValidDate(string $date): bool
    {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    /**
     * Memvalidasi format waktu (H:i atau H:i:s).
     *
     * @param string $time String waktu
     * @return bool True jika format valid
     */
    private function isValidTime(string $time): bool
    {
        // Mendukung format H:i dan H:i:s
        $t = DateTime::createFromFormat('H:i', $time);
        if (!$t) {
            $t = DateTime::createFromFormat('H:i:s', $time);
        }
        return $t !== false;
    }

    /**
     * Menghitung subtotal (quantity × price).
     *
     * Fungsi utilitas untuk perhitungan harga.
     *
     * @param int   $quantity Jumlah item
     * @param float $price   Harga satuan
     * @return float Subtotal
     */
    public static function calculateSubtotal(int $quantity, float $price): float
    {
        return $quantity * $price;
    }

    /**
     * Menghitung total dari array subtotal.
     *
     * @param array $subtotals Array nilai subtotal
     * @return float Total keseluruhan
     */
    public static function calculateTotal(array $subtotals): float
    {
        return array_sum($subtotals);
    }
}
