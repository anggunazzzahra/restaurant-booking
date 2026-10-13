<?php
/**
 * Konfigurasi umum aplikasi.
 *
 * Mengatur session, timezone, error reporting,
 * dan konstanta aplikasi.
 *
 * @package RestaurantBooking\Config
 */

// Muat konfigurasi database (termasuk parser .env)
require_once __DIR__ . '/database.php';

// Pengaturan timezone
date_default_timezone_set('Asia/Jakarta');

// Error reporting berdasarkan mode debug
$debug = ($_ENV['APP_DEBUG'] ?? 'false') === 'true';
if ($debug) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Mulai session jika belum aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Konstanta aplikasi
define('APP_NAME', $_ENV['APP_NAME'] ?? 'Sistem Pemesanan Restoran');
define('BASE_URL', $_ENV['APP_URL'] ?? 'http://localhost/restaurant-booking/public');
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('VIEW_PATH', APP_PATH . '/Views');

/**
 * Menghasilkan token CSRF dan menyimpannya di session.
 *
 * @return string Token CSRF
 */
function generateCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Memvalidasi token CSRF dari request POST.
 *
 * @param string $token Token yang dikirim dari form
 * @return bool True jika token valid
 */
function validateCsrfToken(string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Membersihkan dan menghasilkan token CSRF baru.
 */
function regenerateCsrfToken(): void
{
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/**
 * Escaping output HTML untuk mencegah XSS.
 *
 * @param string|null $string String yang akan di-escape
 * @return string String yang aman untuk ditampilkan di HTML
 */
function e(?string $string): string
{
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect ke URL tertentu.
 *
 * @param string $path Path relatif dari BASE_URL
 */
function redirect(string $path): void
{
    header("Location: " . BASE_URL . $path);
    exit;
}

/**
 * Menyimpan pesan flash ke session.
 *
 * @param string $type Tipe pesan (success, danger, warning, info)
 * @param string $message Isi pesan
 */
function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Mengambil dan menghapus pesan flash dari session.
 *
 * @return array|null Data flash message atau null
 */
function getFlash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

/**
 * Format angka ke Rupiah.
 *
 * @param float|int|string|null $amount Jumlah yang akan diformat (string numerik dari DB, kosong, atau null dianggap angka/0)
 * @return string Format Rupiah (contoh: Rp 150.000)
 */
function formatRupiah(float|int|string|null $amount): string
{
    return 'Rp ' . number_format((float)$amount, 0, ',', '.');
}
