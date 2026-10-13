<?php
require_once ROOT_PATH . '/app/Models/Reservation.php';
require_once ROOT_PATH . '/app/Models/ReservationDetail.php';
require_once ROOT_PATH . '/app/Models/Customer.php';
require_once ROOT_PATH . '/app/Models/RestaurantTable.php';
require_once ROOT_PATH . '/app/Models/Menu.php';
require_once ROOT_PATH . '/app/Services/ReservationService.php';

/**
 * Class ReservationController
 *
 * Menangani pengelolaan pemesanan: list, create, detail, status update.
 * Mendelegasikan logika bisnis ke ReservationService.
 *
 * @package RestaurantBooking\Controllers
 */
class ReservationController
{
    private PDO $db;
    private Reservation $model;
    private ReservationService $service;

    public function __construct(PDO $db)
    {
        $this->db      = $db;
        $this->model   = new Reservation($db);
        $this->service = new ReservationService($db);
    }

    /**
     * Menampilkan daftar pemesanan dengan filter status opsional.
     */
    public function index(): void
    {
        $statusFilter = $_GET['status'] ?? null;
        $reservations = $this->model->getAllWithDetails($statusFilter);
        require VIEW_PATH . '/reservations/index.php';
    }

    /**
     * Menampilkan form tambah pemesanan.
     */
    public function create(): void
    {
        $customerModel = new Customer($this->db);
        $tableModel    = new RestaurantTable($this->db);
        $menuModel     = new Menu($this->db);

        $customers = $customerModel->getAllSorted();
        $tables    = $tableModel->getAllSorted();
        $menus     = $menuModel->getAvailable();
        $errors    = [];
        $reservation = [
            'customer_id'      => '',
            'table_id'         => '',
            'reservation_date' => date('Y-m-d'),
            'reservation_time' => '',
            'guest_count'      => 1,
            'status'           => 'menunggu',
            'notes'            => '',
        ];
        $menuItems = [];

        require VIEW_PATH . '/reservations/form.php';
    }

    /**
     * Menyimpan pemesanan baru.
     * Menggunakan ReservationService dengan transaksi database.
     */
    public function store(): void
    {
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            setFlash('danger', 'Token keamanan tidak valid.');
            redirect('/reservations/create');
            return;
        }

        // Susun data menu items dari form
        $menuItems = [];
        $menuIds = $_POST['menu_id'] ?? [];
        $quantities = $_POST['quantity'] ?? [];
        foreach ($menuIds as $index => $menuId) {
            if (!empty($menuId) && !empty($quantities[$index]) && (int)$quantities[$index] > 0) {
                $menuItems[] = [
                    'menu_id'  => (int)$menuId,
                    'quantity' => (int)$quantities[$index],
                ];
            }
        }

        $data = [
            'customer_id'      => $_POST['customer_id'] ?? '',
            'table_id'         => $_POST['table_id'] ?? '',
            'reservation_date' => $_POST['reservation_date'] ?? '',
            'reservation_time' => $_POST['reservation_time'] ?? '',
            'guest_count'      => $_POST['guest_count'] ?? 1,
            'status'           => $_POST['status'] ?? 'menunggu',
            'notes'            => trim($_POST['notes'] ?? ''),
            'menu_items'       => $menuItems,
        ];

        $result = $this->service->createReservation($data);

        if ($result['success']) {
            regenerateCsrfToken();
            setFlash('success', $result['message']);
            redirect('/reservations');
        } else {
            setFlash('danger', $result['message']);
            // Kembali ke form dengan data yang sudah diisi
            $customerModel = new Customer($this->db);
            $tableModel    = new RestaurantTable($this->db);
            $menuModel     = new Menu($this->db);

            $customers   = $customerModel->getAllSorted();
            $tables      = $tableModel->getAllSorted();
            $menus       = $menuModel->getAvailable();
            $errors      = [];
            $reservation = $data;

            require VIEW_PATH . '/reservations/form.php';
        }
    }

    /**
     * Menampilkan detail pemesanan.
     */
    public function show(int $id): void
    {
        $reservation = $this->model->getFullDetail($id);
        if (!$reservation) {
            setFlash('danger', 'Pemesanan tidak ditemukan.');
            redirect('/reservations');
            return;
        }
        require VIEW_PATH . '/reservations/show.php';
    }

    /**
     * Menampilkan form edit pemesanan.
     */
    public function edit(int $id): void
    {
        $reservation = $this->model->getFullDetail($id);
        if (!$reservation) {
            setFlash('danger', 'Pemesanan tidak ditemukan.');
            redirect('/reservations');
            return;
        }

        $customerModel = new Customer($this->db);
        $tableModel    = new RestaurantTable($this->db);
        $menuModel     = new Menu($this->db);

        $customers = $customerModel->getAllSorted();
        $tables    = $tableModel->getAllSorted();
        $menus     = $menuModel->getAvailable();
        $errors    = [];
        $menuItems = $reservation['items'];

        require VIEW_PATH . '/reservations/form.php';
    }

    /**
     * Memperbarui pemesanan.
     */
    public function update(int $id): void
    {
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            setFlash('danger', 'Token keamanan tidak valid.');
            redirect("/reservations/edit/{$id}");
            return;
        }

        $menuItems = [];
        $menuIds = $_POST['menu_id'] ?? [];
        $quantities = $_POST['quantity'] ?? [];
        foreach ($menuIds as $index => $menuId) {
            if (!empty($menuId) && !empty($quantities[$index]) && (int)$quantities[$index] > 0) {
                $menuItems[] = [
                    'menu_id'  => (int)$menuId,
                    'quantity' => (int)$quantities[$index],
                ];
            }
        }

        $data = [
            'customer_id'      => $_POST['customer_id'] ?? '',
            'table_id'         => $_POST['table_id'] ?? '',
            'reservation_date' => $_POST['reservation_date'] ?? '',
            'reservation_time' => $_POST['reservation_time'] ?? '',
            'guest_count'      => $_POST['guest_count'] ?? 1,
            'status'           => $_POST['status'] ?? 'menunggu',
            'notes'            => trim($_POST['notes'] ?? ''),
            'menu_items'       => $menuItems,
        ];

        $result = $this->service->updateReservation($id, $data);

        if ($result['success']) {
            regenerateCsrfToken();
            setFlash('success', $result['message']);
            redirect('/reservations');
        } else {
            setFlash('danger', $result['message']);
            redirect("/reservations/edit/{$id}");
        }
    }

    /**
     * Memperbarui status pemesanan (via AJAX atau form POST).
     */
    public function updateStatus(int $id): void
    {
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            setFlash('danger', 'Token keamanan tidak valid.');
            redirect('/reservations');
            return;
        }

        $status = $_POST['status'] ?? '';
        $result = $this->service->updateStatus($id, $status);

        if ($result['success']) {
            setFlash('success', $result['message']);
        } else {
            setFlash('danger', $result['message']);
        }
        regenerateCsrfToken();
        redirect('/reservations');
    }

    /**
     * Menghapus pemesanan.
     */
    public function delete(int $id): void
    {
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            setFlash('danger', 'Token keamanan tidak valid.');
            redirect('/reservations');
            return;
        }

        try {
            $this->model->delete($id);
            setFlash('success', 'Pemesanan berhasil dihapus.');
        } catch (PDOException $e) {
            setFlash('danger', 'Gagal menghapus pemesanan.');
        }
        regenerateCsrfToken();
        redirect('/reservations');
    }
}
