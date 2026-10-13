<?php
$pageTitle = 'Data Menu';
$currentPage = 'menus';
ob_start();
?>

<div class="page-header">
    <h1><i class="bi bi-book me-2"></i>Data Menu</h1>
    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>/menus/categories" class="btn btn-outline-secondary">
            <i class="bi bi-tags me-1"></i>Kategori
        </a>
        <a href="<?= BASE_URL ?>/menus/create" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah Menu
        </a>
    </div>
</div>

<div class="card table-card">
    <div class="card-body p-0">
        <?php if (!empty($menus)): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Menu</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($menus as $i => $m): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= e($m['name']) ?></td>
                                <td>
                                    <span class="badge bg-secondary"><?= e($m['category_name'] ?? '-') ?></span>
                                </td>
                                <td><?= formatRupiah($m['price']) ?></td>
                                <td>
                                    <?php if ($m['is_available']): ?>
                                        <span class="badge bg-success">Tersedia</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Tidak Tersedia</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= BASE_URL ?>/menus/edit/<?= $m['id'] ?>"
                                       class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="<?= BASE_URL ?>/menus/delete/<?= $m['id'] ?>"
                                          method="POST" class="d-inline form-delete"
                                          data-confirm="Hapus menu <?= e($m['name']) ?>?">
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
                <i class="bi bi-book"></i>
                <p>Belum ada data menu.</p>
                <a href="<?= BASE_URL ?>/menus/create" class="btn btn-primary btn-sm">Tambah Menu</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>
