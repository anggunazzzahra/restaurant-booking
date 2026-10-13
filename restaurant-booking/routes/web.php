<?php
/**
 * Router Sederhana
 *
 * Menerapkan pemrograman terstruktur: percabangan (if-elseif),
 * pattern matching dengan regex, dan pemisahan routing dari controller.
 *
 * Format URL: /controller/action/id
 * Contoh: /customers/edit/5
 *
 * @package RestaurantBooking\Routes
 */

// Muat konfigurasi aplikasi
require_once __DIR__ . '/../config/app.php';

// Dapatkan koneksi database
$db = Database::getInstance();

// Parse URL request
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$basePath = parse_url(BASE_URL, PHP_URL_PATH) ?? '';
$path = str_replace($basePath, '', parse_url($requestUri, PHP_URL_PATH));
$path = '/' . trim($path, '/');

$method = $_SERVER['REQUEST_METHOD'];

// Muat controller yang dibutuhkan
require_once ROOT_PATH . '/app/Controllers/DashboardController.php';
require_once ROOT_PATH . '/app/Controllers/CustomerController.php';
require_once ROOT_PATH . '/app/Controllers/TableController.php';
require_once ROOT_PATH . '/app/Controllers/MenuController.php';
require_once ROOT_PATH . '/app/Controllers/ReservationController.php';
require_once ROOT_PATH . '/app/Controllers/ReportController.php';

/**
 * Routing dengan pattern matching.
 *
 * Menggunakan percabangan terstruktur (if-elseif) dan regex
 * untuk mencocokkan URL dengan controller dan action.
 */

// ============================================================
// DASHBOARD
// ============================================================
if ($path === '/' || $path === '/dashboard') {
    $controller = new DashboardController($db);
    $controller->index();
}

// ============================================================
// PELANGGAN
// ============================================================
elseif ($path === '/customers' && $method === 'GET') {
    $controller = new CustomerController($db);
    $controller->index();
}
elseif ($path === '/customers/create' && $method === 'GET') {
    $controller = new CustomerController($db);
    $controller->create();
}
elseif ($path === '/customers/store' && $method === 'POST') {
    $controller = new CustomerController($db);
    $controller->store();
}
elseif (preg_match('#^/customers/edit/(\d+)$#', $path, $matches) && $method === 'GET') {
    $controller = new CustomerController($db);
    $controller->edit((int)$matches[1]);
}
elseif (preg_match('#^/customers/update/(\d+)$#', $path, $matches) && $method === 'POST') {
    $controller = new CustomerController($db);
    $controller->update((int)$matches[1]);
}
elseif (preg_match('#^/customers/delete/(\d+)$#', $path, $matches) && $method === 'POST') {
    $controller = new CustomerController($db);
    $controller->delete((int)$matches[1]);
}

// ============================================================
// MEJA RESTORAN
// ============================================================
elseif ($path === '/tables' && $method === 'GET') {
    $controller = new TableController($db);
    $controller->index();
}
elseif ($path === '/tables/create' && $method === 'GET') {
    $controller = new TableController($db);
    $controller->create();
}
elseif ($path === '/tables/store' && $method === 'POST') {
    $controller = new TableController($db);
    $controller->store();
}
elseif (preg_match('#^/tables/edit/(\d+)$#', $path, $matches) && $method === 'GET') {
    $controller = new TableController($db);
    $controller->edit((int)$matches[1]);
}
elseif (preg_match('#^/tables/update/(\d+)$#', $path, $matches) && $method === 'POST') {
    $controller = new TableController($db);
    $controller->update((int)$matches[1]);
}
elseif (preg_match('#^/tables/delete/(\d+)$#', $path, $matches) && $method === 'POST') {
    $controller = new TableController($db);
    $controller->delete((int)$matches[1]);
}

// ============================================================
// MENU
// ============================================================
elseif ($path === '/menus' && $method === 'GET') {
    $controller = new MenuController($db);
    $controller->index();
}
elseif ($path === '/menus/create' && $method === 'GET') {
    $controller = new MenuController($db);
    $controller->create();
}
elseif ($path === '/menus/store' && $method === 'POST') {
    $controller = new MenuController($db);
    $controller->store();
}
elseif (preg_match('#^/menus/edit/(\d+)$#', $path, $matches) && $method === 'GET') {
    $controller = new MenuController($db);
    $controller->edit((int)$matches[1]);
}
elseif (preg_match('#^/menus/update/(\d+)$#', $path, $matches) && $method === 'POST') {
    $controller = new MenuController($db);
    $controller->update((int)$matches[1]);
}
elseif (preg_match('#^/menus/delete/(\d+)$#', $path, $matches) && $method === 'POST') {
    $controller = new MenuController($db);
    $controller->delete((int)$matches[1]);
}
// Kategori Menu
elseif ($path === '/menus/categories' && $method === 'GET') {
    $controller = new MenuController($db);
    $controller->categories();
}
elseif ($path === '/menus/categories/store' && $method === 'POST') {
    $controller = new MenuController($db);
    $controller->storeCategory();
}
elseif (preg_match('#^/menus/categories/delete/(\d+)$#', $path, $matches) && $method === 'POST') {
    $controller = new MenuController($db);
    $controller->deleteCategory((int)$matches[1]);
}

// ============================================================
// PEMESANAN
// ============================================================
elseif ($path === '/reservations' && $method === 'GET') {
    $controller = new ReservationController($db);
    $controller->index();
}
elseif ($path === '/reservations/create' && $method === 'GET') {
    $controller = new ReservationController($db);
    $controller->create();
}
elseif ($path === '/reservations/store' && $method === 'POST') {
    $controller = new ReservationController($db);
    $controller->store();
}
elseif (preg_match('#^/reservations/show/(\d+)$#', $path, $matches) && $method === 'GET') {
    $controller = new ReservationController($db);
    $controller->show((int)$matches[1]);
}
elseif (preg_match('#^/reservations/edit/(\d+)$#', $path, $matches) && $method === 'GET') {
    $controller = new ReservationController($db);
    $controller->edit((int)$matches[1]);
}
elseif (preg_match('#^/reservations/update/(\d+)$#', $path, $matches) && $method === 'POST') {
    $controller = new ReservationController($db);
    $controller->update((int)$matches[1]);
}
elseif (preg_match('#^/reservations/status/(\d+)$#', $path, $matches) && $method === 'POST') {
    $controller = new ReservationController($db);
    $controller->updateStatus((int)$matches[1]);
}
elseif (preg_match('#^/reservations/delete/(\d+)$#', $path, $matches) && $method === 'POST') {
    $controller = new ReservationController($db);
    $controller->delete((int)$matches[1]);
}

// ============================================================
// LAPORAN
// ============================================================
elseif ($path === '/reports' && $method === 'GET') {
    $controller = new ReportController($db);
    $controller->index();
}
elseif ($path === '/reports/print' && $method === 'GET') {
    $controller = new ReportController($db);
    $controller->print();
}

// ============================================================
// 404 NOT FOUND
// ============================================================
else {
    http_response_code(404);
    echo '<!DOCTYPE html><html><head><title>404</title></head>';
    echo '<body style="text-align:center;padding:50px;font-family:sans-serif;">';
    echo '<h1>404 - Halaman Tidak Ditemukan</h1>';
    echo '<p>Halaman yang Anda cari tidak tersedia.</p>';
    echo '<a href="' . BASE_URL . '/">Kembali ke Dashboard</a>';
    echo '</body></html>';
}
