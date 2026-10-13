<?php
/**
 * View: Dashboard
 * Menampilkan ringkasan statistik dan pemesanan terbaru.
 */
$pageTitle = 'Dashboard';
$currentPage = 'dashboard';

ob_start();
?>

<div class="page-header">
    <h1><i class="bi bi-speedometer2 me-2"></i>Dashboard</h1>
</div>

<!-- Statistik Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-value text-primary"><?= (int)($totalCustomers ?? 0) ?></div>
                        <div class="stat-label">Total Pelanggan</div>
                    </div>
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-value text-success"><?= (int)($totalTables ?? 0) ?></div>
                        <div class="stat-label">Total Meja</div>
                    </div>
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-grid-3x3-gap-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-value text-warning"><?= (int)($totalReservations ?? 0) ?></div>
                        <div class="stat-label">Total Pemesanan</div>
                    </div>
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-calendar-check-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-value text-info"><?= formatRupiah($totalRevenue ?? 0) ?></div>
                        <div class="stat-label">Total Pendapatan</div>
                    </div>
                    <div class="stat-icon bg-info bg-opacity-10 text-info">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pemesanan Terbaru -->
<div class="card table-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-clock-history me-2"></i>Pemesanan Terbaru</span>
        <a href="<?= BASE_URL ?>/reservations" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($recentReservations)): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Pelanggan</th>
                            <th>Meja</th>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                            <th>Status</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentReservations as $r): ?>
                            <tr>
                                <td><?= e($r['customer_name'] ?? '-') ?></td>
                                <td><?= e($r['table_number'] ?? '-') ?></td>
                                <td><?= date('d/m/Y', strtotime($r['reservation_date'])) ?></td>
                                <td><?= date('H:i', strtotime($r['reservation_time'])) ?></td>
                                <td>
                                    <?php
                                    $badge = Reservation::STATUS_BADGES[$r['status']] ?? 'secondary';
                                    $label = Reservation::STATUS_LABELS[$r['status']] ?? $r['status'];
                                    ?>
                                    <span class="badge bg-<?= $badge ?>"><?= e($label) ?></span>
                                </td>
                                <td><?= formatRupiah($r['total_amount'] ?? 0) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <p>Belum ada pemesanan.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>
