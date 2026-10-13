<?php
require_once ROOT_PATH . '/app/Models/Reservation.php';

/**
 * Class ReportController
 *
 * Menangani laporan pemesanan berdasarkan rentang tanggal.
 * Menyediakan tampilan laporan dan versi cetak.
 *
 * @package RestaurantBooking\Controllers
 */
class ReportController
{
    private Reservation $model;

    public function __construct(PDO $db)
    {
        $this->model = new Reservation($db);
    }

    /**
     * Menampilkan halaman laporan.
     *
     * Menerima parameter GET: start_date dan end_date.
     * Default: 30 hari terakhir.
     */
    public function index(): void
    {
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate   = $_GET['end_date'] ?? date('Y-m-d');

        // Validasi format tanggal
        if (!$this->isValidDate($startDate)) {
            $startDate = date('Y-m-d', strtotime('-30 days'));
        }
        if (!$this->isValidDate($endDate)) {
            $endDate = date('Y-m-d');
        }

        // Pastikan start <= end
        if ($startDate > $endDate) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        $reservations = $this->model->getReport($startDate, $endDate);
        $summary      = $this->model->getReportSummary($startDate, $endDate);
        $menuSold     = $this->model->getMenuSoldReport($startDate, $endDate);

        require VIEW_PATH . '/reports/index.php';
    }

    /**
     * Menampilkan versi cetak laporan.
     */
    public function print(): void
    {
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate   = $_GET['end_date'] ?? date('Y-m-d');

        if (!$this->isValidDate($startDate)) {
            $startDate = date('Y-m-d', strtotime('-30 days'));
        }
        if (!$this->isValidDate($endDate)) {
            $endDate = date('Y-m-d');
        }
        if ($startDate > $endDate) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        $reservations = $this->model->getReport($startDate, $endDate);
        $summary      = $this->model->getReportSummary($startDate, $endDate);
        $menuSold     = $this->model->getMenuSoldReport($startDate, $endDate);

        require VIEW_PATH . '/reports/print.php';
    }

    /**
     * Memvalidasi format tanggal.
     */
    private function isValidDate(string $date): bool
    {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}
