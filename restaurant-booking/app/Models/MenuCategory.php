<?php
require_once __DIR__ . '/Model.php';

/**
 * Class MenuCategory
 *
 * Model untuk tabel menu_categories.
 *
 * @package RestaurantBooking\Models
 */
class MenuCategory extends Model
{
    /** @var string Nama tabel di database */
    protected string $table = 'menu_categories';

    /**
     * Mengambil semua kategori, diurutkan berdasarkan nama.
     *
     * @return array Daftar kategori
     */
    public function getAllSorted(): array
    {
        return $this->getAll('name', 'ASC');
    }

    /**
     * Mengambil kategori beserta jumlah menu di masing-masing kategori.
     *
     * @return array Kategori dengan count menu
     */
    public function getAllWithMenuCount(): array
    {
        $sql = "SELECT mc.*, COUNT(m.id) as menu_count
                FROM `{$this->table}` mc
                LEFT JOIN `menus` m ON mc.id = m.category_id
                GROUP BY mc.id
                ORDER BY mc.name ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}
