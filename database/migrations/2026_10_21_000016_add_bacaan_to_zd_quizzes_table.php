<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Teks bacaan bersama untuk semua soal satu kuis individu (diinput sekali).
        Schema::table('zd_quizzes', function (Blueprint $table) {
            $table->string('bacaan_judul', 150)->nullable()->after('narration');
            $table->text('bacaan_teks')->nullable()->after('bacaan_judul');
        });
    }

    public function down(): void
    {
        Schema::table('zd_quizzes', function (Blueprint $table) {
            $table->dropColumn(['bacaan_judul', 'bacaan_teks']);
        });
    }
};
