<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('zd_quizzes', function (Blueprint $table) {
            // 1 = kuis kelompok (alur lama, main bareng per kelompok), 0 = kuis individu (baru).
            $table->boolean('is_kelompok')->default(true)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('zd_quizzes', function (Blueprint $table) {
            $table->dropColumn('is_kelompok');
        });
    }
};
