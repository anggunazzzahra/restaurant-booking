<?php
$pageTitle = 'Data Pemesanan';
$currentPage = 'reservations';
ob_start();
?>

<div class="page-header">
    <h1><i class="bi bi-calendar-check me-2"></i>Data Pemesanan</h1>
    <a href="<?= BASE_URL ?>/reservations/create" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Buat Pemesanan
    </a>
</div>

<!-- Filter Status -->
<div class="card mb-3">
    <div class="card-body py-2">
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <span class="text-muted me-2"><i class="bi bi-funnel me-1"></i>Filter:</span>
            <a href="<?= BASE_URL ?>/reservations"
               class="btn btn-sm <?= empty($statusFilter) ? 'btn-dark' : 'btn-outline-dark' ?>">
                Semua
            </a>
            <?php foreach (Reservation::STATUS_LABELS as $key => $label): ?>
                <a href="<?= BASE_URL ?>/reservations?status=<?= e($key) ?>"
                   class="btn btn-sm <?= ($statusFilter ?? '') === $key
                       ? 'btn-' . Reservation::STATUS_BADGES[$key]
                       : 'btn-outline-' . Reservation::STATUS_BADGES[$key] ?>">
                    <?= e($label) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="card table-card">
    <div class="card-body p-0">
        <?php if (!empty($reservations)): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Pelanggan</th>
                            <th>Meja</th>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                            <th>Tamu</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservations as $i => $r): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= e($r['customer_name'] ?? '-') ?></td>
                                <td><?= e($r['table_number'] ?? '-') ?></td>
                                <td><?= date('d/m/Y', strtotime($r['reservation_date'])) ?></td>
                                <td><?= date('H:i', strtotime($r['reservation_time'])) ?></td>
                                <td><?= (int)$r['guest_count'] ?></td>
                                <td>
                                    <!-- Form ubah status inline -->
                                    <form action="<?= BASE_URL ?>/reservations/status/<?= $r['id'] ?>"
                                          method="POST" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= e(generateCsrfToken()) ?>">
                                        <select name="status" class="form-select form-select-sm status-select"
                                                style="width:auto;display:inline-block;">
                                            <?php foreach (Reservation::STATUS_LABELS as $key => $label): ?>
                                                <option value="<?= e($key) ?>" <?= $r['status'] === $key ? 'selected' : '' ?>>
                                                    <?= e($label) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </form>
                                </td>
                                <td><?= formatRupiah($r['total_amount'] ?? 0) ?></td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= BASE_URL ?>/reservations/show/<?= $r['id'] ?>"
                                           class="btn btn-outline-info" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?= BASE_URL ?>/reservations/edit/<?= $r['id'] ?>"
                                           class="btn btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="<?= BASE_URL ?>/reservations/delete/<?= $r['id'] ?>"
                                              method="POST" class="d-inline form-delete"
                                              data-confirm="Hapus pemesanan ini?">
                                            <input type="hidden" name="csrf_token" value="<?= e(generateCsrfToken()) ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="bi bi-calendar-x"></i>
                <p>Tidak ada data pemesanan<?= !empty($statusFilter) ? ' dengan status ini' : '' ?>.</p>
                <a href="<?= BASE_URL ?>/reservations/create" class="btn btn-primary btn-sm">Buat Pemesanan</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>
