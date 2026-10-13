/**
 * Custom JavaScript - Sistem Pemesanan Restoran
 *
 * Menerapkan manipulasi DOM, event handling, dan pemrograman
 * terstruktur menggunakan JavaScript modern (ES6+).
 */

'use strict';

// ============================================================
// Sidebar Toggle (Mobile)
// ============================================================
document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.querySelector('.sidebar');
    const toggle = document.querySelector('.sidebar-toggle');
    const overlay = document.querySelector('.sidebar-overlay');

    if (toggle && sidebar && overlay) {
        toggle.addEventListener('click', function () {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        });

        overlay.addEventListener('click', function () {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    }

    // ============================================================
    // Auto-dismiss Flash Messages (5 detik)
    // ============================================================
    const flashMessages = document.querySelectorAll('.flash-message');
    flashMessages.forEach(function (msg) {
        setTimeout(function () {
            msg.style.transition = 'opacity 0.5s ease';
            msg.style.opacity = '0';
            setTimeout(function () {
                msg.remove();
            }, 500);
        }, 5000);
    });

    // ============================================================
    // Konfirmasi Hapus
    // ============================================================
    const deleteForms = document.querySelectorAll('.form-delete');
    deleteForms.forEach(function (form) {
        form.addEventListener('submit', function (e) {
            const message = form.dataset.confirm || 'Apakah Anda yakin ingin menghapus data ini?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

    // ============================================================
    // Dynamic Menu Items (Form Pemesanan)
    // ============================================================
    const menuContainer = document.getElementById('menu-items-container');
    const btnAddMenu = document.getElementById('btn-add-menu');

    const rowTemplate = document.getElementById('menu-item-template');

    if (menuContainer && btnAddMenu && rowTemplate) {
        // Buat baris baru dengan meng-clone <template> yang dirender PHP
        // (berisi dropdown menu lengkap dengan semua pilihan).
        function addMenuRow() {
            const newRow = rowTemplate.content.firstElementChild.cloneNode(true);
            menuContainer.appendChild(newRow);
            return newRow;
        }

        // Pastikan selalu ada minimal 1 baris saat halaman dibuka
        if (menuContainer.querySelectorAll('.menu-item-row').length === 0) {
            addMenuRow();
        }

        // Tombol tambah item menu
        btnAddMenu.addEventListener('click', function () {
            const row = addMenuRow();
            const select = row.querySelector('select');
            if (select) {
                select.focus();
            }
        });

        // Tombol hapus (event delegation: berlaku untuk baris lama maupun baru)
        menuContainer.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-remove');
            if (!btn) {
                return;
            }
            const rows = menuContainer.querySelectorAll('.menu-item-row');
            if (rows.length > 1) {
                btn.closest('.menu-item-row').remove();
            } else {
                alert('Minimal harus ada 1 item menu.');
            }
        });
    }

    // ============================================================
    // Format Rupiah pada Input Harga (Live Preview)
    // ============================================================
    const priceInputs = document.querySelectorAll('.input-price');
    priceInputs.forEach(function (input) {
        input.addEventListener('input', function () {
            const preview = document.getElementById(input.dataset.preview);
            if (preview) {
                const value = parseFloat(input.value) || 0;
                preview.textContent = 'Rp ' + value.toLocaleString('id-ID');
            }
        });
    });

    // ============================================================
    // Status Update via Form
    // ============================================================
    const statusSelects = document.querySelectorAll('.status-select');
    statusSelects.forEach(function (select) {
        select.addEventListener('change', function () {
            const form = select.closest('form');
            if (form && confirm('Ubah status pemesanan ini?')) {
                form.submit();
            }
        });
    });

    // ============================================================
    // Print Button
    // ============================================================
    const btnPrint = document.getElementById('btn-print');
    if (btnPrint) {
        btnPrint.addEventListener('click', function () {
            window.print();
        });
    }
});
