<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('admin-login:testadmin|127.0.0.1');
        RateLimiter::clear('admin-login:owner|127.0.0.1');
    }

    public function test_admin_login_page_renders_clean_modern_ui(): void
    {
        $response = $this->get(route('admin.login'));

        $response->assertStatus(200);
        $response->assertSee('Login Admin');
        $response->assertSee('Bayeng Putra Helm');
        $response->assertSee('Username');
        $response->assertSee('Password');
        $response->assertSee('Masuk');
        $response->assertSee('Kembali ke Katalog Toko');
        $response->assertDontSee('linear-gradient(135deg, var(--dark)');
        $response->assertDontSee('bootstrap.min.css');
        $response->assertDontSee('Portal Internal');
        $response->assertDontSee('Sistem Operasional Toko');
    }

    public function test_admin_login_fails_with_invalid_credentials(): void
    {
        $response = $this->from(route('admin.login'))->post(route('admin.login.post'), [
            'username' => 'testadmin',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect(route('admin.login'));
        $response->assertSessionHasErrors(['username']);
        $this->assertGuest('web');
    }

    public function test_admin_login_succeeds_with_valid_credentials(): void
    {
        // Ensure user exists with known password
        $user = User::firstOrCreate(
            ['username' => 'admin_test'],
            [
                'nama_lengkap' => 'Admin Testing',
                'password' => Hash::make('Admin123!'),
                'id_role' => 1,
            ]
        );

        $response = $this->post(route('admin.login.post'), [
            'username' => 'admin_test',
            'password' => 'Admin123!',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user, 'web');
    }
}
