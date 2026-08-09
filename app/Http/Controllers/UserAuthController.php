<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Login publik buat siswa (individu) & kelompok — beda dari admin login.
 * Pakai guard terpisah ("siswa" & "kelompok") yang didaftarkan di config/auth.php.
 */
class UserAuthController extends Controller
{
    public function loginSiswa(Request $request)
    {
        $credentials = $request->validate([
            'nisn' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('siswa')->attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'nisn' => 'NISN atau password salah.',
            ])->onlyInput('nisn')->withFragment('user-login');
        }

        $request->session()->regenerate();

        return redirect()->route('quiz.home')->withFragment('user-login');
    }

    public function loginKelompok(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('kelompok')->attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'username' => 'Username atau password salah.',
            ])->onlyInput('username')->withFragment('user-login');
        }

        $request->session()->regenerate();

        return redirect()->route('quiz.home')->withFragment('user-login');
    }

    public function logout(Request $request)
    {
        Auth::guard('siswa')->logout();
        Auth::guard('kelompok')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('quiz.home');
    }
}
