<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Tampilkan form login jika diakses langsung via URL.
     */
    public function showLoginForm(Request $request)
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('home');
        }

        $redirect = $request->query('redirect');
        if (is_string($redirect)) {
            $redirectHost = parse_url($redirect, PHP_URL_HOST);
            $isLocalPath = str_starts_with($redirect, '/') && !str_starts_with($redirect, '//');
            $isSameHost = $redirectHost === $request->getHost()
                && in_array(parse_url($redirect, PHP_URL_SCHEME), ['http', 'https'], true);

            if ($isLocalPath || $isSameHost) {
                $request->session()->put('url.intended', $redirect);
            }
        }

        return redirect()->route('home', ['auth' => 'login']);
    }

    /**
     * Tampilkan form register jika diakses langsung via URL.
     */
    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return redirect()->route('home', ['auth' => 'register']);
    }

    /**
     * Proses Login pengguna.
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'login'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login.required'    => 'Username atau Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $loginInput = trim($validated['login']);
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        $credentials = [
            $fieldType => $loginInput,
            'password' => $validated['password'],
        ];

        $remember = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            return back()
                ->withInput($request->only('login'))
                ->withErrors([
                    'login' => 'Email/Username atau password yang Anda masukkan salah.',
                ])
                ->with('auth_modal', 'login');
        }

        // Regenerate session untuk mencegah Session Fixation
        $request->session()->regenerate();

        $user = Auth::user();

        // Redirect sesuai role: admin ke /admin, user ke home
        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Selamat datang kembali, Admin ' . $user->name . '!');
        }

        return redirect()->intended(route('home'))
            ->with('success', 'Login berhasil! Selamat datang, ' . $user->name . '.');
    }

    /**
     * Proses Register pengguna baru.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
            'phone'                 => ['nullable', 'string', 'max:20'],
        ], [
            'name.required'                  => 'Nama / Username wajib diisi.',
            'email.required'                 => 'Email wajib diisi.',
            'email.email'                    => 'Format email tidak valid.',
            'email.unique'                   => 'Email sudah terdaftar. Silakan gunakan email lain atau login.',
            'password.required'              => 'Password wajib diisi.',
            'password.min'                   => 'Password minimal harus 8 karakter.',
            'password.confirmed'             => 'Konfirmasi password tidak cocok dengan password.',
            'password_confirmation.required' => 'Konfirmasi password wajib diisi.',
        ]);

        // Buat profil default untuk relasi database yang konsisten
        $profile = Profile::create([
            'umur'   => 0,
            'bio'    => 'Member OneMovies',
            'alamat' => $validated['phone'] ?? '-',
        ]);

        // Buat user dengan password yang di-hash menggunakan bcrypt
        $user = User::create([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
            'role'       => 'user',
            'role_id'    => 1,
            'profile_id' => $profile->id,
        ]);

        // Login user secara otomatis setelah berhasil registrasi
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home')
            ->with('success', 'Registrasi berhasil! Selamat datang, ' . $user->name . '.');
    }

    /**
     * Proses Logout pengguna.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'Anda telah berhasil logout.');
    }
}
