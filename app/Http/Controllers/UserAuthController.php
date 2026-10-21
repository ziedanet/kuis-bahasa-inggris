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
                'nisn' => 'NISN or password is incorrect.',
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
                'username' => 'Username or password is incorrect.',
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

        // Opsional: kembali ke halaman asal (dipakai menu logout di header Travel Quest).
        // Hanya path internal yang diterima, supaya tidak jadi open redirect.
        $returnTo = (string) $request->input('return_to', '');
        if ($returnTo !== '' && str_starts_with($returnTo, '/') && ! str_starts_with($returnTo, '//')) {
            return redirect($returnTo);
        }

        return redirect()->route('quiz.home');
    }
}
