<?php
/**
 * Bootstrap PHPUnit
 *
 * Memuat konfigurasi dan dependensi yang dibutuhkan untuk testing.
 */

define('ROOT_PATH', dirname(__DIR__));
define('APP_NAME', 'Restaurant Booking Test');
define('BASE_URL', 'http://localhost/restaurant-booking/public');
define('APP_PATH', ROOT_PATH . '/app');
define('VIEW_PATH', APP_PATH . '/Views');

// Fungsi helper yang dibutuhkan untuk testing
function e(string $string): string
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function formatRupiah(float|int|string|null $amount): string
{
    return 'Rp ' . number_format((float)$amount, 0, ',', '.');
}

// Autoload models dan services
require_once ROOT_PATH . '/app/Models/Model.php';
require_once ROOT_PATH . '/app/Models/Customer.php';
require_once ROOT_PATH . '/app/Models/RestaurantTable.php';
require_once ROOT_PATH . '/app/Models/MenuCategory.php';
require_once ROOT_PATH . '/app/Models/Menu.php';
require_once ROOT_PATH . '/app/Models/Reservation.php';
require_once ROOT_PATH . '/app/Models/ReservationDetail.php';
require_once ROOT_PATH . '/app/Services/ValidationService.php';
