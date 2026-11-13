<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Profile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * Test registrasi berhasil dengan data valid dan password ter-hash.
     */
    public function test_user_can_register_with_valid_data(): void
    {
        $uniqueEmail = 'testuser_' . time() . '@example.com';

        $response = $this->post(route('register.post'), [
            'name'                  => 'Budi Santoso',
            'email'                 => $uniqueEmail,
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'phone'                 => '08123456789',
        ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name'  => 'Budi Santoso',
            'email' => $uniqueEmail,
            'role'  => 'user',
        ]);

        $user = User::where('email', $uniqueEmail)->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('password123', $user->password));
        $this->assertNotEquals('password123', $user->password); // Pastikan bukan plain text
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test validasi registrasi (email unik, format email, password min 8, konfirmasi password).
     */
    public function test_registration_validation_rules(): void
    {
        // 1. Password kurang dari 8 karakter & konfirmasi tidak cocok
        $response = $this->post(route('register.post'), [
            'name'                  => 'User Test',
            'email'                 => 'valid@example.com',
            'password'              => 'short',
            'password_confirmation' => 'different',
        ]);

        $response->assertSessionHasErrors(['password']);

        // 2. Format email salah
        $response = $this->post(route('register.post'), [
            'name'                  => 'User Test',
            'email'                 => 'not-an-email',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);

        // 3. Email sudah terdaftar (admin@example.com)
        $response = $this->post(route('register.post'), [
            'name'                  => 'User Baru',
            'email'                 => 'admin@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Test login berhasil untuk user biasa dan redirect ke home.
     */
    public function test_regular_user_can_login_and_redirect_to_home(): void
    {
        $uniqueEmail = 'user_' . time() . '@example.com';
        $user = User::create([
            'name'     => 'Regular User',
            'email'    => $uniqueEmail,
            'password' => Hash::make('secret1234'),
            'role'     => 'user',
            'role_id'  => 1,
        ]);

        $response = $this->post(route('login.post'), [
            'login'    => $uniqueEmail,
            'password' => 'secret1234',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test login berhasil untuk admin dan redirect ke /admin.
     */
    public function test_admin_can_login_and_redirect_to_admin_dashboard(): void
    {
        $uniqueEmail = 'admin_' . time() . '@example.com';
        $admin = User::create([
            'name'     => 'Super Admin',
            'email'    => $uniqueEmail,
            'password' => Hash::make('admin1234'),
            'role'     => 'admin',
            'role_id'  => 2,
        ]);

        $response = $this->post(route('login.post'), [
            'login'    => $uniqueEmail,
            'password' => 'admin1234',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    /**
     * Test login gagal dengan kredensial salah menampilkan pesan error tanpa membocorkan email/password.
     */
    public function test_login_fails_with_invalid_credentials(): void
    {
        $response = $this->post(route('login.post'), [
            'login'    => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['login']);
        $this->assertGuest();
    }

    /**
     * Test admin dashboard hanya bisa diakses oleh admin.
     */
    public function test_admin_dashboard_authorization(): void
    {
        // Tamu ditolak
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('home', ['auth' => 'login']));

        // User biasa ditolak
        $user = User::where('role', 'user')->first();
        if ($user) {
            $response = $this->actingAs($user)->get(route('admin.dashboard'));
            $response->assertRedirect(route('home'));
            $response->assertSessionHas('error');
        }

        // Admin diizinkan
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            $response = $this->actingAs($admin)->get(route('admin.dashboard'));
            $response->assertStatus(200);
        }
    }

    /**
     * Test logout berfungsi dengan baik dan session di-invalidate.
     */
    public function test_user_can_logout(): void
    {
        $user = User::first();
        $this->assertNotNull($user);

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect(route('home'));
        $this->assertGuest();
    }
}

