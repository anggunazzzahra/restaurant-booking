<?php
$isEdit = isset($menu['id']);
$pageTitle = $isEdit ? 'Edit Menu' : 'Tambah Menu';
$currentPage = 'menus';
ob_start();
?>

<div class="page-header">
    <h1><i class="bi bi-<?= $isEdit ? 'pencil' : 'plus-lg' ?> me-2"></i><?= $pageTitle ?></h1>
    <a href="<?= BASE_URL ?>/menus" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card form-card">
            <div class="card-header">
                <i class="bi bi-book me-2"></i>Formulir Menu
            </div>
            <div class="card-body">
                <form action="<?= BASE_URL ?>/menus/<?= $isEdit ? "update/{$menu['id']}" : 'store' ?>"
                      method="POST" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= e(generateCsrfToken()) ?>">

                    <!-- Nama Menu -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Menu <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                               id="name" name="name" value="<?= e($menu['name'] ?? '') ?>"
                               maxlength="100" required autofocus>
                        <?php if (isset($errors['name'])): ?>
                            <div class="invalid-feedback"><?= e($errors['name']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Kategori -->
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select class="form-select <?= isset($errors['category_id']) ? 'is-invalid' : '' ?>"
                                id="category_id" name="category_id" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"
                                    <?= ((int)($menu['category_id'] ?? 0)) === (int)$cat['id'] ? 'selected' : '' ?>>
                                    <?= e($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['category_id'])): ?>
                            <div class="invalid-feedback"><?= e($errors['category_id']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Harga -->
                    <div class="mb-3">
                        <label for="price" class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control input-price <?= isset($errors['price']) ? 'is-invalid' : '' ?>"
                               id="price" name="price" value="<?= e($menu['price'] ?? '') ?>"
                               min="0" step="500" required data-preview="price-preview">
                        <?php if (isset($errors['price'])): ?>
                            <div class="invalid-feedback"><?= e($errors['price']) ?></div>
                        <?php endif; ?>
                        <div class="form-text">
                            Preview: <strong id="price-preview"><?= formatRupiah($menu['price'] ?? 0) ?></strong>
                        </div>
                    </div>

                    <!-- Ketersediaan -->
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_available"
                                   name="is_available" value="1"
                                <?= ($menu['is_available'] ?? 1) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_available">Tersedia untuk dipesan</label>
                        </div>
                    </div>

                    <hr>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i><?= $isEdit ? 'Perbarui' : 'Simpan' ?>
                        </button>
                        <a href="<?= BASE_URL ?>/menus" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>
