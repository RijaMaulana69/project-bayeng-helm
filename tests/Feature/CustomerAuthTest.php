<?php

namespace Tests\Feature;

use App\Models\Pelanggan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class CustomerAuthTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('pelanggan-login:testauth@gmail.com|127.0.0.1');
    }

    public function test_customer_can_register_with_valid_data(): void
    {
        $uniqueEmail = 'rider_' . rand(1000, 9999) . '@test.com';

        $response = $this->post('/pelanggan/register', [
            'nama' => 'budi pratama', // Should be normalized to Title Case "Budi Pratama"
            'email' => $uniqueEmail,
            'telepon' => '081234567890',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertRedirect('/katalog');
        $this->assertAuthenticated('pelanggan');

        $this->assertDatabaseHas('pelanggan', [
            'email' => $uniqueEmail,
            'nama' => 'Budi Pratama',
            'telepon' => '081234567890',
        ]);
    }

    public function test_customer_registration_fails_if_nama_exceeds_20_characters(): void
    {
        $longName = 'Budi Santoso Pratama Putra'; // 26 chars (> 20)

        $response = $this->from('/pelanggan/register')->post('/pelanggan/register', [
            'nama' => $longName,
            'email' => 'longname@test.com',
            'telepon' => '081234567891',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertRedirect('/pelanggan/register');
        $response->assertSessionHasErrors(['nama']);
    }

    public function test_customer_registration_fails_if_email_exceeds_30_characters(): void
    {
        $longEmail = 'thisisverylongemailaddress123456789@test.com'; // > 30 chars

        $response = $this->from('/pelanggan/register')->post('/pelanggan/register', [
            'nama' => 'Budi Santoso',
            'email' => $longEmail,
            'telepon' => '081234567891',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertRedirect('/pelanggan/register');
        $response->assertSessionHasErrors(['email']);
    }

    public function test_customer_registration_fails_if_telepon_exceeds_15_digits(): void
    {
        $longPhone = '081234567890123456'; // 18 digits (> 15)

        $response = $this->from('/pelanggan/register')->post('/pelanggan/register', [
            'nama' => 'Budi Santoso',
            'email' => 'phone1@test.com',
            'telepon' => $longPhone,
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertRedirect('/pelanggan/register');
        $response->assertSessionHasErrors(['telepon']);
    }

    public function test_customer_registration_fails_if_password_is_under_8_characters(): void
    {
        $response = $this->from('/pelanggan/register')->post('/pelanggan/register', [
            'nama' => 'Budi Santoso',
            'email' => 'shortpass@test.com',
            'telepon' => '081234567892',
            'password' => 'Pass1', // only 5 chars
            'password_confirmation' => 'Pass1',
        ]);

        $response->assertRedirect('/pelanggan/register');
        $response->assertSessionHasErrors(['password']);
    }

    public function test_customer_registration_fails_if_password_exceeds_12_characters(): void
    {
        $response = $this->from('/pelanggan/register')->post('/pelanggan/register', [
            'nama' => 'Budi Santoso',
            'email' => 'longpass@test.com',
            'telepon' => '081234567892',
            'password' => 'Password12345', // 13 chars (> 12)
            'password_confirmation' => 'Password12345',
        ]);

        $response->assertRedirect('/pelanggan/register');
        $response->assertSessionHasErrors(['password']);
    }

    public function test_customer_registration_fails_if_password_lacks_uppercase_or_number(): void
    {
        $response = $this->from('/pelanggan/register')->post('/pelanggan/register', [
            'nama' => 'Budi Santoso',
            'email' => 'weakpass@test.com',
            'telepon' => '081234567893',
            'password' => 'passwordhanya', // lacks uppercase and digits
            'password_confirmation' => 'passwordhanya',
        ]);

        $response->assertRedirect('/pelanggan/register');
        $response->assertSessionHasErrors(['password']);
    }

    public function test_customer_can_login_with_email(): void
    {
        $uniqueEmail = 'login_email_' . rand(1000, 9999) . '@test.com';

        Pelanggan::create([
            'nama' => 'Rider Satu',
            'email' => $uniqueEmail,
            'telepon' => '081987654321',
            'password' => 'Secret123',
        ]);

        $response = $this->post('/pelanggan/login', [
            'email' => $uniqueEmail,
            'password' => 'Secret123',
        ]);

        $response->assertRedirect('/katalog');
        $this->assertAuthenticated('pelanggan');
    }

    public function test_customer_can_login_with_whatsapp_number(): void
    {
        $uniquePhone = '0877' . rand(10000000, 99999999);

        Pelanggan::create([
            'nama' => 'Rider Dua',
            'email' => 'rider_phone_' . uniqid() . '@test.com',
            'telepon' => $uniquePhone,
            'password' => 'Secret123',
        ]);

        // Login using phone with +62 formatting (should be auto normalized to 08...)
        $response = $this->post('/pelanggan/login', [
            'email' => '+62' . substr($uniquePhone, 1),
            'password' => 'Secret123',
        ]);

        $response->assertRedirect('/katalog');
        $this->assertAuthenticated('pelanggan');
    }

    public function test_customer_login_fails_with_invalid_password(): void
    {
        $response = $this->from('/pelanggan/login')->post('/pelanggan/login', [
            'email' => 'budi@gmail.com',
            'password' => 'Salah123',
        ]);

        $response->assertRedirect('/pelanggan/login');
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest('pelanggan');
    }

    public function test_demo_customer_budi_can_login_with_password123(): void
    {
        $response = $this->post('/pelanggan/login', [
            'email' => 'budi@gmail.com',
            'password' => 'Password123',
        ]);

        $response->assertRedirect('/katalog');
        $this->assertAuthenticated('pelanggan');
    }
}
