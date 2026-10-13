<?php
/**
 * Unit Test: ValidationService
 *
 * Menguji logika validasi input pada service layer.
 * Menerapkan unit testing dengan PHPUnit sesuai SKKNI J.620100.033.02.
 *
 * @package RestaurantBooking\Tests\Unit
 */

use PHPUnit\Framework\TestCase;

class ValidationServiceTest extends TestCase
{
    private ValidationService $validator;

    protected function setUp(): void
    {
        $this->validator = new ValidationService();
    }

    // ================================================================
    // VALIDASI PELANGGAN
    // ================================================================

    public function testValidateCustomerWithValidData(): void
    {
        $data = [
            'name'  => 'Budi Santoso',
            'phone' => '081234567890',
            'email' => 'budi@example.com',
        ];

        $result = $this->validator->validateCustomer($data);

        $this->assertTrue($result, 'Data pelanggan valid harus lolos validasi.');
        $this->assertFalse($this->validator->hasErrors());
    }

    public function testValidateCustomerNameRequired(): void
    {
        $data = [
            'name'  => '',
            'phone' => '081234567890',
            'email' => '',
        ];

        $result = $this->validator->validateCustomer($data);

        $this->assertFalse($result, 'Nama kosong harus gagal validasi.');
        $this->assertTrue($this->validator->hasErrors());
        $errors = $this->validator->getErrors();
        $this->assertArrayHasKey('name', $errors);
    }

    public function testValidateCustomerNameMaxLength(): void
    {
        $data = [
            'name'  => str_repeat('A', 101),
            'phone' => '081234567890',
            'email' => '',
        ];

        $result = $this->validator->validateCustomer($data);

        $this->assertFalse($result);
        $errors = $this->validator->getErrors();
        $this->assertArrayHasKey('name', $errors);
    }

    public function testValidateCustomerPhoneFormat(): void
    {
        // Nomor terlalu pendek
        $data = ['name' => 'Test', 'phone' => '0812', 'email' => ''];
        $this->assertFalse($this->validator->validateCustomer($data));

        // Nomor tidak diawali 08
        $this->validator->clearErrors();
        $data['phone'] = '1234567890123';
        $this->assertFalse($this->validator->validateCustomer($data));

        // Nomor valid
        $this->validator->clearErrors();
        $data['phone'] = '081234567890';
        $this->assertTrue($this->validator->validateCustomer($data));
    }

    public function testValidateCustomerEmailOptional(): void
    {
        $data = ['name' => 'Test', 'phone' => '081234567890', 'email' => ''];
        $this->assertTrue($this->validator->validateCustomer($data));

        $this->validator->clearErrors();
        $data['email'] = 'invalid-email';
        $this->assertFalse($this->validator->validateCustomer($data));
    }

    // ================================================================
    // VALIDASI MEJA
    // ================================================================

    public function testValidateTableWithValidData(): void
    {
        $data = [
            'table_number' => 'M01',
            'capacity'     => 4,
            'status'       => 'tersedia',
        ];

        $this->assertTrue($this->validator->validateTable($data));
    }

    public function testValidateTableCapacityRange(): void
    {
        $data = ['table_number' => 'M01', 'capacity' => 0, 'status' => 'tersedia'];
        $this->assertFalse($this->validator->validateTable($data));

        $this->validator->clearErrors();
        $data['capacity'] = 51;
        $this->assertFalse($this->validator->validateTable($data));

        $this->validator->clearErrors();
        $data['capacity'] = 25;
        $this->assertTrue($this->validator->validateTable($data));
    }

    public function testValidateTableStatusEnum(): void
    {
        $data = ['table_number' => 'M01', 'capacity' => 4, 'status' => 'invalid'];
        $this->assertFalse($this->validator->validateTable($data));

        $this->validator->clearErrors();
        $data['status'] = 'dipesan';
        $this->assertTrue($this->validator->validateTable($data));
    }

    // ================================================================
    // VALIDASI MENU
    // ================================================================

    public function testValidateMenuWithValidData(): void
    {
        $data = [
            'name'        => 'Nasi Goreng',
            'category_id' => 1,
            'price'       => 35000,
        ];

        $this->assertTrue($this->validator->validateMenu($data));
    }

    public function testValidateMenuPriceNonNegative(): void
    {
        $data = ['name' => 'Test', 'category_id' => 1, 'price' => -1000];
        $this->assertFalse($this->validator->validateMenu($data));
    }

    // ================================================================
    // VALIDASI PEMESANAN
    // ================================================================

    public function testValidateReservationWithValidData(): void
    {
        $data = [
            'customer_id'      => 1,
            'table_id'         => 1,
            'reservation_date' => date('Y-m-d', strtotime('+1 day')),
            'reservation_time' => '19:00',
            'guest_count'      => 4,
            'status'           => 'menunggu',
            'menu_items'       => [
                ['menu_id' => 1, 'quantity' => 2],
            ],
        ];

        $this->assertTrue($this->validator->validateReservation($data));
    }

    public function testValidateReservationDateNotInPast(): void
    {
        $data = [
            'customer_id'      => 1,
            'table_id'         => 1,
            'reservation_date' => '2020-01-01',
            'reservation_time' => '19:00',
            'guest_count'      => 2,
            'status'           => 'menunggu',
            'menu_items'       => [['menu_id' => 1, 'quantity' => 1]],
        ];

        $this->assertFalse($this->validator->validateReservation($data));
        $this->assertArrayHasKey('reservation_date', $this->validator->getErrors());
    }

    public function testValidateReservationRequiresMenuItems(): void
    {
        $data = [
            'customer_id'      => 1,
            'table_id'         => 1,
            'reservation_date' => date('Y-m-d', strtotime('+1 day')),
            'reservation_time' => '19:00',
            'guest_count'      => 2,
            'status'           => 'menunggu',
            'menu_items'       => [],
        ];

        $this->assertFalse($this->validator->validateReservation($data));
        $this->assertArrayHasKey('menu_items', $this->validator->getErrors());
    }

    // ================================================================
    // KALKULASI
    // ================================================================

    public function testCalculateSubtotal(): void
    {
        // Argumen: (quantity, price) -> 3 porsi x Rp 35.000
        $result = ValidationService::calculateSubtotal(3, 35000);
        $this->assertEquals(105000, $result);
    }

    public function testCalculateTotal(): void
    {
        // calculateTotal() menerima ARRAY SUBTOTAL, bukan array item
        $subtotals = [
            ValidationService::calculateSubtotal(2, 35000), // 70.000
            ValidationService::calculateSubtotal(1, 25000), // 25.000
        ];
        $result = ValidationService::calculateTotal($subtotals);
        $this->assertEquals(95000, $result);
    }

    public function testCalculateTotalOfEmptyListIsZero(): void
    {
        $this->assertEquals(0, ValidationService::calculateTotal([]));
    }

    // ================================================================
    // HELPER METHODS
    // ================================================================

    public function testClearErrors(): void
    {
        $data = ['name' => '', 'phone' => '', 'email' => ''];
        $this->validator->validateCustomer($data);
        $this->assertTrue($this->validator->hasErrors());

        $this->validator->clearErrors();
        $this->assertFalse($this->validator->hasErrors());
        $this->assertEmpty($this->validator->getErrors());
    }

    public function testGetFirstError(): void
    {
        $data = ['name' => '', 'phone' => '123', 'email' => ''];
        $this->validator->validateCustomer($data);

        $firstError = $this->validator->getFirstError();
        $this->assertIsString($firstError);
        $this->assertNotEmpty($firstError);
    }
}
