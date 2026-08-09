<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Halaman profil buat siswa/kelompok yang login di homepage (beda dari profil
 * admin). Dipakai lewat klik nama di navbar — cuma buat ganti password.
 */
class PublicProfileController extends Controller
{
    public function edit()
    {
        $guard = $this->currentGuard();

        if (! $guard) {
            return redirect()->route('quiz.home')->withFragment('user-login');
        }

        return view('quiz.profile', [
            'guard' => $guard,
            'account' => Auth::guard($guard)->user(),
        ]);
    }

    public function update(Request $request)
    {
        $guard = $this->currentGuard();

        if (! $guard) {
            return redirect()->route('quiz.home')->withFragment('user-login');
        }

        $account = Auth::guard($guard)->user();

        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:4', 'confirmed'],
        ]);

        if (! Hash::check($data['current_password'], $account->password)) {
            return back()->withErrors(['current_password' => 'Password lama salah.']);
        }

        $account->update(['password' => $data['password']]);

        return back()->with('status', 'Password berhasil diganti.');
    }

    private function currentGuard(): ?string
    {
        if (Auth::guard('siswa')->check()) {
            return 'siswa';
        }

        if (Auth::guard('kelompok')->check()) {
            return 'kelompok';
        }

        return null;
    }
}
