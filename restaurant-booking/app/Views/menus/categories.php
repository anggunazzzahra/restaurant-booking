<?php
$pageTitle = 'Kategori Menu';
$currentPage = 'categories';
ob_start();
?>

<div class="page-header">
    <h1><i class="bi bi-tags me-2"></i>Kategori Menu</h1>
    <a href="<?= BASE_URL ?>/menus" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali ke Menu
    </a>
</div>

<div class="row g-4">
    <!-- Form Tambah Kategori -->
    <div class="col-lg-4">
        <div class="card form-card">
            <div class="card-header">
                <i class="bi bi-plus-lg me-2"></i>Tambah Kategori
            </div>
            <div class="card-body">
                <form action="<?= BASE_URL ?>/menus/categories/store" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= e(generateCsrfToken()) ?>">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name"
                               maxlength="50" required autofocus>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check-lg me-1"></i>Simpan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Daftar Kategori -->
    <div class="col-lg-8">
        <div class="card table-card">
            <div class="card-header">
                <i class="bi bi-list-ul me-2"></i>Daftar Kategori
            </div>
            <div class="card-body p-0">
                <?php if (!empty($categories)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Kategori</th>
                                    <th>Jumlah Menu</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $i => $cat): ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><?= e($cat['name']) ?></td>
                                        <td>
                                            <span class="badge bg-info"><?= (int)($cat['menu_count'] ?? 0) ?> menu</span>
                                        </td>
                                        <td class="text-center">
                                            <form action="<?= BASE_URL ?>/menus/categories/delete/<?= $cat['id'] ?>"
                                                  method="POST" class="d-inline form-delete"
                                                  data-confirm="Hapus kategori <?= e($cat['name']) ?>?">
                                                <input type="hidden" name="csrf_token" value="<?= e(generateCsrfToken()) ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="bi bi-tags"></i>
                        <p>Belum ada kategori.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>
