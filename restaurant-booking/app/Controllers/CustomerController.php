<?php
require_once ROOT_PATH . '/app/Models/Customer.php';
require_once ROOT_PATH . '/app/Services/ValidationService.php';

/**
 * Class CustomerController
 *
 * Menangani CRUD pelanggan: list, create, store, edit, update, delete.
 * Menerapkan validasi input, CSRF protection, dan escaping output.
 *
 * @package RestaurantBooking\Controllers
 */
class CustomerController
{
    /** @var Customer Model pelanggan */
    private Customer $model;

    /** @var ValidationService Layanan validasi */
    private ValidationService $validator;

    public function __construct(PDO $db)
    {
        $this->model     = new Customer($db);
        $this->validator = new ValidationService();
    }

    /**
     * Menampilkan daftar pelanggan.
     */
    public function index(): void
    {
        $customers = $this->model->getAllSorted();
        require VIEW_PATH . '/customers/index.php';
    }

    /**
     * Menampilkan form tambah pelanggan.
     */
    public function create(): void
    {
        $customer = ['name' => '', 'phone' => '', 'email' => ''];
        $errors = [];
        require VIEW_PATH . '/customers/form.php';
    }

    /**
     * Menyimpan pelanggan baru.
     * Memvalidasi CSRF token dan data input.
     */
    public function store(): void
    {
        // Validasi CSRF
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            setFlash('danger', 'Token keamanan tidak valid. Silakan coba lagi.');
            redirect('/customers/create');
            return;
        }

        $data = [
            'name'  => trim($_POST['name'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
        ];

        // Validasi input
        if (!$this->validator->validateCustomer($data)) {
            $customer = $data;
            $errors = $this->validator->getErrors();
            require VIEW_PATH . '/customers/form.php';
            return;
        }

        // Cek duplikat telepon
        if ($this->model->phoneExists($data['phone'])) {
            $customer = $data;
            $errors = ['phone' => 'Nomor telepon sudah terdaftar.'];
            require VIEW_PATH . '/customers/form.php';
            return;
        }

        // Simpan data
        $this->model->create($data);
        regenerateCsrfToken();
        setFlash('success', 'Pelanggan berhasil ditambahkan.');
        redirect('/customers');
    }

    /**
     * Menampilkan form edit pelanggan.
     *
     * @param int $id ID pelanggan
     */
    public function edit(int $id): void
    {
        $customer = $this->model->getById($id);
        if (!$customer) {
            setFlash('danger', 'Pelanggan tidak ditemukan.');
            redirect('/customers');
            return;
        }
        $errors = [];
        require VIEW_PATH . '/customers/form.php';
    }

    /**
     * Memperbarui data pelanggan.
     *
     * @param int $id ID pelanggan
     */
    public function update(int $id): void
    {
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            setFlash('danger', 'Token keamanan tidak valid.');
            redirect("/customers/edit/{$id}");
            return;
        }

        $data = [
            'name'  => trim($_POST['name'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
        ];

        if (!$this->validator->validateCustomer($data)) {
            $customer = array_merge($data, ['id' => $id]);
            $errors = $this->validator->getErrors();
            require VIEW_PATH . '/customers/form.php';
            return;
        }

        // Cek duplikat telepon (kecualikan diri sendiri)
        if ($this->model->phoneExists($data['phone'], $id)) {
            $customer = array_merge($data, ['id' => $id]);
            $errors = ['phone' => 'Nomor telepon sudah digunakan pelanggan lain.'];
            require VIEW_PATH . '/customers/form.php';
            return;
        }

        $this->model->update($id, $data);
        regenerateCsrfToken();
        setFlash('success', 'Data pelanggan berhasil diperbarui.');
        redirect('/customers');
    }

    /**
     * Menghapus pelanggan.
     *
     * @param int $id ID pelanggan
     */
    public function delete(int $id): void
    {
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            setFlash('danger', 'Token keamanan tidak valid.');
            redirect('/customers');
            return;
        }

        try {
            $this->model->delete($id);
            setFlash('success', 'Pelanggan berhasil dihapus.');
        } catch (PDOException $e) {
            // Foreign key constraint — pelanggan memiliki pemesanan
            setFlash('danger', 'Pelanggan tidak dapat dihapus karena memiliki data pemesanan.');
        }
        regenerateCsrfToken();
        redirect('/customers');
    }
}
