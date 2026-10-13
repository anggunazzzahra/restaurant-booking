<?php
require_once __DIR__ . '/Model.php';

/**
 * Class ReservationDetail
 *
 * Model untuk tabel reservation_details (detail transaksi pemesanan).
 * Setiap pemesanan bisa memiliki banyak detail menu.
 *
 * @package RestaurantBooking\Models
 */
class ReservationDetail extends Model
{
    /** @var string Nama tabel di database */
    protected string $table = 'reservation_details';

    /**
     * Mengambil detail pemesanan berdasarkan reservation_id.
     *
     * @param int $reservationId ID pemesanan
     * @return array Daftar detail item
     */
    public function getByReservationId(int $reservationId): array
    {
        $sql = "SELECT rd.*, m.name as menu_name, m.price as current_price,
                       mc.name as category_name
                FROM `{$this->table}` rd
                INNER JOIN `menus` m ON rd.menu_id = m.id
                INNER JOIN `menu_categories` mc ON m.category_id = mc.id
                WHERE rd.reservation_id = :rid
                ORDER BY mc.name, m.name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['rid' => $reservationId]);
        return $stmt->fetchAll();
    }

    /**
     * Menghapus semua detail untuk pemesanan tertentu.
     *
     * @param int $reservationId ID pemesanan
     * @return bool True jika berhasil
     */
    public function deleteByReservationId(int $reservationId): bool
    {
        $sql = "DELETE FROM `{$this->table}` WHERE `reservation_id` = :rid";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['rid' => $reservationId]);
    }

    /**
     * Menghitung total harga dari semua detail dalam satu pemesanan.
     *
     * Algoritma: SUM(quantity * price) untuk setiap item
     *
     * @param int $reservationId ID pemesanan
     * @return float Total harga
     */
    public function calculateTotal(int $reservationId): float
    {
        $sql = "SELECT COALESCE(SUM(subtotal), 0) FROM `{$this->table}`
                WHERE `reservation_id` = :rid";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['rid' => $reservationId]);
        return (float) $stmt->fetchColumn();
    }
}
