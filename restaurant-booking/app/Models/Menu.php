<?php
require_once __DIR__ . '/Model.php';

/**
 * Class Menu
 *
 * Model untuk tabel menus. Mendukung relasi ke menu_categories.
 *
 * @package RestaurantBooking\Models
 */
class Menu extends Model
{
    /** @var string Nama tabel di database */
    protected string $table = 'menus';

    /**
     * Mengambil semua menu beserta nama kategorinya.
     *
     * Menggunakan JOIN ke tabel menu_categories.
     *
     * @return array Daftar menu dengan informasi kategori
     */
    public function getAllWithCategory(): array
    {
        $sql = "SELECT m.*, mc.name as category_name
                FROM `{$this->table}` m
                INNER JOIN `menu_categories` mc ON m.category_id = mc.id
                ORDER BY mc.name ASC, m.name ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Mengambil menu yang tersedia saja, beserta kategori.
     *
     * @return array Daftar menu tersedia
     */
    public function getAvailable(): array
    {
        $sql = "SELECT m.*, mc.name as category_name
                FROM `{$this->table}` m
                INNER JOIN `menu_categories` mc ON m.category_id = mc.id
                WHERE m.is_available = 1
                ORDER BY mc.name ASC, m.name ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Mengambil menu berdasarkan kategori tertentu.
     *
     * @param int $categoryId ID kategori
     * @return array Daftar menu dalam kategori tersebut
     */
    public function getByCategory(int $categoryId): array
    {
        return $this->getWhere('category_id = :cid', ['cid' => $categoryId], 'name');
    }

    /**
     * Mengambil satu menu beserta nama kategorinya.
     *
     * @param int $id ID menu
     * @return array|false Data menu atau false
     */
    public function getByIdWithCategory(int $id): array|false
    {
        $sql = "SELECT m.*, mc.name as category_name
                FROM `{$this->table}` m
                INNER JOIN `menu_categories` mc ON m.category_id = mc.id
                WHERE m.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
}
