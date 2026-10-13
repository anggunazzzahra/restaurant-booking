<?php
/**
 * Class Model (Abstract Base Model)
 *
 * Kelas dasar untuk semua model yang menyediakan operasi CRUD generik.
 * Menerapkan konsep OOP: encapsulation, inheritance, dan abstraction.
 * Menggunakan PDO dengan prepared statements untuk keamanan.
 *
 * Pola desain: Active Record sederhana
 *
 * @package RestaurantBooking\Models
 * @abstract
 */
abstract class Model
{
    /** @var PDO Instance koneksi database */
    protected PDO $db;

    /** @var string Nama tabel (harus di-override oleh subclass) */
    protected string $table = '';

    /** @var string Primary key tabel */
    protected string $primaryKey = 'id';

    /**
     * Constructor: menerima koneksi PDO.
     *
     * @param PDO $db Instance koneksi database
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Mengambil semua data dari tabel.
     *
     * @param string $orderBy Kolom untuk pengurutan
     * @param string $direction Arah pengurutan (ASC/DESC)
     * @return array Semua baris data
     */
    public function getAll(string $orderBy = 'id', string $direction = 'ASC'): array
    {
        // Whitelist arah pengurutan untuk keamanan
        $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
        $sql = "SELECT * FROM `{$this->table}` ORDER BY `{$orderBy}` {$direction}";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Mengambil satu baris data berdasarkan primary key.
     *
     * @param int $id Nilai primary key
     * @return array|false Data baris atau false jika tidak ditemukan
     */
    public function getById(int $id): array|false
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Menyisipkan data baru ke tabel.
     *
     * @param array $data Array asosiatif [kolom => nilai]
     * @return int ID baris yang baru disisipkan
     */
    public function create(array $data): int
    {
        $columns = implode('`, `', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO `{$this->table}` (`{$columns}`) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Memperbarui data berdasarkan primary key.
     *
     * @param int   $id   Nilai primary key
     * @param array $data Array asosiatif [kolom => nilai]
     * @return bool True jika berhasil
     */
    public function update(int $id, array $data): bool
    {
        $setParts = [];
        foreach (array_keys($data) as $column) {
            $setParts[] = "`{$column}` = :{$column}";
        }
        $setClause = implode(', ', $setParts);
        $sql = "UPDATE `{$this->table}` SET {$setClause} WHERE `{$this->primaryKey}` = :id";
        $data['id'] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    /**
     * Menghapus data berdasarkan primary key.
     *
     * @param int $id Nilai primary key
     * @return bool True jika berhasil
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM `{$this->table}` WHERE `{$this->primaryKey}` = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Menghitung jumlah total baris dalam tabel.
     *
     * @param string|null $where Kondisi WHERE opsional
     * @param array       $params Parameter untuk prepared statement
     * @return int Jumlah baris
     */
    public function count(?string $where = null, array $params = []): int
    {
        $sql = "SELECT COUNT(*) FROM `{$this->table}`";
        if ($where) {
            $sql .= " WHERE {$where}";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Mengambil data dengan kondisi WHERE.
     *
     * @param string $where  Kondisi WHERE
     * @param array  $params Parameter untuk prepared statement
     * @param string $orderBy Kolom pengurutan
     * @param string $direction Arah pengurutan
     * @return array Baris data yang cocok
     */
    public function getWhere(string $where, array $params = [], string $orderBy = 'id', string $direction = 'ASC'): array
    {
        $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
        $sql = "SELECT * FROM `{$this->table}` WHERE {$where} ORDER BY `{$orderBy}` {$direction}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
