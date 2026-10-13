<?php
/**
 * Unit Test: Model Layer
 *
 * Menguji bahwa model-model memiliki konfigurasi tabel yang benar
 * dan konstanta/status yang valid.
 * Menerapkan pengujian unit sesuai SKKNI J.620100.033.02.
 *
 * @package RestaurantBooking\Tests\Unit
 */

use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
{
    // ================================================================
    // RESERVATION MODEL
    // ================================================================

    public function testReservationValidStatuses(): void
    {
        $expected = ['menunggu', 'dikonfirmasi', 'selesai', 'dibatalkan'];
        $this->assertEquals($expected, Reservation::VALID_STATUSES);
    }

    public function testReservationStatusLabels(): void
    {
        $labels = Reservation::STATUS_LABELS;

        $this->assertArrayHasKey('menunggu', $labels);
        $this->assertArrayHasKey('dikonfirmasi', $labels);
        $this->assertArrayHasKey('selesai', $labels);
        $this->assertArrayHasKey('dibatalkan', $labels);

        // Setiap status harus punya label
        foreach (Reservation::VALID_STATUSES as $status) {
            $this->assertArrayHasKey($status, $labels);
            $this->assertNotEmpty($labels[$status]);
        }
    }

    public function testReservationStatusBadges(): void
    {
        $badges = Reservation::STATUS_BADGES;

        foreach (Reservation::VALID_STATUSES as $status) {
            $this->assertArrayHasKey($status, $badges);
            $this->assertNotEmpty($badges[$status]);
        }
    }

    // ================================================================
    // RESTAURANT TABLE MODEL
    // ================================================================

    public function testRestaurantTableValidStatuses(): void
    {
        $expected = ['tersedia', 'dipesan', 'digunakan'];
        $this->assertEquals($expected, RestaurantTable::VALID_STATUSES);
    }

    // ================================================================
    // HELPER FUNCTION
    // ================================================================

    public function testHelperEscapeFunction(): void
    {
        $input = '<script>alert("XSS")</script>';
        $result = e($input);

        $this->assertStringNotContainsString('<script>', $result);
        $this->assertStringContainsString('&lt;script&gt;', $result);
    }

    public function testHelperFormatRupiah(): void
    {
        $this->assertEquals('Rp 0', formatRupiah(0));
        $this->assertEquals('Rp 35.000', formatRupiah(35000));
        $this->assertEquals('Rp 1.500.000', formatRupiah(1500000));
    }
}
