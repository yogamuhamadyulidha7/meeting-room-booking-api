<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function show()
    {
        // Menampilkan halaman login Inertia
        return inertia('Auth/Login');
    }

    public function login(Request $request)
    {
        // 1. Validasi Input
        $cred = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Cek Credential (Login Session Biasa)
        if (!Auth::attempt($cred, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Invalid credentials'])
                ->onlyInput('email');
        }

        // 3. Regenerate Session ID (Keamanan)
        $request->session()->regenerate();

        $user = $request->user();
        $scopes = ['tours:update'];

        // 4. BUAT TOKEN PASSPORT (Ini kuncinya)
        // Token ini dibuat di server, lalu disimpan ke session
        $token = $user->createToken('SPA PAT', $scopes)->accessToken;

        // 5. Simpan Token ke Session agar bisa dibaca Frontend (Inertia shared props)
        session(['pat' => $token, 'pat_scopes' => $scopes]);

        return redirect()->intended('/tours');
    }

    public function logout(Request $request)
    {
        // Hapus token Passport jika ada
        if ($request->user() && $request->user()->token()) {
            $request->user()->token()->revoke();
        }

        // Bersihkan session
        session()->forget(['pat', 'pat_scopes']);

        // Logout Web Session
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
