<?php
require_once __DIR__ . '/Model.php';

/**
 * Class RestaurantTable
 *
 * Model untuk tabel restaurant_tables.
 * Menyediakan operasi terkait pengelolaan meja restoran.
 *
 * @package RestaurantBooking\Models
 */
class RestaurantTable extends Model
{
    /** @var string Nama tabel di database */
    protected string $table = 'restaurant_tables';

    /** @var array Daftar status meja yang valid */
    public const VALID_STATUSES = ['tersedia', 'dipesan', 'digunakan'];

    /**
     * Mengambil semua meja, diurutkan berdasarkan nomor meja.
     *
     * @return array Daftar meja
     */
    public function getAllSorted(): array
    {
        return $this->getAll('table_number', 'ASC');
    }

    /**
     * Mengambil meja yang tersedia dengan kapasitas minimal tertentu.
     *
     * @param int $minCapacity Kapasitas minimum yang dibutuhkan
     * @return array Daftar meja yang tersedia
     */
    public function getAvailable(int $minCapacity = 1): array
    {
        $sql = "SELECT * FROM `{$this->table}`
                WHERE `capacity` >= :capacity
                ORDER BY `capacity` ASC, `table_number` ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['capacity' => $minCapacity]);
        return $stmt->fetchAll();
    }

    /**
     * Memeriksa apakah nomor meja sudah ada (untuk validasi duplikat).
     *
     * @param string   $tableNumber Nomor meja
     * @param int|null $excludeId   ID meja yang dikecualikan (untuk update)
     * @return bool True jika nomor meja sudah ada
     */
    public function tableNumberExists(string $tableNumber, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM `{$this->table}` WHERE `table_number` = :tn";
        $params = ['tn' => $tableNumber];
        if ($excludeId !== null) {
            $sql .= " AND `id` != :id";
            $params['id'] = $excludeId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Memperbarui status meja.
     *
     * @param int    $id     ID meja
     * @param string $status Status baru
     * @return bool True jika berhasil
     * @throws InvalidArgumentException Jika status tidak valid
     */
    public function updateStatus(int $id, string $status): bool
    {
        if (!in_array($status, self::VALID_STATUSES, true)) {
            throw new InvalidArgumentException("Status meja tidak valid: {$status}");
        }
        return $this->update($id, ['status' => $status]);
    }
}
