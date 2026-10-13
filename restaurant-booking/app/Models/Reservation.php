<?php
require_once __DIR__ . '/Model.php';

/**
 * Class Reservation
 *
 * Model untuk tabel reservations (header transaksi pemesanan).
 * Menyediakan method untuk pengelolaan pemesanan dengan relasi
 * ke customers, restaurant_tables, dan reservation_details.
 *
 * @package RestaurantBooking\Models
 */
class Reservation extends Model
{
    /** @var string Nama tabel di database */
    protected string $table = 'reservations';

    /** @var array Daftar status pemesanan yang valid */
    public const VALID_STATUSES = ['menunggu', 'dikonfirmasi', 'selesai', 'dibatalkan'];

    /** @var array Label status untuk ditampilkan di UI */
    public const STATUS_LABELS = [
        'menunggu'      => 'Menunggu Konfirmasi',
        'dikonfirmasi'  => 'Dikonfirmasi',
        'selesai'       => 'Selesai',
        'dibatalkan'    => 'Dibatalkan',
    ];

    /** @var array Warna badge Bootstrap untuk setiap status */
    public const STATUS_BADGES = [
        'menunggu'      => 'warning',
        'dikonfirmasi'  => 'info',
        'selesai'       => 'success',
        'dibatalkan'    => 'danger',
    ];

    /**
     * Mengambil semua pemesanan beserta nama pelanggan dan nomor meja.
     *
     * @param string|null $status Filter status (opsional)
     * @return array Daftar pemesanan
     */
    public function getAllWithDetails(?string $status = null): array
    {
        $sql = "SELECT r.*, c.name as customer_name, c.phone as customer_phone,
                       t.table_number, t.capacity as table_capacity
                FROM `{$this->table}` r
                INNER JOIN `customers` c ON r.customer_id = c.id
                INNER JOIN `restaurant_tables` t ON r.table_id = t.id";
        $params = [];
        if ($status !== null && in_array($status, self::VALID_STATUSES, true)) {
            $sql .= " WHERE r.status = :status";
            $params['status'] = $status;
        }
        $sql .= " ORDER BY r.reservation_date DESC, r.reservation_time DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Mengambil pemesanan terbaru untuk dashboard.
     *
     * @param int $limit Jumlah data yang diambil
     * @return array Daftar pemesanan terbaru
     */
    public function getRecent(int $limit = 5): array
    {
        $sql = "SELECT r.*, c.name as customer_name, t.table_number
                FROM `{$this->table}` r
                INNER JOIN `customers` c ON r.customer_id = c.id
                INNER JOIN `restaurant_tables` t ON r.table_id = t.id
                ORDER BY r.created_at DESC
                LIMIT :lim";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Mengambil detail lengkap satu pemesanan termasuk detail menu.
     *
     * @param int $id ID pemesanan
     * @return array|false Data pemesanan lengkap atau false
     */
    public function getFullDetail(int $id): array|false
    {
        // Ambil header pemesanan
        $sql = "SELECT r.*, c.name as customer_name, c.phone as customer_phone,
                       c.email as customer_email,
                       t.table_number, t.capacity as table_capacity
                FROM `{$this->table}` r
                INNER JOIN `customers` c ON r.customer_id = c.id
                INNER JOIN `restaurant_tables` t ON r.table_id = t.id
                WHERE r.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $reservation = $stmt->fetch();

        if (!$reservation) {
            return false;
        }

        // Ambil detail menu pesanan
        $sql2 = "SELECT rd.*, m.name as menu_name, mc.name as category_name
                 FROM `reservation_details` rd
                 INNER JOIN `menus` m ON rd.menu_id = m.id
                 INNER JOIN `menu_categories` mc ON m.category_id = mc.id
                 WHERE rd.reservation_id = :rid
                 ORDER BY mc.name ASC, m.name ASC";
        $stmt2 = $this->db->prepare($sql2);
        $stmt2->execute(['rid' => $id]);
        $reservation['items'] = $stmt2->fetchAll();

        return $reservation;
    }

    /**
     * Memeriksa apakah ada bentrok jadwal untuk meja tertentu.
     *
     * Dua reservasi dianggap bentrok jika pada meja dan tanggal yang sama,
     * selisih waktunya kurang dari 2 jam, dan statusnya bukan 'dibatalkan'
     * maupun 'selesai'.
     *
     * @param int         $tableId  ID meja
     * @param string      $date     Tanggal reservasi (Y-m-d)
     * @param string      $time     Waktu reservasi (H:i:s)
     * @param int|null    $excludeId ID reservasi yang dikecualikan (untuk update)
     * @return bool True jika ada bentrokan
     */
    public function hasConflict(int $tableId, string $date, string $time, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM `{$this->table}`
                WHERE `table_id` = :tid
                AND `reservation_date` = :rdate
                AND `status` NOT IN ('dibatalkan', 'selesai')
                AND ABS(TIME_TO_SEC(TIMEDIFF(:rtime, `reservation_time`))) < 7200";
        $params = [
            'tid'   => $tableId,
            'rdate' => $date,
            'rtime' => $time,
        ];
        if ($excludeId !== null) {
            $sql .= " AND `id` != :eid";
            $params['eid'] = $excludeId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Menghitung total pendapatan dari pesanan yang selesai.
     *
     * @return float Total pendapatan
     */
    public function getTotalRevenue(): float
    {
        $sql = "SELECT COALESCE(SUM(total_amount), 0) FROM `{$this->table}` WHERE `status` = 'selesai'";
        $stmt = $this->db->query($sql);
        return (float) $stmt->fetchColumn();
    }

    /**
     * Mengambil laporan pemesanan dalam rentang tanggal.
     *
     * @param string $startDate Tanggal mulai (Y-m-d)
     * @param string $endDate   Tanggal akhir (Y-m-d)
     * @return array Data laporan
     */
    public function getReport(string $startDate, string $endDate): array
    {
        $sql = "SELECT r.*, c.name as customer_name, t.table_number
                FROM `{$this->table}` r
                INNER JOIN `customers` c ON r.customer_id = c.id
                INNER JOIN `restaurant_tables` t ON r.table_id = t.id
                WHERE r.reservation_date BETWEEN :start AND :end
                ORDER BY r.reservation_date ASC, r.reservation_time ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['start' => $startDate, 'end' => $endDate]);
        return $stmt->fetchAll();
    }

    /**
     * Mengambil ringkasan laporan (jumlah per status, pendapatan).
     *
     * @param string $startDate Tanggal mulai
     * @param string $endDate   Tanggal akhir
     * @return array Ringkasan laporan
     */
    public function getReportSummary(string $startDate, string $endDate): array
    {
        $sql = "SELECT
                    COUNT(*) as total_reservations,
                    SUM(CASE WHEN status = 'menunggu' THEN 1 ELSE 0 END) as menunggu,
                    SUM(CASE WHEN status = 'dikonfirmasi' THEN 1 ELSE 0 END) as dikonfirmasi,
                    SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) as selesai,
                    SUM(CASE WHEN status = 'dibatalkan' THEN 1 ELSE 0 END) as dibatalkan,
                    COALESCE(SUM(CASE WHEN status = 'selesai' THEN total_amount ELSE 0 END), 0) as revenue
                FROM `{$this->table}`
                WHERE reservation_date BETWEEN :start AND :end";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['start' => $startDate, 'end' => $endDate]);
        return $stmt->fetch();
    }

    /**
     * Mengambil menu terjual dalam rentang tanggal.
     *
     * @param string $startDate Tanggal mulai
     * @param string $endDate   Tanggal akhir
     * @return array Daftar menu terjual
     */
    public function getMenuSoldReport(string $startDate, string $endDate): array
    {
        $sql = "SELECT m.name as menu_name, mc.name as category_name,
                       SUM(rd.quantity) as total_qty,
                       SUM(rd.subtotal) as total_sales
                FROM `reservation_details` rd
                INNER JOIN `reservations` r ON rd.reservation_id = r.id
                INNER JOIN `menus` m ON rd.menu_id = m.id
                INNER JOIN `menu_categories` mc ON m.category_id = mc.id
                WHERE r.reservation_date BETWEEN :start AND :end
                AND r.status = 'selesai'
                GROUP BY m.id, m.name, mc.name
                ORDER BY total_qty DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['start' => $startDate, 'end' => $endDate]);
        return $stmt->fetchAll();
    }
}
