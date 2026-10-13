<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Dashboard') ?> — <?= e(APP_NAME) ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= BASE_URL ?>/css/style.css?v=<?= filemtime(ROOT_PATH . '/public/css/style.css') ?>" rel="stylesheet">
</head>
<body>
    <!-- Mobile Sidebar Toggle -->
    <button class="sidebar-toggle" aria-label="Toggle navigasi">
        <i class="bi bi-list"></i>
    </button>

    <!-- Sidebar Overlay (Mobile) -->
    <div class="sidebar-overlay"></div>

    <div class="wrapper">
        <!-- ============================================================ -->
        <!-- SIDEBAR -->
        <!-- ============================================================ -->
        <nav class="sidebar">
            <a href="<?= BASE_URL ?>/" class="sidebar-brand">
                <i class="bi bi-cup-hot-fill"></i>
                <span><?= e(APP_NAME) ?></span>
            </a>

            <ul class="sidebar-menu">
                <li class="menu-label">Menu Utama</li>

                <li class="menu-item">
                    <a href="<?= BASE_URL ?>/" class="<?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="menu-label">Manajemen Data</li>

                <li class="menu-item">
                    <a href="<?= BASE_URL ?>/customers" class="<?= ($currentPage ?? '') === 'customers' ? 'active' : '' ?>">
                        <i class="bi bi-people"></i>
                        <span>Pelanggan</span>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="<?= BASE_URL ?>/tables" class="<?= ($currentPage ?? '') === 'tables' ? 'active' : '' ?>">
                        <i class="bi bi-grid-3x3"></i>
                        <span>Meja Restoran</span>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="<?= BASE_URL ?>/menus" class="<?= ($currentPage ?? '') === 'menus' ? 'active' : '' ?>">
                        <i class="bi bi-book"></i>
                        <span>Menu Makanan</span>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="<?= BASE_URL ?>/menus/categories" class="<?= ($currentPage ?? '') === 'categories' ? 'active' : '' ?>">
                        <i class="bi bi-tags"></i>
                        <span>Kategori Menu</span>
                    </a>
                </li>

                <li class="menu-label">Transaksi</li>

                <li class="menu-item">
                    <a href="<?= BASE_URL ?>/reservations" class="<?= ($currentPage ?? '') === 'reservations' ? 'active' : '' ?>">
                        <i class="bi bi-calendar-check"></i>
                        <span>Pemesanan</span>
                    </a>
                </li>

                <li class="menu-label">Analisis</li>

                <li class="menu-item">
                    <a href="<?= BASE_URL ?>/reports" class="<?= ($currentPage ?? '') === 'reports' ? 'active' : '' ?>">
                        <i class="bi bi-bar-chart-line"></i>
                        <span>Laporan</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- ============================================================ -->
        <!-- MAIN CONTENT -->
        <!-- ============================================================ -->
        <div class="main-content">
            <!-- Flash Messages -->
            <?php $flash = getFlash(); ?>
            <?php if ($flash): ?>
                <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show flash-message" role="alert">
                    <?= e($flash['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            <?php endif; ?>

            <!-- Page Content -->
            <?= $content ?? '' ?>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="<?= BASE_URL ?>/js/app.js?v=<?= filemtime(ROOT_PATH . '/public/js/app.js') ?>"></script>
</body>
</html>
