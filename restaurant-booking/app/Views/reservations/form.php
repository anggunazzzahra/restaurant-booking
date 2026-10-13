<?php
$isEdit = isset($reservation['id']);
$pageTitle = $isEdit ? 'Edit Pemesanan' : 'Buat Pemesanan Baru';
$currentPage = 'reservations';
ob_start();
?>

<div class="page-header">
    <h1><i class="bi bi-<?= $isEdit ? 'pencil' : 'plus-lg' ?> me-2"></i><?= $pageTitle ?></h1>
    <a href="<?= BASE_URL ?>/reservations" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <form action="<?= BASE_URL ?>/reservations/<?= $isEdit ? "update/{$reservation['id']}" : 'store' ?>"
              method="POST" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e(generateCsrfToken()) ?>">

            <div class="row g-4">
                <!-- Kolom Kiri: Info Pemesanan -->
                <div class="col-md-6">
                    <div class="card form-card">
                        <div class="card-header">
                            <i class="bi bi-info-circle me-2"></i>Informasi Pemesanan
                        </div>
                        <div class="card-body">
                            <!-- Pelanggan -->
                            <div class="mb-3">
                                <label for="customer_id" class="form-label">Pelanggan <span class="text-danger">*</span></label>
                                <select class="form-select" id="customer_id" name="customer_id" required>
                                    <option value="">-- Pilih Pelanggan --</option>
                                    <?php foreach ($customers as $c): ?>
                                        <option value="<?= $c['id'] ?>"
                                            <?= ((int)($reservation['customer_id'] ?? 0)) === (int)$c['id'] ? 'selected' : '' ?>>
                                            <?= e($c['name']) ?> (<?= e($c['phone']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Meja -->
                            <div class="mb-3">
                                <label for="table_id" class="form-label">Meja <span class="text-danger">*</span></label>
                                <select class="form-select" id="table_id" name="table_id" required>
                                    <option value="">-- Pilih Meja --</option>
                                    <?php foreach ($tables as $t): ?>
                                        <option value="<?= $t['id'] ?>"
                                            data-capacity="<?= $t['capacity'] ?>"
                                            <?= ((int)($reservation['table_id'] ?? 0)) === (int)$t['id'] ? 'selected' : '' ?>>
                                            <?= e($t['table_number']) ?> (Kapasitas: <?= $t['capacity'] ?> orang)
                                            <?= $t['status'] !== 'tersedia' ? ' — ' . ucfirst($t['status']) : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Tanggal -->
                            <div class="mb-3">
                                <label for="reservation_date" class="form-label">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="reservation_date"
                                       name="reservation_date"
                                       value="<?= e($reservation['reservation_date'] ?? date('Y-m-d')) ?>"
                                       min="<?= date('Y-m-d') ?>" required>
                            </div>

                            <!-- Waktu -->
                            <div class="mb-3">
                                <label for="reservation_time" class="form-label">Waktu <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="reservation_time"
                                       name="reservation_time"
                                       value="<?= e($reservation['reservation_time'] ?? '') ?>" required>
                            </div>

                            <!-- Jumlah Tamu -->
                            <div class="mb-3">
                                <label for="guest_count" class="form-label">Jumlah Tamu <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="guest_count"
                                       name="guest_count" min="1"
                                       value="<?= (int)($reservation['guest_count'] ?? 1) ?>" required>
                            </div>

                            <!-- Status -->
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <?php foreach (Reservation::STATUS_LABELS as $key => $label): ?>
                                        <option value="<?= e($key) ?>"
                                            <?= ($reservation['status'] ?? 'menunggu') === $key ? 'selected' : '' ?>>
                                            <?= e($label) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Catatan -->
                            <div class="mb-0">
                                <label for="notes" class="form-label">Catatan</label>
                                <textarea class="form-control" id="notes" name="notes"
                                          rows="2"><?= e($reservation['notes'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Item Menu -->
                <div class="col-md-6">
                    <div class="card form-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-cart me-2"></i>Item Menu Pesanan</span>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-menu">
                                <i class="bi bi-plus-lg me-1"></i>Tambah Item
                            </button>
                        </div>
                        <div class="card-body">
                            <?php
                            /**
                             * Membuat HTML satu baris item menu (dropdown menu + qty + tombol hapus).
                             * Dipakai untuk baris yang tampil di halaman DAN untuk <template>
                             * yang di-clone oleh JavaScript saat tombol "Tambah Item" diklik.
                             */
                            $renderMenuRow = function (array $item) use ($menus): string {
                                ob_start();
                                ?>
                                <div class="menu-item-row">
                                    <div class="flex-grow-1">
                                        <select class="form-select" name="menu_id[]" required>
                                            <option value="">-- Pilih Menu --</option>
                                            <?php foreach ($menus as $m): ?>
                                                <option value="<?= (int)$m['id'] ?>"
                                                    <?= ((int)($item['menu_id'] ?? 0)) === (int)$m['id'] ? 'selected' : '' ?>>
                                                    <?= e($m['name']) ?> — <?= formatRupiah($m['price']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div style="width:100px;">
                                        <input type="number" class="form-control" name="quantity[]"
                                               min="1" value="<?= max(1, (int)($item['quantity'] ?? 1)) ?>"
                                               required placeholder="Qty">
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-outline-danger btn-sm btn-remove"
                                                title="Hapus item">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <?php
                                return ob_get_clean();
                            };

                            // Minimal selalu ada 1 baris (di halaman "buat baru" $menuItems berupa array kosong)
                            $items = !empty($menuItems) ? $menuItems : [['menu_id' => '', 'quantity' => 1]];
                            ?>

                            <?php if (empty($menus)): ?>
                                <div class="alert alert-warning py-2" style="font-size:0.85rem;">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    Belum ada menu yang tersedia.
                                    <a href="<?= BASE_URL ?>/menus/create">Tambah menu dulu</a>
                                    atau pastikan menu berstatus tersedia.
                                </div>
                            <?php endif; ?>

                            <div id="menu-items-container">
                                <?php foreach ($items as $item): ?>
                                    <?= $renderMenuRow($item) ?>
                                <?php endforeach; ?>
                            </div>

                            <!-- Cetakan baris baru untuk tombol "Tambah Item" (dibaca oleh app.js) -->
                            <template id="menu-item-template">
                                <?= $renderMenuRow(['menu_id' => '', 'quantity' => 1]) ?>
                            </template>

                            <div class="text-muted mt-2" style="font-size:0.8rem;">
                                <i class="bi bi-info-circle me-1"></i>
                                Minimal 1 item menu harus dipilih. Total akan dihitung otomatis.
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Submit -->
                    <div class="mt-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-lg flex-grow-1">
                            <i class="bi bi-check-lg me-1"></i><?= $isEdit ? 'Perbarui Pemesanan' : 'Simpan Pemesanan' ?>
                        </button>
                        <a href="<?= BASE_URL ?>/reservations" class="btn btn-outline-secondary btn-lg">Batal</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>
