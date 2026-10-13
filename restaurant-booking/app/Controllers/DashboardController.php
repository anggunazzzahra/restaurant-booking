<?php
require_once ROOT_PATH . '/app/Models/Customer.php';
require_once ROOT_PATH . '/app/Models/RestaurantTable.php';
require_once ROOT_PATH . '/app/Models/Reservation.php';

/**
 * Class DashboardController
 *
 * Menangani halaman dashboard utama.
 * Menampilkan ringkasan data dan pemesanan terbaru.
 *
 * @package RestaurantBooking\Controllers
 */
class DashboardController
{
    /** @var PDO Koneksi database */
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Menampilkan halaman dashboard.
     *
     * Data yang ditampilkan:
     * - Jumlah pelanggan
     * - Jumlah meja
     * - Jumlah pemesanan
     * - Total pendapatan (dari pesanan selesai)
     * - Daftar pemesanan terbaru (5 terakhir)
     */
    public function index(): void
    {
        $customerModel    = new Customer($this->db);
        $tableModel       = new RestaurantTable($this->db);
        $reservationModel = new Reservation($this->db);

        // Ringkasan untuk kartu statistik
        $totalCustomers    = $customerModel->count();
        $totalTables       = $tableModel->count();
        $totalReservations = $reservationModel->count();
        $totalRevenue      = $reservationModel->getTotalRevenue();

        // Pemesanan terbaru
        $recentReservations = $reservationModel->getRecent(5);

        // Muat view
        require VIEW_PATH . '/dashboard/index.php';
    }
}
