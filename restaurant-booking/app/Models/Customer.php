<?php
require_once __DIR__ . '/Model.php';

/**
 * Class Customer
 *
 * Model untuk tabel customers. Mewarisi operasi CRUD dari Model.
 * Menambahkan method khusus untuk pencarian pelanggan.
 *
 * Menerapkan OOP: inheritance (extends Model), encapsulation (property protected).
 *
 * @package RestaurantBooking\Models
 */
class Customer extends Model
{
    /** @var string Nama tabel di database */
    protected string $table = 'customers';

    /**
     * Mencari pelanggan berdasarkan nama atau telepon.
     *
     * @param string $keyword Kata kunci pencarian
     * @return array Daftar pelanggan yang cocok
     */
    public function search(string $keyword): array
    {
        $sql = "SELECT * FROM `{$this->table}`
                WHERE `name` LIKE :keyword OR `phone` LIKE :keyword2
                ORDER BY `name` ASC";
        $stmt = $this->db->prepare($sql);
        $param = "%{$keyword}%";
        $stmt->execute(['keyword' => $param, 'keyword2' => $param]);
        return $stmt->fetchAll();
    }

    /**
     * Memeriksa apakah nomor telepon sudah digunakan pelanggan lain.
     *
     * @param string   $phone     Nomor telepon
     * @param int|null $excludeId ID pelanggan yang dikecualikan (untuk update)
     * @return bool True jika telepon sudah ada
     */
    public function phoneExists(string $phone, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM `{$this->table}` WHERE `phone` = :phone";
        $params = ['phone' => $phone];
        if ($excludeId !== null) {
            $sql .= " AND `id` != :id";
            $params['id'] = $excludeId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Mengambil semua pelanggan, diurutkan berdasarkan nama.
     *
     * @return array Daftar pelanggan
     */
    public function getAllSorted(): array
    {
        return $this->getAll('name', 'ASC');
    }
}
