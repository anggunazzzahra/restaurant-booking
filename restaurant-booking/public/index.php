<?php
/**
 * Front Controller
 *
 * Titik masuk utama aplikasi. Semua request HTTP diarahkan ke sini
 * melalui .htaccess, kemudian diteruskan ke router.
 *
 * Catatan: konstanta ROOT_PATH didefinisikan di config/app.php
 * (dimuat oleh routes/web.php), jadi tidak didefinisikan ulang di sini.
 *
 * @package RestaurantBooking
 */

require_once __DIR__ . '/../routes/web.php';
