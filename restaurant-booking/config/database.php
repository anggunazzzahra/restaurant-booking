<?php
/**
 * Konfigurasi koneksi database menggunakan PDO.
 *
 * File ini membaca pengaturan dari file .env di root proyek,
 * lalu membuat koneksi PDO singleton ke MySQL/MariaDB.
 *
 * @package RestaurantBooking\Config
 */

// Muat variabel dari file .env
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Abaikan komentar
        if (str_starts_with(trim($line), '#')) {
            continue;
        }
        if (str_contains($line, '=')) {
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, " \t\n\r\0\x0B\"'");
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

/**
 * Class Database
 *
 * Mengelola koneksi PDO ke database MySQL menggunakan pola Singleton.
 * Menerapkan prepared statements untuk mencegah SQL injection.
 */
class Database
{
    /** @var PDO|null Instance koneksi PDO */
    private static ?PDO $instance = null;

    /**
     * Mendapatkan instance koneksi PDO (Singleton).
     *
     * Konfigurasi PDO:
     * - ERRMODE_EXCEPTION: melempar exception saat error query
     * - FETCH_ASSOC: hasil query dalam bentuk array asosiatif
     * - EMULATE_PREPARES false: gunakan prepared statement asli MySQL
     *
     * @return PDO Instance koneksi database
     * @throws PDOException Jika koneksi gagal
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $host    = $_ENV['DB_HOST'] ?? 'localhost';
            $port    = $_ENV['DB_PORT'] ?? '3306';
            $dbname  = $_ENV['DB_NAME'] ?? 'restaurant_booking';
            $user    = $_ENV['DB_USER'] ?? 'root';
            $pass    = $_ENV['DB_PASS'] ?? '';
            $charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";

            try {
                self::$instance = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                // Log error dan tampilkan pesan umum
                error_log("Database connection error: " . $e->getMessage());
                throw new PDOException("Gagal terhubung ke database. Periksa konfigurasi.");
            }
        }
        return self::$instance;
    }

    /**
     * Mencegah pembuatan instance melalui constructor.
     */
    private function __construct() {}

    /**
     * Mencegah kloning instance.
     */
    private function __clone() {}
}
