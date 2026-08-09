<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hubungkan sesi main kuis kelompok (zd_quiz_groups) ke akun login kelompok
     * (zd_kelompok) — sebelumnya dua hal ini terpisah (isi nama manual, tanpa login).
     * Sekarang wajib login kelompok dulu buat main, jadi datanya otomatis dari sini.
     */
    public function up(): void
    {
        Schema::table('zd_quiz_groups', function (Blueprint $table) {
            $table->foreignId('kelompok_id')->nullable()->after('quiz_id')->constrained('zd_kelompok')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('zd_quiz_groups', function (Blueprint $table) {
            $table->dropConstrainedForeignId('kelompok_id');
        });
    }
};
