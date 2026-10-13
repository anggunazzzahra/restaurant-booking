<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan — <?= e(APP_NAME) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; color: #333; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { font-size: 18px; margin-bottom: 4px; }
        .header p { font-size: 11px; color: #666; }
        .period { text-align: center; margin-bottom: 15px; font-size: 13px; font-weight: 600; }
        .summary { display: flex; gap: 15px; margin-bottom: 20px; justify-content: center; }
        .summary-item { border: 1px solid #ddd; border-radius: 4px; padding: 8px 16px; text-align: center; }
        .summary-item .value { font-size: 18px; font-weight: 700; }
        .summary-item .label { font-size: 10px; color: #666; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background-color: #f5f5f5; font-size: 11px; text-transform: uppercase; font-weight: 600; }
        td { font-size: 11px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .section-title { font-size: 14px; font-weight: 600; margin: 15px 0 8px; padding-bottom: 4px; border-bottom: 1px solid #eee; }
        .footer { text-align: center; margin-top: 30px; font-size: 10px; color: #999; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align:center;margin-bottom:15px;">
        <button onclick="window.print()" style="padding:8px 20px;font-size:14px;cursor:pointer;">
            🖨️ Cetak Halaman Ini
        </button>
        <button onclick="window.close()" style="padding:8px 20px;font-size:14px;cursor:pointer;margin-left:10px;">
            ✕ Tutup
        </button>
    </div>

    <div class="header">
        <h1><?= e(APP_NAME) ?></h1>
        <p>Laporan Pemesanan Restoran</p>
    </div>

    <div class="period">
        Periode: <?= date('d/m/Y', strtotime($startDate)) ?> — <?= date('d/m/Y', strtotime($endDate)) ?>
    </div>

    <!-- Ringkasan -->
    <?php
    // $summary adalah SATU baris hasil agregasi dari Reservation::getReportSummary()
    $printCards = [
        'Total Pemesanan' => (int)($summary['total_reservations'] ?? 0),
        'Menunggu'        => (int)($summary['menunggu'] ?? 0),
        'Dikonfirmasi'    => (int)($summary['dikonfirmasi'] ?? 0),
        'Selesai'         => (int)($summary['selesai'] ?? 0),
        'Dibatalkan'      => (int)($summary['dibatalkan'] ?? 0),
    ];
    ?>
    <div class="summary">
        <?php foreach ($printCards as $label => $value): ?>
            <div class="summary-item">
                <div class="value"><?= $value ?></div>
                <div class="label"><?= e($label) ?></div>
            </div>
        <?php endforeach; ?>
        <div class="summary-item">
            <div class="value" style="font-size:14px;"><?= formatRupiah($summary['revenue'] ?? 0) ?></div>
            <div class="label">Pendapatan (Selesai)</div>
        </div>
    </div>

    <!-- Daftar Pemesanan -->
    <div class="section-title">Daftar Pemesanan</div>
    <?php if (!empty($reservations)): ?>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Pelanggan</th>
                <th>Meja</th>
                <th>Tanggal</th>
                <th class="text-center">Tamu</th>
                <th>Status</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservations as $i => $r): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= e($r['customer_name'] ?? '-') ?></td>
                    <td><?= e($r['table_number'] ?? '-') ?></td>
                    <td><?= date('d/m/Y', strtotime($r['reservation_date'])) ?></td>
                    <td class="text-center"><?= (int)$r['guest_count'] ?></td>
                    <td><?= e(Reservation::STATUS_LABELS[$r['status']] ?? $r['status']) ?></td>
                    <td class="text-right"><?= formatRupiah($r['total_amount'] ?? 0) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p style="text-align:center;padding:20px;color:#999;">Tidak ada data pada periode ini.</p>
    <?php endif; ?>

    <!-- Menu Terlaris -->
    <?php if (!empty($menuSold)): ?>
    <div class="section-title">Menu Terlaris</div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Menu</th>
                <th class="text-center">Jumlah Terjual</th>
                <th class="text-right">Total Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($menuSold as $i => $ms): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= e($ms['menu_name']) ?></td>
                    <td class="text-center"><?= (int)$ms['total_qty'] ?></td>
                    <td class="text-right"><?= formatRupiah($ms['total_sales'] ?? 0) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <div class="footer">
        Dicetak pada: <?= date('d/m/Y H:i:s') ?> WIB — <?= e(APP_NAME) ?>
    </div>
</body>
</html>
