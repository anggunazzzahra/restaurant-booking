<?php
$pageTitle = 'Data Pelanggan';
$currentPage = 'customers';
ob_start();
?>

<div class="page-header">
    <h1><i class="bi bi-people me-2"></i>Data Pelanggan</h1>
    <a href="<?= BASE_URL ?>/customers/create" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Tambah Pelanggan
    </a>
</div>

<div class="card table-card">
    <div class="card-body p-0">
        <?php if (!empty($customers)): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Telepon</th>
                            <th>Email</th>
                            <th>Terdaftar</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $i => $c): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= e($c['name']) ?></td>
                                <td><?= e($c['phone']) ?></td>
                                <td><?= e($c['email'] ?: '-') ?></td>
                                <td><?= date('d/m/Y', strtotime($c['created_at'])) ?></td>
                                <td class="text-center">
                                    <a href="<?= BASE_URL ?>/customers/edit/<?= $c['id'] ?>"
                                       class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="<?= BASE_URL ?>/customers/delete/<?= $c['id'] ?>"
                                          method="POST" class="d-inline form-delete"
                                          data-confirm="Hapus pelanggan <?= e($c['name']) ?>?">
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
                <i class="bi bi-people"></i>
                <p>Belum ada data pelanggan.</p>
                <a href="<?= BASE_URL ?>/customers/create" class="btn btn-primary btn-sm">Tambah Pelanggan</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>
