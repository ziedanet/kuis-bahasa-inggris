<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Username/password admin default bisa diatur lewat .env (ADMIN_DEFAULT_USERNAME,
     * ADMIN_DEFAULT_EMAIL, ADMIN_DEFAULT_PASSWORD). Kalau tidak diisi, jatuh ke nilai
     * default "admin"/"admin" — jangan lupa ganti lewat menu Profil Admin setelah
     * login pertama, apalagi kalau repo ini Public (kredensial default jadi kelihatan
     * di kode kalau tidak dioverride lewat .env).
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => env('ADMIN_DEFAULT_USERNAME', 'admin')],
            [
                'name' => 'Admin',
                'email' => env('ADMIN_DEFAULT_EMAIL', 'admin@sekolah.id'),
                'password' => Hash::make(env('ADMIN_DEFAULT_PASSWORD', 'admin')),
            ]
        );
    }
}
