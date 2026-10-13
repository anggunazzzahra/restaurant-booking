<?php
$pageTitle = 'Data Meja Restoran';
$currentPage = 'tables';
ob_start();
?>

<div class="page-header">
    <h1><i class="bi bi-grid-3x3 me-2"></i>Data Meja Restoran</h1>
    <a href="<?= BASE_URL ?>/tables/create" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Tambah Meja
    </a>
</div>

<div class="card table-card">
    <div class="card-body p-0">
        <?php if (!empty($tables)): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nomor Meja</th>
                            <th>Kapasitas</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tables as $i => $t): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><strong><?= e($t['table_number']) ?></strong></td>
                                <td><?= (int)$t['capacity'] ?> orang</td>
                                <td>
                                    <?php
                                    $statusBadge = [
                                        'tersedia'   => 'success',
                                        'dipesan'    => 'warning',
                                        'digunakan'  => 'danger',
                                    ];
                                    $badge = $statusBadge[$t['status']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $badge ?>"><?= e(ucfirst($t['status'])) ?></span>
                                </td>
                                <td class="text-center">
                                    <a href="<?= BASE_URL ?>/tables/edit/<?= $t['id'] ?>"
                                       class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="<?= BASE_URL ?>/tables/delete/<?= $t['id'] ?>"
                                          method="POST" class="d-inline form-delete"
                                          data-confirm="Hapus meja <?= e($t['table_number']) ?>?">
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
                <i class="bi bi-grid-3x3"></i>
                <p>Belum ada data meja.</p>
                <a href="<?= BASE_URL ?>/tables/create" class="btn btn-primary btn-sm">Tambah Meja</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>
