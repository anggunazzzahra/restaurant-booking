<?php
require_once ROOT_PATH . '/app/Models/Menu.php';
require_once ROOT_PATH . '/app/Models/MenuCategory.php';
require_once ROOT_PATH . '/app/Services/ValidationService.php';

/**
 * Class MenuController
 *
 * Menangani CRUD menu makanan/minuman dan kategori menu.
 *
 * @package RestaurantBooking\Controllers
 */
class MenuController
{
    private Menu $model;
    private MenuCategory $categoryModel;
    private ValidationService $validator;

    public function __construct(PDO $db)
    {
        $this->model         = new Menu($db);
        $this->categoryModel = new MenuCategory($db);
        $this->validator     = new ValidationService();
    }

    /**
     * Menampilkan daftar menu.
     */
    public function index(): void
    {
        $menus = $this->model->getAllWithCategory();
        require VIEW_PATH . '/menus/index.php';
    }

    /**
     * Menampilkan form tambah menu.
     */
    public function create(): void
    {
        $menu = ['name' => '', 'category_id' => '', 'price' => '', 'is_available' => 1];
        $categories = $this->categoryModel->getAllSorted();
        $errors = [];
        require VIEW_PATH . '/menus/form.php';
    }

    /**
     * Menyimpan menu baru.
     */
    public function store(): void
    {
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            setFlash('danger', 'Token keamanan tidak valid.');
            redirect('/menus/create');
            return;
        }

        $data = [
            'name'         => trim($_POST['name'] ?? ''),
            'category_id'  => (int)($_POST['category_id'] ?? 0),
            'price'        => (float)($_POST['price'] ?? 0),
            'is_available' => isset($_POST['is_available']) ? 1 : 0,
        ];

        if (!$this->validator->validateMenu($data)) {
            $menu = $data;
            $categories = $this->categoryModel->getAllSorted();
            $errors = $this->validator->getErrors();
            require VIEW_PATH . '/menus/form.php';
            return;
        }

        $this->model->create($data);
        regenerateCsrfToken();
        setFlash('success', 'Menu berhasil ditambahkan.');
        redirect('/menus');
    }

    /**
     * Menampilkan form edit menu.
     */
    public function edit(int $id): void
    {
        $menu = $this->model->getById($id);
        if (!$menu) {
            setFlash('danger', 'Menu tidak ditemukan.');
            redirect('/menus');
            return;
        }
        $categories = $this->categoryModel->getAllSorted();
        $errors = [];
        require VIEW_PATH . '/menus/form.php';
    }

    /**
     * Memperbarui data menu.
     */
    public function update(int $id): void
    {
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            setFlash('danger', 'Token keamanan tidak valid.');
            redirect("/menus/edit/{$id}");
            return;
        }

        $data = [
            'name'         => trim($_POST['name'] ?? ''),
            'category_id'  => (int)($_POST['category_id'] ?? 0),
            'price'        => (float)($_POST['price'] ?? 0),
            'is_available' => isset($_POST['is_available']) ? 1 : 0,
        ];

        if (!$this->validator->validateMenu($data)) {
            $menu = array_merge($data, ['id' => $id]);
            $categories = $this->categoryModel->getAllSorted();
            $errors = $this->validator->getErrors();
            require VIEW_PATH . '/menus/form.php';
            return;
        }

        $this->model->update($id, $data);
        regenerateCsrfToken();
        setFlash('success', 'Menu berhasil diperbarui.');
        redirect('/menus');
    }

    /**
     * Menghapus menu.
     */
    public function delete(int $id): void
    {
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            setFlash('danger', 'Token keamanan tidak valid.');
            redirect('/menus');
            return;
        }

        try {
            $this->model->delete($id);
            setFlash('success', 'Menu berhasil dihapus.');
        } catch (PDOException $e) {
            setFlash('danger', 'Menu tidak dapat dihapus karena terdapat dalam pesanan.');
        }
        regenerateCsrfToken();
        redirect('/menus');
    }

    // ====================================================================
    // KATEGORI MENU
    // ====================================================================

    /**
     * Menampilkan daftar kategori.
     */
    public function categories(): void
    {
        $categories = $this->categoryModel->getAllWithMenuCount();
        require VIEW_PATH . '/menus/categories.php';
    }

    /**
     * Menyimpan kategori baru.
     */
    public function storeCategory(): void
    {
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            setFlash('danger', 'Token keamanan tidak valid.');
            redirect('/menus/categories');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            setFlash('danger', 'Nama kategori wajib diisi.');
            redirect('/menus/categories');
            return;
        }

        $this->categoryModel->create(['name' => $name]);
        regenerateCsrfToken();
        setFlash('success', 'Kategori berhasil ditambahkan.');
        redirect('/menus/categories');
    }

    /**
     * Menghapus kategori.
     */
    public function deleteCategory(int $id): void
    {
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            setFlash('danger', 'Token keamanan tidak valid.');
            redirect('/menus/categories');
            return;
        }

        try {
            $this->categoryModel->delete($id);
            setFlash('success', 'Kategori berhasil dihapus.');
        } catch (PDOException $e) {
            setFlash('danger', 'Kategori tidak dapat dihapus karena masih memiliki menu.');
        }
        regenerateCsrfToken();
        redirect('/menus/categories');
    }
}
