<?php
$pageTitle = 'Detail Pemesanan #' . ($reservation['id'] ?? '');
$currentPage = 'reservations';
ob_start();
?>

<div class="page-header">
    <h1><i class="bi bi-eye me-2"></i>Detail Pemesanan</h1>
    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>/reservations/edit/<?= $reservation['id'] ?>" class="btn btn-warning">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
        <a href="<?= BASE_URL ?>/reservations" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Info Utama -->
    <div class="col-md-6">
        <div class="card form-card">
            <div class="card-header">
                <i class="bi bi-info-circle me-2"></i>Informasi Pemesanan
            </div>
            <div class="card-body">
                <div class="detail-row">
                    <div class="detail-label">ID Pemesanan</div>
                    <div class="detail-value">#<?= $reservation['id'] ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Pelanggan</div>
                    <div class="detail-value"><?= e($reservation['customer_name'] ?? '-') ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Nomor Meja</div>
                    <div class="detail-value"><?= e($reservation['table_number'] ?? '-') ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Tanggal</div>
                    <div class="detail-value"><?= date('d F Y', strtotime($reservation['reservation_date'])) ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Waktu</div>
                    <div class="detail-value"><?= date('H:i', strtotime($reservation['reservation_time'])) ?> WIB</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Jumlah Tamu</div>
                    <div class="detail-value"><?= (int)$reservation['guest_count'] ?> orang</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Status</div>
                    <div class="detail-value">
                        <?php
                        $badge = Reservation::STATUS_BADGES[$reservation['status']] ?? 'secondary';
                        $label = Reservation::STATUS_LABELS[$reservation['status']] ?? $reservation['status'];
                        ?>
                        <span class="badge bg-<?= $badge ?> fs-6"><?= e($label) ?></span>
                    </div>
                </div>
                <?php if (!empty($reservation['notes'])): ?>
                    <div class="detail-row">
                        <div class="detail-label">Catatan</div>
                        <div class="detail-value"><?= e($reservation['notes']) ?></div>
                    </div>
                <?php endif; ?>
                <div class="detail-row">
                    <div class="detail-label">Dibuat</div>
                    <div class="detail-value"><?= date('d/m/Y H:i', strtotime($reservation['created_at'])) ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Item -->
    <div class="col-md-6">
        <div class="card table-card">
            <div class="card-header">
                <i class="bi bi-cart me-2"></i>Item Pesanan
            </div>
            <div class="card-body p-0">
                <?php if (!empty($reservation['items'])): ?>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Menu</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Harga</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reservation['items'] as $item): ?>
                                    <tr>
                                        <td><?= e($item['menu_name'] ?? $item['name'] ?? '-') ?></td>
                                        <td class="text-center"><?= (int)$item['quantity'] ?></td>
                                        <td class="text-end"><?= formatRupiah($item['price']) ?></td>
                                        <td class="text-end"><?= formatRupiah($item['subtotal']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total</strong></td>
                                    <td class="text-end">
                                        <strong class="text-primary fs-5">
                                            <?= formatRupiah($reservation['total_amount'] ?? 0) ?>
                                        </strong>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <p>Tidak ada item pesanan.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Update Status Cepat -->
        <div class="card form-card mt-3">
            <div class="card-header">
                <i class="bi bi-arrow-repeat me-2"></i>Ubah Status
            </div>
            <div class="card-body">
                <form action="<?= BASE_URL ?>/reservations/status/<?= $reservation['id'] ?>"
                      method="POST" class="d-flex gap-2">
                    <input type="hidden" name="csrf_token" value="<?= e(generateCsrfToken()) ?>">
                    <select name="status" class="form-select">
                        <?php foreach (Reservation::STATUS_LABELS as $key => $lbl): ?>
                            <option value="<?= e($key) ?>" <?= $reservation['status'] === $key ? 'selected' : '' ?>>
                                <?= e($lbl) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-primary" style="white-space:nowrap;">
                        <i class="bi bi-check-lg"></i> Ubah
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>
