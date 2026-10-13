<?php
$isEdit = isset($table['id']);
$pageTitle = $isEdit ? 'Edit Meja' : 'Tambah Meja';
$currentPage = 'tables';
ob_start();
?>

<div class="page-header">
    <h1><i class="bi bi-<?= $isEdit ? 'pencil' : 'plus-lg' ?> me-2"></i><?= $pageTitle ?></h1>
    <a href="<?= BASE_URL ?>/tables" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card form-card">
            <div class="card-header">
                <i class="bi bi-grid-3x3 me-2"></i>Formulir Meja
            </div>
            <div class="card-body">
                <form action="<?= BASE_URL ?>/tables/<?= $isEdit ? "update/{$table['id']}" : 'store' ?>"
                      method="POST" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= e(generateCsrfToken()) ?>">

                    <!-- Nomor Meja -->
                    <div class="mb-3">
                        <label for="table_number" class="form-label">Nomor Meja <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?= isset($errors['table_number']) ? 'is-invalid' : '' ?>"
                               id="table_number" name="table_number"
                               value="<?= e($table['table_number'] ?? '') ?>" required autofocus>
                        <?php if (isset($errors['table_number'])): ?>
                            <div class="invalid-feedback"><?= e($errors['table_number']) ?></div>
                        <?php endif; ?>
                        <div class="form-text">Contoh: M01, M02, VIP-1</div>
                    </div>

                    <!-- Kapasitas -->
                    <div class="mb-3">
                        <label for="capacity" class="form-label">Kapasitas <span class="text-danger">*</span></label>
                        <input type="number" class="form-control <?= isset($errors['capacity']) ? 'is-invalid' : '' ?>"
                               id="capacity" name="capacity"
                               value="<?= (int)($table['capacity'] ?? 4) ?>" min="1" max="50" required>
                        <?php if (isset($errors['capacity'])): ?>
                            <div class="invalid-feedback"><?= e($errors['capacity']) ?></div>
                        <?php endif; ?>
                        <div class="form-text">Jumlah orang (1–50)</div>
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select <?= isset($errors['status']) ? 'is-invalid' : '' ?>"
                                id="status" name="status" required>
                            <?php foreach (RestaurantTable::VALID_STATUSES as $s): ?>
                                <option value="<?= e($s) ?>" <?= ($table['status'] ?? '') === $s ? 'selected' : '' ?>>
                                    <?= e(ucfirst($s)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['status'])): ?>
                            <div class="invalid-feedback"><?= e($errors['status']) ?></div>
                        <?php endif; ?>
                    </div>

                    <hr>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i><?= $isEdit ? 'Perbarui' : 'Simpan' ?>
                        </button>
                        <a href="<?= BASE_URL ?>/tables" class="btn btn-outline-secondary">Batal</a>
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
