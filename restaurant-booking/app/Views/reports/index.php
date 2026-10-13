<?php
$pageTitle = 'Laporan Pemesanan';
$currentPage = 'reports';
ob_start();
?>

<div class="page-header">
    <h1><i class="bi bi-bar-chart-line me-2"></i>Laporan Pemesanan</h1>
    <a href="<?= BASE_URL ?>/reports/print?start_date=<?= e($startDate) ?>&end_date=<?= e($endDate) ?>"
       class="btn btn-outline-secondary" target="_blank">
        <i class="bi bi-printer me-1"></i>Cetak
    </a>
</div>

<!-- Filter Tanggal -->
<div class="card mb-3">
    <div class="card-body">
        <form action="<?= BASE_URL ?>/reports" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="start_date" class="form-label">Tanggal Mulai</label>
                <input type="date" class="form-control" id="start_date" name="start_date"
                       value="<?= e($startDate) ?>">
            </div>
            <div class="col-md-4">
                <label for="end_date" class="form-label">Tanggal Akhir</label>
                <input type="date" class="form-control" id="end_date" name="end_date"
                       value="<?= e($endDate) ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i>Tampilkan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Ringkasan -->
<div class="row g-3 mb-4">
    <?php
    // $summary adalah SATU baris hasil agregasi dari Reservation::getReportSummary()
    $summaryCards = [
        ['label' => 'Total Pemesanan', 'value' => (int)($summary['total_reservations'] ?? 0), 'color' => 'dark'],
        ['label' => 'Menunggu',        'value' => (int)($summary['menunggu'] ?? 0),           'color' => Reservation::STATUS_BADGES['menunggu']],
        ['label' => 'Dikonfirmasi',    'value' => (int)($summary['dikonfirmasi'] ?? 0),       'color' => Reservation::STATUS_BADGES['dikonfirmasi']],
        ['label' => 'Selesai',         'value' => (int)($summary['selesai'] ?? 0),            'color' => Reservation::STATUS_BADGES['selesai']],
        ['label' => 'Dibatalkan',      'value' => (int)($summary['dibatalkan'] ?? 0),         'color' => Reservation::STATUS_BADGES['dibatalkan']],
    ];
    ?>
    <?php foreach ($summaryCards as $card): ?>
        <div class="col-md-2 col-sm-4 col-6">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <div class="stat-value text-<?= e($card['color']) ?>"><?= $card['value'] ?></div>
                    <div class="stat-label"><?= e($card['label']) ?></div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <div class="col-md-2 col-sm-4 col-6">
        <div class="card stat-card">
            <div class="card-body text-center">
                <div class="stat-value text-success" style="font-size:1.2rem;">
                    <?= formatRupiah($summary['revenue'] ?? 0) ?>
                </div>
                <div class="stat-label">Pendapatan (Selesai)</div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Pemesanan -->
<div class="card table-card mb-4">
    <div class="card-header">
        <i class="bi bi-table me-2"></i>Daftar Pemesanan
        (<?= e($startDate) ?> s/d <?= e($endDate) ?>)
    </div>
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
                            <th>Tamu</th>
                            <th>Status</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservations as $i => $r): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= e($r['customer_name'] ?? '-') ?></td>
                                <td><?= e($r['table_number'] ?? '-') ?></td>
                                <td><?= date('d/m/Y', strtotime($r['reservation_date'])) ?></td>
                                <td><?= (int)$r['guest_count'] ?></td>
                                <td>
                                    <?php $badge = Reservation::STATUS_BADGES[$r['status']] ?? 'secondary'; ?>
                                    <span class="badge bg-<?= $badge ?>">
                                        <?= e(Reservation::STATUS_LABELS[$r['status']] ?? $r['status']) ?>
                                    </span>
                                </td>
                                <td class="text-end"><?= formatRupiah($r['total_amount'] ?? 0) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <p>Tidak ada data pada periode ini.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Menu Terlaris -->
<?php if (!empty($menuSold)): ?>
<div class="card table-card">
    <div class="card-header">
        <i class="bi bi-trophy me-2"></i>Menu Terlaris (dari pemesanan berstatus Selesai)
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Menu</th>
                        <th class="text-center">Jumlah Terjual</th>
                        <th class="text-end">Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($menuSold as $i => $ms): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= e($ms['menu_name']) ?></td>
                            <td class="text-center"><?= (int)$ms['total_qty'] ?></td>
                            <td class="text-end"><?= formatRupiah($ms['total_sales'] ?? 0) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>
