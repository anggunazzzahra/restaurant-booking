<?php
require_once __DIR__ . '/../Models/Reservation.php';
require_once __DIR__ . '/../Models/ReservationDetail.php';
require_once __DIR__ . '/../Models/Menu.php';
require_once __DIR__ . '/../Models/RestaurantTable.php';
require_once __DIR__ . '/ValidationService.php';

/**
 * Class ReservationService
 *
 * Service layer untuk logika bisnis pemesanan.
 * Mengkoordinasikan Model, validasi, dan transaksi database.
 *
 * Menerapkan pemisahan tanggung jawab (separation of concerns):
 * - Controller: routing dan request handling
 * - Service: logika bisnis
 * - Model: akses database
 *
 * @package RestaurantBooking\Services
 */
class ReservationService
{
    /** @var PDO Instance koneksi database */
    private PDO $db;

    /** @var Reservation Model reservasi */
    private Reservation $reservationModel;

    /** @var ReservationDetail Model detail reservasi */
    private ReservationDetail $detailModel;

    /** @var Menu Model menu */
    private Menu $menuModel;

    /** @var RestaurantTable Model meja */
    private RestaurantTable $tableModel;

    /** @var ValidationService Service validasi */
    private ValidationService $validator;

    /**
     * Constructor: dependency injection untuk model dan service.
     *
     * @param PDO $db Koneksi database
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->reservationModel = new Reservation($db);
        $this->detailModel = new ReservationDetail($db);
        $this->menuModel = new Menu($db);
        $this->tableModel = new RestaurantTable($db);
        $this->validator = new ValidationService();
    }

    /**
     * Membuat pemesanan baru beserta detail menu.
     *
     * Algoritma:
     * 1. Validasi data input
     * 2. Cek kapasitas meja
     * 3. Cek bentrok jadwal
     * 4. Mulai transaksi database
     * 5. Simpan header reservasi
     * 6. Simpan detail menu (loop)
     * 7. Hitung dan update total harga
     * 8. Commit transaksi
     *
     * Menggunakan database transaction untuk menjaga integritas data.
     *
     * @param array $data Data pemesanan dan menu items
     * @return array ['success' => bool, 'message' => string, 'id' => int|null]
     */
    public function createReservation(array $data): array
    {
        // Step 1: Validasi input
        if (!$this->validator->validateReservation($data)) {
            return [
                'success' => false,
                'message' => $this->validator->getFirstError(),
                'id'      => null,
            ];
        }

        // Step 2: Cek kapasitas meja
        $table = $this->tableModel->getById((int)$data['table_id']);
        if (!$table) {
            return ['success' => false, 'message' => 'Meja tidak ditemukan.', 'id' => null];
        }
        if (!$this->validator->validateCapacity((int)$data['guest_count'], (int)$table['capacity'])) {
            return [
                'success' => false,
                'message' => $this->validator->getFirstError(),
                'id'      => null,
            ];
        }

        // Step 3: Cek bentrok jadwal
        $time = $data['reservation_time'];
        if (strlen($time) === 5) {
            $time .= ':00'; // H:i → H:i:s
        }
        if ($this->reservationModel->hasConflict(
            (int)$data['table_id'],
            $data['reservation_date'],
            $time
        )) {
            return [
                'success' => false,
                'message' => 'Meja sudah dipesan pada tanggal dan waktu yang berdekatan (dalam 2 jam).',
                'id'      => null,
            ];
        }

        // Step 4-8: Transaksi database
        $this->db->beginTransaction();
        try {
            // Step 5: Simpan header reservasi
            $reservationId = $this->reservationModel->create([
                'customer_id'      => (int)$data['customer_id'],
                'table_id'         => (int)$data['table_id'],
                'reservation_date' => $data['reservation_date'],
                'reservation_time' => $time,
                'guest_count'      => (int)$data['guest_count'],
                'status'           => $data['status'] ?? 'menunggu',
                'notes'            => $data['notes'] ?? null,
                'total_amount'     => 0, // Akan dihitung setelah detail disimpan
            ]);

            // Step 6: Simpan detail menu
            $totalAmount = 0.0;
            foreach ($data['menu_items'] as $item) {
                $menu = $this->menuModel->getById((int)$item['menu_id']);
                if (!$menu) {
                    throw new RuntimeException("Menu dengan ID {$item['menu_id']} tidak ditemukan.");
                }
                $quantity = (int)$item['quantity'];
                $price = (float)$menu['price'];
                $subtotal = ValidationService::calculateSubtotal($quantity, $price);

                $this->detailModel->create([
                    'reservation_id' => $reservationId,
                    'menu_id'        => (int)$item['menu_id'],
                    'quantity'       => $quantity,
                    'price'          => $price,
                    'subtotal'       => $subtotal,
                ]);

                $totalAmount += $subtotal;
            }

            // Step 7: Update total harga
            $this->reservationModel->update($reservationId, [
                'total_amount' => $totalAmount,
            ]);

            // Step 8: Commit
            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Pemesanan berhasil dibuat.',
                'id'      => $reservationId,
            ];
        } catch (Exception $e) {
            // Rollback jika ada error
            $this->db->rollBack();
            error_log("Error creating reservation: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan pemesanan: ' . $e->getMessage(),
                'id'      => null,
            ];
        }
    }

    /**
     * Memperbarui pemesanan yang sudah ada.
     *
     * Menggunakan transaksi: hapus detail lama, simpan detail baru,
     * hitung ulang total.
     *
     * @param int   $id   ID pemesanan
     * @param array $data Data pemesanan baru
     * @return array ['success' => bool, 'message' => string]
     */
    public function updateReservation(int $id, array $data): array
    {
        // Validasi input
        if (!$this->validator->validateReservation($data)) {
            return ['success' => false, 'message' => $this->validator->getFirstError()];
        }

        // Cek pemesanan ada
        $existing = $this->reservationModel->getById($id);
        if (!$existing) {
            return ['success' => false, 'message' => 'Pemesanan tidak ditemukan.'];
        }

        // Cek kapasitas meja
        $table = $this->tableModel->getById((int)$data['table_id']);
        if (!$table) {
            return ['success' => false, 'message' => 'Meja tidak ditemukan.'];
        }
        if (!$this->validator->validateCapacity((int)$data['guest_count'], (int)$table['capacity'])) {
            return ['success' => false, 'message' => $this->validator->getFirstError()];
        }

        // Cek bentrok (kecualikan reservasi ini sendiri)
        $time = $data['reservation_time'];
        if (strlen($time) === 5) {
            $time .= ':00';
        }
        if ($this->reservationModel->hasConflict(
            (int)$data['table_id'],
            $data['reservation_date'],
            $time,
            $id
        )) {
            return ['success' => false, 'message' => 'Meja sudah dipesan pada tanggal dan waktu yang berdekatan.'];
        }

        // Transaksi database
        $this->db->beginTransaction();
        try {
            // Update header
            $this->reservationModel->update($id, [
                'customer_id'      => (int)$data['customer_id'],
                'table_id'         => (int)$data['table_id'],
                'reservation_date' => $data['reservation_date'],
                'reservation_time' => $time,
                'guest_count'      => (int)$data['guest_count'],
                'status'           => $data['status'] ?? $existing['status'],
                'notes'            => $data['notes'] ?? null,
            ]);

            // Hapus detail lama dan buat detail baru
            $this->detailModel->deleteByReservationId($id);

            $totalAmount = 0.0;
            foreach ($data['menu_items'] as $item) {
                $menu = $this->menuModel->getById((int)$item['menu_id']);
                if (!$menu) {
                    throw new RuntimeException("Menu dengan ID {$item['menu_id']} tidak ditemukan.");
                }
                $quantity = (int)$item['quantity'];
                $price = (float)$menu['price'];
                $subtotal = ValidationService::calculateSubtotal($quantity, $price);

                $this->detailModel->create([
                    'reservation_id' => $id,
                    'menu_id'        => (int)$item['menu_id'],
                    'quantity'       => $quantity,
                    'price'          => $price,
                    'subtotal'       => $subtotal,
                ]);
                $totalAmount += $subtotal;
            }

            $this->reservationModel->update($id, ['total_amount' => $totalAmount]);
            $this->db->commit();

            return ['success' => true, 'message' => 'Pemesanan berhasil diperbarui.'];
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error updating reservation: " . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal memperbarui pemesanan: ' . $e->getMessage()];
        }
    }

    /**
     * Mengubah status pemesanan.
     *
     * @param int    $id     ID pemesanan
     * @param string $status Status baru
     * @return array ['success' => bool, 'message' => string]
     */
    public function updateStatus(int $id, string $status): array
    {
        if (!in_array($status, Reservation::VALID_STATUSES, true)) {
            return ['success' => false, 'message' => 'Status tidak valid.'];
        }

        $reservation = $this->reservationModel->getById($id);
        if (!$reservation) {
            return ['success' => false, 'message' => 'Pemesanan tidak ditemukan.'];
        }

        $this->reservationModel->update($id, ['status' => $status]);
        return ['success' => true, 'message' => 'Status pemesanan berhasil diperbarui.'];
    }
}
