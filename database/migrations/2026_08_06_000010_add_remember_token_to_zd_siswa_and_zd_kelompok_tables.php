<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Model Siswa & Kelompok pakai trait Authenticatable bawaan Laravel (biar bisa
     * login pakai guard sendiri) — trait ini otomatis butuh kolom `remember_token`
     * buat fitur "ingat saya". Tanpa kolom ini, login gagal dengan error
     * "Column not found: remember_token" begitu form login dikirim.
     */
    public function up(): void
    {
        Schema::table('zd_siswa', function (Blueprint $table) {
            $table->rememberToken()->after('password');
        });

        Schema::table('zd_kelompok', function (Blueprint $table) {
            $table->rememberToken()->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('zd_siswa', function (Blueprint $table) {
            $table->dropColumn('remember_token');
        });

        Schema::table('zd_kelompok', function (Blueprint $table) {
            $table->dropColumn('remember_token');
        });
    }
};
