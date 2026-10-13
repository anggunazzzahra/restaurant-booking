<?php
require_once ROOT_PATH . '/app/Models/RestaurantTable.php';
require_once ROOT_PATH . '/app/Services/ValidationService.php';

/**
 * Class TableController
 *
 * Menangani CRUD meja restoran.
 *
 * @package RestaurantBooking\Controllers
 */
class TableController
{
    private RestaurantTable $model;
    private ValidationService $validator;

    public function __construct(PDO $db)
    {
        $this->model     = new RestaurantTable($db);
        $this->validator = new ValidationService();
    }

    /**
     * Menampilkan daftar meja.
     */
    public function index(): void
    {
        $tables = $this->model->getAllSorted();
        require VIEW_PATH . '/tables/index.php';
    }

    /**
     * Menampilkan form tambah meja.
     */
    public function create(): void
    {
        $table = ['table_number' => '', 'capacity' => 4, 'status' => 'tersedia'];
        $errors = [];
        require VIEW_PATH . '/tables/form.php';
    }

    /**
     * Menyimpan meja baru.
     */
    public function store(): void
    {
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            setFlash('danger', 'Token keamanan tidak valid.');
            redirect('/tables/create');
            return;
        }

        $data = [
            'table_number' => trim($_POST['table_number'] ?? ''),
            'capacity'     => (int)($_POST['capacity'] ?? 4),
            'status'       => $_POST['status'] ?? 'tersedia',
        ];

        if (!$this->validator->validateTable($data)) {
            $table = $data;
            $errors = $this->validator->getErrors();
            require VIEW_PATH . '/tables/form.php';
            return;
        }

        if ($this->model->tableNumberExists($data['table_number'])) {
            $table = $data;
            $errors = ['table_number' => 'Nomor meja sudah ada.'];
            require VIEW_PATH . '/tables/form.php';
            return;
        }

        $this->model->create($data);
        regenerateCsrfToken();
        setFlash('success', 'Meja berhasil ditambahkan.');
        redirect('/tables');
    }

    /**
     * Menampilkan form edit meja.
     */
    public function edit(int $id): void
    {
        $table = $this->model->getById($id);
        if (!$table) {
            setFlash('danger', 'Meja tidak ditemukan.');
            redirect('/tables');
            return;
        }
        $errors = [];
        require VIEW_PATH . '/tables/form.php';
    }

    /**
     * Memperbarui data meja.
     */
    public function update(int $id): void
    {
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            setFlash('danger', 'Token keamanan tidak valid.');
            redirect("/tables/edit/{$id}");
            return;
        }

        $data = [
            'table_number' => trim($_POST['table_number'] ?? ''),
            'capacity'     => (int)($_POST['capacity'] ?? 4),
            'status'       => $_POST['status'] ?? 'tersedia',
        ];

        if (!$this->validator->validateTable($data)) {
            $table = array_merge($data, ['id' => $id]);
            $errors = $this->validator->getErrors();
            require VIEW_PATH . '/tables/form.php';
            return;
        }

        if ($this->model->tableNumberExists($data['table_number'], $id)) {
            $table = array_merge($data, ['id' => $id]);
            $errors = ['table_number' => 'Nomor meja sudah digunakan.'];
            require VIEW_PATH . '/tables/form.php';
            return;
        }

        $this->model->update($id, $data);
        regenerateCsrfToken();
        setFlash('success', 'Data meja berhasil diperbarui.');
        redirect('/tables');
    }

    /**
     * Menghapus meja.
     */
    public function delete(int $id): void
    {
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            setFlash('danger', 'Token keamanan tidak valid.');
            redirect('/tables');
            return;
        }

        try {
            $this->model->delete($id);
            setFlash('success', 'Meja berhasil dihapus.');
        } catch (PDOException $e) {
            setFlash('danger', 'Meja tidak dapat dihapus karena memiliki data pemesanan.');
        }
        regenerateCsrfToken();
        redirect('/tables');
    }
}
